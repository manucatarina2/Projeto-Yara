<?php
// test_cart.php
// Simula uma requisição POST para processa_form.php

// Configura o ambiente para capturar a saída
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['acao'] = 'adicionar_carrinho';
$_POST['produto_id'] = 1; // ID de exemplo

// Captura o buffer de saída
ob_start();
require 'processa_form.php';
$output = ob_get_clean();

echo "--- RAW OUTPUT START ---\n";
echo $output;
echo "\n--- RAW OUTPUT END ---\n";

// Tenta decodificar JSON
$json = json_decode($output, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "JSON Valid: YES\n";
    print_r($json);
} else {
    echo "JSON Valid: NO\n";
    echo "Error: " . json_last_error_msg() . "\n";
}
