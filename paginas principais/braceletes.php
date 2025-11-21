<?php
// braceletes.php
require_once 'funcoes.php';

// Busca apenas os braceletes no banco de dados
$produtos = getProdutosPorCategoria('braceletes');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>YARA - Braceletes</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

.user-dropdown .sair {
  color: #e74c3c;
  border-top: 1px solid #f0f0f0;
  margin-top: 6px;
  padding-top: 10px;
}

/* Avatar placeholder */
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
  cursor: pointer;
}

/* Ícone do carrinho com contador */
.cart-icon {
  position: relative;
  cursor: pointer;
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

/* === Seção Tigre (Mantida a sua estilização) === */
.tigre-section {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #fff;
  color: #000;
  position: relative;
  min-height: 480px;
  padding: 0 80px;
  gap: 100px;
  overflow: hidden;
}

.tigre-section .conteudo { max-width: 420px; flex: 1; }

.tigre-section h1 {
  font-family: "Cormorant Garamond", serif;
  font-size: 45px;
  font-weight: 300;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  margin-bottom: 12px;
  line-height: 1.2;
}

.tigre-section p {
  font-family: "Lato", sans-serif;
  font-size: 18px;
  font-weight: 300;
  margin-bottom: 28px;
  line-height: 1.6;
}

.tigre-section .imagem-tigre { flex: 1; display: flex; justify-content: center; align-items: center; }
.tigre-section .imagem-tigre img { max-width: 480px; height: auto; display: block; object-fit: contain; }
.tigre-section .seta, .tigre-section .botoes { display: none; }

@media (max-width: 900px) {
  .tigre-section { flex-direction: column; text-align: center; padding: 40px 20px; gap: 40px; }
  .tigre-section .imagem-tigre img { width: 100%; max-width: 320px; } /* Ajustado para mobile */
  .tigre-section h1 { font-size: 30px; }
  .tigre-section p { font-size: 16px; }
}

/* Estilos do Card de Produto (Reutilizados para garantir consistência) */
.colecao-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
.produto-card {
  background: #fff; padding: 20px; width: 300px;
  text-align: center; border: 1px solid #ddd;
  display: flex; flex-direction: column; align-items: center;
  position: relative;
}
.produto-card img.produto-img { width: auto; height: 250px; object-fit: contain; margin-bottom: 15px; }
.produto-card h3 { margin: 10px 0; min-height: 40px; font-size: 16px; }
.produto-card button {
  background-color: #e91e7d; color: #fff; border: none;
  padding: 12px 20px; border-radius: 10px; cursor: pointer;
  width: 100%; font-size: 16px; transition: 0.3s;
}
.produto-card button:hover { background-color: #e02192; }
.favorito { position: absolute; top: 10px; right: 10px; cursor: pointer; width: 24px; }
.favorito.ativo { filter: invert(27%) sepia(51%) saturate(2878%) hue-rotate(346deg) brightness(104%) contrast(97%); }

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
  line-height: 1.3;
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
  font-size: 14px;
}

.newsletter-form button {
  background: #000;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 12px 20px;
  cursor: pointer;
  font-size: 16px;
}

.checkbox {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: #666;
}

.checkbox a {
  color: #000;
  text-decoration: underline;
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

.footer-col h3,
.footer-col h4 {
  margin-bottom: 15px;
  font-weight: 500;
}

.footer-col p {
  color: #ccc;
  line-height: 1.6;
  margin-bottom: 15px;
}

.footer-col ul {
  list-style: none;
  padding: 0;
}

.footer-col ul li {
  margin-bottom: 8px;
}

.footer-col ul li a {
  color: #ccc;
  text-decoration: none;
}

.footer-col ul li a:hover {
  color: white;
}

.social {
  display: flex;
  gap: 15px;
}

.social a {
  color: #ccc;
  font-size: 18px;
  transition: color 0.3s;
}

.social a:hover {
  color: white;
}

.footer-bottom {
  border-top: 1px solid #333;
  padding-top: 20px;
  text-align: center;
  color: #ccc;
  font-size: 14px;
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
          <a href="#" id="openContactDropdown">Contato</a>
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
      <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']): ?>
        <!-- Remove a foto e mantém somente a inicial -->
        <div class="avatar-placeholder">
          <?php echo substr($_SESSION['usuario']['nome'], 0, 1); ?>
        </div>

        <div class="user-dropdown">
          <a href="perfil.php">Meu Perfil</a>
          <a href="pedidos.php">Meus Pedidos</a>
          <a href="favoritos.php">Favoritos</a>
          <a href="#" class="sair" id="sairConta">Sair</a>
        </div>
      <?php else: ?>
        <i class="far fa-user"></i>
        <div class="user-dropdown">
          <a href="#" id="openLoginMenu">Fazer Login</a>
          <a href="#" id="openSignupMenu">Cadastrar</a>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="nav-icon cart-icon" id="carrinho" onclick="window.location.href='carrinho.php'">
      <i class="fas fa-shopping-bag"></i>
      <span class="cart-count"><?php echo isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0; ?></span>
    </div>
  </div>
</nav>

<section class="tigre-section">
  <div class="conteudo">
    <h1>LAÇOS DE LUZ, ENERGIA SEM FIM</h1>
    <p>Pulseiras que envolvem seu pulso com charme e elegância.</p>
  </div>

  <div class="imagem-tigre">
    <img src="imgs/tigrebraceletes.png" alt="Tigre">
  </div>
</section>

<section class="colecao-section">
  <h2 style="text-align: center; margin: 40px 0;">COLEÇÃO</h2>

  <div class="colecao-container">
    <?php if (!empty($produtos)): ?>
        <?php foreach ($produtos as $produto): ?>
            <div class="produto-card">
                <img src="imgs/<?php echo htmlspecialchars($produto['imagem']); ?>"
                     alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                     class="produto-img">
               
                <img src="imgs/coracao.png"
                     alt="Curtir"
                     class="favorito <?php echo isFavorito($produto['id']) ? 'ativo' : ''; ?>"
                     onclick="curtirProduto(this, <?php echo $produto['id']; ?>)">
               
                <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
               
                <p style="color: #e91e7d; font-weight: bold; margin-bottom: 10px;">
                    R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                </p>

                <button onclick="adicionarCarrinho(<?php echo $produto['id']; ?>)">
                    Adicionar ao carrinho
                </button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; width: 100%;">Nenhum bracelete encontrado no momento.</p>
    <?php endif; ?>
  </div>
</section>

<section class="newsletter-section">
  <div class="newsletter-container">
    <div class="newsletter-logo">
      <img src="imgs/logo.png" alt="Logo YARA">
    </div>
    <div class="newsletter-content">
      <h2>Descubra primeiro todas as novidades <br> da Yara. Cadastre-se!</h2>
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

<?php if(file_exists('modais.php')) include 'modais.php'; ?>

<script>
// === Dropdown do Usuário ===
document.addEventListener("DOMContentLoaded", () => {
  const userIcon = document.querySelector(".user-icon");
  const dropdown = document.querySelector(".user-dropdown");

  if (!userIcon || !dropdown) return;

  // Abre/fecha ao clicar no ícone
  userIcon.addEventListener("click", (e) => {
    e.stopPropagation();
    dropdown.classList.toggle("show");
  });

  // Fecha ao clicar fora
  document.addEventListener("click", () => {
    dropdown.classList.remove("show");
  });

  // Evita fechar quando clicar dentro
  dropdown.addEventListener("click", (e) => {
    e.stopPropagation();
  });
});

// === Barra de Pesquisa ===
document.addEventListener("DOMContentLoaded", function() {
  const inputPesquisa = document.getElementById('inputPesquisa');
  const resultadosPesquisa = document.getElementById('resultadosPesquisa');
  const searchIconBtn = document.querySelector('.search-icon-btn');

  // Função para buscar produtos
  function buscarProdutos(termo) {
      if (termo.length > 2) {
          fetch('buscar_produtos.php?termo=' + encodeURIComponent(termo))
              .then(response => {
                  if (!response.ok) {
                      throw new Error('Erro na rede: ' + response.status);
                  }
                  return response.json();
              })
              .then(data => {
                  if (resultadosPesquisa) {
                      resultadosPesquisa.innerHTML = '';
                      
                      if (data.success && data.produtos && data.produtos.length > 0) {
                          data.produtos.forEach(produto => {
                              const item = document.createElement('div');
                              item.className = 'resultado-item';
                              
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
                  }
              })
              .catch(error => {
                  console.error('Erro na busca:', error);
                  if (resultadosPesquisa) {
                      resultadosPesquisa.innerHTML = `
                          <div style="padding: 20px; text-align: center; color: #e74c3c;">
                              <i class="fas fa-exclamation-triangle" style="font-size: 20px; margin-bottom: 8px;"></i>
                              <p style="font-size: 12px; margin: 0;">Erro ao buscar produtos</p>
                          </div>
                      `;
                      resultadosPesquisa.classList.add('mostrar');
                  }
              });
      } else {
          resultadosPesquisa.classList.remove('mostrar');
          resultadosPesquisa.innerHTML = '';
      }
  }

  // Evento de input na pesquisa
  if (inputPesquisa) {
      inputPesquisa.addEventListener('input', function() {
          const termo = this.value.trim();
          buscarProdutos(termo);
      });

      // Fechar resultados ao clicar fora
      document.addEventListener('click', function(e) {
          if (!inputPesquisa.contains(e.target) && !resultadosPesquisa.contains(e.target) && !searchIconBtn.contains(e.target)) {
              resultadosPesquisa.classList.remove('mostrar');
          }
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
  }

  // Botão de pesquisa
  if (searchIconBtn) {
      searchIconBtn.addEventListener('click', function() {
          const termo = inputPesquisa.value.trim();
          if (termo.length > 0) {
              window.location.href = `produtos.php?busca=${encodeURIComponent(termo)}`;
          } else {
              inputPesquisa.focus();
          }
      });
  }

  // Logout
  const sairConta = document.getElementById('sairConta');
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
                  window.location.reload();
              }
          });
      });
  }
});

// Função Adicionar Carrinho (Específica para integrar com PHP)
function adicionarCarrinho(idProduto) {
    const formData = new FormData();
    formData.append('acao', 'adicionar_carrinho');
    formData.append('produto_id', idProduto);

    fetch('funcoes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            Swal.fire({
                title: 'Adicionado!',
                text: 'Produto adicionado à sua sacola.',
                icon: 'success',
                confirmButtonColor: '#e91e7d',
                confirmButtonText: 'Continuar comprando',
                showCancelButton: true,
                cancelButtonText: 'Ir para o carrinho',
                cancelButtonColor: '#333'
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = 'carrinho.php';
                }
            });
           
            // Atualizar contador do carrinho na navbar
            const cartCounts = document.querySelectorAll('.cart-count');
            cartCounts.forEach(c => c.textContent = data.total_carrinho);
        }
    });
}

// Função Favoritar
function curtirProduto(elemento, idProduto) {
    const formData = new FormData();
    formData.append('acao', 'toggle_favorito');
    formData.append('produto_id', idProduto);

    fetch('funcoes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if(data.acao === 'adicionado') {
                elemento.classList.add('ativo');
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Adicionado aos favoritos',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                elemento.classList.remove('ativo');
            }
        }
    });
}
</script>

</body>
</html>