<?php include APPPATH . 'views/layouts/admin_header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventory Logs</h2>
        <a href="<?php echo site_url('admin/inventory'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Inventory
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Stock Movement History</h5>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="typeFilter" onchange="filterLogs()">
                            <option value="">All Types</option>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                        <select class="form-select form-select-sm" id="productFilter" onchange="filterLogs()">
                            <option value="">All Products</option>
                            <?php foreach($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Previous Stock</th>
                            <th>New Stock</th>
                            <th>Notes</th>
                            <th>Updated By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($logs)): ?>
                            <?php foreach($logs as $log): ?>
                                <tr>
                                    <td><?php echo date('M d, Y H:i:s', strtotime($log['created_at'])); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if($log['product_image']): ?>
                                                <img src="<?php echo base_url('uploads/products/' . $log['product_image']); ?>" 
                                                     alt="<?php echo $log['product_name']; ?>" 
                                                     style="width: 30px; height: 30px; object-fit: cover; border-radius: 3px;" 
                                                     class="me-2">
                                            <?php endif; ?>
                                            <span><?php echo $log['product_name']; ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php 
                                            echo match($log['type']) {
                                                'in' => 'bg-success',
                                                'out' => 'bg-danger',
                                                'adjustment' => 'bg-warning',
                                                default => 'bg-secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst($log['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($log['type'] == 'in'): ?>
                                            <span class="text-success">+<?php echo $log['quantity']; ?></span>
                                        <?php elseif($log['type'] == 'out'): ?>
                                            <span class="text-danger">-<?php echo $log['quantity']; ?></span>
                                        <?php else: ?>
                                            <span class="text-warning"><?php echo $log['quantity']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $log['previous_stock']; ?></td>
                                    <td><?php echo $log['new_stock']; ?></td>
                                    <td><?php echo $log['notes'] ?: '-'; ?></td>
                                    <td><?php echo $log['updated_by'] ?: 'System'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No inventory logs found.</p>
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
function filterLogs() {
    const type = document.getElementById('typeFilter').value;
    const product = document.getElementById('productFilter').value;
    
    const url = new URL(window.location);
    if(type) {
        url.searchParams.set('type', type);
    } else {
        url.searchParams.delete('type');
    }
    
    if(product) {
        url.searchParams.set('product', product);
    } else {
        url.searchParams.delete('product');
    }
    
    window.location = url;
}

// Set filter values from URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');
    const product = urlParams.get('product');
    
    if(type) {
        document.getElementById('typeFilter').value = type;
    }
    if(product) {
        document.getElementById('productFilter').value = product;
    }
});
</script>

<?php include APPPATH . 'views/layouts/admin_footer.php'; ?>
