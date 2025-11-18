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

// Hero Slider Mejorado
function initHeroSlider() {
    const heroImg = document.getElementById('hero-img');
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');
    
    if (!heroImg) return;
    
    const banners = [
        'assets/banners/banner1.jpg',
        'assets/banners/banner2.jpg', 
        'assets/banners/banner3.jpg'
    ];
    
    let currentBanner = 0;
    
    function changeBanner(index) {
        currentBanner = index;
        heroImg.style.opacity = '0.7';
        setTimeout(() => {
            heroImg.src = banners[currentBanner];
            heroImg.style.opacity = '1';
        }, 300);
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            changeBanner((currentBanner + 1) % banners.length);
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            changeBanner((currentBanner - 1 + banners.length) % banners.length);
        });
    }
    
    // Cambio automático cada 5 segundos
    setInterval(function() {
        changeBanner((currentBanner + 1) % banners.length);
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Página cargada correctamente');
    
    // Inicializar hero slider
    initHeroSlider();
    
    // Animaciones al scroll
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
});