<?php
// conexao.php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "yara_joias";

// Criar conexão
$conexao = new mysqli($servidor, $usuario, $senha, $banco);

// Verificar conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Definir charset
$conexao->set_charset("utf8");
?>