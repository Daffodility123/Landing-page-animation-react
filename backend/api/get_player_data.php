<?php
// backend/api/get_player_data.php
require_once 'db_connect.php';

header('Content-Type: application/json');

$player_name = isset($_GET['player_name']) ? trim($_GET['player_name']) : '';

if (empty($player_name)) {
    echo json_encode(['success' => false, 'message' => 'Player name is required.']);
    exit();
}

try {
    // Get player's game history
    $stmt = $pdo->prepare("
        SELECT id, selected_avatar, score, high_score, level, health, game_result, play_time, created_at 
        FROM player_game_records 
        WHERE player_name = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$player_name]);
    $history = $stmt->fetchAll();

    // Get player's overall high score from leaderboard
    $lb_stmt = $pdo->prepare("SELECT score, rank FROM leaderboard WHERE player_name = ?");
    $lb_stmt->execute([$player_name]);
    $leaderboard_data = $lb_stmt->fetch();

    echo json_encode([
        'success' => true,
        'data' => [
            'player_name' => $player_name,
            'history' => $history,
            'leaderboard' => $leaderboard_data
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
