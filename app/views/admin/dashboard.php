<?php $this->call->view('layouts/admin_header', $data); ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">
            <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
        </h1>
        <div class="text-muted">
            <i class="fas fa-calendar me-1"></i><?php echo date('F d, Y'); ?>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold">₱<?php echo number_format($sales_summary['total_sales'], 2); ?></h3>
                            <p class="mb-0">Total Sales</p>
                        </div>
                        <i class="fas fa-peso-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold">₱<?php echo number_format($today_sales, 2); ?></h3>
                            <p class="mb-0">Today's Sales</p>
                        </div>
                        <i class="fas fa-chart-line fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold"><?php echo $sales_summary['pending_orders']; ?></h3>
                            <p class="mb-0">Pending Orders</p>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="fw-bold"><?php echo $stock_statistics['low_stock'] + $stock_statistics['out_of_stock']; ?></h3>
                            <p class="mb-0">Stock Alerts</p>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Orders</h5>
                    <a href="<?php echo site_url('admin/orders'); ?>" class="btn btn-outline-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_orders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recent_orders, 0, 8) as $order): ?>
                                        <tr>
                                            <td><strong>#<?php echo $order['order_number']; ?></strong></td>
                                            <td><?php echo html_escape($order['customer_name']); ?></td>
                                            <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge <?php 
                                                    echo match($order['status']) {
                                                        'pending' => 'bg-warning',
                                                        'processing' => 'bg-info',
                                                        'shipped' => 'bg-primary',
                                                        'delivered' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, H:i', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <a href="<?php echo site_url('admin/orders/view/' . $order['id']); ?>" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No orders yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Stock Overview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Stock Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <h4 class="text-success"><?php echo $stock_statistics['in_stock']; ?></h4>
                                <small class="text-muted">In Stock</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-warning"><?php echo $stock_statistics['low_stock']; ?></h4>
                            <small class="text-muted">Low Stock</small>
                        </div>
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-danger"><?php echo $stock_statistics['out_of_stock']; ?></h4>
                                <small class="text-muted">Out of Stock</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-primary"><?php echo $stock_statistics['total_products']; ?></h4>
                            <small class="text-muted">Total Products</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Low Stock Alert -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Low Stock Alert</h5>
                    <a href="<?php echo site_url('admin/inventory'); ?>" class="btn btn-outline-warning btn-sm">Manage</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($low_stock_products)): ?>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <?php foreach (array_slice($low_stock_products, 0, 10) as $product): ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong><?php echo html_escape($product['name']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?></small>
                                    </div>
                                    <span class="badge <?php echo $product['stock_quantity'] <= 5 ? 'bg-danger' : 'bg-warning'; ?>">
                                        <?php echo $product['stock_quantity']; ?> left
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">All products well stocked</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo site_url('admin/products/create'); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Product
                        </a>
                        <a href="<?php echo site_url('admin/orders?status=pending'); ?>" class="btn btn-warning">
                            <i class="fas fa-clock me-2"></i>View Pending Orders
                        </a>
                        <a href="<?php echo site_url('admin/inventory'); ?>" class="btn btn-info">
                            <i class="fas fa-boxes me-2"></i>Update Inventory
                        </a>
                        <a href="<?php echo site_url('admin/products'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Manage Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->call->view('layouts/admin_footer'); ?>
