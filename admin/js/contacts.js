/* admin/js/contacts.js — Admin Contact Messages Management */
(function () {
    'use strict';

    /* ── Message Card Click → Show Detail ─────── */
    document.querySelectorAll('.msg-card').forEach(card => {
        card.addEventListener('click', function () {
            const id = this.dataset.msgId;
            document.querySelectorAll('.msg-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            loadMessageDetail(id);
            if (this.classList.contains('unread')) markAsRead(id, this);
        });
    });

    function loadMessageDetail(id) {
        const panel = document.getElementById('msgDetailPanel');
        if (!panel) return;
        panel.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x" style="color:#6C3483"></i></div>';

        fetch(`../php/contact_detail_handler.php?id=${id}`)
            .then(r => r.json())
            .then(data => {
                panel.innerHTML = `
                    <div class="msg-detail-panel">
                        <div class="msg-detail-meta">
                            <div class="msg-detail-meta-item"><div class="msg-detail-meta-label">From</div><div class="msg-detail-meta-value">${data.name} &lt;${data.email}&gt;</div></div>
                            ${data.phone ? `<div class="msg-detail-meta-item"><div class="msg-detail-meta-label">Phone</div><div class="msg-detail-meta-value">${data.phone}</div></div>` : ''}
                            <div class="msg-detail-meta-item"><div class="msg-detail-meta-label">Subject</div><div class="msg-detail-meta-value">${data.subject || '—'}</div></div>
                            <div class="msg-detail-meta-item"><div class="msg-detail-meta-label">Received</div><div class="msg-detail-meta-value">${data.created_at}</div></div>
                        </div>
                        <div class="msg-detail-body">${escapeHtml(data.message)}</div>
                        <div class="reply-box mt-4">
                            <div class="msg-detail-meta-label mb-2" style="font-size:12px">Reply to ${data.email}</div>
                            <textarea id="replyContent" placeholder="Write your reply…"></textarea>
                            <div class="d-flex gap-2 mt-3">
                                <button class="btn btn-sm btn-primary" id="sendReplyBtn" data-contact-id="${data.id}">
                                    <i class="fas fa-paper-plane me-1"></i> Send Reply
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="markResolvedBtn" data-contact-id="${data.id}">
                                    <i class="fas fa-check me-1"></i> Mark Resolved
                                </button>
                            </div>
                        </div>
                    </div>`;

                document.getElementById('sendReplyBtn')?.addEventListener('click', () => sendReply(id));
                document.getElementById('markResolvedBtn')?.addEventListener('click', () => markResolved(id));
            });
    }

    function markAsRead(id, card) {
        card.classList.remove('unread');
        card.querySelector('.msg-status-badge')?.remove();
        const fd = new FormData();
        fd.append('id', id); fd.append('action', 'read');
        fetch('../php/contact_detail_handler.php', { method: 'POST', body: fd });
    }

    function sendReply(id) {
        const content = document.getElementById('replyContent')?.value.trim();
        if (!content) { alert('Please write a reply first.'); return; }
        const fd = new FormData();
        fd.append('id', id); fd.append('action', 'reply'); fd.append('message', content);
        const btn = document.getElementById('sendReplyBtn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending…'; btn.disabled = true;
        fetch('../php/contact_detail_handler.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.adminShowToast?.('Reply sent successfully!', 'success');
                    document.querySelector(`.msg-card[data-msg-id="${id}"] .msg-status-badge`)?.classList.replace('new', 'replied');
                    document.getElementById('replyContent').value = '';
                } else window.adminShowToast?.('Failed to send reply.', 'error');
            })
            .finally(() => { btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Send Reply'; btn.disabled = false; });
    }

    function markResolved(id) {
        const fd = new FormData();
        fd.append('id', id); fd.append('action', 'resolve');
        fetch('../php/contact_detail_handler.php', { method: 'POST', body: fd })
            .then(() => { window.adminShowToast?.('Marked as resolved', 'success'); document.querySelector(`.msg-card[data-msg-id="${id}"]`)?.remove(); document.getElementById('msgDetailPanel').innerHTML = ''; });
    }

    /* ── Bulk Delete ──────────────────────────── */
    document.getElementById('bulkDeleteMsgs')?.addEventListener('click', function () {
        const ids = [...document.querySelectorAll('.msg-checkbox:checked')].map(cb => cb.value);
        if (!ids.length) return;
        if (!confirm(`Delete ${ids.length} message(s)?`)) return;
        const fd = new FormData();
        ids.forEach(id => fd.append('ids[]', id));
        fd.append('action', 'bulk_delete');
        fetch('../php/contact_detail_handler.php', { method: 'POST', body: fd })
            .then(() => location.reload());
    });

    /* ── Helpers ──────────────────────────────── */
    function escapeHtml(str) { const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }

})();
