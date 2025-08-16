<?php include APPPATH . 'views/layouts/admin_header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Bulk Stock Update</h2>
        <a href="<?php echo site_url('admin/inventory'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Update Multiple Product Stock Levels</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Instructions:</strong> Enter new stock quantities for products you want to update. Leave fields empty for products you don't want to change.
            </div>

            <form method="POST" action="<?php echo site_url('admin/inventory/process_bulk_update'); ?>">
                <?php echo csrf_field(); ?>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Update Notes</label>
                    <input type="text" class="form-control" id="notes" name="notes" 
                           placeholder="Reason for bulk update (e.g., Weekly inventory count, Supplier delivery)">
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>New Stock</th>
                                <th>Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if($product['image']): ?>
                                                <img src="<?php echo base_url('uploads/products/' . $product['image']); ?>" 
                                                     alt="<?php echo $product['name']; ?>" 
                                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;" 
                                                     class="me-2">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?php echo html_escape($product['name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo ucfirst($product['unit']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $product['category'])); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $product['stock_quantity'] > 10 ? 'bg-success' : 
                                                ($product['stock_quantity'] > 0 ? 'bg-warning' : 'bg-danger'); 
                                        ?>">
                                            <?php echo $product['stock_quantity']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               class="form-control stock-input" 
                                               name="updates[<?php echo $product['id']; ?>][new_stock]" 
                                               min="0" 
                                               placeholder="<?php echo $product['stock_quantity']; ?>"
                                               data-current="<?php echo $product['stock_quantity']; ?>"
                                               data-product-id="<?php echo $product['id']; ?>">
                                    </td>
                                    <td>
                                        <span class="change-indicator" id="change-<?php echo $product['id']; ?>">
                                            -
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <span class="text-muted">Products to update: <span id="update-count">0</span></span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="clearAll()">
                            Clear All
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            <i class="fas fa-save me-2"></i>Update Stock Levels
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockInputs = document.querySelectorAll('.stock-input');
    const submitBtn = document.getElementById('submit-btn');
    const updateCount = document.getElementById('update-count');
    
    stockInputs.forEach(input => {
        input.addEventListener('input', function() {
            updateChangeIndicator(this);
            updateSubmitButton();
        });
    });
    
    function updateChangeIndicator(input) {
        const productId = input.dataset.productId;
        const currentStock = parseInt(input.dataset.current);
        const newStock = parseInt(input.value);
        const changeElement = document.getElementById('change-' + productId);
        
        if (input.value === '' || isNaN(newStock)) {
            changeElement.textContent = '-';
            changeElement.className = 'change-indicator';
            return;
        }
        
        const difference = newStock - currentStock;
        
        if (difference === 0) {
            changeElement.textContent = 'No change';
            changeElement.className = 'change-indicator text-muted';
        } else if (difference > 0) {
            changeElement.textContent = '+' + difference;
            changeElement.className = 'change-indicator text-success fw-bold';
        } else {
            changeElement.textContent = difference.toString();
            changeElement.className = 'change-indicator text-danger fw-bold';
        }
    }
    
    function updateSubmitButton() {
        let hasChanges = false;
        let changeCount = 0;
        
        stockInputs.forEach(input => {
            if (input.value !== '' && !isNaN(parseInt(input.value))) {
                const currentStock = parseInt(input.dataset.current);
                const newStock = parseInt(input.value);
                if (newStock !== currentStock) {
                    hasChanges = true;
                    changeCount++;
                }
            }
        });
        
        submitBtn.disabled = !hasChanges;
        updateCount.textContent = changeCount;
    }
    
    window.clearAll = function() {
        stockInputs.forEach(input => {
            input.value = '';
            updateChangeIndicator(input);
        });
        updateSubmitButton();
    };
});
</script>

<?php include APPPATH . 'views/layouts/admin_footer.php'; ?>
