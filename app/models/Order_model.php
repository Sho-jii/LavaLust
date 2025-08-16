<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Order_model extends Model {
    
    protected $table = 'orders';
    protected $primary_key = 'id';
    
    public function create_order($order_data, $order_items) {
        $this->db->transaction();
        
        try {
            // Generate order number
            $order_data['order_number'] = $this->generate_order_number();
            $order_data['created_at'] = date('Y-m-d H:i:s');
            
            // Insert order
            $order_id = $this->db->table($this->table)->insert($order_data);
            
            // Insert order items
            foreach ($order_items as $item) {
                $item['order_id'] = $order_id;
                $item['created_at'] = date('Y-m-d H:i:s');
                $this->db->table('order_items')->insert($item);
                
                // Reduce product stock
                $this->call->model('Product_model');
                $this->Product_model->reduce_stock($item['product_id'], $item['quantity']);
            }
            
            $this->db->commit();
            return $order_id;
            
        } catch (Exception $e) {
            $this->db->roll_back();
            return false;
        }
    }
    
    public function get_order_by_number($order_number) {
        return $this->db->table($this->table)
                       ->where('order_number', $order_number)
                       ->get();
    }
    
    public function get_order_with_items($order_id) {
        $order = $this->db->table($this->table)->where('id', $order_id)->get();
        
        if ($order) {
            $order['items'] = $this->db->table('order_items oi')
                                     ->join('products p', 'p.id = oi.product_id')
                                     ->select('oi.*, p.name as product_name')
                                     ->where('oi.order_id', $order_id)
                                     ->get_all();
        }
        
        return $order;
    }
    
    public function get_orders_by_status($status = null, $limit = null) {
        $query = $this->db->table($this->table . ' o')
                         ->join('users u', 'u.id = o.user_id', 'LEFT')
                         ->select('o.*, CONCAT(u.first_name, " ", u.last_name) as customer_name, u.email as customer_email');
        
        if ($status) {
            $query->where('o.status', $status);
        }
        
        $query->order_by('o.created_at', 'DESC');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get_all();
    }
    
    public function update_status($order_id, $status) {
        return $this->db->table($this->table)
                       ->where('id', $order_id)
                       ->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }
    
    private function generate_order_number() {
        return 'SP' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    public function get_sales_summary() {
        $result = $this->db->raw("
            SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(CASE WHEN status IN ('delivered', 'completed') THEN total_amount ELSE 0 END), 0) as total_sales,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders
            FROM {$this->table}
        ")->fetch();
        
        return $result ?: [
            'total_orders' => 0,
            'total_sales' => 0,
            'pending_orders' => 0,
            'processing_orders' => 0
        ];
    }
    
    public function get_monthly_sales() {
        return $this->db->raw("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(total_amount) as sales,
                COUNT(*) as orders
            FROM {$this->table}
            WHERE status IN ('delivered', 'completed')
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll();
    }
}
