<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$config = require __DIR__ . '/config.local.php';
$adminCount = (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
if ($adminCount > 0) { http_response_code(403); exit('An admin account already exists.'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $token = $_POST['setup_token'] ?? '';
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!hash_equals((string)($config['setup_admin_token'] ?? ''), $token)) $error = 'Invalid setup code.';
    elseif (!$email || !$name || !$phone || strlen($password) < 12) $error = 'Use a valid email and a password of at least 12 characters.';
    else {
        try {
            db()->prepare("INSERT INTO users(name,email,password_hash,phone,role) VALUES(?,?,?,?, 'admin')")
                ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $phone]);
            flash('Admin account created. Please log in.');
            redirect('login.php');
        } catch (PDOException $e) { $error = 'Could not create the admin account.'; }
    }
}
page_top('Create first admin'); ?>
<form class="form-card" method="post"><h1>Create the first admin</h1><p class="muted">This page works only until the first admin account exists.</p><?php if ($error): ?><p class="error"><?=e($error)?></p><?php endif; ?><input type="hidden" name="csrf" value="<?=csrf_token()?>"><label>Name</label><input name="name" required><label>Admin email</label><input name="email" type="email" value="gdirectory99@gmail.com" required><label>Phone</label><input name="phone" required><label>New admin password</label><input name="password" type="password" minlength="12" required><label>One-time setup code</label><input name="setup_token" type="password" required><button class="button">Create admin account</button></form><?php page_bottom();
