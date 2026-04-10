(function () {
    const widget = document.getElementById('chat-widget');
    if (!widget) return;

    const toggle = document.getElementById('chat-toggle');
    const panel = document.getElementById('chat-panel');
    const close = document.getElementById('chat-close');
    const messagesEl = document.getElementById('chat-messages');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const statusEl = document.getElementById('chat-status');
    const adminButton = document.getElementById('chat-admin-button');

    let lastId = 0;
    let pollTimer = null;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderMessage(message) {
        if (document.querySelector(`[data-chat-message="${message.id}"]`)) {
            return;
        }

        const item = document.createElement('div');
        item.className = `chat-message chat-message-${message.sender_type}`;
        item.dataset.chatMessage = message.id;
        item.innerHTML = `
            <span>${escapeHtml(message.sender_name || message.sender_type)}</span>
            <p>${escapeHtml(message.message)}</p>
        `;
        messagesEl.appendChild(item);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        lastId = Math.max(lastId, Number(message.id));
    }

    function updateStatus(conversation) {
        if (!conversation) return;

        const statusText = {
            bot: 'Bot is online',
            waiting: 'Waiting for admin',
            admin: 'Admin is online',
            closed: 'Chat closed',
        };

        statusEl.textContent = statusText[conversation.status] || 'Support';
        adminButton.hidden = conversation.status !== 'bot';
        input.disabled = conversation.status === 'closed';
    }

    async function requestJson(url, options) {
        const response = await fetch(url, options);
        return response.json();
    }

    async function initChat() {
        const data = await requestJson('/chat/api.php?action=init');
        if (!data.ok) return;
        updateStatus(data.conversation);
        data.messages.forEach(renderMessage);
    }

    async function fetchMessages() {
        const data = await requestJson(`/chat/api.php?action=fetch&after_id=${lastId}`);
        if (!data.ok) return;
        updateStatus(data.conversation);
        data.messages.forEach(renderMessage);
    }

    async function sendMessage(message) {
        const body = new URLSearchParams();
        body.set('message', message);

        const data = await requestJson('/chat/api.php?action=send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body,
        });

        if (!data.ok) return;
        updateStatus(data.conversation);
        messagesEl.innerHTML = '';
        lastId = 0;
        data.messages.forEach(renderMessage);
    }

    async function requestAdmin() {
        const data = await requestJson('/chat/api.php?action=request_admin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        });

        if (!data.ok) return;
        updateStatus(data.conversation);
        messagesEl.innerHTML = '';
        lastId = 0;
        data.messages.forEach(renderMessage);
    }

    function openChat() {
        widget.classList.add('is-open');
        initChat();
        pollTimer = pollTimer || setInterval(fetchMessages, 2500);
        setTimeout(() => input.focus(), 80);
    }

    function closeChat() {
        widget.classList.remove('is-open');
    }

    toggle.addEventListener('click', openChat);
    close.addEventListener('click', closeChat);
    adminButton.addEventListener('click', requestAdmin);

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const message = input.value.trim();
        if (!message) return;
        input.value = '';
        sendMessage(message);
    });
})();
