<?php
// processa_pedido.php
session_start();
require_once 'funcoes.php';

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit();
}

// Conectar ao banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=yara_joias;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados']);
    exit();
}

$acao = $_POST['acao'] ?? '';

switch ($acao) {
    case 'cancelar':
        cancelarPedido($pdo);
        break;

    case 'adicionar_personalizado':
            // 1. Receber dados da personalização
            $tipo = $_POST['tipo'] ?? 'Joia';
            $material = $_POST['material'] ?? '';
            $pedra = $_POST['pedra'] ?? '';
            $tamanho = $_POST['tamanho'] ?? '';
            $gravacao = $_POST['gravacao'] ?? '';
            $preco = (float)$_POST['preco']; 

            // 2. Montar Nome e Descrição Dinâmicos
            $nomeProduto = "Personalizado: " . ucfirst($tipo);
            
            $descricao = "Joia exclusiva personalizada.\n";
            $descricao .= "Material: " . ucfirst($material) . "\n";
            $descricao .= "Pedra: " . ucfirst($pedra) . "\n";
            $descricao .= "Tamanho: " . $tamanho . "\n";
            if(!empty($gravacao)) {
                $descricao .= "Gravação: " . $gravacao;
            }

            // 3. Definir categoria para o banco (Mapeando para os ENUMs que existem)
            $mapCategoria = [
                'Anel' => 'aneis', 'Colar' => 'colares', 'Brinco' => 'brincos', 
                'Pulseira' => 'pulseiras', 'Bracelete' => 'braceletes'
            ];
            $categoriaDb = $mapCategoria[$tipo] ?? 'colares'; // Fallback

            // 4. Salvar como um novo produto no Banco
            // Usamos uma imagem genérica de personalização
            $imagem = "personalize-base.png"; 
            
            $sql = "INSERT INTO produtos (nome, descricao, preco, categoria, imagem, estoque, disponivel) VALUES (?, ?, ?, ?, ?, 1, 1)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssdss", $nomeProduto, $descricao, $preco, $categoriaDb, $imagem);
            
            if ($stmt->execute()) {
                $novoId = $stmt->insert_id;
                
                // 5. Adicionar esse novo ID ao carrinho
                adicionarAoCarrinho($novoId);
                
                $total_itens = array_sum($_SESSION['carrinho']);
                echo json_encode(['success' => true, 'total_carrinho' => $total_itens]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao criar personalização: ' . $stmt->error]);
            }
            break;
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}

function cancelarPedido($pdo) {
    $pedido_id = $_POST['pedido_id'] ?? '';
    
    if (empty($pedido_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do pedido não informado']);
        return;
    }
    
    try {
        // Verificar se o pedido pertence ao usuário logado
        $stmt = $pdo->prepare("SELECT usuario_id, status FROM pedidos WHERE id = ?");
        $stmt->execute([$pedido_id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pedido) {
            echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
            return;
        }
        
        if ($pedido['usuario_id'] != $_SESSION['usuario']['id']) {
            echo json_encode(['success' => false, 'message' => 'Acesso negado']);
            return;
        }
        
        if ($pedido['status'] != 'pendente') {
            echo json_encode(['success' => false, 'message' => 'Só é possível cancelar pedidos pendentes']);
            return;
        }
        
        // Atualizar status do pedido
        $stmt = $pdo->prepare("UPDATE pedidos SET status = 'cancelado' WHERE id = ?");
        $stmt->execute([$pedido_id]);
        
        echo json_encode(['success' => true, 'message' => 'Pedido cancelado com sucesso']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao cancelar pedido: ' . $e->getMessage()]);
    }
}
?>