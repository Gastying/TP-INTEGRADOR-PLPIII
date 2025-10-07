// Utilidades globales
function $(sel, ctx = document) { return ctx.querySelector(sel); }
function $all(sel, ctx = document) { return Array.from(ctx.querySelectorAll(sel)); }

// Toasts accesibles
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

document.addEventListener('DOMContentLoaded', () => {
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

  // Hero slider funcional con animación suave y rutas corregidas
  // Usa rutas relativas SIN prefijo de carpeta (solo 'assets/banners/...')
  const heroImages = [
    'assets/banners/banner1.jpg',
    'assets/banners/banner2.jpg',
    'assets/banners/banner3.jpg'
  ];
  let heroIndex = 0;

  function setHeroImage(idx) {
    const heroImg = document.getElementById('hero-img');
    if (heroImg) {
      heroImg.src = heroImages[idx];
    }
  }

  function animateHeroChange(newIdx) {
    const heroImg = document.getElementById('hero-img');
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

  setHeroImage(heroIndex);

  const prevBtn = document.getElementById('hero-prev');
  const nextBtn = document.getElementById('hero-next');

  if (prevBtn && nextBtn) {
    prevBtn.onclick = function() {
      heroIndex = (heroIndex - 1 + heroImages.length) % heroImages.length;
      animateHeroChange(heroIndex);
    };
    nextBtn.onclick = function() {
      heroIndex = (heroIndex + 1) % heroImages.length;
      animateHeroChange(heroIndex);
    };
  }

  // Vista rápida: redirige al producto correspondiente (index.html)
  document.querySelectorAll('.quick-view[data-product]').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const id = btn.getAttribute('data-product');
      if (id) {
        window.location.href = `producto.html?id=${id}`;
      }
    });
  });

  // --- CORRIGE SOLO PARA LISTADO MODERNO ---
  // Elimina cualquier JS que manipule los enlaces .btn-quick-view
  // Deja que los <a href="producto.html?id=..."> funcionen normalmente

  // Buscador global
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

  // Filtros y orden en listado_box.html
  const productsGrid = document.querySelector('.products-grid-modern');
  if (productsGrid) {
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
    function getAllProductData() {
      return originalCards.map(getProductData);
    }
    const categoriaSelect = document.querySelector('.filter-select[data-filter="categoria"]');
    const ordenSelect = document.querySelector('.filter-select[data-filter="orden"]');
    const searchInputBox = document.querySelector('.search-input');
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
    filtrarYOrdenar();
  }

  // Preloader solo en la primera visita 
  (function() {
    const preloader = document.getElementById('preloader');
    const alreadyShown = sessionStorage.getItem('preloader_shown');
    if (!alreadyShown) {
      window.addEventListener('load', () => {
        setTimeout(() => {
          document.body.classList.add('preloader-hide');
          setTimeout(() => {
            if (preloader) preloader.style.display = 'none';
          }, 700);
        }, 700);
        sessionStorage.setItem('preloader_shown', '1');
      });
    } else {
      if (preloader) preloader.style.display = 'none';
      document.body.classList.add('preloader-hide');
    }
  })();
});