<?php
// funcoes.php - VERSÃO COMPLETA COM SISTEMA PIX

// Iniciar sessão de forma segura
if (session_status() == PHP_SESSION_NONE) {
    session_start();

    // Headers de segurança
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
}

// Conexão com o banco
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
if (!isset($_SESSION['admin_mode'])) {
    $_SESSION['admin_mode'] = false;
}
if (!isset($_SESSION['presentes'])) {
    $_SESSION['presentes'] = [];
}

// ==================== FUNÇÕES DE SEGURANÇA ====================

function sanitizarInput($dados)
{
    if (is_array($dados)) {
        return array_map('sanitizarInput', $dados);
    }
    return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
}

function validarEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validarSenha($senha)
{
    return strlen($senha) >= 8;
}

// ==================== FUNÇÕES DE PAGAMENTO PIX ====================

/**
 * Gera um código PIX para pagamento
 * @param float $valor Valor total do pedido
 * @param string $pedido_id ID do pedido (opcional)
 * @return string Código PIX gerado
 */
function generatePixCode($valor, $pedido_id = null)
{
    // ✅ DADOS DA LOJA YARA (configure com seus dados reais)
    $chave_pix = "yara.joias@pagamento.com"; // Chave PIX da loja
    $merchant_name = "YARA JOIAS LTDA";
    $merchant_city = "SAO PAULO";

    // ✅ FORMATAR VALOR (remover pontos e vírgulas)
    $valor_formatado = number_format($valor, 2, '', ''); // 150.00 -> 15000
    $valor_pix = str_pad($valor_formatado, 13, '0', STR_PAD_LEFT); // 0000000015000

    // ✅ GERAR TXID ÚNICO
    if (!$pedido_id) {
        $pedido_id = uniqid('YARA');
    }
    $txid = substr($pedido_id, 0, 25); // Máximo 25 caracteres

    // ✅ CONSTRUIR PAYLOAD PIX (formato EMV)
    $payload = "";

    // ID do Payload Format (00)
    $payload .= "000201";

    // Merchant Account Information (26)
    $merchant_info = "0014br.gov.bcb.pix";
    $merchant_info .= "01" . str_pad(strlen($chave_pix), 2, '0', STR_PAD_LEFT) . $chave_pix;
    $payload .= "26" . str_pad(strlen($merchant_info), 2, '0', STR_PAD_LEFT) . $merchant_info;

    // Merchant Category Code (52) - 0000 = Geral
    $payload .= "52040000";

    // Transaction Currency (53) - 986 = Real Brasileiro
    $payload .= "5303986";

    // Transaction Amount (54)
    $payload .= "54" . str_pad(strlen($valor_pix), 2, '0', STR_PAD_LEFT) . $valor_pix;

    // Country Code (58) - BR = Brasil
    $payload .= "5802BR";

    // Merchant Name (59)
    $payload .= "59" . str_pad(strlen($merchant_name), 2, '0', STR_PAD_LEFT) . $merchant_name;

    // Merchant City (60)
    $payload .= "60" . str_pad(strlen($merchant_city), 2, '0', STR_PAD_LEFT) . $merchant_city;

    // Additional Data Field (62)
    $txid_field = "05" . str_pad(strlen($txid), 2, '0', STR_PAD_LEFT) . $txid;
    $additional_data = $txid_field;
    $payload .= "62" . str_pad(strlen($additional_data), 2, '0', STR_PAD_LEFT) . $additional_data;

    // CRC16 (63)
    $payload .= "6304";

    // ✅ CALCULAR CRC16
    $crc = crc16($payload);
    $payload .= $crc;

    return $payload;
}

/**
 * Calcula CRC16 para o payload PIX
 * @param string $str String para calcular CRC
 * @return string CRC16 em hexadecimal
 */
