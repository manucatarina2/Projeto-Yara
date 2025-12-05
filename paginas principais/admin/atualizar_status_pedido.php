<?php
// admin/atualizar_status_pedido.php
require_once '../funcoes.php';

// Verificar se é admin
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario'] || ($_SESSION['usuario']['is_admin'] ?? 0) != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedido_id = isset($_POST['pedido_id']) ? (int)$_POST['pedido_id'] : 0;
    $novo_status = isset($_POST['novo_status']) ? $_POST['novo_status'] : '';

    $status_validos = ['pendente', 'pago', 'enviado', 'entregue', 'cancelado'];

    if ($pedido_id > 0 && in_array($novo_status, $status_validos)) {
        $sql = "UPDATE pedidos SET status = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $novo_status, $pedido_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar no banco de dados.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
}
