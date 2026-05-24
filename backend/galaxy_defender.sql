-- Database creation and use
CREATE DATABASE IF NOT EXISTS galaxy_defender;
USE galaxy_defender;

-- 1. player_game_records table
CREATE TABLE IF NOT EXISTS player_game_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_name VARCHAR(100) NOT NULL,
    selected_avatar VARCHAR(50) NOT NULL,
    avatar_before VARCHAR(50),
    avatar_after VARCHAR(50),
    score INT NOT NULL DEFAULT 0,
    high_score INT NOT NULL DEFAULT 0,
    level INT NOT NULL DEFAULT 1,
    health INT NOT NULL DEFAULT 0,
    status VARCHAR(50) DEFAULT 'completed',
    game_result VARCHAR(50) NOT NULL, -- e.g., 'Win', 'Lose'
    play_time INT NOT NULL DEFAULT 0, -- play duration in seconds
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. avatars table (Recommended)
CREATE TABLE IF NOT EXISTS avatars (
    avatar_id VARCHAR(50) PRIMARY KEY,
    avatar_name VARCHAR(100) NOT NULL,
    avatar_image VARCHAR(255) NOT NULL,
    avatar_rarity VARCHAR(50) DEFAULT 'Common',
    avatar_status VARCHAR(50) DEFAULT 'Unlocked'
);

-- Insert default avatars based on the React app
INSERT IGNORE INTO avatars (avatar_id, avatar_name, avatar_image) VALUES
('avatar1', 'Commander Alpha', 'game_image.jpg'),
('avatar2', 'Starfighter Beta', 'game_image.1.jpg'),
('avatar3', 'Galactic Ranger', 'gmail_image2.jpg'),
('avatar4', 'Nebula Explorer', 'game_image3.jpg');

-- 3. leaderboard table (Recommended)
CREATE TABLE IF NOT EXISTS leaderboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_name VARCHAR(100) NOT NULL UNIQUE,
    score INT NOT NULL DEFAULT 0,
    rank INT DEFAULT 0,
    avatar VARCHAR(50),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
