<?php
// carrinho.php - VERSÃO DE EMERGÊNCIA (COM DEBUG ATIVADO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tenta incluir conexao. Se falhar, avisa.
if (file_exists('conexao.php')) {
    require_once 'conexao.php';
} else {
    die("Erro Crítico: Arquivo 'conexao.php' não encontrado.");
}

require_once 'funcoes.php'; // Se existir

// Inicializa carrinho
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Inicializa presentes
if (!isset($_SESSION['presentes'])) {
    $_SESSION['presentes'] = [];
}

// Lógica de Remoção via Link (Fallback caso JS falhe)
if (isset($_GET['remover'])) {
    $idRemover = (int)$_GET['remover'];
    if (isset($_SESSION['carrinho'][$idRemover])) {
        unset($_SESSION['carrinho'][$idRemover]);
        if (isset($_SESSION['presentes'][$idRemover])) unset($_SESSION['presentes'][$idRemover]);
        if (isset($_SESSION['personalizados'][$idRemover])) unset($_SESSION['personalizados'][$idRemover]);
    }
    header('Location: carrinho.php');
    exit;
}

// Busca Produtos
$carrinhoItens = [];
$subtotal = 0;
$totalGeral = 0;

if (!empty($_SESSION['carrinho'])) {
    $ids = array_keys($_SESSION['carrinho']);
    // Remove IDs inválidos ou vazios
    $ids = array_filter($ids, function($v) { return $v > 0; });
    
    if (!empty($ids)) {
        $idsString = implode(',', array_map('intval', $ids));
        $sql = "SELECT * FROM produtos WHERE id IN ($idsString)";
        $result = $conexao->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                // Se o produto está no banco mas não na sessão (limpeza), pula
                if (!isset($_SESSION['carrinho'][$id])) continue;

                $qtd = $_SESSION['carrinho'][$id];
                $row['quantidade'] = $qtd;

                // Preço Personalizado
                if (isset($_SESSION['personalizados'][$id])) {
                    $row['preco'] = $_SESSION['personalizados'][$id]['preco_override'];
                    $row['descricao_extra'] = $_SESSION['personalizados'][$id]['texto'];
                } else {
                    $row['descricao_extra'] = '';
                }

                $row['subtotal'] = $row['preco'] * $qtd;

                // Presente - informações completas
                $row['presente'] = null;
                if (isset($_SESSION['presentes'][$id])) {
                    $pres = $_SESSION['presentes'][$id];
                    $row['presente'] = $pres;
                    // Soma o preço da embalagem, cartão, fita e mensagem
                    $precoPresenteTotal = $pres['packagingPrice'] + $pres['cardPrice'] + $pres['ribbonPrice'];
                    $row['subtotal'] += $precoPresenteTotal;
                    $row['preco_presente_total'] = $precoPresenteTotal;
                }

                $carrinhoItens[] = $row;
                $subtotal += $row['subtotal'];
            }
        }
    }
}

$frete = ($subtotal > 0) ? 15.00 : 0.00;
$total = $subtotal + $frete;

// Opções para o modal de presente - ATUALIZADAS
$embalagensPresente = [
    ['id' => 1, 'nome' => 'Caixa Luxo', 'preco' => 100.00, 'descricao' => 'Caixa luxuosa premium', 'img' => 'imgs/caixaluxo.png'],
    ['id' => 2, 'nome' => 'Caixa Elegante', 'preco' => 50.00, 'descricao' => 'Caixa elegante e sofisticada', 'img' => 'imgs/elegante.png'],
    ['id' => 3, 'nome' => 'Caixa Sustentável', 'preco' => 30.00, 'descricao' => 'Caixa ecológica minimalista', 'img' => 'imgs/sustentavel.png'],
];

$cartoesPresente = [
    ['id' => 1, 'nome' => 'Cartão Personalizado', 'preco' => 20.00, 'descricao' => 'Cartão com mensagem personalizada', 'img' => 'imgs/cartão.png'],
];

