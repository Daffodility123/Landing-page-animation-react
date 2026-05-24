<?php
// backend/api/leaderboard.php

// CORS Headers to allow React frontend to communicate with PHP backend
header("Access-Control-Allow-Origin: *"); // For development only. In production, specify the exact domain.
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db_connect.php';
header('Content-Type: application/json');

try {
    // Get top 5 scores
    $stmt = $pdo->query("
        SELECT player_name, score, avatar, updated_at 
        FROM leaderboard 
        ORDER BY score DESC 
        LIMIT 5
    ");
    $leaderboard = $stmt->fetchAll();

    // Assign rank
    $rank = 1;
    foreach ($leaderboard as &$entry) {
        $entry['rank'] = $rank++;
    }

    echo json_encode([
        'success' => true,
        'data' => $leaderboard
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
