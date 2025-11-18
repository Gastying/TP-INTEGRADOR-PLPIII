<?php

require_once 'includes/database.php';
$pageTitle = "Detalle de Producto";
include 'includes/header.php';

// Verificar si se proporcionó un ID válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: catalogo.php');
    exit;
}

$producto_id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();

// Obtener producto
$query = "SELECT * FROM productos WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$producto_id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no existe el producto, redirigir
if (!$producto) {
    header('Location: catalogo.php');
    exit;
}

// Obtener productos relacionados (misma categoría)
$query_relacionados = "SELECT * FROM productos WHERE categoria = ? AND id != ? LIMIT 3";
$stmt_relacionados = $db->prepare($query_relacionados);
$stmt_relacionados->execute([$producto['categoria'], $producto_id]);
$productos_relacionados = $stmt_relacionados->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = $producto['nombre'] . " - Essence Shop";
?>

<main class="container producto-detalle">
    <!-- Migas de pan -->
    <nav class="breadcrumb">
        <a href="index.php">Inicio</a>
        <i class="fas fa-chevron-right"></i>
        <a href="catalogo.php">Catálogo</a>
        <i class="fas fa-chevron-right"></i>
        <span><?php echo $producto['nombre']; ?></span>
    </nav>

    <div class="producto-flex">
        <div class="producto-img">
            <img src="<?php echo $producto['imagen']; ?>" 
                 alt="<?php echo $producto['nombre']; ?>" 
                 class="producto-img-main"
                 id="producto-img">
            
            <?php if ($producto['stock'] <= 0): ?>
                <div class="producto-agotado-overlay">
                    <span>Agotado</span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="producto-info">
            <div class="producto-header">
                <h1 class="producto-titulo" id="producto-titulo"><?php echo $producto['nombre']; ?></h1>
                
                <?php if ($producto['destacado']): ?>
                    <span class="producto-badge destacado">Destacado</span>
                <?php endif; ?>
            </div>
            
            <div class="producto-precio" id="producto-precio">
                $<?php echo number_format($producto['precio'], 0, ',', '.'); ?>
            </div>
            
            <div class="producto-stock">
                <?php if ($producto['stock'] > 0): ?>
                    <i class="fas fa-check-circle text-success"></i>
                    <span class="stock-disponible">En stock (<?php echo $producto['stock']; ?> unidades)</span>
                <?php else: ?>
                    <i class="fas fa-times-circle text-danger"></i>
                    <span class="stock-agotado">Producto agotado</span>
                <?php endif; ?>
            </div>
            
                <form method="POST" action="carrito.php" class="add-to-cart-form" onsubmit="addToCart(event, this)">
                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                    <input type="hidden" name="agregar_carrito" value="1">
                    
                    <div class="cantidad-selector">
                        <label for="cantidad">Cantidad:</label>
                        <div class="cantidad-controls">
                            <button type="button" class="cantidad-btn" onclick="decrementCantidad()">-</button>
                            <input type="number" 
                                    name="cantidad" 
                                    id="cantidad" 
                                    value="1" 
                                    min="1" 
                                    max="<?php echo $producto['stock']; ?>"
                                    class="cantidad-input">
                            <button type="button" class="cantidad-btn" onclick="incrementCantidad()">+</button>
                        </div>
                    </div>

                    <button type="submit" 
                            name="agregar_carrito" 
                            class="btn btn-primary btn-add-cart"
                            <?php echo $producto['stock'] <= 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-shopping-cart"></i>
                        <?php echo $producto['stock'] > 0 ? 'Agregar al Carrito' : 'Producto Agotado'; ?>
                    </button>
                </form>
                                
                <div class="secondary-actions">
                    <button class="btn btn-secondary btn-share" onclick="shareProduct()">
                        <i class="fas fa-share-alt"></i> Compartir
                    </button>
                </div>
            </div>
            
            <div class="producto-meta">
                <div class="meta-item">
                    <strong>Categoría:</strong>
                    <span><?php echo ucfirst($producto['categoria']); ?></span>
                </div>
                <div class="meta-item">
                    <strong>SKU:</strong>
                    <span><?php echo strtoupper(substr($producto['nombre'], 0, 2)); ?>-<?php echo str_pad($producto['id'], 3, '0', STR_PAD_LEFT); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Descripción y detalles -->
    <div class="producto-tabs">
        <div class="tabs-header">
            <button class="tab-btn active" data-tab="descripcion">Descripción</button>
            <button class="tab-btn" data-tab="especificaciones">Especificaciones</button>
            <button class="tab-btn" data-tab="reviews">Reseñas</button>
        </div>
        
        <div class="tabs-content">
            <div class="tab-pane active" id="descripcion">
                <h3>Descripción del Producto</h3>
                <p><?php echo $producto['descripcion']; ?></p>
                
                <div class="producto-features">
                    <div class="feature">
                        <i class="fas fa-clock"></i>
                        <span>Duración prolongada</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-wind"></i>
                        <span>Proyección moderada</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-certificate"></i>
                        <span>Producto original</span>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="especificaciones">
                <h3>Especificaciones Técnicas</h3>
                <div class="specs-grid">
                    <div class="spec-item">
                        <strong>Concentración:</strong>
                        <span>Eau de Parfum</span>
                    </div>
                    <div class="spec-item">
                        <strong>Volumen:</strong>
                        <span>100ml</span>
                    </div>
                    <div class="spec-item">
                        <strong>Tipo:</strong>
                        <span><?php echo ucfirst($producto['categoria']); ?></span>
                    </div>
                    <div class="spec-item">
                        <strong>Origen:</strong>
                        <span>Emiratos Árabes Unidos</span>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="reviews">
                <h3>Reseñas de Clientes</h3>
                <div class="reviews-summary">
                    <div class="rating-overview">
                        <div class="avg-rating">4.8</div>
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= 4 ? 'star-filled' : 'star-half'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="reviews-count">Basado en 128 reseñas</div>
                    </div>
                </div>
                <p class="no-reviews">Este producto aún no tiene reseñas. ¡Sé el primero en opinar!</p>
            </div>
        </div>
    </div>
    
    <!-- Productos relacionados -->
    <?php if (!empty($productos_relacionados)): ?>
        <section class="productos-relacionados">
            <h2 class="section-title">Productos <span class="highlight">Relacionados</span></h2>
            <div class="products-grid related-grid">
                <?php foreach ($productos_relacionados as $relacionado): ?>
                    <article class="product-card">
                        <div class="product-image">
                            <img src="<?php echo $relacionado['imagen']; ?>" alt="<?php echo $relacionado['nombre']; ?>" loading="lazy">
                            <div class="product-overlay">
                                <a href="producto.php?id=<?php echo $relacionado['id']; ?>" class="quick-view">
                                    <i class="fas fa-eye"></i> Vista Rápida
                                </a>
                            </div>
                        </div>
                        <div class="product-content">
                            <h3 class="product-title"><?php echo $relacionado['nombre']; ?></h3>
                            <p class="product-description"><?php echo substr($relacionado['descripcion'], 0, 80); ?>...</p>
                            <div class="product-price">$<?php echo number_format($relacionado['precio'], 0, ',', '.'); ?></div>
                            <div class="product-actions">
                                <a href="producto.php?id=<?php echo $relacionado['id']; ?>" class="btn-details">
                                    Ver Detalle
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<style>
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 2rem;
    font-size: 0.9rem;
    color: var(--gray);
}

