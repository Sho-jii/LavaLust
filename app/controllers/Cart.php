<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Cart extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->model('Product_model');
    }
    
    public function index() {
        $data['title'] = 'Shopping Cart - Smart Poultry';
        $data['cart_items'] = $this->get_cart_items();
        $data['cart_total'] = $this->calculate_cart_total();
        
        $this->call->view('customer/cart', $data);
    }
    
    public function add() {
        $product_id = $this->io->post('product_id');
        $quantity = (int)$this->io->post('quantity');
        
        if (!$product_id || $quantity <= 0) {
            redirect('/products');
            return;
        }
        
        $product = $this->Product_model->get_product($product_id);
        
        if (!$product || $product['stock_quantity'] < $quantity) {
            $_SESSION['error'] = 'Product not available or insufficient stock';
            redirect('/products');
            return;
        }
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Add or update cart item
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'product_id' => $product_id,
                'quantity' => $quantity,
                'price' => $product['price']
            ];
        }
        
        $_SESSION['success'] = 'Product added to cart successfully';
        redirect('/cart');
    }
    
    public function update() {
        $product_id = $this->io->post('product_id');
        $quantity = (int)$this->io->post('quantity');
        
        if (isset($_SESSION['cart'][$product_id])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            }
        }
        
        redirect('/cart');
    }
    
    public function remove() {
        $product_id = $this->io->post('product_id');
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        
        redirect('/cart');
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
