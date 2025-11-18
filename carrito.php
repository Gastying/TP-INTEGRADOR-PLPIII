<?php
session_start();

// Función para detectar si es AJAX
function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Manejar agregar producto al carrito
if (isset($_POST['agregar_carrito']) && isset($_POST['producto_id'])) {
    $producto_id = (int)$_POST['producto_id'];
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
    
    // Validar cantidad
    if ($cantidad < 1) {
        $cantidad = 1;
    }
    
    // Conectar a la base de datos para verificar el producto
    try {
        require_once 'includes/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT id, nombre, precio, imagen, stock FROM productos WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$producto_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($producto && $producto['stock'] >= $cantidad) {
            
            // Agregar o actualizar en el carrito
            if (isset($_SESSION['carrito'][$producto_id])) {
                $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
            } else {
                $_SESSION['carrito'][$producto_id] = [
                    'id' => $producto['id'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'imagen' => $producto['imagen'],
                    'cantidad' => $cantidad
                ];
            }
            
            $response = [
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'cart_count' => getCartItemCount()
            ];
            
            // Respuesta para AJAX
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            } else {
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'catalogo.php'));
                exit;
            }
            
        } else {
            $response = [
                'success' => false,
                'message' => $producto ? 'Stock insuficiente' : 'Producto no encontrado'
            ];
            
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
        }
        
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Error de base de datos'
        ];
        
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }
}

// MANEJO DE ELIMINACIÓN DE PRODUCTOS - ESTO FALTABA
if (isset($_GET['eliminar'])) {
    $producto_id = (int)$_GET['eliminar'];
    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
        
        // Si es AJAX, responder con JSON
        if (isAjaxRequest()) {
            $response = [
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'cart_count' => getCartItemCount()
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            // Redirección normal
            header('Location: carrito.php');
            exit;
        }
    }
}

