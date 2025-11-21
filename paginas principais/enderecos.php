<?php
// enderecos.php
require_once 'funcoes.php';

// Verificar se usuário está logado
if (!$_SESSION['usuario']) {
    header('Location: index.php');
    exit();
}

$usuario = $_SESSION['usuario'];

// Simular dados de endereços (em um sistema real, viria do banco de dados)
$enderecos = [
    [
        'id' => 1,
        'titulo' => 'Casa',
        'nome' => 'João Silva',
        'cep' => '01234-567',
        'logradouro' => 'Rua das Flores, 123',
        'bairro' => 'Jardim Paulista',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'complemento' => 'Apto 45',
        'principal' => true
    ],
    [
        'id' => 2,
        'titulo' => 'Trabalho',
        'nome' => 'João Silva',
        'cep' => '04567-890',
        'logradouro' => 'Av. Paulista, 1000',
        'bairro' => 'Bela Vista',
        'cidade' => 'São Paulo',
        'estado' => 'SP',
        'complemento' => 'Sala 1204',
        'principal' => false
    ]
];
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
  <title>YARA - Meus Endereços</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
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
    /* Estilos específicos dos endereços */
    .profile-section {
      padding: 40px 20px 60px;
      background-color: #fff;
    }

    .profile-container {
      max-width: 1100px;
      margin: 0 auto;
      display: flex;
      gap: 40px;
      align-items: flex-start;
    }

    .profile-sidebar {
      flex: 0 0 250px;
      background-color: #ffe7f6;
      padding: 25px;
      border-radius: 8px;
    }

    .profile-sidebar h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.6em;
      margin: 0 0 20px 0;
      padding-bottom: 10px;
      border-bottom: 1px solid #fe7db9;
      color: #333;
    }

    .profile-nav ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .profile-nav li a {
      display: block;
      padding: 12px 15px;
      text-decoration: none;
      color: #333;
      font-weight: 500;
      border-radius: 4px;
      transition: background-color 0.2s ease, color 0.2s ease;
      margin-bottom: 5px;
    }

    .profile-nav li a.active,
    .profile-nav li a:hover {
      background-color: #fe7db9;
      color: #fff;
    }
    
    .profile-nav li a i {
      margin-right: 10px;
      width: 20px;
    }

    .profile-content {
      flex: 1;
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      border: 1px solid #eee;
    }
    
    .profile-content h1 {
      font-family: 'Playfair Display', serif;
      font-size: 2.2em;
      margin: 0 0 30px 0;
      color: #333;
      font-weight: 600;
    }

    .enderecos-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .btn-primary {
      display: inline-block;
      padding: 12px 25px;
      background-color: #f06ca2;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #e3558f;
    }

    .enderecos-grid {
      display: grid;
      gap: 25px;
    }

    .endereco-card {
      border: 1px solid #eee;
      border-radius: 8px;
      padding: 25px;
      background: #fafafa;
      position: relative;
    }

    .endereco-principal {
      border: 2px solid #fe7db9;
      background: #fff;
    }

    .endereco-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 15px;
    }

    .endereco-titulo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .endereco-titulo h3 {
      margin: 0;
      color: #333;
      font-size: 1.3em;
    }

    .badge-principal {
      background: #fe7db9;
      color: white;
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 0.7em;
      font-weight: 600;
      text-transform: uppercase;
    }

    .endereco-actions {
      display: flex;
      gap: 10px;
    }

    .btn-acao {
      padding: 6px 12px;
      border: 1px solid #ddd;
      background: white;
      color: #666;
      border-radius: 4px;
      text-decoration: none;
      font-size: 0.8em;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-acao:hover {
      background: #f5f5f5;
      color: #333;
    }

    .btn-acao.editar {
      border-color: #fe7db9;
      color: #fe7db9;
    }

    .btn-acao.editar:hover {
      background: #fe7db9;
      color: white;
    }

    .btn-acao.excluir {
      border-color: #e74c3c;
      color: #e74c3c;
    }

    .btn-acao.excluir:hover {
      background: #e74c3c;
      color: white;
    }

    .endereco-info {
      line-height: 1.6;
    }

    .endereco-info p {
      margin: 5px 0;
      color: #555;
    }

    .endereco-info strong {
      color: #333;
    }

    .empty-enderecos {
      text-align: center;
      padding: 60px 20px;
      color: #666;
    }

    .empty-enderecos i {
      font-size: 4em;
      color: #ddd;
      margin-bottom: 20px;
    }

    .empty-enderecos h3 {
      font-size: 1.5em;
      margin-bottom: 10px;
      color: #333;
    }

    .empty-enderecos p {
      margin-bottom: 30px;
    }

    @media (max-width: 768px) {
      .profile-container {
        flex-direction: column;
        gap: 20px;
      }
      
      .profile-sidebar {
        flex: 0 0 auto;
        width: 100%;
      }

      .enderecos-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
      }

      .endereco-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }

      .endereco-actions {
        align-self: stretch;
        justify-content: space-between;
      }
    }

    /* Estilos para mensagens */
    .mensagem {
      padding: 15px;
      margin: 20px auto;
      border-radius: 5px;
      text-align: center;
      max-width: 500px;
      display: none;
    }

    .mensagem.sucesso {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .mensagem.erro {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    /* Modal de Endereço */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    padding: 20px;
}

.modal {
    background: white;
    border-radius: 8px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #eee;
}

.modal-header h3 {
    margin: 0;
    color: #333;
    font-size: 1.4em;
}

.close-modal {
    background: none;
    border: none;
    font-size: 24px;
    color: #999;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-modal:hover {
    color: #333;
}

.modal-body {
    padding: 25px;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 0.9em;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #fe7db9;
    box-shadow: 0 0 0 2px rgba(254, 125, 185, 0.2);
}

.cep-group {
    position: relative;
}

.cep-input-container {
    display: flex;
    gap: 10px;
}

.cep-input-container input {
    flex: 1;
}

.btn-buscar-cep {
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 0 15px;
    cursor: pointer;
    font-size: 12px;
    white-space: nowrap;
    transition: all 0.3s ease;
}

.btn-buscar-cep:hover {
    background: #e9ecef;
    border-color: #fe7db9;
    color: #fe7db9;
}

.cep-help {
    display: block;
    margin-top: 5px;
    color: #666;
    font-size: 0.8em;
}

.checkbox-group {
    margin-top: 25px;
}

.checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin: 0;
}

