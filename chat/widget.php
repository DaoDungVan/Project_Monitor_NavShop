<div class="chat-widget" id="chat-widget">
    <button type="button" class="chat-toggle" id="chat-toggle" aria-label="Open chat">
        <svg class="chat-toggle-icon chat-toggle-icon-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="26" height="26"><path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/></svg>
        <svg class="chat-toggle-icon chat-toggle-icon-close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
    </button>

    <section class="chat-panel" id="chat-panel" aria-live="polite">
        <div class="chat-header">
            <div>
                <strong>NavShop Support</strong>
                <span id="chat-status">Bot is online</span>
            </div>
            <button type="button" id="chat-close" aria-label="Close chat">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
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
