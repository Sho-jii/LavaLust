<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Checkout extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->model('Product_model');
        $this->call->model('Order_model');
        $this->call->library('form_validation');
    }
    
    public function index() {
        // Check if cart is empty
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            redirect('/products');
            return;
        }
        
        $data['title'] = 'Checkout - Smart Poultry';
        $data['cart_items'] = $this->get_cart_items();
        $data['cart_total'] = $this->calculate_cart_total();
        
        $this->call->view('customer/checkout', $data);
    }
    
    public function process() {
        // Validate form
        $this->form_validation
            ->name('customer_name')->required()
            ->name('customer_email')->required()->valid_email()
            ->name('customer_phone')->required()
            ->name('delivery_address')->required();
        
        if ($this->form_validation->run() == FALSE) {
            $this->index();
            return;
        }
        
        // Check if cart is empty
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            redirect('/products');
            return;
        }
        
        // Prepare order data
        $order_data = [
            'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0,
            'customer_name' => $this->io->post('customer_name'),
            'customer_email' => $this->io->post('customer_email'),
            'customer_phone' => $this->io->post('customer_phone'),
            'delivery_address' => $this->io->post('delivery_address'),
            'notes' => $this->io->post('notes'),
            'total_amount' => $this->calculate_cart_total(),
            'status' => 'pending'
        ];
        
        // Prepare order items
        $order_items = [];
        foreach ($_SESSION['cart'] as $item) {
            $product = $this->Product_model->get_product($item['product_id']);
            if ($product) {
                $order_items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product['price'],
                    'total_price' => $product['price'] * $item['quantity']
                ];
            }
        }
        
        // Create order
        $order_id = $this->Order_model->create_order($order_data, $order_items);
        
        if ($order_id) {
            $order = $this->Order_model->get_order_with_items($order_id);
            
            // Clear cart
            unset($_SESSION['cart']);
            
            // Redirect to confirmation
            redirect('/order/confirmation/' . $order['order_number']);
        } else {
            $_SESSION['error'] = 'Failed to process order. Please try again.';
            $this->index();
        }
    }
    
    private function get_cart_items() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return [];
        }
        
        $cart_items = [];
        foreach ($_SESSION['cart'] as $item) {
            $product = $this->Product_model->get_product($item['product_id']);
            if ($product) {
                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product['price'] * $item['quantity']
                ];
            }
        }
        
        return $cart_items;
    }
    
    private function calculate_cart_total() {
        $total = 0;
        $cart_items = $this->get_cart_items();
        
        foreach ($cart_items as $item) {
            $total += $item['subtotal'];
        }
        
        return $total;
    }
}
