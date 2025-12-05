<?php
// admin/atualizar_personalizado.php
session_start();

// Verificar se é admin
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['is_admin'] ?? 0) != 1) {
    header('Location: ../login.php');
    exit();
}

require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $novo_status = $_POST['status'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';
    
    $status_validos = ['pendente', 'produzindo', 'pronto', 'entregue'];
    
    if (!in_array($novo_status, $status_validos)) {
        die('Status inválido!');
    }
    
    $stmt = $conexao->prepare("UPDATE pedidos_personalizados SET status = ?, observacoes = ? WHERE id = ?");
    $stmt->bind_param("ssi", $novo_status, $observacoes, $id);
    
    if ($stmt->execute()) {
        header('Location: admin_personalizados.php?success=1');
    } else {
        header('Location: admin_personalizados.php?error=1');
    }
    exit;
}

// Se não for POST, redirecionar
header('Location: admin_personalizados.php');
?>