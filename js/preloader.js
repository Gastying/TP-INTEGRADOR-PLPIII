// Preloader simplificado y confiable
document.addEventListener('DOMContentLoaded', function() {
    const preloader = document.getElementById('preloader');
    
    // Ocultar preloader después de que todo cargue
    window.addEventListener('load', function() {
        setTimeout(function() {
            if (preloader) {
                preloader.style.opacity = '0';
                preloader.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }
        }, 1000); // 1 segundo de delay
    });

    // Fallback: si después de 3 segundos aún no se oculta, forzar
    setTimeout(function() {
        if (preloader && preloader.style.display !== 'none') {
            preloader.style.display = 'none';
        }
    }, 3000);
});