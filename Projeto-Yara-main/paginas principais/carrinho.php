<?php
// carrinho.php
require_once 'funcoes.php';

$carrinhoItens = [];
$subtotal = 0;
$totalItens = 0;

// Verifica carrinho na sessão
if (isset($_SESSION['carrinho']) && count($_SESSION['carrinho']) > 0) {
    
    // Pega IDs e sanitiza para evitar SQL Injection
    $ids = array_keys($_SESSION['carrinho']);
    $ids = array_map('intval', $ids);
    $idsString = implode(',', $ids);

    if (!empty($idsString)) {
        // --- ALTERAÇÃO AQUI: Usando $conexao (MySQLi) ---
        $sql = "SELECT * FROM produtos WHERE id IN ($idsString)";
        $resultado = $conexao->query($sql); // Usa a seta do objeto mysqli

        if ($resultado) {
            // MySQLi não tem fetchAll direto, usamos o while
            while ($produto = $resultado->fetch_assoc()) {
                $id = $produto['id'];
                $qtd = $_SESSION['carrinho'][$id];
                
                $produto['quantidade'] = $qtd;
                $produto['subtotal'] = $produto['preco'] * $qtd;
                
                $carrinhoItens[] = $produto;
                
                $subtotal += $produto['subtotal'];
                $totalItens += $qtd;
            }
        }
    }
}

