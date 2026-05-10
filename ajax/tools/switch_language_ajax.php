<?php
require_once("../../loader.php");
require_once("../../helpers/querys.php");

header('Content-Type: application/json');

$allowed = ['en', 'es', 'fr', 'ar', 'he'];
$lang_code = isset($_POST['language']) ? trim($_POST['language']) : '';

if (!in_array($lang_code, $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid language: ' . $lang_code]);
    exit;
}

$db2 = new Conexion;
$db2->cdp_query("UPDATE cdb_settings SET language = :language");
$db2->bind(':language', $lang_code);
$result = $db2->cdp_execute();

if ($result) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'DB update failed']);
}
