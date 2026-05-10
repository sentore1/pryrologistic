<?php
require_once("../../../loader.php");
require_once("../../../helpers/querys.php");

$errors = array();

if (empty($_POST['name_pay']))
    $errors['name_pay'] = $lang['validate_field_ajax76'];

if (empty($_POST['detail_pay']))
    $errors['detail_pay'] = $lang['validate_field_ajax78'];

if (!isset($_POST['is_active'])) {
    $is_active = 0;
} else {
    $is_active = 1;
}

header('Content-type: application/json; charset=UTF-8');

if (CDP_APP_MODE_DEMO === true) {
    echo json_encode(['status' => 'error', 'message' => 'Demo mode - action not allowed']);
    exit;
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(', ', $errors)]);
    exit;
}

$response = array();

if (cdp_paymentMethodExists($_POST['name_pay'], $_POST['id'])) {
    $response['status'] = 'error';
    $response['message'] = $lang['validate_field_ajax77'];
} else {
    $data = array(
        'name_pay'   => cdp_sanitize($_POST['name_pay']),
        'detail_pay' => cdp_sanitize($_POST['detail_pay']),
        'is_active'  => $is_active,
        'id'         => cdp_sanitize($_POST['id']),
    );

    $update = cdp_updatePaymentMethod_wire($data);

    if ($update) {
        $response['status'] = 'success';
        $response['message'] = $lang['message_ajax_success_add'];
    } else {
        $response['status'] = 'error';
        $response['message'] = $lang['message_ajax_error1'];
    }
}

echo json_encode($response);
?>
