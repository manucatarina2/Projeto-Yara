<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui a conexão com o banco de dados
require_once 'conexao.php';

// Tenta incluir funcoes.php se existir, para usar validações extras se necessário,
// mas o código abaixo foi feito para rodar mesmo sem ele na parte visual.
if (file_exists('funcoes.php')) {
    require_once 'funcoes.php';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia de Presentes | Yara Joias</title>
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    <!-- Scripts para funcionalidade dos botões (Carrinho/Favoritos) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js" defer></script>

    <style>
        /* --- Estilos Gerais --- */
        body {
            background-color: #fff; 
        }

        /* Banner Principal */
        .gift-header {
            background-color: var(--bg, #ffe7f6);
            padding: 80px 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .gift-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3.5em;
            color: #000;
            margin-bottom: 15px;
            font-weight: 300;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .gift-header p {
            font-family: 'Lato', sans-serif;
            color: #333;
            font-size: 1.2em;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Seções */
        .gift-section {
            padding: 50px 20px;
            border-bottom: 1px solid #eee;
        }

        .gift-section:last-of-type {
            border-bottom: none;
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.5em;
            color: var(--accent, #e91e63);
            margin-bottom: 10px;
        }

        .gift-tip {
            display: inline-block;
            background-color: #fff;
            border: 1px solid var(--accent, #e91e63);
            color: #555;
            padding: 10px 25px;
            border-radius: 50px;
            font-family: 'Lato', sans-serif;
            font-size: 0.95em;
            margin-top: 10px;
        }

        /* --- PADRONIZAÇÃO DOS CARDS (Igual colares.php) --- */
        
        .colecao-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .produto-card {
            background: #fff;
            padding: 20px;
            width: 300px;
            text-align: center;
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .produto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .produto-card img.produto-img {
            width: auto;
            max-width: 100%; /* ADICIONADO: Impede que a imagem ultrapasse a largura do card */
            height: 250px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .produto-card h3 {
            margin: 10px 0;
            min-height: 40px;
            font-size: 16px;
            font-family: 'Lato', sans-serif;
            color: #000;
        }

        /* Estilo do Preço */
        .produto-card p.preco-destaque {
            color: #e91e7d; 
            font-weight: bold; 
            margin-bottom: 10px;
        }

        /* Botão Comprar */
        .produto-card button {
            background-color: #e91e7d;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: 0.3s;
            margin-top: auto; /* Empurra para o fundo se o card esticar */
        }

        .produto-card button:hover {
            background-color: #e02192;
        }

        /* Ícone Favorito */
        .favorito {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            width: 24px;
            transition: filter 0.3s ease;
        }

        .favorito.ativo {
            filter: invert(27%) sepia(51%) saturate(2878%) hue-rotate(346deg) brightness(104%) contrast(97%);
        }
    </style>
</head>
<body>

    <!-- Inclui o Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Cabeçalho da Página -->
    <section class="gift-header">
        <h1>Guia de Presentes</h1>
        <p>Transforme momentos especiais em memórias eternas. Selecionamos nossas peças favoritas para ajudar você a encontrar a joia perfeita.</p>
    </section>

    <!-- SEÇÃO 1: PARA NAMORADA -->
    <section class="gift-section">
        <div class="section-header">
            <h2 class="section-title">Para a Namorada</h2>
            <div class="gift-tip">
                💡 <strong>Dica:</strong> Aposte no romantismo com anéis delicados ou símbolos de amor eterno.
            </div>
        </div>
        
        <div class="colecao-container">
            <?php
            $sql_namorada = "SELECT * FROM produtos 
                             WHERE (nome LIKE '%anel%' OR nome LIKE '%coracao%' OR nome LIKE '%aliança%' OR categoria LIKE '%anel%') 
                             AND disponivel = 1 
                             ORDER BY RAND() LIMIT 4";
            
            if (isset($conexao)) {
                $result = $conexao->query($sql_namorada);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $nome = $row['nome'];
                        $preco = number_format($row['preco'], 2, ',', '.');
                        $imagem = $row['imagem'];
                        // Verifica favorito se a função existir, senão vazio
                        $isFav = (function_exists('isFavorito') && isFavorito($id)) ? 'ativo' : '';

                        echo "
                        <div class='produto-card' onclick=\"window.location.href='produto_detalhe.php?id={$id}'\">
                            
                            <img src='imgs/{$imagem}' alt='{$nome}' class='produto-img' onerror=\"this.src='imgs/sem-foto.jpg'\">
                            
                            <img src='imgs/coracao.png' 
                                 alt='Curtir' 
                                 class='favorito {$isFav}' 
                                 onclick='event.stopPropagation(); toggleFavorito(event, this, {$id})'>

                            <h3>{$nome}</h3>

                            <p class='preco-destaque'>R$ {$preco}</p>

                            <button type='button' onclick='event.stopPropagation(); adicionarAoCarrinho({$id})'>
                                Adicionar ao Carrinho
                            </button>
                        </div>
                        ";
                    }
                } else {
                    echo "<p style='text-align:center; width:100%;'>Nenhuma sugestão encontrada.</p>";
                }
            }
            ?>
        </div>
    </section>

    <!-- SEÇÃO 2: PARA MÃE -->
    <section class="gift-section" style="background-color: #fafafa;">
        <div class="section-header">
            <h2 class="section-title">Para a Mãe</h2>
            <div class="gift-tip">
                💡 <strong>Dica:</strong> Conjuntos clássicos e pérolas representam sofisticação e carinho.
            </div>
        </div>

        <div class="colecao-container">
            <?php
            $sql_mae = "SELECT * FROM produtos 
                        WHERE (nome LIKE '%conjunto%' OR nome LIKE '%colar%' OR nome LIKE '%pérola%' OR categoria LIKE '%colar%') 
                        AND disponivel = 1 
                        ORDER BY RAND() LIMIT 4";
            
            if (isset($conexao)) {
                $result = $conexao->query($sql_mae);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $nome = $row['nome'];
                        $preco = number_format($row['preco'], 2, ',', '.');
                        $imagem = $row['imagem'];
                        $isFav = (function_exists('isFavorito') && isFavorito($id)) ? 'ativo' : '';

                        echo "
                        <div class='produto-card' onclick=\"window.location.href='produto_detalhe.php?id={$id}'\">
                            
                            <img src='imgs/{$imagem}' alt='{$nome}' class='produto-img' onerror=\"this.src='imgs/sem-foto.jpg'\">
                            
                            <img src='imgs/coracao.png' 
                                 alt='Curtir' 
                                 class='favorito {$isFav}' 
                                 onclick='event.stopPropagation(); toggleFavorito(event, this, {$id})'>

                            <h3>{$nome}</h3>

                            <p class='preco-destaque'>R$ {$preco}</p>

                            <button type='button' onclick='event.stopPropagation(); adicionarAoCarrinho({$id})'>
                                Adicionar ao Carrinho
                            </button>
                        </div>
                        ";
                    }
                }
            }
            ?>
        </div>
    </section>

    <!-- SEÇÃO 3: PARA AMIGA -->
    <section class="gift-section">
        <div class="section-header">
            <h2 class="section-title">Para a Melhor Amiga</h2>
            <div class="gift-tip">
                💡 <strong>Dica:</strong> Pulseiras e brincos modernos para quem está sempre ao seu lado.
            </div>
        </div>

        <div class="colecao-container">
            <?php
            $sql_amiga = "SELECT * FROM produtos 
                          WHERE (nome LIKE '%pulseira%' OR nome LIKE '%brinco%' OR categoria LIKE '%pulseira%') 
                          AND disponivel = 1 
                          ORDER BY RAND() LIMIT 4";
            
            if (isset($conexao)) {
                $result = $conexao->query($sql_amiga);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $nome = $row['nome'];
                        $preco = number_format($row['preco'], 2, ',', '.');
                        $imagem = $row['imagem'];
                        $isFav = (function_exists('isFavorito') && isFavorito($id)) ? 'ativo' : '';

                        echo "
                        <div class='produto-card' onclick=\"window.location.href='produto_detalhe.php?id={$id}'\">
                            
                            <img src='imgs/{$imagem}' alt='{$nome}' class='produto-img' onerror=\"this.src='imgs/sem-foto.jpg'\">
                            
                            <img src='imgs/coracao.png' 
                                 alt='Curtir' 
                                 class='favorito {$isFav}' 
                                 onclick='event.stopPropagation(); toggleFavorito(event, this, {$id})'>

                            <h3>{$nome}</h3>

                            <p class='preco-destaque'>R$ {$preco}</p>

                            <button type='button' onclick='event.stopPropagation(); adicionarAoCarrinho({$id})'>
                                Adicionar ao Carrinho
                            </button>
                        </div>
                        ";
                    }
                }
            }
            ?>
        </div>
    </section>

    <!-- Botão Ver Tudo -->
    <div style="text-align: center; margin: 60px 0;">
        <a href="produtos.php" style="
            display: inline-block;
            background-color: var(--accent, #e91e63);
            color: #fff;
            padding: 15px 50px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 1.1em;
            transition: background 0.3s;">
            Ver Todos os Produtos
        </a>
    </div>

    <?php include 'footer.php'; ?>

</body>
</html>