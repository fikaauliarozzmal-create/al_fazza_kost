@if($pembayaran->contains(fn ($item) => filled($item->bukti_bayar)))
    <div class="proof-viewer" data-tenant-proof-viewer aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="tenant-proof-viewer-title">
        <div class="proof-viewer-backdrop" data-proof-close></div>

        <section class="proof-viewer-panel" tabindex="-1">
            <header class="proof-viewer-header">
                <div>
                    <span class="proof-viewer-kicker">Al Fazza Kost</span>
                    <h2 id="tenant-proof-viewer-title">Bukti Pembayaran</h2>
                    <p data-proof-description></p>
                </div>

                <button type="button" class="proof-viewer-close" data-proof-close aria-label="Tutup bukti pembayaran">
                    <span class="proof-viewer-close-icon" aria-hidden="true">×</span>
                    <span>Tutup</span>
                </button>
            </header>

            <div class="proof-viewer-preview">
                <img data-proof-image class="proof-viewer-image" src="" alt="" hidden>
            </div>
        </section>
    </div>

    <script>
        (() => {
            const viewer = document.querySelector('[data-tenant-proof-viewer]');
            const panel = viewer?.querySelector('.proof-viewer-panel');
            const image = viewer?.querySelector('[data-proof-image]');
            const description = viewer?.querySelector('[data-proof-description]');

            if (!viewer || !panel || !image || !description) return;

            let opener = null;

            const closeViewer = () => {
                if (!viewer.classList.contains('is-open')) return;

                viewer.classList.remove('is-open');
                viewer.setAttribute('aria-hidden', 'true');
                image.removeAttribute('src');
                image.alt = '';
                image.hidden = true;
                opener?.focus();
            };

            document.querySelectorAll('[data-tenant-proof-open]').forEach((button) => {
                button.addEventListener('click', () => {
                    opener = button;
                    image.src = button.dataset.proofUrl;
                    image.alt = `Bukti pembayaran ${button.dataset.proofDescription}`;
                    image.hidden = false;
                    description.textContent = button.dataset.proofDescription;
                    viewer.classList.add('is-open');
                    viewer.setAttribute('aria-hidden', 'false');
                    panel.focus();
                });
            });

            viewer.querySelectorAll('[data-proof-close]').forEach((button) => {
                button.addEventListener('click', closeViewer);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeViewer();
            });
        })();
    </script>
@endif
