<?php
// produtos.php - VERSÃO ORGANIZADA
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexao.php';
require_once 'funcoes.php';

// === 1. CAPTURA DE FILTROS (CORRIGIDO) ===
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$itensPorPagina = 12;
$offset = ($pagina - 1) * $itensPorPagina;

// Captura Básica
$termo = $_GET['busca'] ?? '';
$categoria = $_GET['categoria'] ?? '';
$ordem = $_GET['ordenar'] ?? 'relevancia';

// CORREÇÃO AQUI: Usamos !empty() para ignorar campos vazios
$min_preco = !empty($_GET['min_preco']) ? (float)$_GET['min_preco'] : null;
$max_preco = !empty($_GET['max_preco']) ? (float)$_GET['max_preco'] : null;
$tamanho   = !empty($_GET['tamanho'])   ? (float)$_GET['tamanho']   : null;
$peso      = !empty($_GET['peso'])      ? (float)$_GET['peso']      : null;

// === 2. CONSTRUÇÃO DA QUERY ===
$where = "WHERE disponivel = 1";
$params = [];
$types = "";

// Busca por Nome
if (!empty($termo)) {
    $where .= " AND (nome LIKE ? OR descricao LIKE ?)";
    $termoLike = "%$termo%";
    $params[] = $termoLike;
    $params[] = $termoLike;
    $types .= "ss";
}