function crc16($str)
{
    $crc = 0xFFFF;
    $strlen = strlen($str);

    for ($c = 0; $c < $strlen; $c++) {
        $crc ^= ord($str[$c]) << 8;
        for ($i = 0; $i < 8; $i++) {
            if ($crc & 0x8000) {
                $crc = ($crc << 1) ^ 0x1021;
            } else {
                $crc = $crc << 1;
            }
        }
    }

    $crc = $crc & 0xFFFF;
    return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
}

/**
 * Valida se um código PIX está no formato correto
 * @param string $pix_code Código PIX a validar
 * @return bool True se válido, False se inválido
 */
function validarCodigoPix($pix_code)
{
    if (empty($pix_code) || strlen($pix_code) < 50) {
        return false;
    }

    // Verificar se começa com 000201
    if (strpos($pix_code, '000201') !== 0) {
        return false;
    }

    // Verificar CRC16
    $payload_sem_crc = substr($pix_code, 0, -4);
    $crc_informado = substr($pix_code, -4);
    $crc_calculado = crc16($payload_sem_crc);

    return $crc_calculado === $crc_calculado;
}

/**
 * Processa pedido com pagamento PIX
 * @param array $dados_pedido Dados do pedido
 * @param string $codigo_pix Código PIX gerado
 * @return array Resultado do processamento
 */
function processarPedidoPix($dados_pedido, $codigo_pix)
{
    global $conexao;

    try {
        // ✅ INICIAR TRANSACTION
        $conexao->begin_transaction();

        // 1. INSERIR PEDIDO
        $sql_pedido = "INSERT INTO pedidos 
                      (usuario_id, nome_cliente, email_cliente, valor_total, forma_pagamento, endereco_entrega, status, codigo_pix) 
                      VALUES (?, ?, ?, ?, ?, ?, 'aguardando_pagamento', ?)";

        $stmt_pedido = $conexao->prepare($sql_pedido);
        $stmt_pedido->bind_param(
            "issdsss",
            $dados_pedido['usuario_id'],
            $dados_pedido['nome'],
            $dados_pedido['email'],
            $dados_pedido['total'],
            $dados_pedido['metodo_pagamento'],
            $dados_pedido['endereco'],
            $codigo_pix
        );

        if (!$stmt_pedido->execute()) {
            throw new Exception("Erro ao criar pedido: " . $stmt_pedido->error);
        }

        $pedido_id = $stmt_pedido->insert_id;

        // 2. INSERIR ITENS DO PEDIDO
        $sql_item = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
        $stmt_item = $conexao->prepare($sql_item);

        foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
            // Buscar preço atual do produto
            $sql_preco = "SELECT preco FROM produtos WHERE id = ?";
            $stmt_preco = $conexao->prepare($sql_preco);
            $stmt_preco->bind_param("i", $produto_id);
            $stmt_preco->execute();
            $result_preco = $stmt_preco->get_result();

            if ($produto = $result_preco->fetch_assoc()) {
                $preco_unitario = $produto['preco'];

                // Verificar se tem presente e adicionar ao preço
                if (isset($_SESSION['presentes'][$produto_id])) {
                    $preco_unitario += $_SESSION['presentes'][$produto_id]['preco'];
                }

                $stmt_item->bind_param("iiid", $pedido_id, $produto_id, $quantidade, $preco_unitario);
                $stmt_item->execute();
            }
        }

        // 3. INSERIR NO HISTÓRICO DE PAGAMENTO PIX
        $sql_pix = "INSERT INTO pagamentos_pix 
                   (pedido_id, codigo_pix, valor, status, data_criacao) 
                   VALUES (?, ?, ?, 'aguardando', NOW())";

        $stmt_pix = $conexao->prepare($sql_pix);
        $stmt_pix->bind_param("isd", $pedido_id, $codigo_pix, $dados_pedido['total']);
        $stmt_pix->execute();

        // ✅ COMMIT DA TRANSACTION
        $conexao->commit();

        // ✅ LIMPAR CARRINHO E PRESENTES
        unset($_SESSION['carrinho']);
        unset($_SESSION['presentes']);

        return [
            'success' => true,
            'id_pedido' => $pedido_id,
            'codigo_pix' => $codigo_pix,
            'message' => 'Pedido criado com sucesso. Aguardando pagamento PIX.'
        ];
    } catch (Exception $e) {
        // ✅ ROLLBACK EM CASO DE ERRO
        $conexao->rollback();
        error_log("Erro no processamento PIX: " . $e->getMessage());

        return [
            'success' => false,
            'message' => 'Erro ao processar pedido PIX: ' . $e->getMessage()
        ];
    }
}

