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

  // Animaciones al scroll (solo visual)
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);
  document.querySelectorAll('.product-card, .feature-card, .stat-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'all 0.6s ease';
    observer.observe(el);
  });

  // Hero slider funcional con animación suave
  const heroImages = ['banner1.jpg', 'banner2.jpg', 'banner3.jpg'];
  let heroIndex = 0;
  const heroImg = document.getElementById('hero-img');
  const prevBtn = document.getElementById('hero-prev');
  const nextBtn = document.getElementById('hero-next');

  if (heroImg) heroImg.src = heroImages[heroIndex];

  function animateHeroChange(newIdx) {
    if (!heroImg) return;
    heroImg.classList.add('fade-hero-out');
    setTimeout(() => {
      heroImg.src = heroImages[newIdx];
      heroImg.classList.remove('fade-hero-out');
      heroImg.classList.add('fade-hero-in');
      setTimeout(() => {
        heroImg.classList.remove('fade-hero-in');
      }, 400);
    }, 400);
  }

  if (heroImg && prevBtn && nextBtn) {
    prevBtn.onclick = function() {
      heroIndex = (heroIndex - 1 + heroImages.length) % heroImages.length;
      animateHeroChange(heroIndex);
    };
    nextBtn.onclick = function() {
      heroIndex = (heroIndex + 1) % heroImages.length;
      animateHeroChange(heroIndex);
    };
  }

  // Vista rápida: redirige al producto correspondiente
  document.querySelectorAll('.quick-view[data-product]').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const id = btn.getAttribute('data-product');
      if (id) {
        window.location.href = `producto.html?id=${id}`;
      }
    });
  });

  // --- Buscador global (para todas las páginas con .search-input) ---
  const searchInput = document.querySelector('.search-input');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const query = this.value.trim().toLowerCase();
      const productCards = document.querySelectorAll('.product-card-modern, .product-card');
      const tableRows = document.querySelectorAll('.tabla-productos tbody tr');
      if (productCards.length) {
        productCards.forEach(card => {
          const title = card.querySelector('.product-title-modern, .product-title');
          const desc = card.querySelector('.product-description-modern, .product-description');
          const text = (title?.textContent + ' ' + (desc?.textContent || '')).toLowerCase();
          card.style.display = text.includes(query) ? '' : 'none';
        });
      }
      if (tableRows.length) {
        tableRows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(query) ? '' : 'none';
        });
      }
    });
  }

  // --- Filtros y orden en listado_box.html ---
  const productsGrid = document.querySelector('.products-grid-modern');
  if (productsGrid) {
    // Almacena la lista original de nodos de productos al cargar la página
    const originalCards = Array.from(productsGrid.querySelectorAll('.product-card-modern'));
    function getProductData(card) {
      const priceText = card.querySelector('.price-main')?.textContent || '';
      const precio = parseFloat(priceText.replace(/[^\d,\.]/g, '').replace('.', '').replace(',', '.')) || 0;
      return {
        el: card,
        categoria: (card.querySelector('.product-category')?.textContent || '').toLowerCase(),
        genero: card.getAttribute('data-genero') || '',
        precio,
        titulo: (card.querySelector('.product-title-modern')?.textContent || '').toLowerCase(),
        descripcion: (card.querySelector('.product-description-modern')?.textContent || '').toLowerCase()
      };
    }

    // Siempre filtra sobre la lista original, no sobre el DOM actual
    function getAllProductData() {
      return originalCards.map(getProductData);
    }

    const categoriaSelect = document.querySelector('.filter-select[data-filter="categoria"]');
    const ordenSelect = document.querySelector('.filter-select[data-filter="orden"]');
    const searchInputBox = document.querySelector('.search-input');

    // --- Lee la categoría de la URL si existe ---
    const params = new URLSearchParams(window.location.search);
    const categoriaUrl = params.get('categoria');
    if (categoriaUrl && categoriaSelect) {
      categoriaSelect.value = categoriaUrl;
    }

    function filtrarYOrdenar() {
      const productCards = getAllProductData();

      let categoria = categoriaSelect ? categoriaSelect.value : 'todas';
      let orden = ordenSelect ? ordenSelect.value : 'destacados';
      let query = searchInputBox ? searchInputBox.value.trim().toLowerCase() : '';

      let filtrados = productCards.filter(p => {
        let matchCategoria = categoria === 'todas' || p.genero === categoria;
        let matchBusqueda = !query || p.titulo.includes(query) || p.descripcion.includes(query);
        return matchCategoria && matchBusqueda;
      });

      if (orden === 'precio-asc') {
        filtrados.sort((a, b) => a.precio - b.precio);
      } else if (orden === 'precio-desc') {
        filtrados.sort((a, b) => b.precio - a.precio);
      }

      productsGrid.innerHTML = '';
      filtrados.forEach(p => productsGrid.appendChild(p.el));
    }

    if (categoriaSelect) categoriaSelect.addEventListener('change', filtrarYOrdenar);
    if (ordenSelect) ordenSelect.addEventListener('change', filtrarYOrdenar);
    if (searchInputBox) searchInputBox.addEventListener('input', filtrarYOrdenar);

    // Ejecutar al cargar para aplicar el filtro inicial (incluye filtro por URL)
    filtrarYOrdenar();
  }

  // --- Newsletter con EmailJS ---
  document.addEventListener('DOMContentLoaded', function () {
    // --- Banner Slider ---
    const heroSlides = [
      { src: 'banner1.jpg', alt: 'Fragancias Premium' },
      { src: 'banner2.jpg', alt: 'Nuevas Fragancias' },
      { src: 'banner3.jpg', alt: 'Colección Exclusiva' }
    ];
    let heroIndex = 0;
    const heroImg = document.querySelector('.hero-background #hero-img');
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');

    function showHero(idx) {
      if (!heroImg) return;
      heroImg.src = heroSlides[idx].src;
      heroImg.alt = heroSlides[idx].alt;
    }

    if (prevBtn && nextBtn && heroImg) {
      prevBtn.addEventListener('click', function (e) {
        e.preventDefault();
        heroIndex = (heroIndex - 1 + heroSlides.length) % heroSlides.length;
        showHero(heroIndex);
      });
      nextBtn.addEventListener('click', function (e) {
        e.preventDefault();
        heroIndex = (heroIndex + 1) % heroSlides.length;
        showHero(heroIndex);
      });
    }

    showHero(heroIndex);

    // --- Newsletter con EmailJS ---
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
      newsletterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const emailInput = newsletterForm.querySelector('input[type="email"]');
        const email = emailInput.value.trim();

        // EmailJS config (asegúrate de poner tu template y public key correctos)
        const serviceID = 'service_g7hr00s';
        const templateID = 'tu_template_id'; // Reemplaza por tu template real
        const publicKey = 'tu_public_key';   // Reemplaza por tu public key real

        // El nombre de la variable debe coincidir con el nombre del campo en tu plantilla de EmailJS
        const templateParams = {
          user_email: email, // o el nombre exacto que pusiste en tu template
          message: '¡Gracias por suscribirte! Esto es una prueba de newsletter desde Essence Shop.'
        };

        const btn = newsletterForm.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

        function sendEmail() {
          window.emailjs.init(publicKey);
          window.emailjs.send(serviceID, templateID, templateParams)
            .then(function () {
              btn.innerHTML = '<i class="fas fa-check"></i> ¡Enviado!';
              btn.disabled = true;
              emailInput.value = '';
              setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-envelope"></i> Suscribirme';
                btn.disabled = false;
              }, 2500);
            }, function (err) {
              btn.innerHTML = '<i class="fas fa-times"></i> Error';
              btn.disabled = false;
              alert('No se pudo enviar el correo. Revisa tu configuración de EmailJS.\n' + (err.text || err));
              setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-envelope"></i> Suscribirme';
              }, 2500);
            });
        }

        // Cargar EmailJS si no está
        if (!window.emailjs) {
          const script = document.createElement('script');
          script.src = 'https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js';
          script.onload = sendEmail;
          document.body.appendChild(script);
        } else {
          sendEmail();
        }
      });
    }
  });
});