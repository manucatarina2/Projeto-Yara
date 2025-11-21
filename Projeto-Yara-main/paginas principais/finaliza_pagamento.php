<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>YARA - A arte de vestir presença</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
        main {
            background-color: #fff; /* Fundo branco para o conteúdo principal */
            padding-top: 20px; /* Espaço após o header */
        }

        .page-title {
            text-align: center;
            padding: 30px 20px 30px; /* Reduzido padding superior */
            background-color: #fff; /* Fundo branco */
        }

        .page-title h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8em;
            color: var(--accent); 
            margin-bottom: 15px;
        }

        .page-title p {
            font-size: 1.1em;
            color: #555;
            max-width: 600px;
            margin: 0 auto;
        }

        .checkout-container {
            display: flex;
            justify-content: center;
            padding: 20px 5% 40px; /* Ajustado padding */
            gap: 40px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .checkout-box { /* Novo container principal */
            background-color: var(--background-white);
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            padding: 40px;
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            width: 100%;
            max-width: 1000px; /* Limite de largura */
        }
        
        /* Coluna Resumo Pedido */
        .order-summary {
            background-color: #faeef4; /* Rosa bem claro */
            border-radius: 8px;
            padding: 30px;
            flex: 1; /* Ocupa espaço disponível */
            min-width: 280px; /* Largura mínima */
            height: fit-content; /* Altura ajusta ao conteúdo */
            position: sticky;
            top: 20px; /* Distância do topo ao fixar */
            align-self: flex-start; 
        }

        /* Coluna Detalhes Contato/Pagamento */
        .contact-payment-details {
            flex: 2; /* Ocupa mais espaço */
            min-width: 300px;
        }
        
        /* Títulos H2 dentro das colunas */
        .order-summary h2,
        .contact-payment-details h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8em;
            color: var(--text);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }
        
        /* Detalhes do Resumo */
        .order-summary .item-detail {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1em;
        }

        .order-summary .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            font-size: 1.3em;
            font-weight: 600;
            color: var(--text);
        }

        /* Formulários */
        .contact-info label,
        .billing-address label,
        .payment-method label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text);
            font-size: 1.1em;
        }

        .contact-info input,
        .billing-address input,
        .payment-details input,
        .installments select {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
            border-radius: 4px; /* Bordas menos arredondadas */
            font-size: 1em;
            background: #fff;
            transition: border-color 0.3s ease;
            font-family: 'Poppins', sans-serif; /* Garantir fonte */
        }

        .contact-info input:focus,
        .billing-address input:focus,
        .payment-details input:focus,
        .installments select:focus {
            outline: none;
            border-color: var(--accent);
        }

        /* Opções de Pagamento (Radio) */
        .payment-options {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            border: 1px solid var(--border-color);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .payment-option input[type="radio"] { display: none; }

        .payment-option .radio-circle {
            width: 18px; height: 18px; border: 2px solid #ccc;
            border-radius: 50%; margin-right: 10px; position: relative;
        }

        .payment-option input[type="radio"]:checked+.radio-circle { border-color: var(--accent); }

        .payment-option input[type="radio"]:checked+.radio-circle::after {
            content: ''; width: 10px; height: 10px; background-color: var(--accent);
            border-radius: 50%; position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%);
        }

        .payment-option:hover { border-color: var(--accent); }
        
        /* Detalhes Cartão */
        .payment-details .card-row { display: flex; gap: 20px; }
        .payment-details .card-row>div { flex: 1; }

        /* Mensagem Segurança e Botão Finalizar */
        .secure-message {
            background-color: #f0f0f0; color: #555; padding: 12px 20px;
            border-radius: 5px; margin-top: 25px; margin-bottom: 30px;
            display: flex; align-items: center; font-weight: 500; font-size: 0.95em;
        }
        .secure-message i { margin-right: 10px; font-size: 1.2em; color: var(--text); }

        .finish-payment-btn {
            width: 100%; padding: 15px; background-color: var(--accent);
            color: white; border: none; border-radius: 4px; font-size: 1.2em;
            font-weight: 600; cursor: pointer; transition: background-color 0.3s ease;
            display: flex; align-items: center; justify-content: center;
        }
        .finish-payment-btn i { margin-right: 10px; }
        .finish-payment-btn:hover { background-color: #d84a7e; /* Tom mais escuro de rosa */ }

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
        /* === MODAL CONFIRMAÇÃO === */
        .modal-overlay {
            position: fixed; inset: 0; background-color: var(--overlay);
            display: flex; justify-content: center; align-items: center; z-index: 2000;
            visibility: hidden; opacity: 0; transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .modal-overlay.active { visibility: visible; opacity: 1; }
        .modal-content {
            background-color: #fff; padding: 40px; border-radius: 8px;
            max-width: 550px; width: 90%; text-align: center; position: relative;
            transform: scale(0.9); transition: transform 0.3s ease;
        }
        .modal-overlay.active .modal-content 
        .modal-close-btn {
            position: absolute; top: 15px; right: 20px; background: none; border: none;
            font-size: 28px; color: var(--accent); cursor: pointer; transition: color 0.3s ease;
        }
        .modal-close-btn:hover { color: var(--text); }
        .modal-header .modal-logo { width: 70px; margin-bottom: 20px; } /* Usar logo correto */
        .modal-header h1 { font-family: 'Playfair Display', serif; color: var(--accent); font-size: 2.4em; margin-bottom: 15px; }
        .modal-header p { color: #666; font-size: 1.05em; line-height: 1.7; max-width: 420px; margin: 0 auto 30px auto; }
        .verification-box { background-color: #fff8fb; border: 1px solid #fde2f0; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: left; }
        .verification-box h2 { font-family: 'Playfair Display', serif; color: var(--accent); text-align: center; font-size: 1.5em; margin-top: 0; margin-bottom: 20px; }
        .verification-content { display: flex; align-items: flex-start; gap: 15px; }
        .verification-content .fas { color: var(--accent); font-size: 20px; margin-top: 5px; }
        .verification-content p { color: #777; font-size: 0.95em; line-height: 1.7; margin-bottom: 10px; }
        .verification-content strong { color: var(--text); }
        .modal-action-btn {
            display: inline-flex; align-items: center; justify-content: center; background-color: var(--accent);
            color: var(--background-white); padding: 12px 30px; border-radius: 25px; font-weight: 600;
            font-size: 1.1em; border: none; cursor: pointer; transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .modal-action-btn:hover { background-color: #d84a7e; transform: translateY(-2px); }
        .modal-action-btn .fas { margin-right: 10px; }

    </style>
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
              <a>Personalize Já</a> 
              <a>Presente</a> 
            </div> 
          </div> 
        </div> 
        <!-- Ícones --> 
        <div class="menu-icons" aria-hidden="true"> 
          <!-- Ícone do coração com ID -->
          <img src="../imgs/coracao.png" alt="Favoritos" id="heartIcon"> 
          <img src="../imgs/lupa.png" alt="Buscar"> 
          <img src="../imgs/tigra.png" alt="Tigre" class="tigre-icon"> 
        </div> 
      </nav> 
    </div> 
  </div> 
</header> 

    <main>
        <section class="page-title">
            <h1>Finalizar Pagamento</h1>
            <p>Deixe que cada joia da Joalheria Yara conte sua história e traduza sua personalidade.</p>
        </section>

        <section class="checkout-container">
            <div class="checkout-box">

                <div class="order-summary">
                    <h2>Resumo do seu Pedido</h2>
                    <div class="item-detail">
                        <span>Produto: Colar Eterna Presença</span>
                        <span></span>
                    </div>
                    <div class="item-detail">
                        <span>Garantia:</span>
                        <span>1 ano</span>
                    </div>
                    <div class="item-detail">
                        <span>Valor por unidade:</span>
                        <span>R$ 299,00</span>
                    </div>
                    <div class="total-row">
                        <span>Total:</span>
                        <span>R$ 299,00</span>
                    </div>
                </div>

                <div class="contact-payment-details">
                    <div class="contact-info">
                        <h2>Informações de Contato</h2>
                        <label for="fullName">Nome Completo</label>
                        <input type="text" id="fullName" placeholder="Seu nome completo">

                        <label for="email">E-mail</label>
                        <input type="email" id="email" placeholder="seu.email@exemplo.com">

                        <label for="phone">Telefone</label>
                        <input type="tel" id="phone" placeholder="(XX) XXXXX-XXXX">
                    </div>

                    <div class="billing-address">
                        <h2>Endereço de Cobrança</h2>
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" placeholder="XXXXX-XXX">

                        <label for="address">Endereço</label>
                        <input type="text" id="address" placeholder="Rua, Av., etc.">

                        <div class="card-row">
                            <div>
                                <label for="number">Número</label>
                                <input type="text" id="number" placeholder="123">
                            </div>
                            <div>
                                <label for="complement">Complemento (Opcional)</label>
                                <input type="text" id="complement" placeholder="Ap, Bloco">
                            </div>
                        </div>
                    </div>

                    <a class="payment-method">
                        <h2>Método de Pagamento</h2>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="paymentMethod" value="creditCard" checked>
                                <span class="radio-circle"></span>
                                Cartão de Crédito
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="paymentMethod" value="pix">
                                <span class="radio-circle"></span>
                                PIX
                            </label>
                        </div>

                        <div class="payment-details">
                            <label for="cardNumber">Número do Cartão</label>
                            <input type="text" id="cardNumber" placeholder="XXXX XXXX XXXX XXXX">

                            <div class="card-row">
                                <div>
                                    <label for="validity">Validade</label>
                                    <input type="text" id="validity" placeholder="MM/AA">
                                </div>
                                <div>
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" placeholder="XXX">
                                </div>
                            </div>

                            <label for="cardName">Nome Impresso no Cartão</label>
                            <input type="text" id="cardName" placeholder="Seu nome no cartão">

                            <div class="installments">
                                <label for="installments">Número de Parcelas</label>
                                <select id="installments">
                                    <option value="1x">1x de R$ 299,00</option>
                                    <option value="2x">2x de R$ 149,50</option>
                                    <option value="3x">3x de R$ 99,67</option>
                                </select>
                            </div>
                        </div>

                        <div class="secure-message">
                            <i class="fas fa-lock"></i>
                            <span>Seu pagamento é 100% seguro e criptografado.</span>
                        </div>

                        <button class="finish-payment-btn">
                            <i class="fas fa-check-circle"></i>
                            Finalizar Pagamento
                        </button>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <div class="modal-overlay" id="confirmationModal">
        <div class="modal-content">
            <button class="modal-close-btn" id="closeModalBtn">&times;</button>

            <div class="modal-header">
                <img src="../imgs/loginho.png" alt="Yara Logo" class="modal-logo">

                <h1>Pagamento Confirmado!</h1>
                <p>Sua escolha na Joalheria Yara está confirmada! Prepare-se para expressar sua verdadeira essência.</p>
            </div>

            <div class="verification-box">
                <h2>Verificação</h2>
                <div class="verification-content">
                    <i class="fas fa-envelope-open-text"></i>
                    <div>
                        <p>
                            Um e-mail de confirmação foi enviado para
                            <strong>**seu.email@exemplo.com**</strong>.
                        </p>
                        <p>Acesse os detalhes completos no seu perfil.</p>
                    </div>
                </div>
            </div>

                <a href="perfil.html" class="modal-action-btn"><span>Acessar Perfil</span></a>
            </a>
        </div>
    </div>

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
const contactCloseX = document.getElementById('closeX');
const contactCloseBtn = document.getElementById('closeModalBtn'); // modal de contato

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
if (contactCloseX) contactCloseX.addEventListener('click', closeContactModal);
if (contactCloseBtn) contactCloseBtn.addEventListener('click', closeContactModal);
if (contactOverlay) contactOverlay.addEventListener('click', e => { if (e.target === contactOverlay) closeContactModal(); });

const contactModalBox = document.querySelector('.contact-modal');
if (contactModalBox) contactModalBox.addEventListener('click', e => e.stopPropagation());

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
    // Verifica se o checkbox existe e está marcado
    if (!newsletterCheckbox || !newsletterCheckbox.checked) {
      alert("Você precisa concordar com a Política de Privacidade para continuar.");
      return;
    }
    // Abre o modal de cadastro
    openSignup();
  });
}

// --- MODAL DE CONFIRMAÇÃO/PAGAMENTO ---
const confirmationModal = document.getElementById('confirmationModal');
const paymentCloseBtn = document.querySelector('.finish-payment-modal .close-btn'); // novo ID ou classe sugerida
const finishPaymentBtn = document.querySelector('.finish-payment-btn');

function openPaymentModal() {
  if (!confirmationModal) return;
  confirmationModal.classList.add('active');
}

function closePaymentModal() {
  if (!confirmationModal) return;
  confirmationModal.classList.remove('active');
}

if (finishPaymentBtn) {
  finishPaymentBtn.addEventListener('click', (event) => {
    event.preventDefault();
    openPaymentModal();
  });
}

if (paymentCloseBtn) {
  paymentCloseBtn.addEventListener('click', closePaymentModal);
}

if (confirmationModal) {
  confirmationModal.addEventListener('click', (event) => {
    if (event.target === confirmationModal) {
      closePaymentModal();
    }
  });
}

// --- FECHAR TODOS OS MODAIS COM ESC ---
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    if (loginOverlay && loginOverlay.style.display === 'flex') closeLogin();
    if (signupOverlay && signupOverlay.style.display === 'flex') closeSignup();
    if (contactOverlay && contactOverlay.style.display === 'flex') closeContactModal();
    if (confirmationModal && confirmationModal.classList.contains('active')) closePaymentModal();
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