<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

// Verificar se usuário está logado
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']) {
    header('Location: index.php');
    exit();
}

$usuario = $_SESSION['usuario'];

// Processar atualização de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_perfil') {
    $novoNome = trim($_POST['nome']);
    $novoEmail = trim($_POST['email']);
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $cpf = $_POST['cpf'] ?? '';

    if (empty($novoNome) || empty($novoEmail)) {
        $mensagemErro = "Nome e email são obrigatórios.";
    } else {
        // Verifica duplicidade de email
        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $novoEmail, $usuario['id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $mensagemErro = "Este email já está em uso.";
        } else {
            try {
                // Atualiza dados básicos
                $sql = "UPDATE usuarios SET nome = ?, email = ?";
                $types = "ss";
                $params = [$novoNome, $novoEmail];

                // Adicionar telefone se fornecido
                if (!empty($telefone)) {
                    $sql .= ", telefone = ?";
                    $types .= "s";
                    $params[] = $telefone;
                }

                // Adicionar CPF se fornecido
                if (!empty($cpf)) {
                    $sql .= ", cpf = ?";
                    $types .= "s";
                    $params[] = $cpf;
                }

                // Atualiza senha se fornecida
                if (!empty($senhaAtual) && !empty($novaSenha)) {
                    // Busca senha atual no banco para verificar hash
                    $stmtCheck = $conexao->prepare("SELECT senha FROM usuarios WHERE id = ?");
                    $stmtCheck->bind_param("i", $usuario['id']);
                    $stmtCheck->execute();
                    $hashAtual = $stmtCheck->get_result()->fetch_assoc()['senha'];

                    if (password_verify($senhaAtual, $hashAtual) || $senhaAtual === $hashAtual) {
                        $sql .= ", senha = ?";
                        $types .= "s";
                        $params[] = password_hash($novaSenha, PASSWORD_DEFAULT);
                    } else {
                        throw new Exception("Senha atual incorreta.");
                    }
                }

                // Upload de Foto
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $nomeArquivo = uniqid() . '.' . $extensao;
                        if (move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $nomeArquivo)) {
                            $sql .= ", foto = ?";
                            $types .= "s";
                            $params[] = $nomeArquivo;
                            $usuario['foto'] = $nomeArquivo; // Atualiza na sessão visualmente
                        }
                    }
                }

                $sql .= " WHERE id = ?";
                $types .= "i";
                $params[] = $usuario['id'];

                $stmt = $conexao->prepare($sql);
                $stmt->bind_param($types, ...$params);

                if ($stmt->execute()) {
                    // Atualiza sessão
                    $_SESSION['usuario']['nome'] = $novoNome;
                    $_SESSION['usuario']['email'] = $novoEmail;
                    if (!empty($telefone)) $_SESSION['usuario']['telefone'] = $telefone;
                    if (!empty($cpf)) $_SESSION['usuario']['cpf'] = $cpf;
                    if (isset($usuario['foto'])) $_SESSION['usuario']['foto'] = $usuario['foto'];

                    $usuario = $_SESSION['usuario']; // Atualiza variável local
                    $mensagemSucesso = "Dados atualizados com sucesso!";
                }
            } catch (Exception $e) {
                $mensagemErro = $e->getMessage();
            }
        }
    }
}

// Buscar dados atualizados do usuário
$stmt = $conexao->prepare("SELECT nome, email, telefone, cpf, foto, data_cadastro FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($userData = $result->fetch_assoc()) {
    $usuario = array_merge($usuario, $userData);
    $_SESSION['usuario'] = $usuario;
}

// Buscar Pedidos com detalhes dos itens
$id_usuario = $_SESSION['usuario']['id'];
$pedidos = [];

