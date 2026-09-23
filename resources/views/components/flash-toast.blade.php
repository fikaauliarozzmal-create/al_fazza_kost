@if(session('success') || $errors->any())
    <div class="flash-toast-stack" aria-live="polite" aria-atomic="true" data-flash-toast-stack>
        @if(session('success'))
            <section class="flash-toast flash-toast--success" role="status" data-flash-toast data-dismiss-after="5000">
                <span class="flash-toast__icon" aria-hidden="true">✓</span>
                <div class="flash-toast__content">
                    <strong>Berhasil</strong>
                    <p>{{ session('success') }}</p>
                </div>
                <button class="flash-toast__close" type="button" aria-label="Tutup notifikasi" data-flash-toast-close>×</button>
            </section>
        @endif

        @if($errors->any())
            <section class="flash-toast flash-toast--error" role="alert" data-flash-toast data-dismiss-after="8000">
                <span class="flash-toast__icon" aria-hidden="true">!</span>
                <div class="flash-toast__content">
                    <strong>Tindakan belum dapat dilakukan</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button class="flash-toast__close" type="button" aria-label="Tutup notifikasi" data-flash-toast-close>×</button>
            </section>
        @endif
    </div>

    <script>
    (() => {
        document.querySelectorAll('[data-flash-toast]').forEach((toast) => {
            const dismiss = () => {
                if (toast.dataset.dismissed) return;
                toast.dataset.dismissed = 'true';
                toast.classList.add('is-leaving');
                window.setTimeout(() => toast.remove(), 240);
            };

            toast.querySelector('[data-flash-toast-close]')?.addEventListener('click', dismiss);
            window.setTimeout(dismiss, Number(toast.dataset.dismissAfter || 6000));
        });
    })();
    </script>
@endif
