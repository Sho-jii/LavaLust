<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Products extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->model('Product_model');
    }
    
    public function index() {
        $data['title'] = 'Our Products - Smart Poultry';
        $data['products'] = $this->Product_model->get_active_products();
        
        $this->call->view('customer/products', $data);
    }
    
    public function show($id) {
        $product = $this->Product_model->get_product($id);
        
        if (!$product) {
            redirect('/products');
            return;
        }
        
        $data['title'] = $product['name'] . ' - Smart Poultry';
        $data['product'] = $product;
        
        $this->call->view('customer/product_detail', $data);
    }
}
