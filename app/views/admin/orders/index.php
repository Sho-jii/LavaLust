<?php include APPPATH . 'views/layouts/admin_header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Orders Management</h2>
        <div class="d-flex gap-2">
            <select class="form-select" id="statusFilter" onchange="filterOrders()">
                <option value="">All Orders</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Orders</h6>
                            <h3><?php echo $stats['total_orders'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Pending Orders</h6>
                            <h3><?php echo $stats['pending_orders'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Completed Orders</h6>
                            <h3><?php echo $stats['completed_orders'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Revenue</h6>
                            <h3>₱<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h3>
                        </div>
                        <i class="fas fa-peso-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($orders)): ?>
                            <?php foreach($orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo $order['order_number']; ?></strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo $order['customer_name']; ?></strong><br>
                                            <small class="text-muted"><?php echo $order['customer_email']; ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo $order['total_items']; ?> items</td>
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
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo site_url('admin/orders/view/' . $order['id']); ?>" 
                                               class="btn btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $order['id']; ?>, 'pending')">Mark as Pending</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $order['id']; ?>, 'processing')">Mark as Processing</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $order['id']; ?>, 'shipped')">Mark as Shipped</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $order['id']; ?>, 'delivered')">Mark as Delivered</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="updateStatus(<?php echo $order['id']; ?>, 'cancelled')">Cancel Order</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No orders found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(orderId, status) {
    if(confirm(`Are you sure you want to update this order status to "${status}"?`)) {
        fetch(`<?php echo site_url('admin/orders/update-status/'); ?>${orderId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Error updating order status: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error updating order status');
        });
    }
}

function filterOrders() {
    const status = document.getElementById('statusFilter').value;
    const url = new URL(window.location);
    if(status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location = url;
}
</script>

<?php include APPPATH . 'views/layouts/admin_footer.php'; ?>
