<?php
// produto_detalhe.php - VERSÃO FINAL COM FICHA TÉCNICA DINÂMICA
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
require_once 'conexao.php';
require_once 'funcoes.php';

// 1. Verificar se foi passado um ID na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
  header('Location: produtos.php');
  exit;
}

$id_produto = (int)$_GET['id'];

// 2. Buscar as informações do produto no banco
$sql = "SELECT * FROM produtos WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$resultado = $stmt->get_result();

// Se o produto não existir
if ($resultado->num_rows === 0) {
  echo "<h2>Produto não encontrado!</h2><a href='index.php'>Voltar</a>";
  exit;
}

$produto = $resultado->fetch_assoc();

// 3. Cálculos Auxiliares
$parcelas = 5;
$valorParcela = $produto['preco'] / $parcelas;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>YARA - <?php echo htmlspecialchars($produto['nome']); ?></title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* === FUNDO BRANCO E ESTILO UNIFORME === */
    body {
      background-color: #ffffff !important;
      font-family: "Arial", sans-serif;
      color: #333;
      margin: 0;
      padding: 0;
    }

    /* === EFEITO DE APARECER OS ITENS === */
    .fade-in {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.8s ease-out;
    }

    .fade-in.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Aplicar efeito a elementos principais */
    .product-page-container,
    .product-content,
    .product-gallery,
    .product-info,
    .section-title,
    .tech-list,
    .info-block,
    .footer {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.8s ease-out;
    }

    .product-page-container.visible,
    .product-content.visible,
    .product-gallery.visible,
    .product-info.visible,
    .section-title.visible,
    .tech-list.visible,
    .info-block.visible,
    .footer.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* === ESTILOS DO PRODUTO COM FUNDO BRANCO === */
    :root {
      --bg: #ffffff;
      --text: #333333;
      --accent: #e91e63;
      --border-color: #e0e0e0;
      --background-white: #fff;
      --light-gray: #f5f5f5;
    }

    .main-image-wrapper {
      text-align: center;
      background: var(--light-gray);
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }

    .main-image-wrapper:hover {
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    }

    .main-image-wrapper img {
      max-height: 500px;
      width: auto;
      max-width: 100%;
      object-fit: contain;
    }

    .product-page-container {
      max-width: 1100px;
      margin: 40px auto;
      padding: 20px 40px;
      background-color: var(--bg);
    }

    .breadcrumbs {
      font-size: 0.85em;
      color: #888;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid var(--border-color);
    }

    .breadcrumbs a {
      text-decoration: none;
      color: #888;
      transition: color 0.3s;
    }

    .breadcrumbs a:hover {
      color: var(--accent);
    }

    .breadcrumbs span {
      margin: 0 8px;
    }

    .product-content {
      display: flex;
      gap: 60px;
      margin-top: 30px;
    }

    .product-gallery {
      flex: 1;
      max-width: 500px;
    }

    .product-info {
      flex: 1;
      padding: 20px 0;
    }

    .product-info h1 {
      font-family: "Cormorant Garamond", serif;
      font-size: 2.6em;
      line-height: 1.2;
      margin-bottom: 15px;
      color: #000;
      font-weight: 400;
    }

    .product-info .price {
      font-size: 2.8em;
      font-weight: 700;
      color: var(--accent);
      margin-bottom: 8px;
    }

    .product-info .installments {
      font-size: 0.95em;
      color: #666;
      margin-bottom: 30px;
      background: var(--light-gray);
      padding: 10px 15px;
      border-radius: 6px;
      display: inline-block;
    }

    .stock-info {
      font-size: 1em;
      margin-bottom: 25px;
      padding: 10px 0;
      border-bottom: 1px solid var(--border-color);
    }

    .stock-info strong {
      color: #000;
    }

    .actions {
      display: flex;
      gap: 15px;
      margin-bottom: 40px;
      align-items: center;
    }

    .add-to-cart-btn {
      flex-grow: 1;
      padding: 16px 25px;
      background-color: var(--accent);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1em;
      font-weight: 600;
      text-transform: uppercase;
      cursor: pointer;
      transition: all 0.3s ease;
      letter-spacing: 0.5px;
    }

    .add-to-cart-btn:hover {
      background-color: #d84a7e;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3);
    }

    .add-to-cart-btn:disabled {
      background: #ccc;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .section-title {
      display: flex;
      align-items: center;
      margin-top: 40px;
      margin-bottom: 20px;
      position: relative;
    }

    .section-title h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.4em;
      color: #000;
      margin-right: 20px;
      white-space: nowrap;
      font-weight: 500;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .section-title::after {
      content: '';
      flex-grow: 1;
      height: 1px;
      background-color: var(--border-color);
    }

    .details-text {
      font-size: 0.95em;
      color: #555;
      line-height: 1.8;
      white-space: pre-line;
      padding: 15px 0;
    }

    /* Estilos da Lista de Informações Técnicas */
    .tech-list {
      list-style: none;
      padding: 0;
      margin: 0;
      background: var(--light-gray);
      border-radius: 8px;
      padding: 20px;
    }

    .tech-list li {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px dashed #ddd;
      font-size: 0.95em;
      transition: background-color 0.2s;
    }

    .tech-list li:hover {
      background-color: rgba(255, 255, 255, 0.7);
      padding-left: 10px;
      padding-right: 10px;
      margin-left: -10px;
      margin-right: -10px;
    }

    .tech-list li:last-child {
      border-bottom: none;
    }

    .tech-label {
      font-weight: 600;
      color: #000;
    }

    .tech-value {
      color: #666;
      text-transform: capitalize;
    }

    /* Blocos de informação */
    .info-block {
      background: var(--light-gray);
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      border-left: 4px solid var(--accent);
    }

    .info-block h3 {
      font-size: 1.1em;
      font-weight: 600;
      margin-bottom: 10px;
      color: #000;
    }

    .info-block p {
      font-size: 0.9em;
      color: #666;
      line-height: 1.6;
      margin: 0;
    }

    /* Responsividade */
    @media (max-width: 768px) {
      .product-content {
        flex-direction: column;
        gap: 30px;
      }

      .product-gallery {
        max-width: 100%;
      }

      .product-page-container {
        padding: 15px;
      }

      .product-info h1 {
        font-size: 2em;
      }

      .product-info .price {
        font-size: 2.2em;
      }

      .actions {
        flex-direction: column;
      }

      .add-to-cart-btn {
        width: 100%;
      }
    }

    /* Footer estilizado para fundo branco */
    .footer {
      background: #000;
      color: #fff;
      padding: 40px 20px 20px;
      margin-top: 60px;
    }

    .footer-container {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .footer-col h3,
    .footer-col h4 {
      margin-bottom: 15px;
      color: #fff;
    }

    .footer-col p {
      margin: 8px 0;
      color: #ccc;
    }

    .footer-col ul {
      list-style: none;
      padding: 0;
    }

    .footer-col ul li {
      margin-bottom: 8px;
    }

    .footer-col ul li a {
      text-decoration: none;
      color: #ccc;
      transition: 0.3s;
    }

    .footer-col ul li a:hover {
      color: #fe7db9;
    }

    .social {
      display: flex;
      gap: 15px;
    }

    .social a {
      color: #ccc;
      font-size: 18px;
      transition: 0.3s;
    }

    .social a:hover {
      color: #fe7db9;
    }

    .footer-bottom {
      text-align: center;
      border-top: 1px solid #fe7db9;
      margin-top: 20px;
      padding-top: 10px;
      font-size: 14px;
      color: #fe7db9;
      max-width: 1200px;
      margin: 20px auto 0;
    }

    /* Botão voltar ao topo */
    .back-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: var(--accent);
      color: white;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      box-shadow: 0 4px 15px rgba(233, 30, 99, 0.3);
      opacity: 0;
      transition: all 0.3s ease;
      z-index: 100;
    }

    .back-to-top.visible {
      opacity: 1;
    }

    .back-to-top:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(233, 30, 99, 0.4);
    }
  </style>
