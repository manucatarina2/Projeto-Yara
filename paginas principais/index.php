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


// Lista de países (lista completa)
$paises = [
  'Brasil',
  'Afeganistão',
  'África do Sul',
  'Albânia',
  'Alemanha',
  'Andorra',
  'Angola',
  'Antígua e Barbuda',
  'Arábia Saudita',
  'Argélia',
  'Argentina',
  'Armênia',
  'Austrália',
  'Áustria',
  'Azerbaijão',
  'Bahamas',
  'Bangladesh',
  'Barbados',
  'Barein',
  'Bélgica',
  'Belize',
  'Benin',
  'Bielorrússia',
  'Bolívia',
  'Bósnia e Herzegovina',
  'Botsuana',
  'Brunei',
  'Bulgária',
  'Burkina Faso',
  'Burundi',
  'Butão',
  'Cabo Verde',
  'Camarões',
  'Camboja',
  'Canadá',
  'Catar',
  'Cazaquistão',
  'Chade',
  'Chile',
  'China',
  'Chipre',
  'Colômbia',
  'Comores',
  'Congo',
  'Coreia do Norte',
  'Coreia do Sul',
  'Costa do Marfim',
  'Costa Rica',
  'Croácia',
  'Cuba',
  'Dinamarca',
  'Djibuti',
  'Dominica',
  'Egito',
  'El Salvador',
  'Emirados Árabes Unidos',
  'Equador',
  'Eritreia',
  'Eslováquia',
  'Eslovênia',
  'Espanha',
  'Estados Unidos',
  'Estônia',
  'Eswatini',
  'Etiópia',
  'Fiji',
  'Filipinas',
  'Finlândia',
  'França',
  'Gabão',
  'Gâmbia',
  'Gana',
  'Geórgia',
  'Granada',
  'Grécia',
  'Guatemala',
  'Guiana',
  'Guiné',
  'Guiné Equatorial',
  'Guiné-Bissau',
  'Haiti',
  'Honduras',
  'Hungria',
  'Iêmen',
  'Ilhas Marshall',
  'Ilhas Salomão',
  'Índia',
  'Indonésia',
  'Irã',
  'Iraque',
  'Irlanda',
  'Islândia',
  'Israel',
  'Itália',
  'Jamaica',
  'Japão',
  'Jordânia',
  'Kiribati',
  'Kuwait',
  'Laos',
  'Lesoto',
  'Letônia',
  'Líbano',
  'Libéria',
  'Líbia',
  'Liechtenstein',
  'Lituânia',
  'Luxemburgo',
  'Macedônia do Norte',
  'Madagascar',
  'Malásia',
  'Malaui',
  'Maldivas',
  'Mali',
  'Malta',
  'Marrocos',
  'Maurícia',
  'Mauritânia',
  'México',
  'Mianmar',
  'Micronésia',
  'Moçambique',
  'Moldávia',
  'Mônaco',
  'Mongólia',
  'Montenegro',
  'Namíbia',
  'Nauru',
  'Nepal',
  'Nicarágua',
  'Níger',
  'Nigéria',
  'Noruega',
  'Nova Zelândia',
  'Omã',
  'Países Baixos',
  'Palau',
  'Panamá',
  'Papua-Nova Guiné',
  'Paquistão',
  'Paraguai',
  'Peru',
  'Polônia',
  'Portugal',
  'Quênia',
  'Quirguistão',
  'Reino Unido',
  'República Centro-Africana',
  'República Checa',
  'República Democrática do Congo',
  'República Dominicana',
  'Romênia',
  'Ruanda',
  'Rússia',
  'Samoa',
  'San Marino',
  'Santa Lúcia',
  'São Cristóvão e Névis',
  'São Tomé e Príncipe',
  'São Vicente e Granadinas',
  'Seicheles',
  'Senegal',
  'Serra Leoa',
  'Sérvia',
  'Singapura',
  'Síria',
  'Somália',
  'Sri Lanka',
  'Sudão',
  'Sudão do Sul',
  'Suécia',
  'Suíça',
  'Suriname',
  'Tailândia',
  'Taiwan',
  'Tajiquistão',
  'Tanzânia',
  'Timor-Leste',
  'Togo',
  'Tonga',
  'Trinidad e Tobago',
  'Tunísia',
  'Turcomenistão',
  'Turquia',
  'Tuvalu',
  'Ucrânia',
  'Uganda',
  'Uruguai',
  'Uzbequistão',
  'Vanuatu',
  'Vaticano',
  'Venezuela',
  'Vietnã',
  'Zâmbia',
  'Zimbábue'
];
sort($paises); // Ordenar alfabeticamente
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>YARA - A arte de vestir presença</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    /* Estilos para navbar reorganizada */
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

    /* Seção Oncinha modificada com vídeo de fundo */
    .oncinha-hero-section {
      position: relative;
      width: 100%;
      height: 600px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      margin: 60px 0;
    }

    .oncinha-video-background {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 1;
    }

    .oncinha-hero-content {
      position: relative;
      z-index: 3;
      text-align: center;
      max-width: 800px;
      padding: 0 20px;
    }

    .oncinha-hero-content h2 {
      font-family: "Cormorant Garamond", serif;
      font-size: 68px;
      font-weight: 300;
      text-transform: uppercase;
      letter-spacing: 3px;
      margin-bottom: 20px;
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
      line-height: 1.1;
    }

    .oncinha-hero-content .btn {
      display: inline-block;
      background-color: rgba(255, 255, 255, 1);
      color: #e91e63;
      border: 1px solid white;
      padding: 14px 32px;
      text-decoration: none;
      font-size: 16px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      margin-top: 20px;
      backdrop-filter: blur(4px);
    }

    .oncinha-hero-content .btn:hover {
      background-color: white;
      color: black;
      transform: translateY(-2px);
    }

    .oncinha-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.3);
      z-index: 2;
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

      .oncinha-hero-section {
        height: 500px;
      }

      .oncinha-hero-content h2 {
        font-size: 42px;
        letter-spacing: 2px;
      }
    }

    @media (max-width: 600px) {
      .oncinha-hero-section {
        height: 400px;
      }

      .oncinha-hero-content h2 {
        font-size: 32px;
        letter-spacing: 1px;
      }

      .oncinha-hero-content .btn {
        padding: 12px 24px;
        font-size: 14px;
      }
    }

    /* Dropdown do usuário controlado via CSS (navbar.css) */
  </style>
