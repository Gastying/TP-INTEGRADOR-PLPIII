<?php
session_start();

// Verificar que el carrito no esté vacío
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header('Location: carrito.php');
    exit;
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar datos
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = htmlspecialchars(trim($_POST['email']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $direccion = htmlspecialchars(trim($_POST['direccion']));
    $ciudad = htmlspecialchars(trim($_POST['ciudad']));
    $provincia = htmlspecialchars(trim($_POST['provincia']));
    $codigo_postal = htmlspecialchars(trim($_POST['codigo_postal']));
    $metodo_pago = htmlspecialchars(trim($_POST['metodo_pago']));
    $observaciones = htmlspecialchars(trim($_POST['observaciones'] ?? ''));
    
    // Calcular total con posible descuento
    $subtotal = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $subtotal += $item['precio'] * $item['cantidad'];
    }
    
    $descuento = 0;
    if ($metodo_pago === 'transferencia') {
        $descuento = $subtotal * 0.10;
    }
    
    $total = $subtotal - $descuento;
    
    // Guardar en base de datos (aquí iría tu lógica de BD)
    try {
        require_once 'includes/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        // 1. Insertar pedido
        $query = "INSERT INTO pedidos (nombre, email, telefono, direccion, ciudad, provincia, codigo_postal, metodo_pago, observaciones, subtotal, descuento, total, estado) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";
        $stmt = $db->prepare($query);
        $stmt->execute([$nombre, $email, $telefono, $direccion, $ciudad, $provincia, $codigo_postal, $metodo_pago, $observaciones, $subtotal, $descuento, $total]);
        
        $pedido_id = $db->lastInsertId();
        
        // 2. Insertar items del pedido y actualizar stock
        foreach ($_SESSION['carrito'] as $item) {
            // Insertar item
            $query_item = "INSERT INTO items_pedido (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)";
            $stmt_item = $db->prepare($query_item);
            $stmt_item->execute([$pedido_id, $item['id'], $item['cantidad'], $item['precio']]);
            
            // Actualizar stock
            $query_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
            $stmt_stock = $db->prepare($query_stock);
            $stmt_stock->execute([$item['cantidad'], $item['id']]);
        }
        
        // 3. Limpiar carrito
        $_SESSION['carrito'] = [];
        
        // 4. Redirigir a confirmación
        $_SESSION['ultimo_pedido'] = $pedido_id;
        header('Location: confirmacion_pedido.php');
        exit;
        
    } catch (Exception $e) {
        // En caso de error
        error_log("Error al procesar pedido: " . $e->getMessage());
        header('Location: error_pedido.php');
        exit;
    }
} else {
    // Si no es POST, redirigir al checkout
    header('Location: checkout.php');
    exit;
}
?>