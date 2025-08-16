<?php include APPPATH . 'views/layouts/admin_header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Details - #<?php echo $order['order_number']; ?></h2>
        <a href="<?php echo site_url('admin/orders'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if($item['product_image']): ?>
                                                    <img src="<?php echo base_url('uploads/products/' . $item['product_image']); ?>" 
                                                         alt="<?php echo $item['product_name']; ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" 
                                                         class="me-3">
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo $item['product_name']; ?></strong><br>
                                                    <small class="text-muted"><?php echo $item['product_category']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Subtotal:</th>
                                    <th>₱<?php echo number_format($order['subtotal'], 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3">Shipping Fee:</th>
                                    <th>₱<?php echo number_format($order['shipping_fee'], 2); ?></th>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="3">Total Amount:</th>
                                    <th>₱<?php echo number_format($order['total_amount'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Order Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <select class="form-select" id="orderStatus" onchange="updateOrderStatus()">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">
                            Order Date: <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Customer Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Shipping Address</h5>
                </div>
                <div class="card-body">
                    <address>
                        <?php echo nl2br($order['shipping_address']); ?>
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateOrderStatus() {
    const status = document.getElementById('orderStatus').value;
    const orderId = <?php echo $order['id']; ?>;
    
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
            alert('Order status updated successfully!');
        } else {
            alert('Error updating order status: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error updating order status');
    });
}
</script>

<?php include APPPATH . 'views/layouts/admin_footer.php'; ?>
