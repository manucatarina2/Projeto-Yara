<?php
// aneis.php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
require_once 'funcoes.php';

// === LÓGICA DE PAGINAÇÃO ===
// 1. Pega o número da página da URL (se não tiver, usa 1)
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

// 2. Define quantos produtos por página (8 conforme pedido)
$itensPorPagina = 8;

// 3. Chama a função de paginação (Certifique-se que a função getProdutosPaginados está no funcoes.php)
// Se a função não existir ainda, vai dar erro. Se der erro, me avise que ajusto aqui.
if (function_exists('getProdutosPaginados')) {
  $dados = getProdutosPaginados('aneis', $pagina, $itensPorPagina);
  $produtos = $dados['produtos'];
  $totalPaginas = $dados['total_paginas'];
} else {
  // Fallback caso a função nova não tenha sido salva no funcoes.php
  $produtos = getProdutosPorCategoria('aneis', 100);
  $totalPaginas = 1;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>YARA - Anéis</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="script.js" defer></script>
  <style>
    /* ... (Seus estilos de navbar mantidos) ... */
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

    /* Barra de pesquisa e Dropdowns (Seus estilos originais) */
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

    .resultados-pesquisa {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      max-height: 300px;
      overflow-y: auto;
      display: none;
      z-index: 1001;
      border: 1px solid #f0f0f0;
    }

    .resultados-pesquisa.mostrar {
      display: block;
    }

    .resultado-item {
      display: flex;
      align-items: center;
      padding: 10px 15px;
      border-bottom: 1px solid #f5f5f5;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .resultado-item:hover {
      background: #f8f8f8;
    }

    .resultado-item img {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 4px;
      margin-right: 12px;
    }

    .icon-divider {
      width: 1px;
      height: 20px;
      background: rgba(0, 0, 0, 0.2);
      margin: 0 5px;
    }

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

    .nav-icon:hover {
      transform: translateY(-1px);
      color: #e91e63;
    }

    .nav-icon i {
      font-size: 16px;
      color: #333;
      transition: color 0.3s ease;
    }

    .nav-icon:hover i {
      color: #e91e63;
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

    .user-dropdown.show {
      display: block;
    }

    .user-dropdown a {
      display: block;
      padding: 10px 16px;
      text-decoration: none;
      color: #333;
      font-size: 13px;
      transition: background-color 0.3s;
    }

    .user-dropdown a:hover {
      background: #f8f8f8;
      color: #e91e63;
    }

    .avatar-placeholder {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: #e91e63;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 14px;
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
      font-weight: 500;
    }

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

    /* Tigre Section */
    .tigre-section {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      background-color: #fff;
      color: #000;
      padding-left: 150px;
      padding-bottom: 0;
      gap: 40px;
      overflow: hidden;
      min-height: 480px;
    }

    .tigre-section .conteudo {
      max-width: 400px;
      flex: 1;
      padding-bottom: 60px;
    }

    .tigre-section h1 {
      font-family: "Cormorant Garamond", serif;
      font-size: 40px;
      font-weight: 300;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-bottom: 10px;
    }

    .tigre-section p {
      font-family: "Lato", sans-serif;
      font-size: 20px;
      font-weight: 300;
      margin-bottom: 28px;
    }

    .tigre-section .imagem-tigre {
      flex: 1;
      display: flex;
      justify-content: flex-end;
      align-items: flex-end;
      margin-bottom: -4px;
    }

    .tigre-section .imagem-tigre img {
      max-width: 650px;
      height: auto;
      object-fit: contain;
    }

    /* Coleção */
    .colecao-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .produto-card {
      background: #fff;
      padding: 20px;
      width: 300px;
      text-align: center;
      border: 1px solid #ddd;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      cursor: pointer;
      transition: transform 0.2s;
    }

    .produto-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .produto-card img.produto-img {
      width: auto;
      height: 250px;
      object-fit: contain;
      margin-bottom: 15px;
    }

    .produto-card h3 {
      margin: 10px 0;
      min-height: 40px;
      font-size: 16px;
    }

    .produto-card button {
      background-color: #e91e7d;
      color: #fff;
      border: none;
      padding: 12px 20px;
      border-radius: 10px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      transition: 0.3s;
    }

    .produto-card button:hover {
      background-color: #e02192;
    }

    .favorito {
      position: absolute;
      top: 10px;
      right: 10px;
      cursor: pointer;
      width: 24px;
    }

    .favorito.ativo {
      filter: invert(27%) sepia(51%) saturate(2878%) hue-rotate(346deg) brightness(104%) contrast(97%);
    }

    /* === PAGINAÇÃO (ESTILO BOTÕES) === */
    .paginacao-container {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 50px;
      margin-bottom: 60px;
      flex-wrap: wrap;
    }

    .pagina-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px 16px;
      min-width: 40px;
      height: 40px;
      border: 1px solid #ddd;
      border-radius: 6px;
      background-color: #fff;
      color: #333;
      text-decoration: none;
      font-family: 'Poppins', sans-serif;
      font-size: 16px;
      font-weight: 500;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .pagina-btn:hover:not(.ativo) {
      background-color: #f9f9f9;
      border-color: #bbb;
    }

    .pagina-btn.ativo {
      background-color: #ff69b4;
      /* Rosa vibrante */
      color: white;
      border-color: #ff69b4;
      cursor: default;
      pointer-events: none;
    }

    /* Newsletter e Footer (Seus estilos) */
    .newsletter-section {
      background: #f8f8f8;
      padding: 60px 20px;
      margin-top: 60px;
    }

    .newsletter-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      gap: 60px;
    }

    .newsletter-logo img {
      height: 80px;
      width: auto;
    }

    .newsletter-content {
      flex: 1;
    }

    .newsletter-content h2 {
      font-family: "Cormorant Garamond", serif;
      font-size: 28px;
      font-weight: 300;
      margin-bottom: 20px;
    }

    .newsletter-form {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
      max-width: 400px;
    }

    .newsletter-form input {
      flex: 1;
      padding: 12px 16px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .newsletter-form button {
      background: #000;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 12px 20px;
      cursor: pointer;
    }

    .footer {
      background: #000;
      color: white;
      padding: 40px 20px 20px;
    }

    .footer-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      gap: 60px;
      margin-bottom: 30px;
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

    @media (max-width: 900px) {
      .tigre-section {
        flex-direction: column;
        text-align: center;
        padding: 40px 20px 0;
      }

      .tigre-section .imagem-tigre {
        justify-content: center;
      }

      .tigre-section .imagem-tigre img {
        max-width: 340px;
      }

      .navbar-container {
        flex-direction: column;
        gap: 15px;
        padding: 15px;
      }

      .newsletter-container {
        flex-direction: column;
        text-align: center;
        gap: 30px;
      }

      .footer-container {
        flex-direction: column;
        gap: 30px;
      }
    }
  </style>
</head>

<body>

  <!-- Navbar Padronizada -->
  <?php include 'navbar.php'; ?>

  <section class="tigre-section">
    <div class="conteudo">
      <h1>ARTE QUE VESTE SEUS DEDOS</h1>
      <p>Criados para impressionar no primeiro olhar e conquistar para sempre.</p>
    </div>
    <div class="imagem-tigre">
      <img src="imgs/tigreanel.png" alt="Tigre">
    </div>
  </section>

  <section class="colecao-section">
    <h2 style="text-align: center; margin: 40px 0;">COLEÇÃO</h2>

    <div class="colecao-container">
      <?php if (!empty($produtos)): ?>
        <?php foreach ($produtos as $produto): ?>
          <div class="produto-card" onclick="window.location.href='produto_detalhe.php?id=<?php echo $produto['id']; ?>'">

            <img src="imgs/<?php echo htmlspecialchars($produto['imagem']); ?>"
              alt="<?php echo htmlspecialchars($produto['nome']); ?>"
              class="produto-img">

            <img src="imgs/coracao.png"
              alt="Curtir"
              class="favorito <?php echo isFavorito($produto['id']) ? 'ativo' : ''; ?>"
              onclick="toggleFavorito(event, this, <?php echo $produto['id']; ?>)">

            <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>

            <p style="color: #e91e7d; font-weight: bold; margin-bottom: 10px;">
              R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
            </p>

            <button type="button" class="btn-comprar" onclick="event.stopPropagation(); adicionarAoCarrinho(<?php echo $produto['id']; ?>)">
              Adicionar ao Carrinho
            </button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center; width: 100%;">Nenhum anel encontrado nesta categoria.</p>
      <?php endif; ?>
    </div>

    <?php if ($totalPaginas > 1): ?>
      <div class="paginacao-container">

        <?php if ($pagina > 1): ?>
          <a href="?pagina=<?php echo $pagina - 1; ?>" class="pagina-btn">Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
          <a href="?pagina=<?php echo $i; ?>" class="pagina-btn <?php echo ($i == $pagina) ? 'ativo' : ''; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>

        <?php if ($pagina < $totalPaginas): ?>
          <a href="?pagina=<?php echo $pagina + 1; ?>" class="pagina-btn">Próximo</a>
        <?php endif; ?>

      </div>
    <?php endif; ?>

  </section>

  <section class="newsletter-section">
    <div class="newsletter-container">
      <div class="newsletter-logo"><img src="imgs/logo.png" alt="Logo YARA"></div>
      <div class="newsletter-content">
        <h2>Descubra primeiro todas as novidades <br>da Yara. Cadastre-se!</h2>
        <form class="newsletter-form" id="newsletterForm">
          <input type="email" placeholder="Digite aqui o seu e-mail" required>
          <button type="submit" id="confirmEmailBtn">&#8594;</button>
        </form>
        <label class="checkbox">
          <input type="checkbox" required>
          <span>Li e concordo com a <a href="#">Política de privacidade</a></span>
        </label>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="footer-container">
      <div class="footer-col">
        <h3>YARA</h3>
        <p>Força e delicadeza em joias que expressam identidade e presença.</p>
        <div class="social">
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
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
        <p><i class="fa-regular fa-envelope"></i> contato@yara.com</p>
        <p><i class="fa-solid fa-phone"></i> (11) 99999-9999</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>@ 2025 Yara. Todos os direitos reservados</p>
    </div>
  </footer>

  <?php if (file_exists('modais.php')) include 'modais.php'; ?>



</body>

</html>