// Categoria
if (!empty($categoria)) {
    $where .= " AND categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}

// Filtro Preço Mínimo
if ($min_preco !== null) {
    $where .= " AND preco >= ?";
    $params[] = $min_preco;
    $types .= "d";
}

// Filtro Preço Máximo
if ($max_preco !== null) {
    $where .= " AND preco <= ?";
    $params[] = $max_preco;
    $types .= "d";
}

// Filtro Tamanho (comprimento_cm)
if ($tamanho !== null) {
    $where .= " AND comprimento_cm BETWEEN ? AND ?";
    $params[] = $tamanho - 2;
    $params[] = $tamanho + 2;
    $types .= "dd";
}

// Filtro Peso (peso_gramas)
if ($peso !== null) {
    $where .= " AND peso_gramas <= ?";
    $params[] = $peso;
    $types .= "d";
}

// Ordenação
$orderBy = "ORDER BY destaque DESC, id DESC";
if ($ordem == 'preco-crescente') $orderBy = "ORDER BY preco ASC";
if ($ordem == 'preco-decrescente') $orderBy = "ORDER BY preco DESC";
if ($ordem == 'nome') $orderBy = "ORDER BY nome ASC";

// === 3. CONTAGEM TOTAL ===
$sqlCount = "SELECT COUNT(*) as total FROM produtos $where";
$stmtCount = $conexao->prepare($sqlCount);
if (!empty($params)) {
    $stmtCount->bind_param($types, ...$params);
}
$stmtCount->execute();
$totalProdutos = $stmtCount->get_result()->fetch_assoc()['total'];
$totalPaginas = ceil($totalProdutos / $itensPorPagina);

// === 4. BUSCA PRODUTOS ===
$sqlProdutos = "SELECT * FROM produtos $where $orderBy LIMIT ? OFFSET ?";
$params[] = $itensPorPagina;
$params[] = $offset;
$types .= "ii";

$stmt = $conexao->prepare($sqlProdutos);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resultado = $stmt->get_result();

$produtos = [];
while ($p = $resultado->fetch_assoc()) {
    $produtos[] = $p;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>YARA - Coleção</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="script.js" defer></script>

    <style>
        body {
            overflow-x: hidden;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* === CONTAINER PRINCIPAL ORGANIZADO === */
        .loja-container {
            display: flex;
            max-width: 1400px;
            width: 100%;
            margin: 40px auto;
            padding: 0 30px;
            gap: 40px;
            align-items: flex-start;
            box-sizing: border-box;
        }

        .coluna-produtos {
            flex: 1;
            min-width: 0;
            box-sizing: border-box;
        }

        .coluna-filtros {
            width: 280px;
            flex-shrink: 0;
            position: sticky;
            top: 100px;
        }

        /* === GRADE DE PRODUTOS ORGANIZADA === */
        .produtos-grade {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 30px;
            width: 100%;
            box-sizing: border-box;
        }

        /* === CARD DE PRODUTO CORRIGIDO (SEM CORTE) === */
        .produto-card {
            background: #fff;
            padding: 20px;
            text-align: center;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: visible; /* MUDADO: de hidden para visible */
            cursor: pointer;
            box-sizing: border-box;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            min-height: 400px; /* Altura mínima para evitar cortes */
        }

        .produto-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
            border-color: #e91e63;
        }

        .produto-card img.produto-img {
            width: 100%;
            max-width: 100%;
            height: 180px; /* Reduzido para dar mais espaço */
            object-fit: contain;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .produto-card:hover .produto-img {
            transform: scale(1.05);
        }

        .produto-card h3 {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-weight: 500;
            min-height: 44px; /* Altura fixa para título */
        }

        .produto-card .preco {
            color: #e91e63;
            font-weight: bold;
            font-size: 18px;
            margin: 15px 0;
            flex-shrink: 0; /* Impede que o preço seja espremido */
        }

        .produto-card button {
            background-color: #e91e63;
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            text-transform: uppercase;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            margin-top: auto; /* Garante que o botão fique na base */
            flex-shrink: 0; /* Impede que o botão seja espremido */
        }

        .produto-card button:hover {
            background-color: #d84a7e;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3);
        }

        /* === FILTROS ORGANIZADOS === */
        .filtro-card {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
        }

        .filtro-titulo {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e91e63;
            padding-bottom: 12px;
            color: #333;
        }

        .filtro-grupo {
            margin-bottom: 25px;
        }

        .filtro-grupo h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
            color: #555;
            letter-spacing: 0.5px;
        }

        .input-range-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .input-filtro {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .input-filtro:focus {
            outline: none;
            border-color: #e91e63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        .radio-group label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            color: #666;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .radio-group label:hover {
            background-color: #f8f8f8;
        }

        .btn-filtrar {
            width: 100%;
            padding: 15px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.3s ease;
            font-size: 14px;
            letter-spacing: 1px;
        }

        .btn-filtrar:hover {
            background-color: #e91e63;
            transform: translateY(-2px);
        }

        .btn-limpar {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #888;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .btn-limpar:hover {
            color: #e91e63;
        }

        /* === FAVORITOS ORGANIZADOS === */
        .favorito {
            position: absolute;
            top: 15px;
            right: 15px;
            cursor: pointer;
            width: 28px;
            height: 28px;
            transition: all 0.3s ease;
            z-index: 5;
            background: white;
            border-radius: 50%;
            padding: 5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .favorito:hover {
            transform: scale(1.1);
        }

        
        .favorito i {
            color: #000000ff;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        /* === HERO SECTION ORGANIZADA === */
        .hero-produtos {
            text-align: center;
            padding: 60px 15px 40px;
            background: linear-gradient(135deg, #fdf2f8 0%, #ffffff 100%);
        }

        .hero-produtos h1 {
            font-family: "Cormorant Garamond", serif;
            font-size: 36px;
            font-weight: 300;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0;
            color: #333;
        }

        .hero-produtos p {
            color: #666;
            margin-top: 15px;
            font-size: 16px;
        }

        /* === CONTROLES ORGANIZADOS === */
        .controles-superiores {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            color: #666;
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .controles-superiores select {
            padding: 12px 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            font-size: 14px;
            min-width: 200px;
            transition: all 0.3s ease;
        }

        .controles-superiores select:focus {
            outline: none;
            border-color: #e91e63;
            box-shadow: 0 0 0 3px rgba(233, 30, 99, 0.1);
        }

        /* === PAGINAÇÃO ORGANIZADA === */
        .paginacao {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #f0f0f0;
        }

        .pagina-link {
            padding: 12px 18px;
            border: 1px solid #e0e0e0;
            text-decoration: none;
            color: #333;
            background: white;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .pagina-link:hover {
            border-color: #e91e63;
            color: #e91e63;
            transform: translateY(-2px);
        }

        .pagina-atual {
            padding: 12px 18px;
            border: 1px solid #e91e63;
            text-decoration: none;
            color: white;
            background: #e91e63;
            border-radius: 8px;
            font-size: 14px;
        }

        /* === FOOTER UNIFORME === */
        .footer {
            background: #000;
            color: #fff;
            padding: 50px 20px 20px;
            margin-top: 80px;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-col h3,
        .footer-col h4 {
            margin-bottom: 15px;
            color: #fff;
        }

        .footer-col p {
            margin: 8px 0;
            color: #ccc;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
        }

        .footer-col ul li {
            margin-bottom: 8px;
        }

        .footer-col ul li a {
            text-decoration: none;
            color: #ccc;
            transition: 0.3s;
        }

        .footer-col ul li a:hover {
            color: #fe7db9;
        }

        .social {
            display: flex;
            gap: 15px;
        }

        .social a {
            color: #ccc;
            font-size: 18px;
            transition: 0.3s;
        }

        .social a:hover {
            color: #fe7db9;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid #fe7db9;
            margin-top: 30px;
            padding-top: 15px;
            font-size: 14px;
            color: #fe7db9;
            max-width: 1200px;
            margin: 30px auto 0;
        }

        /* === EFEITO FADE-IN (IGUAL AO INDEX) === */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Aplicar o efeito às seções principais */
        .hero-produtos,
        .loja-container,
        .filtro-card,
        .produto-card,
        .controles-superiores,
        .paginacao,
        .footer {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .hero-produtos.visible,
        .loja-container.visible,
        .filtro-card.visible,
        .produto-card.visible,
        .controles-superiores.visible,
        .paginacao.visible,
        .footer.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* === RESPONSIVIDADE === */
        @media (max-width: 1200px) {
            .produtos-grade {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 25px;
            }

            .loja-container {
                max-width: 100%;
                padding: 0 25px;
            }
        }

        @media (max-width: 992px) {
            .loja-container {
                flex-direction: column;
                gap: 30px;
            }

            .coluna-filtros {
                width: 100%;
                position: static;
                margin-bottom: 20px;
            }

            .produtos-grade {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .produtos-grade {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 20px;
            }

            .produto-card {
                min-height: 380px;
                padding: 15px;
            }

            .produto-card img.produto-img {
                height: 150px;
            }

            .produto-card h3 {
                font-size: 15px;
                min-height: 42px;
            }

            .hero-produtos h1 {
                font-size: 28px;
            }

            .loja-container {
                padding: 0 20px;
                margin: 30px auto;
            }
        }

        @media (max-width: 576px) {
            .produtos-grade {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .loja-container {
                margin: 25px auto;
                gap: 25px;
            }

            .produto-card {
                min-height: 360px;
                max-width: 100%;
            }

            .controles-superiores {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .hero-produtos {
                padding: 40px 15px 30px;
            }
        }

        /* === ESTILOS GLOBAIS === */
        * {
            box-sizing: border-box;
        }

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }
    </style>
</head>

<body>

    <!-- Navbar Padronizada -->
    <?php include 'navbar.php'; ?>

    <div class="hero-produtos fade-in">
        <h1>NOSSAS JOIAS</h1>
        <p>Encontre a peça perfeita para você.</p>
    </div>
    

    <div class="loja-container fade-in">
        <!-- FILTROS LATERAIS -->
        <aside class="coluna-filtros">
            <div class="filtro-card fade-in">
                <div class="filtro-titulo">Filtros</div>

                <form method="GET" action="produtos.php">

                    <div class="filtro-grupo">
                        <h4>Categoria</h4>
                        <div class="radio-group">
                            <label><input type="radio" name="categoria" value="" <?php echo $categoria == '' ? 'checked' : ''; ?>> Todas</label>
                            <label><input type="radio" name="categoria" value="colares" <?php echo $categoria == 'colares' ? 'checked' : ''; ?>> Colares</label>
                            <label><input type="radio" name="categoria" value="aneis" <?php echo $categoria == 'aneis' ? 'checked' : ''; ?>> Anéis</label>
                            <label><input type="radio" name="categoria" value="brincos" <?php echo $categoria == 'brincos' ? 'checked' : ''; ?>> Brincos</label>
                            <label><input type="radio" name="categoria" value="pulseiras" <?php echo $categoria == 'pulseiras' ? 'checked' : ''; ?>> Pulseiras</label>
                            <label><input type="radio" name="categoria" value="piercings" <?php echo $categoria == 'piercings' ? 'checked' : ''; ?>> Piercings</label>
                            <label><input type="radio" name="categoria" value="braceletes" <?php echo $categoria == 'braceletes' ? 'checked' : ''; ?>> Braceletes</label>
                        </div>
                    </div>

                    <div class="filtro-grupo">
                        <h4>Faixa de Preço</h4>
                        <div class="input-range-group">
                            <input type="number" name="min_preco" class="input-filtro" placeholder="Mínimo" value="<?php echo $min_preco; ?>">
                            <span>-</span>
                            <input type="number" name="max_preco" class="input-filtro" placeholder="Máximo" value="<?php echo $max_preco; ?>">
                        </div>
                    </div>

                    <div class="filtro-grupo">
                        <h4>Peso Máximo (g)</h4>
                        <input type="number" name="peso" class="input-filtro" placeholder="Ex: 5.0" step="0.1" value="<?php echo $peso; ?>">
                    </div>

                    <div class="filtro-grupo">
                        <h4>Tamanho (cm)</h4>
                        <input type="number" name="tamanho" class="input-filtro" placeholder="Ex: 18" value="<?php echo $tamanho; ?>">
                        <small style="color:#999; font-size:12px; display:block; margin-top:5px;">Busca aproximada (+- 2cm)</small>
                    </div>

                    <button type="submit" class="btn-filtrar">Aplicar Filtros</button>
                    <a href="produtos.php" class="btn-limpar">Limpar Filtros</a>
                </form>
            </div>
        </aside>

        <!-- ÁREA DE PRODUTOS -->
        <main class="coluna-produtos">
            <div class="controles-superiores fade-in">
                <span><?php echo $totalProdutos; ?> produtos encontrados</span>

                <select onchange="atualizarURL('ordenar', this.value)">
                    <option value="relevancia" <?php echo $ordem == 'relevancia' ? 'selected' : ''; ?>>Relevância</option>
                    <option value="preco-crescente" <?php echo $ordem == 'preco-crescente' ? 'selected' : ''; ?>>Preço: Menor para Maior</option>
                    <option value="preco-decrescente" <?php echo $ordem == 'preco-decrescente' ? 'selected' : ''; ?>>Preço: Maior para Menor</option>
                    <option value="nome" <?php echo $ordem == 'nome' ? 'selected' : ''; ?>>Nome A-Z</option>
                </select>
            </div>

            <div class="produtos-grade">
                <?php if (!empty($produtos)): ?>
                    <?php foreach ($produtos as $produto): ?>
                        <div class="produto-card fade-in" onclick="window.location.href='produto_detalhe.php?id=<?php echo $produto['id']; ?>'">
                            <div style="position:relative;">
                                <img src="imgs/<?php echo htmlspecialchars($produto['imagem']); ?>"
                                    class="produto-img"
                                    onerror="this.src='imgs/produto-padrao.png'">
                                <div class="favorito <?php echo isFavorito($produto['id']) ? 'ativo' : ''; ?>"
                                    onclick="toggleFavorito(event, this, <?php echo $produto['id']; ?>)">
                                    <i class="fas fa-heart"></i>
                                </div>
                            </div>

                            <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>

                            <p class="preco">
                                R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                            </p>

                            <button type="button" onclick="event.stopPropagation(); adicionarAoCarrinho(<?php echo $produto['id']; ?>)">
                                Adicionar ao Carrinho
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 80px; background: #f8f8f8; border-radius: 12px;">
                        <h3 style="font-size: 20px; color: #666; margin-bottom: 15px;">Nenhum produto encontrado</h3>
                        <p style="color: #888; margin-bottom: 25px;">Tente ajustar os filtros de busca.</p>
                        <a href="produtos.php" style="padding: 12px 30px; background: #e91e63; color: white; text-decoration: none; border-radius: 25px; display: inline-block;">Limpar Filtros</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($totalPaginas > 1): ?>
                <div class="paginacao fade-in">
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <?php if ($i == $pagina): ?>
                            <span class="pagina-atual"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="<?php echo '?pagina=' . $i . '&' . http_build_query(array_merge($_GET, ['pagina' => $i])); ?>"
                               class="pagina-link"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer class="footer fade-in">
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

    <script>
        // === EFEITO DE APARECER OS ITENS (IGUAL AO INDEX) ===
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            
            function checkVisibility() {
                elements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const windowHeight = window.innerHeight;
                    
                    if (elementTop < windowHeight * 0.8) {
                        element.classList.add('visible');
                    }
                });
            }
            
            checkVisibility();
            window.addEventListener('scroll', checkVisibility);
            
            // Animar a navbar
            const navbar = document.querySelector('.navbar-container');
            if (navbar) {
                navbar.style.opacity = '1';
                navbar.style.transform = 'translateY(0)';
            }
        });

        // === FUNÇÕES EXISTENTES ===
        function atualizarURL(key, val) {
            const url = new URL(window.location.href);
            url.searchParams.set(key, val);
            window.location.href = url.toString();
        }

        // Função favorito melhorada
        function toggleFavorito(event, element, produtoId) {
            event.stopPropagation();
            
            const isActive = element.classList.contains('ativo');
            const formData = new FormData();
            formData.append('acao', isActive ? 'remover_favorito' : 'adicionar_favorito');
            formData.append('produto_id', produtoId);
            
            fetch('processa_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    element.classList.toggle('ativo');
                    Swal.fire({
                        title: isActive ? 'Removido!' : 'Adicionado!',
                        text: isActive ? 'Removido dos favoritos' : 'Adicionado aos favoritos',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire('Erro', 'Não foi possível atualizar os favoritos', 'error');
            });
        }

        // Função adicionar ao carrinho
        function adicionarAoCarrinho(idProduto) {
            const formData = new FormData();
            formData.append('acao', 'adicionar_carrinho');
            formData.append('produto_id', idProduto);

            const button = event.target;
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adicionando...';
            button.disabled = true;

            fetch('processa_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Adicionado!',
                        text: 'Produto adicionado ao carrinho',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Atualiza contador do carrinho
                    const cartCounts = document.querySelectorAll('.cart-count');
                    cartCounts.forEach(count => {
                        if (data.total_carrinho) {
                            count.textContent = data.total_carrinho;
                        }
                    });
                } else {
                    Swal.fire('Erro', data.message || 'Erro ao adicionar produto', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire('Erro', 'Erro de conexão', 'error');
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
    </script>
</body>
</html>