$frete = $subtotal > 0 ? 15.00 : 0;
$total = $subtotal + $frete;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>YARA - Carrinho</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  /* (Copie o mesmo CSS do código anterior) */
  :root{ --bg: #ffe7f6; --text: #000; --icon-size: 24px; --logo-height: 60px; --gap: 20px; --container-w: 1100px; --pink-accent: #fe7db9; --btn-primary-bg: #f06ca2; --btn-primary-hover: #e3558f; --dark-gray: #333; }
  body{ margin:0; font-family: 'Poppins', sans-serif; background: #fff; }
  header{ width:100%; background: var(--bg); padding: 14px 20px 6px; box-sizing: border-box; }
  .container{ max-width: var(--container-w); margin: 0 auto; }
  .top-row{ display:flex; justify-content:space-between; align-items:flex-start; gap: 10px; }
  .top-left{ display:flex; gap: 14px; align-items:center; }
  .top-left a{ text-decoration:none; color:var(--text); font-size:12px; letter-spacing:0.5px; position:relative; padding-bottom:4px; cursor:pointer; }
  .top-left a:hover::after{ content:''; position:absolute; left:0; bottom:-4px; width:100%; height:2px; background:var(--text); }
  .top-right-icons{ display:flex; gap:12px; align-items:center; }
  .top-right-icons img{ width: var(--icon-size); height: var(--icon-size); display:block; object-fit:contain; cursor:pointer; }
  .logo-row{ display:flex; justify-content:center; align-items:center; margin-top:4px; }
  .logo-row img{ height: var(--logo-height); width: auto; display:block; }
  .menu-row{ display:flex; justify-content:center; align-items:center; margin-top:6px; }
  .menu{ display:flex; align-items:center; gap: 20px; position:relative; }
  .menu a{ text-decoration:none; color:var(--text); font-size:12px; letter-spacing:0.5px; position:relative; padding-bottom:4px; cursor:pointer; }
  .menu a:not(.active):hover::after{ content:''; position:absolute; left:0; bottom:-4px; width:100%; height:2px; background:var(--text); }
  .menu-icons{ display:flex; gap:12px; align-items:center; margin-left:4px; }
  .menu-icons img{ width: var(--icon-size); height: var(--icon-size); display:block; object-fit:contain; cursor:pointer; }
  .menu-icons img.tigre-icon { width: 34px; height: auto; }
  .menu-item { position: relative; display: flex; align-items: center; } 
  .dropdown { position: absolute; top: calc(100% + 8px); left: 50%; transform: translateX(-50%); background: #fff; padding: 30px 80px; box-shadow: 0px 4px 14px rgba(0,0,0,0.15); border-radius: 2px; display: none; gap: 120px; z-index: 9999; white-space: nowrap; } 
  .menu-item:hover .dropdown, .dropdown:hover { display: flex; } 
  .dropdown h4 { font-size: 14px; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 4px; } 
  .dropdown a { display: block; font-size: 13px; color: #000; margin: 7px 0; text-decoration: none; cursor: pointer; } 
  .dropdown a:hover { text-decoration: underline; }
  .cart-section { padding: 60px 20px; background-color: #fff; }
  .cart-container { max-width: 1100px; margin: 0 auto; }
  .cart-container h1 { font-family: 'Playfair Display', serif; font-size: 38px; font-weight: 700; letter-spacing: 1.5px; margin-bottom: 40px; text-transform: uppercase; text-align: center; color: var(--btn-primary-bg); }
  .cart-wrapper { display: flex; gap: 40px; flex-wrap: wrap; }
  .cart-items { flex: 2; min-width: 300px; }
  .cart-summary { flex: 1; min-width: 300px; background-color: var(--bg); padding: 30px; border-radius: 8px; height: fit-content; }
  .cart-table { width: 100%; border-collapse: collapse; }
  .cart-table thead th { text-align: left; padding-bottom: 15px; border-bottom: 2px solid #eee; font-size: 14px; color: #555; font-weight: 600; }
  .cart-table tbody tr { border-bottom: 1px solid #eee; }
  .cart-item-details { display: flex; align-items: center; gap: 20px; padding: 25px 0; }
  .cart-item-details img { width: 90px; height: 90px; object-fit: cover; border-radius: 4px; }
  .cart-item-info { flex: 1; }
  .cart-item-info .product-name { font-size: 18px; font-weight: 600; color: #000; margin-bottom: 8px; }
  .cart-item-info .product-meta { font-size: 14px; color: #666; }
  .cart-item-qty { display: flex; align-items: center; border: 1px solid #ddd; border-radius: 4px; }
  .cart-item-qty button { background: #f9f9f9; border: none; font-size: 18px; padding: 8px 12px; cursor: pointer; }
  .cart-item-qty input { width: 40px; text-align: center; border: none; font-size: 15px; font-weight: 600; font-family: 'Poppins', sans-serif; }
  .cart-item-qty input::-webkit-outer-spin-button, .cart-item-qty input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
  .cart-item-price { font-size: 18px; font-weight: 600; color: #000; text-align: right; }
  .cart-item-remove button { background: none; border: none; font-size: 20px; color: #999; cursor: pointer; padding: 10px; }
  .cart-item-remove button:hover { color: var(--pink-accent); }
  .cart-summary h2 { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 1px solid var(--pink-accent); }
  .summary-row { display: flex; justify-content: space-between; font-size: 16px; margin-bottom: 15px; }
  .summary-row span:first-child { color: #333; }
  .summary-row span:last-child { font-weight: 600; }
  .summary-total { display: flex; justify-content: space-between; font-size: 20px; font-weight: 700; margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--pink-accent); }
  .checkout-btn { display: block; width: 100%; padding: 13px; background-color: var(--btn-primary-bg); color: #fff; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; text-align: center; text-decoration: none; margin-top: 30px; transition: 0.2s ease; }
  .checkout-btn:hover { background-color: var(--btn-primary-hover); }
  .continue-shopping { display: block; text-align: center; margin-top: 15px; color: var(--btn-primary-bg); font-size: 14px; font-weight: 600; text-decoration: none; }
  .continue-shopping:hover { text-decoration: underline; }
  .empty-cart { text-align: center; padding: 60px 20px; color: #666; }
  .empty-cart i { font-size: 4em; color: #ddd; margin-bottom: 20px; }
  .empty-cart h3 { font-size: 1.5em; margin-bottom: 10px; color: #333; }
  .empty-cart p { margin-bottom: 30px; }
  @media (max-width: 900px) { .cart-wrapper { flex-direction: column-reverse; } }
  @media (max-width: 700px) { .cart-table thead { display: none; } .cart-table tbody tr { display: block; padding: 20px 0; } .cart-table td { display: flex; justify-content: space-between; align-items: center; padding: 5px 0; text-align: right; } .cart-table td:before { content: attr(data-label); font-weight: 600; color: #555; text-align: left; margin-right: 15px; } .cart-item-details { padding: 0; } .cart-table td[data-label="Produto"] { display: block; text-align: left; } .cart-table td[data-label="Produto"]:before { display: none; } .cart-item-remove { justify-content: flex-end; } .cart-table td[data-label="Remover"] { padding-top: 15px; } .cart-table td[data-label="Remover"]:before { display: none; } }
  .usuario-logado { position: relative; cursor: pointer; }
  .menu-usuario { position: absolute; top: 100%; right: 0; background: white; border-radius: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding: 10px 0; min-width: 150px; display: none; z-index: 1000; }
  .menu-usuario.mostrar { display: block; }
  .menu-usuario a { display: block; padding: 8px 15px; text-decoration: none; color: #333; font-size: 12px; }
  .menu-usuario a:hover { background: #f5f5f5; }
  .menu-usuario .sair { color: #e74c3c; border-top: 1px solid #eee; margin-top: 5px; padding-top: 8px; }
  .carrinho-count { position: absolute; top: -8px; right: -8px; background: var(--pink-accent); color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: bold; }
  .top-right-icons { position: relative; }
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
        <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']): ?>
          <div class="usuario-logado" id="usuarioLogado">
            <?php if (!empty($_SESSION['usuario']['foto'])): ?>
              <img src="uploads/<?php echo htmlspecialchars($_SESSION['usuario']['foto']); ?>" alt="Foto do usuário" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
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
          <span class="carrinho-count"><?php echo isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0; ?></span>
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

<main class="cart-section">
  <div class="cart-container">
    <h1>Meu Carrinho</h1>
    <div class="cart-wrapper">
      <div class="cart-items">
        <?php if (empty($carrinhoItens)): ?>
          <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <h3>Seu carrinho está vazio</h3>
            <p>Adicione alguns produtos incríveis!</p>
            <a href="index.php" class="continue-shopping" style="display: inline-block; width: auto; margin-top: 20px;">
              Continuar comprando
            </a>
          </div>
        <?php else: ?>
          <table class="cart-table">
            <thead>
              <tr>
                <th colspan="2">Produto</th>
                <th>Quantidade</th>
                <th>Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="cart-items-body">
              <?php foreach ($carrinhoItens as $item): ?>
              <tr data-produto-id="<?php echo $item['id']; ?>">
                <td data-label="Produto">
                  <div class="cart-item-details">
                    <img src="imgs/<?php echo htmlspecialchars($item['imagem']); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>">
                    <div class="cart-item-info">
                      <div class="product-name"><?php echo htmlspecialchars($item['nome']); ?></div>
                      <div class="product-meta">Categoria: <?php echo ucfirst($item['categoria']); ?></div>
                    </div>
                  </div>
                </td>
                <td data-label="Preço Unit.">
                  <div class="cart-item-price unit-price">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></div>
                </td>
                <td data-label="Quantidade">
                  <div class="cart-item-qty">
                    <button type="button" class="qty-decrease">-</button>
                    <input type="number" value="<?php echo $item['quantidade']; ?>" min="1" class="qty-input">
                    <button type="button" class="qty-increase">+</button>
                  </div>
                </td>
                <td data-label="Total">
                  <div class="cart-item-price item-total">R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></div>
                </td>
                <td data-label="Remover">
                  <div class="cart-item-remove">
                    <button type="button" class="remove-item" aria-label="Remover item">
                      <i class="fa-solid fa-trash-can"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
      
      <?php if (!empty($carrinhoItens)): ?>
      <aside class="cart-summary">
        <h2>Resumo do Pedido</h2>
        <div class="summary-row">
          <span>Subtotal (<span id="items-count"><?php echo $totalItens; ?></span> itens)</span>
          <span id="subtotal">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></span>
        </div>
        <div class="summary-row">
          <span>Frete</span>
          <span id="shipping">R$ <?php echo number_format($frete, 2, ',', '.'); ?></span>
        </div>
        <div class="summary-total">
          <span>Total</span>
          <span id="total">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
        </div>
        <a href="finaliza_pagamento.php" class="checkout-btn" id="checkout-btn">
          Ir para o Pagamento
        </a>
        <a href="index.php" class="continue-shopping">
          Continuar comprando
        </a>
      </aside>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php if(file_exists('modais.php')) include 'modais.php'; ?>

<script>
const SHIPPING_COST = 15.00;

function atualizarQuantidade(produtoId, novaQuantidade) {
  fetch('funcoes.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'acao=atualizar_carrinho&produto_id=' + produtoId + '&quantidade=' + novaQuantidade
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const cartCount = document.querySelector('.carrinho-count');
      if(cartCount) cartCount.textContent = data.total_carrinho;
      location.reload();
    }
  })
  .catch(error => console.error('Erro:', error));
}

function removerItem(produtoId) {
  if (confirm('Tem certeza que deseja remover este item do carrinho?')) {
    fetch('funcoes.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'acao=atualizar_carrinho&produto_id=' + produtoId + '&quantidade=0'
    })
    .then(response => response.json())
    .then(data => { if(data.success) location.reload(); });
  }
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.qty-increase').forEach(btn => {
    btn.addEventListener('click', function() {
      const input = this.parentElement.querySelector('.qty-input');
      const id = this.closest('tr').dataset.produtoId;
      atualizarQuantidade(id, parseInt(input.value) + 1);
    });
  });
  
  document.querySelectorAll('.qty-decrease').forEach(btn => {
    btn.addEventListener('click', function() {
      const input = this.parentElement.querySelector('.qty-input');
      const id = this.closest('tr').dataset.produtoId;
      if(parseInt(input.value) > 1) atualizarQuantidade(id, parseInt(input.value) - 1);
    });
  });
  
  document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', function() {
      removerItem(this.closest('tr').dataset.produtoId);
    });
  });

  // Menu User
  const usuarioLogado = document.getElementById('usuarioLogado');
  const menuUsuario = document.getElementById('menuUsuario');
  if (usuarioLogado) {
    usuarioLogado.addEventListener('click', (e) => { e.stopPropagation(); menuUsuario.classList.toggle('mostrar'); });
    document.addEventListener('click', () => menuUsuario.classList.remove('mostrar'));
  }
  // Logout
  const sairConta = document.getElementById('sairConta');
  if (sairConta) {
    sairConta.addEventListener('click', (e) => {
       e.preventDefault();
       if(confirm('Sair?')) {
           fetch('funcoes.php', { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body:'acao=logout' })
           .then(() => window.location.href='index.php');
       }
    });
  }
  const heart = document.getElementById('heartIcon');
  if(heart) heart.addEventListener('click', () => window.location.href='favoritos.php');
});
</script>
</body>
</html>