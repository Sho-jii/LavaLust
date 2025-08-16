<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->call->model('User_model');
        $this->call->library('form_validation');
    }
    
    public function login() {
        if (isset($_SESSION['user_id'])) {
            redirect('/');
            return;
        }
        $data['title'] = 'Login - Smart Poultry';
        $this->call->view('auth/login', $data);
    }
    
    public function authenticate() {
        $this->form_validation
            ->name('email')->required()->valid_email()
            ->name('password')->required();
        
        if ($this->form_validation->run() == FALSE) {
            $this->login();
            return;
        }
        
        $email = $this->io->post('email');
        $password = $this->io->post('password');
        
        $user = $this->User_model->authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            if ($user['role'] == 'admin') {
                redirect('/admin/dashboard');
            } else {
                redirect('/');
            }
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            $this->login();
        }
    }
    
    public function register() {
        if (isset($_SESSION['user_id'])) {
            redirect('/');
            return;
        }
        
        $data['title'] = 'Register - Smart Poultry';
        $this->call->view('auth/register', $data);
    }
    
    public function store() {
        $this->form_validation
            ->name('username')->required()->min_length(3)
            ->name('email')->required()->valid_email()
            ->name('password')->required()->min_length(6)
            ->name('confirm_password')->required()->matches('password')
            ->name('first_name')->required()
            ->name('last_name')->required();
        
        if ($this->form_validation->run() == FALSE) {
            $this->register();
            return;
        }
        
        $email = $this->io->post('email');
        $username = $this->io->post('username');
        
        if ($this->User_model->email_exists($email)) {
            $_SESSION['error'] = 'Email already exists';
            $this->register();
            return;
        }
        
        if ($this->User_model->username_exists($username)) {
            $_SESSION['error'] = 'Username already exists';
            $this->register();
            return;
        }
        
        $user_data = [
            'username' => $username,
            'email' => $email,
            'password' => $this->io->post('password'),
            'first_name' => $this->io->post('first_name'),
            'last_name' => $this->io->post('last_name'),
            'phone' => $this->io->post('phone'),
            'address' => $this->io->post('address'),
            'role' => 'customer'
        ];
        
        $user_id = $this->User_model->create_user($user_data);
        
        if ($user_id) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            redirect('/login');
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            $this->register();
        }
    }
    
    public function logout() {
        session_destroy();
        redirect('/');
    }
}
