<?php

session_start();

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
// admin/ver_usuarios.php
require_once '../funcoes.php';

// Buscar todos os usuários
$sql = "SELECT id, nome, email, data_cadastro, telefone FROM usuarios ORDER BY data_cadastro DESC";
$result = $conexao->query($sql);

// Estatísticas
$sql_total = "SELECT COUNT(*) as total FROM usuarios";
$total_usuarios = $conexao->query($sql_total)->fetch_assoc()['total'];

$sql_hoje = "SELECT COUNT(*) as hoje FROM usuarios WHERE DATE(data_cadastro) = CURDATE()";
$novos_hoje = $conexao->query($sql_hoje)->fetch_assoc()['hoje'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Usuários</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   
    <style>
        :root { 
            --sidebar-bg: #2c3e50; 
            --sidebar-text: #ecf0f1; 
            --content-bg: #f4f6f9; 
            --card-bg: #ffffff; 
            --primary-color: #e91e7d; 
            --font-family: 'Poppins', sans-serif; 
        }
        @import url('https://fonts.googleapis.com/css?family=Poppins:300,400,600,700&display=swap');
        body { margin: 0; font-family: var(--font-family); background-color: var(--content-bg); display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--sidebar-text); position: fixed; top: 0; left: 0; bottom: 0; display: flex; flex-direction: column; z-index: 100; }
        .sidebar-header { text-align: center; padding: 20px; border-bottom: 1px solid #34495e; }
        .sidebar nav { flex: 1; padding-top: 20px; }
        .sidebar nav a { display: block; padding: 15px 25px; color: var(--sidebar-text); text-decoration: none; transition: 0.3s; }
        .sidebar nav a:hover, .sidebar nav a.active { background-color: #34495e; border-left: 4px solid var(--primary-color); }
        .sidebar nav a i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar-footer { padding: 20px; border-top: 1px solid #34495e; }
        .sidebar-footer a { color: #bdc3c7; text-decoration: none; font-size: 14px; display: block; margin-bottom: 5px; }
        
        /* Conteúdo */
        .content { margin-left: 260px; padding: 30px; width: 100%; box-sizing: border-box; }
        .page-header { margin-bottom: 30px; }
        .page-header h1 { margin: 0; color: #333; }
        
        /* Cards de Estatísticas */
        .stats-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: var(--card-bg); padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; }
        .stat-number { font-size: 32px; font-weight: 700; color: var(--primary-color); margin-bottom: 5px; }
        .stat-label { color: #7f8c8d; font-size: 14px; text-transform: uppercase; }
        
        /* Tabela */
        .table-container { background: var(--card-bg); padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { color: #7f8c8d; background-color: #fafafa; font-weight: 600; }
        
        .user-avatar { 
            width: 40px; height: 40px; 
            background: var(--primary-color); 
            color: white; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: bold; 
            font-size: 16px;
        }
        
        .badge-novo { 
            background: #2ecc71; 
            color: white; 
            padding: 2px 8px; 
            border-radius: 10px; 
            font-size: 10px; 
            margin-left: 5px;
        }
       
        @media (max-width: 768px) { 
            .sidebar { width: 0; overflow: hidden; } 
            .content { margin-left: 0; padding: 20px; } 
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header"><h3>YARA Admin</h3></div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="gerenciar_produtos.php"><i class="fas fa-box"></i> Produtos</a>
            <a href="ver_pedidos.php"><i class="fas fa-shopping-cart"></i> Pedidos</a>
            <a href="ver_usuarios.php" class="active"><i class="fas fa-users"></i> Usuários</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Loja</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </nav>

    <main class="content">
        <div class="page-header">
            <h1>Usuários Cadastrados</h1>
            <p style="color: #777;">Lista de clientes da loja.</p>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_usuarios; ?></div>
                <div class="stat-label">Total de Usuários</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $novos_hoje; ?></div>
                <div class="stat-label">Novos Hoje</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_usuarios - $novos_hoje; ?></div>
                <div class="stat-label">Usuários Antigos</div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="60">Avatar</th>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Data Cadastro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): 
                            $isNovo = date('Y-m-d') == date('Y-m-d', strtotime($row['data_cadastro']));
                        ?>
                        <tr>
                            <td>
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($row['nome'], 0, 1)); ?>
                                </div>
                            </td>
                            <td>#<?php echo $row['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['nome']); ?></strong>
                                <?php if($isNovo): ?>
                                    <span class="badge-novo">NOVO</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo $row['telefone'] ? htmlspecialchars($row['telefone']) : '<span style="color:#999;">Não informado</span>'; ?></td>
                            <td>
                                <?php echo date('d/m/Y H:i', strtotime($row['data_cadastro'])); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding:30px;">Nenhum usuário cadastrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>