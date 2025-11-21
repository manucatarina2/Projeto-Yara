<?php
// perfil.php
require_once 'funcoes.php';

// Verificar se usuário está logado
if (!isset($_SESSION['usuario']) || !$_SESSION['usuario']) {
    header('Location: index.php');
    exit();
}

$usuario = $_SESSION['usuario'];

// Processar atualização de dados se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_perfil') {
    $novoNome = trim($_POST['nome']);
    $novoEmail = trim($_POST['email']);
    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    
    // Validar dados
    if (empty($novoNome) || empty($novoEmail)) {
        $mensagemErro = "Nome e email são obrigatórios.";
    } else {
        // Verificar se o email já existe (exceto para o próprio usuário)
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$novoEmail, $usuario['id']]);
        if ($stmt->fetch()) {
            $mensagemErro = "Este email já está em uso por outro usuário.";
        } else {
            try {
                // Iniciar transação
                $pdo->beginTransaction();
                
                // Atualizar dados básicos
                $sql = "UPDATE usuarios SET nome = ?, email = ?";
                $params = [$novoNome, $novoEmail];
                
                // Se forneceu senha atual e nova senha, verificar e atualizar
                if (!empty($senhaAtual) && !empty($novaSenha)) {
                    // Verificar senha atual
                    if (password_verify($senhaAtual, $usuario['senha'])) {
                        $sql .= ", senha = ?";
                        $params[] = password_hash($novaSenha, PASSWORD_DEFAULT);
                    } else {
                        throw new Exception("Senha atual incorreta.");
                    }
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $usuario['id'];
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                // Atualizar foto se enviada
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $foto = $_FILES['foto'];
                    $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
                    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if (in_array($extensao, $extensoesPermitidas)) {
                        // Verificar tamanho (máximo 2MB)
                        if ($foto['size'] <= 2 * 1024 * 1024) {
                            $nomeArquivo = uniqid() . '.' . $extensao;
                            $caminhoDestino = 'uploads/' . $nomeArquivo;
                            
                            // Criar diretório se não existir
                            if (!is_dir('uploads')) {
                                mkdir('uploads', 0777, true);
                            }
                            
                            if (move_uploaded_file($foto['tmp_name'], $caminhoDestino)) {
                                // Remover foto antiga se existir
                                if (!empty($usuario['foto']) && $usuario['foto'] !== 'default.png') {
                                    $fotoAntiga = 'uploads/' . $usuario['foto'];
                                    if (file_exists($fotoAntiga)) {
                                        unlink($fotoAntiga);
                                    }
                                }
                                
                                // Atualizar no banco
                                $stmt = $pdo->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
                                $stmt->execute([$nomeArquivo, $usuario['id']]);
                                $usuario['foto'] = $nomeArquivo;
                            }
                        } else {
                            throw new Exception("A imagem deve ter no máximo 2MB.");
                        }
                    } else {
                        throw new Exception("Formato de imagem não suportado. Use JPG, PNG ou GIF.");
                    }
                }
                
                $pdo->commit();
                
                // Atualizar dados na sessão
                $stmt = $pdo->prepare("SELECT *, DATE_FORMAT(data_cadastro, '%Y-%m-%d') as data_cadastro_formatada FROM usuarios WHERE id = ?");
                $stmt->execute([$usuario['id']]);
                $usuarioAtualizado = $stmt->fetch();
                
                // Manter a senha na sessão (não atualizar)
                $usuarioAtualizado['senha'] = $usuario['senha'];
                $_SESSION['usuario'] = $usuarioAtualizado;
                $usuario = $_SESSION['usuario'];
                
                $mensagemSucesso = "Dados atualizados com sucesso!";
                
            } catch (Exception $e) {
                $pdo->rollBack();
                $mensagemErro = $e->getMessage();
            }
        }
    }
}

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

