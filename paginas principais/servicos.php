<?php
// servicos.php
session_start();
require_once 'funcoes.php';

// Verificar carrinho
$carrinho_count = isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0;

// Obter inicial do usuário se estiver logado
$inicial_usuario = '';
if (isset($_SESSION['usuario']) && $_SESSION['usuario']) {
    $inicial_usuario = substr($_SESSION['usuario']['nome'], 0, 1);
}

// Lista de países (mesmo do index.php)
$paises = [
    'Brasil',
    'Afeganistão',
    'África do Sul',
    'Albânia',
    'Alemanha',
    'Andorra',
    'Angola',
    'Antígua e Barbuda',
    'Arábia Saudita',
    'Argélia',
    'Argentina',
    'Armênia',
    'Austrália',
    'Áustria',
    'Azerbaijão',
    'Bahamas',
    'Bangladesh',
    'Barbados',
    'Barein',
    'Bélgica',
    'Belize',
    'Benin',
    'Bielorrússia',
    'Bolívia',
    'Bósnia e Herzegovina',
    'Botsuana',
    'Brunei',
    'Bulgária',
    'Burkina Faso',
    'Burundi',
    'Butão',
    'Cabo Verde',
    'Camarões',
    'Camboja',
    'Canadá',
    'Catar',
    'Cazaquistão',
    'Chade',
    'Chile',
    'China',
    'Chipre',
    'Colômbia',
    'Comores',
    'Congo',
    'Coreia do Norte',
    'Coreia do Sul',
    'Costa do Marfim',
    'Costa Rica',
    'Croácia',
    'Cuba',
    'Dinamarca',
    'Djibuti',
    'Dominica',
    'Egito',
    'El Salvador',
    'Emirados Árabes Unidos',
    'Equador',
    'Eritreia',
    'Eslováquia',
    'Eslovênia',
    'Espanha',
    'Estados Unidos',
    'Estônia',
    'Eswatini',
    'Etiópia',
    'Fiji',
    'Filipinas',
    'Finlândia',
    'França',
    'Gabão',
    'Gâmbia',
    'Gana',
    'Geórgia',
    'Granada',
    'Grécia',
    'Guatemala',
    'Guiana',
    'Guiné',
    'Guiné Equatorial',
    'Guiné-Bissau',
    'Haiti',
    'Honduras',
    'Hungria',
    'Iêmen',
    'Ilhas Marshall',
    'Ilhas Salomão',
    'Índia',
    'Indonésia',
    'Irã',
    'Iraque',
    'Irlanda',
    'Islândia',
    'Israel',
    'Itália',
    'Jamaica',
    'Japão',
    'Jordânia',
    'Kiribati',
    'Kuwait',
    'Laos',
    'Lesoto',
    'Letônia',
    'Líbano',
    'Libéria',
    'Líbia',
    'Liechtenstein',
    'Lituânia',
    'Luxemburgo',
    'Macedônia do Norte',
    'Madagascar',
    'Malásia',
    'Malaui',
    'Maldivas',
    'Mali',
    'Malta',
    'Marrocos',
    'Maurícia',
    'Mauritânia',
    'México',
    'Mianmar',
    'Micronésia',
    'Moçambique',
    'Moldávia',
    'Mônaco',
    'Mongólia',
    'Montenegro',
    'Namíbia',
    'Nauru',
    'Nepal',
    'Nicarágua',
    'Níger',
    'Nigéria',
    'Noruega',
    'Nova Zelândia',
    'Omã',
    'Países Baixos',
    'Palau',
    'Panamá',
    'Papua-Nova Guiné',
    'Paquistão',
    'Paraguai',
    'Peru',
    'Polônia',
    'Portugal',
    'Quênia',
    'Quirguistão',
    'Reino Unido',
    'República Centro-Africana',
    'República Checa',
    'República Democrática do Congo',
    'República Dominicana',
    'Romênia',
    'Ruanda',
    'Rússia',
    'Samoa',
    'San Marino',
    'Santa Lúcia',
    'São Cristóvão e Névis',
    'São Tomé e Príncipe',
    'São Vicente e Granadinas',
    'Seicheles',
    'Senegal',
    'Serra Leoa',
    'Sérvia',
    'Singapura',
    'Síria',
    'Somália',
    'Sri Lanka',
    'Sudão',
    'Sudão do Sul',
    'Suécia',
    'Suíça',
    'Suriname',
    'Tailândia',
    'Taiwan',
    'Tajiquistão',
    'Tanzânia',
    'Timor-Leste',
    'Togo',
    'Tonga',
    'Trinidad e Tobago',
    'Tunísia',
    'Turcomenistão',
    'Turquia',
    'Tuvalu',
    'Ucrânia',
    'Uganda',
    'Uruguai',
    'Uzbequistão',
    'Vanuatu',
    'Vaticano',
    'Venezuela',
    'Vietnã',
    'Zâmbia',
    'Zimbábue'
];
sort($paises); // Ordenar alfabeticamente
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>YARA - Serviços</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  /* === ESTILOS DA NAVBAR ATUALIZADA (MESMO DO INDEX.PHP) === */
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
    text-decoration: none; 
    cursor: pointer; 
  }

  /* Avatar placeholder para usuário logado */
  .avatar-placeholder {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e91e63;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
    cursor: pointer;
  }

  /* === ESTILOS DA SEÇÃO SERVIÇOS === */
  .services-section {
    background: #fff;
    padding: 80px 20px 60px;
    max-width: 1200px;
    margin: 0 auto;
  }

  .services-intro {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 60px;
    flex-wrap: wrap;
    margin-bottom: 80px;
  }

  .services-intro-image {
    flex: 1;
    min-width: 300px;
    max-width: 450px;
  }

  .services-intro-image img {
    width: 100%;
    height: auto;
    display: block;
  }

  .services-intro-text {
    flex: 1;
    min-width: 300px;
    max-width: 550px;
  }

  .services-intro-text h2 {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 500;
    font-size: 28px;
    letter-spacing: 0.8px;
    margin-bottom: 24px;
    text-transform: uppercase;
    color: #222;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
  }

  .services-intro-text p {
    font-family: 'Open Sans', sans-serif;
    font-size: 16px;
    color: #444;
    line-height: 1.8;
    margin-bottom: 25px;
  }

  /* Grid de serviços */
  .services-grid {
    display: flex;
    justify-content: center;
    align-items: stretch;
    text-align: center;
    width: 100%;
    gap: 40px;
    flex-wrap: wrap;
    margin: 0 auto 80px;
  }

  .service-item {
    flex: 1;
    min-width: 300px;
    max-width: 350px;
    padding: 40px 30px;
    border: 1px solid #f0f0f0;
    border-radius: 8px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .service-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.08);
    border-color: #e91e63;
  }

  .service-item img {
    width: 70px;
    height: 70px;
    margin-bottom: 25px;
    object-fit: contain;
  }

  .service-item h3 {
    font-family: 'Open Sans', sans-serif;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #222;
  }

  .service-item p {
    font-size: 16px;
    color: #666;
    line-height: 1.7;
  }

  /* === SEÇÃO CHAT COM ESPECIALISTA === */
  .chat-specialist-section {
    background: linear-gradient(135deg, #f9f9f9 0%, #f0f0f0 100%);
    padding: 80px 20px;
    text-align: center;
    margin: 40px auto;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
  }

  .chat-specialist-container {
    max-width: 800px;
    margin: 0 auto;
  }

  .chat-specialist-container h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 32px;
    font-weight: 500;
    margin-bottom: 20px;
    color: #222;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .chat-specialist-container p {
    font-family: 'Open Sans', sans-serif;
    font-size: 18px;
    color: #555;
    line-height: 1.7;
    margin-bottom: 35px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
  }

  .chat-specialist-btn {
    display: inline-block;
    background-color: #e91e63;
    color: white;
    border: none;
    padding: 16px 40px;
    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    margin-top: 20px;
  }

  .chat-specialist-btn:hover {
    background-color: #c2185b;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(233, 30, 99, 0.2);
  }

  .chat-specialist-btn i {
    margin-right: 10px;
    font-size: 18px;
  }

  /* === Seção Newsletter (centralizada) === */
  .newsletter-section {
    background: #fff;
    min-height: 50vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .newsletter-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 80px;
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
    max-width: 1200px;
    margin: 0 auto;
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
    color: #fe7db9;
  }

  .social {
    display: flex;
    gap: 15px;
    margin-top: 20px;
  }

  .social a {
    color: white;
    font-size: 18px;
    transition: color 0.3s;
  }

  .social a:hover {
    color: #fe7db9;
  }

  .footer-bottom {
    text-align: center;
    border-top: 1px solid #fe7db9;
    margin-top: 20px;
    padding-top: 10px;
    font-size: 14px;
    color: #fe7db9;
    max-width: 1200px;
    margin: 20px auto 0;
  }

  /* === MODAIS (MESMO DO INDEX.PHP) === */
  .contact-overlay, .login-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.7);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 24px;
    overflow-y: auto;
  }

  .contact-modal, .login-modal {
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
  .close-x {
    position: absolute;
    right: 12px;
    top: 10px;
    background: transparent;
    border: none;
    color: #fe7db9;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
  }

  /* logo tigre centralizado no topo do modal */
  .modal-logo {
    display: block;
    margin: 6px auto 10px;
    height: 30px;
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
    transition: all 0.3s ease;
  }

  .btn-outline:hover {
    background: #fe7db9;
    color: #fff;
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
    transition: background 0.3s ease;
  }

  .btn-primary:hover {
    background: #e91e63;
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

  /* Responsividade geral */
  @media (max-width: 900px) {
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

    .services-intro {
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .services-intro-text {
      text-align: center;
    }
  }

  @media (max-width: 740px) {
    .contact-modal { 
      padding: 20px 18px; 
    }
    .contact-grid { 
      grid-template-columns: 1fr; 
      gap:14px; 
    }
    .contact-modal select { 
      width: 100%; 
    }
    
    .chat-specialist-container h2 {
      font-size: 26px;
    }
    
    .chat-specialist-container p {
      font-size: 16px;
    }
  }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>
<!-- === SEÇÃO SERVIÇOS === -->
<section class="services-section">
  <div class="services-intro">
    <div class="services-intro-image">
      <img src="imgs/destaque-servico.png" alt="Joia destaque">
    </div>
    <div class="services-intro-text">
      <h2>NOSSOS SERVIÇOS</h2>
      <p>
        Na YARA, cada serviço é parte da arte da joalheria. Seja para ajustes, manutenção, personalização ou cuidados especiais,
        estamos comprometidos em cultivar um relacionamento de confiança que se fortalece com o tempo, acompanhando sua joia em cada capítulo da sua história.
      </p>
      <p>
        Nossos especialistas estão sempre à disposição para oferecer consultoria personalizada, garantindo que cada peça seja tratada com o cuidado e a expertise que merece.
      </p>
    </div>
  </div>

  <div class="services-grid">
    <div class="service-item">
      <img src="imgs/diamante-icon.png" alt="Ícone Cuidados">
      <h3>Cuidados e Reparos</h3>
      <p>Exclusividade para preservar o brilho e a durabilidade das suas peças. Oferecemos limpeza profissional, polimento, ajustes de tamanho e reparos especializados.</p>
    </div>

    <div class="service-item">
      <img src="imgs/interrogacao.png" alt="Ícone FAQ">
      <h3>FAQ e Suporte</h3>
      <p>Tire suas dúvidas sobre pedidos, prazos, serviços e políticas de atendimento. Nossa equipe está pronta para esclarecer todas as suas questões.</p>
    </div>

    <div class="service-item">
      <img src="imgs/lapis-icon.png" alt="Ícone Personalize">
      <h3>Personalize sua Criação</h3>
      <p>Transforme sua joia em algo ainda mais único, feito sob medida para refletir sua identidade. Gravações, pedras especiais e designs exclusivos.</p>
    </div>
  </div>

  <!-- === NOVA SEÇÃO: CHAT COM ESPECIALISTA === -->
  <div class="chat-specialist-section">
    <div class="chat-specialist-container">
      <h2>Precisa de Ajuda Especializada?</h2>
      <p>
        Converse diretamente com nossos especialistas em joalheria. Tire dúvidas sobre nossos serviços, 
        obtenha orientação personalizada para cuidados com suas joias, ou discuta ideias para personalização. 
        Nossa equipe está aqui para ajudar você a tomar as melhores decisões para suas peças especiais.
      </p>
      <p style="font-style: italic; color: #777; margin-bottom: 30px;">
        Atendimento disponível de segunda a sexta, das 10h às 19h.
      </p>
      <a href="chat.php" class="chat-specialist-btn">
        <i class="fas fa-comments"></i> Falar com Especialista
      </a>
    </div>
  </div>
</section>

<!-- === SEÇÃO NEWSLETTER === -->
<section class="newsletter-section">
  <div class="newsletter-container">
    <div class="newsletter-logo">
      <img src="imgs/logo.png" alt="Logo YARA">
    </div>
    <div class="newsletter-content">
      <h2>Descubra primeiro todas as novidades <br>
        da Yara. Cadastre-se!</h2>
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

<!-- === RODAPÉ === -->
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

<!-- === MODAIS (MESMO DO INDEX.PHP) === -->

<!-- Modal de Contato -->
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
        <?php foreach ($paises as $pais): ?>
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

<script>
// === Funções JavaScript principais (MESMO DO INDEX.PHP) ===
document.addEventListener('DOMContentLoaded', function() {

  // --- ABRIR MODAIS DE LOGIN, CADASTRO E CONTATO ---
  const openLoginMenu = document.getElementById('openLoginMenu');
  const openSignupMenu = document.getElementById('openSignupMenu');
  const openContactDropdown = document.getElementById('openContactDropdown');

  const loginOverlay = document.getElementById('loginOverlay');
  const signupOverlay = document.getElementById('signupOverlay');
  const contactOverlay = document.getElementById('contactOverlay');

  // Abrir modal de login
  function openLoginModal() {
    if (loginOverlay) {
      loginOverlay.style.display = 'flex';
      loginOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
  }

  // Abrir modal de cadastro
  function openSignupModal() {
    if (signupOverlay) {
      signupOverlay.style.display = 'flex';
      signupOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
  }

  // Abrir modal de contato
  function openContactModal() {
    if (contactOverlay) {
      contactOverlay.style.display = 'flex';
      contactOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';

      const sel = document.getElementById('locationSelect');
      if (sel) sel.focus();
    }
  }

  // Fechar modais
  function closeModals() {
    if (loginOverlay) {
      loginOverlay.style.display = 'none';
      loginOverlay.setAttribute('aria-hidden', 'true');
    }
    if (signupOverlay) {
      signupOverlay.style.display = 'none';
      signupOverlay.setAttribute('aria-hidden', 'true');
    }
    if (contactOverlay) {
      contactOverlay.style.display = 'none';
      contactOverlay.setAttribute('aria-hidden', 'true');
    }
    document.body.style.overflow = '';
  }

  // Botões para abrir modais
  if (openLoginMenu) {
    openLoginMenu.addEventListener('click', function(e) {
      e.preventDefault();
      openLoginModal();
    });
  }

  if (openSignupMenu) {
    openSignupMenu.addEventListener('click', function(e) {
      e.preventDefault();
      openSignupModal();
    });
  }

  if (openContactDropdown) {
    openContactDropdown.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      openContactModal();
    });
  }

  // --- VERIFICAR LOGIN PARA FAVORITOS E CARRINHO ---
  const heartIcon = document.getElementById('heartIcon');
  const cartIcon = document.getElementById('carrinho');

  function usuarioEstaLogado() {
    return <?php echo (isset($_SESSION['usuario']) && $_SESSION['usuario'] !== null) ? 'true' : 'false'; ?>;
  }

  if (heartIcon) {
    heartIcon.addEventListener('click', function(e) {
      if (!usuarioEstaLogado()) {
        e.preventDefault();
        e.stopPropagation();
        openLoginModal();
        return false;
      }
    });
  }

  if (cartIcon) {
    cartIcon.addEventListener('click', function(e) {
      if (!usuarioEstaLogado()) {
        e.preventDefault();
        e.stopPropagation();
        openLoginModal();
        return false;
      }
    });
  }

  // --- FECHAR MODAIS ---
  const closeLoginX = document.getElementById('closeLoginX');
  const closeSignupX = document.getElementById('closeSignupX');
  const closeX = document.getElementById('closeX');
  const closeModalBtn = document.getElementById('closeModalBtn');

  if (closeLoginX) closeLoginX.addEventListener('click', closeModals);
  if (closeSignupX) closeSignupX.addEventListener('click', closeModals);
  if (closeX) closeX.addEventListener('click', closeModals);
  if (closeModalBtn) closeModalBtn.addEventListener('click', closeModals);

  if (loginOverlay) {
    loginOverlay.addEventListener('click', function(e) {
      if (e.target === loginOverlay) closeModals();
    });
  }

  if (signupOverlay) {
    signupOverlay.addEventListener('click', function(e) {
      if (e.target === signupOverlay) closeModals();
    });
  }

  if (contactOverlay) {
    contactOverlay.addEventListener('click', function(e) {
      if (e.target === contactOverlay) closeModals();
    });
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModals();
  });

  // --- NAVEGAÇÃO ENTRE LOGIN E CADASTRO ---
  const linkCadastro = document.querySelector('#loginOverlay .link-cadastro');
  const goToLogin = document.getElementById('goToLogin');

  if (linkCadastro) {
    linkCadastro.addEventListener('click', function(e) {
      e.preventDefault();
      closeModals();
      openSignupModal();
    });
  }

  if (goToLogin) {
    goToLogin.addEventListener('click', function(e) {
      e.preventDefault();
      closeModals();
      openLoginModal();
    });
  }

  // --- NEWSLETTER - ABRIR MODAL DE LOGIN AO ENVIAR EMAIL ---
  const newsletterForm = document.getElementById('newsletterForm');
  const newsletterCheckbox = document.querySelector('.newsletter-section .checkbox input');

  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();

      // Verificar se o checkbox foi marcado
      if (!newsletterCheckbox.checked) {
        alert("Você precisa concordar com a Política de Privacidade para continuar.");
        return;
      }

      // Verificar se o email foi preenchido
      const emailInput = this.querySelector('input[type="email"]');
      if (!emailInput.value.trim()) {
        alert("Por favor, digite seu email.");
        emailInput.focus();
        return;
      }

      // Se o usuário não estiver logado, abrir modal de login
      if (!usuarioEstaLogado()) {
        closeModals(); // Fechar qualquer modal aberto
        openLoginModal();

        // Opcional: Preencher automaticamente o email no formulário de login
        const loginEmailInput = document.querySelector('#formLogin input[type="email"]');
        if (loginEmailInput) {
          loginEmailInput.value = emailInput.value;
        }
      } else {
        // Se já estiver logado, enviar o formulário normalmente
        const formData = new FormData(this);
        formData.append('acao', 'newsletter');

        fetch('processa_form.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              mostrarMensagem('Email cadastrado na newsletter com sucesso!', 'sucesso');
              this.reset();
            } else {
              mostrarMensagem(data.message || 'Erro ao cadastrar newsletter!', 'erro');
            }
          })
          .catch(error => {
            console.error('Erro:', error);
            mostrarMensagem('Erro ao processar newsletter!', 'erro');
          });
      }
    });
  }

  // --- PROCESSAMENTO LOGIN & CADASTRO ---
  const formLogin = document.getElementById('formLogin');
  const formCadastro = document.getElementById('formCadastro');

  function mostrarMensagem(mensagem, tipo) {
    let mensagemEl = document.getElementById('mensagemFeedback');
    if (!mensagemEl) {
      mensagemEl = document.createElement('div');
      mensagemEl.id = 'mensagemFeedback';
      mensagemEl.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            z-index: 10000;
            font-weight: 500;
            max-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
      document.body.appendChild(mensagemEl);
    }

    mensagemEl.textContent = mensagem;
    mensagemEl.style.backgroundColor = tipo === 'sucesso' ? '#4CAF50' : '#f44336';
    mensagemEl.style.display = 'block';

    setTimeout(() => {
      mensagemEl.style.display = 'none';
    }, 5000);
  }

  // Processar login
  if (formLogin) {
    formLogin.addEventListener('submit', function(e) {
      e.preventDefault();

      const email = this.querySelector('input[type="email"]').value;
      const senha = this.querySelector('input[type="password"]').value;

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
              if (data.redirect) {
                window.location.href = data.redirect;
              } else {
                window.location.reload();
              }
            }, 1500);
          } else {
            mostrarMensagem(data.message, 'erro');
          }
        })
        .catch(error => {
          console.error('Erro:', error);
          mostrarMensagem('Erro de conexão com o servidor. Verifique o console.', 'erro');
        });
    });
  }

  // Cadastro
  if (formCadastro) {
    formCadastro.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      formData.append('acao', 'cadastro');

      fetch('processa_form.php', {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            mostrarMensagem('Cadastro realizado com sucesso!', 'sucesso');
            setTimeout(() => window.location.reload(), 1500);
          } else {
            mostrarMensagem(data.message || 'Erro ao cadastrar!', 'erro');
          }
        })
        .catch(() => mostrarMensagem('Erro ao processar cadastro!', 'erro'));
    });
  }

  // === BARRA DE PESQUISA ===
  const inputPesquisa = document.getElementById('inputPesquisa');
  const resultadosPesquisa = document.getElementById('resultadosPesquisa');
  const searchIconBtn = document.querySelector('.search-icon-btn');

  function buscarProdutos(termo) {
    if (termo.length > 2) {
      fetch('buscar_produtos.php?termo=' + encodeURIComponent(termo))
        .then(r => r.json())
        .then(data => {
          resultadosPesquisa.innerHTML = '';

          if (data.success && data.produtos.length > 0) {
            data.produtos.forEach(produto => {
              const item = document.createElement('div');
              item.className = 'resultado-item';

              const imagemSrc = produto.imagem && produto.imagem !== '' ?
                produto.imagem :
                'imgs/produto-padrao.png';

              item.innerHTML = `
                            <img src="${imagemSrc}" alt="${produto.nome}" onerror="this.src='imgs/produto-padrao.png'">
                            <div class="resultado-info">
                                <h4>${produto.nome}</h4>
                                <div class="preco">R$ ${parseFloat(produto.preco).toFixed(2)}</div>
                            </div>
                        `;

              item.addEventListener('click', () => {
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
        })
        .catch(() => {
          resultadosPesquisa.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: #e74c3c;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 20px; margin-bottom: 8px;"></i>
                        <p style="font-size: 12px; margin: 0;">Erro ao buscar produtos</p>
                    </div>
                `;
          resultadosPesquisa.classList.add('mostrar');
        });
    } else {
      resultadosPesquisa.classList.remove('mostrar');
      resultadosPesquisa.innerHTML = '';
    }
  }

  if (inputPesquisa) {
    inputPesquisa.addEventListener('input', function() {
      buscarProdutos(this.value.trim());
    });

    document.addEventListener('click', function(e) {
      if (!inputPesquisa.contains(e.target) &&
        !resultadosPesquisa.contains(e.target) &&
        !searchIconBtn.contains(e.target)) {

        resultadosPesquisa.classList.remove('mostrar');
      }
    });

    inputPesquisa.addEventListener('keypress', function(e) {
      if (e.key === 'Enter' && this.value.trim() !== '') {
        window.location.href = `produtos.php?busca=${encodeURIComponent(this.value.trim())}`;
      }
    });
  }

  if (searchIconBtn) {
    searchIconBtn.addEventListener('click', function() {
      const termo = inputPesquisa.value.trim();
      if (termo !== '') {
        window.location.href = `produtos.php?busca=${encodeURIComponent(termo)}`;
      } else {
        inputPesquisa.focus();
      }
    });
  }

  // --- DROPDOWN DO USUÁRIO ---
  const userIcon = document.querySelector('.user-icon');
  const userDropdown = document.querySelector('.user-dropdown');
  const sairConta = document.getElementById('sairConta');

  if (userIcon && userDropdown) {
    userIcon.addEventListener('click', function(e) {
      e.stopPropagation();
      userDropdown.classList.toggle('show');
    });

    document.addEventListener('click', function() {
      userDropdown.classList.remove('show');
    });

    userDropdown.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  }

  // Logout
  if (sairConta) {
    sairConta.addEventListener('click', function(e) {
      e.preventDefault();

      fetch('processa_form.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'acao=logout'
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            mostrarMensagem('Logout realizado com sucesso!', 'sucesso');
            setTimeout(() => window.location.reload(), 1500);
          }
        })
        .catch(() => mostrarMensagem('Erro ao fazer logout!', 'erro'));
    });
  }
});

// === Funções de contato ATUALIZADAS ===
function iniciarChat() {
  // Redireciona diretamente para a página de chat
  window.location.href = 'chat.php';
}

function iniciarChatEspecialista() {
  // Redireciona para a página de chat com parâmetro de especialista
  window.location.href = 'chat.php?tipo=especialista';
}

function abrirWhatsApp() {
  const numero = '5511999999999';
  const mensagem = 'Olá, gostaria de mais informações sobre as joias YARA.';
  const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagem)}`;
  window.open(url, '_blank');
}

function agendarVisita() {
  window.location.href = 'chat.php';
}
</script>

<!-- Botão de Admin (se for admin) -->
<?php if ((isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) || (isset($_SESSION['usuario']['is_admin']) && $_SESSION['usuario']['is_admin'] == 1)): ?>
  <style>
    .admin-float-btn {
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 99999;
      background-color: #2c3e50;
      color: #ffffff !important;
      padding: 12px 25px;
      border-radius: 50px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
      text-decoration: none;
      font-family: sans-serif;
      font-weight: bold;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: all 0.3s ease;
      border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .admin-float-btn:hover {
      background-color: #e91e7d;
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(233, 30, 125, 0.5);
      border-color: #fff;
    }
  </style>

  <a href="admin/dashboard.php" class="admin-float-btn" title="Apenas visível para Administradores">
    <i class="fas fa-user-shield"></i>
    Voltar ao Painel Admin
  </a>
<?php endif; ?>

</body>
</html>