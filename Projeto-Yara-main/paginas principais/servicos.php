<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>YARA - Sobre</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root{
    --bg: #ffe7f6;
    --text: #000;
    --icon-size: 24px;
    --logo-height: 60px;
    --gap: 20px;
    --container-w: 1100px;
    --overlay: #0000008e;
  }

  body{
    margin:0;
    font-family:"Arial",serif;
    background:var(--bg);
  }

  header{
    width:100%;
    background:var(--bg);
    padding:14px 20px 6px;
    box-sizing:border-box;
  }
  .container{
    max-width:var(--container-w);
    margin:0 auto;
  }

  .top-row{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:10px;
  }

  .top-left{
    display:flex;
    gap:14px;
    align-items:center;
  }

  .top-left a{
    text-decoration:none;
    color:var(--text);
    font-size:12px;
    letter-spacing:0.5px;
    position:relative;
    padding-bottom:4px;
  }

  .top-left a:hover::after{
    content:'';
    position:absolute;
    left:0;
    bottom:-4px;
    width:100%;
    height:2px;
    background:var(--text);
  }

  .top-right-icons{
    display:flex;
    gap:12px;
    align-items:center;
  }

  .top-right-icons img{
    width:var(--icon-size);
    height:var(--icon-size);
    object-fit:contain;
    cursor:pointer;
  }

  .logo-row{
    display:flex;
    justify-content:center;
    align-items:center;
    margin-top:4px;
  }

  .logo-row img{
    height:var(--logo-height);
    width:auto;
  }

  .menu-row{
    display:flex;
    justify-content:center;
    align-items:center;
    margin-top:6px;
  }

  .menu{
    display:flex;
    align-items:center;
    gap:20px;
  }

  .menu a{
    text-decoration:none;
    color:var(--text);
    font-size:12px;
    letter-spacing:0.5px;
    padding-bottom:4px;
    position:relative;
  }

  .menu a.active{
    border-bottom:2px solid var(--text);
  }

  .menu a:not(.active):hover::after{
    content:'';
    position:absolute;
    left:0;
    bottom:-4px;
    width:100%;
    height:2px;
    background:var(--text);
  }

  .menu-icons{
    display:flex;
    gap:12px;
    align-items:center;
  }

  .menu-icons img{
    width:var(--icon-size);
    height:var(--icon-size);
    object-fit:contain;
    cursor:pointer;
  }

  .menu-icons img.tigre-icon{
    width:34px;
    height:auto;
  }
  .menu-item { position: relative; display: flex; align-items: center; } 
