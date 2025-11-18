<?php
session_start();

// Verificar que el carrito no esté vacío
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header('Location: carrito.php');
    exit;
}

$pageTitle = "Finalizar Compra";
include 'includes/header.php';

// Calcular totales
$subtotal = 0;
foreach ($_SESSION['carrito'] as $item) {
    $subtotal += $item['precio'] * $item['cantidad'];
}
$envio = 0; // Envío gratis
$total = $subtotal + $envio;
?>

<main class="container">
    <section class="checkout-header">
        <div class="section-header">
            <h2 class="section-title">Finalizar <span class="highlight">Compra</span></h2>
            <p class="section-subtitle">Completa tus datos para completar la compra</p>
        </div>
    </section>

    <div class="checkout-content">
        <div class="checkout-form-section">
            <form id="checkout-form" method="POST" action="procesar_pedido.php">
                <!-- Información de Contacto -->
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Información de Contacto</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono *</label>
                            <input type="tel" id="telefono" name="telefono" required>
                        </div>
                    </div>
                </div>

                <!-- Información de Envío -->
                <div class="form-section">
                    <h3><i class="fas fa-truck"></i> Información de Envío</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="direccion">Dirección *</label>
                            <input type="text" id="direccion" name="direccion" required>
                        </div>
                        <div class="form-group">
                            <label for="ciudad">Ciudad *</label>
                            <input type="text" id="ciudad" name="ciudad" required>
                        </div>
                        <div class="form-group">
                            <label for="provincia">Provincia *</label>
                            <select id="provincia" name="provincia" required>
                                <option value="">Seleccionar provincia</option>
                                <option value="Buenos Aires">Buenos Aires</option>
                                <option value="CABA">Ciudad de Buenos Aires</option>
                                <option value="Catamarca">Catamarca</option>
                                <option value="Chaco">Chaco</option>
                                <option value="Chubut">Chubut</option>
                                <option value="Córdoba">Córdoba</option>
                                <option value="Corrientes">Corrientes</option>
                                <option value="Entre Ríos">Entre Ríos</option>
                                <option value="Formosa">Formosa</option>
                                <option value="Jujuy">Jujuy</option>
                                <option value="La Pampa">La Pampa</option>
                                <option value="La Rioja">La Rioja</option>
                                <option value="Mendoza">Mendoza</option>
                                <option value="Misiones">Misiones</option>
                                <option value="Neuquén">Neuquén</option>
                                <option value="Río Negro">Río Negro</option>
                                <option value="Salta">Salta</option>
                                <option value="San Juan">San Juan</option>
                                <option value="San Luis">San Luis</option>
                                <option value="Santa Cruz">Santa Cruz</option>
                                <option value="Santa Fe">Santa Fe</option>
                                <option value="Santiago del Estero">Santiago del Estero</option>
                                <option value="Tierra del Fuego">Tierra del Fuego</option>
                                <option value="Tucumán">Tucumán</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="codigo_postal">Código Postal *</label>
                            <input type="text" id="codigo_postal" name="codigo_postal" required>
                        </div>
                    </div>
                </div>

                <!-- Método de Pago -->
                <div class="form-section">
                    <h3><i class="fas fa-credit-card"></i> Método de Pago</h3>
                    <div class="payment-methods">
                        <div class="payment-option">
                            <input type="radio" id="transferencia" name="metodo_pago" value="transferencia" required>
                            <label for="transferencia">
                                <i class="fas fa-university"></i>
                                <span>Transferencia Bancaria</span>
                                <small>10% de descuento</small>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="mercadopago" name="metodo_pago" value="mercadopago" required>
                            <label for="mercadopago">
                                <i class="fas fa-hand-holding-usd"></i>
                                <span>Mercado Pago</span>
                                <small>Pago rápido y seguro</small>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="efectivo" name="metodo_pago" value="efectivo" required>
                            <label for="efectivo">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Efectivo</span>
                                <small>Acordar con el vendedor</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="form-section">
                    <h3><i class="fas fa-sticky-note"></i> Observaciones (Opcional)</h3>
                    <div class="form-group">
                        <textarea id="observaciones" name="observaciones" rows="4" 
                                  placeholder="Alguna observación especial sobre tu pedido..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="carrito.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Carrito
                    </a>
                    <button type="submit" class="btn btn-primary btn-confirmar">
                        <i class="fas fa-check-circle"></i> Confirmar Pedido
                    </button>
                </div>
            </form>
        </div>

        <div class="checkout-summary">
            <div class="summary-card">
                <h3>Resumen del Pedido</h3>
                
                <div class="order-items">
                    <?php foreach ($_SESSION['carrito'] as $item): ?>
                        <div class="order-item">
                            <div class="item-image">
                                <img src="<?php echo $item['imagen']; ?>" alt="<?php echo $item['nombre']; ?>">
                            </div>
                            <div class="item-details">
                                <h4><?php echo $item['nombre']; ?></h4>
                                <div class="item-price">$<?php echo number_format($item['precio'], 0, ',', '.'); ?></div>
                                <div class="item-quantity">Cantidad: <?php echo $item['cantidad']; ?></div>
                            </div>
                            <div class="item-subtotal">
                                $<?php echo number_format($item['precio'] * $item['cantidad'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-totals">
                    <div class="summary-line">
                        <span>Subtotal:</span>
                        <span>$<?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Envío:</span>
                        <span class="free-shipping">Gratis</span>
                    </div>
                    <div class="summary-line total">
                        <span>Total:</span>
                        <span>$<?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                </div>

                <div class="shipping-info">
                    <i class="fas fa-shipping-fast"></i>
                    <div>
                        <strong>Envío gratis</strong>
                        <small>Recibí tu pedido en 3-5 días hábiles</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.checkout-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
    margin-top: 2rem;
}

.form-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.form-section h3 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    color: var(--primary);
    font-size: 1.2rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: var(--dark);
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 0.75rem;
    border: 1px solid var(--light-gray);
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.payment-option {
    position: relative;
}

.payment-option input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.payment-option label {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px solid var(--light-gray);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-option label:hover {
    border-color: var(--primary);
}

.payment-option input[type="radio"]:checked + label {
    border-color: var(--primary);
    background-color: #f0f9ff;
}

.payment-option label i {
    font-size: 1.5rem;
    color: var(--primary);
}

.payment-option label span {
    font-weight: 500;
    flex: 1;
}

.payment-option label small {
    color: var(--gray);
    font-size: 0.8rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    margin-top: 2rem;
}

.btn-confirmar {
    flex: 1;
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

.summary-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 2rem;
}

.order-items {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 1.5rem;
}

.order-item {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--light-gray);
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-details {
    flex: 1;
}

.item-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.9rem;
}

.item-price {
    color: var(--primary);
    font-weight: 600;
    font-size: 0.9rem;
}

.item-quantity {
    color: var(--gray);
    font-size: 0.8rem;
}

.item-subtotal {
    font-weight: 600;
    color: var(--dark);
}

.summary-totals {
    border-top: 1px solid var(--light-gray);
    padding-top: 1rem;
}

.summary-line {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.summary-line.total {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary);
    border-top: 1px solid var(--light-gray);
    padding-top: 0.5rem;
    margin-top: 0.5rem;
}

.free-shipping {
    color: var(--success);
    font-weight: 600;
}

.shipping-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--light-bg);
    border-radius: 8px;
    margin-top: 1rem;
}

