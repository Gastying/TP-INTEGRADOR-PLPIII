<?php
$pageTitle = "Error en el Pedido";
include 'includes/header.php';
?>

<main class="container">
    <section class="error-section">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1>Error en el Procesamiento</h1>
            <p class="error-subtitle">Lo sentimos, ha ocurrido un error al procesar tu pedido.</p>
            
            <div class="error-message">
                <p>Por favor, intenta nuevamente en unos minutos.</p>
                <p>Si el problema persiste, contáctanos directamente por WhatsApp.</p>
            </div>
            
            <div class="error-actions">
                <a href="checkout.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Volver al Checkout
                </a>
                <a href="https://wa.me/5493764903595" class="btn btn-secondary" target="_blank">
                    <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
                </a>
            </div>
        </div>
    </section>
</main>

<style>
.error-section {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    padding: 2rem 0;
}

.error-card {
    background: white;
    padding: 3rem;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    text-align: center;
    max-width: 500px;
    width: 100%;
}

.error-icon {
    font-size: 4rem;
    color: var(--danger);
    margin-bottom: 1.5rem;
}

.error-card h1 {
    color: var(--danger);
    margin-bottom: 1rem;
}

.error-subtitle {
    color: var(--gray);
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.error-message {
    margin-bottom: 2rem;
    color: var(--gray);
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

@media (max-width: 768px) {
    .error-card {
        padding: 2rem;
        margin: 1rem;
    }
    
    .error-actions {
        flex-direction: column;
    }
}
</style>

<?php include 'includes/footer.php'; ?>