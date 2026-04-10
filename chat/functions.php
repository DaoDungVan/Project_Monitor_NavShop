<?php

function chat_require_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function chat_json(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

function chat_require_admin(): void
{
    chat_require_session();
    if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
        chat_json(['ok' => false, 'error' => 'Unauthorized'], 403);
    }
}

function chat_ensure_tables(PDO $conn): void
{
    $conn->exec("
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
        )
    ");

    $conn->exec("
        CREATE TABLE IF NOT EXISTS chat_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            conversation_id INT NOT NULL,
            sender_type ENUM('visitor','bot','admin','system') NOT NULL,
            sender_name VARCHAR(120) NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE,
            INDEX idx_chat_messages_conversation (conversation_id, id)
        )
    ");
}

function chat_session_token(): string
{
    chat_require_session();
    if (empty($_SESSION['chat_token'])) {
        $_SESSION['chat_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['chat_token'];
}

function chat_get_or_create_conversation(PDO $conn): array
{
    chat_ensure_tables($conn);

    $token = chat_session_token();
    $stmt = $conn->prepare("SELECT * FROM chat_conversations WHERE session_token = ?");
    $stmt->execute([$token]);
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($conversation) {
        return $conversation;
    }

    $visitorName = $_SESSION['user']['name'] ?? 'Guest';
    $stmt = $conn->prepare("
        INSERT INTO chat_conversations (session_token, visitor_name, status)
        VALUES (?, ?, 'bot')
    ");
    $stmt->execute([$token, $visitorName]);
    $conversationId = (int)$conn->lastInsertId();

    chat_add_message(
        $conn,
        $conversationId,
        'bot',
        'NavBot',
        'Hi, I am NavBot. Ask me about products, prices, orders, or delivery. If you need a real person, choose Chat with admin.'
    );

    $stmt = $conn->prepare("SELECT * FROM chat_conversations WHERE id = ?");
    $stmt->execute([$conversationId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function chat_get_conversation_by_id(PDO $conn, int $conversationId): ?array
{
    $stmt = $conn->prepare("SELECT * FROM chat_conversations WHERE id = ?");
    $stmt->execute([$conversationId]);
    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);
    return $conversation ?: null;
}

function chat_add_message(PDO $conn, int $conversationId, string $senderType, ?string $senderName, string $message): int
{
    $stmt = $conn->prepare("
        INSERT INTO chat_messages (conversation_id, sender_type, sender_name, message)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$conversationId, $senderType, $senderName, $message]);

    $conn->prepare("UPDATE chat_conversations SET last_message_at = NOW() WHERE id = ?")
        ->execute([$conversationId]);

    return (int)$conn->lastInsertId();
}

function chat_fetch_messages(PDO $conn, int $conversationId, int $afterId = 0): array
{
    $stmt = $conn->prepare("
        SELECT id, sender_type, sender_name, message, created_at
        FROM chat_messages
        WHERE conversation_id = ? AND id > ?
        ORDER BY id ASC
    ");
    $stmt->execute([$conversationId, $afterId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function chat_wants_admin(string $message): bool
{
    $text = function_exists('mb_strtolower') ? mb_strtolower($message, 'UTF-8') : strtolower($message);
    $keywords = ['admin', 'nhan vien', 'nhân viên', 'tu van', 'tư vấn', 'ho tro', 'hỗ trợ', 'nguoi that', 'người thật'];

    foreach ($keywords as $keyword) {
        if (str_contains($text, $keyword)) {
            return true;
        }
    }

    return false;
}

function chat_bot_reply(string $message): string
{
    $text = function_exists('mb_strtolower') ? mb_strtolower($message, 'UTF-8') : strtolower($message);

    if (str_contains($text, 'gia') || str_contains($text, 'giá') || str_contains($text, 'price')) {
        return 'You can use the price filter on the shop page. For an exact quote, choose Chat with admin and our team will help.';
    }

    if (str_contains($text, 'ship') || str_contains($text, 'delivery') || str_contains($text, 'giao')) {
        return 'Delivery time depends on your address. Please choose Chat with admin if you want us to check shipping details.';
    }

    if (str_contains($text, 'order') || str_contains($text, 'don hang') || str_contains($text, 'đơn hàng')) {
        return 'Logged-in users can check orders in My Orders. If you need help with a specific order, choose Chat with admin.';
    }

    if (str_contains($text, 'oled') || str_contains($text, 'ips') || str_contains($text, 'va') || str_contains($text, '2k') || str_contains($text, '4k')) {
        return 'For gaming, choose high refresh IPS or OLED. For office work, IPS is a safe choice. You can filter products on the shop page.';
    }

    return 'I can answer common questions about monitors, price, delivery, and orders. If you want direct support, choose Chat with admin.';
}

function chat_request_admin(PDO $conn, int $conversationId): void
{
    $conversation = chat_get_conversation_by_id($conn, $conversationId);
    if (!$conversation || $conversation['status'] === 'waiting' || $conversation['status'] === 'admin') {
        return;
    }

    $conn->prepare("UPDATE chat_conversations SET status = 'waiting' WHERE id = ?")
        ->execute([$conversationId]);

    chat_add_message(
        $conn,
        $conversationId,
        'system',
        'System',
        'This chat has been transferred to admin. Please wait for a reply here.'
    );
}

function chat_admin_list(PDO $conn): array
{
    $stmt = $conn->query("
        SELECT c.*,
               (SELECT message FROM chat_messages m WHERE m.conversation_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_message,
               (SELECT COUNT(*) FROM chat_messages m WHERE m.conversation_id = c.id) AS message_count
        FROM chat_conversations c
        ORDER BY FIELD(c.status, 'waiting', 'admin', 'bot', 'closed'), c.last_message_at DESC
        LIMIT 100
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
