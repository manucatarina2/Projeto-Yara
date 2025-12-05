<?php
// admin/dashboard.php - VERSÃO CORRIGIDA SEM TABELA ITENS_PEDIDO

session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../conexao.php';
require_once '../funcoes.php';

// Verificar se é admin
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario'] || ($_SESSION['usuario']['is_admin'] ?? 0) != 1) {
    header('Location: ../index.php');
    exit();
}

// --- BUSCAR DADOS REAIS DO BANCO ---

// 1. Total de Produtos
$sql_prod = "SELECT COUNT(*) as total FROM produtos WHERE disponivel = 1";
$total_produtos = $conexao->query($sql_prod)->fetch_assoc()['total'];

// 2. Total de Usuários
$sql_users = "SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1";
$total_usuarios = $conexao->query($sql_users)->fetch_assoc()['total'];

// 3. Total de Pedidos
$sql_pedidos = "SELECT COUNT(*) as total FROM pedidos";
$res_pedidos = $conexao->query($sql_pedidos);
$total_pedidos = $res_pedidos ? $res_pedidos->fetch_assoc()['total'] : 0;

// 4. Faturamento Total (apenas pedidos pagos)
$sql_faturamento = "SELECT SUM(valor_total) as total FROM pedidos WHERE status = 'pago'";
$res_faturamento = $conexao->query($sql_faturamento);
$total_faturamento = $res_faturamento ? $res_faturamento->fetch_assoc()['total'] : 0;
if (!$total_faturamento) $total_faturamento = 0;

// 5. Pedidos Pendentes
$sql_pendentes = "SELECT COUNT(*) as total FROM pedidos WHERE status = 'pendente'";
$res_pendentes = $conexao->query($sql_pendentes);
$pedidos_pendentes = $res_pendentes ? $res_pendentes->fetch_assoc()['total'] : 0;

// 6. Faturamento do Mês Atual
$mes_atual = date('m');
$ano_atual = date('Y');
$sql_mes_atual = "SELECT SUM(valor_total) as total FROM pedidos WHERE status = 'pago' AND MONTH(data_pedido) = ? AND YEAR(data_pedido) = ?";
$stmt_mes = $conexao->prepare($sql_mes_atual);
$stmt_mes->bind_param('ii', $mes_atual, $ano_atual);
$stmt_mes->execute();
$res_mes = $stmt_mes->get_result();
$faturamento_mes = $res_mes->fetch_assoc()['total'] ?? 0;

// 7. Vendas dos últimos 6 meses para gráfico
$sql_vendas_mensais = "SELECT 
    MONTH(data_pedido) as mes,
    YEAR(data_pedido) as ano,
    SUM(valor_total) as total
    FROM pedidos 
    WHERE status = 'pago' 
    AND data_pedido >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY YEAR(data_pedido), MONTH(data_pedido)
    ORDER BY ano DESC, mes DESC
    LIMIT 6";

$res_vendas = $conexao->query($sql_vendas_mensais);
$vendas_mensais = [];
$labels_meses = [];
$valores_meses = [];

// Mapear números dos meses para nomes
$meses = [
    1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr', 
    5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
    9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
];

while($row = $res_vendas->fetch_assoc()) {
    $vendas_mensais[] = $row;
    $labels_meses[] = $meses[$row['mes']] . '/' . substr($row['ano'], 2);
    $valores_meses[] = floatval($row['total']);
}

// Inverter arrays para mostrar do mais antigo para o mais recente
$labels_meses = array_reverse($labels_meses);
$valores_meses = array_reverse($valores_meses);

// Se não houver dados, preencha com zeros
if (empty($valores_meses)) {
    for ($i = 5; $i >= 0; $i--) {
        $mes = date('n', strtotime("-$i months"));
        $ano = date('Y', strtotime("-$i months"));
        $labels_meses[] = $meses[$mes] . '/' . substr($ano, 2);
        $valores_meses[] = 0;
    }
}

// 8. Categorias dos produtos (verificar quais existem)
$sql_categorias = "SELECT 
    DISTINCT categoria,
    COUNT(*) as quantidade
    FROM produtos 
    WHERE disponivel = 1
    GROUP BY categoria
    ORDER BY quantidade DESC";

$res_categorias = $conexao->query($sql_categorias);
$categorias_labels = [];
$categorias_quantidades = [];

