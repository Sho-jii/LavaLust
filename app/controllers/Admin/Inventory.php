<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Inventory extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_admin_auth();
        $this->call->model('Product_model');
        $this->call->model('Inventory_model');
    }
    
    public function index() {
        $data['title'] = 'Inventory Management - Admin';
        $data['products'] = $this->db->table('products')->where('status', 'active')->order_by('name', 'ASC')->get_all();
        
        // Get inventory statistics
        $data['stats'] = $this->Product_model->get_stock_statistics();
        
        // Get recent inventory logs
        $data['recent_logs'] = $this->Inventory_model->get_logs_with_products(10);
        
        $this->call->view('admin/inventory/index', $data);
    }
    
    public function update_stock() {
        $product_id = $this->io->post('product_id');
        $type = $this->io->post('type'); // 'in', 'out', 'adjustment'
        $quantity = (int)$this->io->post('quantity');
        $notes = $this->io->post('notes');
        
        if (!$product_id || !$type || $quantity <= 0) {
            $this->io->send_json(['success' => false, 'message' => 'Invalid input data']);
            return;
        }
        
        $product = $this->db->table('products')->where('id', $product_id)->get();
        
        if (!$product) {
            $this->io->send_json(['success' => false, 'message' => 'Product not found']);
            return;
        }
        
        $old_stock = $product['stock_quantity'];
        $new_stock = $old_stock;
        
        switch ($type) {
            case 'in':
                $new_stock = $old_stock + $quantity;
                break;
            case 'out':
                $new_stock = max(0, $old_stock - $quantity);
                if ($new_stock < 0) {
                    $this->io->send_json(['success' => false, 'message' => 'Insufficient stock']);
                    return;
                }
                break;
            case 'adjustment':
                $new_stock = $quantity;
                break;
            default:
                $this->io->send_json(['success' => false, 'message' => 'Invalid stock operation type']);
                return;
        }
        
        // Update product stock
        $updated = $this->Product_model->update_stock($product_id, $new_stock);
        
        if ($updated) {
            // Log the change
            $quantity_change = $type === 'adjustment' ? abs($new_stock - $old_stock) : $quantity;
            $this->Inventory_model->log_stock_change(
                $product_id,
                $type,
                $quantity_change,
                $old_stock,
                $new_stock,
                $notes ?: ucfirst($type) . ' stock operation',
                $_SESSION['user_id']
            );
            
            $this->io->send_json(['success' => true, 'message' => 'Stock updated successfully']);
        } else {
            $this->io->send_json(['success' => false, 'message' => 'Failed to update stock']);
        }
    }
    
    public function logs() {
        $type_filter = $this->io->get('type');
        $product_filter = $this->io->get('product');
        
        $data['title'] = 'Inventory Logs - Admin';
        
        // Build query with filters
        $query = $this->db->table('inventory_logs il')
                         ->join('products p', 'p.id = il.product_id')
                         ->left_join('users u', 'u.id = il.created_by')
                         ->select('il.*, p.name as product_name, p.image as product_image, u.first_name, u.last_name');
        
        if ($type_filter) {
            $query->where('il.type', $type_filter);
        }
        
        if ($product_filter) {
            $query->where('il.product_id', $product_filter);
        }
        
        $data['logs'] = $query->order_by('il.created_at', 'DESC')->get_all();
        $data['products'] = $this->db->table('products')->where('status', 'active')->order_by('name', 'ASC')->get_all();
        
        $this->call->view('admin/inventory/logs', $data);
    }
    
    public function bulk_update() {
        $data['title'] = 'Bulk Stock Update - Admin';
        $data['products'] = $this->db->table('products')->where('status', 'active')->order_by('name', 'ASC')->get_all();
        
        $this->call->view('admin/inventory/bulk_update', $data);
    }
    
    public function process_bulk_update() {
        $updates = $this->io->post('updates');
        $notes = $this->io->post('notes') ?: 'Bulk stock update';
        
        if (!$updates || !is_array($updates)) {
            $_SESSION['error'] = 'No updates provided';
            redirect('admin/inventory/bulk_update');
            return;
        }
        
        $success_count = 0;
        $error_count = 0;
        
        foreach ($updates as $product_id => $update_data) {
            if (!isset($update_data['new_stock']) || $update_data['new_stock'] === '') {
                continue;
            }
            
            $new_stock = (int)$update_data['new_stock'];
            $product = $this->db->table('products')->where('id', $product_id)->get();
            
            if (!$product) {
                $error_count++;
                continue;
            }
            
            $old_stock = $product['stock_quantity'];
            
            if ($old_stock != $new_stock) {
                $updated = $this->Product_model->update_stock($product_id, $new_stock);
                
                if ($updated) {
                    // Log the change
                    $type = $new_stock > $old_stock ? 'in' : 'out';
                    $quantity_change = abs($new_stock - $old_stock);
                    
                    $this->Inventory_model->log_stock_change(
                        $product_id,
                        'adjustment',
                        $quantity_change,
                        $old_stock,
                        $new_stock,
                        $notes,
                        $_SESSION['user_id']
                    );
                    
                    $success_count++;
                } else {
                    $error_count++;
                }
            }
        }
        
        if ($success_count > 0) {
            $_SESSION['success'] = "Successfully updated {$success_count} products";
        }
        
        if ($error_count > 0) {
            $_SESSION['error'] = "Failed to update {$error_count} products";
        }
        
        redirect('admin/inventory');
    }
    
    private function check_admin_auth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            redirect('login');
            exit;
        }
    }
}
