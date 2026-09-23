@props(['label' => 'Kembali', 'ariaLabel' => null])

<a {{ $attributes->merge(['class' => 'af-back-button']) }} data-back-button aria-label="{{ $ariaLabel ?? $label }}">
    <span class="af-back-button__arrow" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
            <path d="M19 12H5"></path><path d="m12 19-7-7 7-7"></path>
        </svg>
    </span>
    <span class="af-back-button__home" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
            <path d="m3 10 9-7 9 7"></path><path d="M5 9v11h14V9"></path><path d="M9.5 20v-6h5v6"></path>
        </svg>
    </span>
    <span class="af-back-button__text">{{ $label }}</span>
</a>

<script>
    (() => {
        const button = document.currentScript.previousElementSibling;
        if (!button?.matches('[data-back-button]') || button.dataset.backButtonReady) return;
        button.dataset.backButtonReady = 'true';
        const createRipple = (event = {}) => {
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
            if ((event.key === 'Enter' || event.key === ' ') && !event.repeat) createRipple();
        });
    })();
</script>
