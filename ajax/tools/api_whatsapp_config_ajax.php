<?php
// *************************************************************************
// *                                                                       *
// * DEPRIXA PRO -  Integrated Web Shipping System                         *
// * Copyright (c) JAOMWEB. All Rights Reserved                            *
// *                                                                       *
// *************************************************************************

require_once("../../loader.php");
require_once("../../helpers/querys.php");

header('Content-type: application/json; charset=UTF-8');

$user = new User;
$core = new Core;
$errors = array();

$provider = isset($_POST['whatsapp_provider']) ? trim($_POST['whatsapp_provider']) : 'ultramsg';

// Validate per provider
if ($provider === 'ultramsg') {
    if (empty($_POST['api_ws_url']))    $errors['api_ws_url']   = 'Enter the UltraMsg API URL';
    if (empty($_POST['api_ws_token']))  $errors['api_ws_token'] = 'Enter the UltraMsg API Token';
} elseif ($provider === 'twilio') {
    if (empty($_POST['twilio_wa_sid']))     $errors['twilio_wa_sid']    = 'Enter the Twilio Account SID';
    if (empty($_POST['twilio_wa_token']))   $errors['twilio_wa_token']  = 'Enter the Twilio Auth Token';
    if (empty($_POST['twilio_wa_number']))  $errors['twilio_wa_number'] = 'Enter the Twilio WhatsApp number';
} elseif ($provider === 'meta') {
    if (empty($_POST['meta_wa_token']))     $errors['meta_wa_token']    = 'Enter the Meta Access Token';
    if (empty($_POST['meta_wa_phone_id']))  $errors['meta_wa_phone_id'] = 'Enter the Meta Phone Number ID';
}

$active_whatsapp = isset($_POST['active_whatsapp']) ? 1 : 0;

if (CDP_APP_MODE_DEMO === true) {
    echo json_encode(['status' => 'error', 'message' => 'Demo mode — action not allowed.']);
    exit;
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode(' | ', $errors)]);
    exit;
}

$data = array(
    'api_ws_url'        => trim($_POST['api_ws_url']        ?? ''),
    'api_ws_token'      => trim($_POST['api_ws_token']      ?? ''),
    'active_whatsapp'   => $active_whatsapp,
    'whatsapp_provider' => $provider,
    'twilio_wa_sid'     => trim($_POST['twilio_wa_sid']     ?? ''),
    'twilio_wa_token'   => trim($_POST['twilio_wa_token']   ?? ''),
    'twilio_wa_number'  => trim($_POST['twilio_wa_number']  ?? ''),
    'meta_wa_token'     => trim($_POST['meta_wa_token']     ?? ''),
    'meta_wa_phone_id'  => trim($_POST['meta_wa_phone_id']  ?? ''),
);

// Buffer any stray output (e.g. PDO error echoes from Conexion)
ob_start();
$insert = updateApiWhatsConfig($data);
$dbError = ob_get_clean();

if ($insert) {
    echo json_encode(['status' => 'success', 'message' => $lang['message_ajax_success_updated']]);
} else {
    $errMsg = !empty($dbError) ? $dbError : $lang['message_ajax_error1'];
    echo json_encode(['status' => 'error', 'message' => $errMsg]);
}