.dropdown { position: absolute; top: calc(100% + 8px); left: 50%; transform: translateX(-50%); background: #fff; padding: 30px 80px; box-shadow: 0px 4px 14px rgba(0,0,0,0.15); border-radius: 2px; display: none; gap: 120px; z-index: 9999; white-space: nowrap; } 
.menu-item:hover .dropdown, .dropdown:hover { display: flex; } 
.dropdown h4 { font-size: 14px; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 4px; } 
.dropdown a { display: block; font-size: 13px; color: #000; margin: 7px 0; text-decoration: none; cursor: pointer; } 
.dropdown a:hover { text-decoration: underline; }

  /* === Seção Serviços === */
    .services-section {
        background: #fff;
        padding: 60px 20px;
    }
/* === Seção Newsletter (centralizada) === */
.newsletter-section {
  background: #fff;
  min-height: 50vh; /* ocupa toda a tela */
  display: flex;
  justify-content: center;
  align-items: center;
}

.newsletter-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 80px; /* espaçamento entre logo e texto */
  flex-wrap: wrap;
  text-align: left;
  max-width: 900px;
  width: 100%;
}

/* Logo */
.newsletter-logo img {
  max-width: 200px;
  height: auto;
  display: block;
}

/* Texto e formulário */
.newsletter-content {
  flex: 1;
  min-width: 300px;
}

.newsletter-content h2 {
  font-size: 24px;
  font-weight: 400;
  margin-bottom: 25px;
  color: #000;
  line-height: 1.4;
}

/* Formulário */
.newsletter-form {
  display: flex;
  align-items: stretch;
  border: 1px solid #fe7db9;
  max-width: 420px;
  margin-bottom: 15px;
}

.newsletter-form input {
  flex: 1;
  padding: 12px 14px;
  border: none;
  outline: none;
  font-size: 14px;
  color: #000;
}

.newsletter-form input::placeholder {
  color: #fe7db9;
}

.newsletter-form button {
  background: #fe7db9;
  border: none;
  color: #fff;
  font-size: 20px;
  padding: 0 18px;
  cursor: pointer;
  transition: 0.3s;
}

.newsletter-form button:hover {
  background: #000;
}

/* Checkbox */
.checkbox {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: #333;
}

.checkbox input {
  width: 16px;
  height: 16px;
  cursor: pointer;
}

.checkbox a {
  color: #fe7db9;
  text-decoration: none;
  font-weight: 500;
}

.checkbox a:hover {
  text-decoration: underline;
}

/* === Responsivo === */
@media (max-width: 900px) {
  .newsletter-container {
    flex-direction: column;
    text-align: center;
    gap: 30px;
  }

  .newsletter-content h2 {
    text-align: center;
  }

  .newsletter-form {
    margin: 0 auto 15px;
  }

  .checkbox {
    justify-content: center;
  }
}
/* === Rodapé === */
.footer {
  background: #000;
  color: #fff;
  padding: 40px 20px 20px;
}

.footer-container {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 30px;
}

.footer-col h3, .footer-col h4 {
  margin-bottom: 15px;
}

.footer-col p {
  margin: 8px 0;
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
  color: #fff;
  transition: 0.3s;
}

.footer-col ul li a:hover {
  color: var(--accent);
}

.social a {
  margin-right: 10px;
  font-size: 18px;
  color: #fff;
  transition: 0.3s;
}

.social a:hover {
  color: var(--accent);
}

.footer-bottom {
  text-align: center;
  border-top: 1px solid #fe7db9;
  margin-top: 20px;
  padding-top: 10px;
  font-size: 14px;
  color: #fe7db9;
}

/* Responsivo Newsletter */
@media (max-width: 768px) {
  .newsletter-container {
    flex-direction: column;
    text-align: center;
  }

  .newsletter-form {
    margin: 0 auto 15px auto;
  }

  .checkbox {
    justify-content: center;
  }
}


/* MODAL CONTACT - estilo idêntico*/

.contact-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: var(--overlay);
  z-index: 9999;
  align-items: center;
  justify-content: center;
  padding: 24px;
  overflow-y: auto;
}

.contact-modal {
  background: #fff;
  width: 600px;
  max-width: calc(100% - 48px);
  border-radius: 4px;
  box-shadow: 0 18px 40px rgba(0,0,0,0.6);
  position: relative;
  padding: 28px 34px 24px;
  box-sizing: border-box;
  font-size: 14px;
  color: #222;
  max-height: 90vh; 
  overflow-y: auto; 
}

/* X rosa cantinho */
.contact-modal .close-x {
  position: absolute;
  right: 12px;
  top: 10px;
  background: transparent;
  border: none;
  color: var(--accent);
  font-weight: 700;
  font-size: 16px;
  cursor: pointer;
}

/* logo tigre centralizado no topo do modal */
.contact-modal .modal-logo {
  display: block;
  margin: 6px auto 10px;
  height: 30px; /* parecido com a imagem */
  object-fit: contain;
}

/* Título central */
.contact-modal h3 {
  text-align: center;
  margin: 6px 0 12px;
  font-size: 22px;
  font-weight: 700;
  color: #111;
  letter-spacing: 0.2px;
  font-family: "Arial", serif;
}

/* parágrafo introdutório */
.contact-modal .intro {
  text-align: center;
  color: #6b6b6b;
  font-size: 13px;
  line-height: 1.45;
  max-width: 620px;
  margin: 0 auto 14px;
}

