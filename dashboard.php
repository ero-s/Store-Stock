<?php    
    include 'connect.php'; // Database connection
    include 'readrecords.php'; // Assuming this file contains additional query functions
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Stock Records Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    html, body {
        height: 100%;
        margin: 0;
        background-color: #fffae8;
        font-family: 'Segoe UI', sans-serif;
    }
    .header {
        background: linear-gradient(to right, #791a0d, #af3222);
        color: white;
        padding: 30px;
        text-align: center;
        border-bottom: 5px solid #6b1b0d;
    }
    .container-section {
        margin-top: 30px;
        margin-bottom: 40px;
    }
    .section-title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 15px;
    }
    .card-summary {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }
    .card-summary h3 {
        margin-bottom: 10px;
        font-size: 22px;
    }
    .card-summary p {
        font-size: 18px;
        color: #555;
    }
    .filter-form label {
        font-weight: bold;
    }
    .footer {
        background: #343a40;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 14px;
    }

    .btn{
        width: 200px;
        color: white;
        border-radius: 10px;
        background-color: #791a0d;
        border-color:  #6b1b0d;
    }

    .btn:hover {
        background-color: #af3222;
        border-color: #af3222;
    }

    .btn:active {
        background-color: #791a0d;
        border-color:  #6b1b0d;
    }

    table {
        background-color: white;
    }
    canvas {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
        padding: 10px;
    }
</style>
</head>
 
<body>
 
    <!-- Header -->
<div class="header">
<h1>📦 Stock Records Management</h1>
<p>Insightful overview of your product inventory and orders.</p>
</div>
 
    <div class="container container-section">
 
        <!-- 1st Container: Filter -->
<div class="mb-5">
<div class="section-title">📅 Filter by Date</div>
<form class="row g-3 filter-form">
<div class="col-md-5">
<label for="dateFrom" class="form-label">Date From:</label>
<input type="date" id="dateFrom" name="date_from" class="form-control">
</div>
<div class="col-md-5">
<label for="dateTo" class="form-label">Date To:</label>
<input type="date" id="dateTo" name="date_to" class="form-control">
</div>
<div class="col-md-2 d-flex align-items-end">
<button type="submit" class="btn">Filter</button>
</div>
</form>
</div>
 
        <!-- 2nd Container: Summary Statistics -->
<div class="mb-5">
<div class="section-title">📊 Summary Statistics</div>
<div class="row">
<div class="col-md-6">
<div class="card-summary">
<h3>🧾 Total Products</h3>
<p><?php echo $totalProducts; ?></p>
</div>
</div>
<div class="col-md-6">
<div class="card-summary">
<h3>📈 Average Orders</h3>
<p><?php echo $avgOrders; ?> per day</p>
</div>
</div>
</div>
</div>
        <!-- 3rd Container: Product Table -->
<div class="mb-5">
    <div class="section-title">📋 Product List</div>
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Product ID</th>
                    <th>Supplier ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Last Delivery Date</th> <!-- New column -->
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if there are any products
                if ($productResultList->num_rows > 0) {
                    // Output data for each product
                    while ($row = $productResultList->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['productID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['supplierID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['productName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                        echo "<td>₱" . number_format($row['costPrice'], 2) . "</td>";
                        echo "<td>₱" . number_format($row['sellingPrice'], 2) . "</td>";

                        // Check if lastDeliveryDate exists and format it
                        $lastDeliveryDate = $row['lastDeliveryDate'];
                        if ($lastDeliveryDate) {
                            $formattedDate = date('F j, Y', strtotime($lastDeliveryDate));
                            echo "<td>" . htmlspecialchars($formattedDate) . "</td>";
                        } else {
                            echo "<td>Not Available</td>";
                        }

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No products found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>



 
        <!-- Order List Table -->
<!-- Order List Table -->
<div class="mb-5">
    <div class="section-title">🧾 Order List</div>
    <div class="table-responsive">
        <table class="table table-hover table-bordered table-striped">
            <thead class="table-secondary">
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Total Price</th>
                    <th>Order Type</th>
                    <th>Date Ordered</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orderResultList && mysqli_num_rows($orderResultList) > 0): ?>
                    <?php while ($row = $orderResultList->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['orderId']) ?></td>
                            <td><?= htmlspecialchars($row['customerId']) ?></td>
                            <td>₱<?= number_format($row['totalPrice'], 2) ?></td>
                            <td><?= htmlspecialchars($row['orderType']) ?></td>
                            <td><?= htmlspecialchars($row['orderDate']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No orders found for the selected date range.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


 
        <!-- 4th Container: Graphs -->
<div>
<div class="section-title">📉 Product Statistics Graph</div>
<div class="row g-4">
<!-- Pie Chart -->
<div class="col-md-6">
<canvas id="pieChart" height="300"></canvas>
</div>
<!-- Bar Chart -->
<div class="col-md-6">
<canvas id="barChart" height="300"></canvas>
</div>
</div>
</div>
 
    </div>
 
    <!-- Footer -->
<div class="footer">
<p>Shervin Dale Tabernero, BSCS - 2 F-1 | Store Dashboard</p>
</div>
 
    <!-- Bootstrap & Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
    <!-- Charts Script -->
<script>
        // Bar Chart
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($product_names); ?>,
                datasets: [{
                    label: 'Orders',
                    data: <?php echo json_encode($product_orders); ?>,
                    backgroundColor: ['#007bff', '#17a2b8', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
 
        // Pie Chart
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Delivered', 'Pending', 'Cancelled'],
                datasets: [{
                    label: 'Order Status',
                    data: [65, 25, 10],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true
            }
        });
</script>
 
</body>
</html>