while($cat = $res_categorias->fetch_assoc()) {
    $categorias_labels[] = ucfirst($cat['categoria']);
    $categorias_quantidades[] = $cat['quantidade'];
}

// 9. Últimos pedidos (simplificado sem itens_pedido)
$sql_ultimos = "SELECT 
    pe.id,
    u.nome as cliente,
    pe.valor_total,
    pe.status,
    DATE_FORMAT(pe.data_pedido, '%d/%m/%Y %H:%i') as data_formatada
    FROM pedidos pe
    LEFT JOIN usuarios u ON pe.usuario_id = u.id
    ORDER BY pe.data_pedido DESC 
    LIMIT 8";

$res_ultimos = $conexao->query($sql_ultimos);

// 10. Status dos pedidos para gráfico de pizza
$sql_status_pedidos = "SELECT 
    status,
    COUNT(*) as quantidade
    FROM pedidos 
    GROUP BY status";

$res_status = $conexao->query($sql_status_pedidos);
$status_labels = [];
$status_quantidades = [];
$status_cores = [];

$cores_status = [
    'pago' => '#2ecc71',
    'pendente' => '#f39c12', 
    'cancelado' => '#e74c3c',
    'processando' => '#3498db',
    'enviado' => '#9b59b6',
    'entregue' => '#1abc9c'
];

while($status = $res_status->fetch_assoc()) {
    $status_labels[] = ucfirst($status['status']);
    $status_quantidades[] = $status['quantidade'];
    $status_cores[] = $cores_status[$status['status']] ?? '#95a5a6';
}

// 11. Usuários novos do mês
$sql_novos_usuarios = "SELECT 
    COUNT(*) as total
    FROM usuarios 
    WHERE MONTH(data_cadastro) = MONTH(CURRENT_DATE()) 
    AND YEAR(data_cadastro) = YEAR(CURRENT_DATE())";

$res_novos = $conexao->query($sql_novos_usuarios);
$novos_usuarios_mes = $res_novos->fetch_assoc()['total'] ?? 0;

// 12. Produtos mais vendidos (usando apenas produtos)
$sql_top_produtos = "SELECT 
    nome,
    imagem,
    vendas as total_vendido,
    (preco * vendas) as faturamento_total
    FROM produtos 
    WHERE disponivel = 1 AND vendas > 0
    ORDER BY vendas DESC
    LIMIT 5";

