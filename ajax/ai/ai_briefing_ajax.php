<?php
header('Content-Type: application/json');

require_once("../../loader.php");

$db   = new Conexion;
$user = new User;

if (!$user->cdp_is_Admin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// ------------------------------------------------------------------
// 1. GATHER SYSTEM SNAPSHOT
// ------------------------------------------------------------------

$stuck_shipments = 0;
$unassigned_shipments = 0;
$pending_wire_payments = 0;
$overdue_accounts = 0;
$pending_prealerts = 0;
$incomplete_shipments = 0;
$todays_shipments = 0;
$pending_consolidations = 0;

try {
    $db->cdp_query("SELECT COUNT(*) as total FROM cdb_add_order o WHERE o.status_courier NOT IN (8,21) AND o.is_pickup=0 AND NOT EXISTS (SELECT 1 FROM cdb_courier_track t WHERE t.order_id=o.order_id AND t.t_date >= NOW() - INTERVAL 24 HOUR)");
    $db->cdp_execute();
    $r = $db->cdp_registro(); $stuck_shipments = $r ? (int)$r->total : 0;

    $db->cdp_query("SELECT COUNT(*) as total FROM cdb_add_order WHERE (driver_id IS NULL OR driver_id=0 OR driver_id='') AND status_courier NOT IN (8,21) AND is_pickup=0");
    $db->cdp_execute();
    $r = $db->cdp_registro(); $unassigned_shipments = $r ? (int)$r->total : 0;

    $db->cdp_query("SELECT COUNT(*) as total FROM cdb_payment_gateways WHERE payment_status=2 AND payment_method=5");
    $db->cdp_execute();
    $r = $db->cdp_registro(); $pending_wire_payments = $r ? (int)$r->total : 0;

    $db->cdp_query("SELECT COUNT(*) as total FROM cdb_add_order WHERE due_date < NOW() AND status_invoice=2 AND status_courier!=21");
    $db->cdp_execute();
    $r = $db->cdp_registro(); $overdue_accounts = $r ? (int)$r->total : 0;

    $db->cdp_query("SELECT COUNT(*) as total FROM cdb_prealert WHERE status=0 OR status IS NULL");
    $db->cdp_execute();
    $r = $db->cdp_registro(); $pending_prealerts = $r ? (int)$r->total : 0;

    $db->cdp_query("SELECT COUNT(*) as total FROM cdb_add_order WHERE order_incomplete=1");
    $db->cdp_execute();
    $r = $db->cdp_registro(); $incomplete_shipments = $r ? (int)$r->total : 0;

    $db->cdp_query("SELECT COUNT(*) as total FROM cdb_add_order WHERE DATE(order_date)=CURDATE() AND is_pickup=0");
    $db->cdp_execute();
    $r = $db->cdp_registro(); $todays_shipments = $r ? (int)$r->total : 0;

    $db->cdp_query("SELECT COUNT(*) as total FROM cdb_consolidate WHERE status_courier NOT IN (8,21)");
    $db->cdp_execute();
    $r = $db->cdp_registro(); $pending_consolidations = $r ? (int)$r->total : 0;
} catch (Exception $e) {
    // continue with zeros if any query fails
}

// ------------------------------------------------------------------
// 2. LOAD API KEY FROM DB
// ------------------------------------------------------------------

$api_key  = '';
$provider = 'groq';

try {
    // Add columns if missing (catches error if already exist)
    try { $db->cdp_query("ALTER TABLE cdb_settings ADD COLUMN ai_provider VARCHAR(20) NOT NULL DEFAULT 'groq'"); $db->cdp_execute(); } catch(Exception $e){}
    try { $db->cdp_query("ALTER TABLE cdb_settings ADD COLUMN groq_api_key VARCHAR(255) NOT NULL DEFAULT ''"); $db->cdp_execute(); } catch(Exception $e){}
    try { $db->cdp_query("ALTER TABLE cdb_settings ADD COLUMN openai_api_key VARCHAR(255) NOT NULL DEFAULT ''"); $db->cdp_execute(); } catch(Exception $e){}

    $db->cdp_query("SELECT ai_provider, groq_api_key, openai_api_key FROM cdb_settings LIMIT 1");
    $db->cdp_execute();
    $row = $db->cdp_registro();

    if ($row) {
        $provider = !empty($row->ai_provider)  ? $row->ai_provider  : 'groq';
        $api_key  = !empty($row->groq_api_key) ? trim($row->groq_api_key) : '';
        if ($provider === 'openai' && !empty($row->openai_api_key)) {
            $api_key = trim($row->openai_api_key);
        }
    }
} catch (Exception $e) {
    // fall through — api_key stays empty
}

if (empty($api_key)) {
    echo json_encode([
        'briefing' => 'Add your API key in <a href="tools.php?list=config_ai">AI Settings</a> to activate P-AI.',
        'snapshot' => compact('stuck_shipments','unassigned_shipments','pending_wire_payments','overdue_accounts','pending_prealerts','incomplete_shipments','todays_shipments','pending_consolidations')
    ]);
    exit;
}

// ------------------------------------------------------------------
// 3. CALL LLM
// ------------------------------------------------------------------

$system_prompt = "You are an AI operations assistant for a shipping and logistics company. Analyze the current system state and give a concise prioritized briefing to the admin. Be direct and actionable. Use bullet points. Highlight urgent items first. Keep under 200 words. No markdown headers.";

$user_message = "System snapshot:
- Stuck shipments (no update 24h+): {$stuck_shipments}
- Shipments with no driver: {$unassigned_shipments}
- Wire payments pending confirmation: {$pending_wire_payments}
- Overdue unpaid invoices: {$overdue_accounts}
- Pre-alerts awaiting conversion: {$pending_prealerts}
- Incomplete shipments: {$incomplete_shipments}
- New shipments today: {$todays_shipments}
- Active consolidations: {$pending_consolidations}
What needs my attention right now?";

$endpoint = ($provider === 'openai')
    ? 'https://api.openai.com/v1/chat/completions'
    : 'https://api.groq.com/openai/v1/chat/completions';

$model = ($provider === 'openai') ? 'gpt-4o' : 'llama-3.3-70b-versatile';

$payload = json_encode([
    'model'       => $model,
    'messages'    => [
        ['role' => 'system', 'content' => $system_prompt],
        ['role' => 'user',   'content' => $user_message]
    ],
    'max_tokens'  => 300,
    'temperature' => 0.4
]);

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response  = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err  = curl_error($ch);
curl_close($ch);

// ------------------------------------------------------------------
// 4. RETURN
// ------------------------------------------------------------------

if ($http_code === 200) {
    $result   = json_decode($response, true);
    $briefing = isset($result['choices'][0]['message']['content'])
        ? $result['choices'][0]['message']['content']
        : 'No response from AI.';
} else {
    $briefing = 'AI error (HTTP ' . $http_code . '). ' . ($curl_err ?: 'Check your API key in AI Settings.');
}

echo json_encode([
    'briefing' => $briefing,
    'snapshot' => compact('stuck_shipments','unassigned_shipments','pending_wire_payments','overdue_accounts','pending_prealerts','incomplete_shipments','todays_shipments','pending_consolidations')
]);
