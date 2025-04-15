<?php
// Include the database connection
include 'connect.php';

// Check if connection was successful
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get filter values if set
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;

// Prepare base product query
$productQueryList = "
    SELECT 
        p.productID, 
        p.supplierID, 
        p.productName, 
        p.category, 
        p.costPrice, 
        p.sellingPrice, 
        s.lastDeliveryDate
    FROM product p
    JOIN supplier s ON p.supplierID = s.supplierID
";

// Add filtering to product query if date range is provided
if ($date_from && $date_to) {
    $productQueryList .= " WHERE s.lastDeliveryDate BETWEEN '$date_from' AND '$date_to'";
} elseif ($date_from) {
    $productQueryList .= " WHERE s.lastDeliveryDate >= '$date_from'";
} elseif ($date_to) {
    $productQueryList .= " WHERE s.lastDeliveryDate <= '$date_to'";
}

$productResultList = $connection->query($productQueryList);

// Check if the product query was successful
if ($productResultList === false) {
    die("Error fetching products: " . $connection->error);
}

// Prepare base order query
$orderQueryList = "
    SELECT orderId, customerId, totalPrice, orderType, orderDate 
    FROM `order`
";

// Add filtering to order query if date range is provided
if ($date_from && $date_to) {
    $orderQueryList .= " WHERE orderDate BETWEEN '$date_from' AND '$date_to'";
} elseif ($date_from) {
    $orderQueryList .= " WHERE orderDate >= '$date_from'";
} elseif ($date_to) {
    $orderQueryList .= " WHERE orderDate <= '$date_to'";
}

$orderResultList = $connection->query($orderQueryList);

// Check if the order query was successful
if ($orderResultList === false) {
    die("Error fetching orders: " . $connection->error);
}

// Count total products
$productQuery = "SELECT COUNT(*) as total_products FROM product"; // Replace 'products' with your actual table
$productResult = mysqli_query($connection, $productQuery);
$productRow = mysqli_fetch_assoc($productResult);
$totalProducts = $productRow['total_products'];
 
// Calculate average orders per day
$orderQuery = "SELECT COUNT(*) / COUNT(DISTINCT DATE(orderDate)) AS avg_orders_per_day FROM `order`"; // Replace 'orders' & 'order_date'
$orderResult = mysqli_query($connection, $orderQuery);
$orderRow = mysqli_fetch_assoc($orderResult);
$avgOrders = round($orderRow['avg_orders_per_day'], 2);
?>



?>
