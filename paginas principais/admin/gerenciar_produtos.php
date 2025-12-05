<?php

session_start();

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
// admin/gerenciar_produtos.php - VERSÃO FINAL CORRIGIDA E COMPLETA
require_once '../funcoes.php';

$mensagem = '';
$mensagem_erro = '';

// --- 1. PROCESSAR FORMULÁRIO (ADICIONAR OU EDITAR) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao']; 
    
    // Dados comuns
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = str_replace(',', '.', $_POST['preco']);
    $categoria = $_POST['categoria'];
    $material = $_POST['material'];
    $colecao = $_POST['colecao'];
    $estoque = intval($_POST['estoque']);
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    
    // NOVOS CAMPOS (Peso e Comprimento)
    $peso = isset($_POST['peso_gramas']) ? str_replace(',', '.', $_POST['peso_gramas']) : null;
    $comprimento = isset($_POST['comprimento_cm']) ? str_replace(',', '.', $_POST['comprimento_cm']) : null;

    $diretorio_destino = "../imgs/";

    // --- CADASTRO ---
    if ($acao === 'cadastrar') {
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $novo_nome = uniqid() . "." . $extensao;
            
            if (!is_dir($diretorio_destino)) {
                mkdir($diretorio_destino, 0777, true);
            }

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio_destino . $novo_nome)) {
                // Inserção no banco (AGORA COM DESCRIÇÃO COMO STRING 's' E NOVOS CAMPOS)
                $sql = "INSERT INTO produtos (nome, descricao, preco, categoria, material, colecao, estoque, imagem, disponivel, destaque, peso_gramas, comprimento_cm)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)";
                $stmt = $conexao->prepare($sql);
                
                // s=string, d=double, i=integer
                // A CORREÇÃO DO "0" ESTÁ AQUI: O segundo parâmetro agora é 's' (antes estava 'd')
                $stmt->bind_param("ssssssisisd", $nome, $descricao, $preco, $categoria, $material, $colecao, $estoque, $novo_nome, $destaque, $peso, $comprimento);
                
                if ($stmt->execute()) $mensagem = "Produto cadastrado com sucesso!";
                else $mensagem_erro = "Erro no banco: " . $stmt->error;
            } else {
                $mensagem_erro = "Erro no upload da imagem.";
            }
        } else {
            $mensagem_erro = "Selecione uma imagem.";
        }
    }
    
    // --- EDIÇÃO ---
    elseif ($acao === 'editar') {
        $id = intval($_POST['id']);
        
        // Verifica se enviou NOVA imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
            $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $novo_nome = uniqid() . "." . $extensao;
            
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $diretorio_destino . $novo_nome)) {
                $sql = "UPDATE produtos SET nome=?, descricao=?, preco=?, categoria=?, material=?, colecao=?, estoque=?, destaque=?, imagem=?, peso_gramas=?, comprimento_cm=? WHERE id=?";
                $stmt = $conexao->prepare($sql);
                $stmt->bind_param("ssssssissddi", $nome, $descricao, $preco, $categoria, $material, $colecao, $estoque, $destaque, $novo_nome, $peso, $comprimento, $id);
            }
        } else {
            // Atualiza SÓ DADOS (mantém imagem antiga)
            $sql = "UPDATE produtos SET nome=?, descricao=?, preco=?, categoria=?, material=?, colecao=?, estoque=?, destaque=?, peso_gramas=?, comprimento_cm=? WHERE id=?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssssssisddi", $nome, $descricao, $preco, $categoria, $material, $colecao, $estoque, $destaque, $peso, $comprimento, $id);
        }
        
        if (isset($stmt) && $stmt->execute()) {
            $mensagem = "Produto atualizado com sucesso!";
        } else {
            $mensagem_erro = "Erro ao atualizar: " . $conexao->error;
        }
    }
}

// --- 2. LÓGICA DE EXCLUSÃO ---
if (isset($_GET['excluir'])) {
    $id_delete = intval($_GET['excluir']);
    $res = $conexao->query("SELECT imagem FROM produtos WHERE id = $id_delete");
    if ($row = $res->fetch_assoc()) {
        if (file_exists("../imgs/" . $row['imagem'])) unlink("../imgs/" . $row['imagem']);
    }
    $conexao->query("DELETE FROM produtos WHERE id = $id_delete");
    header("Location: gerenciar_produtos.php?msg=deletado");
    exit;
}

