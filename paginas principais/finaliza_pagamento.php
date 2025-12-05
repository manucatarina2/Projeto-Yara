<?php
// finaliza_pagamento.php - VERSÃO COM API DE CEP
session_start();
require_once 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Verificar Carrinho
if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
    header('Location: produtos.php');
    exit;
}

// Buscar Produtos e Calcular Totais
$carrinhoItens = [];
$subtotal = 0;
$ids = array_keys($_SESSION['carrinho']);

if (!empty($ids)) {
    $idsString = implode(',', array_map('intval', $ids));
    $sql = "SELECT id, nome, preco, imagem FROM produtos WHERE id IN ($idsString)";
    $result = $conexao->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $qtd = $_SESSION['carrinho'][$id];
            
            $row['quantidade'] = $qtd;
            $preco_total = $row['preco'] * $qtd;

            $carrinhoItens[] = $row;
            $subtotal += $preco_total;
        }
    }
}

// Calcular frete e total
$frete = ($subtotal > 0) ? 15.00 : 0;
$total = $subtotal + $frete;

// Salvar dados na sessão
$_SESSION['total_compra'] = $total;
$_SESSION['itens_carrinho'] = $carrinhoItens;
$_SESSION['subtotal'] = $subtotal;
$_SESSION['frete'] = $frete;

// Método de pagamento
$metodo_pagamento = isset($_GET['metodo']) ? $_GET['metodo'] : 'pix';

// Processar pagamento se for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_pagamento'])) {
    
    // Verificar a estrutura da tabela pedidos
    $check_table = $conexao->query("DESCRIBE pedidos");
    $columns = [];
    while ($col = $check_table->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
    
    // Verificar quais colunas existem
    $has_valor_total = in_array('valor_total', $columns);
    
    // Montar query baseada nas colunas existentes
    if ($has_valor_total) {
        // Se tem coluna 'valor_total'
        $sql_pedido = "INSERT INTO pedidos (usuario_id, data_pedido, valor_total, status, forma_pagamento, endereco_entrega) VALUES (?, NOW(), ?, 'pendente', ?, ?)";
    } else {
        // Se não tem 'valor_total', usar 'total'
        $sql_pedido = "INSERT INTO pedidos (usuario_id, data_pedido, total, status, metodo_pagamento, endereco_entrega) VALUES (?, NOW(), ?, 'pendente', ?, ?)";
    }
    
    $stmt = $conexao->prepare($sql_pedido);
    
    // Endereço completo
    $endereco = $_POST['rua'] . ', ' . $_POST['numero'];
    if (!empty($_POST['complemento'])) {
        $endereco .= ' - ' . $_POST['complemento'];
    }
    $endereco .= ' - ' . $_POST['bairro'] . ' - ' . $_POST['cidade'] . '/' . $_POST['estado'] . ' - CEP: ' . $_POST['cep'];
    
    // Ajustar o nome do parâmetro do método de pagamento
    $forma_pagamento = $metodo_pagamento;
    if ($metodo_pagamento == 'pix') $forma_pagamento = 'PIX';
    if ($metodo_pagamento == 'cartao_credito') $forma_pagamento = 'Cartão de Crédito';
    if ($metodo_pagamento == 'cartao_debito') $forma_pagamento = 'Cartão de Débito';
    if ($metodo_pagamento == 'boleto') $forma_pagamento = 'Boleto';
    
    // Ajustar total para PIX (15% de desconto)
    $total_pedido = $total;
    if ($metodo_pagamento == 'pix') {
        $total_pedido = $total * 0.85; // 15% de desconto
    }
    
    $stmt->bind_param("idss", 
        $_SESSION['usuario']['id'],
        $total_pedido,
        $forma_pagamento,
        $endereco
    );
    
    if ($stmt->execute()) {
        $pedido_id = $conexao->insert_id;
        
        // Verificar estrutura da tabela pedido_itens
        $check_itens = $conexao->query("DESCRIBE pedido_itens");
        $colunas_itens = [];
        while ($col = $check_itens->fetch_assoc()) {
            $colunas_itens[] = $col['Field'];
        }
        
        $has_subtotal = in_array('subtotal', $colunas_itens);
        
        // Inserir itens do pedido
        foreach ($carrinhoItens as $item) {
            $preco_final = $item['preco'];
            $qtd = $item['quantidade'];
            
            if ($has_subtotal) {
                // Se tem coluna subtotal
                $sql_item = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
                $preco_total = $preco_final * $qtd;
                
                $stmt_item = $conexao->prepare($sql_item);
                $stmt_item->bind_param("iiidd", 
                    $pedido_id,
                    $item['id'],
                    $qtd,
                    $preco_final,
                    $preco_total
                );
            } else {
                // Se não tem coluna subtotal
                $sql_item = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
                
                $stmt_item = $conexao->prepare($sql_item);
                $stmt_item->bind_param("iiid", 
                    $pedido_id,
                    $item['id'],
                    $qtd,
                    $preco_final
                );
            }
            
            $stmt_item->execute();
        }
        
        // Salvar endereço do usuário no banco de dados
        $sql_save_address = "INSERT INTO enderecos (usuario_id, cep, rua, numero, complemento, bairro, cidade, estado) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                             ON DUPLICATE KEY UPDATE 
                             cep = VALUES(cep), rua = VALUES(rua), numero = VALUES(numero), 
                             complemento = VALUES(complemento), bairro = VALUES(bairro), 
                             cidade = VALUES(cidade), estado = VALUES(estado)";
        
        $stmt_addr = $conexao->prepare($sql_save_address);
        $stmt_addr->bind_param("ississss",
            $_SESSION['usuario']['id'],
            $_POST['cep'],
            $_POST['rua'],
            $_POST['numero'],
            $_POST['complemento'],
            $_POST['bairro'],
            $_POST['cidade'],
            $_POST['estado']
        );
        $stmt_addr->execute();
        
        // Limpar carrinho
        unset($_SESSION['carrinho']);
        unset($_SESSION['itens_carrinho']);
        
        // Redirecionar para confirmação
        header("Location: pedido_confirmado.php?id=$pedido_id");
        exit;
    } else {
        echo "Erro ao inserir pedido: " . $stmt->error;
        exit;
    }
}

