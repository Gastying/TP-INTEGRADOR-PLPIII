<?php
// header.php - VERSIÓN CORREGIDA CON PRELOADER INTELIGENTE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// INCLUIR LA BASE DE DATOS
require_once 'includes/database.php';

// Determinar si mostrar preloader (solo en index.php y primera visita)
$mostrarPreloader = false;
if (basename($_SERVER['PHP_SELF']) == 'index.php') {
    // Solo mostrar en la primera visita a index.php
    if (!isset($_SESSION['preloader_visto'])) {
        $mostrarPreloader = true;
        $_SESSION['preloader_visto'] = true;
    }
}

if (!defined('HEADER_INCLUDED')) {
    define('HEADER_INCLUDED', true);
?>
<!DOCTYPE html>
<html lang="es-AR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Essence Shop - <?php echo $pageTitle ?? 'Perfumes de Lujo'; ?></title>
  
  <!-- Todos los CSS y scripts comunes -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <!-- Preloader SOLO en index.php y primera visita -->
  <?php if ($mostrarPreloader): ?>
  <div id="preloader" style="position:fixed;top:0;left:0;width:100%;height:100%;background:white;z-index:9999;display:flex;align-items:center;justify-content:center;transition:opacity 0.5s ease;">
    <div style="text-align:center;">
      <img src="assets/img/logo.png" alt="Essence Shop" style="height:80px;width:auto;">
      <div style="margin-top:20px;font-size:16px;color:#666;">Cargando...</div>
    </div>
  </div>

  <!-- Script para ocultar el preloader (solo cuando existe) -->
  <script>
  // Preloader solo para index.php en primera visita
  window.addEventListener('load', function() {
    setTimeout(function() {
      const preloader = document.getElementById('preloader');
      if (preloader) {
        preloader.style.opacity = '0';
        setTimeout(function() {
          preloader.style.display = 'none';
        }, 500);
      }
    }, 1500); // 1.5 segundos de delay
  });

  // Fallback: ocultar después de 3 segundos máximo
  setTimeout(function() {
    const preloader = document.getElementById('preloader');
    if (preloader && preloader.style.display !== 'none') {
      preloader.style.display = 'none';
    }
  }, 3000);
  </script>
  <?php endif; ?>

  <header class="site-header">
    <div class="container header-inner">
      <div class="logo">
        <a href="index.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 1rem;">
          <img src="assets/img/logo.png" alt="Essence Shop" class="logo-img">
          <h1>Essence Shop</h1>
        </a>
      </div>
      <nav class="nav">
        <a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'aria-current="page"' : ''; ?>>
          <i class="fas fa-home"></i> Inicio
        </a>
        <a href="catalogo.php" <?php echo basename($_SERVER['PHP_SELF']) == 'catalogo.php' ? 'aria-current="page"' : ''; ?>>
          <i class="fas fa-gem"></i> Catálogo
        </a>
        <a href="listado.php" <?php echo basename($_SERVER['PHP_SELF']) == 'listado.php' ? 'aria-current="page"' : ''; ?>>
          <i class="fas fa-list"></i> Listado
        </a>
        <a href="carrito.php" <?php echo basename($_SERVER['PHP_SELF']) == 'carrito.php' ? 'aria-current="page"' : ''; ?>>
          <i class="fas fa-shopping-cart"></i> Carrito
          <span class="cart-count" style="display: none;">0</span>
        </a>
        <?php if(isset($_SESSION['usuario'])): ?>
          <a href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

<?php } ?>