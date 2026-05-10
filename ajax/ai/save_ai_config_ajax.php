<?php
// =============================================================
// SAVE AI CONFIG — stores keys in cdb_settings (DB)
// =============================================================

require_once("../../loader.php");

header('Content-Type: application/json');

$user = new User;
if (!$user->cdp_is_Admin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$groq_key   = isset($_POST['groq_api_key'])   ? trim($_POST['groq_api_key'])   : '';
$openai_key = isset($_POST['openai_api_key']) ? trim($_POST['openai_api_key']) : '';
$provider   = isset($_POST['ai_provider'])    ? trim($_POST['ai_provider'])    : 'groq';

// Allow alphanumeric, dashes, underscores, dots (all used in API keys)
$groq_key   = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $groq_key);
$openai_key = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $openai_key);
$provider   = in_array($provider, ['groq', 'openai']) ? $provider : 'groq';

// Use raw PDO to handle column creation safely
require_once("../../config/config.php");

try {
    $pdo = new PDO(
        'mysql:host=' . CDP_DB_HOST . ';dbname=' . CDP_DB_NAME . ';charset=utf8',
        CDP_DB_USER,
        CDP_DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Add columns one by one — catches error if column already exists
    $columns = [
        "ALTER TABLE cdb_settings ADD COLUMN ai_provider    VARCHAR(20)  NOT NULL DEFAULT 'groq'",
        "ALTER TABLE cdb_settings ADD COLUMN groq_api_key   VARCHAR(255) NOT NULL DEFAULT ''",
        "ALTER TABLE cdb_settings ADD COLUMN openai_api_key VARCHAR(255) NOT NULL DEFAULT ''",
    ];
    foreach ($columns as $sql) {
        try { $pdo->exec($sql); } catch (PDOException $e) { /* column already exists — ignore */ }
    }

    // Now update
    $stmt = $pdo->prepare("UPDATE cdb_settings SET
        ai_provider    = :ai_provider,
        groq_api_key   = :groq_api_key,
        openai_api_key = :openai_api_key");
    $stmt->execute([
        ':ai_provider'    => $provider,
        ':groq_api_key'   => $groq_key,
        ':openai_api_key' => $openai_key,
    ]);

    echo json_encode(['success' => true, 'message' => 'AI settings saved. P-AI is now active on your dashboard.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}