// Se não existir coluna 'vendas', usar alternativa
$result = $conexao->query("SHOW COLUMNS FROM produtos LIKE 'vendas'");
if ($result->num_rows > 0) {
    // Coluna vendas existe
    $res_top_produtos = $conexao->query($sql_top_produtos);
} else {
    // Coluna vendas não existe, criar lista de produtos ativos
    $sql_top_produtos = "SELECT 
        nome,
        imagem,
        preco
        FROM produtos 
        WHERE disponivel = 1
        ORDER BY RAND()
        LIMIT 5";
    $res_top_produtos = $conexao->query($sql_top_produtos);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard YARA</title>
   
    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   
    <!-- Chart.js para Gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* === CSS DO DASHBOARD === */
        
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
            --content-bg: #f8f9fa;
            --card-bg: #ffffff;
            --primary-color: #e91e63;
            --secondary-color: #ff4081;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #3498db;
            --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body { 
            font-family: var(--font-family); 
            background-color: var(--content-bg); 
            display: flex; 
            min-height: 100vh;
            color: #333;
        }
       
        /* === SIDEBAR === */
        .sidebar { 
            width: 260px; 
            background-color: var(--sidebar-bg); 
            color: var(--sidebar-text); 
            position: fixed; 
            top: 0; 
            left: 0; 
            bottom: 0; 
            display: flex; 
            flex-direction: column;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .sidebar-header { 
            text-align: center; 
            padding: 25px 20px; 
            border-bottom: 1px solid #34495e; 
            background: rgba(0,0,0,0.1);
        }

        .sidebar-header h3 {
            color: white;
            font-weight: 600;
            letter-spacing: 1px;
            font-size: 1.4rem;
        }

        .sidebar-header p {
            color: #bdc3c7;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .sidebar nav { 
            flex: 1; 
            padding-top: 20px; 
        }

        .sidebar nav a { 
            display: block; 
            padding: 15px 25px; 
            color: var(--sidebar-text); 
            text-decoration: none; 
            transition: all 0.3s ease;
            display: flex; 
            align-items: center;
            border-left: 4px solid transparent;
            font-size: 14px;
        }

        .sidebar nav a:hover, 
        .sidebar nav a.active { 
            background-color: rgba(52, 73, 94, 0.6); 
            border-left-color: var(--primary-color);
            transform: translateX(5px);
        }

        .sidebar nav a i { 
            margin-right: 15px; 
            width: 20px; 
            text-align: center;
            font-size: 16px;
        }

        .sidebar-footer { 
            padding: 20px; 
            border-top: 1px solid #34495e;
            background: rgba(0,0,0,0.1);
        }

        .sidebar-footer a { 
            color: #bdc3c7; 
            text-decoration: none; 
            display: block; 
            padding: 8px 0;
            transition: color 0.3s;
            font-size: 13px;
        }

        .sidebar-footer a:hover {
            color: var(--primary-color);
        }

        .sidebar-footer a i {
            margin-right: 8px;
            width: 16px;
        }

        /* === CONTEÚDO PRINCIPAL === */
        .content { 
            margin-left: 260px; 
            padding: 30px; 
            width: 100%; 
            box-sizing: border-box;
        }
       
        /* Header do Dashboard */
        .dashboard-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .dashboard-header h1 { 
            margin: 0; 
            color: #333; 
            font-weight: 600;
            font-size: 28px;
            letter-spacing: 0.5px;
        }

        .dashboard-header p {
            color: #666;
            font-size: 14px;
        }

        .user-info { 
            display: flex; 
            align-items: center; 
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(233, 30, 99, 0.3);
        }

        .user-text {
            text-align: right;
        }

        .user-text .name {
            display: block;
            font-weight: 600;
            color: #333;
        }

        .user-text .role {
            font-size: 12px;
            color: #888;
        }

        /* === CARDS DE MÉTRICAS === */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card-metric {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 4px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card-metric:hover { 
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .card-metric::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .card-metric.faturamento { border-bottom-color: var(--success-color); }
        .card-metric.pedidos { border-bottom-color: var(--info-color); }
        .card-metric.clientes { border-bottom-color: var(--warning-color); }
        .card-metric.produtos { border-bottom-color: var(--primary-color); }
        .card-metric.pendentes { border-bottom-color: var(--danger-color); }
        .card-metric.novos { border-bottom-color: #9b59b6; }

        .metric-info h3 { 
            margin: 0 0 8px 0; 
            font-size: 13px; 
            color: #7f8c8d; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        .metric-info p { 
            margin: 0; 
            font-size: 32px; 
            font-weight: 700; 
            color: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
       
        .metric-info .trend {
            font-size: 12px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .metric-info .trend.positive {
            color: var(--success-color);
        }

        .metric-info .trend.negative {
            color: var(--danger-color);
        }

        .metric-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            transition: transform 0.3s ease;
        }

        .card-metric:hover .metric-icon {
            transform: scale(1.1);
        }

        .faturamento .metric-icon { background: linear-gradient(135deg, #eafaf1, #d4efdf); color: var(--success-color); }
        .pedidos .metric-icon { background: linear-gradient(135deg, #eaf2f8, #d4e6f1); color: var(--info-color); }
        .clientes .metric-icon { background: linear-gradient(135deg, #fef5e7, #fdebd0); color: var(--warning-color); }
        .produtos .metric-icon { background: linear-gradient(135deg, #fce4ec, #f8bbd9); color: var(--primary-color); }
        .pendentes .metric-icon { background: linear-gradient(135deg, #fdeaea, #f9d6d6); color: var(--danger-color); }
        .novos .metric-icon { background: linear-gradient(135deg, #f0e6f5, #e8daef); color: #9b59b6; }

        /* === ÁREA DE GRÁFICOS === */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            height: 400px;
            display: flex;
            flex-direction: column;
        }

        .chart-container:hover {
            transform: translateY(-2px);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title { 
            margin: 0; 
            font-size: 18px; 
            color: #333; 
            font-weight: 600;
        }

        .chart-wrapper {
            flex: 1;
            position: relative;
        }

        /* === TABELAS E LISTAS === */
        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }

        .table-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .table-container:hover {
            transform: translateY(-2px);
        }

        /* Tabela de Pedidos */
        .recent-orders table { 
            width: 100%; 
            border-collapse: collapse;
        }

        .recent-orders th, .recent-orders td { 
            padding: 15px 12px; 
            text-align: left; 
            border-bottom: 1px solid #f0f0f0; 
            font-size: 14px;
        }

        .recent-orders th { 
            color: #7f8c8d; 
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .recent-orders tr:hover td {
            background: #f8f9fa;
        }

        .status { 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 11px; 
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .status.pago { background: #eafaf1; color: #27ae60; }
        .status.pendente { background: #fef5e7; color: #f39c12; }
        .status.cancelado { background: #fdeaea; color: #e74c3c; }
        .status.processando { background: #eaf2f8; color: #3498db; }
        .status.enviado { background: #f0e6f5; color: #9b59b6; }
        .status.entregue { background: #e8f6f3; color: #1abc9c; }

        /* Lista de Produtos */
        .top-products-list {
            list-style: none;
        }

        .top-product-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .top-product-item:last-child {
            border-bottom: none;
        }

        .product-rank {
            width: 30px;
            height: 30px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            margin-right: 15px;
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
            border: 1px solid #eee;
        }

        .product-info {
            flex: 1;
        }

        .product-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .product-stats {
            font-size: 12px;
            color: #666;
        }

        .view-all-link {
            display: inline-block;
            margin-top: 15px;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s;
        }

        .view-all-link:hover {
            color: #c2185b;
        }

        .view-all-link i {
            margin-left: 5px;
            transition: transform 0.3s;
        }

        .view-all-link:hover i {
            transform: translateX(3px);
        }

        /* Responsividade */
        @media (max-width: 1200px) {
            .charts-grid,
            .tables-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                height: 350px;
            }
        }

        @media (max-width: 768px) {
            .sidebar { 
                width: 70px; 
                position: fixed;
                left: -70px;
                transition: left 0.3s ease;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .sidebar-header h3, 
            .sidebar nav a span,
            .sidebar-footer a span { 
                display: none; 
            }
            
            .sidebar nav a { 
                justify-content: center; 
                padding: 15px; 
            }
            
            .sidebar nav a i { 
                margin-right: 0; 
            }
            
            .content { 
                margin-left: 0; 
                padding: 20px; 
            }
            
            .cards-grid { 
                grid-template-columns: repeat(2, 1fr); 
                gap: 15px;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .user-info {
                align-self: flex-end;
            }
            
            .charts-grid,
            .tables-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .cards-grid { 
                grid-template-columns: 1fr; 
            }
            
            .chart-container,
            .table-container {
                padding: 15px;
            }
            
            .chart-container {
                height: 300px;
            }
        }

        /* Botão Mobile Menu */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 101;
            background: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
            }
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-metric, .chart-container, .table-container {
            animation: fadeIn 0.5s ease forwards;
        }

        .card-metric:nth-child(1) { animation-delay: 0.1s; }
        .card-metric:nth-child(2) { animation-delay: 0.2s; }
        .card-metric:nth-child(3) { animation-delay: 0.3s; }
        .card-metric:nth-child(4) { animation-delay: 0.4s; }
        .card-metric:nth-child(5) { animation-delay: 0.5s; }
        .card-metric:nth-child(6) { animation-delay: 0.6s; }
    </style>
</head>
<body>

    <!-- Botão Mobile Menu -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>YARA Admin</h3>
            <p>Dashboard</p>
        </div>
        <nav>
            <a href="dashboard.php" class="active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="gerenciar_produtos.php">
                <i class="fas fa-gem"></i>
                <span>Produtos</span>
            </a>
            <a href="ver_pedidos.php">
                <i class="fas fa-shopping-bag"></i>
                <span>Pedidos</span>
            </a>
            <a href="ver_usuarios.php">
                <i class="fas fa-users"></i>
                <span>Usuários</span>
            </a>
            <a href="admin_personalizados.php">
    <i class="fas fa-gem"></i>
    <span>Personalizados</span>
</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>Ver Loja</span>
            </a>
            <a href="../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>
    </nav>

    <!-- Conteúdo -->
    <main class="content">
       
        <div class="dashboard-header">
            <div>
                <h1>Dashboard YARA</h1>
                <p>Bem-vindo, <?php echo $_SESSION['usuario']['nome'] ?? 'Administrador'; ?>! Aqui está o resumo da sua loja.</p>
            </div>
            <div class="user-info">
                <div class="user-text">
                    <span class="name"><?php echo $_SESSION['usuario']['nome'] ?? 'Admin'; ?></span>
                    <span class="role">Administrador</span>
                </div>
                <div class="user-avatar"><?php echo strtoupper(substr(($_SESSION['usuario']['nome'] ?? 'A'), 0, 1)); ?></div>
            </div>
        </div>

        <!-- 1. CARDS DE MÉTRICAS -->
        <div class="cards-grid">
           
            <!-- Faturamento Total -->
            <div class="card-metric faturamento">
                <div class="metric-info">
                    <h3>Faturamento Total</h3>
                    <p>R$ <?php echo number_format($total_faturamento, 2, ',', '.'); ?></p>
                    <div class="trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>R$ <?php echo number_format($faturamento_mes, 2, ',', '.'); ?> este mês</span>
                    </div>
                </div>
                <div class="metric-icon"><i class="fas fa-wallet"></i></div>
            </div>

            <!-- Pedidos -->
            <div class="card-metric pedidos">
                <div class="metric-info">
                    <h3>Total de Pedidos</h3>
                    <p><?php echo $total_pedidos; ?></p>
                </div>
                <div class="metric-icon"><i class="fas fa-shopping-bag"></i></div>
            </div>

            <!-- Usuários -->
            <div class="card-metric clientes">
                <div class="metric-info">
                    <h3>Clientes</h3>
                    <p><?php echo $total_usuarios; ?></p>
                    <div class="trend positive">
                        <i class="fas fa-user-plus"></i>
                        <span><?php echo $novos_usuarios_mes; ?> novos este mês</span>
                    </div>
                </div>
                <div class="metric-icon"><i class="fas fa-users"></i></div>
            </div>

            <!-- Produtos -->
            <div class="card-metric produtos">
                <div class="metric-info">
                    <h3>Produtos</h3>
                    <p><?php echo $total_produtos; ?></p>
                </div>
                <div class="metric-icon"><i class="fas fa-gem"></i></div>
            </div>

            <!-- Pedidos Pendentes -->
            <div class="card-metric pendentes">
                <div class="metric-info">
                    <h3>Pendentes</h3>
                    <p><?php echo $pedidos_pendentes; ?></p>
                    <div class="trend <?php echo $pedidos_pendentes > 0 ? 'negative' : 'positive'; ?>">
                        <?php if($pedidos_pendentes > 0): ?>
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Precisa de atenção</span>
                        <?php else: ?>
                            <i class="fas fa-check-circle"></i>
                            <span>Todos processados</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="metric-icon"><i class="fas fa-clock"></i></div>
            </div>

            <!-- Faturamento do Mês -->
            <div class="card-metric novos">
                <div class="metric-info">
                    <h3>Faturamento Mês</h3>
                    <p>R$ <?php echo number_format($faturamento_mes, 2, ',', '.'); ?></p>
                    <div class="trend positive">
                        <i class="fas fa-chart-line"></i>
                        <span>Vendas do mês atual</span>
                    </div>
                </div>
                <div class="metric-icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
            <!-- Pedidos Personalizados -->
<div class="card-metric" style="border-bottom-color: #9c27b0;">
    <div class="metric-info">
        <h3>Personalizados</h3>
        <?php
        $sql_pers = "SELECT COUNT(*) as total FROM pedidos_personalizados WHERE status = 'pendente'";
        $res_pers = $conexao->query($sql_pers);
        $total_personalizados_pendentes = $res_pers ? $res_pers->fetch_assoc()['total'] : 0;
        ?>
        <p><?php echo $total_personalizados_pendentes; ?></p>
        <div class="trend <?php echo $total_personalizados_pendentes > 0 ? 'negative' : 'positive'; ?>">
            <?php if($total_personalizados_pendentes > 0): ?>
                <i class="fas fa-gem"></i>
                <span><?php echo $total_personalizados_pendentes; ?> pendentes</span>
            <?php else: ?>
                <i class="fas fa-check-circle"></i>
                <span>Todos processados</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="metric-icon" style="background: linear-gradient(135deg, #f3e5f5, #e1bee7); color: #9c27b0;">
        <i class="fas fa-cogs"></i>
    </div>
</div>

        </div>

        <!-- 2. GRÁFICOS PRINCIPAIS -->
        <div class="charts-grid">
           
            <!-- Gráfico de Vendas Mensais -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="section-title">Vendas dos Últimos 6 Meses</h3>
                    <span style="font-size: 12px; color: #888;">Valores em R$</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="vendasChart"></canvas>
                </div>
            </div>

            <!-- Gráfico de Categorias -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="section-title">Categorias de Produtos</h3>
                    <span style="font-size: 12px; color: #888;">Distribuição</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="categoriasChart"></canvas>
                </div>
            </div>

        </div>

        <!-- 3. TABELAS E LISTAS -->
        <div class="tables-grid">
           
            <!-- Últimos Pedidos -->
            <div class="table-container recent-orders">
                <div class="chart-header">
                    <h3 class="section-title">Pedidos Recentes</h3>
                    <a href="ver_pedidos.php" class="view-all-link">
                        Ver todos
                    </a>
                </div>
                
                <?php if ($res_ultimos && $res_ultimos->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($pedido = $res_ultimos->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo str_pad($pedido['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($pedido['cliente'] ?: 'Cliente não identificado'); ?></td>
                                <td>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="status <?php echo $pedido['status']; ?>">
                                        <?php echo ucfirst($pedido['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $pedido['data_formatada']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #777; text-align: center; padding: 40px 20px;">Nenhum pedido encontrado.</p>
                <?php endif; ?>
            </div>

            <!-- Produtos Populares -->
            <div class="table-container">
                <div class="chart-header">
                    <h3 class="section-title">Produtos em Destaque</h3>
                    <a href="gerenciar_produtos.php" class="view-all-link">
                        Ver todos
                    </a>
                </div>
                
                <?php if ($res_top_produtos && $res_top_produtos->num_rows > 0): ?>
                    <ul class="top-products-list">
                        <?php $rank = 1; ?>
                        <?php while($produto = $res_top_produtos->fetch_assoc()): ?>
                        <li class="top-product-item">
                            <div class="product-rank"><?php echo $rank++; ?></div>
                            <img src="../imgs/<?php echo htmlspecialchars($produto['imagem']); ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                 onerror="this.src='../imgs/produto-padrao.png'">
                            <div class="product-info">
                                <div class="product-name"><?php echo htmlspecialchars($produto['nome']); ?></div>
                                <div class="product-stats">
                                    <?php if(isset($produto['total_vendido']) && $produto['total_vendido'] > 0): ?>
                                        <span><?php echo $produto['total_vendido']; ?> vendas</span>
                                        <?php if(isset($produto['faturamento_total'])): ?>
                                            • R$ <?php echo number_format($produto['faturamento_total'], 2, ',', '.'); ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span>Produto ativo</span>
                                        • R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: #777; text-align: center; padding: 40px 20px;">Nenhum produto encontrado.</p>
                <?php endif; ?>
            </div>

        </div>

    </main>

    <!-- SCRIPT DO GRÁFICO -->
    <script>
        // Função para alternar sidebar mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Fechar sidebar ao clicar fora (mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const menuBtn = document.querySelector('.mobile-menu-btn');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !menuBtn.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // Gráfico de Vendas Mensais
        const ctxVendas = document.getElementById('vendasChart').getContext('2d');
        const vendasChart = new Chart(ctxVendas, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels_meses); ?>,
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: <?php echo json_encode($valores_meses); ?>,
                    borderColor: '#e91e63',
                    backgroundColor: 'rgba(233, 30, 99, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#e91e63',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'R$ ' + context.parsed.y.toFixed(2).replace('.', ',');
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toFixed(2).replace('.', ',');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Gráfico de Categorias (Pizza)
        const ctxCategorias = document.getElementById('categoriasChart').getContext('2d');
        const categoriasChart = new Chart(ctxCategorias, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($categorias_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($categorias_quantidades); ?>,
                    backgroundColor: [
                        '#e91e63', '#9c27b0', '#3f51b5', '#2196f3', 
                        '#03a9f4', '#00bcd4', '#009688', '#4caf50',
                        '#8bc34a', '#cddc39', '#ffeb3b', '#ffc107'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} produtos (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Atualizar automaticamente a cada 60 segundos
        setInterval(function() {
            location.reload();
        }, 60000);
    </script>

</body>
</html>