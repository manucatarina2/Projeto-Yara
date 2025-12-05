<?php
        $total_pix = $dados_pagamento['total'];
        $codigo_pix = $dados_pagamento['codigo_pix'];
        $qr_code = $dados_pagamento['qr_code'];
        $vencimento = $dados_pagamento['vencimento'];
        ?>
        <div class="pix-container">
            <div class="pix-discount-badge">
                <i class="fas fa-tag"></i> 15% DE DESCONTO APLICADO
            </div>
            
            <p style="margin-bottom: 20px;">Valor com desconto: <strong>R$ <?php echo number_format($total_pix, 2, ',', '.'); ?></strong></p>
            
            <div class="alert alert-info">
                <i class="fas fa-clock"></i>
                <div>
                    <strong>Vencimento:</strong> <?php echo $vencimento; ?>
                    <br><small>Este código PIX expira em 30 minutos</small>
                </div>
            </div>
            
            <div class="qrcode-container">
                <img src="<?php echo $qr_code; ?>" alt="QR Code PIX" style="max-width: 250px;">
            </div>
            
            <div class="pix-code-box" id="pixCode">
                <?php echo $codigo_pix; ?>
            </div>
            
            <button type="button" class="btn btn-copy" onclick="copiarCodigoPix()">
                <i class="far fa-copy"></i> COPIAR CÓDIGO PIX
            </button>
        </div>