// Formatar data de cadastro corretamente
$dataCadastro = 'Data não disponível';
if (isset($usuario['data_cadastro']) && !empty($usuario['data_cadastro'])) {
    $dataCadastro = date('d/m/Y', strtotime($usuario['data_cadastro']));
} elseif (isset($usuario['data_cadastro_formatada']) && !empty($usuario['data_cadastro_formatada'])) {
    $dataCadastro = date('d/m/Y', strtotime($usuario['data_cadastro_formatada']));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>YARA - Meu Perfil</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
  <style>
    /* Estilos específicos do perfil */
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

    .data-section .data-item {
      margin-bottom: 20px;
      border-bottom: 1px dashed #eee;
      padding-bottom: 15px;
    }
    
    .data-section .data-item:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
    
    .data-section label {
      display: block;
      font-weight: 600;
      color: #777;
      margin-bottom: 5px;
      font-size: 0.9em;
    }
    
    .data-section span {
      font-size: 1.1em;
      color: #333;
    }

    .edit-button {
      display: inline-block;
      margin-top: 30px;
      padding: 10px 25px;
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
    
    .edit-button:hover {
      background-color: #e3558f;
    }

    .user-avatar {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 30px;
    }

    .user-avatar img, .avatar-placeholder {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
    }

    .avatar-placeholder {
      background: #fe7db9;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 2em;
      font-weight: bold;
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

    /* Estilos do Modal de Edição */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 10000;
      align-items: center;
      justify-content: center;
    }

    .modal-overlay.mostrar {
      display: flex;
    }

    .modal-editar {
      background: white;
      border-radius: 8px;
      padding: 30px;
      width: 90%;
      max-width: 500px;
      max-height: 90vh;
      overflow-y: auto;
      position: relative;
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #eee;
    }

    .modal-header h2 {
      margin: 0;
      color: #333;
      font-family: 'Playfair Display', serif;
    }

    .fechar-modal {
      background: none;
      border: none;
      font-size: 24px;
      cursor: pointer;
      color: #666;
      padding: 0;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .fechar-modal:hover {
      color: #333;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: #555;
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
      box-sizing: border-box;
    }

    .form-group input:focus {
      outline: none;
      border-color: #f06ca2;
    }

    .form-acoes {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 30px;
    }

    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    .btn-primary {
      background-color: #f06ca2;
      color: white;
    }

    .btn-primary:hover {
      background-color: #e3558f;
    }

    .btn-secondary {
      background-color: #6c757d;
      color: white;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
    }

    .avatar-upload {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
    }

    .avatar-preview {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      overflow: hidden;
      background: #ffe7f6;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      font-weight: bold;
      color: #f06ca2;
    }

    .avatar-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .upload-btn {
      background: #f8f9fa;
      border: 1px dashed #ddd;
      padding: 8px 15px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    .upload-btn:hover {
      background: #e9ecef;
      border-color: #f06ca2;
    }

    .senha-info {
      font-size: 12px;
      color: #666;
      margin-top: 5px;
    }

    .campo-opcional {
      opacity: 0.7;
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
          <!-- Mostrar foto do usuário se estiver logado -->
          <div class="usuario-logado" id="usuarioLogado">
            <?php if (!empty($_SESSION['usuario']['foto'])): ?>
              <img src="uploads/<?php echo $_SESSION['usuario']['foto']; ?>" alt="Foto do usuário" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
              <div style="width: 32px; height: 32px; border-radius: 50%; background: #fe7db9; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                <?php echo substr($_SESSION['usuario']['nome'], 0, 1); ?>
              </div>
            <?php endif; ?>
            
            <!-- Menu do usuário -->
            <div class="menu-usuario" id="menuUsuario">
              <a href="perfil.php">Meu Perfil</a>
              <a href="pedidos.php">Meus Pedidos</a>
              <a href="favoritos.php">Favoritos</a>
              <a href="#" class="sair" id="sairConta">Sair</a>
            </div>
          </div>
        <?php else: ?>
          <!-- Mostrar ícone de perfil se não estiver logado -->
          <img src="imgs/perfil.png" alt="Usuário" id="openLogin">
        <?php endif; ?>
        <img src="imgs/localiza.png" alt="Localização">
        <img src="imgs/sacola.png" alt="Sacola" id="carrinho">
        <span class="carrinho-count"><?php echo array_sum($_SESSION['carrinho']); ?></span>
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

        <!-- Ícones -->
        <div class="menu-icons" aria-hidden="true">
          <img src="imgs/coracao.png" alt="Favoritos" id="heartIcon">
          
          <!-- Barra de Pesquisa -->
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
<div id="mensagemFeedback" class="mensagem">
    <?php 
    if (isset($mensagemSucesso)) {
        echo '<div class="mensagem sucesso">' . $mensagemSucesso . '</div>';
    } elseif (isset($mensagemErro)) {
        echo '<div class="mensagem erro">' . $mensagemErro . '</div>';
    }
    ?>
</div>

<main class="profile-section">
    <div class="profile-container container">
    
        <aside class="profile-sidebar">
            <h2>Minha Conta</h2>
            <nav class="profile-nav">
                <ul>
                    <li><a href="#meus-dados" class="active"><i class="fas fa-user"></i> Meus Dados</a></li>
                    <li><a href="pedidos.php"><i class="fas fa-box"></i> Meus Pedidos</a></li>
                    <li><a href="enderecos.php"><i class="fas fa-map-marker-alt"></i> Endereços</a></li>
                    <li><a href="favoritos.php"><i class="fas fa-heart"></i> Favoritos</a></li>
                    <li><a href="#" class="sair" id="sairContaSidebar"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                </ul>
            </nav>
        </aside>

        <section class="profile-content">
            <div class="user-avatar">
                <?php if (!empty($usuario['foto'])): ?>
                    <img src="uploads/<?php echo $usuario['foto']; ?>" alt="Foto de perfil">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <?php echo substr($usuario['nome'], 0, 1); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h1>Olá, <?php echo explode(' ', $usuario['nome'])[0]; ?>!</h1>
                    <p>Bem-vindo(a) ao seu perfil YARA</p>
                </div>
            </div>

            <div id="meus-dados" class="data-section">
                <h2>Meus Dados</h2>
                <div class="data-item">
                    <label>Nome Completo:</label>
                    <span><?php echo htmlspecialchars($usuario['nome']); ?></span>
                </div>
                <div class="data-item">
                    <label>E-mail:</label>
                    <span><?php echo htmlspecialchars($usuario['email']); ?></span>
                </div>
                <div class="data-item">
                    <label>Data de Cadastro:</label>
                    <span><?php echo $dataCadastro; ?></span>
                </div>
                <button class="edit-button" id="abrirModalEditar">Editar Dados</button>
            </div>
        </section>
    </div>
</main>

<!-- Modal de Edição de Dados -->
<div class="modal-overlay" id="modalEditar">
    <div class="modal-editar">
        <div class="modal-header">
            <h2>Editar Meus Dados</h2>
            <button class="fechar-modal" id="fecharModalEditar">&times;</button>
        </div>
        
        <form id="formEditarPerfil" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="atualizar_perfil">
            
            <div class="avatar-upload">
                <div class="avatar-preview" id="avatarPreview">
                    <?php if (!empty($usuario['foto'])): ?>
                        <img src="uploads/<?php echo $usuario['foto']; ?>" alt="Foto atual">
                    <?php else: ?>
                        <?php echo substr($usuario['nome'], 0, 1); ?>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="inputFoto" class="upload-btn">
                        <i class="fas fa-camera"></i> Alterar Foto
                    </label>
                    <input type="file" id="inputFoto" name="foto" accept="image/*" style="display: none;">
                    <div class="senha-info">JPG, PNG ou GIF (máx. 2MB)</div>
                </div>
            </div>

            <div class="form-group">
                <label for="nome">Nome Completo *</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>

            <div class="form-group">
                <label>Alterar Senha <span class="campo-opcional">(opcional)</span></label>
                <input type="password" name="senha_atual" placeholder="Senha atual">
                <input type="password" name="nova_senha" placeholder="Nova senha" style="margin-top: 8px;">
                <div class="senha-info">Deixe em branco se não quiser alterar a senha</div>
            </div>

            <div class="form-acoes">
                <button type="button" class="btn btn-secondary" id="cancelarEdicao">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
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

// === Funções JavaScript principais ===
document.addEventListener('DOMContentLoaded', function() {
    // --- MODAL DE EDIÇÃO DE PERFIL ---
    const modalEditar = document.getElementById('modalEditar');
    const abrirModalEditar = document.getElementById('abrirModalEditar');
    const fecharModalEditar = document.getElementById('fecharModalEditar');
    const cancelarEdicao = document.getElementById('cancelarEdicao');
    const formEditarPerfil = document.getElementById('formEditarPerfil');
    const inputFoto = document.getElementById('inputFoto');
    const avatarPreview = document.getElementById('avatarPreview');

    // Abrir modal
    if (abrirModalEditar) {
        abrirModalEditar.addEventListener('click', function() {
            modalEditar.classList.add('mostrar');
            document.body.style.overflow = 'hidden';
        });
    }

    // Fechar modal
    function fecharModal() {
        modalEditar.classList.remove('mostrar');
        document.body.style.overflow = '';
    }

    if (fecharModalEditar) {
        fecharModalEditar.addEventListener('click', fecharModal);
    }

    if (cancelarEdicao) {
        cancelarEdicao.addEventListener('click', fecharModal);
    }

    // Fechar modal ao clicar fora
    modalEditar.addEventListener('click', function(e) {
        if (e.target === modalEditar) {
            fecharModal();
        }
    });

    // Preview da foto
    if (inputFoto) {
        inputFoto.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Preview da foto">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Envio do formulário
    if (formEditarPerfil) {
        formEditarPerfil.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Mostrar loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Salvando...';
            submitBtn.disabled = true;
            
            fetch('perfil.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                // Recarregar a página para ver as alterações
                window.location.reload();
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarMensagem('Erro ao atualizar dados. Tente novamente.', 'erro');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }

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
                fazerLogout();
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
        if (modalEditar && modalEditar.classList.contains('mostrar')) fecharModal();
      }
    });

    // --- ÍCONES E REDIRECIONAMENTOS ---
    const heartIcon = document.getElementById('heartIcon');
    if (heartIcon) {
      heartIcon.addEventListener('click', () => {
        window.location.href = 'favoritos.php';
      });
    }

    // --- LOGOUT ---
    const sairContaSidebar = document.getElementById('sairContaSidebar');
    
    function fazerLogout() {
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
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarMensagem('Erro ao fazer logout.', 'erro');
            });
        }
    }

    if (sairContaSidebar) {
        sairContaSidebar.addEventListener('click', function(e) {
            e.preventDefault();
            fazerLogout();
        });
    }

// Mostrar mensagens do PHP automaticamente
    <?php if (isset($mensagemSucesso)): ?>
        setTimeout(() => {
            mostrarMensagem('<?php echo $mensagemSucesso; ?>', 'sucesso');
        }, 500);
    <?php endif; ?>
    
    <?php if (isset($mensagemErro)): ?>
        setTimeout(() => {
            mostrarMensagem('<?php echo $mensagemErro; ?>', 'erro');
        }, 500);
    <?php endif; ?>

});

// Função para mostrar mensagens
function mostrarMensagem(mensagem, tipo = 'sucesso') {
  const mensagemDiv = document.getElementById('mensagemFeedback');
  if (mensagemDiv) {
      mensagemDiv.textContent = mensagem;
      mensagemDiv.className = `mensagem ${tipo}`;
      mensagemDiv.style.display = 'block';
      
      // Rolagem suave para a mensagem
      mensagemDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
      
      setTimeout(() => {
          mensagemDiv.style.display = 'none';
      }, 5000);
  }
}
</script>
</script>
</body>
</html>