<?php
session_start();

// Verificar que hay un pedido reciente
if (!isset($_SESSION['ultimo_pedido'])) {
    header('Location: index.php');
    exit;
}

$pedido_id = $_SESSION['ultimo_pedido'];
unset($_SESSION['ultimo_pedido']); // Limpiar después de usar

$pageTitle = "¡Pedido Confirmado!";
include 'includes/header.php';
?>

<main class="container">
    <section class="confirmation-section">
        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>¡Pedido Confirmado!</h1>
            <p class="confirmation-subtitle">Gracias por tu compra. Tu pedido ha sido procesado exitosamente.</p>
            
            <div class="order-details">
                <div class="order-number">
                    <strong>Número de Pedido:</strong>
                    <span>#<?php echo str_pad($pedido_id, 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                
                <div class="confirmation-message">
                    <p>Hemos enviado un correo de confirmación a tu email con los detalles del pedido.</p>
                    <p>Te contactaremos dentro de las próximas 24 horas para coordinar el envío.</p>
                </div>
            </div>
            
            <div class="next-steps">
                <h3>Próximos pasos:</h3>
                <ul>
                    <li><i class="fas fa-envelope"></i> Recibirás un email de confirmación</li>
                    <li><i class="fas fa-phone"></i> Nos contactaremos para coordinar el envío</li>
                    <li><i class="fas fa-truck"></i> Recibirás tu pedido en 3-5 días hábiles</li>
                </ul>
            </div>
            
            <div class="confirmation-actions">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Volver al Inicio
                </a>
                <a href="catalogo.php" class="btn btn-secondary">
                    <i class="fas fa-shopping-bag"></i> Seguir Comprando
                </a>
            </div>
        </div>
    </section>
</main>

<style>
.confirmation-section {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    padding: 2rem 0;
}

.confirmation-card {
    background: white;
    padding: 3rem;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    text-align: center;
    max-width: 600px;
    width: 100%;
}

.confirmation-icon {
    font-size: 4rem;
    color: var(--success);
    margin-bottom: 1.5rem;
}

.confirmation-card h1 {
    color: var(--success);
    margin-bottom: 1rem;
}

.confirmation-subtitle {
    color: var(--gray);
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.order-details {
    background: var(--light-bg);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.order-number {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.order-number span {
    color: var(--primary);
    font-weight: 700;
    font-size: 1.4rem;
}

.confirmation-message {
    text-align: left;
    color: var(--gray);
}

.confirmation-message p {
    margin-bottom: 0.5rem;
}

.next-steps {
    text-align: left;
    margin-bottom: 2rem;
}

.next-steps h3 {
    margin-bottom: 1rem;
    color: var(--dark);
}

.next-steps ul {
    list-style: none;
    padding: 0;
}

.next-steps li {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
    color: var(--gray);
}

.next-steps li i {
    color: var(--primary);
    width: 20px;
}

.confirmation-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

@media (max-width: 768px) {
    .confirmation-card {
        padding: 2rem;
        margin: 1rem;
    }
    
    .confirmation-actions {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>