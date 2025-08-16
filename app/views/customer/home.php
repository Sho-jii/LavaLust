<?php $this->call->view('layouts/header', $data); ?>

<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Fresh Farm Eggs from Smart Poultry</h1>
                <p class="lead mb-4">Experience the finest quality eggs from our IoT-managed poultry farm. Fresh, nutritious, and delivered with care.</p>
                <a href="<?php echo site_url('/products'); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-basket me-2"></i>Shop Now
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <i class="fas fa-egg" style="font-size: 200px; color: var(--light-teal); opacity: 0.8;"></i>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h2 class="fw-bold">Why Choose Smart Poultry?</h2>
            <p class="text-muted">Our advanced IoT system ensures the highest quality eggs</p>
        </div>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <i class="fas fa-microchip fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">IoT Technology</h5>
                    <p class="card-text">Advanced sensors monitor temperature, humidity, and feeding schedules automatically.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Fresh & Natural</h5>
                    <p class="card-text">Our chickens roam freely and eat natural feed, producing the freshest eggs.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center p-4">
                <div class="card-body">
                    <i class="fas fa-truck fa-3x text-info mb-3"></i>
                    <h5 class="card-title">Fast Delivery</h5>
                    <p class="card-text">Fresh eggs delivered to your doorstep within 24 hours of collection.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 text-center mb-4">
            <h2 class="fw-bold">Featured Products</h2>
            <p class="text-muted">Choose from our premium selection of fresh eggs</p>
        </div>
    </div>
    
    <div class="row g-4">
        <?php if (!empty($featured_products)): ?>
            <?php foreach (array_slice($featured_products, 0, 6) as $product): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card product-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold"><?php echo html_escape($product['name']); ?></h5>
                                <span class="stock-badge">
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
                                <a href="<?php echo site_url('/product/' . $product['id']); ?>" class="btn btn-primary">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-muted">No products available at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-4">
        <a href="<?php echo site_url('/products'); ?>" class="btn btn-outline-primary btn-lg">
            View All Products <i class="fas fa-arrow-right ms-2"></i>
        </a>
    </div>
</div>

<?php $this->call->view('layouts/footer'); ?>