</head>

<body>

  <!-- Nova Navbar com Ícones Finos -->
  <!-- Navbar Padronizada -->
  <?php include 'navbar.php'; ?>


  <!-- Barra de Pesquisa (mantida do código original) -->
  <div class="barra-pesquisa" id="barraPesquisaMinimalista">
    <input type="text" id="inputPesquisaMinimalista" placeholder="Digite o nome do produto...">
    <div class="resultados-pesquisa" id="resultadosPesquisaMinimalista"></div>
  </div>

  <!-- === Seção Tigre === -->
  <section class="tigre-section">
    <div class="conteudo">
      <h1>INDOMÁVEL</h1>
      <p>Poder que se veste em diamantes.</p>
      <div class="botoes">
        <a href="produtos.php" class="btn comprar">Comprar agora</a>
        <a href="colares.php" class="btn descubra">Descubra mais</a>
      </div>
    </div>
    <div class="imagem-tigre">
      <img src="imgs/principaltigre.png" alt="Tigre com colar de diamantes">
    </div>
  </section>



  <!-- === Seção Texto === -->
  <section class="texto-section">
    <h2>A ARTE DE VESTIR PRESENÇA</h2>
    <p>
      Desde sua origem, a YARA une força e delicadeza, criando joias que
      transcendem o estilo para se tornarem expressão de identidade e presença.
    </p>
  </section>

  <!-- === Seção Categorias de Produtos === -->
  <section class="categorias-section">
    <h2>COMPRE POR CATEGORIA</h2>
    <div class="categorias-grid">
      <a href="colares.php" class="categoria">
        <div class="card"><img src="imgs/categcolar.png" alt="Colares"></div>
        <div class="categoria-nome">Colares</div>
      </a>
      <a href="pulseiras.php" class="categoria">
        <div class="card"><img src="imgs/categpulseira.png" alt="Pulseiras"></div>
        <div class="categoria-nome">Pulseiras</div>
      </a>
      <a href="brincos.php" class="categoria">
        <div class="card"><img src="imgs/categbrinco.png" alt="Brincos"></div>
        <div class="categoria-nome">Brincos</div>
      </a>
      <a href="braceletes.php" class="categoria">
        <div class="card"><img src="imgs/categbracelete.png" alt="Braceletes"></div>
        <div class="categoria-nome">Braceletes</div>
      </a>
      <a href="aneis.php" class="categoria">
        <div class="card"><img src="imgs/categanel.png" alt="Anéis"></div>
        <div class="categoria-nome">Anéis</div>
      </a>
      <a href="piercings.php" class="categoria">
        <div class="card"><img src="imgs/categpiercing.png" alt="Piercings"></div>
        <div class="categoria-nome">Piercings</div>
      </a>
    </div>
  </section>

  <!-- === Seção Sua Joia, Sua História === -->
  <section class="historia-section">
    <div class="container">
      <h2>Sua Joia, Sua História</h2>
      <p>Onde o excesso e o essencial se encontram e eternizam sua identidade.</p>
      <a href="sobre.php" class="btn">Descubra mais</a>
    </div>
    
  </section>

  <!-- === Seção Oncinha com Vídeo de Fundo === -->
  <section class="oncinha-hero-section">
    <video class="oncinha-video-background" autoplay muted loop playsinline>
      <source src="videos/tigre.mp4" type="video/mp4">
      Seu navegador não suporta o elemento de vídeo.
    </video>
    <div class="oncinha-overlay"></div>
    <div class="oncinha-hero-content">
      <h2>ENCONTRE SUA<br>COLEÇÃO</h2>
      <a href="produtos.php" class="btn">Ver Detalhes</a>
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

  <footer class="footer">
    <div class="footer-container">
      <div class="footer-col">
        <h3>YARA</h3>
        <p>Força e delicadeza em joias que expressam identidade e presença.</p>
        <div class="social">
          <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
          <a href="https://www.facebook.com/"><i class="fab fa-facebook"></i></a>
          <a href="https://www.whatsapp.com/download?lang=pt"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>

      <div class="footer-col">
        <h4>YARA</h4>
        <ul>
          <li><a href="sobre.php">Sobre nós</a></li>
          <li><a href="produtos.php">Produtos</a></li>
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

  <!-- Modal de Contato Atualizado -->
  <div class="contact-overlay" id="contactOverlay" aria-hidden="true">
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
          <?php foreach ($paises as $pais): ?>
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

        <div>
          <div class="contact-block">
            <div class="block-title">Converse com um Especialista em Joias</div>
            <div class="block-desc">De segunda a sexta, das 10h às 19h, nossos embaixadores terão o prazer em lhe orientar.</div>
            <div style="margin-top:8px;">
              <button class="btn-outline" type="button" onclick="iniciarChatEspecialista()">Falar com Especialista</button>
            </div>
          </div><br>

          <div class="contact-block">
            <div class="block-title">Visite-nos em uma Boutique YARA</div>
            <div class="block-desc">Descubra nossas criações em uma de nossas boutiques e viva a experiência exclusiva YARA.</div>
            <div style="margin-top:8px;">
              <button class="btn-outline" type="button" onclick="agendarVisita()">Agendar uma Visita</button>
            </div>
          </div><br>

          <div class="contact-block">
            <div class="block-title">Ajuda</div>
            <div class="block-desc">Tem dúvidas sobre seu pedido, nossos serviços ou política de devolução? Acesse nossa central de ajuda e encontre todas as respostas.</div>
            <div style="margin-top:8px;">
              <button class="btn-outline" type="button" onclick="window.location.href='ajuda.php'">Central de Ajuda</button>
            </div>
          </div>
        </div><br>

      </div>

      <div class="contact-actions" style="margin-top:12px;">
        <button class="btn-primary" id="closeModalBtn" type="button">Fechar</button>
      </div>
    </div>
  </div>

  <!-- Modal Cadastro Atualizado (com upload de foto) -->
  <div class="login-overlay" id="signupOverlay" aria-hidden="true">
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

  <!-- Modal Login -->
  <div class="login-overlay" id="loginOverlay" aria-hidden="true">
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

  <script>
    // === Funções JavaScript principais ===
    document.addEventListener('DOMContentLoaded', function() {

      // --- ABRIR MODAIS DE LOGIN, CADASTRO E CONTATO ---
      const openLoginMenu = document.getElementById('openLoginMenu');
      const openSignupMenu = document.getElementById('openSignupMenu');
      const openContactDropdown = document.getElementById('openContactDropdown');

      const loginOverlay = document.getElementById('loginOverlay');
      const signupOverlay = document.getElementById('signupOverlay');
      const contactOverlay = document.getElementById('contactOverlay');

      // Abrir modal de login
      function openLoginModal() {
        if (loginOverlay) {
          loginOverlay.style.display = 'flex';
          loginOverlay.setAttribute('aria-hidden', 'false');
          document.body.style.overflow = 'hidden';
        }
      }

      // Abrir modal de cadastro
      function openSignupModal() {
        if (signupOverlay) {
          signupOverlay.style.display = 'flex';
          signupOverlay.setAttribute('aria-hidden', 'false');
          document.body.style.overflow = 'hidden';
        }
      }

      // Abrir modal de contato
      function openContactModal() {
        if (contactOverlay) {
          contactOverlay.style.display = 'flex';
          contactOverlay.setAttribute('aria-hidden', 'false');
          document.body.style.overflow = 'hidden';

          const sel = document.getElementById('locationSelect');
          if (sel) sel.focus();
        }
      }

      // Fechar modais
      function closeModals() {
        if (loginOverlay) {
          loginOverlay.style.display = 'none';
          loginOverlay.setAttribute('aria-hidden', 'true');
        }
        if (signupOverlay) {
          signupOverlay.style.display = 'none';
          signupOverlay.setAttribute('aria-hidden', 'true');
        }
        if (contactOverlay) {
          contactOverlay.style.display = 'none';
          contactOverlay.setAttribute('aria-hidden', 'true');
        }
        document.body.style.overflow = '';
      }

      // Botões para abrir modais
      if (openLoginMenu) {
        openLoginMenu.addEventListener('click', function(e) {
          e.preventDefault();
          openLoginModal();
        });
      }

      if (openSignupMenu) {
        openSignupMenu.addEventListener('click', function(e) {
          e.preventDefault();
          openSignupModal();
        });
      }

      if (openContactDropdown) {
        openContactDropdown.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          openContactModal();
        });
      }

      // --- VERIFICAR LOGIN PARA FAVORITOS E CARRINHO ---
      const heartIcon = document.getElementById('heartIcon');
      const cartIcon = document.getElementById('carrinho');

      function usuarioEstaLogado() {
        return <?php echo (isset($_SESSION['usuario']) && $_SESSION['usuario'] !== null) ? 'true' : 'false'; ?>;
      }

      if (heartIcon) {
        heartIcon.addEventListener('click', function(e) {
          if (!usuarioEstaLogado()) {
            e.preventDefault();
            e.stopPropagation();
            openLoginModal();
            return false;
          }
        });
      }

      if (cartIcon) {
        cartIcon.addEventListener('click', function(e) {
          if (!usuarioEstaLogado()) {
            e.preventDefault();
            e.stopPropagation();
            openLoginModal();
            return false;
          }
        });
      }

      // --- FECHAR MODAIS ---
      const closeLoginX = document.getElementById('closeLoginX');
      const closeSignupX = document.getElementById('closeSignupX');
      const closeX = document.getElementById('closeX');
      const closeModalBtn = document.getElementById('closeModalBtn');

      if (closeLoginX) closeLoginX.addEventListener('click', closeModals);
      if (closeSignupX) closeSignupX.addEventListener('click', closeModals);
      if (closeX) closeX.addEventListener('click', closeModals);
      if (closeModalBtn) closeModalBtn.addEventListener('click', closeModals);

      if (loginOverlay) {
        loginOverlay.addEventListener('click', function(e) {
          if (e.target === loginOverlay) closeModals();
        });
      }

      if (signupOverlay) {
        signupOverlay.addEventListener('click', function(e) {
          if (e.target === signupOverlay) closeModals();
        });
      }

      if (contactOverlay) {
        contactOverlay.addEventListener('click', function(e) {
          if (e.target === contactOverlay) closeModals();
        });
      }

      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModals();
      });

      // --- NAVEGAÇÃO ENTRE LOGIN E CADASTRO ---
      const linkCadastro = document.querySelector('#loginOverlay .link-cadastro');
      const goToLogin = document.getElementById('goToLogin');

      if (linkCadastro) {
        linkCadastro.addEventListener('click', function(e) {
          e.preventDefault();
          closeModals();
          openSignupModal();
        });
      }

      if (goToLogin) {
        goToLogin.addEventListener('click', function(e) {
          e.preventDefault();
          closeModals();
          openLoginModal();
        });
      }

      // --- NEWSLETTER - ABRIR MODAL DE LOGIN AO ENVIAR EMAIL ---
      const newsletterForm = document.getElementById('newsletterForm');
      const newsletterCheckbox = document.querySelector('.newsletter-section .checkbox input');

      if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
          e.preventDefault();

          // Verificar se o checkbox foi marcado
          if (!newsletterCheckbox.checked) {
            alert("Você precisa concordar com a Política de Privacidade para continuar.");
            return;
          }

          // Verificar se o email foi preenchido
          const emailInput = this.querySelector('input[type="email"]');
          if (!emailInput.value.trim()) {
            alert("Por favor, digite seu email.");
            emailInput.focus();
            return;
          }

          // Se o usuário não estiver logado, abrir modal de login
          if (!usuarioEstaLogado()) {
            closeModals(); // Fechar qualquer modal aberto
            openLoginModal();

            // Opcional: Preencher automaticamente o email no formulário de login
            const loginEmailInput = document.querySelector('#formLogin input[type="email"]');
            if (loginEmailInput) {
              loginEmailInput.value = emailInput.value;
            }
          } else {
            // Se já estiver logado, enviar o formulário normalmente
            // Aqui você pode adicionar o código para enviar para o backend
            const formData = new FormData(this);
            formData.append('acao', 'newsletter');

            fetch('processa_form.php', {
                method: 'POST',
                body: formData
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  mostrarMensagem('Email cadastrado na newsletter com sucesso!', 'sucesso');
                  this.reset();
                } else {
                  mostrarMensagem(data.message || 'Erro ao cadastrar newsletter!', 'erro');
                }
              })
              .catch(error => {
                console.error('Erro:', error);
                mostrarMensagem('Erro ao processar newsletter!', 'erro');
              });
          }
        });
      }

      // --- PROCESSAMENTO LOGIN & CADASTRO ---
      const formLogin = document.getElementById('formLogin');
      const formCadastro = document.getElementById('formCadastro');

      function mostrarMensagem(mensagem, tipo) {
        let mensagemEl = document.getElementById('mensagemFeedback');
        if (!mensagemEl) {
          mensagemEl = document.createElement('div');
          mensagemEl.id = 'mensagemFeedback';
          mensagemEl.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 5px;
                color: white;
                z-index: 10000;
                font-weight: 500;
                max-width: 300px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
          document.body.appendChild(mensagemEl);
        }

        mensagemEl.textContent = mensagem;
        mensagemEl.style.backgroundColor = tipo === 'sucesso' ? '#4CAF50' : '#f44336';
        mensagemEl.style.display = 'block';

        setTimeout(() => {
          mensagemEl.style.display = 'none';
        }, 5000);
      }

      // Processar login - VERSÃO DEBUG
      if (formLogin) {
        formLogin.addEventListener('submit', function(e) {
          e.preventDefault();

          console.log('=== INICIANDO PROCESSO DE LOGIN ===');

          const email = this.querySelector('input[type="email"]').value;
          const senha = this.querySelector('input[type="password"]').value;

          console.log('Email:', email);
          console.log('Senha:', senha);

          const formData = new FormData(this);
          formData.append('acao', 'login');

          console.log('Enviando requisição para processa_form.php...');

          fetch('processa_form.php', {
              method: 'POST',
              body: formData
            })
            .then(response => {
              console.log('Status da resposta:', response.status);
              console.log('OK?', response.ok);
              return response.json();
            })
            .then(data => {
              console.log('Resposta JSON do servidor:', data);

              if (data.success) {
                console.log('Login bem-sucedido!');
                mostrarMensagem(data.message, 'sucesso');
                setTimeout(() => {
                  if (data.redirect) {
                    console.log('Redirecionando para:', data.redirect);
                    window.location.href = data.redirect;
                  } else {
                    console.log('Recarregando página...');
                    window.location.reload();
                  }
                }, 1500);
              } else {
                console.log('Login falhou:', data.message);
                mostrarMensagem(data.message, 'erro');
              }
            })
            .catch(error => {
              console.error('Erro na requisição:', error);
              mostrarMensagem('Erro de conexão com o servidor. Verifique o console.', 'erro');
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
            .then(r => r.json())
            .then(data => {
              if (data.success) {
                mostrarMensagem('Cadastro realizado com sucesso!', 'sucesso');
                setTimeout(() => window.location.reload(), 1500);
              } else {
                mostrarMensagem(data.message || 'Erro ao cadastrar!', 'erro');
              }
            })
            .catch(() => mostrarMensagem('Erro ao processar cadastro!', 'erro'));
        });
      }

      // === BARRA DE PESQUISA ===
      const inputPesquisa = document.getElementById('inputPesquisa');
      const resultadosPesquisa = document.getElementById('resultadosPesquisa');
      const searchIconBtn = document.querySelector('.search-icon-btn');

      function buscarProdutos(termo) {
        if (termo.length > 2) {
          fetch('buscar_produtos.php?termo=' + encodeURIComponent(termo))
            .then(r => r.json())
            .then(data => {
              resultadosPesquisa.innerHTML = '';

              if (data.success && data.produtos.length > 0) {
                data.produtos.forEach(produto => {
                  const item = document.createElement('div');
                  item.className = 'resultado-item';

                  const imagemSrc = produto.imagem && produto.imagem !== '' ?
                    produto.imagem :
                    'imgs/produto-padrao.png';

                  item.innerHTML = `
                                <img src="${imagemSrc}" alt="${produto.nome}" onerror="this.src='imgs/produto-padrao.png'">
                                <div class="resultado-info">
                                    <h4>${produto.nome}</h4>
                                    <div class="preco">R$ ${parseFloat(produto.preco).toFixed(2)}</div>
                                </div>
                            `;

                  item.addEventListener('click', () => {
                    window.location.href = `produto_detalhe.php?id=${produto.id}`;
                  });

                  resultadosPesquisa.appendChild(item);
                });

                resultadosPesquisa.classList.add('mostrar');
              } else {
                resultadosPesquisa.innerHTML = `
                            <div style="padding: 20px; text-align: center; color: #666;">
                                <i class="fas fa-search" style="font-size: 20px; margin-bottom: 8px;"></i>
                                <p style="font-size: 12px; margin: 0;">Nenhum produto encontrado</p>
                            </div>
                        `;
                resultadosPesquisa.classList.add('mostrar');
              }
            })
            .catch(() => {
              resultadosPesquisa.innerHTML = `
                        <div style="padding: 20px; text-align: center; color: #e74c3c;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 20px; margin-bottom: 8px;"></i>
                            <p style="font-size: 12px; margin: 0;">Erro ao buscar produtos</p>
                        </div>
                    `;
              resultadosPesquisa.classList.add('mostrar');
            });
        } else {
          resultadosPesquisa.classList.remove('mostrar');
          resultadosPesquisa.innerHTML = '';
        }
      }

      if (inputPesquisa) {
        inputPesquisa.addEventListener('input', function() {
          buscarProdutos(this.value.trim());
        });

        document.addEventListener('click', function(e) {
          if (!inputPesquisa.contains(e.target) &&
            !resultadosPesquisa.contains(e.target) &&
            !searchIconBtn.contains(e.target)) {

            resultadosPesquisa.classList.remove('mostrar');
          }
        });

        inputPesquisa.addEventListener('keypress', function(e) {
          if (e.key === 'Enter' && this.value.trim() !== '') {
            window.location.href = `produtos.php?busca=${encodeURIComponent(this.value.trim())}`;
          }
        });
      }

      if (searchIconBtn) {
        searchIconBtn.addEventListener('click', function() {
          const termo = inputPesquisa.value.trim();
          if (termo !== '') {
            window.location.href = `produtos.php?busca=${encodeURIComponent(termo)}`;
          } else {
            inputPesquisa.focus();
          }
        });
      }

      // --- DROPDOWN DO USUÁRIO ---
      const userIcon = document.querySelector('.user-icon');
      const userDropdown = document.querySelector('.user-dropdown');
      const sairConta = document.getElementById('sairConta');

      if (userIcon && userDropdown) {
        userIcon.addEventListener('click', function(e) {
          e.stopPropagation();
          userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function() {
          userDropdown.classList.remove('show');
        });

        userDropdown.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }

      // Logout
      if (sairConta) {
        sairConta.addEventListener('click', function(e) {
          e.preventDefault();

          fetch('processa_form.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              },
              body: 'acao=logout'
            })
            .then(r => r.json())
            .then(data => {
              if (data.success) {
                mostrarMensagem('Logout realizado com sucesso!', 'sucesso');
                setTimeout(() => window.location.reload(), 1500);
              }
            })
            .catch(() => mostrarMensagem('Erro ao fazer logout!', 'erro'));
        });
      }
    });

    // === Funções de contato ATUALIZADAS ===
    function iniciarChat() {
      // Redireciona diretamente para a página de chat
      window.location.href = 'chat.php';
    }

    function iniciarChatEspecialista() {
      // Redireciona para a página de chat com parâmetro de especialista
      window.location.href = 'chat.php?tipo=especialista';
    }

    function abrirWhatsApp() {
      const numero = '5511999999999';
      const mensagem = 'Olá, gostaria de mais informações sobre as joias YARA.';
      const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagem)}`;
      window.open(url, '_blank');
    }

    function agendarVisita() {
      window.location.href = 'chat.php';
    }
  </script>
  <?php if ((isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) || (isset($_SESSION['usuario']['is_admin']) && $_SESSION['usuario']['is_admin'] == 1)): ?>
    <style>
      .admin-float-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 99999;
        /* Alto z-index para ficar sobre tudo */
        background-color: #2c3e50;
        /* Cor escura profissional */
        color: #ffffff !important;
        padding: 12px 25px;
        border-radius: 50px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        text-decoration: none;
        font-family: sans-serif;
        font-weight: bold;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.2);
      }

      .admin-float-btn:hover {
        background-color: #e91e7d;
        /* Cor de destaque do seu tema (rosa) */
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(233, 30, 125, 0.5);
        border-color: #fff;
      }
    </style>

    <a href="admin/dashboard.php" class="admin-float-btn" title="Apenas visível para Administradores">
      <i class="fas fa-user-shield"></i>
      Voltar ao Painel Admin
    </a>
  <?php endif; ?>
</body>

</html>