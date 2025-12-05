<?php

require_once 'conexao.php';

// 1. Definição de Base URL
$currentDir = basename(getcwd());
$baseUrl = ($currentDir == 'admin') ? '../' : '';

// 2. Lista de Países
$paises = ['Brasil', 'Angola', 'Moçambique', 'Portugal', 'Estados Unidos', 'Japão', 'China', 'França', 'Itália', 'Alemanha', 'Reino Unido', 'Canadá', 'Argentina', 'Chile', 'Uruguai'];
sort($paises);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo $baseUrl; ?>navbar.css">

<nav class="navbar-container">
    <div class="navbar-logo">
        <a href="<?php echo $baseUrl; ?>index.php">
            <img src="<?php echo $baseUrl; ?>imgs/yaraletra.png" alt="YARA Logo" style="height: 50px; width: auto;">
        </a>
    </div>

    <ul class="navbar-menu">
        <li><a href="<?php echo $baseUrl; ?>index.php">INÍCIO</a></li>

        <li class="menu-item">
            <a href="<?php echo $baseUrl; ?>sobre.php" class="sobre-link">SOBRE</a>
            <div class="dropdown">
                <div>
                    <h4>Informações</h4>
                    <a href="<?php echo $baseUrl; ?>servicos.php">Serviços</a>
                    <a href="#" id="openContactDropdown">Contato</a>
                </div>
            </div>
        </li>

        <li class="menu-item">
            <a href="<?php echo $baseUrl; ?>produtos.php" class="acessorios-link">ACESSÓRIOS</a>
            <div class="dropdown">
                <div>
                    <h4>Joias Individuais</h4>
                    <a href="<?php echo $baseUrl; ?>produtos.php">Todos</a>
                    <a href="<?php echo $baseUrl; ?>colares.php">Colares</a>
                    <a href="<?php echo $baseUrl; ?>piercings.php">Piercings</a>
                    <a href="<?php echo $baseUrl; ?>aneis.php">Anéis</a>
                    <a href="<?php echo $baseUrl; ?>brincos.php">Brincos</a>
                    <a href="<?php echo $baseUrl; ?>pulseiras.php">Pulseiras</a>
                    <a href="<?php echo $baseUrl; ?>braceletes.php">Braceletes</a>
                </div>
                <div>
                    <h4>Experiências</h4>
                    <a href="<?php echo $baseUrl; ?>personalize.php">Personalize Já</a>
                    <a href="<?php echo $baseUrl; ?>presente.php">Presente</a>
                </div>
            </div>
        </li>
    </ul>

    <div class="navbar-icons">
        <div class="search-container">
            <div class="search-bar">
                <input type="text" placeholder="Buscar..." id="inputPesquisa">
                <button class="search-icon-btn"><i class="fas fa-search"></i></button>
                <div class="resultados-pesquisa" id="resultadosPesquisa"></div>
            </div>
        </div>

        <div class="icon-divider"></div>

        <div class="nav-icon heart-icon" id="heartIcon">
            <i class="far fa-heart"></i>
        </div>

        <div class="nav-icon user-icon">
            <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']): ?>
                <?php if (isset($_SESSION['usuario']['foto']) && !empty($_SESSION['usuario']['foto'])): ?>
                    <img src="<?php echo $baseUrl; ?>uploads/<?php echo htmlspecialchars($_SESSION['usuario']['foto']); ?>" alt="Perfil" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
                
                <div class="user-dropdown">
                    <div style="padding: 10px 16px; font-weight: bold; border-bottom: 1px solid #eee;">
                        Olá, <?php echo explode(' ', $_SESSION['usuario']['nome'])[0]; ?>
                    </div>
                    <a href="<?php echo $baseUrl; ?>perfil.php">Meu Perfil</a>
                    <a href="<?php echo $baseUrl; ?>perfil.php#pedidos">Meus Pedidos</a>
                    <a href="<?php echo $baseUrl; ?>favoritos.php">Favoritos</a>
                    <a href="<?php echo $baseUrl; ?>logout.php" class="sair">Sair</a>
                </div>
            <?php else: ?>
                <i class="far fa-user"></i>
                <div class="user-dropdown">
                    <a href="#" id="openLoginMenu">Fazer Login</a>
                    <a href="#" id="openSignupMenu">Cadastrar</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="nav-icon cart-icon" id="carrinho">
            <img src="<?php echo $baseUrl; ?>imgs/sacola.png" alt="Sacola" style="height: 20px; width: auto;">
            <span class="cart-count"><?php echo isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0; ?></span>
        </div>
    </div>
</nav>

