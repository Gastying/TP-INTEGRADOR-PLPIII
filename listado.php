<?php
$pageTitle = "Listado de Productos";
include 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Obtener todos los productos
$query = "SELECT * FROM productos ORDER BY nombre ASC";
$stmt = $db->prepare($query);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="container">
    <section class="catalogo-header">
        <div class="section-header text-center">
            <span class="section-subtitle">Nuestra Colección</span>
            <h2 class="section-title">Listado de <span class="highlight">Productos</span></h2>
            <p class="form-subtitle">Visualiza todos los productos disponibles en formato tabla</p>
        </div>
    </section>

    <div class="tabla-container">
        <div class="table-actions">
            <div class="table-info">
                Mostrando <strong><?php echo count($productos); ?></strong> productos
            </div>
            <div class="export-options">
                <button class="btn btn-secondary btn-sm" onclick="exportToExcel()">
                    <i class="fas fa-download"></i> Exportar Excel
                </button>
            </div>
        </div>

        <table class="tabla-productos" id="productos-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Imagen</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td style="text-align:center; vertical-align:middle;">
                            <img src="<?php echo $producto['imagen']; ?>" 
                                 alt="<?php echo $producto['nombre']; ?>" 
                                 loading="lazy" 
                                 class="thumb-mini">
                        </td>
                        <td>
                            <strong><?php echo $producto['nombre']; ?></strong><br>
                            <span class="sku">SKU: <?php echo strtoupper(substr($producto['nombre'], 0, 2)); ?>-<?php echo str_pad($producto['id'], 3, '0', STR_PAD_LEFT); ?></span>
                        </td>
                        <td>
                            <span class="categoria-badge <?php echo $producto['categoria']; ?>">
                                <?php echo ucfirst($producto['categoria']); ?>
                            </span>
                        </td>
                        <td class="precio">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></td>
                        <td>
                            <?php if ($producto['stock'] > 0): ?>
                                <span class="stock-amount"><?php echo $producto['stock']; ?> unidades</span>
                            <?php else: ?>
                                <span class="stock-out">Agotado</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($producto['destacado']): ?>
                                <span class="status-badge destacado">Destacado</span>
                            <?php elseif ($producto['stock'] > 0): ?>
                                <span class="status-badge activo">Disponible</span>
                            <?php else: ?>
                                <span class="status-badge inactivo">No disponible</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="producto.php?id=<?php echo $producto['id']; ?>" 
                                   class="btn btn-sm" 
                                   title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" action="carrito.php" class="inline-form add-to-cart-form" onsubmit="addToCart(event, this)">
                                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <input type="hidden" name="agregar_carrito" value="1">
                                    <button type="submit" 
                                            name="agregar_carrito" 
                                            class="btn btn-sm btn-success" 
                                            title="Agregar al carrito"
                                            <?php echo $producto['stock'] <= 0 ? 'disabled' : ''; ?>>
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<style>
.table-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding: 1rem;
    background: var(--white);
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.categoria-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.categoria-badge.femenino {
    background-color: #fce7f3;
    color: #db2777;
}

.categoria-badge.masculino {
    background-color: #dbeafe;
    color: #2563eb;
}

.categoria-badge.unisex {
    background-color: #f0f9ff;
    color: #0369a1;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-badge.destacado {
    background-color: #fef3c7;
    color: #d97706;
}

.status-badge.activo {
    background-color: #d1fae5;
    color: #065f46;
}

.status-badge.inactivo {
    background-color: #fef2f2;
    color: #dc2626;
}

.stock-amount {
    color: var(--success);
    font-weight: 500;
}

.stock-out {
    color: var(--danger);
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.inline-form {
    display: inline;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.btn-success {
    background-color: var(--success);
    color: white;
}

.btn-success:hover:not(:disabled) {
    background-color: #059669;
}

.btn-success:disabled {
    background-color: var(--light-gray);
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .tabla-container {
        overflow-x: auto;
    }
    
    .table-actions {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
}
</style>

<script>
function exportToExcel() {
    Toastify({
        text: "Generando archivo Excel...",
        duration: 2000,
        gravity: "top",
        position: "right"
    }).showToast();
    
    // En una implementación real, aquí iría la lógica para exportar a Excel
    // Por ahora simulamos la descarga
    setTimeout(() => {
        Toastify({
            text: "Archivo descargado correctamente",
            duration: 2000,
            gravity: "top",
            position: "right",
            backgroundColor: "#10b981"
        }).showToast();
    }, 1500);
}

// Toast para botones de acción
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.tabla-productos .btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!btn.disabled) {
                Toastify({
                    text: 'Abriendo detalle…',
                    duration: 1800,
                    gravity: 'top',
                    position: 'right'
                }).showToast();
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>