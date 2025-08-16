<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class User_model extends Model {
    
    protected $table = 'users';
    protected $primary_key = 'id';
    
    public function authenticate($email, $password) {
        $user = $this->db->table($this->table)
                         ->where('email', $email)
                         ->where('status', 'active')
                         ->get();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    public function create_user($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->db->table($this->table)->insert($data);
    }
    
    public function email_exists($email, $exclude_id = null) {
        $query = $this->db->table($this->table)->where('email', $email);
        
        if ($exclude_id) {
            $query->where('id !=', $exclude_id);
        }
        
        return $query->get() ? true : false;
    }
    
    public function username_exists($username, $exclude_id = null) {
        $query = $this->db->table($this->table)->where('username', $username);
        
        if ($exclude_id) {
            $query->where('id !=', $exclude_id);
        }
        
        return $query->get() ? true : false;
    }
}
