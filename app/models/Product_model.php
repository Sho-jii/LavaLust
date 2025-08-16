<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Product_model extends Model {
    
    protected $table = 'products';
    protected $primary_key = 'id';
    
    public function get_active_products() {
        return $this->db->table($this->table)
                       ->where('status', 'active')
                       ->order_by('name', 'ASC')
                       ->get_all();
    }
    
    public function get_by_category($category) {
        return $this->db->table($this->table)
                       ->where('status', 'active')
                       ->where('category', $category)
                       ->order_by('name', 'ASC')
                       ->get_all();
    }
    
    public function get_product($id) {
        return $this->db->table($this->table)
                       ->where('id', $id)
                       ->where('status', 'active')
                       ->get();
    }
    
    public function update_stock($product_id, $quantity) {
        return $this->db->table($this->table)
                       ->where('id', $product_id)
                       ->update(['stock_quantity' => $quantity]);
    }
    
    public function reduce_stock($product_id, $quantity) {
        $product = $this->get_product($product_id);
        if ($product && $product['stock_quantity'] >= $quantity) {
            $new_stock = $product['stock_quantity'] - $quantity;
            return $this->update_stock($product_id, $new_stock);
        }
        return false;
    }
    
    public function get_low_stock_products($threshold = 10) {
        return $this->db->table($this->table)
                       ->where('status', 'active')
                       ->where('stock_quantity <=', $threshold)
                       ->order_by('stock_quantity', 'ASC')
                       ->get_all();
    }
    
    public function get_stock_statistics() {
        $result = $this->db->raw("
            SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN stock_quantity > 10 THEN 1 ELSE 0 END) as in_stock,
                SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= 10 THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock
            FROM {$this->table}
            WHERE status = 'active'
        ")->fetch();
        
        return $result ?: [
            'total_products' => 0,
            'in_stock' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0
        ];
    }
    
    public function get_all_products() {
        return $this->db->table($this->table)
                       ->order_by('created_at', 'DESC')
                       ->get_all();
    }
    
    public function create_product($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->table($this->table)->insert($data);
    }
    
    public function update_product($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->table($this->table)
                       ->where('id', $id)
                       ->update($data);
    }
    
    public function delete_product($id) {
        return $this->db->table($this->table)
                       ->where('id', $id)
                       ->delete();
    }
}
