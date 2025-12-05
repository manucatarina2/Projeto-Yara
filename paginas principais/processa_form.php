<?php
// processa_form.php - VERSÃO CORRIGIDA E COMPLETA
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Resposta sempre em JSON
header('Content-Type: application/json');

require_once 'conexao.php';
require_once 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$acao = $_POST['acao'] ?? '';

try {
    switch ($acao) {

        // --- LOGIN ---
        case 'login':
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';

            if (empty($email) || empty($senha)) {
                echo json_encode(['success' => false, 'message' => 'Preencha email e senha!']);
                break;
            }

            // Login Admin
            if ($email === 'admin@gmail.com' && $senha === 'admin1234') {
                $_SESSION['usuario'] = ['id' => 1, 'nome' => 'Admin', 'email' => 'admin@gmail.com', 'is_admin' => 1];
                echo json_encode(['success' => true, 'redirect' => 'admin/dashboard.php']);
                break;
            }

            // Login Usuário Comum
            if (fazerLogin($email, $senha)) {
                echo json_encode(['success' => true, 'message' => 'Bem-vindo!', 'redirect' => 'index.php']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Dados incorretos!']);
            }
            break;

        // --- CADASTRO ---
        case 'cadastro':
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $foto = $_FILES['foto'] ?? null;

            if (empty($nome) || empty($email) || empty($senha)) {
                echo json_encode(['success' => false, 'message' => 'Preencha todos os campos!']);
                break;
            }

            $res = cadastrarUsuario($nome, $email, $senha, $foto);
            if ($res === true) {
                echo json_encode(['success' => true, 'message' => 'Cadastro realizado!']);
            } else {
                echo json_encode(['success' => false, 'message' => $res]);
            }
            break;

        // --- LOGOUT ---
        case 'logout':
            fazerLogout();
            echo json_encode(['success' => true, 'redirect' => 'login.php']);
            break;

        // --- ADICIONAR ENDEREÇO ---
        case 'adicionar_endereco':
            if (!isset($_SESSION['usuario'])) {
                echo json_encode(['success' => false, 'message' => 'Faça login.']);
                break;
            }
            $uid = $_SESSION['usuario']['id'];
            $dest = $_POST['destinatario'] ?? '';
            $cep = $_POST['cep'] ?? '';
            $rua = $_POST['rua'] ?? '';
            $num = $_POST['numero'] ?? '';
            $comp = $_POST['complemento'] ?? '';
            $bairro = $_POST['bairro'] ?? '';
            $cid = $_POST['cidade'] ?? '';
            $est = $_POST['estado'] ?? '';
            $padrao = isset($_POST['padrao']) ? 1 : 0;

            if ($padrao) {
                $conexao->query("UPDATE enderecos SET padrao = 0 WHERE usuario_id = $uid");
            }

            $sql = "INSERT INTO enderecos (usuario_id, cep, rua, numero, complemento, bairro, cidade, estado, destinatario, padrao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("issssssssi", $uid, $cep, $rua, $num, $comp, $bairro, $cid, $est, $dest, $padrao);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Endereço salvo!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar endereço.']);
            }
            break;

        // --- EXCLUIR ENDEREÇO ---
        case 'excluir_endereco':
            if (!isset($_SESSION['usuario'])) { 
                echo json_encode(['success' => false]); 
                break; 
            }
            $id = $_POST['id_endereco'] ?? 0;
            $uid = $_SESSION['usuario']['id'];
            $conexao->query("DELETE FROM enderecos WHERE id = $id AND usuario_id = $uid");
            echo json_encode(['success' => true]);
            break;

        // --- DEFINIR ENDEREÇO PRINCIPAL ---
        case 'definir_principal':
            if (!isset($_SESSION['usuario'])) { 
                echo json_encode(['success' => false]); 
                break; 
            }
            $id = $_POST['id_endereco'] ?? 0;
            $uid = $_SESSION['usuario']['id'];
            $conexao->query("UPDATE enderecos SET padrao = 0 WHERE usuario_id = $uid");
            $conexao->query("UPDATE enderecos SET padrao = 1 WHERE id = $id AND usuario_id = $uid");
            echo json_encode(['success' => true]);
            break;

        // --- CARRINHO: ADICIONAR ---
        case 'adicionar_carrinho':
            $pid = $_POST['produto_id'] ?? null;
            if ($pid) {
                adicionarAoCarrinho($pid);
                echo json_encode(['success' => true, 'total_carrinho' => array_sum($_SESSION['carrinho'] ?? [])]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Produto inválido']);
            }
            break;

        // --- ADICIONAR PERSONALIZADO ---
        case 'adicionar_personalizado':
            $prodId = 999; // ID do Produto Base
            
            $detalhes = [
                'tipo' => $_POST['tipo'] ?? '',
                'material' => $_POST['material'] ?? '',
                'pedra' => $_POST['pedra'] ?? '',
                'tamanho' => $_POST['tamanho'] ?? '',
                'gravacao' => $_POST['gravacao'] ?? '',
                'preco_final' => (float)($_POST['preco'] ?? 0)
            ];

            if (!isset($_SESSION['carrinho'])) {
                $_SESSION['carrinho'] = [];
            }
            
            $_SESSION['carrinho'][$prodId] = 1;

            $textoPersonalizacao = "Tipo: {$detalhes['tipo']} | Material: {$detalhes['material']} | Pedra: {$detalhes['pedra']} | Tam: {$detalhes['tamanho']}";
            if(!empty($detalhes['gravacao'])) {
                $textoPersonalizacao .= " | Gravação: " . $detalhes['gravacao'];
            }

            if (!isset($_SESSION['personalizados'])) {
                $_SESSION['personalizados'] = [];
            }
            
            $_SESSION['personalizados'][$prodId] = [
                'texto' => $textoPersonalizacao,
                'preco_override' => $detalhes['preco_final']
            ];

            echo json_encode([
                'success' => true, 
                'total_carrinho' => array_sum($_SESSION['carrinho']),
                'message' => 'Joia personalizada adicionada!'
            ]);
            break;
        // No seu processa_form.php, adicione esta seção depois do case 'adicionar_personalizado':

elseif ($acao == 'adicionar_personalizado') {
    session_start();
    
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(['success' => false, 'message' => 'Você precisa estar logado para salvar sua criação!']);
        exit;
    }
    
    $tipo = $_POST['tipo'] ?? '';
    $material = $_POST['material'] ?? '';
    $pedra = $_POST['pedra'] ?? '';
    $tamanho = $_POST['tamanho'] ?? '';
    $gravacao = $_POST['gravacao'] ?? '';
    $preco = floatval($_POST['preco'] ?? 0);
    $usuario_id = $_SESSION['usuario']['id'] ?? 0;
    
    if (!$usuario_id) {
        echo json_encode(['success' => false, 'message' => 'Usuário não identificado.']);
        exit;
    }
    
    // Gerar nome da imagem
    $tipo_lower = strtolower($tipo);
    $material_lower = ($material == 'Ouro 18k') ? 'dourado' : 'prata';
    $pedra_lower = ($pedra == 'Liso') ? 'liso' : strtolower($pedra);
    
    // Tentar diferentes extensões
    $imagem_encontrada = false;
    $imagem_final = 'imgs/produto-padrao.png';
    $extensoes = ['.png', '.jpg', '.jpeg'];
    
    foreach ($extensoes as $ext) {
        $caminho_teste = "imgs/{$tipo_lower}-{$material_lower}-{$pedra_lower}{$ext}";
        if (file_exists($caminho_teste)) {
            $imagem_final = $caminho_teste;
            $imagem_encontrada = true;
            break;
        }
    }
    
    // Salvar no banco de dados (tabela pedidos_personalizados)
    try {
        $stmt = $conexao->prepare("INSERT INTO pedidos_personalizados 
                                  (usuario_id, tipo_joia, material, pedra, tamanho, gravacao, preco_total, imagem_preview) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssds", $usuario_id, $tipo, $material, $pedra, $tamanho, $gravacao, $preco, $imagem_final);
        
        if ($stmt->execute()) {
            $pedido_personalizado_id = $stmt->insert_id;
            
            // Criar produto temporário no carrinho (ID negativo para não conflitar)
            $produto_temp_id = time() * -1;
            
            // Adicionar ao carrinho da sessão
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
                'pedido_personalizado_id' => $pedido_personalizado_id
            ];
            
            // Calcular total do carrinho
            $total_carrinho = array_sum($_SESSION['carrinho']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Sua joia exclusiva foi adicionada à sacola!',
                'pedido_id' => $pedido_personalizado_id,
                'total_carrinho' => $total_carrinho
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao salvar pedido personalizado: ' . $stmt->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erro no banco de dados: ' . $e->getMessage()
        ]);
    }
}

// Adicione também esta nova ação para gerenciar pedidos personalizados no admin:
elseif ($acao == 'atualizar_personalizado_status') {
    session_start();
    
    // Verificar se é admin
    if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['is_admin'] ?? 0) != 1) {
        echo json_encode(['success' => false, 'message' => 'Acesso não autorizado!']);
        exit;
    }
    
    $id = intval($_POST['id'] ?? 0);
    $novo_status = $_POST['status'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';
    
    $status_validos = ['pendente', 'produzindo', 'pronto', 'entregue'];
    
    if (!in_array($novo_status, $status_validos)) {
        echo json_encode(['success' => false, 'message' => 'Status inválido!']);
        exit;
    }
    
    try {
        $stmt = $conexao->prepare("UPDATE pedidos_personalizados SET status = ?, observacoes = ?, data_atualizacao = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $novo_status, $observacoes, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro no banco: ' . $e->getMessage()]);
    }
}

        // --- CARRINHO: ATUALIZAR QTD ---
        case 'atualizar_carrinho':
            $pid = $_POST['produto_id'] ?? null;
            $qtd = $_POST['quantidade'] ?? 0;
            
            if ($pid) {
                atualizarCarrinho($pid, $qtd);
                echo json_encode(['success' => true, 'total_carrinho' => array_sum($_SESSION['carrinho'] ?? [])]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Produto inválido']);
            }
            break;

        // --- REMOVER DO CARRINHO ---
        case 'remover_carrinho':
            $pid = $_POST['produto_id'] ?? null;
            if ($pid) {
                if (isset($_SESSION['carrinho'][$pid])) {
                    unset($_SESSION['carrinho'][$pid]);
                }
                if (isset($_SESSION['presentes'][$pid])) {
                    unset($_SESSION['presentes'][$pid]);
                }
                if (isset($_SESSION['personalizados'][$pid])) {
                    unset($_SESSION['personalizados'][$pid]);
                }
                echo json_encode(['success' => true, 'total_carrinho' => array_sum($_SESSION['carrinho'] ?? [])]);
            }
            break;

        // --- ADICIONAR PRESENTE ---
        case 'adicionar_presente':
            $produto_id = (int)($_POST['produto_id'] ?? 0);
            $presente_data = isset($_POST['presente_data']) ? json_decode($_POST['presente_data'], true) : [];
            
            if (!isset($_SESSION['presentes'])) {
                $_SESSION['presentes'] = [];
            }
            
            $_SESSION['presentes'][$produto_id] = $presente_data;
            
            echo json_encode(['success' => true, 'message' => 'Presente aplicado com sucesso']);
            break;

        // --- REMOVER PRESENTE ---
        case 'remover_presente':
            $produto_id = (int)($_POST['produto_id'] ?? 0);
            
            if (isset($_SESSION['presentes'][$produto_id])) {
                unset($_SESSION['presentes'][$produto_id]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Presente removido com sucesso']);
            break;

        // --- FINALIZAR PEDIDO (CORRIGIDO) ---
        case 'finalizar_pedido':
            if (!isset($_SESSION['usuario'])) {
                echo json_encode(['success' => false, 'message' => 'Faça login para finalizar o pedido.']); 
                break;
            }
            
            if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
                echo json_encode(['success' => false, 'message' => 'Carrinho vazio.']); 
                break;
            }
            
            $uid = $_SESSION['usuario']['id'];
            $total = (float)($_POST['total'] ?? 0);
            $metodo_pagamento = $_POST['metodo_pagamento'] ?? '';
            $endereco = $_POST['endereco'] ?? '';
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $cep = $_POST['cep'] ?? '';
            $numero = $_POST['numero'] ?? '';
            $complemento = $_POST['complemento'] ?? '';
            $parcelas = $_POST['parcelas'] ?? 1;
            
            // Verificar dados obrigatórios
            if (empty($nome) || empty($email) || empty($endereco) || empty($metodo_pagamento)) {
                echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
                break;
            }
            
            // Calcular subtotal dos itens
            $subtotal = 0;
            foreach ($_SESSION['carrinho'] as $prodId => $qtd) {
                $resP = $conexao->query("SELECT preco FROM produtos WHERE id = $prodId");
                if ($resP && $resP->num_rows > 0) {
                    $dadosP = $resP->fetch_assoc();
                    $preco = $dadosP['preco'];
                    
                    // Preço Personalizado
                    if (isset($_SESSION['personalizados'][$prodId])) {
                        $preco = $_SESSION['personalizados'][$prodId]['preco_override'];
                    }
                    
                    // Valor do presente
                    $valorPresente = 0;
                    if (isset($_SESSION['presentes'][$prodId])) {
                        $presente = $_SESSION['presentes'][$prodId];
                        if (isset($presente['preco'])) {
                            $valorPresente = (float)$presente['preco'];
                        } elseif (isset($presente['packagingPrice'])) {
                            $valorPresente = (float)$presente['packagingPrice'] + (float)($presente['cardPrice'] ?? 0) + (float)($presente['ribbonPrice'] ?? 0);
                        }
                    }
                    
                    $subtotal += ($preco + $valorPresente) * $qtd;
                }
            }
            
            $frete = 15.00;
            if ($subtotal == 0) { 
                $frete = 0; 
            }
            
            $total_calculado = $subtotal + $frete;
            
            // Verificar se o total bate
            if (abs($total - $total_calculado) > 0.01) {
                error_log("Total mismatch: POST=$total, CALC=$total_calculado");
            }
            
            // Configurar status e pagamento
            $status_pagamento = 'pendente';
            $status_pedido = 'processando';
            $codigo_pix = null;
            
            if ($metodo_pagamento === 'PIX') {
                $status_pagamento = 'pendente';
                $codigo_pix = generatePixCode($total);
            } elseif (strpos($metodo_pagamento, 'Cartão') !== false) {
                $status_pagamento = 'processando';
                // Adicionar parcelas à descrição
                if ($parcelas > 1) {
                    $metodo_pagamento = "Cartão de Crédito ({$parcelas}x)";
                } else {
                    $metodo_pagamento = "Cartão de Crédito (à vista)";
                }
            }
            
            // Iniciar transação
            $conexao->begin_transaction();
            
            try {
                // 1. Inserir pedido principal
                $sql_pedido = "INSERT INTO pedidos (
                    usuario_id, nome_cliente, email_cliente, telefone_cliente, 
                    valor_subtotal, valor_frete, valor_total, forma_pagamento, 
                    parcelas, endereco_entrega, cep, numero, complemento,
                    status_pedido, status_pagamento, codigo_pix, data_pedido
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
                $stmt = $conexao->prepare($sql_pedido);
                if (!$stmt) {
                    throw new Exception("Erro ao preparar SQL do pedido: " . $conexao->error);
                }
                
                $stmt->bind_param(
                    "isssddddsissssss",
                    $uid, $nome, $email, $telefone,
                    $subtotal, $frete, $total, $metodo_pagamento,
                    $parcelas, $endereco, $cep, $numero, $complemento,
                    $status_pedido, $status_pagamento, $codigo_pix
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Erro ao inserir pedido: " . $stmt->error);
                }
                
                $pedido_id = $conexao->insert_id;
                
                // 2. Inserir itens do pedido
                $sql_item = "INSERT INTO pedido_itens (
                    pedido_id, produto_id, quantidade, preco_unitario, 
                    subtotal, personalizacao, presente_embalagem, presente_cartao, 
                    presente_fita, presente_mensagem, valor_presente
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt_item = $conexao->prepare($sql_item);
                if (!$stmt_item) {
                    throw new Exception("Erro ao preparar SQL dos itens: " . $conexao->error);
                }
                
                foreach ($_SESSION['carrinho'] as $prodId => $qtd) {
                    $resP = $conexao->query("SELECT preco, nome FROM produtos WHERE id = $prodId");
                    
                    if (!$resP || $resP->num_rows == 0) {
                        // Produto não encontrado, pular
                        continue;
                    }
                    
                    $dadosP = $resP->fetch_assoc();
                    $preco = $dadosP['preco'];
                    $personalizacao = null;
                    
                    // Preço Personalizado
                    if (isset($_SESSION['personalizados'][$prodId])) {
                        $preco = $_SESSION['personalizados'][$prodId]['preco_override'];
                        $personalizacao = $_SESSION['personalizados'][$prodId]['texto'] ?? null;
                    }
                    
                    // Dados de Presente
                    $p_emb = null; 
                    $p_cart = null; 
                    $p_fita = null; 
                    $p_msg = null; 
                    $p_val = 0.00;
                    
                    if (isset($_SESSION['presentes'][$prodId])) {
                        $pres = $_SESSION['presentes'][$prodId];
                        
                        // Verificar formato dos dados do presente
                        if (isset($pres['embalagem'])) {
                            $p_emb = $pres['embalagem'];
                        } elseif (isset($pres['packaging'])) {
                            $p_emb = $pres['packaging'];
                        }
                        
                        if (isset($pres['cartao'])) {
                            $p_cart = $pres['cartao'];
                        } elseif (isset($pres['card'])) {
                            $p_cart = $pres['card'];
                        }
                        
                        if (isset($pres['fita'])) {
                            $p_fita = $pres['fita'];
                        } elseif (isset($pres['ribbon'])) {
                            $p_fita = $pres['ribbon'];
                        }
                        
                        if (isset($pres['mensagem'])) {
                            $p_msg = $pres['mensagem'];
                        } elseif (isset($pres['message'])) {
                            $p_msg = $pres['message'];
                        }
                        
                        if (isset($pres['preco'])) {
                            $p_val = (float)$pres['preco'];
                        } elseif (isset($pres['packagingPrice'])) {
                            $p_val = (float)$pres['packagingPrice'] + 
                                    (float)($pres['cardPrice'] ?? 0) + 
                                    (float)($pres['ribbonPrice'] ?? 0);
                        }
                    }
                    
                    $subtotal_item = ($preco + $p_val) * $qtd;
                    
                    $stmt_item->bind_param(
                        "iiiddsssssd",
                        $pedido_id, $prodId, $qtd, $preco, $subtotal_item,
                        $personalizacao, $p_emb, $p_cart, $p_fita, $p_msg, $p_val
                    );
                    
                    if (!$stmt_item->execute()) {
                        throw new Exception("Erro ao inserir item: " . $stmt_item->error);
                    }
                }
                
                // 3. Se for PIX, salvar na tabela de pagamentos PIX
                if ($metodo_pagamento === 'PIX' && $codigo_pix) {
                    $sql_pix = "INSERT INTO pagamentos_pix (pedido_id, codigo_pix, valor, status, data_geracao) 
                               VALUES (?, ?, ?, 'pendente', NOW())";
                    $stmt_pix = $conexao->prepare($sql_pix);
                    if ($stmt_pix) {
                        $stmt_pix->bind_param("isd", $pedido_id, $codigo_pix, $total);
                        $stmt_pix->execute();
                    }
                }
                
                // 4. Limpar carrinho da sessão
                unset($_SESSION['carrinho']);
                unset($_SESSION['presentes']);
                unset($_SESSION['personalizados']);
                
                // Commit da transação
                $conexao->commit();
                
                echo json_encode([
                    'success' => true,
                    'id_pedido' => $pedido_id,
                    'message' => 'Pedido realizado com sucesso!',
                    'redirect' => "pedido_confirmado.php?id=$pedido_id"
                ]);
                
            } catch (Exception $e) {
                $conexao->rollback();
                error_log("Erro no pedido: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Erro ao processar pedido: ' . $e->getMessage()]);
            }
            break;

        // --- FAVORITOS ---
        case 'toggle_favorito':
            if (!isset($_SESSION['usuario'])) {
                echo json_encode(['success' => false, 'message' => 'Faça login para favoritar!']);
                break;
            }
            $uid = $_SESSION['usuario']['id'];
            $pid = $_POST['produto_id'] ?? 0;

            if (isFavoritoUsuario($uid, $pid)) {
                if (removerFavorito($uid, $pid)) {
                    echo json_encode(['success' => true, 'favoritado' => false, 'message' => 'Removido dos favoritos.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao remover.']);
                }
            } else {
                if (adicionarFavorito($uid, $pid)) {
                    echo json_encode(['success' => true, 'favoritado' => true, 'message' => 'Adicionado aos favoritos!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erro ao adicionar.']);
                }
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida: ' . $acao]);
    }
} catch (Exception $e) {
    error_log("Erro geral processa_form.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor.']);
}
?>