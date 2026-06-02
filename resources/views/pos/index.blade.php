@extends('layouts.app')
@section('title', 'Point of Sale')

@section('content')
@php
    $catalog = $products->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'price' => (float) $p->price,
        'stock' => $p->quantity,
        'category' => $p->category->name ?? 'Uncategorized',
        'barcode' => $p->barcode,
    ]);
@endphp

<div class="pos" style="margin:-26px">
    <section class="catalog">
        <div class="pos-search">
            <input type="text" id="search" placeholder="Search products by name or scan barcode…" autofocus>
        </div>
        <div class="cat-tabs" id="cattabs">
            <button class="active" data-cat="all">All</button>
            @foreach($categories as $c)
                <button data-cat="{{ $c->name }}">{{ $c->name }}</button>
            @endforeach
        </div>
        <div class="prod-grid" id="prodgrid"></div>
    </section>

    <aside class="cart">
        <div class="cart-head flex-between">
            <span><x-icon name="cart" size="18" /> Current Order</span>
            <button class="btn ghost sm" id="clearcart">Clear</button>
        </div>
        <div class="cart-items" id="cartitems">
            <div class="empty-cart">No items yet. Tap a product to add it.</div>
        </div>
        <div class="cart-foot">
            <div class="sumrow"><span>Subtotal</span><span id="subtotal">KSh 0.00</span></div>
            <div class="pay-grid">
                <div>
                    <label class="muted" style="font-size:.78rem">Discount (KSh)</label>
                    <input type="number" id="discount" min="0" step="0.01" value="0">
                </div>
                <div>
                    <label class="muted" style="font-size:.78rem">Tax (KSh)</label>
                    <input type="number" id="tax" min="0" step="0.01" value="0">
                </div>
            </div>
            <div class="sumrow total"><span>Total</span><span id="total">KSh 0.00</span></div>
            <div class="pay-grid">
                <div>
                    <label class="muted" style="font-size:.78rem">Customer (optional)</label>
                    <input type="text" id="customer" placeholder="Walk-in">
                </div>
                <div>
                    <label class="muted" style="font-size:.78rem">Method</label>
                    <select id="method"><option value="cash">Cash</option><option value="card">Card</option><option value="mpesa">M-Pesa</option></select>
                </div>
            </div>
            <div class="pay-grid" id="mpesarow" style="display:none">
                <div style="grid-column:1 / -1">
                    <label class="muted" style="font-size:.78rem">M-Pesa Phone (07.. or 2547..)</label>
                    <input type="tel" id="mpesaphone" placeholder="0712345678">
                    @if($mpesaSimulated)<small class="muted" style="font-size:.72rem">Simulation mode — no live charge. Add Daraja keys in .env for real STK push.</small>@endif
                </div>
            </div>
            <div class="pay-grid">
                <div>
                    <label class="muted" style="font-size:.78rem">Amount Paid (KSh)</label>
                    <input type="number" id="paid" min="0" step="0.01" value="0">
                </div>
                <div>
                    <label class="muted" style="font-size:.78rem">Change</label>
                    <input type="text" id="change" value="KSh 0.00" readonly>
                </div>
            </div>
            <button class="btn primary block" id="checkout" style="margin-top:10px">Complete Sale</button>
        </div>
    </aside>
</div>

@push('scripts')
<script>
const CATALOG = @json($catalog);
const CHECKOUT_URL = "{{ route('pos.checkout') }}";
const MPESA_URL = "{{ route('pos.mpesa') }}";
const MPESA_STATUS_URL = "{{ url('pos/mpesa') }}";
const CSRF = document.querySelector('meta[name=csrf-token]').content;
const cart = [];
let activeCat = 'all';

const fmt = n => 'KSh ' + Number(n).toFixed(2);

function renderProducts() {
    const term = document.getElementById('search').value.toLowerCase().trim();
    const grid = document.getElementById('prodgrid');
    const list = CATALOG.filter(p =>
        (activeCat === 'all' || p.category === activeCat) &&
        (!term || p.name.toLowerCase().includes(term) || (p.barcode && p.barcode.toLowerCase().includes(term)))
    );
    if (!list.length) { grid.innerHTML = '<p class="muted">No matching products.</p>'; return; }
    grid.innerHTML = list.map(p => `
        <div class="prod-card" data-id="${p.id}">
            <div class="pname">${p.name}</div>
            <div class="pprice">${fmt(p.price)}</div>
            <div class="pstock">${p.stock} in stock</div>
        </div>`).join('');
    grid.querySelectorAll('.prod-card').forEach(el =>
        el.addEventListener('click', () => addToCart(parseInt(el.dataset.id)))
    );
}

function addToCart(id) {
    const p = CATALOG.find(x => x.id === id);
    if (!p) return;
    const line = cart.find(l => l.id === id);
    const inCart = line ? line.quantity : 0;
    if (inCart + 1 > p.stock) { toast('Not enough stock for ' + p.name); return; }
    if (line) line.quantity++;
    else cart.push({ id: p.id, name: p.name, price: p.price, quantity: 1, stock: p.stock });
    renderCart();
}

function changeQty(id, delta) {
    const line = cart.find(l => l.id === id);
    if (!line) return;
    const next = line.quantity + delta;
    if (next <= 0) { cart.splice(cart.indexOf(line), 1); }
    else if (next > line.stock) { toast('Reached available stock'); return; }
    else line.quantity = next;
    renderCart();
}

