<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Dashboard extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->check_admin_auth();
        $this->call->model('Order_model');
        $this->call->model('Product_model');
    }
    
    public function index() {
        $data['title'] = 'Admin Dashboard - Smart Poultry';
        
        $data['sales_summary'] = $this->Order_model->get_sales_summary();
        $data['recent_orders'] = $this->Order_model->get_orders_by_status(null, 10);
        $data['low_stock_products'] = $this->Product_model->get_low_stock_products(10);
        $data['stock_statistics'] = $this->Product_model->get_stock_statistics();
        $data['monthly_sales'] = $this->Order_model->get_monthly_sales();
        
        // Calculate today's sales
        $today_sales = $this->db->table('orders')
                                ->select_sum('total_amount', 'today_sales')
                                ->where('DATE(created_at)', date('Y-m-d'))
                                ->where('status !=', 'cancelled')
                                ->get();
        
        $data['today_sales'] = $today_sales['today_sales'] ?? 0;
        
        $this->call->view('admin/dashboard', $data);
    }
    
    private function check_admin_auth() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            redirect('/login');
            exit;
        }
    }
}
