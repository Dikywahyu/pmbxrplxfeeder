<script>
    (() => {
        if (!window.Swal) return;

        const fireModal = Swal.fire.bind(Swal);
        const showToast = (icon, title, timer = 3200) => {
            const safeTitle = document.createElement('span');
            safeTitle.textContent = String(title ?? '');
            return fireModal({
                toast: true,
                position: 'top-end',
                icon,
                title: safeTitle,
                showConfirmButton: false,
                timer,
                timerProgressBar: true,
            });
        };

        window.showAppToast = (title, icon = 'success') => showToast(icon, title, icon === 'error' || icon === 'warning' ? 5000 : 3200);

        Swal.fire = function (...args) {
            let options = {};
            if (args[0] && typeof args[0] === 'object') {
                options = { ...args[0] };
            } else if (args.length) {
                options = { title: args[0], text: args[1], icon: args[2] };
            }

            const icon = options.icon;
            const hasInteraction = options.showCancelButton || options.showDenyButton || options.input
                || options.preConfirm || options.preDeny || options.showLoaderOnConfirm || options.didOpen || options.html;
            if (options.toast || hasInteraction || !['success', 'error', 'warning', 'info'].includes(icon)) {
                return fireModal(...args);
            }

            const title = options.text || options.title || 'Pemberitahuan';
            const timer = icon === 'error' || icon === 'warning' ? 5000 : (options.timer || 3200);
            return showToast(icon, title, timer);
        };

        const toastBootstrapAlert = (element) => {
            if (element.dataset.toastHandled) return;
            const icon = element.classList.contains('alert-danger') ? 'error'
                : (element.classList.contains('alert-warning') ? 'warning'
                    : (element.classList.contains('alert-info') ? 'info' : 'success'));
            element.dataset.toastHandled = 'true';
            const text = element.innerText.trim().replace(/\s+/g, ' ');
            element.remove();
            if (text) showToast(icon, text, icon === 'error' || icon === 'warning' ? 5000 : 3200);
        };

        document.querySelectorAll('.alert[data-app-toast]').forEach(toastBootstrapAlert);
        const alertObserver = new MutationObserver((changes) => {
            changes.forEach((change) => change.addedNodes.forEach((node) => {
                if (!(node instanceof Element)) return;
                if (node.matches('.alert[data-app-toast]')) toastBootstrapAlert(node);
                node.querySelectorAll?.('.alert[data-app-toast]').forEach(toastBootstrapAlert);
            }));
        });
        alertObserver.observe(document.body, { childList: true, subtree: true });
    })();
</script>
