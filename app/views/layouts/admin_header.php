<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Admin - Smart Poultry'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #041421;
            --secondary-dark: #042630;
            --primary-teal: #4c7273;
            --light-teal: #86b9b0;
            --light-gray: #d0d6d6;
            --white: #ffffff;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        
        .sidebar .nav-link {
            color: var(--light-gray);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-teal);
            color: var(--white);
        }
        
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }
        
        .top-navbar {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-teal) 0%, var(--light-teal) 100%);
            border: none;
            border-radius: 8px;
        }
        
        .table th {
            background-color: var(--light-gray);
            border: none;
            font-weight: 600;
        }
        
        .badge {
            border-radius: 20px;
            padding: 6px 12px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="p-4">
            <h4 class="text-white mb-4">
                <i class="fas fa-egg me-2"></i>Smart Poultry
            </h4>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('/admin/dashboard'); ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('/admin/products'); ?>">
                    <i class="fas fa-egg me-2"></i>Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('/admin/orders'); ?>">
                    <i class="fas fa-shopping-cart me-2"></i>Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('/admin/inventory'); ?>">
                    <i class="fas fa-boxes me-2"></i>Inventory
                </a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="<?php echo site_url('/'); ?>">
                    <i class="fas fa-globe me-2"></i>View Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('/logout'); ?>">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Welcome, <?php echo $_SESSION['user_name']; ?></h5>
                <small class="text-muted">Admin Panel</small>
            </div>
            <div>
                <span class="text-muted"><?php echo date('M d, Y H:i'); ?></span>
            </div>
        </div>
        
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="container-fluid mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="container-fluid mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endif; ?>
