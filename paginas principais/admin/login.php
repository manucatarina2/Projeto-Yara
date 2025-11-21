<?php
// admin/login.php
session_start();
require_once '../funcoes.php';

$erro = '';

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    // Verificar se é o admin padrão (em um sistema real, isso viria do banco)
    if ($email === 'admin@yara.com' && $senha === 'admin123') {
        $_SESSION['admin_logado'] = true;
        $_SESSION['admin_nome'] = 'Administrador YARA';
        header('Location: dashboard.php');
        exit;
    } else {
        $erro = 'E-mail ou senha incorretos!';
    }
}

// Se já estiver logado, redirecionar para o dashboard
if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Login YARA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #e91e7d;
            --secondary-color: #2c3e50;
            --font-family: 'Poppins', sans-serif;
        }
        
        @import url('https://fonts.googleapis.com/css?family=Poppins:300,400,600,700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-family);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
        }
        
        .login-header {
            background: var(--secondary-color);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .login-header p {
            opacity: 0.8;
            font-size: 14px;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(233, 30, 125, 0.1);
        }
        
        .btn-login {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-login:hover {
            background: #c2185b;
        }
        
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #e1e5e9;
            color: #666;
            font-size: 12px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo img {
            max-width: 80px;
            height: auto;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <!-- Adicione sua logo aqui -->
                <div style="width: 60px; height: 60px; background: white; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; color: var(--secondary-color); font-weight: bold; font-size: 20px;">
                    Y
                </div>
            </div>
            <h1>Painel Admin</h1>
            <p>YARA Joias - Acesso Restrito</p>
        </div>
        
        <div class="login-body">
            <?php if ($erro): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $erro; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="seu@email.com" required>
                </div>
                
                <div class="form-group">
                    <label for="senha"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" id="senha" name="senha" class="form-control" placeholder="Sua senha" required>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Entrar no Painel
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <p>Acesso restrito à equipe administrativa</p>
            <p>© 2025 YARA Joias - Todos os direitos reservados</p>
        </div>
    </div>

</body>
</html>