/* select area with label above */
.contact-modal .select-label {
  display: block;
  margin: 8px 0 6px;
  text-align: center;
  color: #444;
  font-size: 13px;
}

.contact-modal .select-wrap {
  display: flex;
  justify-content: center;
  margin-bottom: 18px;
}

.contact-modal select {
  width: 70%;
  max-width: 420px;
  padding: 10px 12px;
  border: 1px solid #e6e6e6;
  border-radius: 4px;
  background: #fff;
  color: #222;
  font-size: 13px;
  outline: none;
}

/* grid de conteúdo: duas colunas */
.contact-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px 28px;
  margin-top: 6px;
}

/* cada bloco dentro das colunas */
.contact-block {
  display: block;
  padding: 6px 0;
  min-height: 58px;
}

.contact-block .block-title {
  font-weight: 700;
  font-size: 14px;
  margin-bottom: 6px;
  color: #111;
}

.contact-block .block-desc {
  color: #666;
  font-size: 13px;
  line-height: 1.4;
  margin-bottom: 8px;
}

/* outline button pequeno rosa */
.btn-outline {
  display: inline-block;
  padding: 7px 12px;
  border: 1.5px solid #fe7db9;
  background: #fff;
  color: #fe7db9;
  font-weight: 600;
  font-size: 13px;
  text-decoration: none;
  cursor: pointer;
}

/* botão principal Fechar grande */
.contact-actions {
  display: flex;
  justify-content: center;
  margin-top: 16px;
}

.btn-primary {
  display: inline-block;
  width: 260px;
  max-width: 90%;
  padding: 10px 18px;
  background: #fe7db9;
  color: #fff;
  border: none;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
}

/* link de telefone com ícone */
.phone-line {
  display: inline-flex;
  gap:8px;
  align-items:center;
  color:#333;
  font-size:13px;
}

/* pequenos ícones dentro dos blocos */
.block-meta {
  display:flex;
  gap:10px;
  align-items:center;
  color:#888;
  font-size:13px;
}

/* responsivo - modal vira coluna única em telas pequenas */
@media (max-width: 740px) {
  .contact-modal { padding: 20px 18px; }
  .contact-grid { grid-template-columns: 1fr; gap:14px; }
  .contact-modal select { width: 100%; }
}
/* === Modal Login e Cadastro === */
.login-overlay {
  position: fixed;
  inset: 0; /* top:0; left:0; right:0; bottom:0; */
  background-color: rgba(0,0,0,0.6);
  display: none;
  justify-content: center;
  align-items: flex-start; /* alinhamento topo */
  z-index: 1000;
  overflow-y: auto; /* permite rolagem */
  padding: 60px 0;  /* espaço superior e inferior */
}

.login-modal {
  background: #fff;
  width: 400px;
  max-width: 90%;
  border-radius: 12px;
  padding: 30px 35px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.25);
  position: relative;
  font-family: 'Poppins', sans-serif;
  margin: 0 auto; /* centraliza horizontalmente */
  box-sizing: border-box;
}

/* Botão de fechar */
.close-x {
  position: absolute;
  top: 15px;
  right: 20px;
  background: none;
  border: none;
  font-size: 20px;
  color: #f06ca2;
  cursor: pointer;
}

/* Logo do modal */
.modal-logo {
  width: 60px;
  display: block;
  margin: 0 auto 15px;
}

/* Título */
#signupTitle {
  font-size: 22px;
  font-weight: 700;
  color: #000;
  text-align: center;
  margin-bottom: 15px;
}

/* ===== Formulário ===== */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 10px;
  align-items: stretch; /* ⚡ campos ficam esticados à esquerda */
}

.login-form p {
  font-weight: 600;
  color: #000;
  text-align: left;
  margin: 10px 0 6px;
  font-size: 15px;
}

.login-form input[type="text"],
.login-form input[type="email"],
.login-form input[type="password"] {
  width: 100%;
  padding: 12px;
  border-radius: 6px;
  border: 1px solid #e0e0e0;
  font-size: 14px;
  outline: none;
  box-sizing: border-box;
}

