<?php
// funcoes.php
session_start();
require_once 'conexao.php';

// Inicializar sessões se não existirem
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if (!isset($_SESSION['favoritos'])) {
    $_SESSION['favoritos'] = [];
}

if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = null;
}

// ==================== FUNÇÕES DE USUÁRIO ====================

// Função para fazer logout
function fazerLogout() {
    $_SESSION['usuario'] = null;
    return true;
}

// Função para fazer login (ATUALIZADA PARA ADMIN)
function fazerLogin($email, $senha) {
    global $conexao;
    
    $sql = "SELECT id, nome, email, senha, foto, is_admin FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        // Verificar senha (em produção, usar password_verify)
        if ($senha === '123456' || ($email === 'admin@gmail.com' && $senha === 'admin1234')) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'foto' => $usuario['foto'],
                'is_admin' => $usuario['is_admin'] ?? 0
            ];
            return true;
        }
    }
    return false;
}

// Função para cadastrar usuário
function cadastrarUsuario($nome, $email, $senha, $foto = null) {
    global $conexao;
    
    // Verificar se email já existe
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        return "Email já cadastrado!";
    }
    
    // Processar upload da foto
    $foto_nome = null;
    if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
        // Criar pasta uploads se não existir
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $foto_nome = uniqid() . '.' . $extensao;
        $destino = 'uploads/' . $foto_nome;
        
        if (!move_uploaded_file($foto['tmp_name'], $destino)) {
            return "Erro ao fazer upload da foto!";
        }
    }
    
    // Inserir novo usuário
    $sql = "INSERT INTO usuarios (nome, email, senha, foto) VALUES (?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $senha, $foto_nome);
    
    if ($stmt->execute()) {
        $_SESSION['usuario'] = [
            'id' => $stmt->insert_id,
            'nome' => $nome,
            'email' => $email,
            'foto' => $foto_nome
        ];
        return true;
    }
    
    return "Erro ao cadastrar usuário!";
}

// ==================== FUNÇÕES DE PRODUTOS ====================

// Função para buscar produtos em destaque (do banco)
function getProdutosDestaqueDB() {
    global $conexao;
    
    $sql = "SELECT * FROM produtos WHERE destaque = 1 AND disponivel = 1 LIMIT 8";
    $resultado = $conexao->query($sql);
    
    $produtos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while($produto = $resultado->fetch_assoc()) {
            $produtos[] = $produto;
        }
    }
    return $produtos;
}

// Função alternativa: produtos fixos (exemplo/local)
function getProdutosDestaque() {
    return [
        [
            'id' => 1,
            'nome' => 'Conjunto Encanto Lilás',
            'imagem' => '../paginas principais/imgs/2novidades.png',
            'preco' => 89.90,
            'categoria' => 'conjuntos'
        ],
        [
            'id' => 2,
            'nome' => 'Espiral de Serenidade',
            'imagem' => '../paginas principais/imgs/1novidades.png',
            'preco' => 65.50,
            'categoria' => 'colares'
        ],
        [
            'id' => 3,
            'nome' => 'Conjunto Coração de Rubi',
            'imagem' => '../paginas principais/imgs/3novidades.jpg',
            'preco' => 120.00,
            'categoria' => 'conjuntos'
        ]
    ];
}

// Função para buscar produtos por categoria
function getProdutosPorCategoria($categoria, $limite = 6) {
    global $conexao;
    
    $sql = "SELECT * FROM produtos WHERE categoria = ? AND disponivel = 1 LIMIT ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("si", $categoria, $limite);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $produtos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while($produto = $resultado->fetch_assoc()) {
            $produtos[] = $produto;
        }
    }
    return $produtos;
}

// Buscar produtos por termo
function buscarProdutos($termo) {
    global $conexao;
    
    $sql = "SELECT * FROM produtos WHERE (nome LIKE ? OR descricao LIKE ?) AND disponivel = 1 LIMIT 10";
    $stmt = $conexao->prepare($sql);
    $termoBusca = "%$termo%";
    $stmt->bind_param("ss", $termoBusca, $termoBusca);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $produtos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while($produto = $resultado->fetch_assoc()) {
            $produtos[] = $produto;
        }
    }
    return $produtos;
}

// ==================== FUNÇÕES DE CARRINHO ====================

function adicionarAoCarrinho($produtoId, $quantidade = 1) {
    if (isset($_SESSION['carrinho'][$produtoId])) {
        $_SESSION['carrinho'][$produtoId] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produtoId] = $quantidade;
    }
    return true;
}

function removerDoCarrinho($produtoId) {
    if (isset($_SESSION['carrinho'][$produtoId])) {
        unset($_SESSION['carrinho'][$produtoId]);
        return true;
    }
    return false;
}

function atualizarCarrinho($produtoId, $quantidade) {
    if ($quantidade <= 0) {
        return removerDoCarrinho($produtoId);
    } else {
        $_SESSION['carrinho'][$produtoId] = $quantidade;
        return true;
    }
}

