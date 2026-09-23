document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-back-button]').forEach((button) => {
        if (button.dataset.backButtonReady) return;
        button.dataset.backButtonReady = 'true';
        const createRipple = (event) => {
            const rect = button.getBoundingClientRect();
            const point = event.touches?.[0] || event.changedTouches?.[0] || event;
            const ripple = document.createElement('span');
            ripple.className = 'af-back-button__ripple';
            ripple.style.left = `${point.clientX ? point.clientX - rect.left : rect.width / 2}px`;
            ripple.style.top = `${point.clientY ? point.clientY - rect.top : rect.height / 2}px`;
            button.querySelectorAll('.af-back-button__ripple').forEach((item) => item.remove());
            button.append(ripple);
            ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
        };
        button.addEventListener('pointerdown', createRipple, { passive: true });
        button.addEventListener('keydown', (event) => {
            if ((event.key === 'Enter' || event.key === ' ') && !event.repeat) createRipple({ clientX: 0, clientY: 0 });
        });
    });

    const widget = document.querySelector('[data-notification-widget]');
    if (!widget) return;

    const trigger = widget.querySelector('[data-notification-toggle]');
    const panel = widget.querySelector('[data-notification-panel]');
    const badge = widget.querySelector('[data-notification-badge]');
    const list = widget.querySelector('[data-notification-list]');
    const readAll = widget.querySelector('[data-notification-read-all]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;
    const seenKey = 'al-fazza-notification-last-id';
    let audioAllowed = false;

    document.addEventListener('pointerdown', () => { audioAllowed = true; }, { once: true });
    const request = (url, options = {}) => fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf, ...(options.headers || {}) }, ...options });
    const beep = () => {
        if (!audioAllowed || !window.AudioContext) return;
        const context = new AudioContext(); const oscillator = context.createOscillator(); const gain = context.createGain();
        oscillator.frequency.value = 740; gain.gain.setValueAtTime(.04, context.currentTime); gain.gain.exponentialRampToValueAtTime(.001, context.currentTime + .14);
        oscillator.connect(gain).connect(context.destination); oscillator.start(); oscillator.stop(context.currentTime + .14);
    };
    const render = (data) => {
        badge.hidden = !data.unread_count; badge.textContent = data.unread_count || '';
        list.replaceChildren();
        if (!data.notifications.length) { list.innerHTML = '<p class="notification-empty">Belum ada notifikasi.</p>'; return; }
        data.notifications.forEach((item) => {
            const button = document.createElement('button'); button.type = 'button'; button.className = `notification-item${item.dibaca ? '' : ' unread'}`;
            const title = document.createElement('strong'); title.textContent = item.judul;
            const message = document.createElement('span'); message.textContent = item.pesan;
            const time = document.createElement('small'); time.textContent = item.waktu || '';
            button.append(title, message, time);
            button.addEventListener('click', async () => { await request(`${widget.dataset.readUrl}/${item.id}/baca`, { method: 'PATCH' }); refresh(); }); list.append(button);
        });
    };
    const refresh = async () => {
        try { const data = await request(widget.dataset.feedUrl).then((r) => r.ok ? r.json() : null); if (!data) return; const newest = data.notifications[0]?.id; const previous = Number(localStorage.getItem(seenKey) || 0); if (newest && previous && newest > previous) beep(); if (newest) localStorage.setItem(seenKey, newest); render(data); } catch (_) { /* visual notifications remain usable on next poll */ }
    };
    trigger.addEventListener('click', () => { panel.hidden = !panel.hidden; trigger.setAttribute('aria-expanded', String(!panel.hidden)); if (!panel.hidden) refresh(); });
    readAll.addEventListener('click', async () => { await request(widget.dataset.readAllUrl, { method: 'PATCH' }); refresh(); });
    document.addEventListener('click', (event) => { if (!widget.contains(event.target)) panel.hidden = true; });
    refresh(); setInterval(refresh, 15000);
});
