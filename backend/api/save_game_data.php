<?php
// backend/api/save_game_data.php
require_once 'db_connect.php';

header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'No JSON data received.']);
    exit();
}

// Validate required fields
$required_fields = ['player_name', 'selected_avatar', 'score', 'level', 'health', 'game_result'];
foreach ($required_fields as $field) {
    if (!isset($input[$field]) || (empty($input[$field]) && $input[$field] !== 0)) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit();
    }
}

$player_name = trim($input['player_name']);
$selected_avatar = $input['selected_avatar'];
$avatar_before = $input['avatar_before'] ?? null;
$avatar_after = $input['avatar_after'] ?? null;
$score = (int)$input['score'];
$high_score = (int)($input['high_score'] ?? $score);
$level = (int)$input['level'];
$health = (int)$input['health'];
$status = $input['status'] ?? 'completed';
$game_result = $input['game_result'];
$play_time = (int)($input['play_time'] ?? 0);

if (empty($player_name)) {
    echo json_encode(['success' => false, 'message' => 'Player name cannot be empty.']);
    exit();
}
if ($score < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid score.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Insert game record
    $stmt = $pdo->prepare("INSERT INTO player_game_records 
        (player_name, selected_avatar, avatar_before, avatar_after, score, high_score, level, health, status, game_result, play_time) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $player_name, $selected_avatar, $avatar_before, $avatar_after, 
        $score, $high_score, $level, $health, $status, $game_result, $play_time
    ]);

    // Update leaderboard logic
    $lb_stmt = $pdo->prepare("SELECT score FROM leaderboard WHERE player_name = ?");
    $lb_stmt->execute([$player_name]);
    $existing_lb = $lb_stmt->fetch();

    if ($existing_lb) {
        if ($score > $existing_lb['score']) {
            $upd_lb = $pdo->prepare("UPDATE leaderboard SET score = ?, avatar = ? WHERE player_name = ?");
            $upd_lb->execute([$score, $selected_avatar, $player_name]);
        }
    } else {
        $ins_lb = $pdo->prepare("INSERT INTO leaderboard (player_name, score, avatar) VALUES (?, ?, ?)");
        $ins_lb->execute([$player_name, $score, $selected_avatar]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Game data saved successfully.'
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