/**
 * Processa pedido normal (cartão de crédito)
 * @param array $dados_pedido Dados do pedido
 * @return array Resultado do processamento
 */
function processarPedidoNormal($dados_pedido)
{
    global $conexao;

    try {
        // ✅ INICIAR TRANSACTION
        $conexao->begin_transaction();

        // 1. INSERIR PEDIDO
        $sql_pedido = "INSERT INTO pedidos 
                      (usuario_id, nome_cliente, email_cliente, valor_total, forma_pagamento, endereco_entrega, status) 
                      VALUES (?, ?, ?, ?, ?, ?, 'pendente')";

        $stmt_pedido = $conexao->prepare($sql_pedido);
        $stmt_pedido->bind_param(
            "issdss",
            $dados_pedido['usuario_id'],
            $dados_pedido['nome'],
            $dados_pedido['email'],
            $dados_pedido['total'],
            $dados_pedido['metodo_pagamento'],
            $dados_pedido['endereco']
        );

        if (!$stmt_pedido->execute()) {
            throw new Exception("Erro ao criar pedido: " . $stmt_pedido->error);
        }

        $pedido_id = $stmt_pedido->insert_id;

        // 2. INSERIR ITENS DO PEDIDO
        $sql_item = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
        $stmt_item = $conexao->prepare($sql_item);

        foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
            // Buscar preço atual do produto
            $sql_preco = "SELECT preco FROM produtos WHERE id = ?";
            $stmt_preco = $conexao->prepare($sql_preco);
            $stmt_preco->bind_param("i", $produto_id);
            $stmt_preco->execute();
            $result_preco = $stmt_preco->get_result();

            if ($produto = $result_preco->fetch_assoc()) {
                $preco_unitario = $produto['preco'];

                // Verificar se tem presente e adicionar ao preço
                if (isset($_SESSION['presentes'][$produto_id])) {
                    $preco_unitario += $_SESSION['presentes'][$produto_id]['preco'];
                }

                $stmt_item->bind_param("iiid", $pedido_id, $produto_id, $quantidade, $preco_unitario);
                $stmt_item->execute();
            }
        }

        // ✅ COMMIT DA TRANSACTION
        $conexao->commit();

        // ✅ LIMPAR CARRINHO E PRESENTES
        unset($_SESSION['carrinho']);
        unset($_SESSION['presentes']);

        return [
            'success' => true,
            'id_pedido' => $pedido_id,
            'message' => 'Pedido realizado com sucesso!'
        ];
    } catch (Exception $e) {
        // ✅ ROLLBACK EM CASO DE ERRO
        $conexao->rollback();
        error_log("Erro no processamento do pedido: " . $e->getMessage());

        return [
            'success' => false,
            'message' => 'Erro ao processar pedido: ' . $e->getMessage()
        ];
    }
}

// ==================== FUNÇÕES DE USUÁRIO ====================

function fazerLogout()
{
    $_SESSION['usuario'] = null;
    $_SESSION['admin_mode'] = false;
    session_regenerate_id(true);
    return true;
}

