<?php
session_start();
require_once 'config.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Sobat Literasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --tosca: #5BB8A6;
            --tosca-dark: #3D9E8C;
            --tosca-light: #E8F7F4;
            --tosca-mid: #A8DDD5;
            --white: #FFFFFF;
            --gray-soft: #F4F6F8;
            --gray-mid: #8A9BB0;
            --gray-text: #3D4A5C;
            --shadow-soft: 0 4px 24px rgba(91,184,166,0.13);
            --shadow-card: 0 8px 40px rgba(30,60,80,0.10);
            --radius-xl: 24px;
            --radius-lg: 16px;
            --radius-md: 12px;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #EAF7F4 0%, #F0F9F7 40%, #E4F4F1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        /* Decorative blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.35;
            pointer-events: none;
        }
        .blob-1 { width:400px; height:400px; background:var(--tosca); top:-100px; right:-100px; animation: blobFloat 8s ease-in-out infinite; }
        .blob-2 { width:300px; height:300px; background:#B2E8DF; bottom:-80px; left:-80px; animation: blobFloat 10s ease-in-out infinite reverse; }
        .blob-3 { width:200px; height:200px; background:#5BB8A6; top:50%; left:10%; animation: blobFloat 12s ease-in-out infinite 2s; }
        @keyframes blobFloat {
            0%,100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 20px;
        }
        .login-card {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-card), 0 0 0 1px rgba(91,184,166,0.12);
            padding: 48px 44px;
            animation: fadeInUp 0.7s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(30px); }
            to { opacity:1; transform:translateY(0); }
        }
        .brand-logo {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, var(--tosca), var(--tosca-dark));
            border-radius: 18px;
            display: flex; align-items:center; justify-content:center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(91,184,166,0.35);
            animation: logoFloat 4s ease-in-out infinite;
        }
        @keyframes logoFloat {
            0%,100%{ transform:translateY(0); }
            50%{ transform:translateY(-5px); }
        }
        .brand-logo i { font-size: 28px; color: white; }
        .brand-name { font-size: 22px; font-weight: 800; color: var(--gray-text); text-align:center; letter-spacing:-0.5px; }
        .brand-sub { font-size: 13px; color: var(--gray-mid); text-align:center; margin-top:4px; margin-bottom:36px; }
        .brand-sub span { color: var(--tosca); font-weight:600; }

        .form-label { font-size: 13px; font-weight: 600; color: var(--gray-text); margin-bottom: 8px; display:block; }
        .input-group-custom { position: relative; margin-bottom: 18px; }
        .input-icon { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--tosca); font-size:16px; z-index:2; }
        .form-control-custom {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border: 2px solid #E8EEF4;
            border-radius: var(--radius-md);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            color: var(--gray-text);
            background: var(--gray-soft);
            transition: all 0.25s ease;
            outline: none;
        }
        .form-control-custom:focus {
            border-color: var(--tosca);
            background: white;
            box-shadow: 0 0 0 4px rgba(91,184,166,0.12);
        }
        .toggle-pwd { position:absolute; right:16px; top:50%; transform:translateY(-50%); cursor:pointer; color:var(--gray-mid); font-size:15px; z-index:2; transition:color .2s; }
        .toggle-pwd:hover { color: var(--tosca); }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--tosca), var(--tosca-dark));
            border: none;
            border-radius: var(--radius-md);
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(91,184,166,0.35);
            position: relative;
            overflow: hidden;
            margin-top: 8px;
        }
        .btn-login::after {
            content:'';
            position:absolute; inset:0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity:0; transition:opacity .3s;
        }
        .btn-login:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(91,184,166,0.45); }
        .btn-login:hover::after { opacity:1; }
        .btn-login:active { transform:translateY(0); }

        .alert-error {
            background: #FFF0F0;
            border: 1px solid #FFD5D5;
            border-radius: var(--radius-md);
            color: #C53030;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            animation: shake 0.4s ease;
        }
        @keyframes shake {
            0%,100%{ transform:translateX(0); }
            20%{ transform:translateX(-6px); }
            60%{ transform:translateX(6px); }
        }
        .divider-hint { text-align:center; margin-top: 28px; padding-top:20px; border-top:1px solid #E8EEF4; }
        .divider-hint p { font-size:12px; color:var(--gray-mid); }
        .divider-hint strong { color:var(--tosca); }
        .loading-spinner { display:none; width:18px; height:18px; border:2px solid rgba(255,255,255,0.4); border-top-color:white; border-radius:50%; animation:spin .7s linear infinite; margin:0 auto; }
        @keyframes spin { to{ transform:rotate(360deg); } }
        @media(max-width:480px){ .login-card{ padding:36px 28px; } }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-logo"><i class="bi bi-book-half"></i></div>
            <div class="brand-name">Sobat Literasi</div>
            <div class="brand-sub">Panel Admin — <span>Masuk untuk mengelola</span></div>

            <?php if ($error): ?>
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form id="loginForm" action="process_login.php" method="POST" autocomplete="off">
                <div>
                    <label class="form-label">Username</label>
                    <div class="input-group-custom">
                        <i class="bi bi-person-fill input-icon"></i>
                        <input type="text" name="username" class="form-control-custom"
                               placeholder="Masukkan username" required autocomplete="username">
                    </div>
                </div>
                <div>
                    <label class="form-label">Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input type="password" name="password" id="passwordInput" class="form-control-custom"
                               placeholder="Masukkan password" required autocomplete="current-password">
                        <i class="bi bi-eye-fill toggle-pwd" id="togglePwd"></i>
                    </div>
                </div>
                <button type="submit" class="btn-login" id="loginBtn">
                    <span id="btnText"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Dashboard</span>
                    <div class="loading-spinner" id="btnSpinner"></div>
                </button>
            </form>

            <div class="divider-hint">
                <p>Default: <strong>admin</strong> / <strong>password</strong></p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePwd').addEventListener('click', function() {
            const input = document.getElementById('passwordInput');
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            this.className = isText ? 'bi bi-eye-fill toggle-pwd' : 'bi bi-eye-slash-fill toggle-pwd';
        });
        // Loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('btnText').style.display = 'none';
            document.getElementById('btnSpinner').style.display = 'block';
            document.getElementById('loginBtn').disabled = true;
        });
    </script>
</body>
</html>