// ==================== FUNÇÕES DE FAVORITOS ====================

function toggleFavorito($produtoId) {
    if (in_array($produtoId, $_SESSION['favoritos'])) {
        $_SESSION['favoritos'] = array_diff($_SESSION['favoritos'], [$produtoId]);
        return 'removido';
    } else {
        $_SESSION['favoritos'][] = $produtoId;
        return 'adicionado';
    }
}

function isFavorito($produtoId) {
    return in_array($produtoId, $_SESSION['favoritos']);
}

// Buscar produto por ID (usando produtos fixos)
function getProdutoPorId($id) {
    $produtos = getProdutosDestaque();
    foreach ($produtos as $produto) {
        if ($produto['id'] == $id) {
            return $produto;
        }
    }
    return null;
}

// ==================== PROCESSAR AÇÕES VIA POST ====================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'adicionar_carrinho':
                if (isset($_POST['produto_id'])) {
                    adicionarAoCarrinho($_POST['produto_id']);
                    echo json_encode(['success' => true, 'total_carrinho' => array_sum($_SESSION['carrinho'])]);
                }
                exit;
                
            case 'toggle_favorito':
                if (isset($_POST['produto_id'])) {
                    $resultado = toggleFavorito($_POST['produto_id']);
                    echo json_encode(['success' => true, 'acao' => $resultado]);
                }
                exit;
                
            case 'atualizar_carrinho':
                if (isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
                    atualizarCarrinho($_POST['produto_id'], $_POST['quantidade']);
                    echo json_encode(['success' => true, 'total_carrinho' => array_sum($_SESSION['carrinho'])]);
                }
                exit;
                
            case 'buscar_produtos':
                if (isset($_POST['termo'])) {
                    $termo = $_POST['termo'];
                    $produtos = buscarProdutos($termo);
                    
                    // Se não encontrou produtos no banco, usa os produtos de exemplo
                    if (empty($produtos)) {
                        $produtos = getProdutosDestaque();
                    }
                    
                    // Formatar os dados para o JSON - MANTÉM OS CAMINHOS ORIGINAIS
                    $produtosFormatados = [];
                    foreach ($produtos as $produto) {
                        $produtosFormatados[] = [
                            'id' => $produto['id'],
                            'nome' => $produto['nome'],
                            'preco' => $produto['preco'],
                            'imagem' => $produto['imagem'] // Mantém o caminho original
                        ];
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'produtos' => $produtosFormatados
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'produtos' => []
                    ]);
                }
                exit;

            case 'login':
                if (isset($_POST['email']) && isset($_POST['senha'])) {
                    $email = $_POST['email'];
                    $senha = $_POST['senha'];
                    
                    if (fazerLogin($email, $senha)) {
                        // Verificar se é admin para redirecionar
                        if ($email === 'admin@gmail.com') {
                            echo json_encode([
                                'success' => true, 
                                'message' => 'Login realizado com sucesso!',
                                'redirect' => 'admin/dashboard.php'
                            ]);
                        } else {
                            echo json_encode([
                                'success' => true, 
                                'message' => 'Login realizado com sucesso!'
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Email ou senha incorretos!'
                        ]);
                    }
                }
                exit;

            case 'cadastro':
                if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha'])) {
                    $nome = $_POST['nome'];
                    $email = $_POST['email'];
                    $senha = $_POST['senha'];
                    $foto = isset($_FILES['foto']) ? $_FILES['foto'] : null;
                    
                    $resultado = cadastrarUsuario($nome, $email, $senha, $foto);
                    
                    if ($resultado === true) {
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Cadastro realizado com sucesso!'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => false, 
                            'message' => $resultado
                        ]);
                    }
                }
                exit;
        }
    }
}

// Função para buscar endereços do usuário
function getEnderecosUsuario($usuarioId) {
    // Em um sistema real, isso viria do banco de dados
    // Por enquanto, vamos usar a sessão
    if (!isset($_SESSION['enderecos'])) {
        $_SESSION['enderecos'] = [];
    }
    return $_SESSION['enderecos'];
}

// Função para adicionar endereço
function adicionarEndereco($dados) {
    if (!isset($_SESSION['enderecos'])) {
        $_SESSION['enderecos'] = [];
    }
    
    $novoEndereco = [
        'id' => uniqid(),
        'titulo' => $dados['titulo'],
        'nome' => $dados['nome'],
        'cep' => $dados['cep'],
        'logradouro' => $dados['logradouro'],
        'numero' => $dados['numero'],
        'bairro' => $dados['bairro'],
        'cidade' => $dados['cidade'],
        'estado' => $dados['estado'],
        'complemento' => $dados['complemento'] ?? '',
        'pais' => $dados['pais'],
        'principal' => $dados['principal'] ?? false
    ];
    
    // Se for o primeiro endereço ou marcado como principal, definir como principal
    if (empty($_SESSION['enderecos']) || $novoEndereco['principal']) {
        $novoEndereco['principal'] = true;
        // Remover principal de outros endereços
        foreach ($_SESSION['enderecos'] as &$endereco) {
            $endereco['principal'] = false;
        }
    }
    
    $_SESSION['enderecos'][] = $novoEndereco;
    return $novoEndereco;
}

