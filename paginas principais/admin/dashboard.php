<?php
// admin/dashboard.php
require_once '../funcoes.php';

// Verificar se é admin
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario'] || ($_SESSION['usuario']['is_admin'] ?? 0) != 1) {
    header('Location: ../index.php');
    exit();
}

// --- BUSCAR DADOS REAIS DO BANCO ---

// 1. Total de Produtos
$sql_prod = "SELECT COUNT(*) as total FROM produtos";
$total_produtos = $conexao->query($sql_prod)->fetch_assoc()['total'];

// 2. Total de Usuários
$sql_users = "SELECT COUNT(*) as total FROM usuarios";
$total_usuarios = $conexao->query($sql_users)->fetch_assoc()['total'];

// 3. Total de Pedidos
$sql_pedidos = "SELECT COUNT(*) as total FROM pedidos";
$res_pedidos = $conexao->query($sql_pedidos);
$total_pedidos = $res_pedidos ? $res_pedidos->fetch_assoc()['total'] : 0;

// 4. Faturamento Total
$sql_faturamento = "SELECT SUM(valor_total) as total FROM pedidos";
$res_faturamento = $conexao->query($sql_faturamento);
$total_faturamento = $res_faturamento ? $res_faturamento->fetch_assoc()['total'] : 0;

// 5. Buscar os 5 últimos pedidos
$sql_ultimos = "SELECT p.id, u.nome, p.valor_total, p.status, p.data_pedido
                FROM pedidos p
                LEFT JOIN usuarios u ON p.usuario_id = u.id
                ORDER BY p.data_pedido DESC LIMIT 5";
$res_ultimos = $conexao->query($sql_ultimos);

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
        /* === CSS COMBINADO - INDEX.PHP + ADMIN === */
        
        /* Variáveis do tema YARA */
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
            --content-bg: #f8f8f8;
            --card-bg: #ffffff;
            --primary-color: #e91e63;
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
       
        /* === SIDEBAR ESTILO YARA === */
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
       
        /* Header do Dashboard - Estilo YARA */
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
            background: var(--primary-color);
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

        /* === CARDS DE MÉTRICAS - ESTILO YARA === */
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
            background: linear-gradient(90deg, var(--primary-color), #ff6b9d);
        }

        .card-metric.blue { border-bottom-color: #3498db; }
        .card-metric.green { border-bottom-color: #2ecc71; }
        .card-metric.orange { border-bottom-color: #f39c12; }
        .card-metric.pink { border-bottom-color: var(--primary-color); }

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

        .blue .metric-icon { background: linear-gradient(135deg, #eaf2f8, #d4e6f1); color: #3498db; }
        .green .metric-icon { background: linear-gradient(135deg, #eafaf1, #d4efdf); color: #2ecc71; }
        .orange .metric-icon { background: linear-gradient(135deg, #fef5e7, #fdebd0); color: #f39c12; }
        .pink .metric-icon { background: linear-gradient(135deg, #fce4ec, #f8bbd9); color: var(--primary-color); }

        /* === ÁREA DO GRÁFICO E TABELA === */
        .dashboard-lower { 
            display: grid; 
            grid-template-columns: 2fr 1fr; 
            gap: 30px;
        }

        .chart-container, .table-container {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .chart-container:hover, .table-container:hover {
            transform: translateY(-2px);
        }

        .section-title { 
            margin: 0 0 25px 0; 
            font-size: 18px; 
            color: #333; 
            font-weight: 600;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        /* Tabela - Estilo YARA */
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
        }

        .status.pago { background: #eafaf1; color: #27ae60; }
        .status.pendente { background: #fef5e7; color: #f39c12; }
        .status.cancelado { background: #fdeaea; color: #e74c3c; }

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
            .dashboard-lower { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .sidebar-header h3, 
            .sidebar nav a span,
            .sidebar-footer a span { display: none; }
            .sidebar nav a { justify-content: center; padding: 15px; }
            .sidebar nav a i { margin-right: 0; }
            .content { margin-left: 70px; padding: 20px; }
            .cards-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h3>YARA Admin</h3>
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
                <h1>Olá, Administrador!</h1>
                <p>Aqui está o resumo da sua loja hoje.</p>
            </div>
            <div class="user-info">
                <div class="user-text">
                    <span class="name">Admin Yara</span>
                    <span class="role">Gerente</span>
                </div>
                <div class="user-avatar">A</div>
            </div>
        </div>

        <!-- 1. CARDS DE MÉTRICAS -->
        <div class="cards-grid">
           
            <!-- Faturamento -->
            <div class="card-metric green">
                <div class="metric-info">
                    <h3>Faturamento Total</h3>
                    <p>R$ <?php echo number_format($total_faturamento, 2, ',', '.'); ?></p>
                </div>
                <div class="metric-icon"><i class="fas fa-wallet"></i></div>
            </div>

            <!-- Pedidos -->
            <div class="card-metric blue">
                <div class="metric-info">
                    <h3>Total de Pedidos</h3>
                    <p><?php echo $total_pedidos; ?></p>
                </div>
                <div class="metric-icon"><i class="fas fa-shopping-bag"></i></div>
            </div>

            <!-- Usuários -->
            <div class="card-metric orange">
                <div class="metric-info">
                    <h3>Clientes Cadastrados</h3>
                    <p><?php echo $total_usuarios; ?></p>
                </div>
                <div class="metric-icon"><i class="fas fa-users"></i></div>
            </div>

            <!-- Produtos -->
            <div class="card-metric pink">
                <div class="metric-info">
                    <h3>Produtos Ativos</h3>
                    <p><?php echo $total_produtos; ?></p>
                </div>
                <div class="metric-icon"><i class="fas fa-gem"></i></div>
            </div>

        </div>

        <!-- 2. ÁREA DE DETALHES (GRÁFICO E TABELA) -->
        <div class="dashboard-lower">
           
            <!-- Gráfico de Vendas (Chart.js) -->
            <div class="chart-container">
                <h3 class="section-title">Desempenho de Vendas</h3>
                <canvas id="vendasChart"></canvas>
            </div>

            <!-- Últimos Pedidos -->
            <div class="table-container recent-orders">
                <h3 class="section-title">Pedidos Recentes</h3>
                <?php if ($res_ultimos && $res_ultimos->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($pedido = $res_ultimos->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pedido['nome']); ?></td>
                                <td>R$ <?php echo number_format($pedido['valor_total'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="status <?php echo $pedido['status'] == 'pago' ? 'pago' : 'pendente'; ?>">
                                        <?php echo ucfirst($pedido['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #777; text-align: center; padding: 20px;">Nenhum pedido recente.</p>
                <?php endif; ?>
                
                <a href="ver_pedidos.php" class="view-all-link">
                    Ver todos os pedidos <i class="fas fa-arrow-right"></i>
                </a>
            </div>

        </div>

    </main>

    <!-- SCRIPT DO GRÁFICO -->
    <script>
        const ctx = document.getElementById('vendasChart').getContext('2d');
        const vendasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Vendas (R$)',
                    data: [3200, 4500, 3800, 5200, 4800, 6100],
                    borderColor: '#e91e63',
                    backgroundColor: 'rgba(233, 30, 99, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#e91e63',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { 
                        display: false 
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
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
    </script>

</body>
</html>