<?php
// favoritos.php
require_once 'funcoes.php';

// Verificar se usuário está logado
if (!$_SESSION['usuario']) {
    header('Location: index.php');
    exit();
}

$usuario = $_SESSION['usuario'];

// Buscar produtos favoritos
$favoritosItens = [];
foreach ($_SESSION['favoritos'] as $produtoId) {
    $produto = getProdutoPorId($produtoId);
    if ($produto) {
        $favoritosItens[] = $produto;
    }
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
  <title>YARA - Meus Favoritos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
        /* Estilos adicionais */
    .usuario-logado {
        position: relative;
        cursor: pointer;
    }
    
    .menu-usuario {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        padding: 10px 0;
        min-width: 150px;
        display: none;
        z-index: 1000;
    }
    
    .menu-usuario.mostrar {
        display: block;
    }
    
    .menu-usuario a {
        display: block;
        padding: 8px 15px;
        text-decoration: none;
        color: #333;
        font-size: 12px;
    }
    
    .menu-usuario a:hover {
        background: #f5f5f5;
    }
    
    .menu-usuario .sair {
        color: #e74c3c;
        border-top: 1px solid #eee;
        margin-top: 5px;
        padding-top: 8px;
    }
    /* Estilos específicos dos favoritos */
    .profile-section {
      padding: 40px 20px 60px;
      background-color: #fff;
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
      background-color: #ffe7f6;
      padding: 25px;
      border-radius: 8px;
    }

    .profile-sidebar h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.6em;
      margin: 0 0 20px 0;
      padding-bottom: 10px;
      border-bottom: 1px solid #fe7db9;
      color: #333;
    }

    .profile-nav ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .profile-nav li a {
      display: block;
      padding: 12px 15px;
      text-decoration: none;
      color: #333;
      font-weight: 500;
      border-radius: 4px;
      transition: background-color 0.2s ease, color 0.2s ease;
      margin-bottom: 5px;
    }

    .profile-nav li a.active,
    .profile-nav li a:hover {
      background-color: #fe7db9;
      color: #fff;
    }
    
    .profile-nav li a i {
      margin-right: 10px;
      width: 20px;
    }

    .profile-content {
      flex: 1;
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      border: 1px solid #eee;
    }
    
    .profile-content h1 {
      font-family: 'Playfair Display', serif;
      font-size: 2.2em;
      margin: 0 0 30px 0;
      color: #333;
      font-weight: 600;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 30px;
    }

    .product-card {
      border: 1px solid #eee;
      border-radius: 8px;
      text-align: center;
      background: #fff;
      padding: 20px;
      position: relative;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
    }

    .product-card img {
      width: 100%;
      max-width: 200px;
      margin-bottom: 15px;
      border-radius: 5px;
    }

    .product-name {
      font-family: 'Playfair Display', serif;
      font-size: 1.3em;
      color: #333;
      margin-bottom: 5px;
    }

    .product-price {
      color: #f06ca2;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .add-to-cart-btn {
      background: #f06ca2;
      color: #fff;
      border: none;
      border-radius: 20px;
      padding: 10px 25px;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.3s ease;
      width: 100%;
    }

    .add-to-cart-btn:hover {
      background: #e3558f;
    }

    .remove-favorite-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: white;
      border: none;
      color: #999;
      cursor: pointer;
      font-size: 18px;
      width: 35px;
      height: 35px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }

    .remove-favorite-btn:hover { 
      color: #fe7db9;
      transform: scale(1.1);
    }

    .empty-favorites {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }

    .empty-favorites i {
      font-size: 4em;
      color: #ddd;
      margin-bottom: 20px;
    }

    .empty-favorites h3 {
      font-size: 1.5em;
      margin-bottom: 10px;
      color: #333;
    }

    .empty-favorites p {
      margin-bottom: 30px;
    }

    .btn-primary {
      display: inline-block;
      padding: 12px 30px;
      background-color: #f06ca2;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #e3558f;
    }

    @media (max-width: 768px) {
      .profile-container {
        flex-direction: column;
        gap: 20px;
      }
      
      .profile-sidebar {
        flex: 0 0 auto;
        width: 100%;
      }

      .product-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
      }
    }

    /* Estilos para mensagens */
    .mensagem {
      padding: 15px;
      margin: 20px auto;
      border-radius: 5px;
      text-align: center;
      max-width: 500px;
      display: none;
    }

    .mensagem.sucesso {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .mensagem.erro {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>

<header>
  <div class="container">
    <div class="top-row">
      <div class="top-left">
        <a id="openContact">CONTATO</a>
        <a href="servicos.php">SERVIÇOS</a>
      </div>
      <div class="top-right-icons" aria-hidden="true">
        <?php if ($_SESSION['usuario']): ?>
          <div class="usuario-logado" id="usuarioLogado">
            <?php if (!empty($_SESSION['usuario']['foto'])): ?>
              <img src="uploads/<?php echo $_SESSION['usuario']['foto']; ?>" alt="Foto do usuário" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
              <div style="width: 32px; height: 32px; border-radius: 50%; background: #fe7db9; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                <?php echo substr($_SESSION['usuario']['nome'], 0, 1); ?>
              </div>
            <?php endif; ?>
            
            <div class="menu-usuario" id="menuUsuario">
              <a href="perfil.php">Meu Perfil</a>
              <a href="pedidos.php">Meus Pedidos</a>
              <a href="favoritos.php">Favoritos</a>
              <a href="#" class="sair" id="sairConta">Sair</a>
            </div>
          </div>
        <?php else: ?>
          <img src="imgs/perfil.png" alt="Usuário" id="openLogin">
        <?php endif; ?>
        <img src="imgs/localiza.png" alt="Localização">
        <div style="position: relative;">
          <img src="imgs/sacola.png" alt="Sacola" id="carrinho">
          <span class="carrinho-count"><?php echo array_sum($_SESSION['carrinho']); ?></span>
        </div>
      </div>
    </div>  

    <div class="logo-row">
      <img src="imgs/yaraletra.png" alt="YARA Logo">
    </div><br>  

    <div class="menu-row">
      <nav class="menu" role="navigation" aria-label="Menu principal">
        <a href="index.php">INÍCIO</a>
        <a href="sobre.php">SOBRE</a>
        <a href="novidades.php">NOVIDADES</a>

        <div class="menu-item acessorios">
          <a id="acessorios" class="acessorios-link">ACESSÓRIOS</a>
          <div class="dropdown">
            <div>
              <h4>Joias Individuais</h4>
              <a href="produtos.php">Todos</a> 
              <a href="produtos.php?categoria=colares">Colares</a>
              <a href="produtos.php?categoria=piercings">Piercings</a>
              <a href="produtos.php?categoria=aneis">Anéis</a>
              <a href="produtos.php?categoria=brincos">Brincos</a>
              <a href="produtos.php?categoria=pulseiras">Pulseiras</a>
              <a href="produtos.php?categoria=braceletes">Braceletes</a>
            </div>
            <div>
              <h4>Experiências</h4>
              <a href="personalize.php">Personalize Já</a>
              <a href="presente.php">Presente</a>
            </div>
          </div>
        </div>

        <div class="menu-icons" aria-hidden="true">
          <img src="imgs/coracao.png" alt="Favoritos" id="heartIcon">
          
          <div class="menu-item">
            <img src="imgs/lupa.png" alt="Buscar" id="abrirPesquisa">
            <div class="barra-pesquisa" id="barraPesquisa">
              <input type="text" id="inputPesquisa" placeholder="Digite o nome do produto...">
              <div class="resultados-pesquisa" id="resultadosPesquisa"></div>
            </div>
          </div>
          
          <img src="imgs/tigra.png" alt="Tigre" class="tigre-icon">
        </div>
      </nav>
    </div>
  </div>
</header>

<!-- Mensagens de feedback -->
<div id="mensagemFeedback" class="mensagem"></div>

<main class="profile-section">
    <div class="profile-container container">
    
        <aside class="profile-sidebar">
            <h2>Minha Conta</h2>
            <nav class="profile-nav">
                <ul>
                    <li><a href="perfil.php"><i class="fas fa-user"></i> Meus Dados</a></li>
                    <li><a href="pedidos.php"><i class="fas fa-box"></i> Meus Pedidos</a></li>
                    <li><a href="enderecos.php"><i class="fas fa-map-marker-alt"></i> Endereços</a></li>
                    <li><a href="favoritos.php" class="active"><i class="fas fa-heart"></i> Favoritos</a></li>
                    <li><a href="#" class="sair" id="sairContaSidebar"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                </ul>
            </nav>
        </aside>

        <section class="profile-content">
            <h1>Meus Favoritos</h1>

            <?php if (empty($favoritosItens)): ?>
                <div class="empty-favorites">
                    <i class="fas fa-heart-broken"></i>
                    <h3>Nenhum produto favorito</h3>
                    <p>Você ainda não adicionou nenhum produto aos favoritos.</p>
                    <a href="novidades.php" class="btn-primary">Explorar Produtos</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($favoritosItens as $produto): ?>
                    <div class="product-card" data-produto-id="<?php echo $produto['id']; ?>">
                        <button class="remove-favorite-btn" data-id="<?php echo $produto['id']; ?>" aria-label="Remover dos Favoritos">
                            <i class="fas fa-times"></i>
                        </button>
                        <img src="<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">
                        <h3 class="product-name"><?php echo $produto['nome']; ?></h3>
                        <p class="product-price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <button class="add-to-cart-btn" onclick="adicionarAoCarrinho(<?php echo $produto['id']; ?>, '<?php echo $produto['nome']; ?>')">
                            Adicionar ao Carrinho
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

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
// === Funções de contato ===
function iniciarChat() {
    window.location.href = 'chat.php';
}

function abrirWhatsApp() {
    const numero = '5511999999999';
    const mensagem = 'Olá, gostaria de mais informações sobre as joias YARA.';
    const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagem)}`;
    window.open(url, '_blank');
}

function iniciarChatEspecialista() {
    window.location.href = 'chat.php?tipo=especialista';
}

function agendarVisita() {
    window.location.href = 'agendamento.php';
}

// === Funções JavaScript principais ===
document.addEventListener('DOMContentLoaded', function() {
    // --- MENU DO USUÁRIO ---
    const usuarioLogado = document.getElementById('usuarioLogado');
    const menuUsuario = document.getElementById('menuUsuario');
    const sairConta = document.getElementById('sairConta');

    if (usuarioLogado && menuUsuario) {
        usuarioLogado.addEventListener('click', function(e) {
            e.stopPropagation();
            menuUsuario.classList.toggle('mostrar');
        });

        // Fechar menu ao clicar fora
        document.addEventListener('click', function() {
            menuUsuario.classList.remove('mostrar');
        });

        // Logout
        if (sairConta) {
            sairConta.addEventListener('click', function(e) {
                e.preventDefault();
                
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
                        mostrarMensagem(data.message, 'sucesso');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        mostrarMensagem(data.message, 'erro');
                    }
                });
            });
        }
    }

    // --- BARRA DE PESQUISA ---
    const abrirPesquisa = document.getElementById('abrirPesquisa');
    const barraPesquisa = document.getElementById('barraPesquisa');
    const inputPesquisa = document.getElementById('inputPesquisa');
    const resultadosPesquisa = document.getElementById('resultadosPesquisa');

    if (abrirPesquisa) {
        abrirPesquisa.addEventListener('click', function(e) {
            e.stopPropagation();
            barraPesquisa.classList.toggle('ativa');
            if (barraPesquisa.classList.contains('ativa')) {
                inputPesquisa.focus();
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (barraPesquisa && !barraPesquisa.contains(e.target) && e.target !== abrirPesquisa) {
            barraPesquisa.classList.remove('ativa');
        }
    });

    if (inputPesquisa) {
        inputPesquisa.addEventListener('input', function() {
            const termo = this.value.trim();
            if (termo.length > 2) {
                buscarProdutos(termo);
            } else {
                resultadosPesquisa.innerHTML = '';
            }
        });
    }

    function buscarProdutos(termo) {
    console.log('Buscando por:', termo); // Para debug
    
    fetch('buscar_produtos.php?termo=' + encodeURIComponent(termo))
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na rede: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Resposta:', data); // Para debug
            
            if (resultadosPesquisa) {
                resultadosPesquisa.innerHTML = '';
                
                if (data.success && data.produtos && data.produtos.length > 0) {
                    data.produtos.forEach(produto => {
                        const item = document.createElement('div');
                        item.className = 'resultado-item';
                        
                        // Verificar se a imagem existe, caso contrário usar uma padrão
                        const imagemSrc = produto.imagem && produto.imagem !== '' ? 
                            `imgs/${produto.imagem}` : 'imgs/produto-padrao.png';
                        
                        item.innerHTML = `
                            <img src="${imagemSrc}" alt="${produto.nome}" onerror="this.src='imgs/produto-padrao.png'">
                            <div class="resultado-info">
                                <h4>${produto.nome}</h4>
                                <div class="preco">R$ ${parseFloat(produto.preco).toFixed(2)}</div>
                            </div>
                        `;
                        
                        item.addEventListener('click', function() {
                            window.location.href = `produto_detalhe.php?id=${produto.id}`;
                        });
                        
                        resultadosPesquisa.appendChild(item);
                    });
                } else {
                    resultadosPesquisa.innerHTML = `
                        <div style="padding: 20px; text-align: center; color: #666;">
                            <i class="fas fa-search" style="font-size: 24px; margin-bottom: 10px;"></i>
                            <p>Nenhum produto encontrado para "${termo}"</p>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            if (resultadosPesquisa) {
                resultadosPesquisa.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: #e74c3c;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-bottom: 10px;"></i>
                        <p>Erro ao buscar produtos. Tente novamente.</p>
                    </div>
                `;
            }
        });
}

    // --- FORMULÁRIOS AJAX ---
    const formLogin = document.getElementById('formLogin');
    const formCadastro = document.getElementById('formCadastro');
    const formNewsletter = document.getElementById('newsletterForm');

    function mostrarMensagem(mensagem, tipo) {
        const mensagemEl = document.getElementById('mensagemFeedback');
        if (mensagemEl) {
            mensagemEl.textContent = mensagem;
            mensagemEl.className = `mensagem ${tipo}`;
            mensagemEl.style.display = 'block';
            
            setTimeout(() => {
                mensagemEl.style.display = 'none';
            }, 5000);
        }
    }

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
            });
        });
    }

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
            });
        });
    }

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

    // --- MODAL DE CONTATO ---
    const openContact = document.getElementById('openContact');
    const contactOverlay = document.getElementById('contactOverlay');
    const closeX = document.getElementById('closeX');
    const closeModalBtn = document.getElementById('closeModalBtn');

    function openContactModal() {
      if (!contactOverlay) return;
      contactOverlay.style.display = 'flex';
      contactOverlay.setAttribute('aria-hidden', 'false');
      const sel = document.getElementById('locationSelect');
      if (sel) sel.focus();
      document.body.style.overflow = 'hidden';
    }

    function closeContactModal() {
      if (!contactOverlay) return;
      contactOverlay.style.display = 'none';
      contactOverlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (openContact) openContact.focus();
    }

    if (openContact) openContact.addEventListener('click', e => { e.preventDefault(); openContactModal(); });
    if (closeX) closeX.addEventListener('click', closeContactModal);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeContactModal);
    if (contactOverlay) contactOverlay.addEventListener('click', e => { if (e.target === contactOverlay) closeContactModal(); });

    const modalBox = document.querySelector('.contact-modal');
    if (modalBox) modalBox.addEventListener('click', e => e.stopPropagation());

    // --- MODAIS DE LOGIN E CADASTRO ---
    const perfilIcon = document.querySelector('.top-right-icons img[alt="Usuário"]');
    const loginOverlay = document.getElementById('loginOverlay');
    const signupOverlay = document.getElementById('signupOverlay');
    const closeLoginX = document.getElementById('closeLoginX');
    const closeSignupX = document.getElementById('closeSignupX');
    const linkCadastro = document.querySelector('#loginOverlay .link-cadastro');
    const goToLogin = document.getElementById('goToLogin');

    function openLogin() {
      if (!loginOverlay) return;
      loginOverlay.style.display = 'flex';
      loginOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      const firstInput = loginOverlay.querySelector('input');
      if (firstInput) firstInput.focus();
    }

    function closeLogin() {
      if (!loginOverlay) return;
      loginOverlay.style.display = 'none';
      loginOverlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (perfilIcon) perfilIcon.focus();
    }

    if (perfilIcon) perfilIcon.addEventListener('click', e => { e.preventDefault(); openLogin(); });
    if (closeLoginX) closeLoginX.addEventListener('click', closeLogin);
    if (loginOverlay) loginOverlay.addEventListener('click', e => { if (e.target === loginOverlay) closeLogin(); });
    const loginInner = document.querySelector('#loginOverlay .login-modal');
    if (loginInner) loginInner.addEventListener('click', e => e.stopPropagation());

    function openSignup() {
      if (!signupOverlay) return;
      signupOverlay.style.display = 'flex';
      signupOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      const firstInput = signupOverlay.querySelector('input');
      if (firstInput) firstInput.focus();
    }

    function closeSignup() {
      if (!signupOverlay) return;
      signupOverlay.style.display = 'none';
      signupOverlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    if (closeSignupX) closeSignupX.addEventListener('click', closeSignup);
    if (signupOverlay) signupOverlay.addEventListener('click', e => { if (e.target === signupOverlay) closeSignup(); });
    const signupInner = document.querySelector('#signupOverlay .login-modal');
    if (signupInner) signupInner.addEventListener('click', e => e.stopPropagation());

    if (linkCadastro) {
      linkCadastro.addEventListener('click', e => {
        e.preventDefault();
        closeLogin();
        openSignup();
      });
    }

    if (goToLogin) {
      goToLogin.addEventListener('click', e => {
        e.preventDefault();
        closeSignup();
        openLogin();
      });
    }

    // --- NEWSLETTER E ESC ---
    const confirmEmailBtn = document.getElementById('confirmEmailBtn');
    const newsletterCheckbox = document.querySelector('.newsletter-section .checkbox input');

    if (confirmEmailBtn) {
      confirmEmailBtn.addEventListener('click', e => {
        e.preventDefault();
        if (!newsletterCheckbox.checked) {
          alert("Você precisa concordar com a Política de Privacidade para continuar.");
          return;
        }
        openSignup();
      });
    }

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        if (loginOverlay && loginOverlay.style.display === 'flex') closeLogin();
        if (signupOverlay && signupOverlay.style.display === 'flex') closeSignup();
        if (contactOverlay && contactOverlay.style.display === 'flex') closeContactModal();
      }
    });

    // --- ÍCONES E REDIRECIONAMENTOS ---
    const heartIcon = document.getElementById('heartIcon');
    if (heartIcon) {
      heartIcon.addEventListener('click', () => {
        window.location.href = 'favoritos.php';
      });
    }

    const loginForm = document.querySelector('#loginOverlay .login-form');
    if (loginForm) {
      loginForm.addEventListener('submit', e => {
        e.preventDefault();
        window.location.href = 'perfil.php';
      });
    }

    const signupForm = document.querySelector('#signupOverlay .login-form');
    if (signupForm) {
      signupForm.addEventListener('submit', e => {
        e.preventDefault();
        window.location.href = 'perfil.php';
      });
    }
});
// Função para adicionar ao carrinho
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
      document.querySelector('.carrinho-count').textContent = data.total_carrinho;
      
      // Mostrar mensagem de sucesso
      mostrarMensagem(`"${nomeProduto}" adicionado ao carrinho!`, 'sucesso');
    }
  })
  .catch(error => {
    console.error('Erro:', error);
    mostrarMensagem('Erro ao adicionar ao carrinho.', 'erro');
  });
}

// Função para remover favorito
function removerFavorito(produtoId) {
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
      // Remover o elemento do DOM
      const elemento = document.querySelector(`.product-card[data-produto-id="${produtoId}"]`);
      if (elemento) {
        elemento.remove();
      }
      
      // Verificar se ficou vazio
      const container = document.querySelector('.product-grid');
      const produtos = container.querySelectorAll('.product-card');
      if (produtos.length === 0) {
        container.innerHTML = `
          <div class="empty-favorites">
            <i class="fas fa-heart-broken"></i>
            <h3>Nenhum produto favorito</h3>
            <p>Você ainda não adicionou nenhum produto aos favoritos.</p>
            <a href="novidades.php" class="btn-primary">Explorar Produtos</a>
          </div>`;
      }
      
      mostrarMensagem('Produto removido dos favoritos!', 'sucesso');
    }
  })
  .catch(error => {
    console.error('Erro:', error);
    mostrarMensagem('Erro ao remover dos favoritos.', 'erro');
  });
}

// Função para mostrar mensagens
function mostrarMensagem(mensagem, tipo = 'sucesso') {
  const mensagemDiv = document.getElementById('mensagemFeedback');
  mensagemDiv.textContent = mensagem;
  mensagemDiv.className = `mensagem ${tipo}`;
  mensagemDiv.style.display = 'block';
  
  setTimeout(() => {
    mensagemDiv.style.display = 'none';
  }, 3000);
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Botões de remover favorito
  document.querySelectorAll('.remove-favorite-btn').forEach(button => {
    button.addEventListener('click', function() {
      const produtoId = this.dataset.id;
      if (confirm('Deseja remover este item dos favoritos?')) {
        removerFavorito(produtoId);
      }
    });
  });

  // Menu do usuário
  const usuarioLogado = document.getElementById('usuarioLogado');
  const menuUsuario = document.getElementById('menuUsuario');
  const sairConta = document.getElementById('sairConta');
  const sairContaSidebar = document.getElementById('sairContaSidebar');
  
  if (usuarioLogado) {
    usuarioLogado.addEventListener('click', function(e) {
      e.stopPropagation();
      menuUsuario.classList.toggle('mostrar');
    });
    
    // Fechar menu ao clicar fora
    document.addEventListener('click', function() {
      menuUsuario.classList.remove('mostrar');
    });
    
    // Sair da conta (header)
    if (sairConta) {
      sairConta.addEventListener('click', function(e) {
        e.preventDefault();
        fazerLogout();
      });
    }
    
    // Sair da conta (sidebar)
    if (sairContaSidebar) {
      sairContaSidebar.addEventListener('click', function(e) {
        e.preventDefault();
        fazerLogout();
      });
    }
  }

  // Função de logout
  function fazerLogout() {
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
          mostrarMensagem(data.message, 'sucesso');
          setTimeout(() => {
            window.location.href = 'index.php';
          }, 1500);
        } else {
          mostrarMensagem(data.message, 'erro');
        }
      })
      .catch(error => {
        console.error('Erro:', error);
        mostrarMensagem('Erro ao fazer logout.', 'erro');
      });
    }
  }

  // Redirecionar para carrinho
  const carrinhoIcon = document.getElementById('carrinho');
  if (carrinhoIcon) {
    carrinhoIcon.addEventListener('click', () => {
      window.location.href = 'carrinho.php';
    });
  }

  // Barra de pesquisa
  const abrirPesquisa = document.getElementById('abrirPesquisa');
  const barraPesquisa = document.getElementById('barraPesquisa');
  const inputPesquisa = document.getElementById('inputPesquisa');

  if (abrirPesquisa) {
    abrirPesquisa.addEventListener('click', function(e) {
      e.stopPropagation();
      barraPesquisa.classList.toggle('ativa');
      if (barraPesquisa.classList.contains('ativa')) {
        inputPesquisa.focus();
      }
    });
  }

  document.addEventListener('click', function(e) {
    if (barraPesquisa && !barraPesquisa.contains(e.target) && e.target !== abrirPesquisa) {
      barraPesquisa.classList.remove('ativa');
    }
  });
});
</script>
</body>
</html>