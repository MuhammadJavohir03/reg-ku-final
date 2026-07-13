(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    /* ---------------- Ovozli signal (fayl kerak emas, Web Audio orqali "bip") ---------------- */
    function playBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.value = 880;
            gain.gain.value = 0.15;
            osc.connect(gain).connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.18);
        } catch (e) { /* audio bloklangan bo'lishi mumkin, jim o'tamiz */ }
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function renderBubble(message, myId) {
        const mine = message.sender_id === myId;
        const time = new Date(message.created_at).toLocaleTimeString('uz', { hour: '2-digit', minute: '2-digit' });
        const ticks = mine ? (message.status === 1 ? ' ✓✓' : ' ✓') : '';
        const div = document.createElement('div');
        div.className = 'bubble ' + (mine ? 'mine' : 'theirs');
        div.dataset.messageId = message.id;
        div.innerHTML = `<div>${escapeHtml(message.body)}</div><div class="bubble__meta">${time}${ticks}</div>`;
        return div;
    }

    /* ---------------- Xabar yuborish (forma reload bo'lmasdan) ---------------- */
    const sendForm = document.getElementById('send-form');
    const messagesBox = document.getElementById('chat-messages');
    const myId = window.CurrentUserId ?? null;

    if (sendForm && messagesBox) {
        sendForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const input = sendForm.querySelector('input[name="body"]');
            const body = input.value.trim();
            if (!body) return;

            input.value = '';
            input.disabled = true;

            try {
                const res = await fetch(sendForm.dataset.sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ body }),
                });
                const data = await res.json();

                if (!res.ok) {
                    alert(data.error || 'Xatolik yuz berdi.');
                } else {
                    const bubble = renderBubble(data.message, data.message.sender_id);
                    messagesBox.appendChild(bubble);
                    messagesBox.dataset.lastId = data.message.id;
                    messagesBox.scrollTop = messagesBox.scrollHeight;
                }
            } catch (err) {
                alert('Tarmoq xatoligi. Qayta urinib ko\'ring.');
            } finally {
                input.disabled = false;
                input.focus();
            }
        });
    }

    /* ---------------- Suhbat ichidagi polling (yangi xabarlarni tekshirish) ---------------- */
    if (messagesBox && messagesBox.dataset.pollUrl) {
        messagesBox.scrollTop = messagesBox.scrollHeight;

        setInterval(async function () {
            try {
                const url = new URL(messagesBox.dataset.pollUrl, window.location.origin);
                url.searchParams.set('after', messagesBox.dataset.lastId || 0);

                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();

                if (data.messages && data.messages.length) {
                    let hasIncoming = false;
                    data.messages.forEach(function (message) {
                        const bubble = renderBubble(message, message.receiver_id === null ? message.sender_id : (window.CurrentUserId));
                        messagesBox.appendChild(bubble);
                        messagesBox.dataset.lastId = message.id;
                        if (message.sender_id !== window.CurrentUserId) hasIncoming = true;
                    });
                    messagesBox.scrollTop = messagesBox.scrollHeight;
                    if (hasIncoming) playBeep();
                }

                // Talaba-talaba suhbatida rozilik banneri kerak bo'lsa ko'rsatamiz
                if (typeof data.needs_my_approval !== 'undefined') {
                    const banner = document.getElementById('approval-banner');
                    if (data.needs_my_approval && !banner) {
                        location.reload(); // banner hali yo'q bo'lsa - sahifani yangilaymiz
                    } else if (!data.needs_my_approval && banner) {
                        banner.remove();
                    }
                }
            } catch (e) { /* internet vaqtincha yo'q bo'lishi mumkin */ }
        }, 3000);
    }

    /* ---------------- Rozilik berish tugmasi ---------------- */
    const acceptBtn = document.getElementById('accept-btn');
    if (acceptBtn) {
        acceptBtn.addEventListener('click', async function () {
            acceptBtn.disabled = true;
            await fetch(acceptBtn.dataset.acceptUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            });
            document.getElementById('approval-banner')?.remove();
        });
    }

    /* ---------------- Foydalanuvchi / talaba qidiruv ---------------- */
    const searchInput = document.getElementById('student-search');
    const searchResults = document.getElementById('search-results');
    let searchTimer = null;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const q = searchInput.value.trim();
            searchResults.innerHTML = '';
            if (q.length < 2) return;

            searchTimer = setTimeout(async function () {
                const url = new URL(searchInput.dataset.searchUrl || window.ChatSearchUrl, window.location.origin);
                url.searchParams.set('q', q);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const users = await res.json();

                searchResults.innerHTML = '';
                users.forEach(function (u) {
                    const a = document.createElement('a');
                    a.href = (window.ChatUserUrlTemplate || '').replace('__ID__', u.id);
                    a.className = 'chat-item';
                    a.innerHTML = `<div class="chat-avatar">${(u['To‘liq_ismi'] || '?').slice(0,2).toUpperCase()}</div>
                        <div class="chat-item__body"><span class="chat-item__name">${escapeHtml(u['To‘liq_ismi'] || '')}</span>
                        <div class="chat-item__preview">${escapeHtml(u['Guruh'] || '')}</div></div>`;
                    searchResults.appendChild(a);
                });
                if (!users.length) {
                    searchResults.innerHTML = '<div style="padding:8px 16px; color:#9ca3af; font-size:13px;">Topilmadi</div>';
                }
            }, 300);
        });
    }

    /* ---------------- Sidebar uchun umumiy polling (badge + ovoz) ---------------- */
    const sidebarList = document.getElementById('sidebar-list');
    if (sidebarList && window.ChatOverviewUrl) {
        let lastSeenMessageId = null;

        setInterval(async function () {
            try {
                const res = await fetch(window.ChatOverviewUrl, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();

                if (lastSeenMessageId === null) {
                    lastSeenMessageId = data.last_message_id;
                    return;
                }
                if (data.last_message_id && data.last_message_id !== lastSeenMessageId) {
                    lastSeenMessageId = data.last_message_id;
                    playBeep();
                    // Ro'yxatni (oxirgi xabar/badge) yangilash uchun sahifani softly yangilaymiz
                    if (!document.hidden) location.reload();
                }
            } catch (e) { /* jim */ }
        }, 5000);
    }
})();