function fazerLogin($email, $senha)
{
    global $conexao;

    // ✅ VALIDAÇÃO DE INPUT
    $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    $senha = trim($senha);

    if (!$email || empty($senha)) {
        return false;
    }

    // ✅ PREPARED STATEMENT
    $sql = "SELECT id, nome, email, senha, foto, is_admin FROM usuarios WHERE email = ? AND ativo = 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // ✅ VERIFICAÇÃO SEGURA DE SENHA
        $senha_valida = false;

        // Se for admin com senha hardcoded (REMOVER ISSO DEPOIS)
        if ($email === 'admin@gmail.com' && $senha === 'admin1234') {
            $senha_valida = true;
        }
        // Verificar hash bcrypt
        elseif (password_verify($senha, $usuario['senha'])) {
            $senha_valida = true;
        }
        // Migração: se senha estiver em texto, criar hash
        elseif ($senha === $usuario['senha']) {
            // Migrar para hash
            $novo_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql_update = "UPDATE usuarios SET senha = ? WHERE id = ?";
            $stmt_update = $conexao->prepare($sql_update);
            $stmt_update->bind_param("si", $novo_hash, $usuario['id']);
            $stmt_update->execute();
            $senha_valida = true;
        }

        if ($senha_valida) {
            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nome' => $usuario['nome'],
                'email' => $usuario['email'],
                'foto' => $usuario['foto'],
                'is_admin' => $usuario['is_admin'] ?? 0
            ];

            // ✅ REGENERAR SESSION ID
            session_regenerate_id(true);
            return true;
        }
    }
    return false;
}

function cadastrarUsuario($nome, $email, $senha, $foto = null)
{
    global $conexao;

    // ✅ VALIDAÇÃO
    $nome = trim($nome);
    $email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    $senha = trim($senha);

    if (empty($nome) || !$email || !validarSenha($senha)) {
        return "Dados inválidos! Verifique nome, email e senha (mínimo 8 caracteres).";
    }

    // ✅ VERIFICAR EMAIL DUPLICADO
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return "Email já cadastrado!";
    }

    // ✅ UPLOAD SEGURO DE FOTO
    $foto_nome = null;
    if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
        // Validar tipo de arquivo
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $tipo_arquivo = mime_content_type($foto['tmp_name']);

        if (!in_array($tipo_arquivo, $tipos_permitidos)) {
            return "Tipo de arquivo não permitido. Use JPEG, PNG ou GIF.";
        }

        // Validar tamanho (max 2MB)
        if ($foto['size'] > 2 * 1024 * 1024) {
            return "Arquivo muito grande. Máximo 2MB.";
        }

        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true); // ✅ Permissões seguras
        }

        $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $foto_nome = uniqid() . '.' . $extensao;
        $caminho_destino = 'uploads/' . $foto_nome;

        if (!move_uploaded_file($foto['tmp_name'], $caminho_destino)) {
            return "Erro ao fazer upload da foto.";
        }
    }

    // ✅ HASH DE SENHA
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // ✅ INSERIR COM PREPARED STATEMENT
    $sql = "INSERT INTO usuarios (nome, email, senha, foto) VALUES (?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $senha_hash, $foto_nome);

    if ($stmt->execute()) {
        $_SESSION['usuario'] = [
            'id' => $stmt->insert_id,
            'nome' => $nome,
            'email' => $email,
            'foto' => $foto_nome,
            'is_admin' => 0 // Sempre usuário comum
        ];

        session_regenerate_id(true);
        return true;
    }
    return "Erro ao cadastrar usuário!";
}

// ==================== FUNÇÕES DE ADMIN ====================

function verificarAdmin()
{
    if (!isset($_SESSION['usuario']) || !$_SESSION['usuario'] || ($_SESSION['usuario']['is_admin'] ?? 0) != 1) {
        header('Location: login.php');
        exit();
    }
    return true;
}

function entrarModoAdmin()
{
    $_SESSION['admin_mode'] = true;
    $_SESSION['admin_return_url'] = 'admin/dashboard.php';
    return true;
}

function sairModoAdmin()
{
    $_SESSION['admin_mode'] = false;
    unset($_SESSION['admin_return_url']);
    return true;
}

