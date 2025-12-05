<?php
        $total = $dados_pagamento['total'];
        $parcelas = $dados_pagamento['parcelas'] ?? [];
        ?>
        <div class="card-form">
            <div class="card-preview">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <i class="far fa-credit-card fa-2x"></i>
                    <span style="font-size: 1.2rem;">CARTÃO DE CRÉDITO</span>
                </div>
                <div class="card-number" id="cardPreview">•••• •••• •••• ••••</div>
                <div class="card-info">
                    <div>
                        <small>Nome</small>
                        <div id="namePreview">SEU NOME</div>
                    </div>
                    <div>
                        <small>Validade</small>
                        <div id="expiryPreview">MM/AA</div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="card_nome">Nome no Cartão *</label>
                <input type="text" id="card_nome" name="card_nome" class="form-control" required
                       oninput="document.getElementById('namePreview').textContent = this.value || 'SEU NOME'">
            </div>
            
            <div class="form-group">
                <label for="card_numero">Número do Cartão *</label>
                <input type="text" id="card_numero" name="card_numero" class="form-control" required
                       placeholder="0000 0000 0000 0000"
                       oninput="formatCardNumber(this)">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="card_validade">Validade (MM/AA) *</label>
                    <input type="text" id="card_validade" name="card_validade" class="form-control" required
                           placeholder="MM/AA"
                           oninput="formatExpiry(this)">
                </div>
                <div class="form-group">
                    <label for="card_cvv">CVV *</label>
                    <input type="text" id="card_cvv" name="card_cvv" class="form-control" required
                           placeholder="123" maxlength="4">
                </div>
            </div>
            
            <div class="form-group">
                <label for="parcelas">Parcelamento</label>
                <select id="parcelas" name="parcelas" class="form-control">
                    <?php foreach ($parcelas as $vezes => $parcela): ?>
                    <option value="<?php echo $vezes; ?>">
                        <?php echo "{$vezes}x de R$ " . number_format($parcela['valor_parcela'], 2, ',', '.'); ?>
                        <?php echo ($vezes == 1) ? ' (à vista)' : ' sem juros'; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>