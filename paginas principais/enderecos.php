<?php
// enderecos.php - VERSÃO COM CSS DA NAVBAR CORRIGIDO
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';

// Verifica se está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];
$usuario_nome = $_SESSION['usuario']['nome'];

// BUSCAR ENDEREÇOS DO BANCO DE DADOS
$sql = "SELECT * FROM enderecos WHERE usuario_id = ? ORDER BY padrao DESC, id DESC";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Endereços - YARA</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <script src="script.js" defer></script>

    <style>
        /* === CORREÇÃO DA NAVBAR (CSS INSERIDO) === */
        body {
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            font-family: 'Poppins', sans-serif;
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-logo img {
            height: 40px;
            width: auto;
            display: block;
        }

        .navbar-menu {
            display: flex;
            gap: 30px;
            list-style: none;
            /* Remove as bolinhas */
            margin: 0;
            padding: 0;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #000;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .navbar-icons {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-icon {
            cursor: pointer;
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e91e63;
            color: white;
            font-size: 10px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* === ESTILOS DA PÁGINA DE PERFIL === */
        .profile-section {
            padding: 60px 20px;
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

        /* Conteúdo Principal */
        .profile-content {
            flex: 1;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        /* Cards */
        .endereco-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
            transition: 0.3s;
        }

        .endereco-card:hover {
            border-color: #fe7db9;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .endereco-principal {
            border: 2px solid #fe7db9;
            background-color: #fff0f6;
        }

        .tag-principal {
            background: #e91e63;
            color: white;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            text-transform: uppercase;
            margin-left: 10px;
        }

        .endereco-acoes {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .endereco-acoes button {
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
            font-size: 14px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .endereco-acoes button:hover {
            color: #e91e63;
        }

        .btn-add {
            background: #333;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-add:hover {
            background: #e91e63;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.mostrar {
            display: flex;
        }

        .modal {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .btn-primary {
            background: #e91e63;
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        /* Responsividade */
        @media (max-width: 900px) {
            .profile-container {
                flex-direction: column;
            }

            .profile-sidebar {
                width: 100%;
            }

            .navbar-container {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
            }

            .navbar-menu {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar Padronizada -->
    <?php include 'navbar.php'; ?>

    <main class="profile-section">
        <div class="profile-container">

            <aside class="profile-sidebar">
                <h2>Minha Conta</h2>
                <nav class="profile-nav">
                    <ul>
                        <li><a href="perfil.php#dados"><i class="far fa-user"></i> Meus Dados</a></li>
                        <li><a href="perfil.php#pedidos"><i class="fas fa-box-open"></i> Meus Pedidos</a></li>
                        <li><a href="enderecos.php" class="active"><i class="fas fa-map-marker-alt"></i> Endereços</a></li>
                        <li><a href="favoritos.php"><i class="fas fa-heart"></i> Favoritos</a></li>
                        <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                    </ul>
                </nav>
            </aside>

            <section class="profile-content">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
                    <h1 style="font-family:'Playfair Display'; margin:0;">Meus Endereços</h1>
                    <button class="btn-add" onclick="abrirModalEndereco()"><i class="fas fa-plus"></i> Novo Endereço</button>
                </div>

                <?php if ($result->num_rows > 0): ?>
                    <?php while ($end = $result->fetch_assoc()): ?>
                        <div class="endereco-card <?php echo $end['padrao'] ? 'endereco-principal' : ''; ?>">
                            <div class="endereco-titulo" style="font-weight:bold; margin-bottom:5px;">
                                <?php echo htmlspecialchars($end['destinatario']); ?>
                                <?php if ($end['padrao']): ?>
                                    <span class="tag-principal">Principal</span>
                                <?php endif; ?>
                            </div>
                            <div class="endereco-texto" style="font-size:14px; color:#666; line-height:1.6;">
                                <?php echo htmlspecialchars($end['rua']); ?>, <?php echo htmlspecialchars($end['numero']); ?><br>
                                <?php if (!empty($end['complemento'])) echo htmlspecialchars($end['complemento']) . " - "; ?>
                                <?php echo htmlspecialchars($end['bairro']); ?><br>
                                <?php echo htmlspecialchars($end['cidade']); ?> - <?php echo htmlspecialchars($end['estado']); ?><br>
                                CEP: <?php echo htmlspecialchars($end['cep']); ?>
                            </div>
                            <div class="endereco-acoes">
                                <button onclick="excluirEndereco(<?php echo $end['id']; ?>)"><i class="far fa-trash-alt"></i> Excluir</button>
                                <?php if (!$end['padrao']): ?>
                                    <button onclick="definirPrincipal(<?php echo $end['id']; ?>)"><i class="far fa-star"></i> Principal</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align:center; padding:40px; color:#999;">
                        <i class="fas fa-map-marked-alt" style="font-size:40px; margin-bottom:15px; color:#ddd;"></i>
                        <p>Você ainda não cadastrou nenhum endereço.</p>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <div class="modal-overlay" id="modalEndereco">
        <div class="modal">
            <button class="close-modal" onclick="fecharModalEndereco()">&times;</button>
            <h3 style="margin-bottom:20px; font-family:'Playfair Display';">Novo Endereço</h3>

            <form id="formEndereco" class="ajax-form" action="processa_form.php" method="POST">
                <input type="hidden" name="acao" value="adicionar_endereco">

                <div class="form-group">
                    <label>Nome do Destinatário</label>
                    <input type="text" name="destinatario" required>
                </div>

                <div class="form-group">
                    <label>CEP (Digite para buscar)</label>
                    <input type="text" name="cep" id="cep" placeholder="00000-000" maxlength="9" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Rua</label>
                        <input type="text" name="rua" id="rua" required>
                    </div>
                    <div class="form-group">
                        <label>Número</label>
                        <input type="text" name="numero" id="numero" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Bairro</label>
                        <input type="text" name="bairro" id="bairro" required>
                    </div>
                    <div class="form-group">
                        <label>Complemento</label>
                        <input type="text" name="complemento" id="complemento">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Cidade</label>
                        <input type="text" name="cidade" id="cidade" required>
                    </div>
                    <div class="form-group">
                        <label>UF</label>
                        <input type="text" name="estado" id="estado" maxlength="2" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><input type="checkbox" name="padrao" value="1"> Definir como endereço principal</label>
                </div>

                <button type="submit" class="btn-primary">Salvar Endereço</button>
            </form>
        </div>
    </div>

    <footer class="footer" style="background: #000; color: white; padding: 40px 20px 20px; margin-top: 0;">
        <div class="footer-container" style="max-width: 1200px; margin: 0 auto; display: flex; gap: 60px; flex-wrap: wrap;">
            <div class="footer-col">
                <h3 style="margin-bottom: 20px; font-family: 'Playfair Display', serif;">YARA</h3>
                <p>Força e delicadeza em joias.</p>
            </div>
            <div class="footer-col">
                <h4 style="margin-bottom: 20px; text-transform: uppercase; font-size: 14px; letter-spacing: 1px;">YARA</h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 10px;"><a href="sobre.php" style="color: #ccc; text-decoration: none;">Sobre nós</a></li>
                    <li style="margin-bottom: 10px;"><a href="produtos.php" style="color: #ccc; text-decoration: none;">Coleções</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 style="margin-bottom: 20px; text-transform: uppercase; font-size: 14px; letter-spacing: 1px;">Atendimento</h4>
                <p>contato@yara.com</p>
            </div>
        </div>
        <div class="footer-bottom" style="border-top: 1px solid #333; padding-top: 20px; text-align: center; color: #ccc; font-size: 14px; margin-top: 20px;">
            <p>@ 2025 Yara. Todos os direitos reservados</p>
        </div>
    </footer>

    <script>
        const modal = document.getElementById('modalEndereco');

        function abrirModalEndereco() {
            modal.classList.add('mostrar');
        }

        function fecharModalEndereco() {
            modal.classList.remove('mostrar');
        }

        function excluirEndereco(id) {
            if (confirm("Deseja apagar este endereço?")) {
                const fd = new FormData();
                fd.append('acao', 'excluir_endereco');
                fd.append('id_endereco', id);

                fetch('processa_form.php', {
                        method: 'POST',
                        body: fd
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) location.reload();
                        else alert(d.message);
                    });
            }
        }

        function definirPrincipal(id) {
            const fd = new FormData();
            fd.append('acao', 'definir_principal');
            fd.append('id_endereco', id);

            fetch('processa_form.php', {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) location.reload();
                });
        }
    </script>

</body>

</html>