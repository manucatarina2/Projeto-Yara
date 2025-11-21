<?php
// processa_form.php
require_once 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    switch($acao) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (fazerLogin($email, $senha)) {
                echo json_encode(['success' => true, 'message' => 'Login realizado com sucesso!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos!']);
            }
            break;
            
        case 'cadastro':
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $foto = $_FILES['foto'] ?? null;
            
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
            // Aqui você pode salvar o email em uma tabela de newsletter
            echo json_encode(['success' => true, 'message' => 'Inscrição na newsletter realizada com sucesso!']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida!']);
    }
    exit;
}
?>