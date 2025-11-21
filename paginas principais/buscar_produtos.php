<?php
// buscar_produtos.php
require_once 'funcoes.php';

header('Content-Type: application/json');

$termo = $_GET['termo'] ?? '';

if (strlen($termo) > 2) {
    try {
        $produtos = buscarProdutos($termo);
        echo json_encode([
            'success' => true, 
            'produtos' => $produtos,
            'termo' => $termo
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'produtos' => [],
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'produtos' => [],
        'message' => 'Termo muito curto'
    ]);
}
?>