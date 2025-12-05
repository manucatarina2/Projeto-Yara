<?php
// admin/pedidos.php
session_start();
// Ajuste o caminho do require conforme a sua pasta admin
require_once '../conexao.php'; 

// Verifica se é admin (Simulação básica)
// if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['is_admin'] != 1) { header('Location: ../index.php'); exit; }

// --- Atualizar Status (Se o form for enviado) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_status'])) {
    $novo_status = $_POST['status'];
    $id_pedido = $_POST['id_pedido'];
    $conexao->query("UPDATE pedidos SET status = '$novo_status' WHERE id = $id_pedido");
}

// Buscar todos os pedidos (do mais novo para o mais velho)
$sql = "SELECT * FROM pedidos ORDER BY data_pedido DESC";
$pedidos = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Admin - Gestão de Pedidos</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #333; color: #fff; }
        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .pendente { background: #ffeeba; color: #856404; }
        .enviado { background: #d4edda; color: #155724; }
        .cancelado { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Painel de Pedidos (Dashboard)</h1>
    
    <table>
        <thead>
            <tr>
                <th>#ID</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Pagamento</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = $pedidos->fetch_assoc()): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($p['data_pedido'])); ?></td>
                <td>
                    <?php echo $p['nome_cliente']; ?><br>
                    <small><?php echo $p['email_cliente']; ?></small>
                </td>
                <td>R$ <?php echo number_format($p['valor_total'], 2, ',', '.'); ?></td>
                <td><?php echo $p['forma_pagamento']; ?></td>
                <td>
                    <span class="status-badge <?php echo strtolower($p['status']); ?>">
                        <?php echo $p['status']; ?>
                    </span>
                </td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="id_pedido" value="<?php echo $p['id']; ?>">
                        <select name="status">
                            <option value="Pendente" <?php echo $p['status']=='Pendente'?'selected':''; ?>>Pendente</option>
                            <option value="Enviado" <?php echo $p['status']=='Enviado'?'selected':''; ?>>Enviado</option>
                            <option value="Entregue" <?php echo $p['status']=='Entregue'?'selected':''; ?>>Entregue</option>
                        </select>
                        <button type="submit" name="atualizar_status">Atualizar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>