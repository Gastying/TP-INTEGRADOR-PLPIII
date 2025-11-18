<?php
// 1. INICIAR SESIÓN: Necesario para usar $_SESSION (carrito, usuario)
session_start();

// 2. INCLUIR LA CLASE DATABASE: Necesario para que new Database() funcione
require_once 'includes/database.php';

$pageTitle = "Perfumes de Lujo";
include 'includes/header.php'; 

$database = new Database();
$db = $database->getConnection();

// Obtener productos destacados
$query = "SELECT * FROM productos WHERE destacado = 1 LIMIT 3";
$stmt = $db->prepare($query);
$stmt->execute();
$productos_destacados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hero Section COMPLETA Y FUNCIONAL -->
<section class="hero hero-slider">
    <div class="hero-background">
        <!-- Banner principal -->
        <img src="assets/banners/banner1.jpg" alt="Essence Shop - Perfumes de Lujo" class="hero-bg" id="hero-img">
        
        <!-- Filtro oscuro -->
        <div class="hero-bg-filter"></div>
        
        <!-- Flechas de navegación -->
        <button class="hero-arrow hero-arrow-left" id="hero-prev" aria-label="Banner anterior">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="hero-arrow hero-arrow-right" id="hero-next" aria-label="Banner siguiente">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <!-- Contenido del hero -->
        <div class="hero-content">
            <a href="#featured" class="btn-hero-scroll">
                Ver productos destacados 
                <i class="fas fa-arrow-down"></i>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Dinámicos -->
<section id="featured" class="featured">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Selección Exclusiva</span>
            <h2 class="section-title">Nuestros <span class="highlight">Destacados</span></h2>
        </div>

        <div class="products-grid">
            <?php foreach($productos_destacados as $producto): ?>
            <article class="product-card">
                <div class="product-badge"><?php echo $producto['stock'] > 10 ? 'Más Vendido' : 'Últimas Unidades'; ?></div>
                <div class="product-image">
                    <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>" loading="lazy">
                    <div class="product-overlay">
                        <button class="quick-view" data-product="<?php echo $producto['id']; ?>">
                            <i class="fas fa-eye"></i> Vista Rápida
                        </button>
                    </div>
                </div>
                <div class="product-content">
                    <h3 class="product-title"><?php echo $producto['nombre']; ?></h3>
                    <p class="product-description"><?php echo $producto['descripcion']; ?></p>
                    <div class="product-price">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></div>
                    <div class="product-actions">
                        <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn-details">
                            Ver Detalle
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
// Hero Slider - Versión Súper Robusta
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando Hero Slider...');
    
    // Elementos con verificación de existencia
    const heroImg = document.getElementById('hero-img');
    const prevBtn = document.getElementById('hero-prev');
    const nextBtn = document.getElementById('hero-next');
    
    // Verificar que los elementos existan
    if (!heroImg || !prevBtn || !nextBtn) {
        console.error('❌ Elementos del hero no encontrados. Creando elementos...');
        createHeroElements();
        return;
    }
    
    console.log('✅ Todos los elementos del hero encontrados');
    
    // Array de banners
    const banners = [
        'assets/banners/banner1.jpg',
        'assets/banners/banner2.jpg', 
        'assets/banners/banner3.jpg'
    ];
    
    let currentBanner = 0;
    
    // Función para cambiar banner
    function changeBanner(index) {
        currentBanner = index;
        console.log(`🖼️ Cambiando a banner ${currentBanner + 1}: ${banners[currentBanner]}`);
        
        heroImg.src = banners[currentBanner];
        heroImg.alt = `Essence Shop - Banner ${currentBanner + 1}`;
        
        // Efecto de transición
        heroImg.style.opacity = '0.7';
        setTimeout(() => {
            heroImg.style.opacity = '1';
        }, 300);
    }
    
    // Navegación
    nextBtn.addEventListener('click', function() {
        changeBanner((currentBanner + 1) % banners.length);
    });
    
    prevBtn.addEventListener('click', function() {
        changeBanner((currentBanner - 1 + banners.length) % banners.length);
    });
    
    // Cambio automático
    let slideInterval = setInterval(function() {
        changeBanner((currentBanner + 1) % banners.length);
    }, 5000);
    
    // Pausar en hover
    const heroSection = document.querySelector('.hero');
    heroSection.addEventListener('mouseenter', () => {
        clearInterval(slideInterval);
    });
    
    heroSection.addEventListener('mouseleave', () => {
        slideInterval = setInterval(function() {
            changeBanner((currentBanner + 1) % banners.length);
        }, 5000);
    });
    
    // Verificar banners
    function verifyBanners() {
        banners.forEach((banner, index) => {
            const img = new Image();
            img.onload = function() {
                console.log(`✅ Banner ${index + 1} disponible: ${banner}`);
            };
            img.onerror = function() {
                console.warn(`⚠️ Banner no encontrado: ${banner}`);
                // Si el primer banner no existe, crear uno de respaldo
                if (index === 0) {
                    createFallbackBanner();
                }
            };
            img.src = banner;
        });
    }
    
    // Banner de respaldo
    function createFallbackBanner() {
        console.log('🎨 Creando banner de respaldo...');
        const heroBg = document.querySelector('.hero-background');
        
        // Ocultar imagen original
        if (heroImg) heroImg.style.display = 'none';
        
        // Crear banner con CSS
        const fallbackDiv = document.createElement('div');
        fallbackDiv.className = 'hero-fallback';
        fallbackDiv.innerHTML = `
            <div class="hero-fallback-content">
                <h2>ESSENCE SHOP</h2>
                <p>Perfumes de Lujo Exclusivos</p>
                <div style="margin-top: 2rem; font-size: 4rem;">🎁</div>
            </div>
        `;
        
        heroBg.appendChild(fallbackDiv);
    }
    
    // Crear elementos si no existen
    function createHeroElements() {
        const heroSection = document.querySelector('.hero');
        if (!heroSection) {
            console.error('❌ No se encontró la sección .hero');
            return;
        }
        
        heroSection.innerHTML = `
            <div class="hero-background">
                <img src="assets/banners/banner1.jpg" alt="Essence Shop" class="hero-bg" id="hero-img">
                <div class="hero-bg-filter"></div>
                <button class="hero-arrow hero-arrow-left" id="hero-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="hero-arrow hero-arrow-right" id="hero-next">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="hero-content">
                    <a href="#featured" class="btn-hero-scroll">
                        Ver productos destacados 
                        <i class="fas fa-arrow-down"></i>
                    </a>
                </div>
            </div>
        `;
        
        // Reinicializar después de crear elementos
        setTimeout(() => {
            location.reload();
        }, 100);
    }
    
    // Inicializar
    verifyBanners();
    changeBanner(0);
    
    console.log('🎯 Hero Slider inicializado correctamente');
});

// Debug adicional
console.log('🔍 Estado inicial del hero:');
console.log('- hero-img:', document.getElementById('hero-img'));
console.log('- hero-prev:', document.getElementById('hero-prev')); 
console.log('- hero-next:', document.getElementById('hero-next'));
</script>

<?php include 'includes/footer.php'; ?>