// Função para editar endereço
function editarEndereco($id, $dados) {
    if (!isset($_SESSION['enderecos'])) {
        return false;
    }
    
    foreach ($_SESSION['enderecos'] as &$endereco) {
        if ($endereco['id'] == $id) {
            $endereco['titulo'] = $dados['titulo'];
            $endereco['nome'] = $dados['nome'];
            $endereco['cep'] = $dados['cep'];
            $endereco['logradouro'] = $dados['logradouro'];
            $endereco['numero'] = $dados['numero'];
            $endereco['bairro'] = $dados['bairro'];
            $endereco['cidade'] = $dados['cidade'];
            $endereco['estado'] = $dados['estado'];
            $endereco['complemento'] = $dados['complemento'] ?? '';
            $endereco['pais'] = $dados['pais'];
            
            // Se marcou como principal, atualizar todos
            if ($dados['principal'] ?? false) {
                $endereco['principal'] = true;
                foreach ($_SESSION['enderecos'] as &$end) {
                    if ($end['id'] != $id) {
                        $end['principal'] = false;
                    }
                }
            }
            
            return true;
        }
    }
    return false;
}

// Função para excluir endereço
function excluirEndereco($id) {
    if (!isset($_SESSION['enderecos'])) {
        return false;
    }
    
    $_SESSION['enderecos'] = array_filter($_SESSION['enderecos'], function($endereco) use ($id) {
        return $endereco['id'] != $id;
    });
    
    return true;
}

// Função para definir endereço principal
function definirEnderecoPrincipal($id) {
    if (!isset($_SESSION['enderecos'])) {
        return false;
    }
    
    foreach ($_SESSION['enderecos'] as &$endereco) {
        $endereco['principal'] = ($endereco['id'] == $id);
    }
    
    return true;
}

// Processar ações de endereço via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    switch ($_POST['acao']) {
        case 'buscar_cep':
            if (isset($_POST['cep'])) {
                $cep = preg_replace('/[^0-9]/', '', $_POST['cep']);
                echo buscarEnderecoPorCEP($cep);
            }
            exit;
            
        case 'adicionar_endereco':
            $dados = [
                'titulo' => $_POST['titulo'],
                'nome' => $_POST['nome'],
                'cep' => $_POST['cep'],
                'logradouro' => $_POST['logradouro'],
                'numero' => $_POST['numero'],
                'bairro' => $_POST['bairro'],
                'cidade' => $_POST['cidade'],
                'estado' => $_POST['estado'],
                'complemento' => $_POST['complemento'] ?? '',
                'pais' => $_POST['pais'],
                'principal' => isset($_POST['principal'])
            ];
            
            $endereco = adicionarEndereco($dados);
            echo json_encode(['success' => true, 'endereco' => $endereco]);
            exit;
            
        case 'editar_endereco':
            if (isset($_POST['id'])) {
                $dados = [
                    'titulo' => $_POST['titulo'],
                    'nome' => $_POST['nome'],
                    'cep' => $_POST['cep'],
                    'logradouro' => $_POST['logradouro'],
                    'numero' => $_POST['numero'],
                    'bairro' => $_POST['bairro'],
                    'cidade' => $_POST['cidade'],
                    'estado' => $_POST['estado'],
                    'complemento' => $_POST['complemento'] ?? '',
                    'pais' => $_POST['pais'],
                    'principal' => isset($_POST['principal'])
                ];
                
                $sucesso = editarEndereco($_POST['id'], $dados);
                echo json_encode(['success' => $sucesso]);
            }
            exit;
            
        case 'excluir_endereco':
            if (isset($_POST['id'])) {
                $sucesso = excluirEndereco($_POST['id']);
                echo json_encode(['success' => $sucesso]);
            }
            exit;
            
        case 'definir_principal':
            if (isset($_POST['id'])) {
                $sucesso = definirEnderecoPrincipal($_POST['id']);
                echo json_encode(['success' => $sucesso]);
            }
            exit;
    }
}

// Função para buscar CEP via API
function buscarEnderecoPorCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    
    if (strlen($cep) !== 8) {
        return json_encode(['success' => false, 'message' => 'CEP inválido']);
    }
    
    // ViaCEP API
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $dados = json_decode($response, true);
        
        if (!isset($dados['erro'])) {
            return json_encode([
                'success' => true,
                'logradouro' => $dados['logradouro'] ?? '',
                'bairro' => $dados['bairro'] ?? '',
                'cidade' => $dados['localidade'] ?? '',
                'estado' => $dados['uf'] ?? ''
            ]);
        }
    }
    
    return json_encode(['success' => false, 'message' => 'CEP não encontrado']);
}
?>