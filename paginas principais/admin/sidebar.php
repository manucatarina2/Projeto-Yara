<?php
// admin/sidebar.php
// Componente de sidebar reutilizável para todas as páginas do admin

// Determinar página atual para highlight do menu
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar">
    <div class="sidebar-header">
        <h3>YARA Admin</h3>
        <small style="color: #bdc3c7; font-size: 12px;">Painel de Controle</small>
    </div>
    
    <nav>
        <a href="dashboard.php" class="<?php echo $pagina_atual == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <a href="gerenciar_produtos.php" class="<?php echo $pagina_atual == 'gerenciar_produtos.php' ? 'active' : ''; ?>">
            <i class="fas fa-box"></i> Produtos
        </a>
        
        <a href="ver_pedidos.php" class="<?php echo $pagina_atual == 'ver_pedidos.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Pedidos
            <?php
            // Contador de pedidos pendentes (opcional)
            $sql_pendentes = "SELECT COUNT(*) as pendentes FROM pedidos WHERE status = 'pendente'";
            $result_pendentes = $conexao->query($sql_pendentes);
            if ($result_pendentes) {
                $pendentes = $result_pendentes->fetch_assoc()['pendentes'];
                if ($pendentes > 0) {
                    echo '<span style="background: #e74c3c; color: white; border-radius: 10px; padding: 2px 6px; font-size: 10px; margin-left: auto;">' . $pendentes . '</span>';
                }
            }
            ?>
        </a>
        
        <a href="ver_usuarios.php" class="<?php echo $pagina_atual == 'ver_usuarios.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Usuários
        </a>
        
        <!-- Menu adicional para futuras expansões -->
        <div style="margin-top: 20px; padding: 0 25px; color: #95a5a6; font-size: 12px; text-transform: uppercase;">
            Relatórios
        </div>
        
        <a href="#">
            <i class="fas fa-chart-bar"></i> Relatórios de Vendas
        </a>
        
        <a href="#">
            <i class="fas fa-file-invoice"></i> Relatórios Financeiros
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="../index.php" target="_blank">
            <i class="fas fa-external-link-alt"></i> Ver Loja
        </a>
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
        <small style="color: #7f8c8d; margin-top: 10px; display: block;">
            © 2025 YARA Joias
        </small>
    </div>
</nav>