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
</head>
<body>

<header> 
  <div class="container"> 
    <div class="top-row"> 
      <div class="top-left"> 
        <a id="openContact">CONTATO</a> 
        <a href="#">SERVIÇOS</a> 
      </div> 
      <div class="top-right-icons" aria-hidden="true"> 
        <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']): ?>
            <a href="perfil.php"><img src="imgs/perfil.png" alt="Minha Conta"></a>
        <?php else: ?>
            <img src="imgs/perfil.png" alt="Login" id="openLoginIcon">
        <?php endif; ?>

        <img src="imgs/localiza.png" alt="Localização">
        
        <div style="position: relative; cursor: pointer;" onclick="window.location.href='carrinho.php'">
            <img src="imgs/sacola.png" alt="Sacola">
            <span class="carrinho-count">
                <?php echo isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0; ?>
            </span>
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
        </nav> 
        <div class="menu-icons" aria-hidden="true"> 
          <img src="imgs/coracao.png" alt="Favoritos" onclick="window.location.href='favoritos.php'" style="cursor: pointer;"> 
          <img src="imgs/lupa.png" alt="Buscar"> 
          <img src="imgs/tigra.png" alt="Tigre" class="tigre-icon"> 
        </div> 
      </div> 
  </div> 
</header> 


<section class="tigre-section"> 
  <div class="conteudo">
    <h1>LAÇOS DE LUZ, ENERGIA SEM FIM</h1>
    <p>Pulseiras que envolvem seu pulso com charme e elegância.</p>
  </div>

  <div class="imagem-tigre"> 
    <img src="imgs/tigrebraceletes.png" alt="Tigre"> 
  </div>
</section>

<style>
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

.carrinho-count {
    position: absolute; top: -5px; right: -10px;
    background: #e91e7d; color: white; border-radius: 50%;
    width: 20px; height: 20px; font-size: 12px;
    display: flex; align-items: center; justify-content: center;
}
</style>

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

<script src="script.js"></script> <script>
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
            
            const contadores = document.querySelectorAll('.carrinho-count');
            contadores.forEach(c => c.textContent = data.total_carrinho);
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