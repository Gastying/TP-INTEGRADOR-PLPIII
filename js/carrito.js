// Función global para agregar al carrito con AJAX - Versión Mejorada
function addToCart(event, formElement) {
    event.preventDefault(); // Prevenir envío normal del formulario
    
    const formData = new FormData(formElement);
    const submitButton = formElement.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    const originalDisabled = submitButton.disabled;
    
    // Mostrar loading en el botón
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    submitButton.disabled = true;
    
    // Enviar petición AJAX
    fetch('carrito.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.text();
    })
    .then(data => {
        // Mostrar toast de éxito
        Toastify({
            text: "✅ Producto agregado al carrito",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#10b981",
            stopOnFocus: true
        }).showToast();
        
        // Actualizar contador del carrito
        updateCartCount();
        
        // Forzar actualización de la sesión
        setTimeout(() => {
            updateCartCount();
        }, 500);
    })
    .catch(error => {
        console.error('Error:', error);
        Toastify({
            text: "❌ Error al agregar al carrito",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ef4444"
        }).showToast();
    })
    .finally(() => {
        // Restaurar botón después de 1 segundo
        setTimeout(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = originalDisabled;
        }, 1000);
    });
}

// Función para actualizar el contador del carrito - Versión Mejorada
function updateCartCount() {
    fetch('includes/get_cart_count.php?t=' + new Date().getTime()) // Evitar cache
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener contador');
            }
            return response.text();
        })
        .then(count => {
            const cartCount = parseInt(count) || 0;
            
            // Actualizar todos los contadores del carrito
            document.querySelectorAll('.cart-count').forEach(element => {
                if (cartCount > 0) {
                    element.textContent = cartCount;
                    element.style.display = 'inline';
                } else {
                    element.style.display = 'none';
                }
            });
            
            // También actualizar en el sessionStorage para consistencia
            sessionStorage.setItem('cartCount', cartCount);
        })
        .catch(error => {
            console.error('Error updating cart count:', error);
            // Usar valor de sessionStorage como fallback
            const fallbackCount = sessionStorage.getItem('cartCount') || 0;
            document.querySelectorAll('.cart-count').forEach(element => {
                if (fallbackCount > 0) {
                    element.textContent = fallbackCount;
                    element.style.display = 'inline';
                }
            });
        });
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    // También actualizar cada 30 segundos por si hay cambios en otras pestañas
    setInterval(updateCartCount, 30000);
});

function addToCart(event, formElement) {
    event.preventDefault(); // Prevenir envío normal del formulario
    
    const formData = new FormData(formElement);
    const submitButton = formElement.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    const originalDisabled = submitButton.disabled;
    
    // Mostrar loading en el botón
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    submitButton.disabled = true;
    
    // Enviar petición AJAX
    fetch('carrito.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.text();
    })
    .then(data => {
        // Mostrar toast de éxito
        Toastify({
            text: "✅ Producto agregado al carrito",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#10b981",
            stopOnFocus: true
        }).showToast();
        
        // Actualizar contador del carrito
        updateCartCount();
        
        // Forzar actualización de la sesión
        setTimeout(() => {
            updateCartCount();
        }, 500);
    })
    .catch(error => {
        console.error('Error:', error);
        Toastify({
            text: "❌ Error al agregar al carrito",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ef4444"
        }).showToast();
    })
    .finally(() => {
        // Restaurar botón después de 1 segundo
        setTimeout(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = originalDisabled;
        }, 1000);
    });
}

// js/carrito.js - Función global para actualizar el contador del carrito
function updateCartCount() {
    fetch('includes/get_cart_count.php?t=' + new Date().getTime())
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener contador');
            }
            return response.text();
        })
        .then(count => {
            const cartCount = parseInt(count) || 0;
            
            // Actualizar todos los contadores del carrito en el HEADER
            document.querySelectorAll('.nav .cart-count').forEach(element => {
                if (cartCount > 0) {
                    element.textContent = cartCount;
                    element.style.display = 'inline';
                } else {
                    element.style.display = 'none';
                }
            });
            
            // Actualizar contador en el FOOTER (si existe)
            const footerCartCount = document.querySelector('.footer-links .cart-count');
            if (footerCartCount) {
                if (cartCount > 0) {
                    footerCartCount.textContent = `(${cartCount})`;
                    footerCartCount.style.display = 'inline';
                } else {
                    footerCartCount.style.display = 'none';
                }
            }
            
            // También actualizar en el sessionStorage para consistencia
            sessionStorage.setItem('cartCount', cartCount);
            
            console.log('🛒 Contador actualizado:', cartCount);
        })
        .catch(error => {
            console.error('Error updating cart count:', error);
            // Usar valor de sessionStorage como fallback
            const fallbackCount = sessionStorage.getItem('cartCount') || 0;
            document.querySelectorAll('.cart-count').forEach(element => {
                if (fallbackCount > 0) {
                    element.textContent = fallbackCount;
                    element.style.display = 'inline';
                }
            });
        });
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    // También actualizar cada 30 segundos por si hay cambios en otras pestañas
    setInterval(updateCartCount, 30000);
});