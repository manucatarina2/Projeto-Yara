<?php
require_once 'conexao.php';

$sql = "CREATE TABLE IF NOT EXISTS favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    produto_id INT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorito (usuario_id, produto_id)
)";

if ($conexao->query($sql) === TRUE) {
    echo "Tabela 'favoritos' criada com sucesso!";
} else {
    echo "Erro ao criar tabela: " . $conexao->error;
}
