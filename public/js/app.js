/* PharmacyPOS — modals, AJAX CRUD forms, toasts */
(function () {
    'use strict';

    const csrf = () => (document.querySelector('meta[name=csrf-token]') || {}).content;

    window.toast = function (msg, type) {
        const t = document.createElement('div');
        t.className = 'toast';
        if (type === 'error') { t.style.background = '#e11d48'; }
        if (type === 'success') { t.style.background = '#16a34a'; }
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 3000);
    };

    window.openModal = function (id) {
        const m = document.getElementById(id);
        if (m) m.classList.add('open');
    };
    window.closeModal = function (id) {
        const m = document.getElementById(id);
        if (m) m.classList.remove('open');
    };

    function clearErrors(form) {
        form.querySelectorAll('.err').forEach(e => e.remove());
        form.querySelectorAll('.has-error').forEach(e => e.classList.remove('has-error'));
    }

    function showErrors(form, errors) {
        clearErrors(form);
        Object.keys(errors).forEach(field => {
            const input = form.querySelector('[name="' + field + '"]');
            if (input) {
                input.classList.add('has-error');
                const div = document.createElement('div');
                div.className = 'err';
                div.textContent = errors[field][0];
                (input.closest('.field') || input.parentNode).appendChild(div);
            } else {
                window.toast(errors[field][0], 'error');
            }
        });
    }

    // Open modal triggers
    document.addEventListener('click', function (e) {
        const opener = e.target.closest('[data-modal-open]');
        if (opener) {
            e.preventDefault();
            const id = opener.getAttribute('data-modal-open');
            const modal = document.getElementById(id);
            if (modal) {
                const form = modal.querySelector('form');
                if (form && opener.hasAttribute('data-create')) {
                    form.reset();
                    clearErrors(form);
                    const methodField = form.querySelector('input[name="_method"]');
                    if (methodField) methodField.remove();
                    if (opener.dataset.action) form.setAttribute('action', opener.dataset.action);
                }
                // Edit: hydrate from JSON record
                if (form && opener.dataset.record) {
                    clearErrors(form);
                    const rec = JSON.parse(opener.dataset.record);
                    if (opener.dataset.action) form.setAttribute('action', opener.dataset.action);
                    let methodField = form.querySelector('input[name="_method"]');
                    if (!methodField) {
                        methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        form.appendChild(methodField);
                    }
                    methodField.value = 'PUT';
                    Object.keys(rec).forEach(k => {
                        const input = form.querySelector('[name="' + k + '"]');
                        if (!input) return;
                        if (input.type === 'checkbox') input.checked = !!rec[k];
                        else input.value = rec[k] === null ? '' : rec[k];
                    });
                }
                openModal(id);
            }
        }
        // Close triggers
        const closer = e.target.closest('[data-modal-close]');
        if (closer) {
            e.preventDefault();
            closer.closest('.modal').classList.remove('open');
        }
        // Backdrop click
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('open');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') document.querySelectorAll('.modal.open').forEach(m => m.classList.remove('open'));
    });

    // AJAX form submit
    document.addEventListener('submit', async function (e) {
        const form = e.target;
        if (!form.classList.contains('ajax-form')) return;
        e.preventDefault();
        const btn = form.querySelector('[type="submit"]');
        const label = btn ? btn.textContent : '';
        if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }

        try {
            const res = await fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            });
            if (res.status === 422) {
                const data = await res.json();
                showErrors(form, data.errors || {});
                if (btn) { btn.disabled = false; btn.textContent = label; }
                return;
            }
            if (!res.ok) throw new Error('Request failed');
            const data = await res.json().catch(() => ({}));
            sessionStorage.setItem('flash', data.message || 'Saved successfully.');
            window.location.reload();
        } catch (err) {
            window.toast('Something went wrong. Please try again.', 'error');
            if (btn) { btn.disabled = false; btn.textContent = label; }
        }
    });

    // Show flash after reload
    const flash = sessionStorage.getItem('flash');
    if (flash) { sessionStorage.removeItem('flash'); window.toast(flash, 'success'); }

    // Debounced auto-search
    document.querySelectorAll('form[data-autosearch] input[name="search"], form[data-autosearch] select').forEach(function (el) {
        let timer;
        const form = el.closest('form');
        const evt = el.tagName === 'SELECT' ? 'change' : 'input';
        el.addEventListener(evt, function () {
            clearTimeout(timer);
            timer = setTimeout(() => form.submit(), el.tagName === 'SELECT' ? 0 : 350);
        });
    });
})();
