<?php
// includes/footer.php
// Solo incluir si no se ha incluido ya (para evitar errores)
if (!defined('FOOTER_INCLUDED')) {
    define('FOOTER_INCLUDED', true);
?>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <img src="assets/img/logo.png" alt="Essence Shop" class="logo-img">
                        <h3>Essence Shop</h3>
                    </div>
                    <p>Tu destino premium para fragancias exclusivas importadas de todo el mundo. Ofrecemos los mejores perfumes con calidad garantizada.</p>
                    <div class="social-links">
                        <a href="https://www.instagram.com/essenceshop.arg/" target="_blank" rel="noopener" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/5493764903595" target="_blank" rel="noopener" aria-label="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://tiktok.com" target="_blank" rel="noopener" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>Enlaces Rápidos</h4>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                        <li><a href="catalogo.php"><i class="fas fa-gem"></i> Catálogo</a></li>
                        <li><a href="listado.php"><i class="fas fa-list"></i> Listado</a></li>
                        <li><a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito 
                            <span class="cart-count" style="display: none;">(0)</span>
                        </a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Categorías</h4>
                    <ul class="footer-links">
                        <li><a href="catalogo.php?categoria=femenino"><i class="fas fa-female"></i> Perfumes Femeninos</a></li>
                        <li><a href="catalogo.php?categoria=masculino"><i class="fas fa-male"></i> Perfumes Masculinos</a></li>
                        <li><a href="catalogo.php?categoria=unisex"><i class="fas fa-venus-mars"></i> Perfumes Unisex</a></li>
                        <li><a href="catalogo.php?orden=nuevos"><i class="fas fa-star"></i> Nuevos Lanzamientos</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Contacto</h4>
                    <div class="contact-info">
                        <p>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:hola@essence.com">hola@essence.com</a>
                        </p>
                        <p>
                            <i class="fas fa-phone"></i>
                            <a href="https://wa.me/5493764903595" target="_blank">+54 9 3764 903595</a>
                        </p>
                        <p>
                            <i class="fas fa-map-marker-alt"></i>
                            Misiones, Argentina
                        </p>
                        <p>
                            <i class="fas fa-clock"></i>
                            Lun - Vie: 9:00 - 18:00
                        </p>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>Newsletter</h4>
                    <p>Suscríbete para recibir ofertas exclusivas y novedades.</p>
                    <form class="newsletter-form" method="POST" action="newsletter_subscribe.php">
                        <div class="newsletter-input-group">
                            <input type="email" name="email" placeholder="Tu email" required>
                            <button type="submit" class="btn-newsletter">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <small>No spam, prometemos.</small>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; 2025 Essence Shop. Todos los derechos reservados.</p>
                    <div class="footer-links">
                        <a href="terminos.php">Términos y Condiciones</a>
                        <a href="privacidad.php">Política de Privacidad</a>
                        <a href="devoluciones.php">Política de Devoluciones</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Botón flotante de WhatsApp -->
    <a href="https://wa.me/5493764903595?text=Hola!%20Me%20interesa%20saber%20más%20sobre%20sus%20productos" 
       class="whatsapp-float" 
       target="_blank" 
       rel="noopener"
       aria-label="Contactar por WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Botón para ir arriba -->
    <button class="back-to-top" aria-label="Volver arriba">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="js/carrito.js"></script>
    <script src="js/script.js"></script>

    <!-- Scripts adicionales para funcionalidades del footer -->
    <script>
    // Ir arriba
    document.querySelector('.back-to-top').addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Mostrar/ocultar botón ir arriba
    window.addEventListener('scroll', () => {
        const backToTop = document.querySelector('.back-to-top');
        if (window.scrollY > 300) {
            backToTop.style.display = 'flex';
        } else {
            backToTop.style.display = 'none';
        }
    });

    // Actualizar contador del carrito en el footer
    function updateFooterCartCount() {
        fetch('includes/get_cart_count.php?t=' + new Date().getTime())
            .then(response => response.text())
            .then(count => {
                const cartCount = parseInt(count) || 0;
                const footerCartCount = document.querySelector('.footer-links .cart-count');
                if (footerCartCount) {
                    if (cartCount > 0) {
                        footerCartCount.textContent = `(${cartCount})`;
                        footerCartCount.style.display = 'inline';
                    } else {
                        footerCartCount.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error actualizando contador footer:', error));
    }

    // Newsletter form
    document.querySelector('.newsletter-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Toastify({
                    text: "¡Te has suscrito correctamente!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#10b981"
                }).showToast();
                this.reset();
            } else {
                Toastify({
                    text: data.message || "Error al suscribirse",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444"
                }).showToast();
            }
        })
        .catch(error => {
            Toastify({
                text: "Error de conexión",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ef4444"
            }).showToast();
        });
    });

    // Inicializar contador del footer al cargar
    document.addEventListener('DOMContentLoaded', function() {
        updateFooterCartCount();
        
        // Actualizar cada 30 segundos
        setInterval(updateFooterCartCount, 30000);
    });

    // Función global para que otros archivos puedan actualizar el footer
    window.updateFooterCartCount = updateFooterCartCount;
    </script>

    </body>
</html>

<?php } ?>