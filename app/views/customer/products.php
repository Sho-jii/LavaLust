<?php $this->call->view('layouts/header', $data); ?>

<div class="container my-5">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="fw-bold">Our Premium Egg Collection</h1>
            <p class="text-muted">Fresh from our smart poultry farm to your table</p>
        </div>
    </div>
    
    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card product-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold"><?php echo html_escape($product['name']); ?></h5>
                                <span class="stock-badge <?php echo $product['stock_quantity'] <= 10 ? 'bg-warning' : 'bg-success'; ?>">
                                    <?php echo $product['stock_quantity']; ?> in stock
                                </span>
                            </div>
                            
                            <p class="card-text text-muted mb-3">
                                <?php echo html_escape($product['description']); ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?></span>
                                <span class="badge bg-info"><?php echo ucfirst(str_replace('_', ' ', $product['packaging'])); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price-tag">₱<?php echo number_format($product['price'], 2); ?></span>
                                <div>
                                    <a href="<?php echo site_url('/product/' . $product['id']); ?>" class="btn btn-outline-primary btn-sm me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($product['stock_quantity'] > 0): ?>
                                        <form method="POST" action="<?php echo site_url('/cart/add'); ?>" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fas fa-times"></i> Out of Stock
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <div class="card p-5">
                    <i class="fas fa-egg fa-4x text-muted mb-3"></i>
                    <h3>No Products Available</h3>
                    <p class="text-muted">Please check back later for fresh eggs!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->call->view('layouts/footer'); ?>
