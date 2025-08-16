<?php $this->call->view('layouts/header', $data); ?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <h1 class="fw-bold mb-4">
                <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
            </h1>
        </div>
    </div>
    
    <?php if (!empty($cart_items)): ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="row align-items-center border-bottom py-3">
                                <div class="col-md-6">
                                    <h5 class="fw-bold"><?php echo html_escape($item['product']['name']); ?></h5>
                                    <p class="text-muted mb-1"><?php echo html_escape($item['product']['description']); ?></p>
                                    <small class="text-muted">
                                        <?php echo ucfirst(str_replace('_', ' ', $item['product']['category'])); ?> - 
                                        <?php echo ucfirst(str_replace('_', ' ', $item['product']['packaging'])); ?>
                                    </small>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="fw-bold">₱<?php echo number_format($item['product']['price'], 2); ?></span>
                                </div>
                                <div class="col-md-2">
                                    <form method="POST" action="<?php echo site_url('/cart/update'); ?>" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                        <input type="number" class="form-control form-control-sm" name="quantity" 
                                               value="<?php echo $item['quantity']; ?>" min="1" 
                                               max="<?php echo $item['product']['stock_quantity']; ?>"
                                               onchange="this.form.submit()">
                                    </form>
                                </div>
                                <div class="col-md-1 text-center">
                                    <span class="fw-bold">₱<?php echo number_format($item['subtotal'], 2); ?></span>
                                </div>
                                <div class="col-md-1 text-center">
                                    <form method="POST" action="<?php echo site_url('/cart/remove'); ?>" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span class="fw-bold">₱<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Delivery Fee:</span>
                            <span class="text-success">FREE</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold fs-5">₱<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="<?php echo site_url('/checkout'); ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                            <a href="<?php echo site_url('/products'); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-12 text-center">
                <div class="card p-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h3>Your cart is empty</h3>
                    <p class="text-muted mb-4">Add some fresh eggs to get started!</p>
                    <a href="<?php echo site_url('/products'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-basket me-2"></i>Shop Now
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $this->call->view('layouts/footer'); ?>
