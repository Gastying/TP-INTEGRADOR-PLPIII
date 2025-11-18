<?php
// AÑADIR: Necesitas la clase Database ANTES de usar new Database()
require_once 'includes/database.php'; 

$pageTitle = "Catálogo de Perfumes";
include 'includes/header.php'; // header.php ya inicia la sesión.

$database = new Database(); // ¡Ahora funciona!
$db = $database->getConnection();

// Parámetros de filtro
$categoria = $_GET['categoria'] ?? 'todas';
$orden = $_GET['orden'] ?? 'destacados';
$busqueda = $_GET['busqueda'] ?? '';

// Construir consulta
$query = "SELECT * FROM productos WHERE 1=1";
$params = [];

if ($categoria !== 'todas') {
    $query .= " AND categoria = ?";
    $params[] = $categoria;
}

if (!empty($busqueda)) {
    $query .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

// Ordenamiento
switch ($orden) {
    case 'precio-asc':
        $query .= " ORDER BY precio ASC";
        break;
    case 'precio-desc':
        $query .= " ORDER BY precio DESC";
        break;
    case 'nuevos':
        $query .= " ORDER BY created_at DESC";
        break;
    default:
        $query .= " ORDER BY destacado DESC, created_at DESC";
        break;
}

$stmt = $db->prepare($query);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container">
    <section class="catalogo-header">
        <div class="section-header text-center">
            <span class="section-subtitle">Nuestra Colección</span>
            <h2 class="section-title">Catálogo de <span class="highlight">Perfumes</span></h2>
            <p class="form-subtitle">Descubre nuestra exclusiva selección de fragancias premium</p>
        </div>

        <div class="catalogo-toolbar">
            <form method="GET" class="catalogo-filters">
                <div class="filter-group">
                    <select name="categoria" class="filter-select" onchange="this.form.submit()">
                        <option value="todas" <?php echo $categoria === 'todas' ? 'selected' : ''; ?>>Todas las Categorías</option>
                        <option value="femenino" <?php echo $categoria === 'femenino' ? 'selected' : ''; ?>>Femenino</option>
                        <option value="masculino" <?php echo $categoria === 'masculino' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="unisex" <?php echo $categoria === 'unisex' ? 'selected' : ''; ?>>Unisex</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="orden" class="filter-select" onchange="this.form.submit()">
                        <option value="destacados" <?php echo $orden === 'destacados' ? 'selected' : ''; ?>>Ordenar por: Destacados</option>
                        <option value="precio-asc" <?php echo $orden === 'precio-asc' ? 'selected' : ''; ?>>Precio: Menor a Mayor</option>
                        <option value="precio-desc" <?php echo $orden === 'precio-desc' ? 'selected' : ''; ?>>Precio: Mayor a Menor</option>
                        <option value="nuevos" <?php echo $orden === 'nuevos' ? 'selected' : ''; ?>>Más Recientes</option>
                    </select>
                </div>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                        name="busqueda" 
                        class="search-input" 
                        placeholder="Buscar perfumes..." 
                        value="<?php echo htmlspecialchars($busqueda); ?>"
                        id="search-input">
                </div>
            
            <div class="view-options">
                <span class="results-count"><?php echo count($productos); ?> productos encontrados</span>
            </div>
        </div>
    </section>

    <section class="products-grid-modern">
        <?php if (empty($productos)): ?>
            <div class="no-products">
                <i class="fas fa-search"></i>
                <h3>No se encontraron productos</h3>
                <p>Intenta con otros filtros o términos de búsqueda</p>
            </div>
        <?php else: ?>
            <?php foreach ($productos as $producto): ?>
                <article class="product-card-modern" data-genero="<?php echo $producto['categoria']; ?>">
            <?php if ($producto['destacado']): ?>
            <div class="product-badge-modern badge-popular">Destacado</div>
            <?php elseif ($producto['stock'] < 5): ?>
            <div class="product-badge-modern badge-new">Últimas Unidades</div>
            <?php endif; ?>
        
        <div class="product-image-modern">
            <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>" loading="lazy">
            <div class="product-actions-modern">
                <form method="POST" action="carrito.php" class="add-to-cart-form" onsubmit="addToCart(event, this)">
                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                    <input type="hidden" name="cantidad" value="1">
                    <input type="hidden" name="agregar_carrito" value="1">
                    <button type="submit" name="agregar_carrito" class="action-btn" title="Agregar al carrito" 
                            <?php echo $producto['stock'] <= 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="product-content-modern">
            <div class="product-category"><?php echo ucfirst($producto['categoria']); ?></div>
            <h3 class="product-title-modern"><?php echo $producto['nombre']; ?></h3>
            <p class="product-description-modern"><?php echo $producto['descripcion']; ?></p>
            
            <div class="product-price-modern">
                <div>
                    <span class="price-main">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></span>
                    <?php if ($producto['stock'] <= 0): ?>
                        <span class="stock-badge out-of-stock">Agotado</span>
                    <?php elseif ($producto['stock'] < 10): ?>
                        <span class="stock-badge low-stock">Pocas unidades</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-footer-modern">
                <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn-quick-view">
                    Ver detalles <i class="far fa-eye"></i>
                </a>
                <!-- FORMULARIO CORREGIDO -->
                <form method="POST" action="carrito.php" class="add-to-cart-form" onsubmit="addToCart(event, this)">
                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                    <input type="hidden" name="cantidad" value="1">
                    <input type="hidden" name="agregar_carrito" value="1">
                    <button type="submit" name="agregar_carrito" class="btn-add-cart" 
                            <?php echo $producto['stock'] <= 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-cart-plus"></i>
                    </button>
                </form>
            </div>
        </div>
    </article>
<?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- Paginación (para cuando tengas muchos productos) -->
    <!-- <div class="pagination">
        <button class="page-btn active">1</button>
        <button class="page-btn">2</button>
        <button class="page-btn">3</button>
        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
    </div> -->
</main>

<style>
.no-products {
    text-align: center;
    padding: 4rem 2rem;
    grid-column: 1 / -1;
    color: var(--gray);
}

.no-products i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--light-gray);
}

.stock-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    margin-left: 0.5rem;
}

.out-of-stock {
    background-color: #fef2f2;
    color: #dc2626;
}

.low-stock {
    background-color: #fffbeb;
    color: #d97706;
}

.add-to-cart-form {
    display: inline;
}

.results-count {
    color: var(--gray);
    font-size: 0.9rem;
}
</style>

<script>
// FUNCIÓN MEJORADA - MANEJA RESPUESTAS JSON Y ACTUALIZA CONTADOR
function addToCart(event, formElement) {
    event.preventDefault();
    console.log('🛒 Intentando agregar al carrito...');
    
    const formData = new FormData(formElement);
    const submitButton = formElement.querySelector('button[type="submit"]');
    const originalHTML = submitButton.innerHTML;
    
    // Loading state
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    submitButton.disabled = true;
    
    fetch('carrito.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Respuesta JSON:', data);
        
        if (data.success) {
            Toastify({
                text: "✅ " + data.message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981"
            }).showToast();
            
            // ACTUALIZAR CONTADOR DEL CARRITO - ESTO ES LO IMPORTANTE
            updateCartCount();
            
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Toastify({
            text: "❌ " + error.message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ef4444"
        }).showToast();
    })
    .finally(() => {
        setTimeout(() => {
            submitButton.innerHTML = originalHTML;
            submitButton.disabled = false;
        }, 1000);
    });
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

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    console.log('Catálogo inicializado');
});
</script>
<?php include 'includes/footer.php'; ?>
