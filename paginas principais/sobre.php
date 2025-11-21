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
    font-family: "Arial", serif;
    background: var(--bg);
    color: var(--text);
  }

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
/* Dropdown do usuário controlado via JS */
.user-dropdown {
  display: none !important;
}

.user-dropdown.show {
  display: block !important;
}
  .logo-row img{
    height: var(--logo-height);
    width: auto;
    display:block;
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
  gap: 20px;
  position:relative;
}

.menu a{
  text-decoration:none;
  color:var(--text);
  font-size:12px;
  letter-spacing:0.5px;
  position:relative;
  padding-bottom:4px;
  cursor:pointer;
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
  margin-left:4px;
}

.menu-icons img{
  width: var(--icon-size);
  height: var(--icon-size);
  display:block;
  object-fit:contain;
  cursor:pointer;
}

.menu-icons img.tigre-icon {
  width: 34px;
  height: auto;
}

.menu-item { position: relative; display: flex; align-items: center; } 
.dropdown { position: absolute; top: calc(100% + 8px); left: 50%; transform: translateX(-50%); background: #fff; padding: 30px 80px; box-shadow: 0px 4px 14px rgba(0,0,0,0.15); border-radius: 2px; display: none; gap: 120px; z-index: 9999; white-space: nowrap; } 
.menu-item:hover .dropdown, .dropdown:hover { display: flex; } 
.dropdown h4 { font-size: 14px; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 4px; } 
.dropdown a { display: block; font-size: 13px; color: #000; margin: 7px 0; text-decoration: none; cursor: pointer; } 
.dropdown a:hover { text-decoration: underline; }

  .menu-icons img.tigre-icon{
    width:34px;
    height:auto;
  }

  /* === SEÇÃO SOBRE === */
.sobre-section {
  display: flex;
  align-items: center;
  justify-content: center; /* centraliza o conjunto */
  background-color: #000;
  color: #fff;
  position: relative;
  min-height: 480px;
  padding: 0 80px; /* margens laterais equilibradas */
  gap: 100px; /* distância entre o texto e a imagem */
}

/* Texto */
.sobre-section .sobre-text {
  max-width: 420px;
  flex: 1;
  padding-left: 100px;
}

.sobre-section .sobre-text h2 {
  font-size: 38px;
  font-weight: 300;
  letter-spacing: 1.5px;
  margin-bottom: 16px;
  text-transform: uppercase;
}

.sobre-section .sobre-text p {
  font-size: 16px;
  font-weight: 300;
  line-height: 1.7;
  color: #ddd;
  margin-bottom: 25px;
}

/* Imagem */
.sobre-section .sobre-img {
  flex: 1;
  display: flex;
  justify-content: center;
}

.sobre-section .sobre-img img {
  max-width: 420px; /* mesmo tamanho da imagem da tigre */
  width: 100%;
  height: auto;
  display: block;
}

/* === Responsivo === */
@media (max-width: 900px) {
  .sobre-section {
    flex-direction: column;
    text-align: center;
    gap: 40px;
    padding: 60px 30px;
  }

  .sobre-section .sobre-text {
    max-width: 100%;
  }

  .sobre-section .sobre-text h2 {
    font-size: 28px;
  }

  .sobre-section .sobre-text p {
    font-size: 15px;
  }

  .sobre-section .sobre-img img {
    max-width: 100%;
  }
}

/* === SEÇÃO IDENTIDADE VISUAL === */
.identidade-section {
  display: flex;
  align-items: center;
  justify-content: space-evenly !important;
  background-color: #fff;
  color: #000;
  position: relative;
  min-height: 480px;
  padding: 0 80px;
}

/* Imagem à esquerda */
.identidade-section .identidade-img {
  display: flex;
  justify-content: flex-start; /* imagem à esquerda */
}

.identidade-section .identidade-img img {
  width: 350px;
  height: auto;
  display: block;
}

/* Texto à direita */
.identidade-section .identidade-text {
  max-width: 480px;
  text-align: right; /* conteúdo alinhado à direita */
}

.identidade-section .identidade-text h2 {
  font-size: 36px;
  font-weight: 300;
  letter-spacing: 1.5px;
  margin-bottom: 16px;
  text-transform: uppercase;
}

.identidade-section .identidade-text p {
  font-size: 16px;
  font-weight: 300;
  line-height: 1.7;
  color: #333;
  margin-bottom: 25px;
}

/* === Responsivo === */
@media (max-width: 900px) {
  .identidade-section {
    flex-direction: column;
    text-align: center;
    gap: 40px;
    padding: 60px 30px;
  }

  .identidade-section .identidade-text {
    max-width: 100%;
    text-align: center; /* centraliza no mobile */
  }

  .identidade-section .identidade-text h2 {
    font-size: 28px;
  }

  .identidade-section .identidade-text p {
    font-size: 15px;
  }

  .identidade-section .identidade-img {
    justify-content: center;
  }

  .identidade-section .identidade-img img {
    max-width: 180px;
  }
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

<!-- Nova Navbar com Ícones Finos -->
<nav class="navbar-container">
  <!-- Logo à esquerda -->
  <div class="navbar-logo">
    <img src="imgs/yaraletra.png" alt="YARA Logo">
  </div>
  
  <!-- Menu centralizado -->
  <ul class="navbar-menu">
    <li><a href="index.php">INÍCIO</a></li>
    
     <li class="menu-item">
      <a href="sobre.php" class="sobre-link">SOBRE</a>
      <div class="dropdown sobre-dropdown">
        <div>
          <h4>Informações</h4>
          <a href="servicos.php">Serviços</a>
          <a href="contato.php">Contato</a>
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

    
    <div class="nav-icon cart-icon" id="carrinho">
      <i class="fas fa-shopping-bag"></i>
      <span class="cart-count"><?php echo isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0; ?></span>
    </div>
  </div>
</nav>

<section class="sobre-section">
  <div class="sobre-text">
    <h2>SOBRE NÓS</h2>
    <p>
      A YARA nasce da fusão entre intensidade e delicadeza, criando joias que ultrapassam o adorno e se tornam expressão. Cada criação carrega identidade, presença e significado, revelando histórias que dialogam com quem as usa.
    </p>
  </div>
  <div class="sobre-img">
    <img src="../paginas principais/imgs/sobre.png" alt="Tigre YARA">
  </div>
</section>

<section class="identidade-section">
  <div class="identidade-img">
    <img src="../paginas principais/imgs/logo.png" alt="Logo YARA Rosa">
    <div class="color-bubbles">
    <span style="--color: #000000"></span>
    <span style="--color: #F33283"></span>
    <span style="--color: #FF80B5"></span>
    <span style="--color: #FFF4FB"></span>
    <span style="--color: #FFFFFF"></span>
  </div>
  </div>
  <div class="identidade-text">
    <h2>IDENTIDADE VISUAL</h2>
    <p>
      O tigre é a presença, coragem e intensidade que coexistem com a delicadeza. A cor rosa traduz a essência contemporânea da marca, unindo ousadia, modernidade e singularidade em equilíbrio entre minimalismo e maximalismo. O logotipo, de traço sólido e refinado, reforça a autenticidade e distinção da joalheria, consolidando uma identidade única que se destaca em qualquer contexto.
    </p>
  </div>
</section>

<!-- === Seção Newsletter === -->
<section class="newsletter-section">
  <div class="newsletter-container">
    <div class="newsletter-logo">
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
    // --- BARRA DE PESQUISA ---
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

    // --- MENU DO USUÁRIO ---
    const userIcon = document.querySelector('.user-icon');
    const userDropdown = document.querySelector('.user-dropdown');
    const sairConta = document.getElementById('sairConta');

    if (userIcon && userDropdown) {
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function() {
            userDropdown.style.display = 'none';
        });
    }

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
                    window.location.reload();
                }
            });
        });
    }

    // Login e Cadastro
    const openLoginMenu = document.getElementById('openLoginMenu');
    const openSignupMenu = document.getElementById('openSignupMenu');

    if (openLoginMenu) {
        openLoginMenu.addEventListener('click', function(e) {
            e.preventDefault();
            // Abrir modal de login (implementar conforme necessário)
            console.log('Abrir modal de login');
        });
    }

    if (openSignupMenu) {
        openSignupMenu.addEventListener('click', function(e) {
            e.preventDefault();
            // Abrir modal de cadastro (implementar conforme necessário)
            console.log('Abrir modal de cadastro');
        });
    }

    // Debug: Verificar se o ícone do carrinho está presente
    console.log('Ícone do carrinho:', document.querySelector('.cart-icon'));
});
// === Dropdown do Usuário - Funcional ===
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
</script>
</body>
</html>