.login-form input::placeholder {
  color: #bfbfbf;
}

/* ===== Checkbox ===== */
.checkbox {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 14px;
  color: #000;
  margin-top: 15px;
  line-height: 1.5;
}

.checkbox input {
  width: 18px;
  height: 18px;
  accent-color: #f06ca2;
  margin-top: 2px;
}

.checkbox a {
  color: #f06ca2;
  text-decoration: underline;
  font-weight: 400;
}

/* ===== Botões ===== */
.btn-primary {
  background-color: #f06ca2;
  border: none;
  color: #fff;
  width: 100%;
  padding: 13px;
  font-size: 15px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: 0.2s ease;
  margin-top: 18px;
}

.btn-primary:hover {
  background-color: #e3558f;
}

#loginGoogle {
  display: block;
  width: 100%;
  margin-top: 10px;
  padding: 10px;
  border-radius: 4px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  box-sizing: border-box;
}

/* ===== Link final ===== */
.login-modal p:last-of-type {
  text-align: center;
  font-size: 14px;
  color: #444;
  margin-top: 18px;
}

.login-modal a#goToLogin {
  color: #f06ca2;
  text-decoration: none;
  font-weight: 500;
}

.login-modal a#goToLogin:hover {
  text-decoration: underline;
}

/* === Responsividade === */
@media (max-width: 500px) {
  .login-modal {
    width: 90%;
    padding: 20px;
  }

  .login-form input,
  .login-form button,
  #loginGoogle {
    max-width: 100%;
  }
}
/* === Bolhinhas de cor === */
.color-bubbles {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 25px;
}

