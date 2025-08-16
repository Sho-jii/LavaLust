<?php include APPPATH . 'views/layouts/admin_header.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Inventory Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStockModal">
            <i class="fas fa-plus"></i> Update Stock
        </button>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">In Stock</h6>
                            <h3><?php echo $stats['in_stock'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Low Stock</h6>
                            <h3><?php echo $stats['low_stock'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Out of Stock</h6>
                            <h3><?php echo $stats['out_of_stock'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Products</h6>
                            <h3><?php echo $stats['total_products'] ?? 0; ?></h3>
                        </div>
                        <i class="fas fa-cube fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5>Current Stock Levels</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($products)): ?>
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
                                            <strong><?php echo $product['name']; ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo $product['category']; ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            echo $product['stock_quantity'] > 10 ? 'bg-success' : 
                                                ($product['stock_quantity'] > 0 ? 'bg-warning' : 'bg-danger'); 
                                        ?>">
                                            <?php echo $product['stock_quantity']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo ucfirst($product['unit']); ?></td>
                                    <td>
                                        <?php if($product['stock_quantity'] > 10): ?>
                                            <span class="text-success">In Stock</span>
                                        <?php elseif($product['stock_quantity'] > 0): ?>
                                            <span class="text-warning">Low Stock</span>
                                        <?php else: ?>
                                            <span class="text-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($product['updated_at'])); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="openUpdateModal(<?php echo $product['id']; ?>, '<?php echo $product['name']; ?>', <?php echo $product['stock_quantity']; ?>)">
                                            <i class="fas fa-edit"></i> Update
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No products found.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Recent Stock Updates</h5>
            <a href="<?php echo site_url('admin/inventory/logs'); ?>" class="btn btn-sm btn-outline-primary">View All Logs</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($recent_logs)): ?>
                            <?php foreach($recent_logs as $log): ?>
                                <tr>
                                    <td><?php echo date('M d, H:i', strtotime($log['created_at'])); ?></td>
                                    <td><?php echo $log['product_name']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $log['type'] == 'in' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ucfirst($log['type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $log['quantity']; ?></td>
                                    <td><?php echo $log['notes'] ?: '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-3">
                                    <span class="text-muted">No recent stock updates</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStockForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product</label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            <?php foreach($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Update Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="in">Stock In (Add)</option>
                            <option value="out">Stock Out (Remove)</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUpdateModal(productId, productName, currentStock) {
    document.getElementById('product_id').value = productId;
    document.getElementById('quantity').value = '';
    document.getElementById('notes').value = '';
    new bootstrap.Modal(document.getElementById('updateStockModal')).show();
}

document.getElementById('updateStockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?php echo site_url('admin/inventory/update-stock'); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo csrf_token(); ?>'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Stock updated successfully!');
            location.reload();
        } else {
            alert('Error updating stock: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error updating stock');
    });
});
</script>

<?php include APPPATH . 'views/layouts/admin_footer.php'; ?>
