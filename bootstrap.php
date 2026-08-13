<?php
declare(strict_types=1);
session_name('dealer_market');
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);
require_once __DIR__ . '/db.php';

function e(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function is_admin(): bool { return (current_user()['role'] ?? '') === 'admin'; }
function require_login(): void { if (!current_user()) { header('Location: login.php'); exit; } }
function require_admin(): void { require_login(); if (!is_admin()) { http_response_code(403); exit('Access denied.'); } }
function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); exit('Invalid form request.'); } }
function flash(string $message = null): ?string { if ($message !== null) { $_SESSION['flash'] = $message; return null; } $m = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $m; }
function redirect(string $url): void { header('Location: ' . $url); exit; }
function page_top(string $title): void { $user = current_user(); ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?=e($title)?> | Dealer Car Market</title><link rel="stylesheet" href="style.css"></head><body>
<header><nav class="nav"><a class="brand" href="index.php">Dealer Car Market</a><div><?php if ($user): ?><a href="dealer-dashboard.php">Dashboard</a><?php if (is_admin()): ?><a href="admin-dashboard.php">Admin</a><?php endif; ?><a href="add-car.php">Add car</a><a href="logout.php">Logout</a><?php else: ?><a href="login.php">Login</a><a class="button small" href="register.php">Register</a><?php endif; ?></div></nav></header><main class="container"><?php if ($m = flash()): ?><p class="flash"><?=e($m)?></p><?php endif; ?>
<?php }
function page_bottom(): void { ?></main></body></html><?php }
function car_image(array $car): string { return $car['image_path'] ? e($car['image_path']) : 'assets/car-placeholder.svg'; }
