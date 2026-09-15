<script>
(function () {
    const routes = {
        index:  "{{ route('cart.index') }}",
        clear:  "{{ route('cart.clear') }}",
        add:    "{{ route('cart.add', ['medicine' => '__ID__']) }}",
        update: "{{ route('cart.update', ['medicine' => '__ID__']) }}",
        remove: "{{ route('cart.remove', ['medicine' => '__ID__']) }}",
    };

    /** Fill the medicine id into one of the templated cart routes. */
    function url(template, id, quantity) {
        const path = template.replace('__ID__', id);
        return quantity === undefined ? path : path + '?quantity=' + quantity;
    }
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const lang = {
        empty: @json(__('site.cart_empty')),
        remove: @json(__('site.remove')),
        priceOnConfirmation: @json(__('site.price_on_confirmation')),
    };

    const el = {
        float:      document.getElementById('cartFloat'),
        count:      document.getElementById('cartCount'),
        lines:      document.getElementById('cartLinesBadge'),
        items:      document.getElementById('cartItems'),
        empty:      document.getElementById('cartEmpty'),
        footer:     document.getElementById('cartFooter'),
        total:      document.getElementById('cartTotal'),
        priceNote:  document.getElementById('cartPriceNote'),
        panel:      document.getElementById('cartPanel'),
        toast:      document.getElementById('cartToast'),
        toastText:  document.getElementById('cartToastText'),
    };

    let toastTimer = null;

    function toast(message, type) {
        if (!message) return;
        el.toastText.textContent = message;
        el.toast.classList.toggle('danger', type === 'danger');
        el.toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => el.toast.classList.remove('show'), 2600);
    }

    function money(value) {
        return Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /** POST/GET the cart endpoints and repaint the panel from the response. */
    function call(url, method) {
        return fetch(url, {
            method: method || 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        })
        .then(res => res.json().then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            render(data);
            toast(data.message, ok ? (data.type || 'success') : 'danger');
            return data;
        })
        .catch(() => toast('⚠', 'danger'));
    }

    function render(data) {
        if (!data || typeof data.count === 'undefined') return;

        el.count.textContent = data.count;
        el.count.classList.toggle('d-none', data.count === 0);
        el.lines.textContent = data.items.length;

        el.empty.classList.toggle('d-none', data.count > 0);
        el.footer.classList.toggle('d-none', data.count === 0);
        el.total.textContent = money(data.total);
        el.priceNote.classList.toggle('d-none', data.prices_visible !== false || data.count === 0);

        el.items.innerHTML = data.items.map(item => `
            <div class="cart-line" data-id="${item.id}">
                <img src="${item.image}" alt="">
                <div class="flex-grow-1 min-w-0">
                    <a href="${item.url}" class="cart-line-title text-truncate">${escapeHtml(item.name)}</a>
                    ${item.show_price ? `<div class="small text-muted mb-1">${money(item.price)}</div>` : ''}
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="qty-box">
                            <button type="button" data-qty="${item.quantity - 1}" title="-"><i class="fas fa-minus"></i></button>
                            <span>${item.quantity}</span>
                            <button type="button" data-qty="${item.quantity + 1}" title="+"><i class="fas fa-plus"></i></button>
                        </div>
                        ${item.show_price ? `<span class="fw-bold text-ph small">${money(item.total)}</span>` : ''}
                        <button type="button" class="btn btn-sm text-danger p-1" data-remove title="${lang.remove}">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        el.float.classList.remove('bump');
        void el.float.offsetWidth;
        el.float.classList.add('bump');
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    // Add to cart from a medicine card or the details page
    document.addEventListener('click', function (e) {
        const add = e.target.closest('[data-cart-add]');
        if (!add) return;

        e.preventDefault();
        const id = add.dataset.cartAdd;
        const qtyInput = add.dataset.qtyFrom ? document.getElementById(add.dataset.qtyFrom) : null;
        const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value) || 1) : 1;

        add.disabled = true;
        call(url(routes.add, id, qty)).finally(() => { add.disabled = false; });
    });

    // Quantity +/- and remove inside the panel
    el.items.addEventListener('click', function (e) {
        const line = e.target.closest('.cart-line');
        if (!line) return;
        const id = line.dataset.id;

        const qtyBtn = e.target.closest('[data-qty]');
        if (qtyBtn) {
            return call(url(routes.update, id, qtyBtn.dataset.qty));
        }

        if (e.target.closest('[data-remove]')) {
            return call(url(routes.remove, id));
        }
    });

    document.getElementById('cartClear').addEventListener('click', () => call(routes.clear));

    // Load the lines the first time the panel opens, then keep them fresh
    el.panel.addEventListener('show.bs.offcanvas', () => call(routes.index, 'GET'));

    @if (cart()->count())
        call(routes.index, 'GET');
    @endif
})();
</script>

@if (session('cart_msg'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const t = document.getElementById('cartToast');
            document.getElementById('cartToastText').textContent = @json(session('cart_msg'));
            t.classList.toggle('danger', @json(session('cart_type')) === 'danger');
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        });
    </script>
@endif
