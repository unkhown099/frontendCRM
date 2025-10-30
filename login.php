<?php
session_start();

// SAMPLE-ONLY (DB-LESS) LOGIN FOR LOCAL TESTING
// This login does NOT use the database and is restricted to localhost.
// WARNING: Remove or disable this file before deploying to production.

$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remote, ['127.0.0.1', '::1'])) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Access denied. This login is allowed from localhost only for safety.';
    exit;
}

// Configurable sample credentials (via env) or defaults
$sample_email = getenv('SAMPLE_LOGIN_EMAIL') ?: 'sample@local';
$sample_password = getenv('SAMPLE_LOGIN_PASSWORD') ?: 'samplepass';

$errors = [];
$email = '';

if (isset($_SESSION['user_id'])) {
    // already logged in, go to dashboard
    header('Location: CRM modules/CrmDashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') $errors[] = 'Email is required.';
    if ($password === '') $errors[] = 'Password is required.';

    if (empty($errors)) {
        if ($email === $sample_email && $password === $sample_password) {
            // Set the same session keys other pages expect
            $_SESSION['user_id'] = 'sample';
            $_SESSION['user_name'] = 'Sample User';
            header('Location: CRM modules/CrmDashboard.php');
            exit;
        }

        $errors[] = 'Invalid sample credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login (Sample) - CRM System</title>
    <link rel="stylesheet" href="CRM modules/styles/crmGlobalStyles.css">
    <style>
        body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; background:#f3f4f6}
        .wrap{max-width:420px;margin:80px auto;background:#fff;padding:28px;border-radius:12px;box-shadow:0 8px 24px rgba(15,23,42,0.08)}
        .err{background:#fee2e2;color:#991b1b;padding:8px;border-radius:6px;margin-bottom:12px}
        .muted{color:#6b7280;font-size:13px}
        .form-input{width:100%;padding:10px;margin-bottom:12px;border-radius:6px;border:1px solid #e5e7eb}
        .btn{background:#2563eb;color:#fff;padding:10px 14px;border-radius:8px;border:0;cursor:pointer}
        .warn{font-size:13px;color:#b91c1c;margin-bottom:12px}
    </style>
</head>
<body>
    <div class="wrap">
        <h2>Local sample login (no DB)</h2>
        <p class="muted">This logs you in locally without the database. Use only for development.</p>
        <p class="warn">Warning: Remove this file before deploying to any public server.</p>

        <?php if (!empty($errors)): ?>
            <div class="err"><?php echo htmlspecialchars(implode('<br>', $errors)); ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <input class="form-input" type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
            <input class="form-input" type="password" name="password" placeholder="Password" required>
            <div style="display:flex;gap:8px;align-items:center">
                <button class="btn" type="submit">Sign in</button>
                <a class="muted" href="login_sample.php" style="text-decoration:none;margin-left:8px">Or use sample helper</a>
            </div>
        </form>

        <hr style="margin:18px 0">
        <div class="muted">Sample credentials (env-configurable):</div>
        <pre style="background:#f9fafb;padding:8px;border-radius:6px">Email: <?php echo htmlspecialchars($sample_email); ?>

Password: <?php echo htmlspecialchars($sample_password); ?></pre>
    </div>
</body>
</html>