.shipping-info i {
    color: var(--success);
    font-size: 1.5rem;
}

.shipping-info strong {
    display: block;
    margin-bottom: 0.25rem;
}

.shipping-info small {
    color: var(--gray);
}

@media (max-width: 968px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const btnConfirmar = form.querySelector('.btn-confirmar');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar formulario
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Mostrar loading
        const originalText = btnConfirmar.innerHTML;
        btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        btnConfirmar.disabled = true;
        
        // Simular envío (en producción esto enviaría realmente)
        setTimeout(() => {
            // En producción, quitar el setTimeout y descomentar la siguiente línea:
            // form.submit();
            
            // Por ahora solo redirigimos a una página de confirmación
            window.location.href = 'confirmacion_pedido.php';
        }, 2000);
    });
    
    // Calcular descuento por transferencia
    const metodoPagoRadios = document.querySelectorAll('input[name="metodo_pago"]');
    const totalElement = document.querySelector('.summary-line.total span:last-child');
    const subtotal = <?php echo $subtotal; ?>;
    
    metodoPagoRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'transferencia') {
                const descuento = subtotal * 0.10;
                const totalConDescuento = subtotal - descuento;
                totalElement.textContent = `$${totalConDescuento.toLocaleString('es-AR')}`;
            } else {
                totalElement.textContent = `$${subtotal.toLocaleString('es-AR')}`;
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>