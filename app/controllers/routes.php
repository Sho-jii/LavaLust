<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
/**
 * ------------------------------------------------------------------
 * LavaLust - an opensource lightweight PHP MVC Framework
 * ------------------------------------------------------------------
 *
 * MIT License
 *
 * Copyright (c) 2020 Ronald M. Marasigan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package LavaLust
 * @author Ronald M. Marasigan <ronald.marasigan@yahoo.com>
 * @since Version 1
 * @link https://github.com/ronmarasigan/LavaLust
 * @license https://opensource.org/licenses/MIT MIT License
 */

/*
| -------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------
| Here is where you can register web routes for your application.
|
|
*/

// Customer Routes
$router->get('/', 'Home::index');
$router->get('/products', 'Products::index');
$router->get('/product/{id}', 'Products::show');

// Cart Routes
$router->get('/cart', 'Cart::index');
$router->post('/cart/add', 'Cart::add');
$router->post('/cart/update', 'Cart::update');
$router->post('/cart/remove', 'Cart::remove');

// Checkout Routes
$router->get('/checkout', 'Checkout::index');
$router->post('/checkout/process', 'Checkout::process');

// Order Routes
$router->get('/order/confirmation/{order_number}', 'Orders::confirmation');

// Authentication Routes
$router->get('/login', 'Auth::login');
$router->post('/login', 'Auth::authenticate');
$router->get('/register', 'Auth::register');
$router->post('/register', 'Auth::store');
$router->get('/logout', 'Auth::logout');

// Admin Routes
$router->group('admin', function() use ($router) {
    // Dashboard
    $router->get('dashboard', 'Admin/Dashboard::index');
    
    // Products Management
    $router->get('products', 'Admin/Products::index');
    $router->get('products/create', 'Admin/Products::create');
    $router->post('products/store', 'Admin/Products::store');
    $router->get('products/edit/{id}', 'Admin/Products::edit');
    $router->post('products/update/{id}', 'Admin/Products::update');
    $router->post('products/delete/{id}', 'Admin/Products::delete');
    
    // Orders Management
    $router->get('orders', 'Admin/Orders::index');
    $router->get('orders/view/{id}', 'Admin/Orders::view');
    $router->post('orders/update-status/{id}', 'Admin/Orders::updateStatus');
    
    // Inventory Management
    $router->get('inventory', 'Admin/Inventory::index');
    $router->post('inventory/update-stock', 'Admin/Inventory::updateStock');
    $router->get('inventory/logs', 'Admin/Inventory::logs');
});