// Buscar endereço do usuário
$enderecoUsuario = [
    'cep' => '', 'numero' => '', 'rua' => '', 'bairro' => '', 
    'cidade' => '', 'estado' => '', 'complemento' => ''
];

$uid = $_SESSION['usuario']['id'];
$sqlEnd = "SELECT * FROM enderecos WHERE usuario_id = ? ORDER BY id DESC LIMIT 1";
$stmtEnd = $conexao->prepare($sqlEnd);
$stmtEnd->bind_param("i", $uid);
$stmtEnd->execute();
$resEnd = $stmtEnd->get_result();
if ($resEnd && $resEnd->num_rows > 0) {
    $enderecoUsuario = $resEnd->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pagamento - YARA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            text-align: center;
            padding: 30px 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 2rem;
            font-weight: 600;
            color: #e91e63;
            margin-bottom: 10px;
        }
        
        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }
        
        @media (max-width: 768px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e91e63;
            font-size: 1.3rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #e91e63;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .payment-method {
            position: relative;
        }
        
        .payment-method input {
            display: none;
        }
        
        .method-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .payment-method input:checked + .method-label {
            border-color: #e91e63;
            background: #fff5f8;
            color: #e91e63;
        }
        
        .method-label i {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .method-label span {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .pix-info {
            background: #f0f9f0;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
            margin-top: 20px;
        }
        
        .pix-code {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            margin: 15px 0;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .item-info h4 {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .item-info p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .item-price {
            font-weight: 600;
            color: #333;
        }
        
        .totals {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #666;
        }
        
        .grand-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: #e91e63;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }
        
        .btn {
            width: 100%;
            padding: 16px;
            background: #e91e63;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .btn:hover {
            background: #d81b60;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #e91e63;
            color: #e91e63;
            margin-top: 10px;
        }
        
        .btn-outline:hover {
            background: #e91e63;
            color: white;
        }
        
        .secure-notice {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f0f9f0;
            border-radius: 8px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .secure-notice i {
            color: #4CAF50;
            margin-right: 8px;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: #e91e63;
        }
        
        .loading i {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .error {
            color: #e74c3c;
            background: #fee;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #fcc;
        }
        
        .success {
            color: #27ae60;
            background: #dfffd9;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #bff;
        }
        
        .cep-group {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        
        .cep-group .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        .cep-group .btn-cep {
            padding: 12px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .cep-group .btn-cep:hover {
            background: #45a049;
        }
        
        .cep-loading {
            display: none;
            text-align: center;
            padding: 5px;
            color: #e91e63;
            font-size: 0.9rem;
        }
        
        .cep-loading i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .address-loading {
            display: none;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">YARA</div>
        <p>Finalizar Pagamento</p>
    </header>
    
    <div class="container">
        <?php if (isset($_GET['error'])): ?>
            <div class="error">
                <i class="fas fa-exclamation-triangle"></i>
                <?php 
                    $errors = [
                        'estoque' => 'Alguns produtos não têm estoque suficiente.',
                        'pagamento' => 'Erro ao processar pagamento. Tente novamente.',
                        'endereco' => 'Preencha todos os campos do endereço.',
                        'cep' => 'CEP inválido ou não encontrado.'
                    ];
                    echo $errors[$_GET['error']] ?? 'Ocorreu um erro.';
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                Pedido realizado com sucesso!
            </div>
        <?php endif; ?>
        
        <div class="checkout-grid">
            <!-- Formulário -->
            <div class="checkout-form">
                <form id="paymentForm" method="POST">
                    <div class="card">
                        <h2><i class="fas fa-user"></i> Dados Pessoais</h2>
                        
                        <div class="form-group">
                            <label for="nome">Nome Completo *</label>
                            <input type="text" id="nome" name="nome" class="form-control" required 
                                   value="<?php echo htmlspecialchars($_SESSION['usuario']['nome'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">E-mail *</label>
                                <input type="email" id="email" name="email" class="form-control" required 
                                       value="<?php echo htmlspecialchars($_SESSION['usuario']['email'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="telefone">Telefone *</label>
                                <input type="tel" id="telefone" name="telefone" class="form-control" required 
                                       value="<?php echo htmlspecialchars($_SESSION['usuario']['telefone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <h2><i class="fas fa-map-marker-alt"></i> Endereço de Entrega</h2>
                        
                        <div class="form-group">
                            <label for="cep">CEP *</label>
                            <div class="cep-group">
                                <div class="form-group">
                                    <input type="text" id="cep" name="cep" class="form-control" required 
                                           value="<?php echo htmlspecialchars($enderecoUsuario['cep']); ?>"
                                           placeholder="00000-000">
                                </div>
                                <button type="button" class="btn-cep" id="buscarCep">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                            <div class="cep-loading" id="cepLoading">
                                <i class="fas fa-spinner fa-spin"></i> Buscando endereço...
                            </div>
                        </div>
                        
                        <div id="addressFields">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="rua">Rua *</label>
                                    <input type="text" id="rua" name="rua" class="form-control" required 
                                           value="<?php echo htmlspecialchars($enderecoUsuario['rua']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="numero">Número *</label>
                                    <input type="text" id="numero" name="numero" class="form-control" required 
                                           value="<?php echo htmlspecialchars($enderecoUsuario['numero']); ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bairro">Bairro *</label>
                                    <input type="text" id="bairro" name="bairro" class="form-control" required 
                                           value="<?php echo htmlspecialchars($enderecoUsuario['bairro']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="cidade">Cidade *</label>
                                    <input type="text" id="cidade" name="cidade" class="form-control" required 
                                           value="<?php echo htmlspecialchars($enderecoUsuario['cidade']); ?>">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="estado">Estado *</label>
                                    <input type="text" id="estado" name="estado" class="form-control" required 
                                           value="<?php echo htmlspecialchars($enderecoUsuario['estado']); ?>" maxlength="2">
                                </div>
                                <div class="form-group">
                                    <label for="complemento">Complemento (opcional)</label>
                                    <input type="text" id="complemento" name="complemento" class="form-control" 
                                           value="<?php echo htmlspecialchars($enderecoUsuario['complemento']); ?>"
                                           placeholder="Apto, Bloco, etc.">
                                </div>
                            </div>
                        </div>
                        
                        <div class="address-loading" id="addressLoading">
                            <i class="fas fa-spinner fa-spin"></i> Carregando campos de endereço...
                        </div>
                    </div>
                    
                    <div class="card">
                        <h2><i class="fas fa-credit-card"></i> Forma de Pagamento</h2>
                        
                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" name="metodo" id="metodo_pix" value="pix" 
                                       <?php echo $metodo_pagamento == 'pix' ? 'checked' : ''; ?> 
                                       onchange="window.location.href='finaliza_pagamento.php?metodo=pix'">
                                <label for="metodo_pix" class="method-label">
                                    <i class="fas fa-qrcode"></i>
                                    <span>PIX</span>
                                    <small style="color: #4CAF50; margin-top: 5px;">15% OFF</small>
                                </label>
                            </div>
                            
                            <div class="payment-method">
                                <input type="radio" name="metodo" id="metodo_credito" value="cartao_credito"
                                       <?php echo $metodo_pagamento == 'cartao_credito' ? 'checked' : ''; ?>
                                       onchange="window.location.href='finaliza_pagamento.php?metodo=cartao_credito'">
                                <label for="metodo_credito" class="method-label">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Cartão</span>
                                    <small>Crédito</small>
                                </label>
                            </div>
                            
                            <div class="payment-method">
                                <input type="radio" name="metodo" id="metodo_debito" value="cartao_debito"
                                       <?php echo $metodo_pagamento == 'cartao_debito' ? 'checked' : ''; ?>
                                       onchange="window.location.href='finaliza_pagamento.php?metodo=cartao_debito'">
                                <label for="metodo_debito" class="method-label">
                                    <i class="far fa-credit-card"></i>
                                    <span>Cartão</span>
                                    <small>Débito</small>
                                </label>
                            </div>
                            
                            <div class="payment-method">
                                <input type="radio" name="metodo" id="metodo_boleto" value="boleto"
                                       <?php echo $metodo_pagamento == 'boleto' ? 'checked' : ''; ?>
                                       onchange="window.location.href='finaliza_pagamento.php?metodo=boleto'">
                                <label for="metodo_boleto" class="method-label">
                                    <i class="fas fa-barcode"></i>
                                    <span>Boleto</span>
                                    <small>Bancário</small>
                                </label>
                            </div>
                        </div>
                        
                        <?php if ($metodo_pagamento == 'pix'): ?>
                        <div class="pix-info">
                            <h3><i class="fas fa-qrcode"></i> Pagamento via PIX</h3>
                            <p>Você tem <strong>15% de desconto</strong> pagando com PIX!</p>
                            <p>Valor com desconto: <strong>R$ <?php echo number_format($total * 0.85, 2, ',', '.'); ?></strong></p>
                            <div class="pix-code">
                                00020126360014BR.GOV.BCB.PIX0114+552198765432152040000530398654058.00BR5909LOJA YARA6008SAO PAULO62070503***6304C6AB
                            </div>
                            <p><small><i class="fas fa-clock"></i> Este código PIX expira em 30 minutos</small></p>
                        </div>
                        <?php elseif ($metodo_pagamento == 'cartao_credito' || $metodo_pagamento == 'cartao_debito'): ?>
                        <div class="form-group">
                            <label for="card_nome">Nome no Cartão *</label>
                            <input type="text" id="card_nome" name="card_nome" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="card_numero">Número do Cartão *</label>
                            <input type="text" id="card_numero" name="card_numero" class="form-control" required 
                                   placeholder="0000 0000 0000 0000">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="card_validade">Validade (MM/AA) *</label>
                                <input type="text" id="card_validade" name="card_validade" class="form-control" required 
                                       placeholder="MM/AA">
                            </div>
                            <div class="form-group">
                                <label for="card_cvv">CVV *</label>
                                <input type="text" id="card_cvv" name="card_cvv" class="form-control" required 
                                       placeholder="123">
                            </div>
                        </div>
                        
                        <?php if ($metodo_pagamento == 'cartao_credito'): ?>
                        <div class="form-group">
                            <label for="parcelas">Parcelas</label>
                            <select id="parcelas" name="parcelas" class="form-control">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>">
                                        <?php echo $i == 1 ? 'À vista' : "$i x de R$ " . number_format($total / $i, 2, ',', '.'); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <?php elseif ($metodo_pagamento == 'boleto'): ?>
                        <div class="pix-info">
                            <h3><i class="fas fa-barcode"></i> Boleto Bancário</h3>
                            <p>O boleto será gerado após a confirmação do pedido.</p>
                            <p>Vencimento: <?php echo date('d/m/Y', strtotime('+3 days')); ?></p>
                            <p><strong>Valor: R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></p>
                            <p><small>O prazo de compensação é de até 3 dias úteis após o pagamento.</small></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="confirmar_pagamento" value="1">
                    <button type="submit" class="btn" id="confirmBtn">
                        <i class="fas fa-lock"></i> CONFIRMAR PAGAMENTO
                    </button>
                    
                    <button type="button" class="btn btn-outline" onclick="window.history.back()">
                        <i class="fas fa-arrow-left"></i> VOLTAR
                    </button>
                </form>
                
                <div class="loading" id="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Processando pagamento...</p>
                </div>
            </div>
            
            <!-- Resumo do pedido -->
            <div class="checkout-summary">
                <div class="card" style="position: sticky; top: 20px;">
                    <h2><i class="fas fa-shopping-bag"></i> Resumo do Pedido</h2>
                    
                    <div class="order-items" style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                        <?php if (empty($carrinhoItens)): ?>
                            <p style="text-align: center; color: #666; padding: 20px;">
                                <i class="fas fa-shopping-cart"></i><br>
                                Carrinho vazio
                            </p>
                        <?php else: ?>
                            <?php foreach ($carrinhoItens as $item): ?>
                            <div class="order-item">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($item['nome']); ?></h4>
                                    <p>Qtd: <?php echo $item['quantidade']; ?> × R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></p>
                                </div>
                                <div class="item-price">
                                    R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="totals">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
                        </div>
                        <div class="total-row">
                            <span>Frete</span>
                            <span>R$ <?php echo number_format($frete, 2, ',', '.'); ?></span>
                        </div>
                        
                        <?php if ($metodo_pagamento == 'pix'): ?>
                        <div class="total-row" style="color: #4CAF50; font-weight: 600;">
                            <span>Desconto PIX (15%)</span>
                            <span>- R$ <?php echo number_format($total * 0.15, 2, ',', '.'); ?></span>
                        </div>
                        
                        <div class="grand-total">
                            <span>Total</span>
                            <span>R$ <?php echo number_format($total * 0.85, 2, ',', '.'); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="grand-total">
                            <span>Total</span>
                            <span>R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="secure-notice">
                        <i class="fas fa-shield-alt"></i>
                        Compra 100% segura | Dados protegidos
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Função para buscar CEP
        function buscarCEP() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            
            if (cep.length !== 8) {
                alert('Digite um CEP válido com 8 dígitos');
                return;
            }
            
            // Mostrar loading
            document.getElementById('cepLoading').style.display = 'block';
            document.getElementById('buscarCep').disabled = true;
            
            // Fazer requisição para a API ViaCEP
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        throw new Error('CEP não encontrado');
                    }
                    
                    // Preencher os campos
                    document.getElementById('rua').value = data.logradouro || '';
                    document.getElementById('bairro').value = data.bairro || '';
                    document.getElementById('cidade').value = data.localidade || '';
                    document.getElementById('estado').value = data.uf || '';
                    
                    // Focar no campo número após preencher
                    document.getElementById('numero').focus();
                    
                    // Mostrar mensagem de sucesso
                    showMessage('CEP encontrado com sucesso!', 'success');
                })
                .catch(error => {
                    console.error('Erro ao buscar CEP:', error);
                    showMessage('CEP não encontrado. Preencha os campos manualmente.', 'error');
                })
                .finally(() => {
                    // Esconder loading
                    document.getElementById('cepLoading').style.display = 'none';
                    document.getElementById('buscarCep').disabled = false;
                });
        }
        
        // Função para mostrar mensagens
        function showMessage(message, type) {
            // Criar elemento de mensagem
            const messageDiv = document.createElement('div');
            messageDiv.className = type === 'success' ? 'success' : 'error';
            messageDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}`;
            
            // Inserir antes do formulário
            const container = document.querySelector('.container');
            const firstChild = container.firstElementChild;
            if (firstChild.classList.contains('success') || firstChild.classList.contains('error')) {
                container.removeChild(firstChild);
            }
            container.insertBefore(messageDiv, container.firstChild);
            
            // Remover mensagem após 5 segundos
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.parentNode.removeChild(messageDiv);
                }
            }, 5000);
        }
        
        // Event Listeners
        document.getElementById('buscarCep').addEventListener('click', buscarCEP);
        
        // Buscar CEP automaticamente quando digitar 8 dígitos
        document.getElementById('cep').addEventListener('input', function(e) {
            const cep = e.target.value.replace(/\D/g, '');
            if (cep.length === 8) {
                // Pequeno delay para usuário terminar de digitar
                setTimeout(() => {
                    if (document.getElementById('cep').value.replace(/\D/g, '').length === 8) {
                        buscarCEP();
                    }
                }, 500);
            }
        });
        
        // Validação do formulário principal
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            // Validar campos obrigatórios
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showMessage('Por favor, preencha todos os campos obrigatórios (*)', 'error');
                return;
            }
            
            // Validar CEP
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            if (cep.length !== 8) {
                e.preventDefault();
                showMessage('Digite um CEP válido com 8 dígitos', 'error');
                return;
            }
            
            // Mostrar loading
            document.getElementById('confirmBtn').style.display = 'none';
            document.getElementById('loading').style.display = 'block';
            document.getElementById('confirmBtn').disabled = true;
        });
        
        // Máscaras
        document.getElementById('telefone')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) {
                value = '(' + value.substring(0,2) + ') ' + value.substring(2,7) + '-' + value.substring(7,11);
            }
            e.target.value = value.substring(0, 15);
        });
        
        document.getElementById('cep')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0,5) + '-' + value.substring(5,8);
            }
            e.target.value = value.substring(0, 9);
        });
        
        document.getElementById('estado')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase().substring(0, 2);
        });
        
        document.getElementById('card_numero')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})/g, '$1 ').trim();
            e.target.value = value.substring(0, 19);
        });
        
        document.getElementById('card_validade')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0,2) + '/' + value.substring(2,4);
            }
            e.target.value = value.substring(0, 5);
        });
        
        document.getElementById('card_cvv')?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });
        
        // Remover borda vermelha ao digitar
        document.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('input', function() {
                this.style.borderColor = '';
            });
        });
        
        // Prevenir envio de formulário com Enter no CEP
        document.getElementById('cep').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarCEP();
            }
        });
        
        // Função para calcular frete baseado no CEP (opcional)
        function calcularFretePorCEP() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            if (cep.length === 8) {
                // Aqui você pode implementar uma chamada para sua API de cálculo de frete
                console.log('CEP para cálculo de frete:', cep);
            }
        }
        
        // Calcular frete quando o CEP for alterado
        document.getElementById('cep').addEventListener('change', calcularFretePorCEP);
    </script>
</body>
</html>