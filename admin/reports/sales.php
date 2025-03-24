<?php
require_once '../config.php';
require_once 'report-utils.php';
checkAdminAuth();

$conn = getConnection();
$error = null;

try {
    // get date range from request, default to last 30 days
    $endDate = date('Y-m-d');
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : $endDate;

    // get daily sales data
    $stmt = $conn->prepare("
        SELECT 
            DATE(o.created_at) as date,
            COUNT(DISTINCT o.order_id) as order_count,
            SUM(oi.quantity) as items_sold,
            SUM(oi.quantity * oi.price) as revenue
        FROM Orders o
        JOIN Order_Items oi ON o.order_id = oi.order_id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status != 'cancelled'
        GROUP BY DATE(o.created_at)
        ORDER BY date
    ");
    $stmt->execute([$startDate, $endDate]);
    $dailySales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // handle export requests
    if (isset($_GET['export'])) {
        $exportData = formatDataForExport($dailySales, 'sales');
        $headers = getExportHeaders('sales');
        $filename = 'sales_analysis_' . date('Y-m-d');
        
        if ($_GET['export'] === 'csv') {
            exportToCSV($exportData, $headers, $filename);
        } elseif ($_GET['export'] === 'pdf') {
            exportToPDF($exportData, $headers, 'Sales Analysis Report', $filename);
        }
    }

    // get product performance
    $stmt = $conn->prepare("
        SELECT 
            p.name as product_name,
            c.name as category_name,
            COUNT(DISTINCT o.order_id) as order_count,
            SUM(oi.quantity) as quantity_sold,
            SUM(oi.quantity * oi.price) as revenue
        FROM Order_Items oi
        JOIN Orders o ON oi.order_id = o.order_id
        JOIN Products p ON oi.product_id = p.product_id
        JOIN Category c ON p.category_id = c.category_id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status != 'cancelled'
        GROUP BY p.product_id
        ORDER BY revenue DESC
        LIMIT 10
    ");
    $stmt->execute([$startDate, $endDate]);
    $productStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // get category performance
    $stmt = $conn->prepare("
        SELECT 
            c.name as category_name,
            COUNT(DISTINCT o.order_id) as order_count,
            SUM(oi.quantity) as quantity_sold,
            SUM(oi.quantity * oi.price) as revenue
        FROM Order_Items oi
        JOIN Orders o ON oi.order_id = o.order_id
        JOIN Products p ON oi.product_id = p.product_id
        JOIN Category c ON p.category_id = c.category_id
        WHERE DATE(o.created_at) BETWEEN ? AND ?
        AND o.status != 'cancelled'
        GROUP BY c.category_id
        ORDER BY revenue DESC
    ");
    $stmt->execute([$startDate, $endDate]);
    $categoryStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // calculate the totals
    $totalRevenue = array_sum(array_column($dailySales, 'revenue'));
    $totalOrders = array_sum(array_column($dailySales, 'order_count'));
    $totalItems = array_sum(array_column($dailySales, 'items_sold'));
    $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

include '../templates/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include '../templates/admin-menu.php'; ?>
        </div>
        
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="mb-0">Sales Analysis Report</h4>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>">
                                        Export to CSV
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>">
                                        Export to PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <form class="d-flex gap-2">
                        <input type="date" class="form-control" name="start_date" value="<?= $startDate ?>">
                        <input type="date" class="form-control" name="end_date" value="<?= $endDate ?>">
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <!-- summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="text-uppercase mb-2">Total Revenue</h6>
                                    <h3 class="mb-0">£<?= number_format($totalRevenue, 2) ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="text-uppercase mb-2">Total Orders</h6>
                                    <h3 class="mb-0"><?= number_format($totalOrders) ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="text-uppercase mb-2">Items Sold</h6>
                                    <h3 class="mb-0"><?= number_format($totalItems) ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="text-uppercase mb-2">Avg. Order Value</h6>
                                    <h3 class="mb-0">£<?= number_format($avgOrderValue, 2) ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- charts -->
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Daily Sales Trend</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Category Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- top products table -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Top Performing Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th>Orders</th>
                                            <th>Units Sold</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($productStats as $product): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($product['product_name']) ?></td>
                                                <td><?= htmlspecialchars($product['category_name']) ?></td>
                                                <td><?= number_format($product['order_count']) ?></td>
                                                <td><?= number_format($product['quantity_sold']) ?></td>
                                                <td>£<?= number_format($product['revenue'], 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        
                                        <?php if (empty($productStats)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No data available for the selected period.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // sales trend chart
    new Chart(document.getElementById('salesTrendChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($dailySales, 'date')) ?>,
            datasets: [{
                label: 'Revenue',
                data: <?= json_encode(array_column($dailySales, 'revenue')) ?>,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                yAxisID: 'y'
            }, {
                label: 'Orders',
                data: <?= json_encode(array_column($dailySales, 'order_count')) ?>,
                borderColor: 'rgb(255, 99, 132)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Revenue (£)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Number of Orders'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // category chart
    new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($categoryStats, 'category_name')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($categoryStats, 'revenue')) ?>,
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)',
                    'rgb(255, 159, 64)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>

<?php include '../templates/footer.php'; ?>
