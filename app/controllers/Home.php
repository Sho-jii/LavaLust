<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Home extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->model('Product_model');
    }
    
    public function index() {
        $data['title'] = 'Smart Poultry - Fresh Farm Eggs';
        $data['featured_products'] = $this->Product_model->get_active_products();
        
        $this->call->view('customer/home', $data);
    }
}