// MANEJO DE ACTUALIZACIÓN DE CANTIDAD - ESTO FALTABA
if (isset($_POST['actualizar_cantidad']) && isset($_POST['producto_id']) && isset($_POST['cantidad'])) {
    $producto_id = (int)$_POST['producto_id'];
    $cantidad = (int)$_POST['cantidad'];
    
    if (isset($_SESSION['carrito'][$producto_id]) && $cantidad > 0) {
        $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
        
        $response = [
            'success' => true,
            'message' => 'Cantidad actualizada',
            'cart_count' => getCartItemCount(),
            'subtotal' => $_SESSION['carrito'][$producto_id]['precio'] * $cantidad,
            'total' => getCartTotal()
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Si llegamos aquí y es AJAX, algo salió mal
if (isAjaxRequest()) {
    $response = [
        'success' => false,
        'message' => 'Solicitud inválida'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Función auxiliar para contar items del carrito
function getCartItemCount() {
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return 0;
    }
    
    $count = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $count += $item['cantidad'];
    }
    
    return $count;
}

// Función para calcular total del carrito
function getCartTotal() {
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }
    
    return $total;
}

// EL RESTO DEL CÓDIGO PARA MOSTRAR LA PÁGINA DEL CARRITO...
$pageTitle = "Carrito de Compras";
$total = getCartTotal();

include 'includes/header.php';
?>

<main class="container">
    <section class="carrito-header">
        <div class="section-header">
            <h2 class="section-title">Tu <span class="highlight">Carrito</span></h2>
            <p class="section-subtitle">Revisa y gestiona tus productos seleccionados</p>
        </div>
    </section>

    <?php if (empty($_SESSION['carrito'])): ?>
        <div class="carrito-vacio">
            <i class="fas fa-shopping-cart"></i>
            <h3>Tu carrito está vacío</h3>
            <p>Agrega algunos productos increíbles</p>
            <a href="catalogo.php" class="btn">Explorar Catálogo</a>
        </div>
    <?php else: ?>
        <div class="carrito-contenido">
            <div class="carrito-items">
                <?php foreach ($_SESSION['carrito'] as $item): ?>
                    <div class="carrito-item" data-product-id="<?php echo $item['id']; ?>">
                        <div class="carrito-item-img">
                            <img src="<?php echo $item['imagen']; ?>" alt="<?php echo $item['nombre']; ?>">
                        </div>
                        <div class="carrito-item-info">
                            <h4><?php echo $item['nombre']; ?></h4>
                            <div class="carrito-item-precio">$<?php echo number_format($item['precio'], 0, ',', '.'); ?></div>
                        </div>
                        <div class="carrito-item-cantidad">
                            <button class="cantidad-btn decrement" data-product-id="<?php echo $item['id']; ?>">-</button>
                            <input type="number" 
                                   class="cantidad-input" 
                                   value="<?php echo $item['cantidad']; ?>" 
                                   min="1" 
                                   data-product-id="<?php echo $item['id']; ?>"
                                   data-precio="<?php echo $item['precio']; ?>">
                            <button class="cantidad-btn increment" data-product-id="<?php echo $item['id']; ?>">+</button>
                        </div>
                        <div class="carrito-item-subtotal" id="subtotal-<?php echo $item['id']; ?>">
                            $<?php echo number_format($item['precio'] * $item['cantidad'], 0, ',', '.'); ?>
                        </div>
                        <div class="carrito-item-actions">
                            <button class="btn-eliminar" 
                                    data-product-id="<?php echo $item['id']; ?>"
                                    onclick="eliminarDelCarrito(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="carrito-resumen">
                <div class="resumen-card">
                    <h3>Resumen del Pedido</h3>
                    <div class="resumen-linea">
                        <span>Subtotal:</span>
                        <span id="subtotal-total">$<?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <div class="resumen-linea">
                        <span>Envío:</span>
                        <span>Gratis</span>
                    </div>
                    <div class="resumen-linea total">
                        <span>Total:</span>
                        <span id="total-final">$<?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    
                    <div class="carrito-actions">
                        <a href="catalogo.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Seguir Comprando
                        </a>
                        <a href="checkout.php" class="btn btn-primary">
                            <i class="fas fa-credit-card"></i> Finalizar Compra
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
// JavaScript para manejar cantidad en carrito
document.addEventListener('DOMContentLoaded', function() {
    
    // Función para actualizar cantidad en el servidor
    function actualizarCantidadEnServidor(productId, nuevaCantidad) {
        const formData = new FormData();
        formData.append('actualizar_cantidad', '1');
        formData.append('producto_id', productId);
        formData.append('cantidad', nuevaCantidad);
        
        fetch('carrito.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar subtotal del producto
                const subtotalElement = document.getElementById(`subtotal-${productId}`);
                const precio = parseFloat(document.querySelector(`.cantidad-input[data-product-id="${productId}"]`).dataset.precio);
                const nuevoSubtotal = precio * nuevaCantidad;
                subtotalElement.textContent = `$${nuevoSubtotal.toLocaleString('es-AR')}`;
                
                // Actualizar totales
                document.getElementById('subtotal-total').textContent = `$${data.total.toLocaleString('es-AR')}`;
                document.getElementById('total-final').textContent = `$${data.total.toLocaleString('es-AR')}`;
                
                // Actualizar contador global del carrito
                updateCartCount();
                
                Toastify({
                    text: "✅ " + data.message,
                    duration: 2000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#10b981"
                }).showToast();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Event listeners para botones de cantidad
    document.querySelectorAll('.increment').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const input = document.querySelector(`.cantidad-input[data-product-id="${productId}"]`);
            const nuevaCantidad = parseInt(input.value) + 1;
            input.value = nuevaCantidad;
            actualizarCantidadEnServidor(productId, nuevaCantidad);
        });
    });
    
    document.querySelectorAll('.decrement').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const input = document.querySelector(`.cantidad-input[data-product-id="${productId}"]`);
            if (input.value > 1) {
                const nuevaCantidad = parseInt(input.value) - 1;
                input.value = nuevaCantidad;
                actualizarCantidadEnServidor(productId, nuevaCantidad);
            }
        });
    });
    
    // Event listener para cambios directos en el input
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            let nuevaCantidad = parseInt(this.value);
            
            if (nuevaCantidad < 1) {
                nuevaCantidad = 1;
                this.value = 1;
            }
            
            actualizarCantidadEnServidor(productId, nuevaCantidad);
        });
    });
});

// Función para eliminar producto del carrito
function eliminarDelCarrito(productId) {
    if (confirm('¿Eliminar producto del carrito?')) {
        fetch(`carrito.php?eliminar=${productId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Eliminar el elemento del DOM
                const itemElement = document.querySelector(`.carrito-item[data-product-id="${productId}"]`);
                if (itemElement) {
                    itemElement.remove();
                }
                
                // Actualizar contador
                updateCartCount();
                
                // Si el carrito queda vacío, recargar la página
                if (data.cart_count === 0) {
                    location.reload();
                } else {
                    // Actualizar totales
                    document.getElementById('subtotal-total').textContent = `$${data.total.toLocaleString('es-AR')}`;
                    document.getElementById('total-final').textContent = `$${data.total.toLocaleString('es-AR')}`;
                }
                
                Toastify({
                    text: "✅ " + data.message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#10b981"
                }).showToast();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

// Función para actualizar el contador del carrito (debe estar definida globalmente)
function updateCartCount() {
    fetch('includes/get_cart_count.php?t=' + new Date().getTime())
        .then(response => response.text())
        .then(count => {
            const cartCount = parseInt(count) || 0;
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = cartCount;
                el.style.display = cartCount > 0 ? 'inline' : 'none';
            });
        })
        .catch(error => console.error('Error actualizando contador:', error));
}

// Inicializar contador al cargar
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});
</script>

<?php include 'includes/footer.php'; ?>