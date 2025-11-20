<?php
// produtos.php
require_once 'funcoes.php';

// Conectar ao banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=yara_joias;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Processar filtros e ordenação
$categoria_filtro = $_GET['categoria'] ?? '';
$termo_busca = $_GET['busca'] ?? '';
$ordenar = $_GET['ordenar'] ?? 'relevancia';
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$limite = 12; // Produtos por página

// Construir query base
$sql = "SELECT * FROM produtos WHERE disponivel = 1";
$params = [];

// Aplicar filtros
if (!empty($categoria_filtro)) {
    $sql .= " AND categoria = ?";
    $params[] = $categoria_filtro;
}

if (!empty($termo_busca)) {
    $sql .= " AND (nome LIKE ? OR descricao LIKE ?)";
    $params[] = "%$termo_busca%";
    $params[] = "%$termo_busca%";
}

// Aplicar ordenação
switch ($ordenar) {
    case 'preco-crescente':
        $sql .= " ORDER BY preco ASC";
        break;
    case 'preco-decrescente':
        $sql .= " ORDER BY preco DESC";
        break;
    case 'nome':
        $sql .= " ORDER BY nome ASC";
        break;
    case 'novidades':
        $sql .= " ORDER BY data_cadastro DESC";
        break;
    default:
        $sql .= " ORDER BY destaque DESC, data_cadastro DESC";
        break;
}

// Contar total de produtos para paginação
$sql_count = "SELECT COUNT(*) as total FROM produtos WHERE disponivel = 1";
$count_params = [];

if (!empty($categoria_filtro)) {
    $sql_count .= " AND categoria = ?";
    $count_params[] = $categoria_filtro;
}

if (!empty($termo_busca)) {
    $sql_count .= " AND (nome LIKE ? OR descricao LIKE ?)";
    $count_params[] = "%$termo_busca%";
    $count_params[] = "%$termo_busca%";
}

$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($count_params);
$total_produtos = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
$total_paginas = ceil($total_produtos / $limite);

// Aplicar paginação - CORREÇÃO AQUI
$offset = ($pagina - 1) * $limite;
$sql .= " LIMIT :limite OFFSET :offset";

// Buscar produtos
$stmt = $pdo->prepare($sql);

// Vincular parâmetros
$param_index = 1;
foreach ($params as $param) {
    $stmt->bindValue($param_index, $param);
    $param_index++;
}

