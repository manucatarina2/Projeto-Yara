<?php
// admin/get_pedido_detalhes.php
require_once '../funcoes.php';

// Verificar se é admin
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario'] || ($_SESSION['usuario']['is_admin'] ?? 0) != 1) {
    http_response_code(403);
    echo "Acesso negado";
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID do pedido não fornecido.";
    exit;
}

$pedido_id = (int)$_GET['id'];

// 1. Buscar dados do pedido
$sql_pedido = "SELECT p.*, u.nome as nome_cliente, u.email as email_cliente 
               FROM pedidos p 
               LEFT JOIN usuarios u ON p.usuario_id = u.id 
               WHERE p.id = ?";
$stmt = $conexao->prepare($sql_pedido);
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();

if (!$pedido) {
    echo "Pedido não encontrado.";
    exit;
}

// 2. Buscar itens do pedido
$sql_itens = "SELECT pi.*, p.nome, p.imagem 
              FROM pedido_itens pi 
              JOIN produtos p ON pi.produto_id = p.id 
              WHERE pi.pedido_id = ?";
$stmt_itens = $conexao->prepare($sql_itens);
$stmt_itens->bind_param("i", $pedido_id);
$stmt_itens->execute();
$result_itens = $stmt_itens->get_result();
?>

<div class="modal-header-custom">
    <h2>Pedido #<?php echo $pedido['id']; ?></h2>
    <span class="status-badge <?php echo $pedido['status']; ?>"><?php echo ucfirst($pedido['status']); ?></span>
</div>

<div class="modal-grid">
    <!-- Coluna da Esquerda: Itens -->
    <div class="modal-col-main">
        <h3>Itens do Pedido</h3>
        <div class="lista-itens">
            <?php while ($item = $result_itens->fetch_assoc()): ?>
                <div class="item-pedido">
                    <img src="../<?php echo $item['imagem']; ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" class="item-img">
                    <div class="item-info">
                        <h4><?php echo htmlspecialchars($item['nome']); ?></h4>
                        <p>Qtd: <?php echo $item['quantidade']; ?> x R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></p>
                    </div>
                    <div class="item-total">
                        R$ <?php echo number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.'); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="resumo-financeiro">
            <div class="resumo-linha">
                <span>Subtotal</span>
                <span>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></span>
            </div>
            <div class="resumo-linha total">
                <span>Total</span>
                <span>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></span>
            </div>
        </div>
    </div>

    <!-- Coluna da Direita: Detalhes -->
    <div class="modal-col-sidebar">
        <div class="info-block">
            <h4><i class="fas fa-user"></i> Cliente</h4>
            <p><strong><?php echo htmlspecialchars($pedido['nome_cliente']); ?></strong></p>
            <p><?php echo htmlspecialchars($pedido['email_cliente']); ?></p>
        </div>

        <div class="info-block">
            <h4><i class="fas fa-map-marker-alt"></i> Entrega</h4>
            <p><?php echo nl2br(htmlspecialchars($pedido['endereco_entrega'])); ?></p>
        </div>

        <div class="info-block">
            <h4><i class="fas fa-credit-card"></i> Pagamento</h4>
            <p>Método: <?php echo ucfirst($pedido['forma_pagamento']); ?></p>
            <p>Data: <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
        </div>

        <?php if ($pedido['status'] !== 'cancelado' && $pedido['status'] !== 'entregue'): ?>
            <div class="acoes-pedido">
                <h4>Atualizar Status</h4>
                <form id="formStatusPedido" onsubmit="atualizarStatus(event)">
                    <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                    <select name="novo_status" class="form-select">
                        <option value="pendente" <?php echo $pedido['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="pago" <?php echo $pedido['status'] == 'pago' ? 'selected' : ''; ?>>Pago</option>
                        <option value="enviado" <?php echo $pedido['status'] == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                        <option value="entregue" <?php echo $pedido['status'] == 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                        <option value="cancelado" <?php echo $pedido['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                    <button type="submit" class="btn-atualizar">Salvar</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .modal-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .modal-header-custom h2 {
        margin: 0;
        color: #2c3e50;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-badge.pago {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.pendente {
        background: #fff3cd;
        color: #856404;
    }

    .status-badge.cancelado {
        background: #f8d7da;
        color: #721c24;
    }

    .status-badge.enviado {
        background: #cce5ff;
        color: #004085;
    }

    .modal-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
    }

    .lista-itens {
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
    }

    .item-pedido {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .item-pedido:last-child {
        border-bottom: none;
    }

    .item-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 15px;
    }

    .item-info {
        flex: 1;
    }

    .item-info h4 {
        margin: 0 0 5px 0;
        font-size: 14px;
        color: #333;
    }

    .item-info p {
        margin: 0;
        font-size: 12px;
        color: #666;
    }

    .item-total {
        font-weight: bold;
        color: #e91e7d;
    }

    .resumo-financeiro {
        margin-top: 20px;
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
    }

    .resumo-linha {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .resumo-linha.total {
        font-weight: bold;
        font-size: 16px;
        color: #2c3e50;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #ddd;
    }

    .info-block {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .info-block h4 {
        margin: 0 0 10px 0;
        font-size: 13px;
        color: #7f8c8d;
        text-transform: uppercase;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }

    .info-block p {
        margin: 0 0 5px 0;
        font-size: 13px;
        color: #333;
    }

    .acoes-pedido {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .form-select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .btn-atualizar {
        width: 100%;
        background: #2c3e50;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-atualizar:hover {
        background: #34495e;
    }

    @media (max-width: 768px) {
        .modal-grid {
            grid-template-columns: 1fr;
        }
    }
</style>