<?php $this->call->view('layouts/header', $data); ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-4x text-success"></i>
                    </div>
                    
                    <h1 class="fw-bold text-success mb-3">Order Confirmed!</h1>
                    <p class="lead mb-4">Thank you for your order. We'll prepare your fresh eggs right away!</p>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Order Details</h5>
                            
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <p><strong>Order Number:</strong> <?php echo $order['order_number']; ?></p>
                                    <p><strong>Customer:</strong> <?php echo html_escape($order['customer_name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo html_escape($order['customer_email']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo html_escape($order['customer_phone']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-warning"><?php echo ucfirst($order['status']); ?></span>
                                    </p>
                                    <p><strong>Total Amount:</strong> 
                                        <span class="fw-bold text-primary">₱<?php echo number_format($order['total_amount'], 2); ?></span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <p><strong>Delivery Address:</strong></p>
                                <p class="text-muted"><?php echo nl2br(html_escape($order['delivery_address'])); ?></p>
                            </div>
                            
                            <?php if (!empty($order['notes'])): ?>
                                <div class="mt-3">
                                    <p><strong>Order Notes:</strong></p>
                                    <p class="text-muted"><?php echo nl2br(html_escape($order['notes'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Ordered Items</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($order['items'] as $item): ?>
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <div>
                                        <strong><?php echo html_escape($item['product_name']); ?></strong>
                                        <br>
                                        <small class="text-muted">Quantity: <?php echo $item['quantity']; ?> × ₱<?php echo number_format($item['unit_price'], 2); ?></small>
                                    </div>
                                    <span class="fw-bold">₱<?php echo number_format($item['total_price'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>What's Next?</strong><br>
                        We'll process your order and contact you within 24 hours with delivery details.
                        Your fresh eggs will be delivered within 1-2 business days.
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="<?php echo site_url('/'); ?>" class="btn btn-primary btn-lg me-md-2">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                        <a href="<?php echo site_url('/products'); ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-shopping-basket me-2"></i>Shop More
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->call->view('layouts/footer'); ?>