</head>

<body>

  <?php include 'navbar.php'; ?>

  <main class="product-page-container">

    <nav class="breadcrumbs">
      <a href="index.php">Início</a>
      <span>/</span>
      <a href="produtos.php?cat=<?php echo $produto['categoria']; ?>"><?php echo ucfirst($produto['categoria']); ?></a>
      <span>/</span>
      <?php echo htmlspecialchars($produto['nome']); ?>
    </nav>

    <section class="product-content">

      <div class="product-gallery">
        <div class="main-image-wrapper">
          <img src="imgs/<?php echo htmlspecialchars($produto['imagem']); ?>" 
               alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
               id="mainImage" 
               onerror="this.src='imgs/produto-padrao.png'">
        </div>
      </div>

      <div class="product-info">
        <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>

        <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>

        <p class="installments">ou em até <strong>5x de R$ <?php echo number_format($valorParcela, 2, ',', '.'); ?></strong> sem juros</p>

        <p class="stock-info"><strong>Estoque:</strong> 
            <?php 
                if ($produto['estoque'] > 5) {
                    echo $produto['estoque'] . ' unidades';
                } elseif ($produto['estoque'] > 0) {
                    echo '<span style="color:#e67e22">Restam apenas ' . $produto['estoque'] . ' unidades!</span>';
                } else {
                    echo '<span style="color:#e91e63">Esgotado</span>';
                }
            ?>
        </p>

        <div class="actions">
          <?php if ($produto['estoque'] > 0): ?>
            <button type="button" class="add-to-cart-btn" onclick="adicionarAoCarrinho(<?php echo $produto['id']; ?>)">
              <i class="fas fa-shopping-bag"></i> Adicionar à Sacola
            </button>
          <?php else: ?>
            <button type="button" class="add-to-cart-btn" style="background:#ccc; cursor:not-allowed;" disabled>
              <i class="fas fa-times-circle"></i> Produto Indisponível
            </button>
          <?php endif; ?>
        </div>

        <?php if (!empty($produto['descricao']) && $produto['descricao'] != '0'): ?>
            <div class="section-title">
              <h2>DETALHES</h2>
            </div>
            <div class="details-text">
              <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?>
            </div>
        <?php endif; ?>

        <div class="section-title">
          <h2>FICHA TÉCNICA</h2>
        </div>
        
        <ul class="tech-list">
            <li>
                <span class="tech-label">Categoria</span>
                <span class="tech-value"><?php echo ucfirst($produto['categoria']); ?></span>
            </li>

            <?php if (!empty($produto['material'])): ?>
            <li>
                <span class="tech-label">Material</span>
                <span class="tech-value"><?php echo htmlspecialchars($produto['material']); ?></span>
            </li>
            <?php endif; ?>

            <?php if (!empty($produto['colecao'])): ?>
            <li>
                <span class="tech-label">Coleção</span>
                <span class="tech-value"><?php echo htmlspecialchars($produto['colecao']); ?></span>
            </li>
            <?php endif; ?>

            <?php if (!empty($produto['peso_gramas']) && $produto['peso_gramas'] > 0): ?>
            <li>
                <span class="tech-label">Peso Aproximado</span>
                <span class="tech-value"><?php echo htmlspecialchars($produto['peso_gramas']); ?> g</span>
            </li>
            <?php endif; ?>

            <?php if (!empty($produto['comprimento_cm']) && $produto['comprimento_cm'] > 0): ?>
            <li>
                <span class="tech-label">Comprimento</span>
                <span class="tech-value"><?php echo htmlspecialchars($produto['comprimento_cm']); ?> cm</span>
            </li>
            <?php endif; ?>
        </ul>

        <div class="section-title">
          <h2>INFORMAÇÕES</h2>
        </div>
        <div class="info-block">
          <h3><i class="fas fa-gem" style="color:#e91e63; margin-right:10px;"></i> Qualidade YARA</h3>
          <p>As nossas Semijoias possuem camadas de banho de ouro ou ródio, garantindo durabilidade e um acabamento premium.</p>
        </div>
        <div class="info-block">
          <h3><i class="fas fa-shield-alt" style="color:#e91e63; margin-right:10px;"></i> Garantia</h3>
          <p>Oferecemos 1 ano de garantia no banho e na micro cravação das zircônias.</p>
        </div>

      </div>
    </section>
  </main>

  <!-- Botão voltar ao topo -->
  <a href="#" class="back-to-top" id="backToTop">
    <i class="fas fa-chevron-up"></i>
  </a>

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

  <script>
    // === EFEITO DE APARECER OS ITENS DA PÁGINA ===
    document.addEventListener('DOMContentLoaded', function() {
      // Seleciona todos os elementos principais para animação
      const elements = document.querySelectorAll('.product-page-container, .product-content, .product-gallery, .product-info, .section-title, .tech-list, .info-block, .footer');
      const backToTop = document.getElementById('backToTop');
      
      // Função para verificar se o elemento está visível na tela
      function checkVisibility() {
        elements.forEach(element => {
          const elementTop = element.getBoundingClientRect().top;
          const windowHeight = window.innerHeight;
          
          // Se o elemento estiver visível na tela (80% da altura da janela)
          if (elementTop < windowHeight * 0.8) {
            element.classList.add('visible');
          }
        });
        
        // Mostrar/ocultar botão voltar ao topo
        if (window.scrollY > 300) {
          backToTop.classList.add('visible');
        } else {
          backToTop.classList.remove('visible');
        }
      }
      
      // Verificar visibilidade quando a página carrega
      setTimeout(() => checkVisibility(), 100);
      
      // Verificar visibilidade quando o usuário scrolla
      window.addEventListener('scroll', checkVisibility);
      
      // Animar a navbar quando a página carrega
      const navbar = document.querySelector('.navbar-container');
      if (navbar) {
        setTimeout(() => {
          navbar.style.opacity = '1';
          navbar.style.transform = 'translateY(0)';
        }, 100);
      }
      
      // Botão voltar ao topo
      backToTop.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });
    });

    // Função Adicionar Carrinho
    function adicionarAoCarrinho(idProduto) {
      const formData = new FormData();
      formData.append('acao', 'adicionar_carrinho');
      formData.append('produto_id', idProduto);

      fetch('processa_form.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              title: 'Adicionado!',
              text: 'Produto adicionado à sua sacola.',
              icon: 'success',
              confirmButtonColor: '#e91e63',
              confirmButtonText: 'Continuar comprando',
              showCancelButton: true,
              cancelButtonText: 'Ir para o carrinho',
              cancelButtonColor: '#333'
            }).then((result) => {
              if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = 'carrinho.php';
              }
            });

            // Atualiza contador
            if(data.total_carrinho && document.querySelectorAll('.cart-count').length > 0) {
                document.querySelectorAll('.cart-count').forEach(c => c.textContent = data.total_carrinho);
            }
          } else {
            Swal.fire('Erro', data.message || 'Erro ao adicionar.', 'error');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire('Erro', 'Erro de conexão.', 'error');
        });
    }
  </script>

</body>
</html>