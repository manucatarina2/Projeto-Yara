<?php
// admin/admin_personalizados.php - Versão simplificada
session_start();

// Verificar se é admin
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['is_admin'] ?? 0) != 1) {
    header('Location: ../login.php');
    exit();
}

require_once '../conexao.php';

// Buscar pedidos
$sql = "SELECT p.*, u.nome as usuario_nome, u.email as usuario_email 
        FROM pedidos_personalizados p
        LEFT JOIN usuarios u ON p.usuario_id = u.id
        ORDER BY p.data_criacao DESC";
$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pedidos Personalizados</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .pedidos-grid { display: grid; gap: 20px; }
        .pedido-card { 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .pedido-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .pedido-id { font-weight: bold; color: #333; }
        .status { 
            padding: 5px 10px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: bold;
        }
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-produzindo { background: #d1ecf1; color: #0c5460; }
        .status-pronto { background: #d4edda; color: #155724; }
        .status-entregue { background: #e2e3e5; color: #383d41; }
        .pedido-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .info-group { margin-bottom: 10px; }
        .info-label { font-size: 12px; color: #666; margin-bottom: 5px; }
        .info-value { font-weight: 500; }
        .preview-img { max-width: 150px; border-radius: 5px; }
        .btn { 
            padding: 8px 15px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        select { padding: 5px; border-radius: 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pedidos Personalizados</h1>
            <a href="dashboard.php">← Voltar ao Dashboard</a>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="pedidos-grid">
                <?php while($pedido = $result->fetch_assoc()): ?>
                <div class="pedido-card">
                    <div class="pedido-header">
                        <div>
                            <div class="pedido-id">#<?php echo str_pad($pedido['id'], 6, '0', STR_PAD_LEFT); ?></div>
                            <small><?php echo date('d/m/Y H:i', strtotime($pedido['data_criacao'])); ?></small>
                        </div>
                        <span class="status status-<?php echo $pedido['status']; ?>">
                            <?php echo ucfirst($pedido['status']); ?>
                        </span>
                    </div>

                    <div class="pedido-info">
                        <div class="info-group">
                            <div class="info-label">Cliente</div>
                            <div class="info-value"><?php echo htmlspecialchars($pedido['usuario_nome'] ?: 'N/A'); ?></div>
                            <small><?php echo htmlspecialchars($pedido['usuario_email'] ?: ''); ?></small>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Joia</div>
                            <div class="info-value"><?php echo htmlspecialchars($pedido['tipo_joia']); ?></div>
                            <small><?php echo htmlspecialchars($pedido['material']); ?> • <?php echo htmlspecialchars($pedido['pedra']); ?></small>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Detalhes</div>
                            <div class="info-value">Tamanho: <?php echo htmlspecialchars($pedido['tamanho']); ?></div>
                            <?php if (!empty($pedido['gravacao'])): ?>
                                <small>Gravação: "<?php echo htmlspecialchars($pedido['gravacao']); ?>"</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Preview</div>
                            <img src="../<?php echo htmlspecialchars($pedido['imagem_preview']); ?>" 
                                 class="preview-img"
                                 alt="Preview"
                                 onerror="this.src='../imgs/produto-padrao.png'">
                        </div>
                        
                        <div class="info-group">
                            <div class="info-label">Preço</div>
                            <div class="info-value" style="font-size: 18px; color: #e91e63;">
                                R$ <?php echo number_format($pedido['preco_total'], 2, ',', '.'); ?>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                        <form method="POST" action="atualizar_personalizado.php">
                            <input type="hidden" name="id" value="<?php echo $pedido['id']; ?>">
                            
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <select name="status" required>
                                    <option value="pendente" <?php echo $pedido['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                    <option value="produzindo" <?php echo $pedido['status'] == 'produzindo' ? 'selected' : ''; ?>>Produzindo</option>
                                    <option value="pronto" <?php echo $pedido['status'] == 'pronto' ? 'selected' : ''; ?>>Pronto</option>
                                    <option value="entregue" <?php echo $pedido['status'] == 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                                </select>
                                
                                <input type="text" name="observacoes" 
                                       placeholder="Observações" 
                                       value="<?php echo htmlspecialchars($pedido['observacoes'] ?? ''); ?>"
                                       style="flex: 1; padding: 5px;">
                                
                                <button type="submit" class="btn btn-primary">Atualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 10px;">
                <h3 style="color: #666;">Nenhum pedido personalizado encontrado</h3>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Adicionar classes CSS para status
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status').forEach(el => {
                const status = el.textContent.toLowerCase();
                el.classList.add('status-' + status);
            });
        });
    </script>
</body>
</html>