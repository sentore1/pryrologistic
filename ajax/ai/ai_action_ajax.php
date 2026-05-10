<?php
// =============================================================
// AI ACTION EXECUTOR — performs actions on behalf of Pryro AI
// =============================================================
header('Content-Type: application/json');
require_once("../../loader.php");

$user = new User;
if (!$user->cdp_is_Admin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action  = isset($_POST['action'])  ? trim($_POST['action'])  : '';
$payload = isset($_POST['payload']) ? $_POST['payload']       : '{}';
$data    = json_decode($payload, true) ?: [];

$db = new Conexion;

switch ($action) {

    // ----------------------------------------------------------
    // ASSIGN DRIVER to a shipment
    // ----------------------------------------------------------
    case 'assign_driver':
        $order_id  = isset($data['order_id'])  ? (int)$data['order_id']  : 0;
        $driver_id = isset($data['driver_id']) ? (int)$data['driver_id'] : 0;
        if (!$order_id || !$driver_id) {
            echo json_encode(['success' => false, 'message' => 'Missing order_id or driver_id']);
            exit;
        }
        $db->cdp_query("UPDATE cdb_add_order SET driver_id=:driver_id WHERE order_id=:order_id");
        $db->bind(':driver_id', $driver_id);
        $db->bind(':order_id',  $order_id);
        $db->cdp_execute();
        echo json_encode(['success' => true, 'message' => 'Driver assigned successfully.']);
        break;

    // ----------------------------------------------------------
    // CONFIRM PAYMENT on a shipment
    // ----------------------------------------------------------
    case 'confirm_payment':
        $order_id   = isset($data['order_id'])   ? (int)$data['order_id']   : 0;
        $order_type = isset($data['order_type']) ? $data['order_type']       : 'courier'; // courier | consolidate | package
        if (!$order_id) {
            echo json_encode(['success' => false, 'message' => 'Missing order_id']);
            exit;
        }
        if ($order_type === 'consolidate') {
            $db->cdp_query("UPDATE cdb_consolidate SET status_invoice=3 WHERE c_id=:id");
        } elseif ($order_type === 'package') {
            $db->cdp_query("UPDATE cdb_customers_packages SET status_invoice=3 WHERE order_id=:id");
        } else {
            $db->cdp_query("UPDATE cdb_add_order SET status_invoice=3 WHERE order_id=:id");
        }
        $db->bind(':id', $order_id);
        $db->cdp_execute();

        // Also update payment gateway record if exists
        $db->cdp_query("UPDATE cdb_payment_gateways SET payment_status=3 WHERE order_id=:id AND payment_status=2");
        $db->bind(':id', $order_id);
        $db->cdp_execute();

        echo json_encode(['success' => true, 'message' => 'Payment confirmed successfully.']);
        break;

    // ----------------------------------------------------------
    // UPDATE STATUS on a shipment
    // ----------------------------------------------------------
    case 'update_status':
        $order_id   = isset($data['order_id'])     ? (int)$data['order_id']     : 0;
        $status_id  = isset($data['status_id'])    ? (int)$data['status_id']    : 0;
        $order_type = isset($data['order_type'])   ? $data['order_type']        : 'courier';
        $comment    = isset($data['comment'])      ? trim($data['comment'])      : 'Status updated by Pryro AI';
        if (!$order_id || !$status_id) {
            echo json_encode(['success' => false, 'message' => 'Missing order_id or status_id']);
            exit;
        }

        if ($order_type === 'consolidate') {
            $db->cdp_query("UPDATE cdb_consolidate SET status_courier=:status WHERE c_id=:id");
        } elseif ($order_type === 'package') {
            $db->cdp_query("UPDATE cdb_customers_packages SET status_courier=:status WHERE order_id=:id");
        } else {
            $db->cdp_query("UPDATE cdb_add_order SET status_courier=:status WHERE order_id=:id");
        }
        $db->bind(':status', $status_id);
        $db->bind(':id',     $order_id);
        $db->cdp_execute();

        // Add tracking record
        $db->cdp_query("SELECT order_prefix, order_no FROM cdb_add_order WHERE order_id=:id");
        $db->bind(':id', $order_id);
        $db->cdp_execute();
        $ord = $db->cdp_registro();
        if ($ord) {
            $track = $ord->order_prefix . $ord->order_no;
            $userData = $user->cdp_getUserData();
            $db->cdp_query("INSERT INTO cdb_courier_track (order_id, order_track, comments, t_date, status_courier, user_id) VALUES (:oid, :track, :comment, NOW(), :status, :uid)");
            $db->bind(':oid',     $order_id);
            $db->bind(':track',   $track);
            $db->bind(':comment', $comment);
            $db->bind(':status',  $status_id);
            $db->bind(':uid',     $userData->id);
            $db->cdp_execute();
        }

        echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
        break;

    // ----------------------------------------------------------
    // BULK CONFIRM all pending wire payments
    // ----------------------------------------------------------
    case 'confirm_all_wire_payments':
        $db->cdp_query("UPDATE cdb_add_order SET status_invoice=3 WHERE due_date < NOW() AND status_invoice=2 AND status_courier!=21");
        $db->cdp_execute();
        $affected = $db->cdp_rowCount();
        echo json_encode(['success' => true, 'message' => $affected . ' overdue invoice(s) marked as paid.']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action: ' . htmlspecialchars($action)]);
}
