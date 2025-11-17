<?php
// index.php
require_once 'funcoes.php';

// Buscar produtos em destaque
$produtosDestaque = getProdutosDestaque();

// CORREÇÃO DOS CAMINHOS DAS IMAGENS - CAMINHO CORRETO
function corrigirCaminhoImagem($caminho) {
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

// Lista de países (lista completa)
$paises = [
    'Brasil', 'Afeganistão', 'África do Sul', 'Albânia', 'Alemanha', 'Andorra', 'Angola', 'Antígua e Barbuda',
    'Arábia Saudita', 'Argélia', 'Argentina', 'Armênia', 'Austrália', 'Áustria', 'Azerbaijão', 'Bahamas',
    'Bangladesh', 'Barbados', 'Barein', 'Bélgica', 'Belize', 'Benin', 'Bielorrússia', 'Bolívia', 'Bósnia e Herzegovina',
    'Botsuana', 'Brunei', 'Bulgária', 'Burkina Faso', 'Burundi', 'Butão', 'Cabo Verde', 'Camarões', 'Camboja',
    'Canadá', 'Catar', 'Cazaquistão', 'Chade', 'Chile', 'China', 'Chipre', 'Colômbia', 'Comores', 'Congo',
    'Coreia do Norte', 'Coreia do Sul', 'Costa do Marfim', 'Costa Rica', 'Croácia', 'Cuba', 'Dinamarca', 'Djibuti',
    'Dominica', 'Egito', 'El Salvador', 'Emirados Árabes Unidos', 'Equador', 'Eritreia', 'Eslováquia', 'Eslovênia',
    'Espanha', 'Estados Unidos', 'Estônia', 'Eswatini', 'Etiópia', 'Fiji', 'Filipinas', 'Finlândia', 'França',
    'Gabão', 'Gâmbia', 'Gana', 'Geórgia', 'Granada', 'Grécia', 'Guatemala', 'Guiana', 'Guiné', 'Guiné Equatorial',
    'Guiné-Bissau', 'Haiti', 'Honduras', 'Hungria', 'Iêmen', 'Ilhas Marshall', 'Ilhas Salomão', 'Índia', 'Indonésia',
    'Irã', 'Iraque', 'Irlanda', 'Islândia', 'Israel', 'Itália', 'Jamaica', 'Japão', 'Jordânia', 'Kiribati', 'Kuwait',
    'Laos', 'Lesoto', 'Letônia', 'Líbano', 'Libéria', 'Líbia', 'Liechtenstein', 'Lituânia', 'Luxemburgo', 'Macedônia do Norte',
    'Madagascar', 'Malásia', 'Malaui', 'Maldivas', 'Mali', 'Malta', 'Marrocos', 'Maurícia', 'Mauritânia', 'México',
    'Mianmar', 'Micronésia', 'Moçambique', 'Moldávia', 'Mônaco', 'Mongólia', 'Montenegro', 'Namíbia', 'Nauru', 'Nepal',
    'Nicarágua', 'Níger', 'Nigéria', 'Noruega', 'Nova Zelândia', 'Omã', 'Países Baixos', 'Palau', 'Panamá', 'Papua-Nova Guiné',
    'Paquistão', 'Paraguai', 'Peru', 'Polônia', 'Portugal', 'Quênia', 'Quirguistão', 'Reino Unido', 'República Centro-Africana',
    'República Checa', 'República Democrática do Congo', 'República Dominicana', 'Romênia', 'Ruanda', 'Rússia', 'Samoa',
    'San Marino', 'Santa Lúcia', 'São Cristóvão e Névis', 'São Tomé e Príncipe', 'São Vicente e Granadinas', 'Seicheles',
    'Senegal', 'Serra Leoa', 'Sérvia', 'Singapura', 'Síria', 'Somália', 'Sri Lanka', 'Sudão', 'Sudão do Sul', 'Suécia',
    'Suíça', 'Suriname', 'Tailândia', 'Taiwan', 'Tajiquistão', 'Tanzânia', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad e Tobago',
    'Tunísia', 'Turcomenistão', 'Turquia', 'Tuvalu', 'Ucrânia', 'Uganda', 'Uruguai', 'Uzbequistão', 'Vanuatu', 'Vaticano',
    'Venezuela', 'Vietnã', 'Zâmbia', 'Zimbábue'
];
sort($paises); // Ordenar alfabeticamente
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
    /* === NAVBAR ATUALIZADA === */
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

    /* Barra de pesquisa */
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

    .search-bar input::placeholder {
      color: #999;
    }

    .search-icon-btn {
      background: none;
      border: none;
      cursor: pointer;
      color: #666;
      transition: color 0.3s;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .search-icon-btn:hover {
      color: #e91e63;
    }

    /* Resultados da pesquisa */
    .resultados-pesquisa {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

    .resultado-item:last-child {
      border-bottom: none;
    }

    .resultado-item img {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 4px;
      margin-right: 12px;
    }

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
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
      box-shadow: 0px 4px 14px rgba(0,0,0,0.15); 
      border-radius: 2px; 
      display: none; 
      gap: 100px; 
      z-index: 9999; 
      white-space: nowrap; 
    } 

    .menu-item:hover .dropdown, .dropdown:hover { 
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

    /* Ajustes para responsividade */
    @media (max-width: 768px) {
      .navbar-container {
        padding: 15px 20px;
        flex-direction: column;
        gap: 15px;
      }
      
      .navbar-menu {
        gap: 20px;
        order: 2;
      }
      
      .navbar-icons {
        gap: 15px;
        order: 3;
      }
      
      .navbar-logo {
        order: 1;
      }
      
      .search-bar input {
        width: 120px;
      }
    }

    /* Dropdown do usuário controlado via JS */
    .user-dropdown {
      display: none !important;
    }

    .user-dropdown.show {
      display: block !important;
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
    @media(max-width: 700px){
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

    @media(max-width: 480px){
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
    .mensagem-curtida, .mensagem-carrinho {
      background: #f8f9fa;
      border: 2px solid #fe7db9;
      border-radius: 10px;
      padding: 15px;
      margin: 20px auto;
      text-align: center;
      max-width: 400px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .mensagem-curtida p, .mensagem-carrinho p {
      margin: 0 0 15px 0;
      color: #333;
      font-size: 16px;
    }

    .mensagem-curtida button, .mensagem-carrinho button {
      background: #fe7db9;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background 0.3s;
    }

    .mensagem-curtida button:hover, .mensagem-carrinho button:hover {
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
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
<nav class="navbar-container">
  <!-- Logo à esquerda -->
  <div class="navbar-logo">
    <img src="imgs/yaraletra.png" alt="YARA Logo">
  </div>
  
  <!-- Menu centralizado -->
  <ul class="navbar-menu">
    <li><a href="index.php">INÍCIO</a></li>
    
    <!-- Item Sobre com dropdown -->
    <li class="menu-item">
      <a href="sobre.php" class="sobre-link">SOBRE</a>
      <div class="dropdown">
        <div>
          <h4>Informações</h4>
          <a href="servicos.php">Serviços</a>
          <a href="contato.php">Contato</a>
        </div>
      </div>
    </li>
    
    <li><a href="novidades.php">NOVIDADES</a></li>

    <!-- Item Acessórios com dropdown -->
    <li class="menu-item">
      <a href="produtos.php" class="acessorios-link">ACESSÓRIOS</a>
      <div class="dropdown">
        <div>
          <h4>Joias Individuais</h4>
          <a href="produtos.php">Todos</a> 
          <a href="colares.php">Colares</a>
          <a href="piercings.php">Piercings</a>
          <a href="aneis.php">Anéis</a>
          <a href="brincos.php">Brincos</a>
          <a href="pulseiras.php">Pulseiras</a>
          <a href="braceletes.php">Braceletes</a>
        </div>
        <div>
          <h4>Experiências</h4>
          <a href="personalize.php">Personalize Já</a>
          <a href="presente.php">Presente</a>
        </div>
      </div>
    </li>
  </ul>
  
  <!-- Lupa e ícones à direita -->
  <div class="navbar-icons">
    <!-- Barra de pesquisa -->
    <div class="search-container">
      <div class="search-bar">
        <input type="text" placeholder="Buscar produtos..." id="inputPesquisa">
        <button class="search-icon-btn" type="button">
          <i class="fas fa-search"></i>
        </button>
        <div class="resultados-pesquisa" id="resultadosPesquisa"></div>
      </div>
    </div>
    
    <!-- Linha divisória transparente -->
    <div class="icon-divider"></div>
    
    <!-- Ícones finos -->
    <div class="nav-icon heart-icon" id="heartIcon" onclick="window.location.href='favoritos.php'">
      <i class="far fa-heart"></i>
    </div>
    
    <!-- Usuário -->
<div class="nav-icon user-icon">
  <i class="far fa-user"></i>
  <div class="user-dropdown">
    <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']): ?>
      <!-- Usuário logado -->
      <a href="perfil.php">Meu Perfil</a>
      <a href="pedidos.php">Meus Pedidos</a>
      <a href="favoritos.php">Favoritos</a>
      <a href="#" class="sair" id="sairConta">Sair</a>
    <?php else: ?>
      <!-- Usuário não logado -->
      <a href="#" id="openLoginMenu">Fazer Login</a>
      <a href="#" id="openSignupMenu">Cadastrar</a>
    <?php endif; ?>
  </div>
</div>
    
    <div class="nav-icon cart-icon" id="carrinho" onclick="window.location.href='carrinho.php'">
      <i class="fas fa-shopping-bag"></i>
      <span class="cart-count"><?php echo isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0; ?></span>
    </div>
  </div>
</nav>

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

<!-- MODAIS -->
<!-- Modal de Contato -->
<div class="contact-overlay" id="contactOverlay" aria-hidden="true" style="display: none;">
  <div class="contact-modal" role="dialog" aria-modal="true" aria-labelledby="contactTitle">
    <button class="close-x" id="closeX" aria-label="Fechar">X</button>

    <img src="imgs/loginho.png" alt="Yara tigre" class="modal-logo">

    <h3 id="contactTitle">Entre em Contato</h3>

    <p class="intro">
      Ficaremos honrados em ajudar com seu pedido, oferecer consultoria personalizada, criar listas de presentes e muito mais. Selecione o canal de contato de sua preferência e fale com um Embaixador YARA.
    </p><br>

    <label class="select-label" for="locationSelect">Por favor, selecione o seu país/região</label>
    <div class="select-wrap">
      <select id="locationSelect" aria-label="Escolha a sua localização">
        <option value="">Escolha a sua localização:</option>
        <?php foreach($paises as $pais): ?>
          <option value="<?php echo $pais; ?>" <?php echo $pais === 'Brasil' ? 'selected' : ''; ?>>
            <?php echo $pais; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div><br>

    <div class="contact-grid" aria-hidden="false">
      <div>
        <div class="contact-block">
          <div class="block-title">Fale Conosco</div>
          <div class="block-desc">Estamos disponíveis para lhe atender com exclusividade nos seguintes horários:</div>
          <div class="block-meta"><i class="fa-solid fa-phone"></i> <span>(11) 4380-0328</span></div>
          <div style="margin-top:8px;">
            <a class="btn-outline" href="tel:+551143800328">Ligar Agora</a>
          </div>
        </div><br>

        <div class="contact-block">
          <div class="block-title">Escreva para Nós</div>
          <div class="block-desc">Um embaixador YARA irá responder dentro de um dia útil.</div>
          <div style="margin-top:8px;">
            <button class="btn-outline" type="button" onclick="window.location.href='mailto:contato@yara.com'">Enviar Email</button>
          </div>
        </div><br>
      </div>

      <div>
        <div class="contact-block">
          <div class="block-title">Atendimento via Chat</div>
          <div class="block-desc">De segunda a sexta, das 10h às 19h, nossos embaixadores estão prontos para ajudar.</div>
          <div style="margin-top:8px;">
            <button class="btn-outline" type="button" onclick="iniciarChat()">Iniciar Chat</button>
          </div>
        </div><br>

        <div class="contact-block">
          <div class="block-title">Fale pelo WhatsApp</div>
          <div class="block-desc">Receba atendimento personalizado de um embaixador YARA.</div>
          <div style="margin-top:8px;">
            <button class="btn-outline" type="button" onclick="abrirWhatsApp()">Enviar Mensagem</button>
          </div>
        </div><br>
      </div>
    </div>

    <div class="contact-actions" style="margin-top:12px;">
      <button class="btn-primary" id="closeModalBtn" type="button">Fechar</button>
    </div>
  </div>
</div>

<!-- Modal Login -->
<div class="login-overlay" id="loginOverlay" aria-hidden="true" style="display: none;">
  <div class="login-modal" role="dialog" aria-modal="true" aria-labelledby="loginTitle">
    <button class="close-x" id="closeLoginX" aria-label="Fechar">X</button>
    <img src="imgs/loginho.png" alt="Logo YARA" class="modal-logo">
    <h3 id="loginTitle">Faça login e encontre o poder de se expressar através de joias únicas.</h3><br>
    <form class="login-form" id="formLogin">
      <input type="email" name="email" placeholder="seuemail@exemplo.com" required>
      <input type="password" name="senha" placeholder="Sua senha" required>
      <button type="submit" class="btn-primary">Entrar</button>
    </form>
    <p style="text-align:center; margin: 12px 0;">
      Ainda não tem uma conta? <a href="#" class="link-cadastro">Cadastre-se</a>
    </p><br>
    <button class="btn-outline" id="loginGoogle">Entrar com Google</button>
  </div>
</div>

<!-- Modal Cadastro -->
<div class="login-overlay" id="signupOverlay" aria-hidden="true" style="display: none;">
  <div class="login-modal" role="dialog" aria-modal="true" aria-labelledby="signupTitle">
    <button class="close-x" id="closeSignupX" aria-label="Fechar">×</button>
    <img src="imgs/loginho.png" alt="Logo YARA" class="modal-logo">
    <h3 id="signupTitle">Crie sua conta</h3>
    <form class="login-form" id="formCadastro" enctype="multipart/form-data">
      <p>Nome Completo</p>
      <input type="text" name="nome" placeholder="Seu nome" required>

      <p>E-mail</p>
      <input type="email" name="email" placeholder="seu.email@exemplo.com" required>

      <p>Senha</p>
      <input type="password" name="senha" placeholder="Mínimo 8 caracteres" required>

      <p>Foto de Perfil (opcional)</p>
      <input type="file" name="foto" accept="image/*">

      <label class="checkbox">
        <input type="checkbox" required>
        <span>Eu concordo com os <a href="#">Termos de Uso</a> e <a href="#">Política de Privacidade</a></span>
      </label>

      <button type="submit" class="btn-primary">Cadastrar</button>
    </form>
    <p>Já tem uma conta? <a href="#" id="goToLogin">Faça login aqui</a></p>
  </div>
</div>

<style>
/* Estilos para os modais */
.contact-overlay, .login-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
}

.contact-modal, .login-modal {
  background: white;
  padding: 30px;
  border-radius: 10px;
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.close-x {
  position: absolute;
  top: 15px;
  right: 15px;
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #333;
}

.modal-logo {
  height: 60px;
  margin-bottom: 20px;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

.btn-primary {
  background: #e91e63;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  width: 100%;
}

.btn-outline {
  background: white;
  color: #e91e63;
  border: 1px solid #e91e63;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
}

.login-form input {
  width: 100%;
  padding: 12px;
  margin: 8px 0;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-sizing: border-box;
}
</style>

<script>
// === SCRIPT ÚNICO E ORGANIZADO ===
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página carregada - inicializando funcionalidades...');

    // --- FUNÇÕES DOS MODAIS ---
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    // --- MODAL DE CONTATO ---
    const openContact = document.getElementById('openContact');
    if (openContact) {
        openContact.addEventListener('click', function(e) {
            e.preventDefault();
            openModal('contactOverlay');
        });
    }

    // Fechar modal de contato
    document.getElementById('closeX')?.addEventListener('click', () => closeModal('contactOverlay'));
    document.getElementById('closeModalBtn')?.addEventListener('click', () => closeModal('contactOverlay'));
    document.getElementById('contactOverlay')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal('contactOverlay');
    });

    // --- MODAIS DE LOGIN E CADASTRO ---
    // Abrir modais
    document.getElementById('openLoginMenu')?.addEventListener('click', function(e) {
        e.preventDefault();
        closeModal('signupOverlay');
        openModal('loginOverlay');
    });

    document.getElementById('openSignupMenu')?.addEventListener('click', function(e) {
        e.preventDefault();
        closeModal('loginOverlay');
        openModal('signupOverlay');
    });

    // Links dentro dos modais
    document.querySelector('.link-cadastro')?.addEventListener('click', function(e) {
        e.preventDefault();
        closeModal('loginOverlay');
        openModal('signupOverlay');
    });

    document.getElementById('goToLogin')?.addEventListener('click', function(e) {
        e.preventDefault();
        closeModal('signupOverlay');
        openModal('loginOverlay');
    });

    // Fechar modais
    document.getElementById('closeLoginX')?.addEventListener('click', () => closeModal('loginOverlay'));
    document.getElementById('closeSignupX')?.addEventListener('click', () => closeModal('signupOverlay'));

    // Fechar ao clicar fora
    document.getElementById('loginOverlay')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal('loginOverlay');
    });

    document.getElementById('signupOverlay')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal('signupOverlay');
    });

    // ESC para fechar modais
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('loginOverlay');
            closeModal('signupOverlay');
            closeModal('contactOverlay');
        }
    });

    // --- DROPDOWN DO USUÁRIO ---
    const userIcon = document.querySelector('.user-icon');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userIcon && userDropdown) {
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function() {
            userDropdown.classList.remove('show');
        });

        // Logout
        const sairConta = document.getElementById('sairConta');
        if (sairConta) {
            sairConta.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Deseja realmente sair?')) {
                    fetch('processa_form.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'acao=logout'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                }
            });
        }
    }

    // --- FORMULÁRIOS AJAX ---
    const formLogin = document.getElementById('formLogin');
    const formCadastro = document.getElementById('formCadastro');
    const formNewsletter = document.getElementById('newsletterForm');

    function mostrarMensagem(mensagem, tipo = 'sucesso') {
        const mensagemDiv = document.getElementById('mensagemFeedback');
        if (mensagemDiv) {
            mensagemDiv.textContent = mensagem;
            mensagemDiv.className = `mensagem ${tipo}`;
            mensagemDiv.style.display = 'block';
            
            setTimeout(() => {
                mensagemDiv.style.display = 'none';
            }, 5000);
        }
    }

    // Login
    if (formLogin) {
        formLogin.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('acao', 'login');
            
            fetch('processa_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensagem(data.message, 'sucesso');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarMensagem(data.message, 'erro');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarMensagem('Erro ao fazer login.', 'erro');
            });
        });
    }

    // Cadastro
    if (formCadastro) {
        formCadastro.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('acao', 'cadastro');
            
            fetch('processa_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensagem(data.message, 'sucesso');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarMensagem(data.message, 'erro');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarMensagem('Erro ao cadastrar.', 'erro');
            });
        });
    }

    // Newsletter
    if (formNewsletter) {
        formNewsletter.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('acao', 'newsletter');
            
            fetch('processa_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensagem(data.message, 'sucesso');
                    this.reset();
                } else {
                    mostrarMensagem(data.message, 'erro');
                }
            });
        });
    }