<div class="contact-overlay" id="contactOverlay" aria-hidden="true">
    <div class="contact-modal" role="dialog" aria-modal="true">
        <button class="close-x" id="closeX">X</button>
        <img src="<?php echo $baseUrl; ?>imgs/loginho.png" class="modal-logo">
        <h3 id="contactTitle">Entre em Contato</h3>
        <p class="intro">Fale com um Embaixador YARA.</p>
        
        <div class="select-wrap">
            <select id="locationSelect">
                <option value="">Escolha sua localização:</option>
                <?php foreach ($paises as $pais) echo "<option value='$pais'>$pais</option>"; ?>
            </select>
        </div>

        <div class="contact-grid">
            <div class="contact-block">
                <div class="block-title">Fale Conosco</div>
                <div class="block-desc">(11) 4380-0328</div>
            </div>

            <div class="contact-block">
                <div class="block-title">Escreva para Nós</div>
                <button class="btn-outline" onclick="window.location.href='mailto:contato@yara.com'">Email</button>
            </div>

            <div class="contact-block">
                <div class="block-title">Atendimento Online</div>
                <div class="block-desc">Tire dúvidas agora mesmo.</div>
                <button class="btn-outline" onclick="iniciarChat()" style="background-color: #e91e63; color: white; border: none;">
                    <i class="fas fa-comments"></i> Iniciar Chat
                </button>
            </div>
        </div>

        <div class="contact-actions">
            <button class="btn-primary" id="closeModalBtn">Fechar</button>
        </div>
    </div>
</div>

<div class="login-overlay" id="loginOverlay" aria-hidden="true">
    <div class="login-modal" role="dialog">
        <button class="close-x" id="closeLoginX">X</button>
        <img src="<?php echo $baseUrl; ?>imgs/loginho.png" class="modal-logo">
        <h3>Faça login</h3>
        <form class="login-form" id="formLogin">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit" class="btn-primary">Entrar</button>
        </form>
        <p>Não tem conta? <a href="#" class="link-cadastro">Cadastre-se</a></p>
    </div>
</div>

<div class="login-overlay" id="signupOverlay" aria-hidden="true">
    <div class="login-modal" role="dialog">
        <button class="close-x" id="closeSignupX">×</button>
        <img src="<?php echo $baseUrl; ?>imgs/loginho.png" class="modal-logo">
        <h3>Crie sua conta</h3>
        <form class="login-form" id="formCadastro" enctype="multipart/form-data">
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit" class="btn-primary">Cadastrar</button>
        </form>
        <p>Já tem conta? <a href="#" id="goToLogin">Faça login</a></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?php echo $baseUrl; ?>';
    const loginOverlay = document.getElementById('loginOverlay');
    const signupOverlay = document.getElementById('signupOverlay');
    const contactOverlay = document.getElementById('contactOverlay');

    window.openLoginModal = () => { loginOverlay.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    window.openSignupModal = () => { signupOverlay.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    window.openContactModal = () => { contactOverlay.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    
    window.closeModals = () => {
        if(loginOverlay) loginOverlay.style.display = 'none';
        if(signupOverlay) signupOverlay.style.display = 'none';
        if(contactOverlay) contactOverlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    if(document.getElementById('openLoginMenu')) document.getElementById('openLoginMenu').onclick = (e) => { e.preventDefault(); window.openLoginModal(); };
    if(document.getElementById('openSignupMenu')) document.getElementById('openSignupMenu').onclick = (e) => { e.preventDefault(); window.openSignupModal(); };
    if(document.getElementById('openContactDropdown')) document.getElementById('openContactDropdown').onclick = (e) => { e.preventDefault(); e.stopPropagation(); window.openContactModal(); };
    
    document.querySelectorAll('.close-x, #closeModalBtn').forEach(btn => btn.onclick = window.closeModals);
    
    // Login Lógica
    const usuarioLogado = <?php echo (isset($_SESSION['usuario']) && $_SESSION['usuario']) ? 'true' : 'false'; ?>;
    
    const cartAction = (e) => {
        if(!usuarioLogado) { e.preventDefault(); e.stopPropagation(); window.openLoginModal(); }
        else { window.location.href = baseUrl + 'carrinho.php'; }
    };
    if(document.getElementById('carrinho')) document.getElementById('carrinho').onclick = cartAction;

    const favAction = (e) => {
        if(!usuarioLogado) { e.preventDefault(); e.stopPropagation(); window.openLoginModal(); }
        else { window.location.href = baseUrl + 'favoritos.php'; }
    };
    if(document.getElementById('heartIcon')) document.getElementById('heartIcon').onclick = favAction;

    // Switch Login/Signup
    const linkCadastro = document.querySelector('#loginOverlay .link-cadastro');
    if(linkCadastro) linkCadastro.onclick = (e) => { e.preventDefault(); closeModals(); openSignupModal(); };
    
    const goToLogin = document.getElementById('goToLogin');
    if(goToLogin) goToLogin.onclick = (e) => { e.preventDefault(); closeModals(); openLoginModal(); };
});
</script>