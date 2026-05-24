<?php
// backend/api/update_score.php
require_once 'db_connect.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'No JSON data received.']);
    exit();
}

$player_name = trim($input['player_name'] ?? '');
$score = (int)($input['score'] ?? 0);
$avatar = $input['avatar'] ?? '';

if (empty($player_name)) {
    echo json_encode(['success' => false, 'message' => 'Player name is required.']);
    exit();
}

try {
    // Check if player exists in leaderboard
    $stmt = $pdo->prepare("SELECT score FROM leaderboard WHERE player_name = ?");
    $stmt->execute([$player_name]);
    $existing = $stmt->fetch();

    if ($existing) {
        if ($score > $existing['score']) {
            $upd = $pdo->prepare("UPDATE leaderboard SET score = ?, avatar = ? WHERE player_name = ?");
            $upd->execute([$score, $avatar, $player_name]);
            echo json_encode(['success' => true, 'message' => 'Score updated successfully.']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Score is not higher than existing high score.']);
        }
    } else {
        $ins = $pdo->prepare("INSERT INTO leaderboard (player_name, score, avatar) VALUES (?, ?, ?)");
        $ins->execute([$player_name, $score, $avatar]);
        echo json_encode(['success' => true, 'message' => 'New player score added to leaderboard.']);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
