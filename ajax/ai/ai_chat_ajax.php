<?php
header('Content-Type: application/json');
require_once("../../loader.php");

$user = new User;
if (!$user->cdp_is_Admin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$message      = isset($_POST['message'])  ? trim($_POST['message'])  : '';
$history_json = isset($_POST['history'])  ? $_POST['history']        : '[]';
$history      = json_decode($history_json, true) ?: [];

$db = new Conexion;
$context = [];

// --- Stuck shipments with order_id for actions ---
$db->cdp_query("
    SELECT o.order_id, o.order_prefix, o.order_no, o.order_date,
           TIMESTAMPDIFF(HOUR, MAX(t.t_date), NOW()) as hours_stuck,
           u.fname, u.lname,
           d.fname as driver_fname, d.lname as driver_lname,
           o.total_order, o.status_invoice
    FROM cdb_add_order o
    LEFT JOIN cdb_courier_track t ON t.order_id = o.order_id
    LEFT JOIN cdb_users u ON u.id = o.sender_id
    LEFT JOIN cdb_users d ON d.id = o.driver_id
    WHERE o.status_courier NOT IN (8,21) AND o.is_pickup=0
    GROUP BY o.order_id
    HAVING hours_stuck > 24 OR MAX(t.t_date) IS NULL
    ORDER BY hours_stuck DESC LIMIT 10
");
$db->cdp_execute();
$stuck = $db->cdp_registros();
$context['stuck_shipments'] = [];
if ($stuck) foreach ($stuck as $r) {
    $context['stuck_shipments'][] = [
        'order_id'     => (int)$r->order_id,
        'tracking'     => $r->order_prefix . $r->order_no,
        'hours_stuck'  => (int)$r->hours_stuck,
        'customer'     => trim($r->fname . ' ' . $r->lname),
        'driver'       => $r->driver_fname ? trim($r->driver_fname . ' ' . $r->driver_lname) : 'Unassigned',
        'value'        => (float)$r->total_order,
        'payment_status' => (int)$r->status_invoice,
    ];
}

// --- Driver workload ---
$db->cdp_query("
    SELECT d.fname, d.lname, d.id, COUNT(o.order_id) as active_shipments
    FROM cdb_users d
    LEFT JOIN cdb_add_order o ON o.driver_id = d.id AND o.status_courier NOT IN (8,21)
    WHERE d.userlevel = 3
    GROUP BY d.id ORDER BY active_shipments ASC LIMIT 10
");
$db->cdp_execute();
$drivers = $db->cdp_registros();
$context['drivers'] = [];
if ($drivers) foreach ($drivers as $r) {
    $context['drivers'][] = [
        'driver_id'        => (int)$r->id,
        'name'             => trim($r->fname . ' ' . $r->lname),
        'active_shipments' => (int)$r->active_shipments,
    ];
}

// --- Overdue invoices with order_id ---
$db->cdp_query("
    SELECT o.order_id, o.order_prefix, o.order_no, o.due_date, o.total_order,
           DATEDIFF(NOW(), o.due_date) as days_overdue, u.fname, u.lname
    FROM cdb_add_order o
    LEFT JOIN cdb_users u ON u.id = o.sender_id
    WHERE o.due_date < NOW() AND o.status_invoice = 2 AND o.status_courier != 21
    ORDER BY days_overdue DESC LIMIT 10
");
$db->cdp_execute();
$overdue = $db->cdp_registros();
$context['overdue_invoices'] = [];
if ($overdue) foreach ($overdue as $r) {
    $context['overdue_invoices'][] = [
        'order_id'     => (int)$r->order_id,
        'tracking'     => $r->order_prefix . $r->order_no,
        'customer'     => trim($r->fname . ' ' . $r->lname),
        'amount'       => (float)$r->total_order,
        'days_overdue' => (int)$r->days_overdue,
    ];
}

// --- Revenue ---
$db->cdp_query("SELECT IFNULL(SUM(total_order),0) as total FROM cdb_add_order WHERE MONTH(order_date)=MONTH(NOW()) AND YEAR(order_date)=YEAR(NOW()) AND status_courier!=21");
$db->cdp_execute(); $r = $db->cdp_registro();
$context['revenue_this_month'] = $r ? (float)$r->total : 0;

$db->cdp_query("SELECT IFNULL(SUM(total_order),0) as total FROM cdb_add_order WHERE MONTH(order_date)=MONTH(NOW()-INTERVAL 1 MONTH) AND YEAR(order_date)=YEAR(NOW()-INTERVAL 1 MONTH) AND status_courier!=21");
$db->cdp_execute(); $r = $db->cdp_registro();
$context['revenue_last_month'] = $r ? (float)$r->total : 0;

// --- Top customers ---
$db->cdp_query("
    SELECT u.fname, u.lname, COUNT(o.order_id) as shipments, IFNULL(SUM(o.total_order),0) as total
    FROM cdb_add_order o LEFT JOIN cdb_users u ON u.id = o.sender_id
    WHERE MONTH(o.order_date)=MONTH(NOW()) AND YEAR(o.order_date)=YEAR(NOW()) AND o.status_courier!=21
    GROUP BY o.sender_id ORDER BY total DESC LIMIT 5
");
$db->cdp_execute();
$top = $db->cdp_registros();
$context['top_customers'] = [];
if ($top) foreach ($top as $r) {
    $context['top_customers'][] = ['name' => trim($r->fname . ' ' . $r->lname), 'shipments' => (int)$r->shipments, 'revenue' => (float)$r->total];
}

// --- Last 24h ---
$db->cdp_query("SELECT COUNT(*) as total FROM cdb_add_order WHERE order_datetime >= NOW() - INTERVAL 24 HOUR AND is_pickup=0");
$db->cdp_execute(); $r = $db->cdp_registro(); $context['new_shipments_24h'] = $r ? (int)$r->total : 0;

$db->cdp_query("SELECT COUNT(*) as total FROM cdb_payment_gateways WHERE payment_date >= NOW() - INTERVAL 24 HOUR AND payment_status=3");
$db->cdp_execute(); $r = $db->cdp_registro(); $context['payments_received_24h'] = $r ? (int)$r->total : 0;

$db->cdp_query("SELECT COUNT(*) as total FROM cdb_add_order WHERE order_datetime >= NOW() - INTERVAL 24 HOUR AND status_courier=21");
$db->cdp_execute(); $r = $db->cdp_registro(); $context['cancellations_24h'] = $r ? (int)$r->total : 0;

$db->cdp_query("SELECT COUNT(*) as total FROM cdb_add_order WHERE (driver_id IS NULL OR driver_id=0 OR driver_id='') AND status_courier NOT IN (8,21) AND is_pickup=0");
$db->cdp_execute(); $r = $db->cdp_registro(); $context['unassigned_shipments'] = $r ? (int)$r->total : 0;

$db->cdp_query("SELECT COUNT(*) as total FROM cdb_prealert WHERE status=0 OR status IS NULL");
$db->cdp_execute(); $r = $db->cdp_registro(); $context['pending_prealerts'] = $r ? (int)$r->total : 0;

$db->cdp_query("SELECT COUNT(*) as total FROM cdb_users WHERE userlevel=1 AND created >= NOW() - INTERVAL 7 DAY");
$db->cdp_execute(); $r = $db->cdp_registro(); $context['new_customers_week'] = $r ? (int)$r->total : 0;

// --- Currency ---
$db->cdp_query("SELECT currency, for_symbol, for_currency FROM cdb_settings LIMIT 1");
$db->cdp_execute();
$settings_row = $db->cdp_registro();
$currency = 'FRw';
if ($settings_row) {
    $currency = !empty($settings_row->for_symbol) ? $settings_row->for_symbol
              : (!empty($settings_row->for_currency) ? $settings_row->for_currency : $settings_row->currency);
}

$context_json = json_encode($context, JSON_PRETTY_PRINT);

// ------------------------------------------------------------------
// SYSTEM PROMPT
// ------------------------------------------------------------------
$system_prompt = <<<PROMPT
You are Pryro AI, an intelligent operations assistant for a shipping and logistics company called Pryro.
You have access to live system data and help the admin manage shipments, drivers, payments, and customers.
Be concise, direct, and actionable. Use bullet points when listing items.
Always refer to specific tracking numbers, customer names, and amounts from the data when relevant.
Always use "{$currency}" as the currency symbol. Never use dollar sign or any other currency.

IMPORTANT - ACTION BUTTONS:
When you identify actions that can be taken, append them at the very end of your reply using this exact format on a single line:
ACTIONS_JSON:[{"action":"confirm_payment","label":"Confirm Payment","order_id":123,"order_type":"courier","description":"Confirm payment for WIL123"},{"action":"update_status","label":"Mark In Transit","order_id":456,"status_id":4,"order_type":"courier","description":"Update WIL456 to In Transit"},{"action":"confirm_all_wire_payments","label":"Confirm All Overdue Payments","description":"Mark all overdue invoices as paid"}]

Rules for ACTIONS_JSON:
- Only include it when there are real actionable items from the live data
- Use the actual order_id numbers from the data provided
- Available actions: confirm_payment, update_status, confirm_all_wire_payments
- Status IDs for update_status: 2=Pending, 3=Processing, 4=In Transit, 5=Out for Delivery, 8=Delivered, 21=Cancelled
- Do NOT wrap ACTIONS_JSON in markdown code blocks

Here is the current live system data:
{$context_json}
PROMPT;

// ------------------------------------------------------------------
// BUILD MESSAGES
// ------------------------------------------------------------------
$messages = [['role' => 'system', 'content' => $system_prompt]];
foreach ($history as $h) {
    if (isset($h['role']) && isset($h['content'])) {
        $messages[] = ['role' => $h['role'], 'content' => $h['content']];
    }
}
if (!empty($message)) {
    $messages[] = ['role' => 'user', 'content' => $message];
}

// ------------------------------------------------------------------
// LOAD API KEY
// ------------------------------------------------------------------
$api_key = ''; $provider = 'groq';
try {
    try { $db->cdp_query("ALTER TABLE cdb_settings ADD COLUMN ai_provider VARCHAR(20) NOT NULL DEFAULT 'groq'"); $db->cdp_execute(); } catch(Exception $e){}
    try { $db->cdp_query("ALTER TABLE cdb_settings ADD COLUMN groq_api_key VARCHAR(255) NOT NULL DEFAULT ''"); $db->cdp_execute(); } catch(Exception $e){}
    try { $db->cdp_query("ALTER TABLE cdb_settings ADD COLUMN openai_api_key VARCHAR(255) NOT NULL DEFAULT ''"); $db->cdp_execute(); } catch(Exception $e){}
    $db->cdp_query("SELECT ai_provider, groq_api_key, openai_api_key FROM cdb_settings LIMIT 1");
    $db->cdp_execute();
    $row = $db->cdp_registro();
    if ($row) {
        $provider = !empty($row->ai_provider)  ? $row->ai_provider  : 'groq';
        $api_key  = !empty($row->groq_api_key) ? trim($row->groq_api_key) : '';
        if ($provider === 'openai' && !empty($row->openai_api_key)) $api_key = trim($row->openai_api_key);
    }
} catch (Exception $e) {}

if (empty($api_key)) {
    echo json_encode(['reply' => 'API key not configured. Go to AI Settings to add your key.', 'actions' => []]);
    exit;
}

// ------------------------------------------------------------------
// CALL LLM
// ------------------------------------------------------------------
$endpoint = ($provider === 'openai') ? 'https://api.openai.com/v1/chat/completions' : 'https://api.groq.com/openai/v1/chat/completions';
$model    = ($provider === 'openai') ? 'gpt-4o' : 'llama-3.3-70b-versatile';

$payload = json_encode(['model' => $model, 'messages' => $messages, 'max_tokens' => 600, 'temperature' => 0.4]);

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $api_key]);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    echo json_encode(['reply' => 'AI error (HTTP ' . $http_code . '). Check your API key.', 'actions' => []]);
    exit;
}

$result     = json_decode($response, true);
$full_reply = isset($result['choices'][0]['message']['content']) ? $result['choices'][0]['message']['content'] : 'No response.';

// ------------------------------------------------------------------
// PARSE ACTIONS_JSON OUT OF REPLY
// ------------------------------------------------------------------
$actions = [];
if (preg_match('/ACTIONS_JSON:(\[.*?\])/s', $full_reply, $matches)) {
    $actions_raw = $matches[1];
    $actions     = json_decode($actions_raw, true) ?: [];
    // Remove ACTIONS_JSON block from the visible reply
    $full_reply  = trim(preg_replace('/ACTIONS_JSON:\[.*?\]/s', '', $full_reply));
}

echo json_encode(['reply' => $full_reply, 'actions' => $actions]);
