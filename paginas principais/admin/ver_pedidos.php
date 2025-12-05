<?php

session_start();

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
// admin/ver_pedidos.php
require_once '../funcoes.php';

// Buscar pedidos com nome do usuário
$sql = "SELECT p.id, p.data_pedido, p.valor_total, p.status, p.forma_pagamento, u.nome, u.email
        FROM pedidos p
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.data_pedido DESC";
$result = $conexao->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pedidos</title>
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

        body {
            margin: 0;
            font-family: var(--font-family);
            background-color: var(--content-bg);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
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
            z-index: 100;
        }

        .sidebar-header {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid #34495e;
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
            transition: 0.3s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background-color: #34495e;
            border-left: 4px solid var(--primary-color);
        }

        .sidebar nav a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid #34495e;
        }

        .sidebar-footer a {
            color: #bdc3c7;
            text-decoration: none;
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }

        /* Conteúdo */
        .content {
            margin-left: 260px;
            padding: 30px;
            width: 100%;
            box-sizing: border-box;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            margin: 0;
            color: #333;
        }

        /* Tabela */
        .table-container {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        th {
            color: #7f8c8d;
            background-color: #fafafa;
            font-weight: 600;
        }

        /* Status Badges */
        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status.pago {
            background: #d4edda;
            color: #155724;
        }

        .status.pendente {
            background: #fff3cd;
            color: #856404;
        }

        .status.cancelado {
            background: #f8d7da;
            color: #721c24;
        }

        .status.enviado {
            background: #cce5ff;
            color: #004085;
        }

        .status.processando {
            background: #e2e3e5;
            color: #383d41;
        }

        .btn-detalhes {
            background-color: var(--primary-color);
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            border: none;
            cursor: pointer;
        }

        .btn-detalhes:hover {
            background-color: #c2185b;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }

            .content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <nav class="sidebar">
        <div class="sidebar-header">
            <h3>YARA Admin</h3>
        </div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="gerenciar_produtos.php"><i class="fas fa-box"></i> Produtos</a>
            <a href="ver_pedidos.php" class="active"><i class="fas fa-shopping-cart"></i> Pedidos</a>
            <a href="ver_usuarios.php"><i class="fas fa-users"></i> Usuários</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Loja</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </nav>

    <main class="content">
        <div class="page-header">
            <h1>Pedidos Realizados</h1>
            <p style="color: #777;">Acompanhe as vendas da loja.</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Pagamento</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['data_pedido'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['nome']); ?></strong><br>
                                    <small style="color:#999;"><?php echo htmlspecialchars($row['email']); ?></small>
                                </td>
                                <td>R$ <?php echo number_format($row['valor_total'], 2, ',', '.'); ?></td>
                                <td><?php echo ucfirst($row['forma_pagamento']); ?></td>
                                <td>
                                    <span class="status <?php echo $row['status']; ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-detalhes" onclick="verDetalhesPedido(<?php echo $row['id']; ?>)">
                                        <i class="fas fa-eye"></i> Detalhes
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:30px;">Nenhum pedido encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal de Detalhes -->
    <div id="modalDetalhes" class="modal-overlay">
        <div class="modal-content">
            <button class="close-modal" onclick="fecharModal()">&times;</button>
            <div id="modalBody">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i> Carregando detalhes...
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos do Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            border-radius: 12px;
            padding: 30px;
            position: relative;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: translateY(0);
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            transition: color 0.3s;
            z-index: 10;
        }

        .close-modal:hover {
            color: #e91e7d;
        }

        .loading-spinner {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 16px;
        }
    </style>

    <script>
        const modal = document.getElementById('modalDetalhes');
        const modalBody = document.getElementById('modalBody');

        function verDetalhesPedido(idPedido) {
            modal.classList.add('show');
            modalBody.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Carregando detalhes...</div>';

            fetch('get_pedido_detalhes.php?id=' + idPedido)
                .then(response => response.text())
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    modalBody.innerHTML = '<p style="text-align:center; color:red;">Erro ao carregar detalhes.</p>';
                    console.error('Erro:', error);
                });
        }

        function fecharModal() {
            modal.classList.remove('show');
        }

        // Fechar ao clicar fora
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                fecharModal();
            }
        });

        // Fechar com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('show')) {
                fecharModal();
            }
        });

        // Função para atualizar status (chamada de dentro do modal)
        function atualizarStatus(e) {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button');
            const originalText = btn.innerText;

            btn.disabled = true;
            btn.innerText = 'Salvando...';

            const formData = new FormData(form);

            fetch('atualizar_status_pedido.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                        btn.disabled = false;
                        btn.innerText = originalText;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao atualizar status.');
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
        }
    </script>

</body>

</html>