<?php
        $total = $dados_pagamento['total'];
        $vencimento = $dados_pagamento['vencimento'];
        $codigo_barras = $dados_pagamento['codigo_barras'];
        $linha_digitavel = $dados_pagamento['linha_digitavel'];
        ?>
        <div class="boleto-container">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Vencimento: <?php echo $vencimento; ?></strong>
                    <br><small>O boleto pode levar até 3 dias úteis para ser compensado após o pagamento.</small>
                </div>
            </div>
            
            <div class="boleto-preview">
                <div style="text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-barcode fa-3x" style="color: #e91e63;"></i>
                    <h3 style="margin-top: 10px;">BOLETO BANCÁRIO</h3>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                    <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">Valor:</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #e91e63;">
                        R$ <?php echo number_format($total, 2, ',', '.'); ?>
                    </div>
                </div>
                
                <div class="boleto-line" onclick="copiarTexto('<?php echo $codigo_barras; ?>', 'Código de barras copiado!')">
                    <?php echo $codigo_barras; ?>
                </div>
                
                <div style="margin: 20px 0; text-align: center;">
                    <div style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">Linha digitável:</div>
                    <div class="boleto-line" onclick="copiarTexto('<?php echo $linha_digitavel; ?>', 'Linha digitável copiada!')">
                        <?php echo $linha_digitavel; ?>
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn btn-copy" onclick="copiarTexto('<?php echo $linha_digitavel; ?>', 'Código do boleto copiado!')">
                <i class="far fa-copy"></i> COPIAR CÓDIGO DO BOLETO
            </button>
        </div>