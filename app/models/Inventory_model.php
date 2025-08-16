<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Inventory_model extends Model {
    
    protected $table = 'inventory_logs';
    protected $primary_key = 'id';
    
    public function log_stock_change($product_id, $type, $quantity, $previous_stock, $new_stock, $notes = '', $created_by = null) {
        $data = [
            'product_id' => $product_id,
            'type' => $type,
            'quantity' => $quantity,
            'previous_stock' => $previous_stock,
            'new_stock' => $new_stock,
            'notes' => $notes,
            'created_by' => $created_by,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->table($this->table)->insert($data);
    }
    
    public function get_logs_with_products($limit = 50) {
        return $this->db->table($this->table . ' il')
                       ->join('products p', 'p.id = il.product_id')
                       ->left_join('users u', 'u.id = il.created_by')
                       ->select('il.*, p.name as product_name, p.image as product_image, CONCAT(u.first_name, " ", u.last_name) as updated_by')
                       ->order_by('il.created_at', 'DESC')
                       ->limit($limit)
                       ->get_all();
    }
    
    public function get_product_logs($product_id, $limit = 20) {
        return $this->db->table($this->table . ' il')
                       ->left_join('users u', 'u.id = il.created_by')
                       ->select('il.*, CONCAT(u.first_name, " ", u.last_name) as updated_by')
                       ->where('il.product_id', $product_id)
                       ->order_by('il.created_at', 'DESC')
                       ->limit($limit)
                       ->get_all();
    }
    
    public function get_low_stock_alerts($threshold = 10) {
        return $this->db->table('products p')
                       ->select('p.*, COUNT(il.id) as recent_changes')
                       ->left_join($this->table . ' il', 'il.product_id = p.id AND il.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')
                       ->where('p.status', 'active')
                       ->where('p.stock_quantity <=', $threshold)
                       ->group_by('p.id')
                       ->order_by('p.stock_quantity', 'ASC')
                       ->get_all();
    }
    
    public function get_stock_movement_summary($days = 30) {
        return $this->db->raw("
            SELECT 
                DATE(created_at) as date,
                SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) as stock_in,
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as stock_out,
                COUNT(*) as total_transactions
            FROM {$this->table}
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ")->fetchAll();
    }
}