function removeLine(id) {
    const i = cart.findIndex(l => l.id === id);
    if (i > -1) cart.splice(i, 1);
    renderCart();
}

function renderCart() {
    const box = document.getElementById('cartitems');
    if (!cart.length) {
        box.innerHTML = '<div class="empty-cart">No items yet. Tap a product to add it.</div>';
    } else {
        box.innerHTML = cart.map(l => `
            <div class="cart-line">
                <div class="cl-name">${l.name}<small>${fmt(l.price)} each</small></div>
                <div class="qty">
                    <button data-act="dec" data-id="${l.id}">−</button>
                    <span>${l.quantity}</span>
                    <button data-act="inc" data-id="${l.id}">+</button>
                </div>
                <div style="width:64px;text-align:right;font-weight:600">${fmt(l.price * l.quantity)}</div>
                <button class="cl-rm" data-act="rm" data-id="${l.id}">✕</button>
            </div>`).join('');
        box.querySelectorAll('button[data-act]').forEach(b => b.addEventListener('click', () => {
            const id = parseInt(b.dataset.id);
            if (b.dataset.act === 'inc') changeQty(id, 1);
            if (b.dataset.act === 'dec') changeQty(id, -1);
            if (b.dataset.act === 'rm') removeLine(id);
        }));
    }
    recalc();
}

function recalc() {
    const subtotal = cart.reduce((s, l) => s + l.price * l.quantity, 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const total = Math.max(0, subtotal - discount + tax);
    const paid = parseFloat(document.getElementById('paid').value) || 0;
    document.getElementById('subtotal').textContent = fmt(subtotal);
    document.getElementById('total').textContent = fmt(total);
    document.getElementById('change').value = fmt(Math.max(0, paid - total));
}

function toast(msg) {
    const t = document.createElement('div');
    t.className = 'toast'; t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2600);
}

function cartTotal() {
    const subtotal = cart.reduce((s, l) => s + l.price * l.quantity, 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    return Math.max(0, subtotal - discount + tax);
}

const sleep = ms => new Promise(r => setTimeout(r, ms));

async function runMpesa(total, btn) {
    const phone = document.getElementById('mpesaphone').value.trim();
    if (!phone) { toast('Enter the customer M-Pesa phone number'); return null; }
    btn.textContent = 'Sending STK push…';
    const res = await fetch(MPESA_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ phone, amount: total })
    });
    const data = await res.json();
    if (!res.ok || !data.ok) { toast(data.message || 'M-Pesa request failed'); return null; }
    toast(data.message);
    btn.textContent = 'Awaiting payment…';
    // Poll for confirmation (up to ~30s)
    for (let i = 0; i < 12; i++) {
        await sleep(2500);
        const st = await fetch(MPESA_STATUS_URL + '/' + encodeURIComponent(data.checkoutid), { headers: { 'Accept': 'application/json' } });
        const sd = await st.json();
        if (sd.paid) { return sd.receipt || data.checkoutid; }
    }
    toast('Payment not confirmed in time. Please retry.');
    return null;
}

async function checkout() {
    if (!cart.length) { toast('Cart is empty'); return; }
    const btn = document.getElementById('checkout');
    const method = document.getElementById('method').value;
    const total = cartTotal();
    btn.disabled = true;

    let mpesareceipt = null;
    let paid = parseFloat(document.getElementById('paid').value) || 0;

    if (method === 'mpesa') {
        mpesareceipt = await runMpesa(total, btn);
        if (!mpesareceipt) { btn.disabled = false; btn.textContent = 'Complete Sale'; return; }
        paid = total;
    } else if (method === 'card') {
        paid = total;
    }

    btn.textContent = 'Processing…';
    try {
        const res = await fetch(CHECKOUT_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                items: cart.map(l => ({ id: l.id, quantity: l.quantity })),
                customer: document.getElementById('customer').value,
                discount: parseFloat(document.getElementById('discount').value) || 0,
                tax: parseFloat(document.getElementById('tax').value) || 0,
                paid: paid,
                method: method,
                mpesareceipt: mpesareceipt,
            })
        });
        const data = await res.json();
        if (!res.ok) {
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Sale failed');
            toast(msg); btn.disabled = false; btn.textContent = 'Complete Sale'; return;
        }
        window.open(data.receipt, '_blank');
        window.location.reload();
    } catch (e) {
        toast('Network error, please retry.');
        btn.disabled = false; btn.textContent = 'Complete Sale';
    }
}

document.getElementById('method').addEventListener('change', function () {
    const isMpesa = this.value === 'mpesa';
    document.getElementById('mpesarow').style.display = isMpesa ? 'grid' : 'none';
    const paid = document.getElementById('paid');
    if (this.value === 'card' || isMpesa) { paid.value = cartTotal().toFixed(2); paid.readOnly = true; }
    else { paid.readOnly = false; }
    recalc();
});

document.getElementById('search').addEventListener('input', renderProducts);
document.getElementById('cattabs').addEventListener('click', e => {
    if (e.target.tagName !== 'BUTTON') return;
    document.querySelectorAll('#cattabs button').forEach(b => b.classList.remove('active'));
    e.target.classList.add('active');
    activeCat = e.target.dataset.cat;
    renderProducts();
});
['discount', 'tax', 'paid'].forEach(id => document.getElementById(id).addEventListener('input', recalc));
document.getElementById('clearcart').addEventListener('click', () => { cart.length = 0; renderCart(); });
document.getElementById('checkout').addEventListener('click', checkout);

renderProducts();
renderCart();
</script>
@endpush
@endsection
