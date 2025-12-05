<?php
// ATIVAR EXIBIÇÃO DE ERROS (Para debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica conexão para evitar crash
if (file_exists('conexao.php')) {
    require_once 'conexao.php';
} else {
    $conexao = null;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Personalize Já - YARA</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Estilos específicos da página Personalize */
        body {
            background-color: #ffffff;
        }
        .personalize-section { 
            padding: 40px 20px; 
            background-color: #ffffff;
        }
        .personalize-header { 
            text-align: center; 
            margin-bottom: 40px; 
        }
        .personalize-header h1 { 
            font-family: "Cormorant Garamond", serif; 
            font-size: 36px; 
            margin-bottom: 10px; 
            font-weight: 300; 
            letter-spacing: 2px; 
            color: #000;
        }
        .personalize-header p { 
            font-size: 16px; 
            color: #666; 
            max-width: 600px; 
            margin: 0 auto; 
        }

        .personalize-container { 
            display: flex; 
            gap: 40px; 
            max-width: 1200px; 
            margin: 0 auto; 
            flex-wrap: wrap; 
            align-items: flex-start; 
        }
        
        /* Preview (Esquerda) */
        .preview-section { 
            flex: 1; 
            min-width: 300px; 
            position: sticky; 
            top: 100px; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
        }
        .preview-image { 
            width: 100%; 
            max-width: 400px; 
            height: 400px; 
            background-color: #f8f8f8; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-bottom: 20px; 
            border-radius: 8px; 
            overflow: hidden;
            border: 1px solid #eee;
        }
        .preview-image img { 
            max-width: 100%; 
            max-height: 100%; 
            object-fit: contain; 
            transition: all 0.5s ease; 
        }
        
        .preview-details { 
            width: 100%; 
            max-width: 400px; 
            background-color: #ffffff; 
            padding: 25px; 
            border-radius: 8px; 
            border: 1px solid #eee;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .preview-details h3 { 
            font-size: 18px; 
            margin-bottom: 15px; 
            font-weight: 500; 
            color: #333;
        }
        .detail-item { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 10px; 
            padding-bottom: 10px; 
            border-bottom: 1px solid #eee; 
        }
        .detail-label {
            color: #666;
        }
        .detail-value {
            font-weight: 500;
            color: #333;
        }
        .preview-price { 
            margin-top: 20px; 
            text-align: center; 
            font-size: 24px; 
            font-weight: 300; 
            color: #e91e63;
        }

        /* Customização (Direita) */
        .customize-section { 
            flex: 2; 
            min-width: 300px; 
        }
        .customize-steps { 
            display: flex; 
            flex-direction: column; 
            gap: 30px; 
        }
        .customize-step { 
            background-color: #ffffff; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); 
            border: 1px solid #eee; 
        }
        .step-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
        }
        .step-title { 
            font-size: 18px; 
            font-weight: 500; 
            color: #333;
        }
        .step-number { 
            background-color: #000; 
            color: #fff; 
            width: 30px; 
            height: 30px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 14px; 
        }

        /* Grid de Opções */
        .options-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); 
            gap: 15px; 
        }
        .option-item { 
            border: 1px solid #eee; 
            border-radius: 8px; 
            padding: 15px; 
            text-align: center; 
            cursor: pointer; 
            transition: all 0.3s ease;
            background-color: #fff; 
        }
        .option-item:hover { 
            border-color: #000; 
            transform: translateY(-2px);
        }
        .option-item.selected { 
            border-color: #e91e63; 
            background-color: #fff0f6; 
            color: #e91e63; 
            font-weight: bold; 
            box-shadow: 0 0 0 1px #e91e63; 
        }
        .option-image { 
            width: 60px; 
            height: 60px; 
            margin: 0 auto 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .option-image img { 
            max-width: 100%; 
            max-height: 100%; 
            object-fit: contain;
        }
        .option-name {
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .option-price {
            font-size: 12px;
            color: #666;
        }
        
        /* Tamanhos e Inputs */
        .size-options { 
            display: flex; 
            gap: 10px; 
            flex-wrap: wrap; 
        }
        .size-option { 
            padding: 10px 15px; 
            border: 1px solid #eee; 
            border-radius: 8px; 
            cursor: pointer; 
            transition: 0.3s;
            background-color: #fff;
        }
        .size-option:hover { 
            border-color: #000; 
        }
        .size-option.selected { 
            border-color: #e91e63; 
            background: #fff0f6; 
            color: #e91e63; 
            font-weight: bold; 
        }
        .engraving-input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #eee; 
            border-radius: 8px; 
            font-size: 14px; 
            margin-bottom: 10px; 
            box-sizing: border-box; 
            outline: none;
            background-color: #fff;
        }
        .engraving-input:focus { 
            border-color: #e91e63; 
        }

        /* Botões */
.action-buttons { 
    display: flex; 
    gap: 15px; 
    margin-top: 40px; 
}

.btn { 
    flex: 1 1 0;            /* 🔹 Ambos crescem IGUALMENTE */
    padding: 15px;          /* 🔹 Remove padding lateral desigual */
    border-radius: 8px; 
    font-size: 14px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.3s ease; 
    text-align: center; 
    text-transform: uppercase; 
    white-space: nowrap;    /* 🔹 Impede quebra de linha */
}


.btn-primary { 
    background-color: #e91e63; 
    color: #fff; 
    border: none; 
}

.btn-primary:hover { 
    background-color: #c2185b; 
}

.btn-secondary { 
    background-color: transparent; 
    color: #000; 
    border: 1px solid #000; 
}

.btn-secondary:hover { 
    background-color: #f5f5f5; 
}


        @media (max-width: 768px) {
            .personalize-container { 
                flex-direction: column; 
            }
            .preview-section, .customize-section { 
                width: 100%; 
            }
            .preview-section { 
                position: relative; 
                top: 0; 
            }
            .options-grid {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            }
        }
    </style>
</head>
<body>

    <?php if(file_exists('navbar.php')) include 'navbar.php'; ?>

    <section class="personalize-section">
        <div class="personalize-header">
            <h1>CRIE SUA HISTÓRIA</h1>
            <p>Escolha cada detalhe e nós criaremos uma peça única para você.</p>
        </div>

        <div class="personalize-container">
            
            <div class="preview-section">
                <div class="preview-image">
                    <img id="previewImg" src="imgs/anel-dourado-diamante.png" alt="Joia Personalizada" onerror="this.src='imgs/produto-padrao.png'">
                </div>
                <div class="preview-details">
                    <h3>Resumo da Criação</h3>
                    <div class="detail-item">
                        <span class="detail-label">Tipo:</span>
                        <span id="detailType" class="detail-value">Anel</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Material:</span>
                        <span id="detailMaterial" class="detail-value">Ouro 18k</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Pedra:</span>
                        <span id="detailStone" class="detail-value">Diamante</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tamanho:</span>
                        <span id="detailSize" class="detail-value">17</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Gravação:</span>
                        <span id="detailEngraving" class="detail-value">-</span>
                    </div>
                    <div class="preview-price">R$ <span id="totalPrice">1.290,00</span></div>
                </div>
            </div>

            <div class="customize-section">
                <div class="customize-steps">
                    
                    <!-- 1. Tipo de Joia -->
                    <div class="customize-step">
                        <div class="step-header">
                            <div class="step-title">1. Tipo de Joia</div>
                            <div class="step-number">1</div>
                        </div>
                        <div class="options-grid" id="jewelryTypeOptions">
                            <div class="option-item selected" 
                                 data-type="Anel" 
                                 data-price="290"
                                 data-suffix="anel">
                                <div class="option-image">
                                    <img src="imgs/anel.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Anel</div>
                                <div class="option-price">+R$ 290,00</div>
                            </div>
                            <div class="option-item" 
                                 data-type="Colar" 
                                 data-price="490"
                                 data-suffix="colar">
                                <div class="option-image">
                                    <img src="imgs/colar.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Colar</div>
                                <div class="option-price">+R$ 490,00</div>
                            </div>
                            <div class="option-item" 
                                 data-type="Brinco" 
                                 data-price="320"
                                 data-suffix="brinco">
                                <div class="option-image">
                                    <img src="imgs/brinco.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Brinco</div>
                                <div class="option-price">+R$ 320,00</div>
                            </div>
                            <div class="option-item" 
                                 data-type="Pulseira" 
                                 data-price="380"
                                 data-suffix="pulseira">
                                <div class="option-image">
                                    <img src="imgs/pulseira.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Pulseira</div>
                                <div class="option-price">+R$ 380,00</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Material -->
                    <div class="customize-step">
                        <div class="step-header">
                            <div class="step-title">2. Material</div>
                            <div class="step-number">2</div>
                        </div>
                        <div class="options-grid" id="materialOptions">
                            <div class="option-item selected" 
                                 data-material="Ouro 18k" 
                                 data-price="500"
                                 data-suffix="dourado">
                                <div class="option-image">
                                    <img src="imgs/ouro18k.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Ouro 18k</div>
                                <div class="option-price">+R$ 500,00</div>
                            </div>
                            <div class="option-item" 
                                 data-material="Ouro Branco" 
                                 data-price="550"
                                 data-suffix="prata">
                                <div class="option-image">
                                    <img src="imgs/ourobranco.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Ouro Branco</div>
                                <div class="option-price">+R$ 550,00</div>
                            </div>
                            <div class="option-item" 
                                 data-material="Prata 925" 
                                 data-price="150"
                                 data-suffix="prata">
                                <div class="option-image">
                                    <img src="imgs/prata925.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Prata 925</div>
                                <div class="option-price">+R$ 150,00</div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Pedra Principal (com Esmeralda adicionada) -->
                    <div class="customize-step">
                        <div class="step-header">
                            <div class="step-title">3. Pedra Principal</div>
                            <div class="step-number">3</div>
                        </div>
                        <div class="options-grid" id="stoneOptions">
                            <div class="option-item selected" 
                                 data-stone="Diamante" 
                                 data-price="500"
                                 data-suffix="diamante">
                                <div class="option-image">
                                    <img src="imgs/diamante.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Diamante</div>
                                <div class="option-price">+R$ 500,00</div>
                            </div>
                            <div class="option-item" 
                                 data-stone="Rubi" 
                                 data-price="350"
                                 data-suffix="rubi">
                                <div class="option-image">
                                    <img src="imgs/rubi.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Rubi</div>
                                <div class="option-price">+R$ 350,00</div>
                            </div>
                            <div class="option-item" 
                                 data-stone="Safira" 
                                 data-price="350"
                                 data-suffix="safira">
                                <div class="option-image">
                                    <img src="imgs/safira.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Safira</div>
                                <div class="option-price">+R$ 350,00</div>
                            </div>
                            <div class="option-item" 
                                 data-stone="Esmeralda" 
                                 data-price="400"
                                 data-suffix="esmeralda">
                                <div class="option-image">
                                    <img src="imgs/esmeralda.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Esmeralda</div>
                                <div class="option-price">+R$ 400,00</div>
                            </div>
                            <div class="option-item" 
                                 data-stone="Liso" 
                                 data-price="0"
                                 data-suffix="liso">
                                <div class="option-image">
                                    <img src="imgs/sempedra.png" onerror="this.src='imgs/produto-padrao.png'">
                                </div>
                                <div class="option-name">Liso</div>
                                <div class="option-price">+R$ 0,00</div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Personalização Final -->
                    <div class="customize-step">
                        <div class="step-header">
                            <div class="step-title">4. Personalização Final</div>
                            <div class="step-number">4</div>
                        </div>
                        <p style="margin-bottom:10px; font-weight:500; color:#333;">Tamanho:</p>
                        <div class="size-options" id="sizeOptions">
                            <div class="size-option" data-size="14">14</div>
                            <div class="size-option" data-size="15">15</div>
                            <div class="size-option" data-size="16">16</div>
                            <div class="size-option selected" data-size="17">17</div>
                            <div class="size-option" data-size="18">18</div>
                            <div class="size-option" data-size="19">19</div>
                            <div class="size-option" data-size="20">20</div>
                        </div>
                        <div style="margin-top:20px;">
                            <p style="margin-bottom: 10px; font-weight:500; color:#333;">Gravação (Opcional):</p>
                            <input type="text" id="engravingInput" class="engraving-input" placeholder="Digite o texto (máx. 15 caracteres)" maxlength="15">
                        </div>
                    </div>

                </div>

                <div class="action-buttons">
                    <button class="btn btn-secondary" id="resetBtn">RECOMEÇAR</button>
                    <button class="btn btn-primary" id="addToCartBtn">ADICIONAR AO CARRINHO</button>
                </div>
            </div>
        </div>
    </section>

    <?php if(file_exists('footer.php')) include 'footer.php'; ?>
    <?php if(file_exists('modais.php')) include 'modais.php'; ?>

    <script>
        // === LÓGICA PRINCIPAL ===
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. Definição Inicial
            let basePrice = 0;
            let currentData = {
                type: 'Anel', 
                typePrice: 290,
                material: 'Ouro 18k', 
                materialPrice: 500,
                stone: 'Diamante', 
                stonePrice: 500,
                size: '17', 
                engraving: '',
                typeSuffix: 'anel',
                materialSuffix: 'dourado',
                stoneSuffix: 'diamante'
            };

            // Lista de extensões possíveis para as imagens
            const imageExtensions = ['.png', '.jpg', '.jpeg'];
            
            // Mapeamento de materiais para sufixos de imagem
            const materialMapping = {
                'Ouro 18k': 'dourado',
                'Ouro Branco': 'prata',
                'Prata 925': 'prata'
            };

            // 2. Função para atualizar UI
            function updateUI() {
                document.getElementById('detailType').textContent = currentData.type;
                document.getElementById('detailMaterial').textContent = currentData.material;
                document.getElementById('detailStone').textContent = currentData.stone;
                document.getElementById('detailSize').textContent = currentData.size;
                document.getElementById('detailEngraving').textContent = currentData.engraving || '-';

                const total = basePrice + currentData.typePrice + currentData.materialPrice + currentData.stonePrice;
                document.getElementById('totalPrice').textContent = total.toLocaleString('pt-BR', { 
                    minimumFractionDigits: 2 
                });

                // Atualizar imagem de preview
                updatePreviewImage();
            }

            // 3. Função para encontrar a imagem correta
            async function findCorrectImage(baseName) {
                // Verificar cada extensão possível
                for (const ext of imageExtensions) {
                    const imgPath = `imgs/${baseName}${ext}`;
                    const exists = await checkImageExists(imgPath);
                    if (exists) {
                        return imgPath;
                    }
                }
                // Se não encontrou nenhuma, retorna null
                return null;
            }

            // 4. Função para verificar se uma imagem existe
            function checkImageExists(url) {
                return new Promise((resolve) => {
                    const img = new Image();
                    img.onload = () => resolve(true);
                    img.onerror = () => resolve(false);
                    img.src = url;
                });
            }

            // 5. Função para atualizar imagem de preview
            async function updatePreviewImage() {
                const previewImg = document.getElementById('previewImg');
                
                // Converter tipo para minúsculas
                let typeName = currentData.typeSuffix.toLowerCase();
                
                // Usar o mapeamento para obter o sufixo correto do material
                let materialSuffix = materialMapping[currentData.material] || currentData.materialSuffix;
                
                // Construir nome base da imagem (sem extensão)
                const baseImageName = `${typeName}-${materialSuffix}-${currentData.stoneSuffix}`;
                
                console.log('Buscando imagem:', baseImageName);
                
                // Tentar encontrar a imagem com a extensão correta
                const correctImagePath = await findCorrectImage(baseImageName);
                
                if (correctImagePath) {
                    previewImg.src = correctImagePath;
                    previewImg.alt = `${currentData.type} ${currentData.material} ${currentData.stone}`;
                    
                    // Adicionar efeito de transição suave
                    previewImg.style.opacity = '0.7';
                    setTimeout(() => {
                        previewImg.style.opacity = '1';
                    }, 300);
                } else {
                    console.log('Imagem não encontrada para:', baseImageName);
                    // Usar fallback
                    previewImg.src = 'imgs/produto-padrao.png';
                }
            }

            // 6. Configurar Clicks nas Opções
            function setupOptions(containerId, dataKey, priceKey, suffixKey = null) {
                const container = document.getElementById(containerId);
                if(!container) return; 

                const options = container.querySelectorAll('.option-item');
                options.forEach(opt => {
                    opt.addEventListener('click', function() {
                        options.forEach(o => o.classList.remove('selected'));
                        this.classList.add('selected');

                        currentData[dataKey] = this.getAttribute('data-' + dataKey.toLowerCase());
                        currentData[priceKey] = parseFloat(this.getAttribute('data-price'));
                        
                        // Atualizar suffix se existir
                        if (suffixKey && this.getAttribute('data-suffix')) {
                            currentData[suffixKey] = this.getAttribute('data-suffix');
                        }
                        
                        // Se for material, atualizar também o mapeamento
                        if (dataKey === 'material') {
                            currentData.materialSuffix = this.getAttribute('data-suffix');
                        }
                        
                        updateUI();
                    });
                });
            }

            // Configurar cada seção
            setupOptions('jewelryTypeOptions', 'type', 'typePrice', 'typeSuffix');
            setupOptions('materialOptions', 'material', 'materialPrice', 'materialSuffix');
            setupOptions('stoneOptions', 'stone', 'stonePrice', 'stoneSuffix');

            // Tamanhos
            const sizeOpts = document.querySelectorAll('#sizeOptions .size-option');
            sizeOpts.forEach(opt => {
                opt.addEventListener('click', function() {
                    sizeOpts.forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    currentData.size = this.getAttribute('data-size');
                    updateUI();
                });
            });

            // Gravação
            document.getElementById('engravingInput').addEventListener('input', function() {
                currentData.engraving = this.value;
                updateUI();
            });

            // Recomeçar
            document.getElementById('resetBtn').addEventListener('click', () => location.reload());

            // 7. ADICIONAR AO CARRINHO (INTEGRAÇÃO BACKEND)
            // Substitua esta parte no seu personalize.php:

document.getElementById('addToCartBtn').addEventListener('click', function() {
    
    // Verificar Login via PHP
    <?php if(!isset($_SESSION['usuario'])): ?>
        Swal.fire({
            title: 'Login Necessário',
            text: 'Você precisa estar logado para salvar sua criação!',
            icon: 'warning',
            confirmButtonColor: '#000',
            confirmButtonText: 'Fazer Login'
        }).then((result) => {
            if (result.isConfirmed) {
                if(typeof window.openLoginModal === 'function') window.openLoginModal();
                else window.location.href = 'login.php';
            }
        });
        return;
    <?php endif; ?>

    const btn = this;
    const originalText = btn.innerText;
    btn.innerText = 'Processando...';
    btn.disabled = true;

    const precoFinal = basePrice + currentData.typePrice + currentData.materialPrice + currentData.stonePrice;

    // Usar o novo arquivo personalizado_salvar.php
    fetch('personalizado_salvar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'tipo': currentData.type,
            'material': currentData.material,
            'pedra': currentData.stone,
            'tamanho': currentData.size,
            'gravacao': currentData.engraving,
            'preco': precoFinal
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro na rede: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Resposta:', data);
        
        if (data.success) {
            Swal.fire({
                title: 'Sucesso!',
                text: data.message || 'Sua joia exclusiva foi adicionada à sacola!',
                icon: 'success',
                confirmButtonColor: '#e91e63',
                showCancelButton: true,
                confirmButtonText: 'Ir para Sacola',
                cancelButtonText: 'Criar Outra'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'carrinho.php';
                } else {
                    location.reload();
                }
            });
            
            // Atualizar contador do carrinho
            if (document.querySelectorAll('.cart-count').length > 0 && data.total_carrinho) {
                document.querySelectorAll('.cart-count').forEach(c => c.textContent = data.total_carrinho);
            }
        } else {
            Swal.fire('Erro', data.message || 'Erro ao processar sua criação.', 'error');
        }
        btn.innerText = originalText;
        btn.disabled = false;
    })
    .catch(error => {
        console.error('Erro detalhado:', error);
        Swal.fire({
            title: 'Erro de Conexão',
            text: 'Não foi possível conectar ao servidor. Verifique sua conexão.',
            icon: 'error'
        });
        btn.innerText = originalText;
        btn.disabled = false;
    });
});
            // Adicionar evento para imagem de erro
            document.getElementById('previewImg').addEventListener('error', function() {
                console.log('Erro ao carregar imagem:', this.src);
                this.src = 'imgs/produto-padrao.png';
            });

            // Inicializar
            updateUI();
        });
    </script>
</body>
</html>