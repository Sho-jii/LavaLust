<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Products extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_admin_auth();
        $this->call->model('Product_model');
        $this->call->model('Inventory_model');
        $this->call->library('form_validation');
        $this->call->library('upload');
    }
    
    public function index() {
        $data['title'] = 'Manage Products - Admin';
        $data['products'] = $this->Product_model->get_all_products();
        
        $this->call->view('admin/products/index', $data);
    }
    
    public function create() {
        $data['title'] = 'Add New Product - Admin';
        $this->call->view('admin/products/create', $data);
    }
    
    public function store() {
        $this->form_validation
            ->name('name')->required()->min_length(3)
            ->name('category')->required()
            ->name('unit')->required()
            ->name('price')->required()->numeric()
            ->name('stock_quantity')->required()->numeric();
        
        if ($this->form_validation->run() == FALSE) {
            $data['errors'] = $this->form_validation->get_errors();
            $data['title'] = 'Add New Product - Admin';
            $this->call->view('admin/products/create', $data);
            return;
        }
        
        // Handle image upload
        $image_name = null;
        if (!empty($_FILES['image']['name'])) {
            $image_name = $this->handle_image_upload();
            if (!$image_name) {
                $data['errors'] = ['Image upload failed. Please try again.'];
                $data['title'] = 'Add New Product - Admin';
                $this->call->view('admin/products/create', $data);
                return;
            }
        }
        
        $product_data = [
            'name' => $this->io->post('name'),
            'description' => $this->io->post('description'),
            'category' => $this->io->post('category'),
            'unit' => $this->io->post('unit'),
            'price' => $this->io->post('price'),
            'stock_quantity' => $this->io->post('stock_quantity'),
            'status' => $this->io->post('status') ?: 'active',
            'featured' => $this->io->post('featured') ? 1 : 0,
            'image' => $image_name,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $product_id = $this->Product_model->create_product($product_data);
        
        if ($product_id) {
            // Log initial stock
            $this->Inventory_model->log_stock_change(
                $product_id,
                'in',
                $product_data['stock_quantity'],
                0,
                $product_data['stock_quantity'],
                'Initial stock - Product created',
                $_SESSION['user_id']
            );
            
            $_SESSION['success'] = 'Product added successfully';
        } else {
            $_SESSION['error'] = 'Failed to add product';
        }
        
        redirect('admin/products');
    }
    
    public function edit($id) {
        $product = $this->db->table('products')->where('id', $id)->get();
        
        if (!$product) {
            $_SESSION['error'] = 'Product not found';
            redirect('admin/products');
            return;
        }
        
        $data['title'] = 'Edit Product - Admin';
        $data['product'] = $product;
        
        $this->call->view('admin/products/edit', $data);
    }
    
    public function update($id) {
        $product = $this->db->table('products')->where('id', $id)->get();
        
        if (!$product) {
            $_SESSION['error'] = 'Product not found';
            redirect('admin/products');
            return;
        }
        
        $this->form_validation
            ->name('name')->required()->min_length(3)
            ->name('category')->required()
            ->name('unit')->required()
            ->name('price')->required()->numeric()
            ->name('stock_quantity')->required()->numeric();
        
        if ($this->form_validation->run() == FALSE) {
            $data['errors'] = $this->form_validation->get_errors();
            $data['title'] = 'Edit Product - Admin';
            $data['product'] = $product;
            $this->call->view('admin/products/edit', $data);
            return;
        }
        
        $old_stock = $product['stock_quantity'];
        $new_stock = $this->io->post('stock_quantity');
        
        // Handle image upload
        $image_name = $product['image']; // Keep existing image by default
        if (!empty($_FILES['image']['name'])) {
            $new_image = $this->handle_image_upload();
            if ($new_image) {
                // Delete old image if exists
                if ($product['image'] && file_exists('uploads/products/' . $product['image'])) {
                    unlink('uploads/products/' . $product['image']);
                }
                $image_name = $new_image;
            }
        }
        
        $product_data = [
            'name' => $this->io->post('name'),
            'description' => $this->io->post('description'),
            'category' => $this->io->post('category'),
            'unit' => $this->io->post('unit'),
            'price' => $this->io->post('price'),
            'stock_quantity' => $new_stock,
            'status' => $this->io->post('status'),
            'featured' => $this->io->post('featured') ? 1 : 0,
            'image' => $image_name,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $updated = $this->Product_model->update_product($id, $product_data);
        
        if ($updated) {
            // Log stock change if different
            if ($old_stock != $new_stock) {
                $action = $new_stock > $old_stock ? 'in' : 'out';
                $quantity_change = abs($new_stock - $old_stock);
                
                $this->Inventory_model->log_stock_change(
                    $id,
                    $action,
                    $quantity_change,
                    $old_stock,
                    $new_stock,
                    'Manual stock update via product edit',
                    $_SESSION['user_id']
                );
            }
            
            $_SESSION['success'] = 'Product updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update product';
        }
        
        redirect('admin/products');
    }
    
    public function delete($id) {
        $product = $this->db->table('products')->where('id', $id)->get();
        
        if (!$product) {
            $this->io->set_status_code(404);
            $this->io->send_json(['success' => false, 'message' => 'Product not found']);
            return;
        }
        
        // Check if product has orders
        $has_orders = $this->db->table('order_items')->where('product_id', $id)->get();
        
        if ($has_orders) {
            // Don't delete, just deactivate
            $this->db->table('products')->where('id', $id)->update([
                'status' => 'inactive',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $this->io->send_json(['success' => true, 'message' => 'Product deactivated successfully']);
        } else {
            // Delete image if exists
            if ($product['image'] && file_exists('uploads/products/' . $product['image'])) {
                unlink('uploads/products/' . $product['image']);
            }
            
            $deleted = $this->Product_model->delete_product($id);
            
            if ($deleted) {
                $this->io->send_json(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                $this->io->send_json(['success' => false, 'message' => 'Failed to delete product']);
            }
        }
    }
    
    private function handle_image_upload() {
        // Create uploads directory if it doesn't exist
        if (!is_dir('uploads/products')) {
            mkdir('uploads/products', 0755, true);
        }
        
        $config = [
            'upload_path' => 'uploads/products/',
            'allowed_types' => 'gif|jpg|jpeg|png',
            'max_size' => 2048, // 2MB
            'encrypt_name' => true
        ];
        
        $this->upload->initialize($config);
        
        if ($this->upload->do_upload('image')) {
            $upload_data = $this->upload->data();
            return $upload_data['file_name'];
        }
        
        return false;
    }
    
    private function check_admin_auth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            redirect('login');
            exit;
        }
    }
}
