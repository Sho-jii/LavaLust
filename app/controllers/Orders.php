<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Orders extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->model('Order_model');
    }
    
    public function confirmation($order_number) {
        $order = $this->Order_model->get_order_by_number($order_number);
        
        if (!$order) {
            redirect('/');
            return;
        }
        
        $data['title'] = 'Order Confirmation - Smart Poultry';
        $data['order'] = $this->Order_model->get_order_with_items($order['id']);
        
        $this->call->view('customer/order_confirmation', $data);
    }
}
