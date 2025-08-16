<?php $this->call->view('layouts/header', $data); ?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body text-center p-5">
                    <i class="fas fa-egg" style="font-size: 150px; color: var(--primary-teal);"></i>
                    <h3 class="mt-3"><?php echo html_escape($product['name']); ?></h3>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body p-4">
                    <h1 class="fw-bold mb-3"><?php echo html_escape($product['name']); ?></h1>
                    
                    <div class="mb-4">
                        <span class="price-tag fs-3">₱<?php echo number_format($product['price'], 2); ?></span>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <strong>Category:</strong><br>
                            <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?></span>
                        </div>
                        <div class="col-6">
                            <strong>Packaging:</strong><br>
                            <span class="badge bg-info"><?php echo ucfirst(str_replace('_', ' ', $product['packaging'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <strong>Stock Available:</strong>
                        <span class="stock-badge <?php echo $product['stock_quantity'] <= 10 ? 'bg-warning' : 'bg-success'; ?>">
                            <?php echo $product['stock_quantity']; ?> units
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <strong>Description:</strong>
                        <p class="text-muted mt-2"><?php echo html_escape($product['description']); ?></p>
                    </div>
                    
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <form method="POST" action="<?php echo site_url('/cart/add'); ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            
                            <div class="row mb-3">
                                <div class="col-4">
                                    <label for="quantity" class="form-label">Quantity:</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" 
                                           value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" required>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-primary btn-lg me-md-2">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="<?php echo site_url('/products'); ?>" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Products
                                </a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This product is currently out of stock.
                        </div>
                        <a href="<?php echo site_url('/products'); ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Back to Products
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->call->view('layouts/footer'); ?>