.breadcrumb a {
    color: var(--gray);
    text-decoration: none;
}

.breadcrumb a:hover {
    color: var(--primary);
}

.breadcrumb i {
    font-size: 0.7rem;
}

.producto-flex {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-bottom: 3rem;
}

.producto-img {
    position: relative;
}

.producto-img-main {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.producto-agotado-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.producto-agotado-overlay span {
    background: #dc2626;
    color: white;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1.2rem;
}

.producto-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.producto-badge {
    padding: 0.25rem 0.75rem;
    background: var(--primary);
    color: white;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.producto-precio {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 1rem;
}

.producto-stock {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 2rem;
}

.text-success { color: #10b981; }
.text-danger { color: #ef4444; }

.cantidad-selector {
    margin-bottom: 1.5rem;
}

.cantidad-selector label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.cantidad-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cantidad-btn {
    width: 40px;
    height: 40px;
    border: 1px solid var(--light-gray);
    background: white;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cantidad-input {
    width: 60px;
    height: 40px;
    border: 1px solid var(--light-gray);
    border-radius: 4px;
    text-align: center;
    font-weight: 500;
}

.btn-add-cart {
    width: 100%;
    padding: 1rem;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.btn-add-cart:disabled {
    background-color: var(--light-gray);
    cursor: not-allowed;
}

.secondary-actions {
    display: flex;
    gap: 1rem;
}

.producto-meta {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--light-gray);
}

.meta-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.producto-tabs {
    margin: 3rem 0;
}

.tabs-header {
    display: flex;
    border-bottom: 1px solid var(--light-gray);
    margin-bottom: 2rem;
}

.tab-btn {
    padding: 1rem 2rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-weight: 500;
    color: var(--gray);
}

.tab-btn.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

.producto-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.feature {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--light-bg);
    border-radius: 8px;
}

.feature i {
    color: var(--primary);
    font-size: 1.2rem;
}

.specs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.spec-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--light-gray);
}

.reviews-summary {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
    padding: 2rem;
    background: var(--light-bg);
    border-radius: 12px;
}

.avg-rating {
    font-size: 3rem;
    font-weight: 700;
    color: var(--primary);
}

.stars {
    display: flex;
    gap: 0.25rem;
}

.star-filled { color: #fbbf24; }
.star-half { color: #d1d5db; }

.no-reviews {
    text-align: center;
    padding: 2rem;
    color: var(--gray);
    font-style: italic;
}

.productos-relacionados {
    margin-top: 4rem;
}

.related-grid {
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

@media (max-width: 968px) {
    .producto-flex {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .producto-img-main {
        height: 400px;
    }
    
    .producto-actions .btn-primary {
    width: 100%;
    margin-bottom: 1rem;
}

.secondary-actions {
    display: flex;
    gap: 1rem;
}

.secondary-actions {
    flex-direction: column;
}

.secondary-actions .btn {
    width: 100%;
    justify-content: center;
}
    
    .tabs-header {
        flex-direction: column;
    }
    
    .reviews-summary {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
// FUNCIÓN MEJORADA - MANEJA RESPUESTAS JSON
// En producto.php - busca la función addToCart y asegúrate de que tenga:
function addToCart(event, formElement) {
    // ... código anterior igual ...
    
    .then(data => {
        if (data.success) {
            Toastify({
                text: "✅ " + data.message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981"
            }).showToast();
            
            // ESTA LÍNEA ES CLAVE:
            updateCartCount();
            
        } else {
            throw new Error(data.message);
        }
    })
    // ... resto del código igual ...
}

function updateCartCount() {
    fetch('includes/get_cart_count.php?t=' + new Date().getTime())
        .then(response => response.text())
        .then(count => {
            const cartCount = parseInt(count) || 0;
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = cartCount;
                el.style.display = cartCount > 0 ? 'inline' : 'none';
            });
            console.log('Contador actualizado:', cartCount);
        })
        .catch(error => console.error('Error actualizando contador:', error));
}

// Funciones específicas de producto.php
document.addEventListener('DOMContentLoaded', function() {
    // Tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    updateCartCount();
});

// Control de cantidad
function incrementCantidad() {
    const input = document.getElementById('cantidad');
    const max = parseInt(input.max);
    if (input.value < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decrementCantidad() {
    const input = document.getElementById('cantidad');
    if (input.value > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// Compartir producto
function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo $producto['nombre']; ?>',
            text: 'Mira este increíble perfume en Essence Shop',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            Toastify({
                text: "Enlace copiado al portapapeles",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981"
            }).showToast();
        });
    }
}
</script>
<?php include 'includes/footer.php'; ?>