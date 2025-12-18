<?php
// login.php
require_once 'config.php';

$error = '';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username)) {
        $error = 'Username tidak boleh kosong';
    } elseif (empty($password)) {
        $error = 'Password tidak boleh kosong';
    } elseif (strlen($password) < 4) {
        $error = 'Password minimal 4 karakter';
    } else {
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Username atau password salah';
            }
        } else {
            // Auto-register for demo purposes
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashedPassword);
            
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['username'] = $username;
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Tugas Kuliah</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f9fafb;
            color: #111827;
            line-height: 1.5;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
        }

        .login-cover {
            display: none;
            width: 50%;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        }

        .login-cover-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 3rem;
            height: 100%;
            text-align: center;
        }

        .login-cover h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .login-form-container {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: #f9fafb;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 28rem;
        }

        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
        }

        .logo-mobile {
            display: inline-flex;
            padding: 0.75rem;
            background: #3b82f6;
            border-radius: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            color: #374151;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            padding-left: 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
            width: 100%;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .error-message {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            color: #dc2626;
            font-size: 0.875rem;
        }

        .demo-hint {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.5rem;
            color: #1e40af;
            font-size: 0.875rem;
            text-align: center;
        }

        .icon {
            width: 1.25rem;
            height: 1.25rem;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }

        .icon-lg {
            width: 1.5rem;
            height: 1.5rem;
        }

        .icon-xl {
            width: 2.5rem;
            height: 2.5rem;
        }

        @media (min-width: 768px) {
            .login-cover {
                display: flex;
            }

            .login-form-container {
                width: 50%;
            }

            .logo-mobile {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-cover">
            <div class="login-cover-content">
                <div style="max-width: 28rem;">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: inline-flex; padding: 1rem; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 1rem;">
                            <svg class="icon-xl" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </div>
                    </div>
                    <h1>Manajemen Tugas Kuliah</h1>
                    <p style="color: rgba(255,255,255,0.9); font-size: 1.125rem; margin-top: 1rem;">
                        Kelola semua tugas perkuliahan Anda dengan mudah. Pantau deadline, prioritas, dan status pengerjaan dalam satu tempat.
                    </p>
                </div>
            </div>
        </div>

        <div class="login-form-container">
            <div class="login-form-wrapper">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <span class="logo-mobile">
                        <svg class="icon-lg" style="color: white;" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    </span>
                    <h1>Manajemen Tugas Kuliah</h1>
                    <p style="color: #6b7280; margin-top: 0.5rem;">Kelola tugas perkuliahan Anda</p>
                </div>

                <div class="login-card">
                    <div style="margin-bottom: 2rem;">
                        <h2>Selamat Datang Kembali!</h2>
                        <p style="color: #6b7280; margin-top: 0.5rem;">Silakan login untuk melanjutkan</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg class="icon" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                </span>
                                <input type="text" id="username" name="username" placeholder="Masukkan username Anda" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required autocomplete="username">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg class="icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                </span>
                                <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required autocomplete="current-password">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">Login</button>
                    </form>

                    <div class="demo-hint">
                        <span style="display: block; margin-bottom: 0.25rem;">💡 Untuk demo, gunakan username apa saja</span>
                        <span style="color: #2563eb;">Password minimal 4 karakter (otomatis register jika user baru)</span>
                    </div>
                </div>

                <p style="text-align: center; color: #6b7280; font-size: 0.875rem; margin-top: 1.5rem;">
                    © 2024 Manajemen Tugas Kuliah. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>