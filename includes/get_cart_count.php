<?php
// includes/get_cart_count.php
session_start();

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

// Solo devolver el número
header('Content-Type: text/plain');
echo getCartItemCount();
?>