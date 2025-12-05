<?php
// personalizado_salvar.php - Versão simplificada
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

session_start();

// Verificar login
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Você precisa estar logado!']);
    exit;
}

// Verificar conexão
require_once 'conexao.php';

if (!$conexao) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados.']);
    exit;
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// Capturar dados
$tipo = $_POST['tipo'] ?? '';
$material = $_POST['material'] ?? '';
$pedra = $_POST['pedra'] ?? '';
$tamanho = $_POST['tamanho'] ?? '';
$gravacao = $_POST['gravacao'] ?? '';
$preco = $_POST['preco'] ?? 0;
$usuario_id = $_SESSION['usuario']['id'] ?? 0;

// Validar dados
if (empty($tipo) || empty($material) || empty($pedra)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
    exit;
}

// Gerar nome da imagem
$tipo_lower = strtolower($tipo);
$material_lower = ($material == 'Ouro 18k') ? 'dourado' : 'prata';
$pedra_lower = ($pedra == 'Liso') ? 'liso' : strtolower($pedra);

// Tentar encontrar a imagem
$imagem_final = 'imgs/produto-padrao.png';
$extensoes = ['.png', '.jpg', '.jpeg'];

foreach ($extensoes as $ext) {
    $caminho_teste = "imgs/{$tipo_lower}-{$material_lower}-{$pedra_lower}{$ext}";
    if (file_exists($caminho_teste)) {
        $imagem_final = $caminho_teste;
        break;
    }
}

try {
    // Salvar no banco
    $stmt = $conexao->prepare("INSERT INTO pedidos_personalizados 
                              (usuario_id, tipo_joia, material, pedra, tamanho, gravacao, preco_total, imagem_preview) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("isssssds", 
        $usuario_id, 
        $tipo, 
        $material, 
        $pedra, 
        $tamanho, 
        $gravacao, 
        $preco, 
        $imagem_final
    );
    
    if ($stmt->execute()) {
        $pedido_id = $stmt->insert_id;
        
        // Adicionar ao carrinho (ID temporário negativo)
        $produto_temp_id = -$pedido_id; // Usar ID negativo baseado no pedido
        
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }
        
        $_SESSION['carrinho'][$produto_temp_id] = 1;
        
        // Salvar detalhes da personalização
        if (!isset($_SESSION['personalizados'])) {
            $_SESSION['personalizados'] = [];
        }
        
        $_SESSION['personalizados'][$produto_temp_id] = [
            'descricao' => "{$tipo} {$material} com {$pedra}",
            'preco_override' => $preco,
            'texto' => "Personalizado: {$tipo}/{$material}/{$pedra}/Tamanho:{$tamanho}" . ($gravacao ? "/Gravação:{$gravacao}" : ""),
            'pedido_id' => $pedido_id
        ];
        
        // Calcular total do carrinho
        $total_carrinho = array_sum($_SESSION['carrinho']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Joia personalizada adicionada ao carrinho!',
            'pedido_id' => $pedido_id,
            'total_carrinho' => $total_carrinho
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao salvar no banco de dados: ' . $stmt->error
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ]);
}
?>