// Vincular LIMIT e OFFSET como inteiros - CORREÇÃO IMPORTANTE
$stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lista de países para o modal de contato
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
sort($paises);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>YARA - Nossas Joias</title>
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

    header{
      width:100%;
      background: var(--bg);
      padding: 14px 20px 6px;
      box-sizing: border-box;
    }

    .container{
      max-width: var(--container-w);
      margin: 0 auto;
    }

    .top-row{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap: 10px;
    }

    .top-left{
      display:flex;
      gap: 14px;
      align-items:center;
    }

    .top-left a{
      text-decoration:none;
      color:var(--text);
      font-size:12px;
      letter-spacing:0.5px;
      position:relative;
      padding-bottom:4px;
      cursor:pointer;
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
      width: var(--icon-size);
      height: var(--icon-size);
      display:block;
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

    /* === SEÇÃO DE PRODUTOS COM FILTROS === */
    .produtos-section {
      padding: 40px 20px;
      background-color: #fff;
    }

    .produtos-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .produtos-header h1 {
      font-size: 28px;
      color: #333;
      font-weight: 300;
      letter-spacing: 1.5px;
    }

    .ordenar {
      display: flex;
      align-items: center;
    }

    .ordenar label {
      margin-right: 10px;
      font-size: 14px;
    }

    .ordenar select {
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      background-color: white;
      font-size: 14px;
    }

    .produtos-container {
      display: flex;
      gap: 30px;
    }

    /* === FILTROS === */
    .filtros {
      width: 250px;
      background: #ffe7f6;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      height: fit-content;
    }

    .filtro-grupo {
      margin-bottom: 25px;
      border-bottom: 1px solid #eee;
      padding-bottom: 20px;
    }

    .filtro-grupo:last-child {
      border-bottom: none;
    }

    .filtro-grupo h3 {
      margin-bottom: 15px;
      font-size: 16px;
      color: #333;
      font-weight: 600;
    }

    .filtro-opcoes {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .filtro-opcao {
      display: flex;
      align-items: center;
    }

    .filtro-opcao input {
      margin-right: 8px;
    }

    .filtro-opcao label {
      font-size: 14px;
      cursor: pointer;
    }

    .preco-inputs {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }

    .preco-inputs input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
    }

    .aplicar-filtros {
      background-color: #fe7db9;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      font-weight: 500;
      transition: background-color 0.3s;
    }

    .aplicar-filtros:hover {
      background-color: #f06ca2;
    }

    /* === GRADE DE PRODUTOS === */
    .produtos-grade {
      flex: 1;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 25px;
    }

    .produto-card {
      background: #ffffff;
      border-radius: 5px;
      border: #0000001a solid 1px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      transition: transform 0.3s, box-shadow 0.3s;
      position: relative;
    }

    .produto-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .produto-img-container {
      position: relative;
      overflow: hidden;
      height: 250px;
    }

    .produto-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s;
    }

    .produto-card:hover .produto-img {
      transform: scale(1.05);
    }

    .favorito {
      position: absolute;
      top: 10px;
      right: 10px;
      width: 24px;
      height: 24px;
      cursor: pointer;
      transition: transform 0.3s;
      z-index: 10;
    }

    .favorito:hover {
      transform: scale(1.2);
    }

    .favorito.ativo {
      filter: brightness(0) saturate(100%) invert(27%) sepia(85%) saturate(3107%) hue-rotate(310deg) brightness(95%) contrast(101%);
    }

    .produto-info {
      padding: 15px;
    }

    .produto-info h3 {
      margin-bottom: 10px;
      font-size: 16px;
      color: #333;
      font-weight: 500;
    }

    .produto-preco {
      font-weight: bold;
      color: #fe7db9;
      margin-bottom: 15px;
      font-size: 18px;
    }

    .produto-info button {
      background-color: #fe7db9;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      font-weight: 500;
      transition: background-color 0.3s;
    }

    .produto-info button:hover {
      background-color: #f06ca2;
    }

    /* === PAGINAÇÃO === */
    .paginacao {
      display: flex;
      justify-content: center;
      margin-top: 40px;
      gap: 10px;
    }

    .paginacao button {
      padding: 8px 15px;
      border: 1px solid #ddd;
      background: white;
      border-radius: 5px;
      cursor: pointer;
      transition: all 0.3s;
    }

    .paginacao button:hover {
      background: #f5f5f5;
    }

    .paginacao button.ativo {
      background: #fe7db9;
      color: white;
      border-color: #fe7db9;
    }

    /* === NEWSLETTER === */
    .newsletter-section {
      background: #fff;
      min-height: 50vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 50px;
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

    .newsletter-logo img {
      max-width: 200px;
      height: auto;
      display: block;
    }

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

    /* === FOOTER === */
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

    /* === MENSAGENS === */
    .mensagem-curtida {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      z-index: 1000;
      text-align: center;
    }

    .mensagem-curtida p {
      margin-bottom: 15px;
    }

    .mensagem-curtida button {
      background-color: #fe7db9;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
    }

    /* === MODAIS === */
    .contact-overlay, .login-overlay {
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

    .modal-logo {
      display: block;
      margin: 6px auto 10px;
      height: 30px;
      object-fit: contain;
    }

    /* === RESPONSIVIDADE === */
    @media (max-width: 900px) {
      .produtos-container {
        flex-direction: column;
      }
      
      .filtros {
        width: 100%;
        margin-bottom: 30px;
      }
      
      .produtos-grade {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
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
    }

    @media (max-width: 600px) {
      .produtos-grade {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      }
      
      .menu {
        flex-wrap: wrap;
      }
      
      .menu a {
        margin: 5px 10px;
      }
      
      .newsletter-form {
        flex-direction: column;
      }
      
      .newsletter-form input {
        border-radius: 5px;
        margin-bottom: 10px;
      }
      
      .newsletter-form button {
        border-radius: 5px;
        padding: 12px;
      }
    }

    /* Estilos para usuário logado */
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

    /* Barra de pesquisa */
    .barra-pesquisa {
      position: absolute;
      top: 100%;
      right: 0;
      background: white;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      display: none;
      z-index: 1000;
    }

    .barra-pesquisa.ativa {
      display: block;
    }

    .barra-pesquisa input {
      width: 300px;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }

    .resultados-pesquisa {
      max-height: 300px;
      overflow-y: auto;
      margin-top: 10px;
    }

    .resultado-item {
      display: flex;
      align-items: center;
      padding: 10px;
      border-bottom: 1px solid #eee;
      cursor: pointer;
    }

    .resultado-item:hover {
      background: #f5f5f5;
    }

    .resultado-item img {
      width: 40px;
      height: 40px;
      object-fit: cover;
      margin-right: 10px;
    }

    .resultado-info h4 {
      margin: 0;
      font-size: 14px;
    }

    .resultado-info .preco {
      color: #fe7db9;
      font-weight: bold;
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
          <div class="usuario-logado" id="usuarioLogado">
            <?php if (!empty($_SESSION['usuario']['foto'])): ?>
              <img src="uploads/<?php echo $_SESSION['usuario']['foto']; ?>" alt="Foto do usuário" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
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
        <span class="carrinho-count"><?php echo array_sum($_SESSION['carrinho']); ?></span>
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


<!-- === SEÇÃO DE PRODUTOS COM FILTROS === -->
<section class="produtos-section">
  <div class="container">
    <div class="produtos-header">
      <h1>
        <?php 
        if (!empty($categoria_filtro)) {
            echo strtoupper($categoria_filtro);
        } elseif (!empty($termo_busca)) {
            echo "RESULTADOS PARA: " . strtoupper($termo_busca);
        } else {
            echo "NOSSAS JOIAS";
        }
        ?>
        <?php if ($total_produtos > 0): ?>
          <small style="font-size: 14px; color: #666; display: block; margin-top: 5px;">
            (<?php echo $total_produtos; ?> produto<?php echo $total_produtos != 1 ? 's' : ''; ?> encontrado<?php echo $total_produtos != 1 ? 's' : ''; ?>)
          </small>
        <?php endif; ?>
      </h1>
      <div class="ordenar">
        <label for="ordenar">Ordenar por:</label>
        <select id="ordenar" onchange="window.location.href = atualizarParametroURL('ordenar', this.value)">
          <option value="relevancia" <?php echo $ordenar == 'relevancia' ? 'selected' : ''; ?>>Relevância</option>
          <option value="preco-crescente" <?php echo $ordenar == 'preco-crescente' ? 'selected' : ''; ?>>Preço: Menor para Maior</option>
          <option value="preco-decrescente" <?php echo $ordenar == 'preco-decrescente' ? 'selected' : ''; ?>>Preço: Maior para Menor</option>
          <option value="nome" <?php echo $ordenar == 'nome' ? 'selected' : ''; ?>>Nome A-Z</option>
          <option value="novidades" <?php echo $ordenar == 'novidades' ? 'selected' : ''; ?>>Novidades</option>
        </select>
      </div>
    </div>

    <div class="produtos-container">

      <!-- PRODUTOS -->
      <div class="produtos-grade">
        <?php if (empty($produtos)): ?>
          <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
            <i class="fas fa-search" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
            <h3>Nenhum produto encontrado</h3>
            <p>Tente ajustar os filtros ou buscar por outros termos.</p>
            <a href="produtos.php" style="display: inline-block; margin-top: 20px; padding: 12px 30px; background-color: #f06ca2; color: #fff; border: none; border-radius: 4px; font-size: 1em; font-weight: 600; cursor: pointer; text-decoration: none; transition: background-color 0.3s ease;">Ver Todos os Produtos</a>
          </div>
        <?php else: ?>
          <?php foreach ($produtos as $produto): ?>
          <div class="produto-card">
            <div class="produto-img-container">
              <img src="imgs/<?php echo $produto['imagem'] ?? 'produto-padrao.png'; ?>" 
                   alt="<?php echo htmlspecialchars($produto['nome']); ?>" 
                   class="produto-img"
                   onerror="this.src='imgs/produto-padrao.png'">
              <img src="imgs/coracao.png" 
                   alt="Curtir" 
                   class="favorito <?php echo isFavorito($produto['id']) ? 'ativo' : ''; ?>" 
                   onclick="toggleFavorito(<?php echo $produto['id']; ?>, this, '<?php echo htmlspecialchars($produto['nome']); ?>')">
            </div>
            <div class="produto-info">
              <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
              <div class="produto-preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
              <button onclick="adicionarCarrinho(<?php echo $produto['id']; ?>, '<?php echo htmlspecialchars($produto['nome']); ?>')">
                Adicionar ao carrinho
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- PAGINAÇÃO -->
    <?php if ($total_paginas > 1): ?>
    <div class="paginacao">
      <?php if ($pagina > 1): ?>
        <button onclick="mudarPagina(<?php echo $pagina - 1; ?>)">Anterior</button>
      <?php endif; ?>
      
      <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
        <?php if ($i == 1 || $i == $total_paginas || ($i >= $pagina - 2 && $i <= $pagina + 2)): ?>
          <button class="<?php echo $i == $pagina ? 'ativo' : ''; ?>" 
                  onclick="mudarPagina(<?php echo $i; ?>)">
            <?php echo $i; ?>
          </button>
        <?php elseif ($i == $pagina - 3 || $i == $pagina + 3): ?>
          <span style="padding: 8px 15px;">...</span>
        <?php endif; ?>
      <?php endfor; ?>
      
      <?php if ($pagina < $total_paginas): ?>
        <button onclick="mudarPagina(<?php echo $pagina + 1; ?>)">Próximo</button>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- === Seção Newsletter === -->
<section class="newsletter-section">
  <div class="newsletter-container">
    
    <!-- Logo -->
    <div class="newsletter-logo">
      <img src="imgs/logo.png" alt="Logo YARA">
    </div>

    <!-- Conteúdo -->
    <div class="newsletter-content">
      <h2>Descubra primeiro todas as novidades <br>
        da Yara. Cadastre-se!</h2>
      
      <!-- Formulário -->
      <form class="newsletter-form" id="newsletterForm">
        <input type="email" name="email" placeholder="Digite aqui o seu e-mail" required>
        <button type="submit" id="confirmEmailBtn">&#8594;</button>
      </form>

      <!-- Checkbox -->
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

<!-- Mensagens -->
<div id="mensagemCurtida" class="mensagem-curtida">
  <p id="textoCurtida"></p>
  <button onclick="verCurtidos()">Ver curtidos</button>
</div>

<div id="mensagemCarrinho" class="mensagem-curtida">
  <p id="textoCarrinho"></p>
  <button onclick="verCarrinho()">Ver carrinho</button>
</div>

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

<!-- Modal Cadastro -->
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
                        mostrarMensagem('Logout realizado com sucesso!', 'sucesso');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        mostrarMensagem('Erro ao fazer logout', 'erro');
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
        fetch('buscar_produtos.php?termo=' + encodeURIComponent(termo))
            .then(response => response.json())
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
        // Criar elemento de mensagem se não existir
        let mensagemEl = document.getElementById('mensagemFeedback');
        if (!mensagemEl) {
            mensagemEl = document.createElement('div');
            mensagemEl.id = 'mensagemFeedback';
            mensagemEl.className = 'mensagem';
            document.body.appendChild(mensagemEl);
        }
        
        mensagemEl.textContent = mensagem;
        mensagemEl.className = `mensagem ${tipo}`;
        mensagemEl.style.display = 'block';
        
        setTimeout(() => {
            mensagemEl.style.display = 'none';
        }, 5000);
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
                    mostrarMensagem('Login realizado com sucesso!', 'sucesso');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarMensagem(data.message || 'Erro ao fazer login', 'erro');
                }
            })
            .catch(error => {
                mostrarMensagem('Erro de conexão', 'erro');
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
                    mostrarMensagem('Cadastro realizado com sucesso!', 'sucesso');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarMensagem(data.message || 'Erro ao cadastrar', 'erro');
                }
            })
            .catch(error => {
                mostrarMensagem('Erro de conexão', 'erro');
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
                    mostrarMensagem('Inscrição realizada com sucesso!', 'sucesso');
                    this.reset();
                } else {
                    mostrarMensagem(data.message || 'Erro na inscrição', 'erro');
                }
            })
            .catch(error => {
                mostrarMensagem('Erro de conexão', 'erro');
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

    const carrinhoIcon = document.getElementById('carrinho');
    if (carrinhoIcon) {
      carrinhoIcon.addEventListener('click', () => {
        window.location.href = 'carrinho.php';
      });
    }
});

