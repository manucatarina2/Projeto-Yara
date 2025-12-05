<?php
        $total = $dados_pagamento['total'];
        ?>
        <div class="card-form">
            <div class="card-preview">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <i class="far fa-credit-card fa-2x"></i>
                    <span style="font-size: 1.2rem;">CARTÃO DE DÉBITO</span>
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
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Pagamento à vista - O valor será debitado imediatamente da sua conta.
            </div>
        </div>