function isModoAdmin()
{
    return isset($_SESSION['admin_mode']) && $_SESSION['admin_mode'] === true;
}

// ==================== FUNÇÕES DE PRODUTOS E PAGINAÇÃO ====================

function getProdutosPaginados($categoria, $paginaAtual = 1, $itensPorPagina = 8)
{
    global $conexao;

    // ✅ VALIDAÇÃO
    $paginaAtual = max(1, (int)$paginaAtual);
    $itensPorPagina = max(1, (int)$itensPorPagina);
    $offset = ($paginaAtual - 1) * $itensPorPagina;

    // ✅ COUNT COM PREPARED STATEMENT
    $sqlCount = "SELECT COUNT(*) as total FROM produtos WHERE categoria = ? AND disponivel = 1";
    $stmtCount = $conexao->prepare($sqlCount);
    $stmtCount->bind_param("s", $categoria);
    $stmtCount->execute();
    $resultadoCount = $stmtCount->get_result()->fetch_assoc();
    $totalProdutos = $resultadoCount['total'];

    $totalPaginas = ceil($totalProdutos / $itensPorPagina);

    // ✅ SELECT COM PREPARED STATEMENT
    $sql = "SELECT * FROM produtos WHERE categoria = ? AND disponivel = 1 LIMIT ? OFFSET ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sii", $categoria, $itensPorPagina, $offset);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $produtos = [];
    while ($produto = $resultado->fetch_assoc()) {
        $produtos[] = $produto;
    }

    return [
        'produtos' => $produtos,
        'total_paginas' => $totalPaginas,
        'pagina_atual' => $paginaAtual,
        'total_produtos' => $totalProdutos
    ];
}

function getProdutosDestaqueDB()
{
    global $conexao;
    // ✅ PREPARED STATEMENT
    $sql = "SELECT * FROM produtos WHERE destaque = 1 AND disponivel = 1 LIMIT 8";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $produtos = [];
    while ($produto = $resultado->fetch_assoc()) {
        $produtos[] = $produto;
    }
    return $produtos;
}

function getProdutosPorCategoria($categoria, $limite = 50)
{
    global $conexao;
    // ✅ PREPARED STATEMENT
    $sql = "SELECT * FROM produtos WHERE categoria = ? AND disponivel = 1 LIMIT ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("si", $categoria, $limite);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $produtos = [];
    while ($produto = $resultado->fetch_assoc()) {
        $produtos[] = $produto;
    }
    return $produtos;
}

function buscarProdutos($termo)
{
    global $conexao;
    // ✅ PREPARED STATEMENT
    $sql = "SELECT * FROM produtos WHERE (nome LIKE ? OR descricao LIKE ?) AND disponivel = 1 LIMIT 10";
    $stmt = $conexao->prepare($sql);
    $termoBusca = "%" . $termo . "%";
    $stmt->bind_param("ss", $termoBusca, $termoBusca);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $produtos = [];
    while ($produto = $resultado->fetch_assoc()) {
        $produtos[] = $produto;
    }
    return $produtos;
}

// ✅ FUNÇÃO CONSISTENTE
function getProdutosDestaque()
{
    return getProdutosDestaqueDB(); // Usa a função segura
}

// ==================== FUNÇÕES DE CARRINHO ====================

function adicionarAoCarrinho($produtoId, $quantidade = 1)
{
    $produtoId = (int)$produtoId;
    $quantidade = max(1, (int)$quantidade);

    if (isset($_SESSION['carrinho'][$produtoId])) {
        $_SESSION['carrinho'][$produtoId] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produtoId] = $quantidade;
    }
    return true;
}

function removerDoCarrinho($produtoId)
{
    $produtoId = (int)$produtoId;
    if (isset($_SESSION['carrinho'][$produtoId])) {
        unset($_SESSION['carrinho'][$produtoId]);
        return true;
    }
    return false;
}

