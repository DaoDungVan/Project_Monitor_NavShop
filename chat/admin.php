<?php require_once '../includes/header_admin.php'; ?>

<div class="page-header-bar">
    <div>
        <p class="eyebrow">Support</p>
        <h1 class="page-title">Live Chat</h1>
        <p class="page-subtitle">Reply to visitors without moving them away from the shop page.</p>
    </div>
</div>

<div class="chat-admin-layout">
    <aside class="chat-admin-list" id="chat-admin-list">
        <p class="text-muted">Loading conversations...</p>
    </aside>

    <section class="chat-admin-room">
        <div class="chat-admin-room-header">
            <div>
                <strong id="chat-admin-title">Select a conversation</strong>
                <span id="chat-admin-meta">Waiting chats appear first.</span>
            </div>
            <div class="chat-admin-room-actions">
                <button type="button" class="btn btn-navy btn-sm" id="chat-take-button" disabled>Take chat</button>
                <button type="button" class="btn btn-gray btn-sm" id="chat-close-button" disabled>Close</button>
            </div>
        </div>

        <div class="chat-admin-messages" id="chat-admin-messages">
            <p class="text-muted">Choose a conversation on the left.</p>
        </div>

        <form class="chat-admin-form" id="chat-admin-form">
            <input type="text" id="chat-admin-input" class="form-control" placeholder="Reply as admin..." disabled>
            <button type="submit" class="btn btn-green" disabled id="chat-admin-send">Send</button>
        </form>
    </section>
</div>

<script src="/assets/js/chat-admin.js"></script>

<?php require_once '../includes/footer_admin.php'; ?>
