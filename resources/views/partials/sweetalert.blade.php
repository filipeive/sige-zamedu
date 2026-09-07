{{-- ============================================================
     SWEETALERT2 — Toast & Confirm Helper (Padronizado + Z-Index Fit)
     Garante Toasts no canto superior direito para notificações,
     SweetAlert com Confirmação e gestão dinâmica de Z-Index para modais.
     ============================================================ --}}
<style>
    /* Elevação hierárquica de Z-Index para evitar sobreposição de modais, alertas e toasts */
    .swal2-container {
        z-index: 100000 !important;
    }
    .toast-container, #toast-container {
        z-index: 100001 !important;
    }
    /* Suporte a modais empilhados */
    body.modal-open {
        overflow: hidden !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toast global (canto superior direito)
    const SwalToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Helper global de Toasts
    window.showToast = function (message, type = 'success') {
        const validIcon = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
        SwalToast.fire({ icon: validIcon, title: message });
    };

    // Helper global de Confirmações
    window.confirmAction = function (message, callback, title = 'Tem a certeza?') {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sim, confirmar!',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-2xl shadow-2xl font-sans' }
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    };

    window.confirmSwal = window.confirmAction;

    // Interceptador de flash messages Laravel no DOMReady
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('success'))
            showToast(@json(session('success')), 'success');
        @endif
        @if (session('status'))
            showToast(@json(session('status')), 'success');
        @endif
        @if (session('warning'))
            showToast(@json(session('warning')), 'warning');
        @endif
        @if (session('info'))
            showToast(@json(session('info')), 'info');
        @endif
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Atenção',
                text: @json(session('error')),
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
                confirmButtonText: 'Compreendido',
                customClass: { popup: 'rounded-2xl shadow-2xl font-sans' }
            });
        @endif
        @if ($errors->any() && !$errors->hasBag('default_bag_error_handled'))
            Swal.fire({
                icon: 'error',
                title: 'Verifique os dados informados',
                html: '<ul style="text-align: left; font-size: 13px; color: #334155; padding-left: 10px;">@foreach ($errors->all() as $err)<li style="margin-bottom: 4px;">• {{ addslashes($err) }}</li>@endforeach</ul>',
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
                confirmButtonText: 'Corrigir',
                customClass: { popup: 'rounded-2xl shadow-2xl font-sans' }
            });
        @endif
    });

    // Gestão dinâmica de Z-Index para múltiplos modais Bootstrap empilhados
    document.addEventListener('show.bs.modal', function (event) {
        const modal = event.target;
        const openModals = document.querySelectorAll('.modal.show');
        const count = openModals.length;
        if (count > 0) {
            const zIndex = 1055 + (count * 20);
            modal.style.zIndex = zIndex;
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 0) {
                    const lastBackdrop = backdrops[backdrops.length - 1];
                    lastBackdrop.style.zIndex = zIndex - 5;
                }
            }, 10);
        }
    });

    document.addEventListener('hidden.bs.modal', function () {
        const remainingModals = document.querySelectorAll('.modal.show');
        if (remainingModals.length > 0) {
            document.body.classList.add('modal-open');
        }
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

    // Confirmação para botões e links com data-confirm
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-confirm]');
        if (!btn) return;
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

    // Substitui window.alert por um Swal modal limpo
    window.alert = function (message) {
        Swal.fire({
            icon: 'info',
            title: message,
            confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#10b981',
            customClass: { popup: 'rounded-2xl shadow-2xl font-sans' }
        });
    };
</script>