// === BARRA DE PESQUISA FUNCIONAL COM CAMINHOS CORRETOS ===
document.addEventListener('DOMContentLoaded', function() {
    const inputPesquisa = document.getElementById('inputPesquisa');
    const resultadosPesquisa = document.getElementById('resultadosPesquisa');
    const searchIconBtn = document.querySelector('.search-icon-btn');

    if (!inputPesquisa || !resultadosPesquisa || !searchIconBtn) {
        console.log('Elementos da barra de pesquisa não encontrados');
        return;
    }

    console.log('Inicializando barra de pesquisa...');

    let timeoutPesquisa;

    // Pesquisa em tempo real
    inputPesquisa.addEventListener('input', function() {
        clearTimeout(timeoutPesquisa);
        const termo = this.value.trim();
        
        if (termo.length === 0) {
            resultadosPesquisa.classList.remove('mostrar');
            resultadosPesquisa.innerHTML = '';
            return;
        }
        
        // Debounce - espera 300ms após o usuário parar de digitar
        timeoutPesquisa = setTimeout(() => {
            buscarProdutos(termo);
        }, 300);
    });

    // Focar na barra de pesquisa ao clicar no ícone
    searchIconBtn.addEventListener('click', function() {
        inputPesquisa.focus();
    });

    // Enter na pesquisa
    inputPesquisa.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const termo = this.value.trim();
            if (termo.length > 0) {
                window.location.href = `produtos.php?busca=${encodeURIComponent(termo)}`;
            }
        }
    });

    // Botão de pesquisa
    searchIconBtn.addEventListener('click', function() {
        const termo = inputPesquisa.value.trim();
        if (termo.length > 0) {
            window.location.href = `produtos.php?busca=${encodeURIComponent(termo)}`;
        } else {
            inputPesquisa.focus();
        }
    });

    // Fechar resultados ao clicar fora
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-container')) {
            resultadosPesquisa.classList.remove('mostrar');
        }
    });

    // Função para buscar produtos
    function buscarProdutos(termo) {
        console.log('Buscando produtos para:', termo);
        
        // Busca via AJAX
        fetch('funcoes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'acao=buscar_produtos&termo=' + encodeURIComponent(termo)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na rede');
            }
            return response.json();
        })
        .then(data => {
            console.log('Resposta do servidor:', data);
            
            if (data.success && data.produtos && data.produtos.length > 0) {
                exibirResultados(data.produtos, termo);
            } else {
                // Fallback para busca local
                buscarProdutosLocal(termo);
            }
        })
        .catch(error => {
            console.error('Erro na busca AJAX:', error);
            // Fallback para busca local em caso de erro
            buscarProdutosLocal(termo);
        });
    }

    // Busca local (fallback) - COM CAMINHOS CORRETOS
    function buscarProdutosLocal(termo) {
        const produtosLocais = [
            {
                id: 1,
                nome: 'Conjunto Encanto Lilás',
                preco: 89.90,
                imagem: '../paginas principais/imgs/2novidades.png'
            },
            {
                id: 2,
                nome: 'Espiral de Serenidade',
                preco: 65.50,
                imagem: '../paginas principais/imgs/1novidades.png'
            },
            {
                id: 3,
                nome: 'Conjunto Coração de Rubi',
                preco: 120.00,
                imagem: '../paginas principais/imgs/3novidades.jpg'
            }
        ];

        const termoLower = termo.toLowerCase();
        const resultados = produtosLocais.filter(produto => 
            produto.nome.toLowerCase().includes(termoLower)
        );

        if (resultados.length > 0) {
            exibirResultados(resultados, termo);
        } else {
            exibirResultadosVazios(termo);
        }
    }

    // Exibir resultados da pesquisa - COM CAMINHOS CORRETOS
    function exibirResultados(produtos, termo) {
        resultadosPesquisa.innerHTML = '';
        
        produtos.forEach(produto => {
            // USA O CAMINHO CORRETO DIRETAMENTE
            const imagemSrc = produto.imagem;
            
            const resultadoItem = document.createElement('div');
            resultadoItem.className = 'resultado-item';
            resultadoItem.innerHTML = `
                <img src="${imagemSrc}" alt="${produto.nome}" 
                     onerror="this.src='../paginas principais/imgs/placeholder.jpg'; this.alt='Imagem não disponível'">
                <div class="resultado-info">
                    <h4>${produto.nome}</h4>
                    <div class="preco">R$ ${typeof produto.preco === 'number' ? 
                        produto.preco.toFixed(2).replace('.', ',') : 
                        parseFloat(produto.preco).toFixed(2).replace('.', ',')}
                    </div>
                </div>
            `;
            
            resultadoItem.addEventListener('click', function() {
                window.location.href = `produto_detalhe.php?id=${produto.id}`;
            });
            
            resultadosPesquisa.appendChild(resultadoItem);
        });
        
        // Adicionar link para ver todos os resultados
        if (produtos.length > 0) {
            const verTodos = document.createElement('div');
            verTodos.className = 'resultado-item ver-todos';
            verTodos.innerHTML = `
                <div style="text-align: center; width: 100%; font-weight: 500;">
                    Ver todos os resultados para "${termo}"
                </div>
            `;
            verTodos.addEventListener('click', function() {
                window.location.href = `produtos.php?busca=${encodeURIComponent(termo)}`;
            });
            
            resultadosPesquisa.appendChild(verTodos);
        }
        
        resultadosPesquisa.classList.add('mostrar');
    }

    // Exibir quando não há resultados
    function exibirResultadosVazios(termo) {
        resultadosPesquisa.innerHTML = `
            <div class="resultado-item" style="justify-content: center; color: #666; text-align: center;">
                <div style="width: 100%;">
                    <i class="fas fa-search" style="font-size: 20px; margin-bottom: 8px; display: block;"></i>
                    Nenhum produto encontrado para "${termo}"
                </div>
            </div>
        `;
        resultadosPesquisa.classList.add('mostrar');
    }

    console.log('Barra de pesquisa inicializada com sucesso!');
});

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