$fitasPresente = [
    ['id' => 1, 'nome' => 'Cetim Preto', 'preco' => 15.00, 'descricao' => 'Fita de cetim na cor preta', 'cor' => '#000000'],
    ['id' => 2, 'nome' => 'Cetim Dourado', 'preco' => 20.00, 'descricao' => 'Fita de cetim na cor dourada', 'cor' => '#FFD700'],
    ['id' => 3, 'nome' => 'Cetim Rosa', 'preco' => 15.00, 'descricao' => 'Fita de cetim na cor rosa', 'cor' => '#e91e63'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Carrinho - YARA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { background-color: #f8f8f8; font-family: 'Helvetica Neue', Arial, sans-serif; }
        .cart-wrapper { max-width: 1200px; margin: 40px auto; padding: 0 20px; display: flex; gap: 40px; flex-wrap: wrap; }
        .cart-items { flex: 2; min-width: 300px; }
        .cart-summary { flex: 1; min-width: 300px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #eee; height: fit-content; }
        
        .cart-item { display: flex; align-items: center; background: #fff; padding: 20px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #eee; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: relative; }
        .cart-img { width: 80px; height: 80px; object-fit: contain; margin-right: 20px; background: #f9f9f9; border-radius: 4px; }
        .cart-info { flex: 1; }
        .cart-info h4 { margin: 0 0 5px; color: #333; font-weight: 500; }
        .personalizacao-tag { font-size: 11px; color: #e91e63; background: #fff0f6; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-top: 4px; border: 1px solid #fe7db9; }
        
        .qtd-control { display: flex; align-items: center; border: 1px solid #eee; border-radius: 8px; margin-right: 20px; }
        .qtd-btn { background: none; border: none; width: 30px; height: 30px; cursor: pointer; font-weight: bold; color: #555; }
        .qtd-input { width: 30px; text-align: center; border: none; outline: none; font-size: 14px; }
        
        .item-price { font-weight: 600; font-size: 16px; color: #333; margin-right: 20px; min-width: 80px; text-align: right; }
        .btn-remove { color: #ccc; cursor: pointer; background: none; border: none; font-size: 18px; }
        .btn-remove:hover { color: #e91e63; }

        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; color: #666; font-size: 14px; }
        .summary-total { display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 18px; font-weight: 700; color: #e91e63; }
        
        .btn-checkout { width: 100%; padding: 15px; background: #000; color: white; border: 1px solid #000; border-radius: 8px; font-weight: 500; cursor: pointer; margin-top: 20px; text-transform: uppercase; display: block; text-align: center; text-decoration: none; }
        .btn-checkout:hover { background: #333; }

        /* Botão Presente - Estilo melhorado */
        .btn-presente {
            background: #fff;
            color: #e91e63;
            border: 1px solid #e91e63;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            cursor: pointer;
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }
        .btn-presente:hover {
            background: #e91e63;
            color: white;
        }
        .btn-presente.active {
            background: #e91e63;
            color: white;
            border-color: #e91e63;
        }
        .btn-presente i {
            font-size: 14px;
        }

        /* Detalhes do presente no item */
        .presente-detalhes {
            background: #fff0f6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            border-left: 3px solid #e91e63;
        }
        .presente-detalhes-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .presente-detalhes-label {
            color: #666;
        }
        .presente-detalhes-value {
            font-weight: 500;
            color: #333;
        }
        .presente-detalhes-preco {
            color: #e91e63;
            font-weight: 600;
        }
        .presente-detalhes-remover {
            color: #e91e63;
            cursor: pointer;
            font-size: 12px;
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
        }
        .presente-detalhes-remover:hover {
            text-decoration: underline;
        }
        .presente-total-item {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #ff9ec5;
            font-weight: 600;
            color: #333;
        }

        /* Modal de Presente - Baseado no código fornecido */
        .modal-presente {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .modal-presente-content {
            background: white;
            border-radius: 8px;
            width: 100%;
            max-width: 1000px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
        }
        .modal-header {
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            padding: 40px;
            text-align: center;
            position: relative;
        }
        .modal-title {
            font-size: 36px;
            font-weight: 300;
            letter-spacing: 2px;
            margin-bottom: 10px;
            font-family: "Cormorant Garamond", serif;
        }
        .modal-subtitle {
            font-size: 16px;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #333;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s;
        }
        .close-modal:hover {
            background: rgba(0,0,0,0.1);
        }
        .modal-body {
            padding: 40px;
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
        }
        .customization-preview {
            flex: 1;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .preview-box {
            width: 100%;
            max-width: 400px;
            height: 400px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid #eee;
        }
        .preview-box img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            transition: opacity 0.3s ease;
        }
        .preview-details {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        .preview-details h3 {
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            color: #666;
        }
        .detail-value {
            font-weight: 500;
        }
        .preview-price {
            margin-top: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: 300;
            color: #e91e63;
        }
        .customization-options {
            flex: 2;
            min-width: 300px;
        }
        .customization-steps {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        .customization-step {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        .step-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .step-title {
            font-size: 18px;
            font-weight: 500;
        }
        .step-number {
            background-color: #000;
            color: #fff;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
        }
        .option-item {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .option-item:hover {
            border-color: #000;
        }
        .option-item.selected {
            border-color: #000;
            background-color: #f9f9f9;
        }
        .option-image {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .option-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .option-name {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .option-price {
            font-size: 12px;
            color: #666;
        }
        .personalization-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 10px;
            resize: vertical;
            min-height: 80px;
        }
        .personalization-note {
            font-size: 12px;
            color: #666;
        }
        .modal-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        .btn-modal {
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
            border: none;
        }
        .btn-cancelar {
            background-color: transparent;
            color: #000;
            border: 1px solid #000;
        }
        .btn-cancelar:hover {
            background-color: #f5f5f5;
        }
        .btn-aplicar {
            background-color: #000;
            color: #fff;
            border: 1px solid #000;
        }
        .btn-aplicar:hover {
            background-color: #333;
        }

        @media(max-width: 768px) {
            .cart-item { flex-direction: column; align-items: flex-start; gap: 15px; }
            .cart-actions { width: 100%; display: flex; justify-content: space-between; align-items: center; }
            .btn-remove { position: absolute; top: 15px; right: 15px; }
            .modal-body { flex-direction: column; }
            .modal-header { padding: 30px 20px; }
            .modal-body { padding: 20px; }
            .options-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <h1 style="text-align:center; font-family: 'Cormorant Garamond', serif; margin-top: 40px; color:#000; font-weight: 300; letter-spacing: 2px;">Meu Carrinho</h1>

    <div class="cart-wrapper">
        
        <div class="cart-items">
            <?php if (count($carrinhoItens) > 0): ?>
                <?php foreach ($carrinhoItens as $item): ?>
                    <div class="cart-item" id="cart-item-<?php echo $item['id']; ?>">
                        <img src="imgs/<?php echo $item['imagem']; ?>" class="cart-img" onerror="this.src='imgs/produto-padrao.png'">
                        
                        <div class="cart-info">
                            <h4><?php echo htmlspecialchars($item['nome']); ?></h4>
                            
                            <?php if (!empty($item['descricao_extra'])): ?>
                                <span class="personalizacao-tag">
                                    <i class="fas fa-gem"></i> <?php echo htmlspecialchars($item['descricao_extra']); ?>
                                </span><br>
                            <?php endif; ?>
                            
                            <!-- Botão Presente -->
                            <button class="btn-presente <?php echo $item['presente'] ? 'active' : ''; ?>" 
                                    onclick="abrirModalPresente(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['nome']); ?>', <?php echo $item['preco']; ?>)">
                                <i class="fas fa-gift"></i>
                                <?php echo $item['presente'] ? 'Presente Ativo' : 'Definir como Presente'; ?>
                            </button>
                            
                            <!-- Detalhes do Presente -->
                            <?php if ($item['presente']): ?>
                                <div class="presente-detalhes">
                                    <div class="presente-detalhes-item">
                                        <span class="presente-detalhes-label">Embalagem:</span>
                                        <span class="presente-detalhes-value"><?php echo htmlspecialchars($item['presente']['packaging']); ?></span>
                                        <span class="presente-detalhes-preco">+R$ <?php echo number_format($item['presente']['packagingPrice'], 2, ',', '.'); ?></span>
                                    </div>
                                    <div class="presente-detalhes-item">
                                        <span class="presente-detalhes-label">Cartão:</span>
                                        <span class="presente-detalhes-value"><?php echo htmlspecialchars($item['presente']['card']); ?></span>
                                        <span class="presente-detalhes-preco">+R$ <?php echo number_format($item['presente']['cardPrice'], 2, ',', '.'); ?></span>
                                    </div>
                                    <div class="presente-detalhes-item">
                                        <span class="presente-detalhes-label">Fita:</span>
                                        <span class="presente-detalhes-value"><?php echo htmlspecialchars($item['presente']['ribbon']); ?></span>
                                        <span class="presente-detalhes-preco">+R$ <?php echo number_format($item['presente']['ribbonPrice'], 2, ',', '.'); ?></span>
                                    </div>
                                    <?php if (!empty($item['presente']['message'])): ?>
                                        <div class="presente-detalhes-item">
                                            <span class="presente-detalhes-label">Mensagem:</span>
                                            <span class="presente-detalhes-value">"<?php echo htmlspecialchars($item['presente']['message']); ?>"</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="presente-detalhes-item presente-total-item">
                                        <span class="presente-detalhes-label">Total do Presente:</span>
                                        <span class="presente-detalhes-preco">+R$ <?php echo number_format($item['preco_presente_total'], 2, ',', '.'); ?></span>
                                    </div>
                                    <a href="#" class="presente-detalhes-remover" onclick="removerPresente(<?php echo $item['id']; ?>)">
                                        <i class="fas fa-times"></i> Remover Presente
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="cart-actions" style="display:flex; align-items:center;">
                            <div class="qtd-control">
                                <button class="qtd-btn" onclick="alterarQtd(<?php echo $item['id']; ?>, -1)">-</button>
                                <input type="text" class="qtd-input" value="<?php echo $item['quantidade']; ?>" readonly>
                                <button class="qtd-btn" onclick="alterarQtd(<?php echo $item['id']; ?>, 1)">+</button>
                            </div>
                            
                            <div class="item-price">R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></div>
                            
                            <button class="btn-remove" onclick="perguntarRemover(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center; padding: 60px; background:white; border-radius:8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <i class="fas fa-shopping-basket" style="font-size: 50px; color: #ddd; margin-bottom: 20px;"></i>
                    <p style="color:#666;">Seu carrinho está vazio.</p>
                    <a href="produtos.php" style="color:#e91e63; font-weight:600; text-decoration:none;">Ir às compras</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (count($carrinhoItens) > 0): ?>
        <div class="cart-summary">
            <h3 style="margin-top:0; color:#333;">Resumo</h3>
            
            <div class="summary-row">
                <span>Subtotal dos Produtos</span>
                <span>R$ <?php echo number_format($subtotal - array_sum(array_column(array_filter($carrinhoItens, function($item) { 
                    return isset($item['preco_presente_total']); 
                }), 'preco_presente_total')), 2, ',', '.'); ?></span>
            </div>
            
            <?php 
            $totalPresentes = 0;
            foreach ($carrinhoItens as $item) {
                if (isset($item['preco_presente_total'])) {
                    $totalPresentes += $item['preco_presente_total'];
                }
            }
            if ($totalPresentes > 0): ?>
            <div class="summary-row">
                <span>Kit Presente</span>
                <span>+R$ <?php echo number_format($totalPresentes, 2, ',', '.'); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="summary-row">
                <span>Frete</span>
                <span>R$ <?php echo number_format($frete, 2, ',', '.'); ?></span>
            </div>
            
            <div class="summary-total">
                <span>Total</span>
                <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
            </div>

            <a href="finaliza_pagamento.php" class="btn-checkout">FINALIZAR COMPRA</a>
            
            <div style="text-align:center; margin-top:15px;">
                <a href="produtos.php" style="color:#e91e63; text-decoration:none; font-size:14px;">Continuar Comprando</a>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Modal de Presente ATUALIZADO -->
    <div class="modal-presente" id="modalPresente">
        <div class="modal-presente-content">
            <div class="modal-header">
                <button class="close-modal" onclick="fecharModalPresente()">&times;</button>
                <h2 class="modal-title">A ARTE DE PRESENTEAR</h2>
                <p class="modal-subtitle">Personalize o seu presente com nossas opções exclusivas</p>
            </div>
            
            <div class="modal-body">
                <div class="customization-preview">
                    
                    <div class="preview-details">
                        <h3>Resumo do Presente</h3>
                        <div class="detail-item">
                            <span class="detail-label">Embalagem:</span>
                            <span id="detailPackaging" class="detail-value">Caixa Luxo</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Cartão:</span>
                            <span id="detailCard" class="detail-value">Personalizado</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Fita:</span>
                            <span id="detailRibbon" class="detail-value">Cetim Preto</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Mensagem:</span>
                            <span id="detailMessage" class="detail-value">-</span>
                        </div>
                        <div class="preview-price">Adicional: R$ <span id="totalPrice">135,00</span></div>
                    </div>
                </div>
                
                <div class="customization-options">
                    <div class="customization-steps">
                        <!-- 1. Embalagem -->
                        <div class="customization-step">
                            <div class="step-header">
                                <div class="step-title">1. Escolha a embalagem</div>
                                <div class="step-number">1</div>
                            </div>
                            <div class="options-grid" id="packagingOptions">
                                <div class="option-item selected" 
                                     data-packaging="Caixa Luxo" 
                                     data-price="100.00" 
                                     data-img="imgs/caixaluxo.png">
                                    <div class="option-image">
                                        <img src="imgs/caixaluxo.png" onerror="this.src='imgs/produto-padrao.png'">
                                    </div>
                                    <div class="option-name">Caixa Luxo</div>
                                    <div class="option-price">+R$ 100,00</div>
                                </div>
                                <div class="option-item" 
                                     data-packaging="Caixa Elegante" 
                                     data-price="50.00" 
                                     data-img="imgs/elegante.png">
                                    <div class="option-image">
                                        <img src="imgs/elegante.png" onerror="this.src='imgs/produto-padrao.png'">
                                    </div>
                                    <div class="option-name">Caixa Elegante</div>
                                    <div class="option-price">+R$ 50,00</div>
                                </div>
                                <div class="option-item" 
                                     data-packaging="Caixa Sustentável" 
                                     data-price="30.00" 
                                     data-img="imgs/sustentavel.png">
                                    <div class="option-image">
                                        <img src="imgs/sustentavel.png" onerror="this.src='imgs/produto-padrao.png'">
                                    </div>
                                    <div class="option-name">Caixa Sustentável</div>
                                    <div class="option-price">+R$ 30,00</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 2. Cartão (SOMENTE UMA OPÇÃO) -->
                        <div class="customization-step">
                            <div class="step-header">
                                <div class="step-title">2. Cartão Personalizado</div>
                                <div class="step-number">2</div>
                            </div>
                            <div class="options-grid" id="cardOptions">
                                <div class="option-item selected" 
                                     data-card="Cartão Personalizado" 
                                     data-price="20.00">
                                    <div class="option-image">
                                        <img src="imgs/cartao.png" onerror="this.src='imgs/produto-padrao.png'">
                                    </div>
                                    <div class="option-name">Cartão Personalizado</div>
                                    <div class="option-price">+R$ 20,00</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 3. Fita (SEM JUTA NATURAL) -->
                        <div class="customization-step">
                            <div class="step-header">
                                <div class="step-title">3. Escolha a fita</div>
                                <div class="step-number">3</div>
                            </div>
                            <div class="options-grid" id="ribbonOptions">
                                <div class="option-item selected" 
                                     data-ribbon="Cetim Preto" 
                                     data-price="15.00">
                                    <div class="option-image">
                                        <div style="width:100%;height:100%;background:#000000;border-radius:4px;"></div>
                                    </div>
                                    <div class="option-name">Cetim Preto</div>
                                    <div class="option-price">+R$ 15,00</div>
                                </div>
                                <div class="option-item" 
                                     data-ribbon="Cetim Dourado" 
                                     data-price="20.00">
                                    <div class="option-image">
                                        <div style="width:100%;height:100%;background:#FFD700;border-radius:4px;"></div>
                                    </div>
                                    <div class="option-name">Cetim Dourado</div>
                                    <div class="option-price">+R$ 20,00</div>
                                </div>
                                <div class="option-item" 
                                     data-ribbon="Cetim Rosa" 
                                     data-price="15.00">
                                    <div class="option-image">
                                        <div style="width:100%;height:100%;background:#e91e63;border-radius:4px;"></div>
                                    </div>
                                    <div class="option-name">Cetim Rosa</div>
                                    <div class="option-price">+R$ 15,00</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 4. Mensagem -->
                        <div class="customization-step">
                            <div class="step-header">
                                <div class="step-title">4. Personalize a mensagem</div>
                                <div class="step-number">4</div>
                            </div>
                            <div class="personalization-section">
                                <p style="margin-bottom: 10px;">Adicione uma mensagem ao cartão:</p>
                                <textarea class="personalization-input" id="messageInput" placeholder="Digite sua mensagem especial (máx. 150 caracteres)" rows="4" maxlength="150"></textarea>
                                <div class="personalization-note">Sua mensagem será impressa e entregue com o presente.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <button class="btn-modal btn-cancelar" onclick="fecharModalPresente()">CANCELAR</button>
                        <button class="btn-modal btn-aplicar" onclick="aplicarPresente()">APLICAR PRESENTE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        let produtoIdAtual = null;
        let produtoNomeAtual = null;
        let produtoPrecoBase = 0;
        
        // Dados do presente em edição - VALORES ATUALIZADOS
        let currentData = {
            packaging: 'Caixa Luxo',
            packagingPrice: 100.00,
            card: 'Cartão Personalizado',
            cardPrice: 20.00,
            ribbon: 'Cetim Preto',
            ribbonPrice: 15.00,
            message: ''
        };

        function abrirModalPresente(produtoId, produtoNome, precoBase) {
            produtoIdAtual = produtoId;
            produtoNomeAtual = produtoNome;
            produtoPrecoBase = precoBase;
            
            const modal = document.getElementById('modalPresente');
            
            // Resetar dados para valores padrão
            currentData = {
                packaging: 'Caixa Luxo',
                packagingPrice: 100.00,
                card: 'Cartão Personalizado',
                cardPrice: 20.00,
                ribbon: 'Cetim Preto',
                ribbonPrice: 15.00,
                message: ''
            };
            
            // Resetar seleções visuais
            resetSelections();
            
            // Preencher com dados existentes se houver
            <?php if (isset($_SESSION['presentes'])): ?>
                const presentes = <?php echo json_encode($_SESSION['presentes']); ?>;
                if (presentes[produtoId]) {
                    const presente = presentes[produtoId];
                    currentData = presente;
                    
                    // Selecionar embalagem
                    const embalagemEl = document.querySelector(`#packagingOptions .option-item[data-packaging="${presente.packaging}"]`);
                    if (embalagemEl) {
                        selecionarOpcao(embalagemEl, 'packaging');
                    }
                    
                    // Selecionar cartão (sempre o mesmo)
                    const cartaoEl = document.querySelector('#cardOptions .option-item');
                    if (cartaoEl) {
                        cartaoEl.classList.add('selected');
                    }
                    
                    // Selecionar fita
                    const fitaEl = document.querySelector(`#ribbonOptions .option-item[data-ribbon="${presente.ribbon}"]`);
                    if (fitaEl) {
                        selecionarOpcao(fitaEl, 'ribbon');
                    }
                    
                    // Preencher mensagem
                    if (presente.message) {
                        document.getElementById('messageInput').value = presente.message;
                        currentData.message = presente.message;
                    }
                }
            <?php endif; ?>
            
            modal.style.display = 'flex';
            updateUI();
        }
        
        function resetSelections() {
            // Limpar todas as seleções
            document.querySelectorAll('.option-item').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Selecionar padrões
            document.querySelector('#packagingOptions .option-item:first-child').classList.add('selected');
            document.querySelector('#cardOptions .option-item:first-child').classList.add('selected');
            document.querySelector('#ribbonOptions .option-item:first-child').classList.add('selected');
            document.getElementById('messageInput').value = '';
        }
        
        function fecharModalPresente() {
            document.getElementById('modalPresente').style.display = 'none';
        }
        
        function setupOptions(containerId, dataKey, priceKey, imgKey = null) {
            const options = document.querySelectorAll(`#${containerId} .option-item`);
            
            options.forEach(opt => {
                opt.addEventListener('click', function() {
                    selecionarOpcao(this, dataKey, priceKey, imgKey);
                });
            });
        }
        
        function selecionarOpcao(elemento, dataKey, priceKey = null, imgKey = null) {
            // Remover seleção de todos os itens do mesmo grupo
            const container = elemento.closest('.options-grid');
            container.querySelectorAll('.option-item').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Adicionar seleção ao item clicado
            elemento.classList.add('selected');
            
            // Atualizar dados
            currentData[dataKey] = elemento.getAttribute(`data-${dataKey}`);
            if (priceKey) {
                currentData[priceKey] = parseFloat(elemento.getAttribute('data-price'));
            }
            
            updateUI();
        }
        
        function updateUI() {
            // Atualizar detalhes
            document.getElementById('detailPackaging').textContent = currentData.packaging;
            document.getElementById('detailCard').textContent = currentData.card;
            document.getElementById('detailRibbon').textContent = currentData.ribbon;
            document.getElementById('detailMessage').textContent = currentData.message || '-';
            
            // Calcular e mostrar total
            const total = currentData.packagingPrice + currentData.cardPrice + currentData.ribbonPrice;
            document.getElementById('totalPrice').textContent = total.toLocaleString('pt-BR', {
                minimumFractionDigits: 2
            });
        }
        
        function aplicarPresente() {
            if (!produtoIdAtual) return;
            
            // Atualizar mensagem dos dados
            currentData.message = document.getElementById('messageInput').value;
            
            const fd = new FormData();
            fd.append('acao', 'adicionar_presente');
            fd.append('produto_id', produtoIdAtual);
            fd.append('presente_data', JSON.stringify(currentData));
            
            fetch('processa_form.php', {
                method: 'POST',
                body: fd
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fecharModalPresente();
                    // Recarregar a página para atualizar os preços
                    location.reload();
                } else {
                    Swal.fire({
                        title: 'Erro!',
                        text: data.message || 'Ocorreu um erro ao aplicar o presente.',
                        icon: 'error',
                        confirmButtonColor: '#e91e63'
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    title: 'Erro!',
                    text: 'Ocorreu um erro ao processar a requisição.',
                    icon: 'error',
                    confirmButtonColor: '#e91e63'
                });
            });
        }
        
        function removerPresente(produtoId) {
            Swal.fire({
                title: 'Remover presente?',
                text: "O presente será removido deste item.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e91e63',
                cancelButtonColor: '#333',
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const fd = new FormData();
                    fd.append('acao', 'remover_presente');
                    fd.append('produto_id', produtoId);
                    
                    fetch('processa_form.php', {
                        method: 'POST',
                        body: fd
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
                }
            });
        }
        
        function alterarQtd(id, delta) {
            const input = document.querySelector(`button[onclick="alterarQtd(${id}, 1)"]`).previousElementSibling;
            let qtdAtual = parseInt(input.value);
            
            if (delta === -1 && qtdAtual <= 1) {
                perguntarRemover(id);
                return;
            }

            const fd = new FormData();
            fd.append('acao', 'atualizar_carrinho');
            fd.append('produto_id', id);
            fd.append('quantidade', qtdAtual + delta);

            fetch('processa_form.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if(data.success) location.reload();
            });
        }

        function perguntarRemover(id) {
            Swal.fire({
                title: 'Remover item?',
                text: "Tem certeza que deseja retirar este produto?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e91e63',
                cancelButtonColor: '#333',
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'carrinho.php?remover=' + id;
                }
            });
        }

        // Configurar eventos após o DOM carregar
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar opções (cartão não precisa de configuração especial pois só tem uma opção)
            setupOptions('packagingOptions', 'packaging', 'packagingPrice');
            setupOptions('ribbonOptions', 'ribbon', 'ribbonPrice');
            
            // Configurar cartão (só tem uma opção, sempre selecionado)
            const cartaoOption = document.querySelector('#cardOptions .option-item');
            if (cartaoOption) {
                cartaoOption.addEventListener('click', function() {
                    // Cartão sempre selecionado, não faz nada ao clicar
                });
            }
            
            // Configurar textarea de mensagem
            document.getElementById('messageInput').addEventListener('input', function() {
                currentData.message = this.value;
                updateUI();
            });
            
            // Fechar modal ao clicar fora
            window.onclick = function(event) {
                const modal = document.getElementById('modalPresente');
                if (event.target === modal) {
                    fecharModalPresente();
                }
            };
            
            // Atualizar UI inicial
            updateUI();
        });
    </script>
</body>
</html>