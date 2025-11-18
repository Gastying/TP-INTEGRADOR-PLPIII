<?php
/**
 * Funciones útiles para Essence Shop
 */

/**
 * Obtener la URL base del sitio
 */
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . "://" . $host . dirname($_SERVER['PHP_SELF']);
}

/**
 * Sanitizar entrada de usuario
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Formatear precio
 */
function formatPrice($price) {
    return '$' . number_format($price, 0, ',', '.');
}

/**
 * Obtener productos destacados
 */
function getFeaturedProducts($limit = 3) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM productos WHERE destacado = 1 AND stock > 0 LIMIT ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener productos por categoría
 */
function getProductsByCategory($category, $limit = null) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM productos WHERE categoria = ? AND stock > 0";
    if ($limit) {
        $query .= " LIMIT ?";
    }
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $category);
    if ($limit) {
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
    }
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Buscar productos
 */
function searchProducts($searchTerm) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM productos WHERE (nombre LIKE ? OR descripcion LIKE ?) AND stock > 0";
    $stmt = $db->prepare($query);
    $searchTerm = "%$searchTerm%";
    $stmt->bindParam(1, $searchTerm);
    $stmt->bindParam(2, $searchTerm);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener producto por ID
 */
function getProductById($id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM productos WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Calcular total del carrito
 */
function getCartTotal() {
    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }
    
    return $total;
}

/**
 * Obtener cantidad de items en carrito
 */
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

/**
 * Generar SKU automático
 */
function generateSKU($productName, $productId) {
    $prefix = strtoupper(substr($productName, 0, 2));
    return $prefix . '-' . str_pad($productId, 3, '0', STR_PAD_LEFT);
}

/**
 * Validar stock disponible
 */
function checkStock($productId, $quantity) {
    $product = getProductById($productId);
    if (!$product) {
        return false;
    }
    
    return $product['stock'] >= $quantity;
}

/**
 * Reducir stock después de compra
 */
function reduceStock($productId, $quantity) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?";
    $stmt = $db->prepare($query);
    return $stmt->execute([$quantity, $productId, $quantity]);
}

/**
 * Obtener estadísticas del sitio
 */
function getSiteStats() {
    $database = new Database();
    $db = $database->getConnection();
    
    $stats = [];
    
    // Total productos
    $query = "SELECT COUNT(*) as total FROM productos";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['total_productos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos en stock
    $query = "SELECT COUNT(*) as total FROM productos WHERE stock > 0";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['productos_stock'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos agotados
    $query = "SELECT COUNT(*) as total FROM productos WHERE stock = 0";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['productos_agotados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    return $stats;
}

/**
 * Log de actividades (para admin)
 */
function logActivity($action, $details = '') {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    
    $user_id = $_SESSION['user_id'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    return $stmt->execute([$user_id, $action, $details, $ip_address]);
}

/**
 * Enviar email de contacto
 */
function sendContactEmail($name, $email, $message) {
    // En una implementación real, aquí iría la lógica para enviar emails
    // Por ahora solo simulamos el envío
    
    $to = "hola@essence.com";
    $subject = "Nuevo mensaje de contacto de $name";
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $body = "
    <html>
    <body>
        <h2>Nuevo mensaje de contacto</h2>
        <p><strong>Nombre:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Mensaje:</strong></p>
        <p>$message</p>
    </body>
    </html>
    ";
    
    // En producción, descomentar esta línea:
    // return mail($to, $subject, $body, $headers);
    
    // Por ahora solo retornamos true para simular éxito
    return true;
}

/**
 * Generar paginación
 */
function generatePagination($currentPage, $totalPages, $url) {
    if ($totalPages <= 1) return '';
    
    $pagination = '<div class="pagination">';
    
    // Botón anterior
    if ($currentPage > 1) {
        $pagination .= '<a href="' . $url . '?page=' . ($currentPage - 1) . '" class="page-btn">';
        $pagination .= '<i class="fas fa-chevron-left"></i> Anterior</a>';
    }
    
    // Números de página
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            $pagination .= '<span class="page-btn active">' . $i . '</span>';
        } else {
            $pagination .= '<a href="' . $url . '?page=' . $i . '" class="page-btn">' . $i . '</a>';
        }
    }
    
    // Botón siguiente
    if ($currentPage < $totalPages) {
        $pagination .= '<a href="' . $url . '?page=' . ($currentPage + 1) . '" class="page-btn">';
        $pagination .= 'Siguiente <i class="fas fa-chevron-right"></i></a>';
    }
    
    $pagination .= '</div>';
    
    return $pagination;
}

/**
 * Obtener productos aleatorios
 */
function getRandomProducts($limit = 3) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM productos WHERE stock > 0 ORDER BY RAND() LIMIT ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Validar datos de producto
 */
function validateProductData($data) {
    $errors = [];
    
    if (empty($data['nombre'])) {
        $errors[] = "El nombre del producto es requerido";
    }
    
    if (empty($data['precio']) || !is_numeric($data['precio']) || $data['precio'] <= 0) {
        $errors[] = "El precio debe ser un número válido mayor a 0";
    }
    
    if (empty($data['descripcion'])) {
        $errors[] = "La descripción es requerida";
    }
    
    if (empty($data['categoria']) || !in_array($data['categoria'], ['femenino', 'masculino', 'unisex'])) {
        $errors[] = "La categoría debe ser femenino, masculino o unisex";
    }
    
    if (!isset($data['stock']) || !is_numeric($data['stock']) || $data['stock'] < 0) {
        $errors[] = "El stock debe ser un número válido mayor o igual a 0";
    }
    
    return $errors;
}
?>