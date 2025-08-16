<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Orders extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_admin_auth();
        $this->call->model('Order_model');
    }
    
    public function index() {
        $status = $this->io->get('status');
        
        $data['title'] = 'Manage Orders - Admin';
        $data['orders'] = $this->Order_model->get_orders_by_status($status);
        $data['current_status'] = $status;
        
        // Get order statistics
        $data['stats'] = $this->Order_model->get_sales_summary();
        
        $this->call->view('admin/orders/index', $data);
    }
    
    public function view($id) {
        $order = $this->Order_model->get_order_with_items($id);
        
        if (!$order) {
            $_SESSION['error'] = 'Order not found';
            redirect('admin/orders');
            return;
        }
        
        $data['title'] = 'Order Details - Admin';
        $data['order'] = $order;
        
        // Get order items with product details
        $data['order_items'] = $this->db->table('order_items oi')
                                       ->join('products p', 'p.id = oi.product_id')
                                       ->select('oi.*, p.name as product_name, p.category as product_category, p.image as product_image')
                                       ->where('oi.order_id', $id)
                                       ->get_all();
        
        $this->call->view('admin/orders/view', $data);
    }
    
    public function update_status($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? null;
        
        $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $valid_statuses)) {
            $this->io->set_status_code(400);
            $this->io->send_json(['success' => false, 'message' => 'Invalid status']);
            return;
        }
        
        $updated = $this->Order_model->update_status($id, $status);
        
        if ($updated) {
            $this->io->send_json(['success' => true, 'message' => 'Order status updated successfully']);
        } else {
            $this->io->send_json(['success' => false, 'message' => 'Failed to update order status']);
        }
    }
    
    private function check_admin_auth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            redirect('login');
            exit;
        }
    }
}
