<?php
require_once '../config/db.php';
require_once 'functions.php';

chat_require_session();

$action = $_GET['action'] ?? $_POST['action'] ?? 'init';

try {
    if ($action === 'init') {
        $conversation = chat_get_or_create_conversation($conn);
        chat_json([
            'ok' => true,
            'conversation' => $conversation,
            'messages' => chat_fetch_messages($conn, (int)$conversation['id']),
        ]);
    }

    if ($action === 'fetch') {
        $conversation = chat_get_or_create_conversation($conn);
        $afterId = max(0, (int)($_GET['after_id'] ?? 0));
        chat_json([
            'ok' => true,
            'conversation' => chat_get_conversation_by_id($conn, (int)$conversation['id']),
            'messages' => chat_fetch_messages($conn, (int)$conversation['id'], $afterId),
        ]);
    }

    if ($action === 'send') {
        $conversation = chat_get_or_create_conversation($conn);
        $message = trim($_POST['message'] ?? '');

        if ($message === '') {
            chat_json(['ok' => false, 'error' => 'Message is required.'], 422);
        }

        chat_add_message($conn, (int)$conversation['id'], 'visitor', $conversation['visitor_name'] ?? 'Guest', $message);

        if ($conversation['status'] === 'bot') {
            if (chat_wants_admin($message)) {
                chat_request_admin($conn, (int)$conversation['id']);
            } else {
                chat_add_message($conn, (int)$conversation['id'], 'bot', 'NavBot', chat_bot_reply($message));
            }
        }

        chat_json([
            'ok' => true,
            'conversation' => chat_get_conversation_by_id($conn, (int)$conversation['id']),
            'messages' => chat_fetch_messages($conn, (int)$conversation['id']),
        ]);
    }

    if ($action === 'request_admin') {
        $conversation = chat_get_or_create_conversation($conn);
        chat_request_admin($conn, (int)$conversation['id']);
        chat_json([
            'ok' => true,
            'conversation' => chat_get_conversation_by_id($conn, (int)$conversation['id']),
            'messages' => chat_fetch_messages($conn, (int)$conversation['id']),
        ]);
    }

    if ($action === 'admin_list') {
        chat_require_admin();
        chat_ensure_tables($conn);
        chat_json(['ok' => true, 'conversations' => chat_admin_list($conn)]);
    }

    if ($action === 'admin_fetch') {
        chat_require_admin();
        $conversationId = max(0, (int)($_GET['conversation_id'] ?? 0));
        $conversation = chat_get_conversation_by_id($conn, $conversationId);

        if (!$conversation) {
            chat_json(['ok' => false, 'error' => 'Conversation not found.'], 404);
        }

        chat_json([
            'ok' => true,
            'conversation' => $conversation,
            'messages' => chat_fetch_messages($conn, $conversationId),
        ]);
    }

    if ($action === 'admin_take') {
        chat_require_admin();
        $conversationId = max(0, (int)($_POST['conversation_id'] ?? 0));
        $conversation = chat_get_conversation_by_id($conn, $conversationId);

        if (!$conversation) {
            chat_json(['ok' => false, 'error' => 'Conversation not found.'], 404);
        }

        $conn->prepare("UPDATE chat_conversations SET status = 'admin', assigned_admin_id = ? WHERE id = ?")
            ->execute([$_SESSION['user']['id'], $conversationId]);
        chat_add_message($conn, $conversationId, 'system', 'System', 'Admin joined the chat.');

        chat_json(['ok' => true]);
    }

    if ($action === 'admin_send') {
        chat_require_admin();
        $conversationId = max(0, (int)($_POST['conversation_id'] ?? 0));
        $message = trim($_POST['message'] ?? '');
        $conversation = chat_get_conversation_by_id($conn, $conversationId);

        if (!$conversation) {
            chat_json(['ok' => false, 'error' => 'Conversation not found.'], 404);
        }

        if ($message === '') {
            chat_json(['ok' => false, 'error' => 'Message is required.'], 422);
        }

        $conn->prepare("UPDATE chat_conversations SET status = 'admin', assigned_admin_id = ? WHERE id = ?")
            ->execute([$_SESSION['user']['id'], $conversationId]);
        chat_add_message($conn, $conversationId, 'admin', $_SESSION['user']['name'] ?? 'Admin', $message);

        chat_json(['ok' => true]);
    }

    if ($action === 'admin_close') {
        chat_require_admin();
        $conversationId = max(0, (int)($_POST['conversation_id'] ?? 0));
        $conversation = chat_get_conversation_by_id($conn, $conversationId);

        if (!$conversation) {
            chat_json(['ok' => false, 'error' => 'Conversation not found.'], 404);
        }

        $conn->prepare("UPDATE chat_conversations SET status = 'closed' WHERE id = ?")
            ->execute([$conversationId]);
        chat_add_message($conn, $conversationId, 'system', 'System', 'Admin closed the chat.');

        chat_json(['ok' => true]);
    }

    chat_json(['ok' => false, 'error' => 'Unknown action.'], 400);
} catch (Throwable $e) {
    chat_json(['ok' => false, 'error' => 'Chat service error.'], 500);
}
?>