try {
    // Primeira consulta: buscar pedidos e itens
    $sqlPedidos = "
        SELECT p.*, 
               GROUP_CONCAT(CONCAT(pi.produto_id, '|', pi.quantidade, '|', pi.preco_unitario) SEPARATOR ';') as itens_info
        FROM pedidos p
        LEFT JOIN pedido_itens pi ON p.id = pi.pedido_id
        WHERE p.usuario_id = ?
        GROUP BY p.id
        ORDER BY p.data_pedido DESC
    ";
    
    $stmt = $conexao->prepare($sqlPedidos);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $pedidos_db = $stmt->get_result();
    
    // Processar os pedidos e seus itens
    while ($pedido_db = $pedidos_db->fetch_assoc()) {
        $pedido = [
            'id' => 'YARA-' . $pedido_db['id'],
            'id_original' => $pedido_db['id'],
            'data' => $pedido_db['data_pedido'],
            'status' => $pedido_db['status'],
            'total' => floatval($pedido_db['valor_total'] ?? $pedido_db['total'] ?? 0),
            'forma_pagamento' => $pedido_db['forma_pagamento'] ?? $pedido_db['metodo_pagamento'] ?? '',
            'endereco_entrega' => $pedido_db['endereco_entrega'] ?? '',
            'itens' => []
        ];
        
        // Processar itens do pedido
        if (!empty($pedido_db['itens_info'])) {
            $itens_info = explode(';', $pedido_db['itens_info']);
            foreach ($itens_info as $item_info) {
                if (!empty($item_info)) {
                    list($produto_id, $quantidade, $preco_unitario) = explode('|', $item_info);
                    
                    // Buscar informações do produto
                    $stmt_produto = $conexao->prepare("SELECT nome, imagem FROM produtos WHERE id = ?");
                    $stmt_produto->bind_param("i", $produto_id);
                    $stmt_produto->execute();
                    $produto = $stmt_produto->get_result()->fetch_assoc();
                    
                    if ($produto) {
                        $pedido['itens'][] = [
                            'nome' => $produto['nome'],
                            'quantidade' => intval($quantidade),
                            'preco' => floatval($preco_unitario),
                            'imagem' => $produto['imagem']
                        ];
                    }
                }
            }
        }
        
        $pedidos[] = $pedido;
    }
    
} catch (Exception $e) {
    $erro_pedidos = "Erro ao carregar pedidos: " . $e->getMessage();
}

