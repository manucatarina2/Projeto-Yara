<?php
// favoritos.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';
require_once 'funcoes.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$usuario = $_SESSION['usuario'];

// BUSCAR FAVORITOS DO BANCO DE DADOS
$produtosFavoritos = getFavoritosUsuario($usuario['id']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Favoritos - YARA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Reutilizando Layout do Perfil */
        body {
            background-color: #f9f9f9;
        }

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

        .profile-content {
            flex: 1;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        /* Grid de Favoritos */
        .fav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .fav-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            position: relative;
            transition: 0.3s;
        }

        .fav-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-color: #fe7db9;
        }

        .fav-img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .fav-title {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }

        .fav-price {
            color: #e91e63;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .btn-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            border: 1px solid #eee;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            color: #999;
            transition: 0.3s;
        }

        .btn-remove:hover {
            color: red;
            border-color: red;
        }

        /* Styles padrao */
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
            font-weight: 500;
        }

        .navbar-icons {
            display: flex;
            gap: 20px;
            align-items: center;
            flex: 0 0 auto;
        }

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
        }

        .footer {
            background: #000;
            color: white;
            padding: 40px 20px 20px;
            margin-top: 0;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            gap: 60px;
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 20px;
            text-align: center;
            color: #ccc;
            font-size: 14px;
            margin-top: 20px;
        }

        @media (max-width: 900px) {
            .navbar-container {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }

            .profile-container {
                flex-direction: column;
            }

            .footer-container {
                flex-direction: column;
                gap: 30px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <!-- Navbar Padronizada -->
    <?php include 'navbar.php'; ?>

    <main class="profile-section">
        <div class="profile-container">

            <!-- SIDEBAR -->
            <aside class="profile-sidebar">
                <h2>Minha Conta</h2>
                <nav class="profile-nav">
                    <ul>
                        <li><a href="perfil.php#dados"><i class="far fa-user"></i> Meus Dados</a></li>
                        <li><a href="perfil.php#pedidos"><i class="fas fa-box-open"></i> Meus Pedidos</a></li>
                        <li><a href="enderecos.php"><i class="fas fa-map-marker-alt"></i> Endereços</a></li>
                        <li><a href="favoritos.php" class="active"><i class="fas fa-heart"></i> Favoritos</a></li>
                        <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- CONTEÚDO -->
            <section class="profile-content">
                <h1 style="font-family:'Playfair Display'; margin-bottom:30px;">Meus Favoritos</h1>

                <?php if (!empty($produtosFavoritos)): ?>
                    <div class="fav-grid">
                        <?php foreach ($produtosFavoritos as $prod): ?>
                            <div class="fav-card">
                                <button class="btn-remove" onclick="removerFavorito(<?php echo $prod['id']; ?>)"><i class="fas fa-times"></i></button>
                                <img src="imgs/<?php echo htmlspecialchars($prod['imagem']); ?>" class="fav-img">
                                <div class="fav-title"><?php echo htmlspecialchars($prod['nome']); ?></div>
                                <div class="fav-price">R$ <?php echo number_format($prod['preco'], 2, ',', '.'); ?></div>
                                <button onclick="adicionarAoCarrinho(<?php echo $prod['id']; ?>)" style="width:100%; padding:10px; background:#333; color:white; border:none; border-radius:4px; cursor:pointer;">Adicionar</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align:center; padding:60px; color:#999;">
                        <i class="fas fa-heart-broken" style="font-size:50px; margin-bottom:20px; color:#eee;"></i>
                        <h3>Nenhum produto favorito</h3>
                        <p>Você ainda não adicionou nenhum produto aos favoritos.</p>
                        <a href="produtos.php" style="display:inline-block; margin-top:20px; padding:10px 25px; background:#fe7db9; color:white; text-decoration:none; border-radius:4px;">Explorar Produtos</a>
                    </div>
                <?php endif; ?>

            </section>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-col">
                <h3>YARA</h3>
                <p>Força e delicadeza em joias.</p>
            </div>
            <div class="footer-col">
                <h4>YARA</h4>
                <ul>
                    <li><a href="sobre.php">Sobre nós</a></li>
                    <li><a href="produtos.php">Coleções</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Atendimento</h4>
                <p>contato@yara.com</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>@ 2025 Yara. Todos os direitos reservados</p>
        </div>
    </footer>

    <script>
        function logout() {
            if (confirm("Deseja realmente sair?")) {
                fetch('processa_form.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'acao=logout'
                    })
                    .then(r => r.json()).then(d => {
                        if (d.success) window.location.href = 'index.php';
                    });
            }
        }

        function removerFavorito(id) {
            fetch('processa_form.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'acao=toggle_favorito&produto_id=' + id
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) location.reload();
                });
        }

        function adicionarAoCarrinho(id) {
            fetch('processa_form.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'acao=adicionar_carrinho&produto_id=' + id
                })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Adicionado!',
                            text: 'Produto adicionado ao carrinho',
                            confirmButtonColor: '#e91e63'
                        });
                        document.querySelector('.cart-count').textContent = d.total_carrinho;
                    }
                });
        }
    </script>

</body>

</html>