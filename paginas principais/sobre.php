<?php
// sobre.php
require_once 'funcoes.php';

// Lista de países
$paises = ['Brasil', 'Estados Unidos', 'Portugal', 'Angola', 'Moçambique', 'Japão', 'China'];
sort($paises);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>YARA - Sobre</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        /* Estilos originais mantidos */
        .sobre-section { display: flex; align-items: center; justify-content: center; background-color: #000; color: #fff; min-height: 480px; padding: 0 80px; gap: 100px; }
        .sobre-text { max-width: 420px; flex: 1; padding-left: 100px; }
        .sobre-text h2 { font-size: 38px; font-weight: 300; letter-spacing: 1.5px; margin-bottom: 16px; text-transform: uppercase; font-family: 'Playfair Display', serif; }
        .sobre-text p { font-size: 16px; font-weight: 300; line-height: 1.7; color: #ddd; margin-bottom: 25px; font-family: 'Poppins', sans-serif; }
        .sobre-img { flex: 1; display: flex; justify-content: center; }
        .sobre-img img { max-width: 420px; width: 100%; height: auto; display: block; }

        .identidade-section { display: flex; align-items: center; justify-content: space-evenly; background-color: #fff; color: #000; min-height: 480px; padding: 0 80px; }
        .identidade-img { display: flex; justify-content: flex-start; position: relative; }
        .identidade-img img { width: 350px; height: auto; display: block; }
        .identidade-text { max-width: 480px; text-align: right; }
        .identidade-text h2 { font-size: 36px; font-weight: 300; letter-spacing: 1.5px; margin-bottom: 16px; text-transform: uppercase; font-family: 'Playfair Display', serif; }
        .identidade-text p { font-size: 16px; font-weight: 300; line-height: 1.7; color: #333; margin-bottom: 25px; font-family: 'Poppins', sans-serif; }

        .color-bubbles { display: flex; flex-direction: column; align-items: center; gap: 25px; position: absolute; right: -40px; top: 50%; transform: translateY(-50%); }
        .color-bubbles span { width: 30px; height: 30px; border-radius: 50%; background-color: var(--color); box-shadow: 0 0 10px rgba(0,0,0,0.1); transition: transform 0.3s; border: 1px solid #eee; }
        
        /* Newsletter */
        .newsletter-section { background: #fff; min-height: 40vh; display: flex; justify-content: center; align-items: center; border-top: 1px solid #eee; }
        .newsletter-container { display: flex; align-items: center; justify-content: center; gap: 80px; flex-wrap: wrap; max-width: 900px; width: 100%; }
        .newsletter-content h2 { font-size: 24px; font-weight: 400; margin-bottom: 25px; color: #000; font-family: 'Playfair Display', serif; }
        .newsletter-form { display: flex; align-items: stretch; border: 1px solid #fe7db9; max-width: 420px; margin-bottom: 15px; }
        .newsletter-form input { flex: 1; padding: 12px 14px; border: none; outline: none; }
        .newsletter-form button { background: #fe7db9; border: none; color: #fff; padding: 0 18px; cursor: pointer; font-size: 20px; }

        /* Estilo para o botão virar link e parecer botão */
        .btn-link-fix {
            display: inline-block;
            padding: 7px 12px;
            border: 1.5px solid #fe7db9;
            background: #fff;
            color: #fe7db9;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none; /* Tira o sublinhado */
            cursor: pointer;
            text-align: center;
            border-radius: 0; /* Estilo original quadrado */
            font-family: Arial, sans-serif;
        }
        .btn-link-fix:hover {
            background: #fe7db9; 
            color: #fff;
        }

        @media (max-width: 900px) {
            .sobre-section, .identidade-section { flex-direction: column; text-align: center; padding: 60px 30px; gap: 40px; }
            .sobre-text { padding: 0; }
            .identidade-text { text-align: center; }
            .identidade-img { justify-content: center; }
            .color-bubbles { position: static; flex-direction: row; margin-top: 20px; transform: none; }
            .newsletter-container { flex-direction: column; text-align: center; gap: 30px; }
        }
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <section class="sobre-section">
        <div class="sobre-text">
            <h2>SOBRE NÓS</h2>
            <p>A YARA nasce da fusão entre intensidade e delicadeza, criando joias que ultrapassam o adorno e se tornam expressão. Cada criação carrega identidade, presença e significado.</p>
        </div>
        <div class="sobre-img">
            <img src="../paginas principais/imgs/sobre.png" alt="Tigre YARA" onerror="this.src='imgs/produto-padrao.png'">
        </div>
    </section>

    <section class="identidade-section">
        <div class="identidade-img">
            <img src="../paginas principais/imgs/logo.png" alt="Logo YARA" onerror="this.src='imgs/yaraletra.png'">
            <div class="color-bubbles">
                <span style="--color: #000000"></span>
                <span style="--color: #F33283"></span>
                <span style="--color: #FF80B5"></span>
                <span style="--color: #FFF4FB"></span>
            </div>
        </div>
        <div class="identidade-text">
            <h2>IDENTIDADE VISUAL</h2>
            <p>O tigre é a presença, coragem e intensidade que coexistem com a delicadeza. A cor rosa traduz a essência contemporânea da marca.</p>
        </div>
    </section>

    <section class="newsletter-section">
        <div class="newsletter-container">
            <div class="newsletter-content">
                <h2>Descubra primeiro todas as novidades <br> da Yara. Cadastre-se!</h2>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" name="email" placeholder="Digite aqui o seu e-mail" required>
                    <button type="submit">→</button>
                </form>
                <label class="checkbox" style="display:flex; align-items:center; gap:5px; font-size:13px; color:#333;">
                    <input type="checkbox" required> <span>Li e concordo com a <a href="#" style="color:#fe7db9;">Política de privacidade</a></span>
                </label>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <div class="contact-overlay" id="contactOverlay" aria-hidden="true">
        <div class="contact-modal" role="dialog" aria-modal="true">
            <button class="close-x" id="closeX">X</button>
            <img src="imgs/loginho.png" class="modal-logo">
            <h3 id="contactTitle">Entre em Contato</h3>
            <p class="intro">Fale com um Embaixador YARA.</p>

            <div class="contact-grid">
                <div class="contact-block">
                    <div class="block-title">Fale Conosco</div>
                    <div class="block-desc">(11) 4380-0328</div>
                    <a class="btn-outline" href="tel:+551143800328">Ligar Agora</a>
                </div>

                <div class="contact-block">
                    <div class="block-title">Escreva para Nós</div>
                    <button class="btn-outline" onclick="window.location.href='mailto:contato@yara.com'">Enviar Email</button>
                </div>

                <div class="contact-block">
                    <div class="block-title">Atendimento via Chat</div>
                    <div class="block-desc">Tire dúvidas agora mesmo.</div>
                    <a href="chat.php" class="btn-link-fix">Iniciar Chat</a>
                </div>

                <div class="contact-block">
                    <div class="block-title">WhatsApp</div>
                    <div class="block-desc">Atendimento personalizado.</div>
                    <button class="btn-outline" onclick="abrirWhatsApp()">Enviar Mensagem</button>
                </div>
                
                <div class="contact-block">
                    <div class="block-title">Especialista</div>
                    <div class="block-desc">Dúvidas técnicas?</div>
                    <a href="chat.php?tipo=especialista" class="btn-link-fix">Falar com Especialista</a>
                </div>
            </div>

            <div class="contact-actions" style="margin-top:20px;">
                <button class="btn-primary" id="closeModalBtn">Fechar</button>
            </div>
        </div>
    </div>

    <script>
        function abrirWhatsApp() {
            window.open('https://wa.me/5511999999999', '_blank');
        }

        // Controle do Modal
        const contactOverlay = document.getElementById('contactOverlay');
        const closeX = document.getElementById('closeX');
        const closeModalBtn = document.getElementById('closeModalBtn');

        function closeContactModal() {
            if (contactOverlay) {
                contactOverlay.style.display = 'none';
                contactOverlay.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        }

        if (closeX) closeX.addEventListener('click', closeContactModal);
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeContactModal);
        if (contactOverlay) contactOverlay.addEventListener('click', e => {
            if (e.target === contactOverlay) closeContactModal();
        });
    </script>
</body>
</html>