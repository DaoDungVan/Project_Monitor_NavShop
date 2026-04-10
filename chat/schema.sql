CREATE TABLE IF NOT EXISTS chat_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_token VARCHAR(64) NOT NULL UNIQUE,
    visitor_name VARCHAR(120) NULL,
    visitor_email VARCHAR(150) NULL,
    status ENUM('bot','waiting','admin','closed') NOT NULL DEFAULT 'bot',
    assigned_admin_id INT NULL,
    last_message_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_chat_status_updated (status, updated_at),
    INDEX idx_chat_last_message (last_message_at)
);

CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_type ENUM('visitor','bot','admin','system') NOT NULL,
    sender_name VARCHAR(120) NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
    INDEX idx_chat_messages_conversation (conversation_id, id)
);