$dataCadastro = isset($usuario['data_cadastro']) ? date('d/m/Y', strtotime($usuario['data_cadastro'])) : 'Recente';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Minha Conta - YARA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <!-- SweetAlert2 para mensagens bonitas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* === NAVBAR PADRONIZADA === */
        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-logo {
            flex: 0 0 auto;
        }

        .navbar-logo img {
            height: 50px;
            width: auto;
        }

        .navbar-menu {
            display: flex;
            gap: 40px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex: 1;
            justify-content: center;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #000;
            font-size: 14px;
            letter-spacing: 0.5px;
            transition: color 0.3s;
            position: relative;
            font-weight: 500;
        }

        .navbar-menu a:hover {
            color: #888;
        }

        .navbar-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 1px;
            background-color: #000;
            transition: width 0.3s;
        }

        .navbar-menu a:hover::after {
            width: 100%;
        }

        /* === LAYOUT DO PERFIL === */
        .profile-section {
            padding: 60px 20px;
            background-color: #f9f9f9;
            min-height: 80vh;
        }

        .profile-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        /* Sidebar */
        .profile-sidebar {
            flex: 0 0 250px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .profile-sidebar h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6em;
            margin: 0 0 25px 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }

        .profile-nav ul {
            list-style: none;
            padding: 0;
        }

        .profile-nav li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            text-decoration: none;
            color: #555;
            font-weight: 500;
            border-radius: 6px;
            transition: 0.3s;
            margin-bottom: 5px;
        }

        .profile-nav li a:hover,
        .profile-nav li a.active {
            background-color: #fff0f6;
            color: #e91e63;
        }

        .profile-nav li a i {
            width: 20px;
            text-align: center;
        }

        /* Conteúdo */
        .profile-content {
            flex: 1;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.ativo {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* User Header */
        .user-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 40px;
            border-bottom: 1px solid #eee;
            padding-bottom: 30px;
        }

        .user-avatar-lg img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e91e63;
        }

        .user-avatar-lg .placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e91e63, #ff4081);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            border: 3px solid #ff80ab;
        }

        .user-info h1 {
            margin: 0;
            font-size: 24px;
            font-family: 'Playfair Display', serif;
        }

        .user-info p {
            color: #777;
            margin: 5px 0 0;
        }

        /* Dados */
        .dados-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .dados-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #e91e63;
        }

        .dados-item label {
            display: block;
            font-weight: 600;
            color: #888;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .dados-item span {
            font-size: 16px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .edit-button {
            margin-top: 30px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #e91e63, #ff4081);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
        }

        .edit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3);
        }

        /* Estilos da aba Pedidos */
        .pedidos-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .pedido-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 25px;
            background: #fafafa;
            transition: transform 0.3s;
        }

        .pedido-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .pedido-info h3 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1.2em;
        }

        .pedido-info p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
        }

        .pedido-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-entregue {
            background: #d4edda;
            color: #155724;
        }

        .status-processando {
            background: #fff3cd;
            color: #856404;
        }

        .status-enviado {
            background: #cce7ff;
            color: #004085;
        }
        
        .status-pendente {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-pago {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-cancelado {
            background: #e2e3e5;
            color: #383d41;
        }

        .pedido-itens {
            margin-bottom: 20px;
        }

        .item-pedido {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px dashed #eee;
        }

        .item-pedido:last-child {
            border-bottom: none;
        }

        .item-imagem {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid #eee;
        }

        .item-imagem img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-info {
            flex: 1;
        }

        .item-info .nome {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .item-info .quantidade {
            color: #666;
            font-size: 0.9em;
        }

        .item-preco {
            font-weight: 600;
            color: #333;
            min-width: 100px;
            text-align: right;
        }

        .pedido-total {
            text-align: right;
            font-size: 1.2em;
            font-weight: 700;
            color: #e91e63;
            padding-top: 15px;
            border-top: 2px solid #fe7db9;
        }

        .pedido-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .btn-acao {
            padding: 8px 16px;
            border: 1px solid #e91e63;
            background: white;
            color: #e91e63;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-acao:hover {
            background: #e91e63;
            color: white;
        }

        .btn-acao:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-acao:disabled:hover {
            background: white;
            color: #e91e63;
        }

        .empty-pedidos {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-pedidos i {
            font-size: 4em;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-pedidos h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #333;
        }

        .empty-pedidos p {
            margin-bottom: 30px;
        }

        .btn-primary {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #e91e63, #ff4081);
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #d81b60, #f50057);
        }

        .info-pagamento {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
        
        .endereco-entrega {
            margin: 10px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 0.9em;
            color: #666;
            border-left: 3px solid #e91e63;
        }

        /* Modal Editar Dados */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(5px);
        }

        .modal-overlay.mostrar {
            display: flex;
        }

        .modal-editar {
            background: white;
            padding: 40px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fechar-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .fechar-modal:hover {
            background: #f5f5f5;
            color: #e91e63;
        }

        .modal-editar h2 {
            margin: 0 0 25px 0;
            font-family: 'Playfair Display', serif;
            color: #333;
            padding-bottom: 15px;
            border-bottom: 2px solid #e91e63;
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

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            transition: border 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #e91e63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: #888;
            font-size: 12px;
        }

        .preview-foto {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #e91e63;
            display: none;
        }

        .preview-foto.visible {
            display: block;
        }

        .btn-modal-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #e91e63, #ff4081);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-modal-primary:hover {
            background: linear-gradient(135deg, #d81b60, #f50057);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3);
        }

        .btn-modal-secondary {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: #e91e63;
            border: 2px solid #e91e63;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-modal-secondary:hover {
            background: #e91e63;
            color: white;
        }

        /* Mensagens */
        .mensagem {
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            text-align: center;
            max-width: 500px;
            display: none;
        }

        .mensagem.sucesso {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensagem.erro {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .mensagem.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .profile-sidebar {
                width: 100%;
            }

            .pedido-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .item-pedido {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .item-preco {
                align-self: flex-end;
            }
            
            .pedido-actions {
                flex-direction: column;
            }
            
            .navbar-container {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }

            .dados-container {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .modal-editar {
                padding: 20px;
                margin: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <?php include 'navbar.php'; ?>

    <main class="profile-section">
        <div class="profile-container">

            <!-- MENU LATERAL -->
            <aside class="profile-sidebar">
                <h2>Minha Conta</h2>
                <nav class="profile-nav">
                    <ul>
                        <li><a href="#dados" onclick="abrirTab('dados', this)" class="active"><i class="far fa-user"></i> Meus Dados</a></li>
                        <li><a href="#pedidos" onclick="abrirTab('pedidos', this)"><i class="fas fa-box-open"></i> Meus Pedidos</a></li>
                        <li><a href="enderecos.php"><i class="fas fa-map-marker-alt"></i> Endereços</a></li>
                        <li><a href="favoritos.php"><i class="far fa-heart"></i> Favoritos</a></li>
                        <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- CONTEÚDO -->
            <section class="profile-content">

                <!-- CABEÇALHO DO USUÁRIO -->
                <div class="user-header">
                    <div class="user-avatar-lg">
                        <?php if (!empty($usuario['foto'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($usuario['foto']); ?>" alt="Foto" id="currentAvatar">
                        <?php else: ?>
                            <div class="placeholder"><?php echo substr($usuario['nome'], 0, 1); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="user-info">
                        <h1>Olá, <?php echo htmlspecialchars(explode(' ', $usuario['nome'])[0]); ?>!</h1>
                        <p>Membro desde <?php echo $dataCadastro; ?></p>
                    </div>
                </div>

                <!-- ABA DADOS -->
                <div id="tab-dados" class="tab-content ativo">
                    <div class="dados-container">
                        <div class="dados-item">
                            <label>Nome Completo</label>
                            <span><?php echo htmlspecialchars($usuario['nome']); ?></span>
                        </div>
                        <div class="dados-item">
                            <label>E-mail</label>
                            <span><?php echo htmlspecialchars($usuario['email']); ?></span>
                        </div>
                        <?php if (!empty($usuario['telefone'])): ?>
                        <div class="dados-item">
                            <label>Telefone</label>
                            <span><?php echo htmlspecialchars($usuario['telefone']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($usuario['cpf'])): ?>
                        <div class="dados-item">
                            <label>CPF</label>
                            <span><?php echo htmlspecialchars($usuario['cpf']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <button class="edit-button" onclick="abrirModalEditar()">
                        <i class="fas fa-pen"></i> Editar Dados
                    </button>
                </div>

                <!-- ABA PEDIDOS -->
                <div id="tab-pedidos" class="tab-content">
                    <h3 style="margin-bottom:20px; font-family:'Playfair Display',serif;">Meus Pedidos</h3>

                    <?php if (isset($erro_pedidos)): ?>
                        <div class="mensagem erro" style="display: block;">
                            <?php echo $erro_pedidos; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($pedidos)): ?>
                        <div class="empty-pedidos">
                            <i class="fas fa-box-open"></i>
                            <h3>Nenhum pedido encontrado</h3>
                            <p>Você ainda não realizou nenhum pedido em nossa loja.</p>
                            <a href="produtos.php" class="btn-primary">Explorar Produtos</a>
                        </div>
                    <?php else: ?>
                        <div class="pedidos-list">
                            <?php foreach ($pedidos as $pedido): ?>
                            <div class="pedido-card">
                                <div class="pedido-header">
                                    <div class="pedido-info">
                                        <h3>Pedido <?php echo $pedido['id']; ?></h3>
                                        <p>Realizado em <?php echo date('d/m/Y', strtotime($pedido['data'])); ?></p>
                                        <?php if (!empty($pedido['forma_pagamento'])): ?>
                                        <p class="info-pagamento">
                                            <i class="fas fa-credit-card"></i> 
                                            <?php 
                                            $formas_pagamento = [
                                                'cartao_credito' => 'Cartão de Crédito',
                                                'cartao_debito' => 'Cartão de Débito',
                                                'boleto' => 'Boleto Bancário',
                                                'pix' => 'PIX',
                                                'Cartão de Crédito' => 'Cartão de Crédito',
                                                'Cartão de Débito' => 'Cartão de Débito',
                                                'Boleto' => 'Boleto',
                                                'PIX' => 'PIX'
                                            ];
                                            echo $formas_pagamento[$pedido['forma_pagamento']] ?? $pedido['forma_pagamento'];
                                            ?>
                                        </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="pedido-status status-<?php echo $pedido['status']; ?>">
                                        <?php 
                                        $statusText = [
                                            'pendente' => 'Pendente',
                                            'pago' => 'Pago',
                                            'processando' => 'Processando',
                                            'enviado' => 'Enviado',
                                            'entregue' => 'Entregue',
                                            'cancelado' => 'Cancelado'
                                        ];
                                        echo $statusText[$pedido['status']] ?? $pedido['status']; 
                                        ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($pedido['endereco_entrega'])): ?>
                                <div class="endereco-entrega">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <strong>Endereço de entrega:</strong> <?php echo htmlspecialchars($pedido['endereco_entrega']); ?>
                                </div>
                                <?php endif; ?>

                                <div class="pedido-itens">
                                    <?php foreach ($pedido['itens'] as $item): ?>
                                    <div class="item-pedido">
                                        <div class="item-imagem">
                                            <img src="imgs/<?php echo !empty($item['imagem']) ? htmlspecialchars($item['imagem']) : 'produto-padrao.png'; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['nome']); ?>"
                                                 onerror="this.src='imgs/produto-padrao.png'">
                                        </div>
                                        <div class="item-info">
                                            <div class="nome"><?php echo htmlspecialchars($item['nome']); ?></div>
                                            <div class="quantidade">Quantidade: <?php echo $item['quantidade']; ?></div>
                                        </div>
                                        <div class="item-preco">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="pedido-total">
                                    Total: R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?>
                                </div>

                                <div class="pedido-actions">
                                    <button class="btn-acao" onclick="window.location.href='detalhes_pedido.php?id=<?php echo $pedido['id_original']; ?>'">
                                        <i class="fas fa-eye"></i> Ver Detalhes
                                    </button>
                                    <button class="btn-acao" onclick="comprarNovamente(<?php echo htmlspecialchars(json_encode($pedido['itens'])); ?>)">
                                        <i class="fas fa-redo"></i> Comprar Novamente
                                    </button>
                                    <?php if ($pedido['status'] === 'entregue'): ?>
                                    <button class="btn-acao" onclick="window.location.href='avaliacao.php?pedido_id=<?php echo $pedido['id_original']; ?>'">
                                        <i class="fas fa-star"></i> Avaliar Produtos
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($pedido['status'] === 'pendente'): ?>
                                    <button class="btn-acao" onclick="cancelarPedido(<?php echo $pedido['id_original']; ?>)">
                                        <i class="fas fa-times"></i> Cancelar Pedido
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </section>
        </div>
    </main>

    <!-- MODAL EDITAR DADOS -->
    <div class="modal-overlay" id="modalEditar">
        <div class="modal-editar">
            <button class="fechar-modal" onclick="fecharModalEditar()">&times;</button>
            <h2>Editar Dados Pessoais</h2>
            
            <!-- Preview da foto -->
            <?php if (!empty($usuario['foto'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($usuario['foto']); ?>" alt="Foto atual" class="preview-foto visible" id="fotoPreview">
            <?php else: ?>
            <div class="preview-foto" id="fotoPreview" style="display: none;"></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="formEditarDados">
                <input type="hidden" name="acao" value="atualizar_perfil">

                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>" placeholder="(11) 99999-9999">
                    </div>
                    <div class="form-group">
                        <label for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($usuario['cpf'] ?? ''); ?>" placeholder="000.000.000-00">
                    </div>
                </div>

                <div class="form-group">
                    <label for="foto">Foto de Perfil</label>
                    <input type="file" id="foto" name="foto" accept="image/*" onchange="previewFoto(this)">
                    <small>Formatos aceitos: JPG, PNG, GIF (Máx. 2MB)</small>
                </div>

                <div class="form-group">
                    <label>Alterar Senha (Opcional)</label>
                    <input type="password" name="nova_senha" placeholder="Nova senha (mínimo 6 caracteres)">
                </div>

                <div class="form-group">
                    <label>Confirmar com Senha Atual</label>
                    <input type="password" name="senha_atual" placeholder="Digite sua senha atual para confirmar">
                    <small>Necessário para alterar senha ou e-mail</small>
                </div>

                <button type="submit" class="btn-modal-primary">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
                
                <button type="button" class="btn-modal-secondary" onclick="fecharModalEditar()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Sistema de abas
        function abrirTab(id, link) {
            // Esconde todas as abas
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('ativo'));
            // Remove ativo dos links
            document.querySelectorAll('.profile-nav a').forEach(a => a.classList.remove('active'));

            // Mostra a aba certa
            document.getElementById('tab-' + id).classList.add('ativo');
            // Ativa o link certo
            link.classList.add('active');
            
            // Atualiza URL sem recarregar a página
            history.pushState(null, null, '#' + id);
        }

        // Verificar hash na URL ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash.replace('#', '');
            if (hash) {
                const link = document.querySelector(`.profile-nav a[href="#${hash}"]`);
                if (link) {
                    abrirTab(hash, link);
                }
            }
            
            // Aplicar máscaras nos inputs
            aplicarMascaras();
        });

        // Modal de edição
        function abrirModalEditar() {
            document.getElementById('modalEditar').classList.add('mostrar');
            document.body.style.overflow = 'hidden';
        }

        function fecharModalEditar() {
            document.getElementById('modalEditar').classList.remove('mostrar');
            document.body.style.overflow = '';
        }

        // Fechar modal ao clicar fora
        document.getElementById('modalEditar').addEventListener('click', function(e) {
            if (e.target === this) {
                fecharModalEditar();
            }
        });

        // Fechar modal com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('modalEditar').classList.contains('mostrar')) {
                fecharModalEditar();
            }
        });

        // Preview da foto
        function previewFoto(input) {
            const preview = document.getElementById('fotoPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('visible');
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.remove('visible');
                preview.style.display = 'none';
            }
        }

        // Aplicar máscaras
        function aplicarMascaras() {
            // Máscara para telefone
            const telefoneInput = document.getElementById('telefone');
            if (telefoneInput) {
                telefoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 10) {
                        value = '(' + value.substring(0,2) + ') ' + value.substring(2,7) + '-' + value.substring(7,11);
                    }
                    e.target.value = value.substring(0, 15);
                });
            }

            // Máscara para CPF
            const cpfInput = document.getElementById('cpf');
            // CÓDIGO NOVO CORRIGIDO
if (value.length > 9) {
    value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6, 9) + '-' + value.substring(9, 11);
} else if (value.length > 6) {
    value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6, 9);
} else if (value.length > 3) {
    value = value.substring(0, 3) + '.' + value.substring(3, 6);
} 
        }

        // Função de logout
        function logout() {
            Swal.fire({
                title: 'Sair da conta?',
                text: 'Você será desconectado do sistema.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e91e63',
                cancelButtonColor: '#666',
                confirmButtonText: 'Sim, sair',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('processa_form.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'acao=logout'
                    }).then(r => r.json()).then(d => {
                        if (d.success) {
                            window.location.href = 'index.php';
                        }
                    });
                }
            });
        }

        // Funções da aba Pedidos
        function comprarNovamente(itens) {
            Swal.fire({
                title: 'Comprar novamente?',
                text: 'Deseja adicionar todos os itens deste pedido ao carrinho?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e91e63',
                cancelButtonColor: '#666',
                confirmButtonText: 'Sim, adicionar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aqui você implementaria a lógica para adicionar ao carrinho
                    // Por enquanto, redirecionamos para produtos
                    window.location.href = 'produtos.php';
                }
            });
        }
        
        function cancelarPedido(pedidoId) {
            Swal.fire({
                title: 'Cancelar pedido?',
                text: 'Esta ação não poderá ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e91e63',
                cancelButtonColor: '#666',
                confirmButtonText: 'Sim, cancelar',
                cancelButtonText: 'Voltar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('processa_pedido.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `acao=cancelar&pedido_id=${pedidoId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: data.message,
                                confirmButtonColor: '#e91e63'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: data.message,
                                confirmButtonColor: '#666'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao cancelar pedido.',
                            confirmButtonColor: '#666'
                        });
                    });
                }
            });
        }

        // Validação do formulário de edição
        document.getElementById('formEditarDados').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nome = document.getElementById('nome').value.trim();
            const email = document.getElementById('email').value.trim();
            const senhaAtual = document.querySelector('input[name="senha_atual"]').value;
            const novaSenha = document.querySelector('input[name="nova_senha"]').value;
            
            if (!nome || !email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campos obrigatórios',
                    text: 'Nome e e-mail são obrigatórios.',
                    confirmButtonColor: '#e91e63'
                });
                return;
            }
            
            // Se estiver alterando senha, precisa da senha atual
            if (novaSenha && !senhaAtual) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Senha atual necessária',
                    text: 'Para alterar a senha, informe sua senha atual.',
                    confirmButtonColor: '#e91e63'
                });
                return;
            }
            
            // Se tudo ok, enviar o formulário
            this.submit();
        });

        // Feedback de mensagens do PHP
        <?php if (isset($mensagemSucesso)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '<?php echo $mensagemSucesso; ?>',
            confirmButtonColor: '#e91e63',
            timer: 3000,
            timerProgressBar: true
        });
        <?php endif; ?>

        <?php if (isset($mensagemErro)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: '<?php echo $mensagemErro; ?>',
            confirmButtonColor: '#666'
        });
        <?php endif; ?>
    </script>

</body>

</html>