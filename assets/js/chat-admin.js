(function () {
    const listEl = document.getElementById('chat-admin-list');
    if (!listEl) return;

    const titleEl = document.getElementById('chat-admin-title');
    const metaEl = document.getElementById('chat-admin-meta');
    const messagesEl = document.getElementById('chat-admin-messages');
    const form = document.getElementById('chat-admin-form');
    const input = document.getElementById('chat-admin-input');
    const sendButton = document.getElementById('chat-admin-send');
    const takeButton = document.getElementById('chat-take-button');
    const closeButton = document.getElementById('chat-close-button');

    let activeId = null;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function requestJson(url, options) {
        const response = await fetch(url, options);
        return response.json();
    }

    function statusLabel(status) {
        return {
            bot: 'Bot',
            waiting: 'Waiting',
            admin: 'Admin',
            closed: 'Closed',
        }[status] || status;
    }

    async function loadList() {
        const data = await requestJson('/chat/api.php?action=admin_list');
        if (!data.ok) return;

        if (data.conversations.length === 0) {
            listEl.innerHTML = '<p class="text-muted">No conversations yet.</p>';
            return;
        }

        listEl.innerHTML = data.conversations.map((conversation) => `
            <button type="button" class="chat-admin-item ${Number(conversation.id) === Number(activeId) ? 'active' : ''}" data-id="${conversation.id}">
                <span>
                    <strong>#${conversation.id} ${escapeHtml(conversation.visitor_name || 'Guest')}</strong>
                    <small>${escapeHtml(conversation.last_message || 'No message yet')}</small>
                </span>
                <em class="chat-status-${conversation.status}">${statusLabel(conversation.status)}</em>
            </button>
        `).join('');
    }

    async function loadConversation(id) {
        activeId = id;
        const data = await requestJson(`/chat/api.php?action=admin_fetch&conversation_id=${id}`);
        if (!data.ok) return;

        titleEl.textContent = `Conversation #${data.conversation.id}`;
        metaEl.textContent = `${data.conversation.visitor_name || 'Guest'} - ${statusLabel(data.conversation.status)}`;
        input.disabled = data.conversation.status === 'closed';
        sendButton.disabled = data.conversation.status === 'closed';
        takeButton.disabled = data.conversation.status === 'closed';
        closeButton.disabled = data.conversation.status === 'closed';

        messagesEl.innerHTML = data.messages.map((message) => `
            <div class="chat-message chat-message-${message.sender_type}">
                <span>${escapeHtml(message.sender_name || message.sender_type)}</span>
                <p>${escapeHtml(message.message)}</p>
            </div>
        `).join('');
        messagesEl.scrollTop = messagesEl.scrollHeight;
        loadList();
    }

    async function postAction(action, params) {
        const body = new URLSearchParams(params);
        const data = await requestJson(`/chat/api.php?action=${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body,
        });
        return data.ok;
    }

    listEl.addEventListener('click', function (event) {
        const item = event.target.closest('[data-id]');
        if (!item) return;
        loadConversation(item.dataset.id);
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!activeId) return;

        const message = input.value.trim();
        if (!message) return;

        input.value = '';
        if (await postAction('admin_send', { conversation_id: activeId, message })) {
            loadConversation(activeId);
        }
    });

    takeButton.addEventListener('click', async function () {
        if (!activeId) return;
        if (await postAction('admin_take', { conversation_id: activeId })) {
            loadConversation(activeId);
        }
    });

    closeButton.addEventListener('click', async function () {
        if (!activeId) return;
        if (await postAction('admin_close', { conversation_id: activeId })) {
            loadConversation(activeId);
        }
    });

    loadList();
    setInterval(loadList, 3000);
    setInterval(function () {
        if (activeId) loadConversation(activeId);
    }, 3000);
})();