// --- 3. BUSCAR PRODUTOS ---
$termo = $_GET['busca'] ?? '';
$sql_produtos = "SELECT * FROM produtos WHERE nome LIKE ? ORDER BY id DESC";
$stmt_prod = $conexao->prepare($sql_produtos);
$termo_like = "%$termo%";
$stmt_prod->bind_param("s", $termo_like);
$stmt_prod->execute();
$result = $stmt_prod->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Produtos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* (MANTENDO SEU CSS ORIGINAL - SEM ALTERAÇÕES VISUAIS DRÁSTICAS) */
        :root { --sidebar-bg: #2c3e50; --sidebar-text: #ecf0f1; --content-bg: #f4f6f9; --card-bg: #ffffff; --primary-color: #e91e7d; --font-family: 'Poppins', sans-serif; }
        @import url('https://fonts.googleapis.com/css?family=Poppins:300,400,600,700&display=swap');
        body { margin: 0; font-family: var(--font-family); background-color: var(--content-bg); display: flex; min-height: 100vh; }
        
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--sidebar-text); position: fixed; top: 0; left: 0; bottom: 0; display: flex; flex-direction: column; z-index: 100; }
        .sidebar-header { text-align: center; padding: 20px; border-bottom: 1px solid #34495e; }
        .sidebar nav { flex: 1; padding-top: 20px; }
        .sidebar nav a { display: block; padding: 15px 25px; color: var(--sidebar-text); text-decoration: none; transition: 0.3s; }
        .sidebar nav a:hover, .sidebar nav a.active { background-color: #34495e; border-left: 4px solid var(--primary-color); }
        .sidebar nav a i { margin-right: 10px; width: 20px; text-align: center; }
        .sidebar-footer { padding: 20px; border-top: 1px solid #34495e; }
        .sidebar-footer a { color: #bdc3c7; text-decoration: none; font-size: 14px; display: block; margin-bottom: 5px; }

        .content { margin-left: 260px; padding: 30px; width: 100%; box-sizing: border-box; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { margin: 0; color: #333; }
        
        .btn-add { background-color: var(--primary-color); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add:hover { background-color: #c2185b; }

        .table-container { background: var(--card-bg); padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { color: #7f8c8d; background-color: #fafafa; }
        .prod-img { width: 40px; height: 40px; border-radius: 4px; object-fit: cover; }
        .action-btn { padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 13px; margin-right: 5px; cursor: pointer; border: none; }
        .btn-edit { background-color: #eaf2f8; color: #3498db; }
        .btn-delete { background-color: #fdedec; color: #e74c3c; }

        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; backdrop-filter: blur(3px); }
        .modal-overlay.open { display: flex; }
        .modal-card { background: #fff; width: 90%; max-width: 700px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto; animation: slideUp 0.3s ease; }
        @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .modal-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h2 { margin: 0; font-size: 20px; color: #333; }
        .btn-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #999; }
        .modal-body { padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; box-sizing: border-box; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; }
        .modal-footer { padding: 20px; border-top: 1px solid #eee; text-align: right; background: #f9f9f9; border-radius: 0 0 8px 8px; }
        .btn-cancel { background: #95a5a6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px; }
        .btn-submit { background: var(--primary-color); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; }
        
        @media (max-width: 768px) { .sidebar { width: 0; overflow: hidden; } .content { margin-left: 0; padding: 20px; } .form-row { flex-direction: column; gap: 0; } }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-header"><h3>YARA Admin</h3></div>
        <nav>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="gerenciar_produtos.php" class="active"><i class="fas fa-box"></i> Produtos</a>
            <a href="ver_pedidos.php"><i class="fas fa-shopping-cart"></i> Pedidos</a>
            <a href="ver_usuarios.php"><i class="fas fa-users"></i> Usuários</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Loja</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
    </nav>

    <main class="content">
        <div class="page-header">
            <h1>Produtos</h1>
            <button onclick="abrirModalCadastro()" class="btn-add"><i class="fas fa-plus"></i> Novo Produto</button>
        </div>

        <?php if ($mensagem): ?>
            <script>Swal.fire({icon: 'success', title: 'Sucesso', text: '<?php echo $mensagem; ?>', confirmButtonColor: '#e91e7d'});</script>
        <?php endif; ?>
        <?php if ($mensagem_erro): ?>
            <script>Swal.fire({icon: 'error', title: 'Erro', text: '<?php echo $mensagem_erro; ?>', confirmButtonColor: '#e91e7d'});</script>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th width="60">Foto</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../imgs/<?php echo $row['imagem']; ?>" class="prod-img" onerror="this.src='../imgs/produto-padrao.png'"></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['nome']); ?></strong>
                                <?php if($row['destaque']): ?><span style="font-size:10px; background:#ffeb3b; padding:2px 5px; border-radius:3px; margin-left:5px;">⭐ Destaque</span><?php endif; ?>
                            </td>
                            <td><?php echo ucfirst($row['categoria']); ?></td>
                            <td>R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></td>
                            <td><?php echo $row['estoque']; ?></td>
                            <td>
                                <button class="action-btn btn-edit" onclick='abrirModalEditar(<?php echo json_encode($row); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="gerenciar_produtos.php?excluir=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Tem certeza?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding:30px;">Nenhum produto encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div class="modal-overlay" id="modalProduto">
        <div class="modal-card">
            <div class="modal-header">
                <h2 id="modalTitulo">Novo Produto</h2>
                <button class="btn-close" onclick="fecharModal()">&times;</button>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data" id="formProduto">
                <div class="modal-body">
                    <input type="hidden" name="acao" id="acaoInput" value="cadastrar">
                    <input type="hidden" name="id" id="idInput" value="">

                    <div class="form-group">
                        <label>Nome do Produto</label>
                        <input type="text" name="nome" id="nome" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" id="descricao" rows="3" class="form-control" required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Preço (R$)</label>
                            <input type="text" name="preco" id="preco" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Estoque</label>
                            <input type="number" name="estoque" id="estoque" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Categoria</label>
                            <select name="categoria" id="categoria" class="form-control" required>
                                <option value="colares">Colares</option>
                                <option value="brincos">Brincos</option>
                                <option value="aneis">Anéis</option>
                                <option value="pulseiras">Pulseiras</option>
                                <option value="piercings">Piercings</option>
                                <option value="braceletes">Braceletes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Material</label>
                            <select name="material" id="material" class="form-control" required>
                                <option value="ouro">Ouro</option>
                                <option value="prata">Prata</option>
                                <option value="diamante">Diamante</option>
                                <option value="ouro_branco">Ouro Branco</option>
                                <option value="pedras">Pedras</option>
                                <option value="platina">Platina</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Coleção</label>
                        <select name="colecao" id="colecao" class="form-control">
                            <option value="classica">Clássica</option>
                            <option value="moderna">Moderna</option>
                            <option value="exclusiva">Exclusiva</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Peso (g)</label>
                            <input type="text" name="peso_gramas" id="peso_gramas" class="form-control" placeholder="Ex: 5.2">
                        </div>
                        <div class="form-group">
                            <label>Comprimento (cm)</label>
                            <input type="text" name="comprimento_cm" id="comprimento_cm" class="form-control" placeholder="Ex: 45">
                        </div>
                    </div>

                    <div class="form-group">
                        <label id="labelImagem">Imagem</label>
                        <input type="file" name="imagem" id="imagem" class="form-control" accept="image/*">
                        <small style="color:#888" id="avisoImagem"></small>
                    </div>

                    <div style="display:flex; gap:10px; align-items:center; margin-top:10px;">
                        <input type="checkbox" name="destaque" id="destaque" value="1">
                        <label for="destaque" style="margin:0; cursor:pointer;">Exibir em Destaque (Home)</label>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="fecharModal()">Cancelar</button>
                    <button type="submit" class="btn-submit" id="btnSalvar">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalProduto');
        const form = document.getElementById('formProduto');
        const titulo = document.getElementById('modalTitulo');
        const acaoInput = document.getElementById('acaoInput');
        const idInput = document.getElementById('idInput');
        const avisoImagem = document.getElementById('avisoImagem');
        const imgInput = document.getElementById('imagem');

        function abrirModalCadastro() {
            form.reset();
            titulo.innerText = "Novo Produto";
            acaoInput.value = 'cadastrar';
            idInput.value = '';
            imgInput.required = true;
            avisoImagem.innerText = "";
            modal.classList.add('open');
        }

        function abrirModalEditar(produto) {
            form.reset();
            titulo.innerText = "Editar Produto";
            acaoInput.value = 'editar';
            idInput.value = produto.id;
            
            document.getElementById('nome').value = produto.nome;
            document.getElementById('descricao').value = produto.descricao;
            document.getElementById('preco').value = produto.preco;
            document.getElementById('estoque').value = produto.estoque;
            document.getElementById('categoria').value = produto.categoria;
            document.getElementById('material').value = produto.material;
            document.getElementById('colecao').value = produto.colecao;
            
            // Popula os novos campos
            document.getElementById('peso_gramas').value = produto.peso_gramas || '';
            document.getElementById('comprimento_cm').value = produto.comprimento_cm || '';
            
            if(produto.destaque == 1) {
                document.getElementById('destaque').checked = true;
            } else {
                document.getElementById('destaque').checked = false;
            }

            imgInput.required = false;
            avisoImagem.innerText = "Deixe vazio para manter a imagem atual.";
            modal.classList.add('open');
        }

        function fecharModal() {
            modal.classList.remove('open');
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) fecharModal();
        });
    </script>

</body>
</html>