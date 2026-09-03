{{-- ============================================================
     SWEETALERT2 — Toast & Confirm Helper
     Substitui alerts nativos e intercepta flash messages Laravel.
     ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toast global (canto superior direito)
    const SwalToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Interceptador de flash messages Laravel
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            SwalToast.fire({ icon: 'success', title: @json(session('success')) });
        @endif
        @if (session('status'))
            SwalToast.fire({ icon: 'success', title: @json(session('status')) });
        @endif
        @if (session('warning'))
            SwalToast.fire({ icon: 'warning', title: @json(session('warning')) });
        @endif
        @if (session('info'))
            SwalToast.fire({ icon: 'info', title: @json(session('info')) });
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Atenção',
                text: @json(session('error')),
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
                confirmButtonText: 'Compreendido'
            });
        @endif
        @if ($errors->any() && !$errors->hasBag('default_bag_error_handled'))
            Swal.fire({
                icon: 'error',
                title: 'Verifique os dados',
                html: '<ul style="text-align: left; font-size: 13px; color: #334155; padding-left: 10px;">@foreach ($errors->all() as $err)<li style="margin-bottom: 4px;">• {{ addslashes($err) }}</li>@endforeach</ul>',
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
                confirmButtonText: 'Corrigir'
            });
        @endif
    });

    // Confirmação antes de submit de formulário (substitui onclick="return confirm(...)")
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.dataset.confirm && !form.dataset.confirmed) {
            e.preventDefault();
            Swal.fire({
                title: form.dataset.confirmTitle || 'Tem a certeza?',
                text: form.dataset.confirm,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: form.dataset.confirmText || 'Sim, confirmar!',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-2xl shadow-2xl font-sans' }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        }
    }, true);

    // Confirmação para botões com data-confirm (substitui onclick="return confirm(...)")
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-confirm]');
        if (!btn) return;
        // Se for um submit-button dentro de form já tratado, ignora
        if (btn.type === 'submit' && btn.closest('form')?.dataset.confirm) return;

        e.preventDefault();
        e.stopPropagation();
        Swal.fire({
            title: btn.dataset.confirmTitle || 'Tem a certeza?',
            text: btn.dataset.confirm,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: btn.dataset.confirmText || 'Sim, confirmar!',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl shadow-2xl font-sans' }
        }).then((result) => {
            if (!result.isConfirmed) return;
            const form = btn.closest('form');
            if (form) {
                form.dataset.confirmed = '1';
                form.submit();
            } else if (btn.tagName === 'A' && btn.href) {
                window.location.href = btn.href;
            }
        });
    }, true);

    // Helper global para confirmações programáticas
    window.confirmSwal = function (message, title = 'Tem a certeza?') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sim, confirmar!',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl shadow-2xl font-sans' }
        }).then((r) => r.isConfirmed);
    };

    // Substitui window.alert por um Swal modal
    window.alert = function (message) {
        Swal.fire({
            icon: 'info',
            title: message,
            confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
            customClass: { popup: 'rounded-2xl shadow-2xl font-sans' }
        });
    };
</script>