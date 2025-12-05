<?php
// detalhes_pedido.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';
require_once 'funcoes.php';

// Verificar login
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']) {
    header('Location: index.php');
    exit();
}

// Verificar ID do pedido
if (!isset($_GET['id'])) {
    header('Location: pedidos.php');
    exit();
}

$pedido_id = (int)$_GET['id'];
$usuario_id = $_SESSION['usuario']['id'];

// Buscar pedido
$stmt = $conexao->prepare("SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $pedido_id, $usuario_id);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();

if (!$pedido) {
    echo "Pedido não encontrado.";
    exit();
}

// Buscar itens do pedido
$stmt_itens = $conexao->prepare("
    SELECT pi.*, p.nome, p.imagem 
    FROM pedido_itens pi 
    JOIN produtos p ON pi.produto_id = p.id 
    WHERE pi.pedido_id = ?
");
$stmt_itens->bind_param("i", $pedido_id);
$stmt_itens->execute();
$itens = $stmt_itens->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido #<?php echo $pedido['id']; ?> - YARA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: #f9f9f9;
            font-family: 'Poppins', sans-serif;
        }

        .details-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .header-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .header-details h1 {
            font-family: 'Playfair Display', serif;
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }

        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }

        .status-pago {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-processando {
            background: #e2e3e5;
            color: #383d41;
        }

        .status-enviado {
            background: #cce5ff;
            color: #004085;
        }

        .status-entregue {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelado {
            background: #f8d7da;
            color: #721c24;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .info-box h3 {
            font-size: 16px;
            color: #888;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .info-box p {
            font-size: 16px;
            color: #333;
            margin: 0;
            line-height: 1.5;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #eee;
            color: #555;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-info img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .total-row {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            color: #e91e63;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn-back:hover {
            color: #e91e63;
        }

        /* === NAVBAR STYLES === */
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

        .navbar-icons {
            display: flex;
            gap: 20px;
            align-items: center;
            flex: 0 0 auto;
        }

        /* Search Bar */
        .search-container {
            display: flex;
            align-items: center;
            position: relative;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #f8f8f8;
            border-radius: 20px;
            padding: 6px 12px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            position: relative;
        }

        .search-bar:hover,
        .search-bar:focus-within {
            background: white;
            border-color: #e91e63;
            box-shadow: 0 2px 8px rgba(233, 30, 99, 0.1);
        }

        .search-bar input {
            border: none;
            background: none;
            outline: none;
            padding: 0 8px;
            font-size: 12px;
            width: 150px;
            color: #333;
        }

        .search-icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-divider {
            width: 1px;
            height: 20px;
            background: rgba(0, 0, 0, 0.2);
            margin: 0 5px;
        }

        /* Icons */
        .nav-icon {
            width: 20px;
            height: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-icon i {
            font-size: 16px;
            color: #333;
        }

        .nav-icon:hover i {
            color: #e91e63;
        }

        /* User Dropdown */
        .user-icon {
            position: relative;
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 8px 0;
            min-width: 160px;
            display: none;
            z-index: 1000;
            border: 1px solid #f0f0f0;
        }

        .user-icon:hover .user-dropdown,
        .user-dropdown:hover {
            display: block;
        }

        .user-dropdown a {
            display: block;
            padding: 10px 16px;
            text-decoration: none;
            color: #333;
            font-size: 13px;
        }

        .user-dropdown a:hover {
            background: #f8f8f8;
            color: #e91e63;
        }

        /* Cart Count */
        .cart-count {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #e91e63;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
        }

        /* Dropdowns */
        .menu-item {
            position: relative;
            display: flex;
            align-items: center;
        }

        .dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 20px 40px;
            box-shadow: 0px 4px 14px rgba(0, 0, 0, 0.15);
            border-radius: 2px;
            display: none;
            gap: 100px;
            z-index: 9999;
            white-space: nowrap;
        }

        .menu-item:hover .dropdown,
        .dropdown:hover {
            display: flex;
        }

        .dropdown h4 {
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        .dropdown a {
            display: block;
            font-size: 12px;
            color: #000;
            margin: 6px 0;
            text-decoration: none;
            cursor: pointer;
        }

        /* Footer Styles */
        .footer {
            background: #000;
            color: white;
            padding: 40px 20px 20px;
            margin-top: 60px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 60px;
            margin-bottom: 30px;
        }

        .footer-col h3,
        .footer-col h4 {
            color: white;
            margin-bottom: 15px;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
        }

        .footer-col ul li a {
            color: #ccc;
            text-decoration: none;
        }

        .social {
            display: flex;
            gap: 15px;
        }

        .social a {
            color: #ccc;
            font-size: 18px;
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 20px;
            text-align: center;
            color: #ccc;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                gap: 30px;
            }

            .navbar-container {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="details-container">
        <div class="header-details">
            <div>
                <a href="pedidos.php" class="btn-back"><i class="fas fa-arrow-left"></i> Voltar para Pedidos</a>
                <h1 style="margin-top: 15px;">Pedido #<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></h1>
                <p style="color: #777; margin: 5px 0;">Realizado em <?php echo date('d/m/Y \à\s H:i', strtotime($pedido['data_pedido'])); ?></p>
            </div>
            <span class="status-badge status-<?php echo strtolower($pedido['status']); ?>">
                <?php echo ucfirst($pedido['status']); ?>
            </span>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>Endereço de Entrega</h3>
                <p><?php echo nl2br(htmlspecialchars($pedido['endereco_entrega'])); ?></p>
            </div>
            <div class="info-box">
                <h3>Pagamento</h3>
                <p><strong>Forma:</strong> <?php echo ucfirst($pedido['forma_pagamento']); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($pedido['status']); ?></p>
            </div>
        </div>

        <h3>Itens do Pedido</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço Unit.</th>
                    <th>Qtd.</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $itens->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="imgs/<?php echo !empty($item['imagem']) ? $item['imagem'] : 'produto-padrao.png'; ?>" alt="Produto">
                                <span><?php echo htmlspecialchars($item['nome']); ?></span>
                            </div>
                        </td>
                        <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                        <td><?php echo $item['quantidade']; ?></td>
                        <td style="text-align: right;">R$ <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

    <?php include 'footer.php'; ?>

</body>

</html>