.color-bubbles span {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-color: var(--color);
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.color-bubbles span:hover {
  transform: scale(1.2);
  box-shadow: 0 0 15px rgba(0,0,0,0.2);
}

/* Responsivo */
@media (max-width: 900px) {
  .color-bubbles {
    flex-direction: row;
    gap: 15px;
  }
}

</style>
</head>
<body>

<header> 
  <div class="container"> 
    <div class="top-row"> 
      <div class="top-left"> 
        <a id="openContact">CONTATO</a> 
        <a href="servicos.html">SERVIÇOS</a> 
      </div> 
      <div class="top-right-icons" aria-hidden="true"> 
        <img src="../imgs/perfil.png" alt="Usuário"> 
        <img src="../imgs/localiza.png" alt="Localização"> 
        <img src="../imgs/sacola.png" alt="Sacola"> 
      </div> 
    </div>  
      <div class="logo-row"> 
        <img src="../imgs/yaraletra.png" alt="YARA Logo"> 
      </div><br>  
      <div class="menu-row"> 
        <nav class="menu" role="navigation" aria-label="Menu principal"> 
          <a href="index.html">INÍCIO</a> 
          <a href="sobre.html">SOBRE</a> 
          <a href="novidades.html">NOVIDADES</a>
          <div class="menu-item acessorios"> 
            <a id="acessorios" class="acessorios-link">ACESSÓRIOS</a> 
            <div class="dropdown"> <div> 
              <h4>Joias Individuais</h4> 
              <a href="all-colares.html">Todos</a> 
              <a href="colares.html">Colares</a> 
              <a href="piercings.html">Piercings</a> 
              <a href="aneis.html">Anéis</a> 
              <a href="brincos.html">Brincos</a> 
              <a href="pulseiras.html">Pulseiras</a> 
              <a href="braceletes.html">Braceletes</a> 
            </div> 
            <div> 
              <h4>Experiências</h4> 
              <a href="personalize.html">Personalize Já</a> 
              <a href="presente.html">Presente</a> 
            </div> 
          </div> 
        </div> <!-- Ícones --> 
        <div class="menu-icons" aria-hidden="true"> 
          <img src="../imgs/coracao.png" alt="Favoritos"> 
          <img src="../imgs/lupa.png" alt="Buscar"> 
          <img src="../imgs/tigra.png" alt="Tigre" class="tigre-icon"> 
        </div> 
      </nav> 
    </div> 
  </div> 
</header> 

<section class="services-section" style="display:flex; flex-direction:column; align-items:center; padding:60px 20px;">
  <div style="display:flex; justify-content:center; align-items:flex-start; gap:40px; flex-wrap:wrap; max-width:1100px; margin-bottom:60px;">
    <div style="flex:1; min-width:300px; max-width:400px;">
      <img src="../imgs/destaque-servico.png" alt="Joia destaque" style="width:100%; display:block;">
    </div>
    <div style="flex:1; min-width:300px; max-width:500px;">
      <h2 style="font-family:'Cormorant Garamond', serif; font-weight:500; font-size:22px; letter-spacing:0.5px; margin-bottom:14px; text-transform:uppercase;">
        NOSSOS SERVIÇOS
      </h2>
      <p style="font-family:'Open Sans', sans-serif; font-size:14px; color:#222; line-height:1.6;">
        Na YARA, cada serviço é parte da arte da joalheria. Seja para ajustes, manutenção, personalização ou cuidados especiais,
        estamos comprometidos em cultivar um relacionamento de confiança que se fortalece com o tempo, acompanhando sua joia em cada capítulo da sua história.
      </p>
    </div>
  </div>

<div class="services-grid" style="
  display: flex;
  justify-content: center;
  align-items: flex-start;
  text-align: center;
  width: 100%;
  gap: 100px;
  flex-wrap: nowrap; /* impede quebra de linha */
  margin: 0 auto;
">

  <div class="service-item" style="
    flex: 1;
    max-width: 380px;
    padding: 40px 25px;
  ">
    <img src="../imgs/diamante-icon.png" alt="Ícone Cuidados"
         style="width:50px; margin-bottom:24px;">
    <h3 style="
      font-family:'Open Sans', sans-serif;
      font-size:22px;
      font-weight:700;
      margin-bottom:12px;
    ">
      Cuidados e Reparos
    </h3>
    <p style="font-size:16px; color:#444; line-height:1.6;">
      Exclusividade para preservar o brilho e a durabilidade das suas peças.
    </p>
  </div>

  <div class="service-item" style="
    flex: 1;
    max-width: 380px;
    padding: 40px 25px;
  ">
    <img src="../imgs/interrogacao.png" alt="Ícone FAQ"
         style="width:65px; margin-bottom:24px;">
    <h3 style="
      font-family:'Open Sans', sans-serif;
      font-size:22px;
      font-weight:700;
      margin-bottom:12px;
    ">
      FAQ
    </h3>
    <p style="font-size:16px; color:#444; line-height:1.6;">
      Tire suas dúvidas sobre pedidos, prazos, serviços e políticas de atendimento.
    </p>
  </div>

  <div class="service-item" style="
    flex: 1;
    max-width: 380px;
    padding: 40px 25px;
  ">
    <img src="../imgs/lapis-icon.png" alt="Ícone Personalize"
         style="width:60px; margin-bottom:24px;">
    <h3 style="
      font-family:'Open Sans', sans-serif;
      font-size:22px;
      font-weight:700;
      margin-bottom:12px;
    ">
      Personalize sua Criação
    </h3>
    <p style="font-size:16px; color:#444; line-height:1.6;">
      Transforme sua joia em algo ainda mais único, feito sob medida para refletir sua identidade.
    </p>
  </div>
</div>



  </div>
</section>


<section class="newsletter-section">
  <div class="newsletter-container">
    <div class="newsletter-logo">
      <img src="../imgs/logo.png" alt="Logo YARA">
    </div>
    <div class="newsletter-content">
      <h2>Descubra primeiro todas as novidades <br>
        da Yara. Cadastre-se!</h2>
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
        <li><a href="#sobre">Sobre nós</a></li>
        <li><a href="#">Coleções</a></li>
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
<div class="contact-overlay" id="contactOverlay" aria-hidden="true">
  <div class="contact-modal" role="dialog" aria-modal="true" aria-labelledby="contactTitle">
    <button class="close-x" id="closeX" aria-label="Fechar">X</button>

    <img src="../imgs/loginho.png" alt="Yara tigre" class="modal-logo">

    <h3 id="contactTitle">Entre em Contato</h3>

    <p class="intro">
      Ficaremos honrados em ajudar com seu pedido, oferecer consultoria personalizada, criar listas de presentes e muito mais. Selecione o canal de contato de sua preferência e fale com um Embaixador YARA.
    </p><br>

    <label class="select-label" for="locationSelect">Por favor, selecione o seu país/região</label>
    <div class="select-wrap">
      <select id="locationSelect" aria-label="Escolha a sua localização">
        <option>Escolha a sua localização:</option>
        <option>Brasil</option>
        <option>Portugal</option>
        <option>Estados Unidos</option>
        <option>Itália</option>
      </select>
    </div><br>

    <div class="contact-grid" aria-hidden="false">

      <div>
        <div class="contact-block">
          <div class="block-title">Fale Conosco</div>
          <div class="block-desc">Estamos disponíveis para lhe atender com exclusividade nos seguintes horários:</div>
          <div class="block-meta"><i class="fa-solid fa-phone"></i> <span>(11) 4380-0328</span></div>
          <div style="margin-top:8px;">
            <a class="btn-outline" href="tel:+551143800328">Conversar pra Nós</a>
          </div>
        </div><br>

        <div class="contact-block">
          <div class="block-title">Escreva para Nós</div>
          <div class="block-desc">Um embaixador YARA irá responder dentro de um dia útil.</div>
          <div style="margin-top:8px;">
            <button class="btn-outline" type="button">Inicie o chat</button>
          </div>
        </div><br>

        <div class="contact-block">
          <div class="block-title">Atendimento via Chat</div>
          <div class="block-desc">De segunda a sexta, das 10h às 19h, nossos embaixadores estão prontos para ajudar.</div>
        </div><br>

        <div class="contact-block">
          <div class="block-title">Fale pelo WhatsApp</div>
          <div class="block-desc">Receba atendimento personalizado de um embaixador YARA.</div>
          <div style="margin-top:8px;">
            <button class="btn-outline" type="button">Envie uma mensagem</button>
          </div>
        </div><br>
      </div>

      <div>
        <div class="contact-block">
          <div class="block-title">Converse com um Especialista em Joias</div>
          <div class="block-desc">De segunda a sexta, das 10h às 19h, nossos embaixadores terão o prazer em lhe orientar.</div>
          <div style="margin-top:8px;">
            <button class="btn-outline" type="button">Inicie o chat</button>
          </div>
        </div><br>

        <div class="contact-block">
          <div class="block-title">Visite-nos em uma Boutique YARA</div>
          <div class="block-desc">Descubra nossas criações em uma de nossas boutiques e viva a experiência exclusiva YARA.</div>
          <div style="margin-top:8px;">
            <button class="btn-outline" type="button">Agende uma visita</button>
          </div>
        </div><br>

        <div class="contact-block">
          <div class="block-title">Ajuda</div>
          <div class="block-desc">Tem dúvidas sobre seu pedido, nossas serviços ou política de devolução? Acesse nossa central de ajuda e encontre todas as respostas.</div>
        </div>
      </div><br>

    </div>

    <div class="contact-actions" style="margin-top:12px;">
      <button class="btn-primary" id="closeModalBtn" type="button">Fechar</button>
    </div>
  </div>
</div>
<div class="login-overlay" id="loginOverlay" aria-hidden="true">
  <div class="login-modal" role="dialog" aria-modal="true" aria-labelledby="loginTitle">
    <button class="close-x" id="closeLoginX" aria-label="Fechar">X</button>

    <img src="../imgs/loginho.png" alt="Logo YARA" class="modal-logo">

    <h3 id="loginTitle">Faça login e encontre o poder de se expressar através de joias únicas.</h3><br>

    <form class="login-form">
      <input type="email" placeholder="seuemail@exemplo.com" required>
      <input type="password" placeholder="Sua senha" required>
      <button type="submit" class="btn-primary">Entrar</button>
    </form>

    <p style="text-align:center; margin: 12px 0;">
      Ainda não tem uma conta? <a href="#" class="link-cadastro">Cadastre-se</a>
    </p><br>

    <button class="btn-outline" id="loginGoogle">Entrar com Google</button>
  </div>
</div>

<div class="login-overlay" id="signupOverlay" aria-hidden="true">
  <div class="login-modal" role="dialog" aria-modal="true" aria-labelledby="signupTitle">
    <button class="close-x" id="closeSignupX" aria-label="Fechar">×</button>

    <img src="../imgs/loginho.png" alt="Logo YARA" class="modal-logo">

    <h3 id="signupTitle">Crie sua conta</h3>

    <form class="login-form">
      <p>Nome Completo</p>
      <input type="text" placeholder="Seu nome" required>

      <p>E mail</p>
      <input type="email" placeholder="seu.email@exemplo.com" required>

      <p>Senha</p>
      <input type="password" placeholder="Mínimo 8 caracteres" required>

      <label class="checkbox">
        <input type="checkbox" required>
        <span>Eu concordo com os <a href="#">Termos de Uso</a> e <a href="#">Política de Privacidade</a></span>
      </label>

      <button type="submit" class="btn-primary">Cadastrar</button>
    </form>

    <p>Já tem uma conta? <a href="#" id="goToLogin">Faça login aqui</a></p>
  </div>
</div>

<script>
/* === MODAL DE CONTATO === */
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

/* === MODAIS DE LOGIN E CADASTRO === */
const perfilIcon = document.querySelector('.top-right-icons img[alt="Usuário"]');
const loginOverlay = document.getElementById('loginOverlay');
const signupOverlay = document.getElementById('signupOverlay');
const closeLoginX = document.getElementById('closeLoginX');
const closeSignupX = document.getElementById('closeSignupX');
const linkCadastro = document.querySelector('#loginOverlay .link-cadastro');
const goToLogin = document.getElementById('goToLogin');

// --- LOGIN ---
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

// --- CADASTRO ---
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

// --- TROCA LOGIN ↔ CADASTRO ---
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

// --- MODAL DE CADASTRO PELO NEWSLETTER ---
const confirmEmailBtn = document.getElementById('confirmEmailBtn');
const newsletterCheckbox = document.querySelector('.newsletter-section .checkbox input');

if (confirmEmailBtn) {
  confirmEmailBtn.addEventListener('click', e => {
    e.preventDefault();
    // Verifica se o checkbox está marcado
    if (!newsletterCheckbox.checked) {
      alert("Você precisa concordar com a Política de Privacidade para continuar.");
      return;
    }
    // Abre o modal de cadastro
    openSignup();
  });
}

// --- FECHAR TODOS OS MODAIS COM ESC ---
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    if (loginOverlay && loginOverlay.style.display === 'flex') closeLogin();
    if (signupOverlay && signupOverlay.style.display === 'flex') closeSignup();
    if (contactOverlay && contactOverlay.style.display === 'flex') closeContactModal();
  }
});

// --- REDIRECIONAR AO CLICAR NO CORAÇÃO ---
const heartIcon = document.getElementById('heartIcon');
if (heartIcon) {
  heartIcon.addEventListener('click', () => {
    window.location.href = 'favoritos.html';
  });
}
// --- REDIRECIONAR LOGIN ---
const loginForm = document.querySelector('#loginOverlay .login-form');
if (loginForm) {
  loginForm.addEventListener('submit', e => {
    e.preventDefault(); // evita envio real do formulário
    window.location.href = 'perfil.html'; // redireciona
  });
}

// --- REDIRECIONAR CADASTRO ---
const signupForm = document.querySelector('#signupOverlay .login-form');
if (signupForm) {
  signupForm.addEventListener('submit', e => {
    e.preventDefault(); // evita envio real do formulário
    window.location.href = 'perfil.html'; // redireciona
  });
}
</script>

</body>
</html>

