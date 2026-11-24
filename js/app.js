// EDIT YOUR PRODUCTS HERE
let PRODUCTS = [
  { id: 1, name: 'Static Saint Tee', category: 'Tops', price: 1290, tag: 'SB', image: 'images/static-bloom.jpg', description: 'Relaxed graphic tee with a heavyweight cotton feel.' },
  { id: 2, name: 'Signal Hoodie', category: 'Tops', price: 2490, tag: 'SH', image: 'images/signal-hoodie.jpg', description: 'Oversized fleece hoodie made for daily rotation.' },
  { id: 3, name: 'Night Shift Shell', category: 'Outerwear', price: 3290, tag: 'NS', image: 'images/night-shell.jpg', description: 'Lightweight shell layer with reflective detailing.' },
  { id: 4, name: 'Saint Transit Cap', category: 'Accessories', price: 890, tag: 'TC', image: 'images/transit-cap.jpg', description: 'Structured cap with embroidered VaporSaint mark.' },
  { id: 5, name: 'Saint Bomber', category: 'Outerwear', price: 3990, tag: 'AB', image: 'images/afterglow-bomber.jpg', description: 'Cropped bomber with a clean streetwear silhouette.' },
  { id: 6, name: 'Saint Cargo Pant', category: 'Bottoms', price: 2190, tag: 'VC', image: 'images/void-cargo.jpg', description: 'Utility cargo pants with adjustable ankle fit.' }
];

// Change to true only when you want products loaded from api/products.php.
const USE_DATABASE_PRODUCTS = true;

const peso = n => '₱' + n.toLocaleString('en-PH');
const getCart = () => JSON.parse(localStorage.getItem('vs-cart') || '[]');
const saveCart = c => {
  localStorage.setItem('vs-cart', JSON.stringify(c));
  updateCount()
};
const updateCount = () => document.querySelectorAll('.cart-count').forEach(e => e.textContent = getCart().reduce((a, x) => a + x.qty, 0));