// Funções de utilitário
function atualizarParametroURL(parametro, valor) {
    const url = new URL(window.location.href);
    url.searchParams.set(parametro, valor);
    url.searchParams.set('pagina', '1'); // Reset para primeira página ao ordenar
    return url.toString();
}

function mudarPagina(pagina) {
    const url = new URL(window.location.href);
    url.searchParams.set('pagina', pagina);
    window.location.href = url.toString();
}

// Funções de curtida e carrinho
function toggleFavorito(produtoId, elemento, nome) {
    fetch('processa_form.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `acao=toggle_favorito&produto_id=${produtoId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            elemento.classList.toggle('ativo');
            
            const msg = document.getElementById("mensagemCurtida");
            const texto = document.getElementById("textoCurtida");
            if (msg && texto) {
                texto.textContent = `Produto "${nome}" ${data.acao === 'adicionado' ? 'adicionado aos' : 'removido dos'} favoritos!`;
                msg.style.display = "block";
                setTimeout(() => { msg.style.display = "none"; }, 4000);
            }
        }
    });
}

function adicionarCarrinho(produtoId, nome) {
    fetch('processa_form.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `acao=adicionar_carrinho&produto_id=${produtoId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar contador do carrinho
            const carrinhoCount = document.querySelector('.carrinho-count');
            if (carrinhoCount) {
                carrinhoCount.textContent = data.total_carrinho;
            }
            
            const msg = document.getElementById("mensagemCarrinho");
            const texto = document.getElementById("textoCarrinho");
            if (msg && texto) {
                texto.textContent = `Produto "${nome}" adicionado ao carrinho!`;
                msg.style.display = "block";
                setTimeout(() => { msg.style.display = "none"; }, 4000);
            }
        }
    });
}

function verCurtidos() {
    window.location.href = "favoritos.php";
}

function verCarrinho() {
    window.location.href = "carrinho.php";
}
</script>

</body>
</html>