function atualizarCarrinho($produtoId, $quantidade)
{
    $produtoId = (int)$produtoId;
    $quantidade = (int)$quantidade;

    if ($quantidade <= 0) {
        return removerDoCarrinho($produtoId);
    } else {
        $_SESSION['carrinho'][$produtoId] = $quantidade;
        return true;
    }
}

// ==================== FUNÇÕES DE FAVORITOS (BANCO DE DADOS) ====================

function adicionarFavorito($usuarioId, $produtoId)
{
    global $conexao;
    $usuarioId = (int)$usuarioId;
    $produtoId = (int)$produtoId;

    $sql = "INSERT IGNORE INTO favoritos (usuario_id, produto_id) VALUES (?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $usuarioId, $produtoId);
    return $stmt->execute();
}

function removerFavorito($usuarioId, $produtoId)
{
    global $conexao;
    $usuarioId = (int)$usuarioId;
    $produtoId = (int)$produtoId;

    $sql = "DELETE FROM favoritos WHERE usuario_id = ? AND produto_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $usuarioId, $produtoId);
    return $stmt->execute();
}

function isFavoritoUsuario($usuarioId, $produtoId)
{
    global $conexao;
    $usuarioId = (int)$usuarioId;
    $produtoId = (int)$produtoId;

    $sql = "SELECT id FROM favoritos WHERE usuario_id = ? AND produto_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $usuarioId, $produtoId);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function getFavoritosUsuario($usuarioId)
{
    global $conexao;
    $usuarioId = (int)$usuarioId;

    $sql = "SELECT p.* FROM produtos p 
            INNER JOIN favoritos f ON p.id = f.produto_id 
            WHERE f.usuario_id = ? 
            ORDER BY f.criado_em DESC";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuarioId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $produtos = [];
    while ($produto = $resultado->fetch_assoc()) {
        $produtos[] = $produto;
    }
    return $produtos;
}

// Wrapper para compatibilidade com código antigo (ex: produtos.php)
function isFavorito($produtoId)
{
    if (isset($_SESSION['usuario']['id'])) {
        return isFavoritoUsuario($_SESSION['usuario']['id'], $produtoId);
    }
    return false;
}


// ==================== FUNÇÕES DE ENDEREÇO ====================

function buscarEnderecoPorCEP($cep)
{
    $cep = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep) !== 8) {
        return json_encode(['success' => false, 'message' => 'CEP inválido']);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/{$cep}/json/");
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

// Funções auxiliares de endereço (mantidas do seu original)
function adicionarEndereco($dados)
{
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
    if (empty($_SESSION['enderecos']) || $novoEndereco['principal']) {
        $novoEndereco['principal'] = true;
        foreach ($_SESSION['enderecos'] as &$endereco) {
            $endereco['principal'] = false;
        }
    }
    $_SESSION['enderecos'][] = $novoEndereco;
    return $novoEndereco;
}

function editarEndereco($id, $dados)
{
    if (!isset($_SESSION['enderecos'])) return false;
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
            $endereco['complemento'] = $dados['complemento'];
            $endereco['pais'] = $dados['pais'];

            if ($dados['principal'] ?? false) {
                $endereco['principal'] = true;
                foreach ($_SESSION['enderecos'] as &$end) {
                    if ($end['id'] != $id) $end['principal'] = false;
                }
            }
            return true;
        }
    }
    return false;
}

function excluirEndereco($id)
{
    if (!isset($_SESSION['enderecos'])) return false;
    $_SESSION['enderecos'] = array_filter($_SESSION['enderecos'], function ($endereco) use ($id) {
        return $endereco['id'] != $id;
    });
    return true;
}

function definirEnderecoPrincipal($id)
{
    if (!isset($_SESSION['enderecos'])) return false;
    foreach ($_SESSION['enderecos'] as &$endereco) {
        $endereco['principal'] = ($endereco['id'] == $id);
    }
    return true;
}
