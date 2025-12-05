<?php
// index.php
require_once 'funcoes.php';

// Buscar produtos em destaque
$produtosDestaque = getProdutosDestaque();

// CORREÇÃO DOS CAMINHOS DAS IMAGENS - CAMINHO CORRETO
function corrigirCaminhoImagem($caminho)
{
  // Se já começar com o caminho correto, mantém
  if (strpos($caminho, '../paginas principais/imgs/') === 0) {
    return $caminho;
  }

  // Se tiver '../imgs/', substitui pelo caminho correto
  if (strpos($caminho, '../imgs/') === 0) {
    return str_replace('../imgs/', '../paginas principais/imgs/', $caminho);
  }

  // Se for apenas o nome do arquivo, adiciona o caminho completo
  return '../paginas principais/imgs/' . $caminho;
}

// Lista de países removida (já incluída em navbar.php)
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>YARA - Novidades</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    /* Navbar styles removed - using navbar.css */

    .resultado-info h4 {
      margin: 0 0 4px 0;
      font-size: 13px;
      font-weight: 500;
      color: #333;
    }

    .resultado-info .preco {
      color: #e91e63;
      font-weight: 600;
      font-size: 12px;
    }

    /* Linha divisória */
    .icon-divider {
      width: 1px;
      height: 20px;
      background: rgba(0, 0, 0, 0.2);
      margin: 0 5px;
    }

    /* Ícones finos e minimalistas */
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
      font-weight: 300;
    }

    .nav-icon:hover i {
      color: #e91e63;
    }

    /* Ícone do usuário com dropdown */
    .user-icon {
      position: relative;
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

    .user-icon:hover .user-dropdown,
    .user-dropdown:hover {
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

    .user-dropdown .sair {
      color: #e74c3c;
      border-top: 1px solid #f0f0f0;
      margin-top: 6px;
      padding-top: 10px;
    }

    /* Estilos para o ícone do usuário */
    .user-icon {
      width: 20px;
      height: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .user-icon:hover {
      transform: translateY(-1px);
      color: #e91e63;
    }

    .user-icon i {
      font-size: 16px;
      color: #333;
      transition: color 0.3s ease;
      font-weight: 300;
    }

    .user-icon:hover i {
      color: #e91e63;
    }

    /* Ícone do carrinho com contador */
    .cart-icon {
      position: relative;
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

    /* Estilos para dropdowns do menu */
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

    /* Avatar placeholder */
    .avatar-placeholder {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, #fe7db9, #e91e63);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 14px;
      border: 2px solid #f0f0f0;
      transition: all 0.3s;
    }

    .avatar-placeholder:hover {
      transform: scale(1.05);
      border-color: #e91e63;
    }





    /* === Seção Topo Novidades === */
    .gato-section {
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #000;
      color: #fff;
      position: relative;
      min-height: 50px;
      padding: 20px;
      gap: 100px;
      padding-top: 60px;
      padding: 0;
    }

    .gato-section .conteudo {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      margin-bottom: -100px;
    }

    .gato-section .texto {
      flex: 1;
      min-width: 300px;
      text-align: left;
      padding-left: 0;
      padding-bottom: 0;
    }

    .gato-section h1 {
      font-size: 50px;
      font-weight: 300;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-bottom: 10px;
    }

    .gato-section p {
      font-size: 17px;
      font-weight: 300;
      margin-bottom: 28px;
    }

    .imagem-tigre {
      max-width: 420px;
      flex: 1;
      display: flex;
      justify-content: center;
    }

    .imagem-tigre img {
      width: 100%;
      height: auto;
      display: block;
    }

    /* === Responsividade === */
    @media(max-width: 700px) {
      .gato-section {
        padding: 30px 0 0;
      }

      .gato-section h1 {
        font-size: 2rem;
      }

      .gato-section p {
        font-size: 1rem;
      }

      .imagem-tigre img {
        width: 100%;
      }

      .gato-section .texto {
        padding-left: 20px;
        padding-right: 20px;
        padding-bottom: 20px;
      }
    }

    @media(max-width: 480px) {
      .gato-section .conteudo {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .imagem-tigre {
        width: 400px;
        justify-content: center;
        padding: 40px;
        padding-bottom: 0px;
      }

      .imagem-tigre img {
        width: 100%;
      }
    }

    /* Estilos para mensagens */
    .mensagem-curtida,
    .mensagem-carrinho {
      background: #f8f9fa;
      border: 2px solid #fe7db9;
      border-radius: 10px;
      padding: 15px;
      margin: 20px auto;
      text-align: center;
      max-width: 400px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .mensagem-curtida p,
    .mensagem-carrinho p {
      margin: 0 0 15px 0;
      color: #333;
      font-size: 16px;
    }

    .mensagem-curtida button,
    .mensagem-carrinho button {
      background: #fe7db9;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
    }

    .mensagem-curtida button:hover,
    .mensagem-carrinho button:hover {
      background: #e56ba6;
    }

    .favorito.ativo {
      filter: brightness(0) saturate(100%) invert(25%) sepia(100%) saturate(2000%) hue-rotate(300deg);
    }

    .preco {
      color: #fe7db9;
      font-weight: bold;
      font-size: 18px;
      margin: 10px 0;
    }

    .produto-card {
      position: relative;
      transition: transform 0.3s;
      border: 1px solid #f0f0f0;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
    }

    .produto-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .favorito {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 30px;
      height: 30px;
      cursor: pointer;
      transition: transform 0.2s;
    }

    .favorito:hover {
      transform: scale(1.1);
    }

    .produto-img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 4px;
      margin-bottom: 10px;
    }

    .colecao-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      padding: 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .colecao-section h2 {
      text-align: center;
      font-size: 32px;
      margin: 40px 0 20px 0;
      color: #333;
    }
  </style>
</head>

<body>

  <!-- Nova Navbar com Ícones Finos -->
  <!-- Navbar Padronizada -->
  <?php include 'navbar.php'; ?>

  <!-- Mensagens de feedback -->
  <div id="mensagemFeedback" class="mensagem"></div>

  <!-- === Seção Topo Novidades === -->
  <section class="gato-section">
    <div class="conteudo">
      <div class="texto">
        <h1>DESCUBRA O QUE HÁ DE NOVO</h1>
        <p>Peças inéditas que transformam cada momento em presença única.</p>
      </div>

      <div class="imagem-tigre">
        <img src="../paginas principais/imgs/tigrenovidades.jpg" alt="Imagem destaque novidades">
      </div>
    </div>
  </section>

  <!--Seção Novidades-->
  <section class="colecao-section">
    <h2>NOVIDADES</h2>

    <div class="colecao-container">
      <?php foreach ($produtosDestaque as $produto):
        // USA O CAMINHO CORRETO
        $imagem = $produto['imagem']; // Já vem com o caminho correto da função getProdutosDestaque()
      ?>
        <div class="produto-card" data-produto-id="<?php echo $produto['id']; ?>">
          <img src="<?php echo $imagem; ?>" alt="<?php echo $produto['nome']; ?>" class="produto-img"
            onerror="this.src='../paginas principais/imgs/placeholder.jpg'">
          <img src="../paginas principais/imgs/coracao.png" alt="Curtir" class="favorito <?php echo isFavorito($produto['id']) ? 'ativo' : ''; ?>"
            onclick="toggleFavorito(this, <?php echo $produto['id']; ?>, '<?php echo $produto['nome']; ?>')">
          <h3><?php echo $produto['nome']; ?></h3>
          <p class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
          <button onclick="adicionarAoCarrinho(<?php echo $produto['id']; ?>, '<?php echo $produto['nome']; ?>')">
            Adicionar ao carrinho
          </button>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- === Seção Newsletter === -->
  <section class="newsletter-section">
    <div class="newsletter-container">
      <div class="newsletter-logo">
        <img src="imgs/logo.png" alt="Logo YARA">
      </div>

      <div class="newsletter-content">
        <h2>Descubra primeiro todas as novidades <br> da Yara. Cadastre-se!</h2>
        <form class="newsletter-form" id="newsletterForm">
          <input type="email" name="email" placeholder="Digite aqui o seu e-mail" required>
          <button type="submit" id="confirmEmailBtn">&#8594;</button>
        </form>

        <label class="checkbox">
          <input type="checkbox" required>
          <span>Li e concordo com a <a href="#">Política de privacidade</a></span>
        </label>
      </div>
    </div>
  </section>

  <!-- ... (todo o código HTML/PHP anterior permanece igual até o footer) ... -->

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

  <!-- Modais removidos (já incluídos em navbar.php) -->



  <script>
    // === SCRIPT ÚNICO E ORGANIZADO ===
    console.log('Página carregada - inicializando funcionalidades...');
    // Funcionalidades da navbar e modais já estão em navbar.php
    // Scripts específicos da página novidades.php abaixo:


    // Funções globais
    function iniciarChat() {
      window.location.href = 'chat.php';
    }

    function abrirWhatsApp() {
      const numero = '5511999999999';
      const mensagem = 'Olá, gostaria de mais informações sobre as joias YARA.';
      const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagem)}`;
      window.open(url, '_blank');
    }

    function toggleFavorito(elemento, produtoId, nomeProduto) {
      fetch('funcoes.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'acao=toggle_favorito&produto_id=' + produtoId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            elemento.classList.toggle('ativo');
            const msg = document.getElementById("mensagemCurtida");
            const texto = document.getElementById("textoCurtida");

            if (data.acao === 'adicionado') {
              texto.textContent = `"${nomeProduto}" adicionado aos favoritos!`;
            } else {
              texto.textContent = `"${nomeProduto}" removido dos favoritos!`;
            }

            msg.style.display = "block";
            setTimeout(() => {
              msg.style.display = "none";
            }, 3000);
          }
        })
        .catch(error => {
          console.error('Erro:', error);
          mostrarMensagem('Erro ao processar favorito.', 'erro');
        });
    }

    function adicionarAoCarrinho(produtoId, nomeProduto) {
      fetch('funcoes.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'acao=adicionar_carrinho&produto_id=' + produtoId
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Atualizar contador do carrinho
            document.querySelector('.cart-count').textContent = data.total_carrinho;

            // Mostrar mensagem de sucesso
            const msg = document.getElementById("mensagemCarrinho");
            const texto = document.getElementById("textoCarrinho");
            texto.textContent = `"${nomeProduto}" adicionado ao carrinho!`;
            msg.style.display = "block";

            setTimeout(() => {
              msg.style.display = "none";
            }, 3000);
          }
        })
        .catch(error => {
          console.error('Erro:', error);
          mostrarMensagem('Erro ao adicionar ao carrinho.', 'erro');
        });
    }

    function verCurtidos() {
      window.location.href = "favoritos.php";
    }

    function verCarrinho() {
      window.location.href = "carrinho.php";
    }
  </script>
</body>

</html>