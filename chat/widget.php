<div class="chat-widget" id="chat-widget">
    <button type="button" class="chat-toggle" id="chat-toggle" aria-label="Open chat">
        Chat
    </button>

    <section class="chat-panel" id="chat-panel" aria-live="polite">
        <div class="chat-header">
            <div>
                <strong>NavShop Support</strong>
                <span id="chat-status">Bot is online</span>
            </div>
            <button type="button" id="chat-close" aria-label="Close chat">x</button>
        </div>

        <div class="chat-messages" id="chat-messages"></div>

        <div class="chat-actions" id="chat-actions">
            <button type="button" class="chat-admin-button" id="chat-admin-button">Chat with admin</button>
        </div>

        <form class="chat-form" id="chat-form">
            <input type="text" id="chat-input" placeholder="Type your message..." autocomplete="off">
            <button type="submit">Send</button>
        </form>
    </section>
</div>

<script src="/assets/js/chat-widget.js"></script>
