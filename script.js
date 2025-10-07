/* =========================
   Utilidades globales
========================= */
const money = new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' });

function $(sel, ctx = document) { return ctx.querySelector(sel); }
function $all(sel, ctx = document) { return Array.from(ctx.querySelectorAll(sel)); }

/* =========================
   Toasts accesibles
========================= */
(function ensureToastRegion(){
  if (!$('#toast-region')) {
    const div = document.createElement('div');
    div.id = 'toast-region';
    div.setAttribute('aria-live', 'polite');
    div.setAttribute('aria-atomic', 'true');
    div.style.position = 'fixed';
    div.style.right = '1rem';
    div.style.bottom = '1rem';
    div.style.zIndex = '9999';
    document.body.appendChild(div);
  }
})();

function mostrarMensaje(texto, tipo = 'info') {
  const toast = document.createElement('div');
  toast.className = `toast ${tipo}`;
  toast.textContent = texto;
  toast.style.padding = '12px 14px';
  toast.style.borderRadius = '10px';
  toast.style.marginTop = '8px';
  toast.style.background = tipo === 'success' ? '#16a34a' : tipo === 'error' ? '#dc2626' : '#334155';
  toast.style.color = 'white';
  toast.style.boxShadow = '0 6px 20px rgba(0,0,0,.25)';
  $('#toast-region').appendChild(toast);
  setTimeout(() => toast.remove(), 2800);
}

/* =========================
   Carrito (localStorage)
========================= */
function getCart() {
  try { return JSON.parse(localStorage.getItem('cart')) || []; }
  catch { return []; }
}
function setCart(c) { localStorage.setItem('cart', JSON.stringify(c)); }

function agregarAlCarrito(id, qty = 1) {
  id = Number(id);
  qty = Number(qty) || 1;
  const cart = getCart();
  const i = cart.findIndex(p => p.id === id);
  if (i >= 0) cart[i].qty += qty; else cart.push({ id, qty });
  setCart(cart);
  mostrarMensaje('Producto agregado al carrito', 'success');
}

function comprarProducto(id, qty = 1) {
  agregarAlCarrito(id, qty);
  window.location.href = 'comprar.html';
}

/* =========================
   Detalle de producto
========================= */
function cambiarImagen(imgEl) {
  const principal = $('#imagenPrincipal');
  if (principal && imgEl && imgEl.src) {
    principal.src = imgEl.src;
    $all('.miniatura').forEach(m => m.classList.remove('activa'));
    imgEl.classList.add('activa');
  }
}
// Accesibilidad con teclado para miniaturas
document.addEventListener('keydown', (e) => {
  const t = e.target;
  if (t && t.classList && t.classList.contains('miniatura') && (e.key === 'Enter' || e.key === ' ')) {
    e.preventDefault();
    cambiarImagen(t);
  }
});

/* =========================
   Checkout: estado por pasos
========================= */
const STEP_KEY = 'checkout_state';
function getState() {
  try { return JSON.parse(sessionStorage.getItem(STEP_KEY)) || {}; }
  catch { return {}; }
}
function setState(patch) {
  const now = getState();
  sessionStorage.setItem(STEP_KEY, JSON.stringify({ ...now, ...patch }));
}

/* Rellenar resúmenes si existen zonas de resumen */
function hydrateCheckout() {
  const s = getState();
  if ($('#resumenProducto') && s.productoNombre) $('#resumenProducto').textContent = s.productoNombre;
  if ($('#resumenCantidad') && s.cantidad) $('#resumenCantidad').textContent = s.cantidad;
  if ($('#resumenMetodoPago') && s.metodoPago) $('#resumenMetodoPago').textContent = s.metodoPago;
  if ($('#resumenTotal') && s.total) $('#resumenTotal').textContent = money.format(Number(s.total));
}
document.addEventListener('DOMContentLoaded', hydrateCheckout);

/* =========================
   Validación simple de formularios
========================= */
function validarCampo(ev) {
  const el = ev.target || ev;
  const v = String(el.value || '').trim();
  let ok = true;

  if (el.hasAttribute('required') && !v) ok = false;

  if (ok && el.type === 'email') {
    ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  }
  if (ok && el.name === 'numero_tarjeta') {
    ok = /^[0-9\s-]{12,19}$/.test(v);
  }
  if (ok && el.name === 'cvv') {
    ok = /^[0-9]{3,4}$/.test(v);
  }

  el.classList.toggle('input-error', !ok);
  const msg = el.nextElementSibling && el.nextElementSibling.classList.contains('error-msg')
    ? el.nextElementSibling
    : null;
  if (msg) msg.style.display = ok ? 'none' : 'block';
  return ok;
}

function limpiarError(ev) {
  const el = ev.target;
  el.classList.remove('input-error');
  const msg = el.nextElementSibling && el.nextElementSibling.classList.contains('error-msg')
    ? el.nextElementSibling
    : null;
  if (msg) msg.style.display = 'none';
}

function validarFormulario(form) {
  const f = form || $('form');
  if (!f) return true;
  let ok = true;
  $all('[required]', f).forEach(c => { if (!validarCampo(c)) ok = false; });
  return ok;
}

document.addEventListener('DOMContentLoaded', () => {
  // Delegar validación
  $all('form').forEach(form => {
    form.addEventListener('submit', (e) => {
      if (!validarFormulario(form)) {
        e.preventDefault();
        mostrarMensaje('Revisá los campos obligatorios.', 'error');
      }
    });
    $all('input,select,textarea', form).forEach(c => {
      c.addEventListener('blur', validarCampo);
      c.addEventListener('input', limpiarError);
    });
  });

  // Botones data-add-to-cart
  $all('[data-add-to-cart]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const qty = btn.getAttribute('data-qty') || 1;
      agregarAlCarrito(id, qty);
    });
  });

  // Botones data-buy-now
  $all('[data-buy-now]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const qty = btn.getAttribute('data-qty') || 1;
      comprarProducto(id, qty);
    });
  });
});
