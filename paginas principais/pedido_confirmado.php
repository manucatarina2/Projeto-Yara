<?php
// pedido_confirmado.php - VERSÃO FINAL CORRIGIDA
session_start();
require_once 'conexao.php';

// Verificar se foi passado um ID de pedido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: produtos.php');
    exit;
}

$pedido_id = intval($_GET['id']);

// Buscar informações do pedido
$sql = "SELECT p.*, u.nome as cliente_nome, u.email as cliente_email 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        WHERE p.id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: produtos.php');
    exit;
}

$pedido = $result->fetch_assoc();

// Verificar qual coluna tem o valor total
$has_valor_total = isset($pedido['valor_total']);
$valor_total = $has_valor_total ? $pedido['valor_total'] : ($pedido['total'] ?? 0);

// Buscar itens do pedido
$sql_itens = "SELECT pi.*, pr.nome as produto_nome 
              FROM pedido_itens pi
              JOIN produtos pr ON pi.produto_id = pr.id
              WHERE pi.pedido_id = ?";
$stmt_itens = $conexao->prepare($sql_itens);
$stmt_itens->bind_param("i", $pedido_id);
$stmt_itens->execute();
$itens_result = $stmt_itens->get_result();
$itens_pedido = [];
$subtotal = 0;

while ($item = $itens_result->fetch_assoc()) {
    $itens_pedido[] = $item;
    // Calcular subtotal se não existir na coluna
    if (isset($item['subtotal'])) {
        $subtotal += $item['subtotal'];
    } else {
        $subtotal += $item['preco_unitario'] * $item['quantidade'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - YARA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f9f5f0 0%, #f0e6d8 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .confirmation-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }
        
        .confirmation-header {
            background: linear-gradient(135deg, #e91e63 0%, #d81b60 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .check-icon {
            font-size: 60px;
            margin-bottom: 20px;
            animation: bounce 1s ease infinite alternate;
        }
        
        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-10px); }
        }
        
        .confirmation-header h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .confirmation-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .order-number {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 12px 25px;
            border-radius: 30px;
            margin-top: 20px;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .confirmation-content {
            padding: 30px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-section h3 {
            color: #e91e63;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-section h3 i {
            font-size: 1.2rem;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .info-item p {
            margin: 8px 0;
            color: #666;
        }
        
        .info-label {
            font-weight: 600;
            color: #333;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e91e63;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .total-row {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .total-amount {
            color: #e91e63;
            font-size: 1.3rem;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 14px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #e91e63 0%, #d81b60 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(233, 30, 99, 0.3);
        }
        
        .btn-secondary {
            background: #333;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #555;
            transform: translateY(-3px);
        }
        
        .btn-outline {
            background: transparent;
            color: #e91e63;
            border: 2px solid #e91e63;
        }
        
        .btn-outline:hover {
            background: #e91e63;
            color: white;
        }
        
        .comprovante {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-top: 30px;
            border: 2px dashed #ddd;
        }
        
        .comprovante-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .comprovante-header h2 {
            color: #e91e63;
        }
        
        .comprovante-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .confirmation-header {
                padding: 30px 20px;
            }
            
            .confirmation-header h1 {
                font-size: 1.8rem;
            }
            
            .check-icon {
                font-size: 50px;
            }
            
            .confirmation-content {
                padding: 20px;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .items-table {
                font-size: 0.9rem;
            }
            
            .items-table th,
            .items-table td {
                padding: 10px;
            }
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white;
                padding: 0;
            }
            
            .confirmation-container {
                box-shadow: none;
                max-width: 100%;
            }
            
            .confirmation-header {
                background: #f0f0f0 !important;
                color: #333 !important;
                -webkit-print-color-adjust: exact;
            }
            
            .btn {
                display: none !important;
            }
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-pago {
            background: #d4edda;
            color: #155724;
        }
        
        .status-processando {
            background: #cce5ff;
            color: #004085;
        }
        
        .pix-badge {
            background: #d4edda;
            color: #155724;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="confirmation-header">
            <div class="check-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Pedido Confirmado!</h1>
            <p>Seu pedido foi recebido com sucesso e está sendo processado.</p>
            <div class="order-number">
                Pedido #<?php echo str_pad($pedido_id, 6, '0', STR_PAD_LEFT); ?>
            </div>
        </div>
        
        <div class="confirmation-content">
            <div class="info-section">
                <h3><i class="fas fa-info-circle"></i> Informações do Pedido</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <p><span class="info-label">Data do Pedido:</span></p>
                        <p><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
                    </div>
                    <div class="info-item">
                        <p><span class="info-label">Cliente:</span></p>
                        <p><?php echo htmlspecialchars($pedido['cliente_nome']); ?></p>
                    </div>
                    <div class="info-item">
                        <p><span class="info-label">Forma de Pagamento:</span></p>
                        <p>
                            <?php 
                            $forma_pagamento = $pedido['forma_pagamento'] ?? $pedido['metodo_pagamento'] ?? 'Não informado';
                            echo htmlspecialchars($forma_pagamento);
                            if ($forma_pagamento == 'PIX'): ?>
                                <span class="pix-badge">15% OFF</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="info-item">
                        <p><span class="info-label">Status:</span></p>
                        <p>
                            <span class="status-badge status-<?php echo $pedido['status']; ?>">
                                <?php echo ucfirst($pedido['status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <h3><i class="fas fa-box"></i> Itens do Pedido</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço Unitário</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens_pedido as $item): 
                            $item_subtotal = isset($item['subtotal']) ? $item['subtotal'] : ($item['preco_unitario'] * $item['quantidade']);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['produto_nome']); ?></td>
                            <td><?php echo $item['quantidade']; ?></td>
                            <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                            <td>R$ <?php echo number_format($item_subtotal, 2, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                            <td class="total-amount">
                                R$ <?php echo number_format($valor_total, 2, ',', '.'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="comprovante">
                <div class="comprovante-header">
                    <h2><i class="fas fa-receipt"></i> Comprovante do Pedido</h2>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <p><span class="info-label">Código do Pedido:</span></p>
                        <p style="font-family: monospace; font-size: 1.1rem;">
                            #<?php echo str_pad($pedido_id, 6, '0', STR_PAD_LEFT); ?>
                        </p>
                    </div>
                    <div class="info-item">
                        <p><span class="info-label">Valor Total:</span></p>
                        <p class="total-amount">R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></p>
                    </div>
                    <div class="info-item">
                        <p><span class="info-label">Data de Emissão:</span></p>
                        <p><?php echo date('d/m/Y H:i:s'); ?></p>
                    </div>
                </div>
                <div class="comprovante-footer">
                    <p><i class="fas fa-info-circle"></i> Este comprovante não tem validade fiscal</p>
                </div>
            </div>
            
            <div class="actions no-print">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Imprimir Comprovante
                </button>
                <button onclick="window.location.href='produtos.php'" class="btn btn-outline">
                    <i class="fas fa-shopping-bag"></i> Continuar Comprando
                </button>
                <button onclick="window.location.href='index.php'" class="btn btn-outline">
                    <i class="fas fa-home"></i> Página Inicial
                </button>
            </div>
            
            <div class="comprovante-footer no-print" style="margin-top: 30px; font-size: 0.9rem; color: #666;">
                <p><i class="fas fa-envelope"></i> Um e-mail de confirmação foi enviado para: <?php echo htmlspecialchars($pedido['cliente_email']); ?></p>
                <p><i class="fas fa-clock"></i> Seu pedido será processado em até 48 horas úteis</p>
            </div>
        </div>
    </div>
    
    <script>
        // Confetti animation
        function showConfetti() {
            if (typeof confetti === 'function') {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }
        }
        
        // Show confetti on load
        window.onload = showConfetti;
        
        // Auto scroll to comprovante
        setTimeout(() => {
            document.querySelector('.comprovante').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }, 1000);
        
        // Copy order number to clipboard
        function copyOrderNumber() {
            const orderNumber = '#<?php echo str_pad($pedido_id, 6, '0', STR_PAD_LEFT); ?>';
            navigator.clipboard.writeText(orderNumber).then(() => {
                alert('Número do pedido copiado: ' + orderNumber);
            });
        }
        
        // Add click to copy on order number
        document.querySelector('.order-number').addEventListener('click', copyOrderNumber);
    </script>
    
    <!-- Confetti library -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
</body>
</html>