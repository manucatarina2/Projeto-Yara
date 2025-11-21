<?php
// processa_form.php - VERSÃO CORRIGIDA

// Iniciar sessão PRIMEIRO
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Headers para JSON ANTES de qualquer output
header('Content-Type: application/json');

// Incluir funcoes.php DEPOIS dos headers
require_once 'funcoes.php';

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Obter a ação
$acao = $_POST['acao'] ?? '';

try {
    switch($acao) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (empty($email) || empty($senha)) {
                echo json_encode(['success' => false, 'message' => 'Email e senha são obrigatórios!']);
                break;
            }
            
            // TESTE DIRETO - Credenciais do admin
            if ($email === 'admin@gmail.com' && $senha === 'admin1234') {
                $_SESSION['usuario'] = [
                    'id' => 1,
                    'nome' => 'Administrador YARA',
                    'email' => 'admin@gmail.com',
                    'is_admin' => 1
                ];
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login de administrador realizado com sucesso!',
                    'redirect' => 'admin/dashboard.php'
                ]);
                break;
            }
            
            // Login normal
            if (fazerLogin($email, $senha)) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Login realizado com sucesso!'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos!']);
            }
            break;
            
        case 'cadastro':
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $foto = $_FILES['foto'] ?? null;
            
            if (empty($nome) || empty($email) || empty($senha)) {
                echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios!']);
                break;
            }
            
            $resultado = cadastrarUsuario($nome, $email, $senha, $foto);
            if ($resultado === true) {
                echo json_encode(['success' => true, 'message' => 'Cadastro realizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => $resultado]);
            }
            break;
            
        case 'logout':
            if (fazerLogout()) {
                echo json_encode(['success' => true, 'message' => 'Logout realizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao fazer logout!']);
            }
            break;
            
        case 'newsletter':
            $email = $_POST['email'] ?? '';
            if (empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Email é obrigatório!']);
                break;
            }
            echo json_encode(['success' => true, 'message' => 'Inscrição na newsletter realizada com sucesso!']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida!']);
    }
    
} catch (Exception $e) {
    // Log do erro (não mostrar para o usuário)
    error_log("Erro em processa_form.php: " . $e->getMessage());
    
    // Mensagem genérica para o usuário
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}

exit;
?>