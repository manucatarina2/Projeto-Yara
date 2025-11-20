<?php
// index.php
require_once 'funcoes.php';

// Buscar produtos em destaque
$produtosDestaque = getProdutosDestaque();

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
  display: flex; /* Adiciona flex para alinhar texto e imagem */
  align-items: center;
  justify-content: space-between;
  width: 100%;
  max-width: 800px; /* Limite máximo para o conteúdo */
  margin: 0 auto;
  margin-bottom: -100px;
}

.gato-section .texto {
  flex: 1;
  min-width: 300px;
  text-align: left;
  padding-left: 0; /* Remove padding-left já que está usando gap */
  padding-bottom: 0; /* Remove padding-bottom */
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
}

.produto-card:hover {
    transform: translateY(-5px);
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
          <!-- Mostrar foto do usuário se estiver logado -->
          <div class="usuario-logado" id="usuarioLogado">
            <?php if (!empty($_SESSION['usuario']['foto'])): ?>
              <img src="uploads/<?php echo $_SESSION['usuario']['foto']; ?>" alt="Foto do usuário" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
              <div style="width: 32px; height: 32px; border-radius: 50%; background: #fe7db9; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                <?php echo substr($_SESSION['usuario']['nome'], 0, 1); ?>
              </div>
            <?php endif; ?>
            
            <!-- Menu do usuário -->
            <div class="menu-usuario" id="menuUsuario">
              <a href="perfil.php">Meu Perfil</a>
              <a href="pedidos.php">Meus Pedidos</a>
              <a href="favoritos.php">Favoritos</a>
              <a href="#" class="sair" id="sairConta">Sair</a>
            </div>
          </div>
        <?php else: ?>
          <!-- Mostrar ícone de perfil se não estiver logado -->
          <img src="imgs/perfil.png" alt="Usuário" id="openLogin">
        <?php endif; ?>
        <img src="imgs/localiza.png" alt="Localização">
        <img src="imgs/sacola.png" alt="Sacola" id="carrinho">
        <span class="carrinho-count"><?php echo array_sum($_SESSION['carrinho']); ?></span>
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

        <!-- Ícones -->
        <div class="menu-icons" aria-hidden="true">
          <img src="imgs/coracao.png" alt="Favoritos" id="heartIcon">
          
          <!-- Barra de Pesquisa -->
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

<!-- === Seção Topo Novidades === --> 
<section class="gato-section"> 
  <div class="conteudo">
    <div class="texto">
      <h1>DESCUBRA O QUE HÁ DE NOVO</h1>
      <p>Peças inéditas que transformam cada momento em presença única.</p>
    </div>

    <div class="imagem-tigre"> 
      <img src="../imgs/tigrenovidades.jpg" alt="Imagem destaque novidades"> 
    </div>
  </div>
</section>

<!--Secao Novidades-->
<section class="colecao-section">
  <h2>NOVIDADES</h2>

  <div class="colecao-container">
    <?php foreach ($produtosDestaque as $produto): ?>
    <div class="produto-card" data-produto-id="<?php echo $produto['id']; ?>">
      <img src="<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>" class="produto-img">
      <img src="imgs/coracao.png" alt="Curtir" class="favorito <?php echo isFavorito($produto['id']) ? 'ativo' : ''; ?>" 
           onclick="toggleFavorito(this, <?php echo $produto['id']; ?>, '<?php echo $produto['nome']; ?>')">
      <h3><?php echo $produto['nome']; ?></h3>
      <p class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
      <button onclick="adicionarAoCarrinho(<?php echo $produto['id']; ?>, '<?php echo $produto['nome']; ?>')">
        Adicionar ao carrinho
      </button>
    </div>
    <?php endforeach; ?>
  </div>

  <div id="mensagemCurtida" class="mensagem-curtida" style="display: none;">
    <p id="textoCurtida"></p>
    <button onclick="verCurtidos()">Ver curtidos</button>
  </div>

  <div id="mensagemCarrinho" class="mensagem-carrinho" style="display: none;">
    <p id="textoCarrinho"></p>
    <button onclick="verCarrinho()">Ver carrinho</button>
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

<?php include 'modais.php'; ?>

<script>
// Função para alternar favorito
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

// Função para ver favoritos
function verCurtidos() {
    window.location.href = "favoritos.php";
}

// Função para ver carrinho
function verCarrinho() {
    window.location.href = "carrinho.php";
}

// Função para mostrar mensagens gerais
function mostrarMensagem(mensagem, tipo = 'sucesso') {
    const mensagemDiv = document.getElementById('mensagemFeedback');
    mensagemDiv.textContent = mensagem;
    mensagemDiv.className = `mensagem ${tipo}`;
    mensagemDiv.style.display = 'block';
    
    setTimeout(() => {
        mensagemDiv.style.display = 'none';
    }, 3000);
}

// Menu do usuário
document.addEventListener('DOMContentLoaded', function() {
    const usuarioLogado = document.getElementById('usuarioLogado');
    const menuUsuario = document.getElementById('menuUsuario');
    const sairConta = document.getElementById('sairConta');
    
    if (usuarioLogado) {
        usuarioLogado.addEventListener('click', function(e) {
            e.stopPropagation();
            menuUsuario.classList.toggle('mostrar');
        });
        
        // Fechar menu ao clicar fora
        document.addEventListener('click', function() {
            menuUsuario.classList.remove('mostrar');
        });
        
        // Sair da conta
        if (sairConta) {
            sairConta.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Deseja realmente sair?')) {
                    window.location.href = 'logout.php';
                }
            });
        }
    }
});
</script>
<script src="script.js"></script>
</body>
</html>