function card(p) {
  const originalPrice = Math.round(p.price * 1.4);
  return `<article class="product-card">
    <a href="product-details.html?id=${p.id}">
      <div class="product-image">
        <img src="${p.image}" alt="${p.name}">
        <span class="discount-badge">-29%</span>
      </div>
    </a>
    <div class="product-info">
      <b class="product-name">${p.name}</b>
      <p class="product-desc">${p.description}</p>
      <div class="product-price-row">
        <span class="price-old">${peso(originalPrice)}</span>
        <span class="price-new">${peso(p.price)}</span>
      </div>
    </div>
    <div class="card-footer">
      <button class="btn card-btn" onclick="addToCart(${p.id})">Add to cart</button>
    </div>
  </article>`;
} function renderProducts(id, items) {
  const e = document.getElementById(id);
  if (e) e.innerHTML = items.map(card).join('');
  updateCount()
}
function initCatalog() {
  renderProducts('catalog', PRODUCTS);
  document.querySelectorAll('.filters button').forEach(b => b.onclick = () => {
    document.querySelectorAll('.filters button').forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    renderProducts('catalog', b.dataset.filter === 'All' ? PRODUCTS : PRODUCTS.filter(p => p.category === b.dataset.filter))
  })
}
function addToCart(id, qty = 1, showMessage = true) {
  const c = getCart(), x = c.find(y => y.id === id);
  x ? x.qty += qty : c.push({ id, qty });
  saveCart(c);
  if (showMessage) alert('Product added to cart')
}
function renderDetail() {
  const id = Number(new URLSearchParams(location.search).get('id')) || 1, p = PRODUCTS.find(x => x.id === id) || PRODUCTS[0];
  document.getElementById('product-detail').innerHTML = `<div class="detail-grid"><div class="product-image detail-img"><img src="${p.image}" alt="${p.name}"></div><div class="detail-copy"><p class="eyebrow">${p.category}</p><h1>${p.name}</h1><h2 style="color:var(--lime)">${peso(p.price)}</h2><p>${p.description}</p><label>Quantity<input id="qty" class="quantity" type="number" min="1" value="1"></label><br><button class="btn" onclick="addToCart(${p.id},Number(document.getElementById('qty').value)||1)">Add to cart</button></div></div>`;
  updateCount()
}
function renderCart() {
  const wrap = document.getElementById('cart-layout'), c = getCart();
  if (!wrap) return;
  if (!c.length) {
    wrap.innerHTML = `<div class="cart-empty">
      <div class="empty-icon">🛒</div>
      <h2>Your cart is empty.</h2>
      <p>Add some pieces to get started.</p>
      <a class="btn" href="products.html">Shop Now</a>
    </div>`;
    return;
  }
  let sub = 0;
  const rows = c.map(x => {
    const p = PRODUCTS.find(y => y.id === x.id);
    if (!p) return '';
    sub += p.price * x.qty;
    return `<div class="cart-row">
      <div class="cart-thumb"><img src="${p.image}" alt="${p.name}"></div>
      <div class="cart-info">
        <span class="item-name">${p.name}</span>
        <span class="item-cat">${p.category}</span>
        <span class="item-price">${peso(p.price)} each</span>
      </div>
      <div class="cart-actions">
        <div class="qty-ctrl">
          <button onclick="changeQty(${p.id},-1)" aria-label="Decrease">−</button>
          <span>${x.qty}</span>
          <button onclick="changeQty(${p.id},1)" aria-label="Increase">+</button>
        </div>
        <span class="item-line-total">${peso(p.price * x.qty)}</span>
        <button class="remove-btn" onclick="removeItem(${p.id})">Remove</button>
      </div>
    </div>`;
  }).join('');
  const delivery = 150;
  wrap.innerHTML = `
    <div class="cart-items-col">${rows}</div>
    <div class="cart-summary">
      <h2>Order Summary</h2>
      <div class="sum-row"><span>Subtotal</span><b>${peso(sub)}</b></div>
      <div class="sum-row"><span>Delivery</span><b>₱${delivery}</b></div>
      <div class="sum-row total"><span>Total</span><b>${peso(sub + delivery)}</b></div>
      <a class="checkout-btn" href="checkout.html">Proceed to Checkout →</a>
      <a class="continue-link" href="products.html">← Continue shopping</a>
    </div>`;
  updateCount();
}
function changeQty(id, n) {
  const c = getCart(), x = c.find(y => y.id === id);
  x.qty = Math.max(1, x.qty + n);
  saveCart(c);
  renderCart()
} function removeItem(id) {
  saveCart(getCart().filter(x => x.id !== id));
  renderCart()
}
function renderCheckout() {
  const c = getCart();
  const delivery = c.length ? 150 : 0;
  let sub = 0;

  // Render summary sidebar items
  const itemsEl = document.getElementById('summary-items');
  if (itemsEl) {
    itemsEl.innerHTML = c.map(x => {
      const p = PRODUCTS.find(y => y.id === x.id);
      if (!p) return '';
      sub += p.price * x.qty;
      return `<div class="summary-line">
        <div class="summary-thumb"><img src="${p.image}" alt="${p.name}"></div>
        <div class="summary-line-info">
          <div class="summary-line-name">${p.name}</div>
          <div class="summary-line-qty">Qty: ${x.qty}</div>
        </div>
        <span class="summary-line-price">${peso(p.price * x.qty)}</span>
      </div>`;
    }).join('');
  }

  // Set totals
  const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  setEl('sum-subtotal', peso(sub));
  setEl('sum-delivery', c.length ? '₱150' : '₱0');
  setEl('sum-total', peso(sub + delivery));

  // Form submit
  const form = document.getElementById('checkout-form');
  if (!form) return;
  form.onsubmit = async ev => {
    ev.preventDefault();
    const msg = document.getElementById('form-msg');
    const btn = form.querySelector('.place-order-btn');
    if (!c.length) {
      msg.className = 'form-msg error';
      msg.textContent = 'Your cart is empty. Add a product before checking out.';
      return;
    }
    if (!form.checkValidity()) {
      msg.className = 'form-msg error';
      msg.textContent = 'Please complete all fields and select a payment method.';
      form.reportValidity();
      return;
    }
    const data = new FormData(form);
    const items = c.map(x => {
      const p = PRODUCTS.find(y => y.id === x.id);
      return { id: p.id, name: p.name, price: p.price, qty: x.qty };
    });
    btn.disabled = true;
    btn.textContent = 'Placing Order…';
    try {
      const response = await fetch('api/place-order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name: data.get('name'), email: data.get('email'),
          address: data.get('address'), phone: data.get('phone'),
          payment: data.get('payment'), items
        })
      });
      const result = await response.json();
      if (!response.ok) throw new Error(result.error || 'Order failed');
      localStorage.setItem('vs-last-order', JSON.stringify({
        orderId: result.order_id, items: c,
        subtotal: sub, total: result.total,
        payment: data.get('payment'), name: data.get('name')
      }));
      localStorage.removeItem('vs-cart');
      location.href = 'confirmation.html';
    } catch (err) {
      msg.className = 'form-msg error';
      msg.textContent = err.message;
      btn.disabled = false;
      btn.textContent = 'Place Order →';
    }
  };
  updateCount();
}
// ── OUTFIT BUILDER ──────────────────────────────────────────
function initBuilder() {

  // Each slot: which <select> id, which preview box id, and which category to filter
  const SLOTS = [
    { selectId: 'builder-top',       previewId: 'preview-top',       category: 'Tops'        },
    { selectId: 'builder-outer',     previewId: 'preview-outer',     category: 'Outerwear'   },
    { selectId: 'builder-bottoms',   previewId: 'preview-bottoms',   category: 'Bottoms'     },
    { selectId: 'builder-accessory', previewId: 'preview-accessory', category: 'Accessories' },
  ];

  // Fill each dropdown with only the products for its category
  SLOTS.forEach(({ selectId, category }) => {
    const el = document.getElementById(selectId);
    if (!el) return;
    const opts = PRODUCTS
      .filter(p => p.category === category)
      .map(p => `<option value="${p.id}">${p.name} — ${peso(p.price)}</option>`)
      .join('');
    el.innerHTML = `<option value="">— Select —</option>${opts}`;
  });

  // Restore saved outfit from localStorage so it survives a page refresh
  const saved = JSON.parse(localStorage.getItem('vs-builder') || '{}');
  if (saved.top)       { const el = document.getElementById('builder-top');       if (el) el.value = saved.top; }
  if (saved.outer)     { const el = document.getElementById('builder-outer');     if (el) el.value = saved.outer; }
  if (saved.bottoms)   { const el = document.getElementById('builder-bottoms');   if (el) el.value = saved.bottoms; }
  if (saved.accessory) { const el = document.getElementById('builder-accessory'); if (el) el.value = saved.accessory; }
  if (saved.size) {
    const sizeEl = document.getElementById('builder-size');
    if (sizeEl) sizeEl.value = saved.size;
  }

  // ── updateBuilder: recalculates everything when any selection changes ──
  function updateBuilder() {
    // Collect selected product IDs (ignore empty slots)
    const ids = SLOTS
      .map(s => Number(document.getElementById(s.selectId)?.value || 0))
      .filter(Boolean);

    // Get current size from the size dropdown
    const sizeEl = document.getElementById('builder-size');
    const selectedSize = sizeEl ? sizeEl.value : 'M';

    // Calculate raw subtotal
    const rawTotal = ids.reduce((sum, id) => {
      return sum + (PRODUCTS.find(p => p.id === id)?.price || 0);
    }, 0);

    // Discount: 5%=2 items, 10%=3 items, 35%=all 4 items
    const discountPct = ids.length === 4 ? 0.35 : ids.length === 3 ? 0.10 : ids.length === 2 ? 0.05 : 0;
    const discountAmt = Math.round(rawTotal * discountPct);
    const finalTotal  = rawTotal - discountAmt;

    // — Update price rows —
    const sub    = document.getElementById('b-subtotal');
    const disc   = document.getElementById('b-discount');
    const discR  = document.getElementById('b-discount-row');
    const discL  = document.getElementById('b-discount-label');
    const tot    = document.getElementById('b-total');
    const res    = document.getElementById('builder-result');
    const prevT  = document.getElementById('preview-total');

    if (ids.length === 0) {
      if (sub)   sub.textContent  = '—';
      if (tot)   tot.textContent  = '—';
      if (discR) discR.style.display = 'none';
      if (res)   res.textContent  = 'Choose pieces to preview your fit.';
      if (prevT) prevT.textContent = '—';
    } else {
      if (sub)   sub.textContent  = peso(rawTotal);
      if (tot)   tot.textContent  = peso(finalTotal);
      if (discR) discR.style.display = discountPct > 0 ? 'flex' : 'none';
      if (disc)  disc.textContent = `− ${peso(discountAmt)}`;
      if (discL) discL.textContent =
        ids.length === 4 ? 'Full Outfit Discount (−35%)' :
        ids.length === 3 ? 'Outfit Discount (−10%)' :
                           'Multi-piece Discount (−5%)';
      if (res)   res.textContent  = `${ids.length} piece(s) selected · Size: ${selectedSize}`;
      if (prevT) prevT.textContent = peso(finalTotal);
    }

    // — Update each preview box with the selected product image, name, and price —
    SLOTS.forEach(({ selectId, previewId }) => {
      const box  = document.getElementById(previewId);
      if (!box) return;
      const pid  = Number(document.getElementById(selectId)?.value || 0);
      const prod = PRODUCTS.find(p => p.id === pid);

      if (prod) {
        // Show product photo + name + price below
        box.innerHTML = `
          <img src="${prod.image}" alt="${prod.name}">
          <p class="pi-name">${prod.name}</p>
          <p class="pi-price">${peso(prod.price)}</p>`;
      } else {
        // Show placeholder when nothing is selected
        const label = previewId.replace('preview-', '').toUpperCase();
        box.innerHTML = `<div class="preview-placeholder"><span>${label}</span></div>`;
      }
    });

    // Save selections to localStorage so they persist on refresh
    localStorage.setItem('vs-builder', JSON.stringify({
      top:       document.getElementById('builder-top')?.value       || '',
      outer:     document.getElementById('builder-outer')?.value     || '',
      bottoms:   document.getElementById('builder-bottoms')?.value   || '',
      accessory: document.getElementById('builder-accessory')?.value || '',
      size:      selectedSize,
    }));
  }

  // Listen for changes on all three product selects and the size select
  SLOTS.forEach(({ selectId }) => {
    const el = document.getElementById(selectId);
    if (el) el.onchange = updateBuilder;
  });
  const sizeEl = document.getElementById('builder-size');
  if (sizeEl) sizeEl.onchange = updateBuilder;

  // ── Add Outfit to Cart ──
  const addBtn = document.getElementById('add-outfit');
  if (addBtn) addBtn.onclick = () => {
    const ids = SLOTS
      .map(s => Number(document.getElementById(s.selectId)?.value || 0))
      .filter(Boolean);
    const msg  = document.getElementById('builder-success');
    const size = document.getElementById('builder-size')?.value || 'M';

    if (!ids.length) {
      if (msg) {
        msg.style.display = 'block';
        msg.className = 'builder-success error';
        msg.textContent = 'Choose at least one item to add to your cart.';
      }
      return;
    }

    // Recalculate final price with discount
    const rawTotal   = ids.reduce((s, id) => s + (PRODUCTS.find(p => p.id === id)?.price || 0), 0);
    const discPct    = ids.length === 4 ? 0.35 : ids.length === 3 ? 0.10 : ids.length === 2 ? 0.05 : 0;
    const finalTotal = rawTotal - Math.round(rawTotal * discPct);
    const names      = ids.map(id => PRODUCTS.find(p => p.id === id)?.name).filter(Boolean);

    // Add each selected product using the existing addToCart() — no second cart system
    ids.forEach(id => addToCart(id, 1, false));

    if (msg) {
      msg.style.display = 'block';
      msg.className = 'builder-success ok';
      msg.textContent =
        `✓ Your outfit was added to the cart. ` +
        `${names.join(', ')} · Size ${size} · ${peso(finalTotal)}`;
    }
  };

  // ── Clear Outfit ──
  const clearBtn = document.getElementById('clear-outfit');
  if (clearBtn) clearBtn.onclick = () => {
    // Reset all dropdowns
    SLOTS.forEach(({ selectId }) => {
      const el = document.getElementById(selectId);
      if (el) el.value = '';
    });
    // Remove saved outfit from storage
    localStorage.removeItem('vs-builder');
    // Hide success message
    const msg = document.getElementById('builder-success');
    if (msg) msg.style.display = 'none';
    // Refresh preview and prices
    updateBuilder();
  };

  // ── Random Fit: randomly pick one product from each category ──
  const randBtn = document.getElementById('random-outfit');
  if (randBtn) randBtn.onclick = () => {
    SLOTS.forEach(({ selectId, category }) => {
      const pool = PRODUCTS.filter(p => p.category === category);
      const el   = document.getElementById(selectId);
      if (el && pool.length) {
        el.value = pool[Math.floor(Math.random() * pool.length)].id;
      }
    });
    updateBuilder();
  };

  // Run once on init to draw the saved state (or placeholders if nothing saved)
  updateBuilder();
}
function renderConfirmation() {
  const order = JSON.parse(localStorage.getItem('vs-last-order') || 'null');
  if (!order) return;

  // Order number
  const numEl = document.getElementById('confirm-order-num');
  if (numEl && order.orderId) numEl.textContent = 'Order #VS-' + order.orderId;

  // Items list
  const itemsEl = document.getElementById('confirm-items');
  if (itemsEl && order.items) {
    itemsEl.innerHTML = order.items.map(x => {
      const p = PRODUCTS.find(y => y.id === x.id);
      if (!p) return '';
      return `<div class="confirm-line">
        <div class="confirm-thumb"><img src="${p.image}" alt="${p.name}"></div>
        <div class="confirm-line-info">
          <div class="confirm-line-name">${p.name}</div>
          <div class="confirm-line-qty">Qty: ${x.qty}</div>
        </div>
        <span class="confirm-line-price">${peso(p.price * x.qty)}</span>
      </div>`;
    }).join('');
  }

  // Totals
  const sub = order.subtotal || (order.total ? order.total - 150 : 0);
  const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
  setEl('confirm-subtotal', peso(sub));
  setEl('confirm-delivery', '₱150');
  setEl('confirm-total', peso(order.total || sub + 150));
}
document.querySelectorAll('.menu-btn').forEach(b => b.onclick = () => b.nextElementSibling.classList.toggle('open'));
function initAuthForms() {
  const login = document.getElementById('login-form');
  const register = document.getElementById('register-form');
  if (login) login.onsubmit = e => { e.preventDefault(); document.getElementById('login-message').textContent = 'Demo login successful. Opening your account...'; setTimeout(() => location.href = 'account.html', 500) };
  if (register) register.onsubmit = e => { e.preventDefault(); document.getElementById('register-message').textContent = 'Account created successfully. You can now continue shopping.' };
}
updateCount();

// Load the same product records used by the admin inventory page.
async function loadProductsFromDatabase() {
  try {
    const response = await fetch('api/products.php', { headers: { Accept: 'application/json' } });
    if (!response.ok) throw new Error('Products API unavailable');
    const databaseProducts = await response.json();
    if (!Array.isArray(databaseProducts) || !databaseProducts.length) return;
    PRODUCTS = databaseProducts.map(product => ({
      id: Number(product.id),
      name: product.name,
      category: product.category,
      price: Number(product.price),
      tag: (product.name || 'VS').split(' ').map(word => word[0]).join('').slice(0, 2).toUpperCase(),
      image: product.image || 'images/static-bloom.jpg',
      description: product.description || 'VaporSaint streetwear essential.'
    }));
    if (document.getElementById('catalog')) renderProducts('catalog', PRODUCTS);
    if (document.getElementById('featured')) renderProducts('featured', PRODUCTS.slice(0, 3));
    if (document.getElementById('builder-top')) initBuilder();
    if (document.getElementById('product-detail')) renderDetail();
  } catch (error) {
    console.warn('Using fallback products:', error.message);
  }
}

if (USE_DATABASE_PRODUCTS) loadProductsFromDatabase();