.checkbox span {
    font-weight: normal;
    color: #333;
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.btn-cancelar {
    padding: 12px 25px;
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #666;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-cancelar:hover {
    background: #e9ecef;
    color: #333;
}

/* Estados do CEP */
.cep-loading {
    position: relative;
}

.cep-loading::after {
    content: '';
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #fe7db9;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: translateY(-50%) rotate(0deg); }
    100% { transform: translateY(-50%) rotate(360deg); }
}

/* Responsivo */
@media (max-width: 768px) {
    .modal {
        margin: 20px;
        max-height: calc(100vh - 40px);
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .cep-input-container {
        flex-direction: column;
    }
    
    .btn-buscar-cep {
        padding: 10px;
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

<!-- Mensagens de feedback -->
<div id="mensagemFeedback" class="mensagem"></div>

<main class="profile-section">
    <div class="profile-container container">
    
        <aside class="profile-sidebar">
            <h2>Minha Conta</h2>
            <nav class="profile-nav">
                <ul>
                    <li><a href="perfil.php"><i class="fas fa-user"></i> Meus Dados</a></li>
                    <li><a href="pedidos.php"><i class="fas fa-box"></i> Meus Pedidos</a></li>
                    <li><a href="enderecos.php" class="active"><i class="fas fa-map-marker-alt"></i> Endereços</a></li>
                    <li><a href="favoritos.php"><i class="fas fa-heart"></i> Favoritos</a></li>
                    <li><a href="#" class="sair" id="sairContaSidebar"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                </ul>
            </nav>
        </aside>

        <section class="profile-content">
            <h1>Meus Endereços</h1>

            <?php 
            // Buscar endereços reais da sessão
            $enderecos = getEnderecosUsuario($_SESSION['usuario']['id'] ?? 0);
            ?>
            
            <?php if (empty($enderecos)): ?>
                <div class="empty-enderecos">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Nenhum endereço cadastrado</h3>
                    <p>Adicione um endereço para facilitar suas compras.</p>
                    
                </div>
            <?php else: ?>
                <div class="enderecos-grid">
                    <?php foreach ($enderecos as $endereco): ?>
                    <div class="endereco-card <?php echo $endereco['principal'] ? 'endereco-principal' : ''; ?>">
                        <div class="endereco-header">
                            <div class="endereco-titulo">
                                <h3><?php echo htmlspecialchars($endereco['titulo']); ?></h3>
                                <?php if ($endereco['principal']): ?>
                                <span class="badge-principal">Principal</span>
                                <?php endif; ?>
                            </div>
                            <div class="endereco-actions">
                                <button class="btn-acao editar" data-id="<?php echo $endereco['id']; ?>" 
                                        data-titulo="<?php echo htmlspecialchars($endereco['titulo']); ?>"
                                        data-nome="<?php echo htmlspecialchars($endereco['nome']); ?>"
                                        data-cep="<?php echo htmlspecialchars($endereco['cep']); ?>"
                                        data-logradouro="<?php echo htmlspecialchars($endereco['logradouro']); ?>"
                                        data-numero="<?php echo htmlspecialchars($endereco['numero'] ?? ''); ?>"
                                        data-bairro="<?php echo htmlspecialchars($endereco['bairro']); ?>"
                                        data-cidade="<?php echo htmlspecialchars($endereco['cidade']); ?>"
                                        data-estado="<?php echo htmlspecialchars($endereco['estado']); ?>"
                                        data-complemento="<?php echo htmlspecialchars($endereco['complemento'] ?? ''); ?>"
                                        data-pais="<?php echo htmlspecialchars($endereco['pais']); ?>"
                                        data-principal="<?php echo $endereco['principal'] ? '1' : '0'; ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <?php if (!$endereco['principal']): ?>
                                <button class="btn-acao excluir" data-id="<?php echo $endereco['id']; ?>">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="endereco-info">
                            <p><strong>Destinatário:</strong> <?php echo htmlspecialchars($endereco['nome']); ?></p>
                            <p><strong>CEP:</strong> <?php echo htmlspecialchars($endereco['cep']); ?></p>
                            <p><strong>Endereço:</strong> <?php echo htmlspecialchars($endereco['logradouro']); ?>, <?php echo htmlspecialchars($endereco['numero'] ?? ''); ?></p>
                            <p><strong>Bairro:</strong> <?php echo htmlspecialchars($endereco['bairro']); ?></p>
                            <p><strong>Cidade/UF:</strong> <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['estado']); ?></p>
                            <p><strong>País:</strong> <?php echo htmlspecialchars($endereco['pais']); ?></p>
                            <?php if (!empty($endereco['complemento'])): ?>
                            <p><strong>Complemento:</strong> <?php echo htmlspecialchars($endereco['complemento']); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (!$endereco['principal']): ?>
                        <div style="margin-top: 15px; text-align: right;">
                            <button class="btn-acao" onclick="definirPrincipal('<?php echo $endereco['id']; ?>')">
                                <i class="fas fa-star"></i> Definir como Principal
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="enderecos-header">
                <button class="btn-primary" id="btnNovoEndereco">
                    <i class="fas fa-plus"></i> Novo Endereço
                </button>
            </div>
        </section>
    </div>
</main>

<!-- Modal para adicionar/editar endereço -->
<div class="modal-overlay" id="enderecoModalOverlay" style="display: none;">
    <div class="modal" id="enderecoModal">
        <div class="modal-header">
            <h3 id="modalTitulo">Novo Endereço</h3>
            <button class="close-modal" id="closeEnderecoModal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formEndereco">
                <input type="hidden" id="enderecoId" name="id">
                
                <div class="form-group">
                    <label for="titulo">Título do Endereço *</label>
                    <input type="text" id="titulo" name="titulo" required 
                           placeholder="Ex: Casa, Trabalho, Apartamento">
                </div>

                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" required 
                           placeholder="Nome do destinatário">
                </div>

                <div class="form-group">
                    <label for="pais">País *</label>
                    <select id="pais" name="pais" required>
                        <option value="">Selecione o país</option>
                        <?php foreach($paises as $pais): ?>
                            <option value="<?php echo $pais; ?>"><?php echo $pais; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group cep-group">
                    <label for="cep">CEP *</label>
                    <div class="cep-input-container">
                        <input type="text" id="cep" name="cep" required 
                               placeholder="00000-000" maxlength="9">
                        <button type="button" id="buscarCep" class="btn-buscar-cep">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <small class="cep-help">Digite o CEP para preencher automaticamente</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="logradouro">Logradouro *</label>
                        <input type="text" id="logradouro" name="logradouro" required 
                               placeholder="Rua, Avenida, etc.">
                    </div>
                    <div class="form-group">
                        <label for="numero">Número *</label>
                        <input type="text" id="numero" name="numero" required 
                               placeholder="Número">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="bairro">Bairro *</label>
                        <input type="text" id="bairro" name="bairro" required 
                               placeholder="Bairro">
                    </div>
                    <div class="form-group">
                        <label for="complemento">Complemento</label>
                        <input type="text" id="complemento" name="complemento" 
                               placeholder="Apto, Bloco, Casa, etc.">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cidade">Cidade *</label>
                        <input type="text" id="cidade" name="cidade" required 
                               placeholder="Cidade">
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado *</label>
                        <select id="estado" name="estado" required>
                            <option value="">UF</option>
                            <option value="AC">AC</option>
                            <option value="AL">AL</option>
                            <option value="AP">AP</option>
                            <option value="AM">AM</option>
                            <option value="BA">BA</option>
                            <option value="CE">CE</option>
                            <option value="DF">DF</option>
                            <option value="ES">ES</option>
                            <option value="GO">GO</option>
                            <option value="MA">MA</option>
                            <option value="MT">MT</option>
                            <option value="MS">MS</option>
                            <option value="MG">MG</option>
                            <option value="PA">PA</option>
                            <option value="PB">PB</option>
                            <option value="PR">PR</option>
                            <option value="PE">PE</option>
                            <option value="PI">PI</option>
                            <option value="RJ">RJ</option>
                            <option value="RN">RN</option>
                            <option value="RS">RS</option>
                            <option value="RO">RO</option>
                            <option value="RR">RR</option>
                            <option value="SC">SC</option>
                            <option value="SP">SP</option>
                            <option value="SE">SE</option>
                            <option value="TO">TO</option>
                        </select>
                    </div>
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox">
                        <input type="checkbox" id="principal" name="principal" value="1">
                        <span>Definir como endereço principal</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancelar" id="cancelarEndereco">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Salvar Endereço
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

// === SISTEMA DE ENDEREÇOS ===
let modoEdicao = false;

// Função para mostrar mensagens
function mostrarMensagem(mensagem, tipo = 'sucesso') {
    const mensagemDiv = document.getElementById('mensagemFeedback');
    if (mensagemDiv) {
        mensagemDiv.textContent = mensagem;
        mensagemDiv.className = `mensagem ${tipo}`;
        mensagemDiv.style.display = 'block';
        
        setTimeout(() => {
            mensagemDiv.style.display = 'none';
        }, 3000);
    }
}

// Função para formatar CEP
function formatarCEP(cep) {
    cep = cep.replace(/\D/g, '');
    if (cep.length > 5) {
        cep = cep.substring(0, 5) + '-' + cep.substring(5, 8);
    }
    return cep;
}

// Função para buscar CEP
async function buscarCEP() {
    const cepInput = document.getElementById('cep');
    const cep = cepInput.value.replace(/\D/g, '');
    
    if (cep.length !== 8) {
        mostrarMensagem('Digite um CEP válido com 8 dígitos', 'erro');
        return;
    }
    
    // Mostrar loading
    cepInput.classList.add('cep-loading');
    document.getElementById('buscarCep').disabled = true;
    
    try {
        const response = await fetch('funcoes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'acao=buscar_cep&cep=' + cep
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('logradouro').value = data.logradouro;
            document.getElementById('bairro').value = data.bairro;
            document.getElementById('cidade').value = data.cidade;
            document.getElementById('estado').value = data.estado;
            
            // Focar no número após preencher o CEP
            document.getElementById('numero').focus();
            
            mostrarMensagem('Endereço encontrado! Complete com o número.', 'sucesso');
        } else {
            mostrarMensagem(data.message || 'CEP não encontrado', 'erro');
        }
    } catch (error) {
        console.error('Erro ao buscar CEP:', error);
        mostrarMensagem('Erro ao buscar CEP. Tente novamente.', 'erro');
    } finally {
        cepInput.classList.remove('cep-loading');
        document.getElementById('buscarCep').disabled = false;
    }
}

// Função para abrir modal de endereço
function abrirModalEndereco(editar = false, enderecoData = null) {
    const modal = document.getElementById('enderecoModalOverlay');
    const titulo = document.getElementById('modalTitulo');
    const form = document.getElementById('formEndereco');
    
    if (!modal) {
        console.error('Modal não encontrado!');
        return;
    }
    
    modoEdicao = editar;
    
    if (editar && enderecoData) {
        titulo.textContent = 'Editar Endereço';
        document.getElementById('enderecoId').value = enderecoData.id;
        document.getElementById('titulo').value = enderecoData.titulo;
        document.getElementById('nome').value = enderecoData.nome;
        document.getElementById('pais').value = enderecoData.pais;
        document.getElementById('cep').value = enderecoData.cep;
        document.getElementById('logradouro').value = enderecoData.logradouro;
        document.getElementById('numero').value = enderecoData.numero;
        document.getElementById('bairro').value = enderecoData.bairro;
        document.getElementById('complemento').value = enderecoData.complemento;
        document.getElementById('cidade').value = enderecoData.cidade;
        document.getElementById('estado').value = enderecoData.estado;
        document.getElementById('principal').checked = enderecoData.principal === '1';
    } else {
        titulo.textContent = 'Novo Endereço';
        form.reset();
        document.getElementById('enderecoId').value = '';
        document.getElementById('pais').value = 'Brasil';
        document.getElementById('principal').checked = false;
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// Função para fechar modal de endereço
function fecharModalEndereco() {
    const modal = document.getElementById('enderecoModalOverlay');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Função para salvar endereço
async function salvarEndereco(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const acao = modoEdicao ? 'editar_endereco' : 'adicionar_endereco';
    formData.append('acao', acao);
    
    try {
        const response = await fetch('funcoes.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarMensagem(
                modoEdicao ? 'Endereço atualizado com sucesso!' : 'Endereço adicionado com sucesso!', 
                'sucesso'
            );
            fecharModalEndereco();
            
            // Recarregar a página para mostrar os endereços atualizados
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarMensagem('Erro ao salvar endereço. Tente novamente.', 'erro');
        }
    } catch (error) {
        console.error('Erro:', error);
        mostrarMensagem('Erro ao salvar endereço. Tente novamente.', 'erro');
    }
}

// Função para excluir endereço
async function excluirEndereco(enderecoId) {
    if (confirm('Tem certeza que deseja excluir este endereço?')) {
        try {
            const response = await fetch('funcoes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'acao=excluir_endereco&id=' + enderecoId
            });
            
            const data = await response.json();
            
            if (data.success) {
                mostrarMensagem('Endereço excluído com sucesso!', 'sucesso');
                setTimeout(() => location.reload(), 1500);
            } else {
                mostrarMensagem('Erro ao excluir endereço. Tente novamente.', 'erro');
            }
        } catch (error) {
            console.error('Erro:', error);
            mostrarMensagem('Erro ao excluir endereço. Tente novamente.', 'erro');
        }
    }
}

// Função para definir endereço como principal
async function definirPrincipal(enderecoId) {
    if (confirm('Deseja definir este endereço como principal?')) {
        try {
            const response = await fetch('funcoes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'acao=definir_principal&id=' + enderecoId
            });
            
            const data = await response.json();
            
            if (data.success) {
                mostrarMensagem('Endereço definido como principal com sucesso!', 'sucesso');
                setTimeout(() => location.reload(), 1500);
            } else {
                mostrarMensagem('Erro ao definir endereço principal. Tente novamente.', 'erro');
            }
        } catch (error) {
            console.error('Erro:', error);
            mostrarMensagem('Erro ao definir endereço principal. Tente novamente.', 'erro');
        }
    }
}

// === Funções JavaScript principais ===
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página carregada - inicializando sistema de endereços');
    
    // --- SISTEMA DE ENDEREÇOS ---
    // Botões de novo endereço
    const btnNovoEndereco = document.getElementById('btnNovoEndereco');
    const btnNovoEnderecoEmpty = document.getElementById('btnNovoEnderecoEmpty');
    
    console.log('Botão novo endereço:', btnNovoEndereco);
    console.log('Botão novo endereço empty:', btnNovoEnderecoEmpty);
    
    if (btnNovoEndereco) {
        btnNovoEndereco.addEventListener('click', () => {
            console.log('Clicou em Novo Endereço');
            abrirModalEndereco(false);
        });
    }
    
    if (btnNovoEnderecoEmpty) {
        btnNovoEnderecoEmpty.addEventListener('click', () => {
            console.log('Clicou em Adicionar Endereço (empty)');
            abrirModalEndereco(false);
        });
    }
    
    // Botões de editar endereço
    document.querySelectorAll('.btn-acao.editar').forEach(btn => {
        btn.addEventListener('click', function() {
            const enderecoData = {
                id: this.dataset.id,
                titulo: this.dataset.titulo,
                nome: this.dataset.nome,
                cep: this.dataset.cep,
                logradouro: this.dataset.logradouro,
                numero: this.dataset.numero,
                bairro: this.dataset.bairro,
                complemento: this.dataset.complemento,
                cidade: this.dataset.cidade,
                estado: this.dataset.estado,
                pais: this.dataset.pais,
                principal: this.dataset.principal
            };
            abrirModalEndereco(true, enderecoData);
        });
    });
    
    // Botões de excluir endereço
    document.querySelectorAll('.btn-acao.excluir').forEach(btn => {
        btn.addEventListener('click', function() {
            const enderecoId = this.dataset.id;
            excluirEndereco(enderecoId);
        });
    });
    
    // Formulário de endereço
    const formEndereco = document.getElementById('formEndereco');
    if (formEndereco) {
        formEndereco.addEventListener('submit', salvarEndereco);
    }
    
    // Buscar CEP
    const btnBuscarCep = document.getElementById('buscarCep');
    if (btnBuscarCep) {
        btnBuscarCep.addEventListener('click', buscarCEP);
    }
    
    // Formatar CEP automaticamente
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function() {
            this.value = formatarCEP(this.value);
        });
    }
    
    // Fechar modal
    const closeEnderecoModal = document.getElementById('closeEnderecoModal');
    const cancelarEndereco = document.getElementById('cancelarEndereco');
    const modalOverlay = document.getElementById('enderecoModalOverlay');
    
    if (closeEnderecoModal) {
        closeEnderecoModal.addEventListener('click', fecharModalEndereco);
    }
    
    if (cancelarEndereco) {
        cancelarEndereco.addEventListener('click', fecharModalEndereco);
    }
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                fecharModalEndereco();
            }
        });
    }
    
    // Fechar com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modalOverlay = document.getElementById('enderecoModalOverlay');
            if (modalOverlay && modalOverlay.style.display === 'flex') {
                fecharModalEndereco();
            }
            
            if (loginOverlay && loginOverlay.style.display === 'flex') closeLogin();
            if (signupOverlay && signupOverlay.style.display === 'flex') closeSignup();
            if (contactOverlay && contactOverlay.style.display === 'flex') closeContactModal();
        }
    });

    // --- MENU DO USUÁRIO ---
    const usuarioLogado = document.getElementById('usuarioLogado');
    const menuUsuario = document.getElementById('menuUsuario');
    const sairConta = document.getElementById('sairConta');
    const sairContaSidebar = document.getElementById('sairContaSidebar');

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
        
        // Sair da conta (sidebar)
        if (sairContaSidebar) {
            sairContaSidebar.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Deseja realmente sair?')) {
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
                                window.location.href = 'index.php';
                            }, 1500);
                        } else {
                            mostrarMensagem(data.message, 'erro');
                        }
                    });
                }
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
                if (resultadosPesquisa) resultadosPesquisa.innerHTML = '';
            }
        });
    }

    function buscarProdutos(termo) {
        console.log('Buscando por:', termo);
        
        fetch('buscar_produtos.php?termo=' + encodeURIComponent(termo))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na rede: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Resposta:', data);
                
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

    // --- ÍCONES E REDIRECIONAMENTOS ---
    const heartIcon = document.getElementById('heartIcon');
    if (heartIcon) {
      heartIcon.addEventListener('click', () => {
        window.location.href = 'favoritos.php';
      });
    }

    // Redirecionar para carrinho
    const carrinhoIcon = document.getElementById('carrinho');
    if (carrinhoIcon) {
      carrinhoIcon.addEventListener('click', () => {
        window.location.href = 'carrinho.php';
      });
    }
});
</script>
</body>
</html>