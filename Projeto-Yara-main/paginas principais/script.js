// === Funções de contato ===
function iniciarChat() {
    window.location.href = 'chat.php';
}

function abrirWhatsApp() {
    const numero = '5511999999999';
    const mensagem = 'Olá, gostaria de mais informações sobre as joias YARA.';
    const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagem)}`;
    window.open(url, '_blank');
}

function iniciarChatEspecialista() {
    window.location.href = 'chat.php?tipo=especialista';
}

function agendarVisita() {
    window.location.href = 'agendamento.php';
}

// === Funções JavaScript principais ===
document.addEventListener('DOMContentLoaded', function() {
    // --- MENU DO USUÁRIO ---
    const usuarioLogado = document.getElementById('usuarioLogado');
    const menuUsuario = document.getElementById('menuUsuario');
    const sairConta = document.getElementById('sairConta');

    if (usuarioLogado && menuUsuario) {
        usuarioLogado.addEventListener('click', function(e) {
            e.stopPropagation();
            menuUsuario.classList.toggle('mostrar');
        });

        // Fechar menu ao clicar fora
        document.addEventListener('click', function() {
            menuUsuario.classList.remove('mostrar');
        });

        // Logout
        if (sairConta) {
            sairConta.addEventListener('click', function(e) {
                e.preventDefault();
                
                fetch('processa_form.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'acao=logout'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarMensagem(data.message, 'sucesso');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        mostrarMensagem(data.message, 'erro');
                    }
                });
            });
        }
    }

    // --- BARRA DE PESQUISA ---
    const abrirPesquisa = document.getElementById('abrirPesquisa');
    const barraPesquisa = document.getElementById('barraPesquisa');
    const inputPesquisa = document.getElementById('inputPesquisa');
    const resultadosPesquisa = document.getElementById('resultadosPesquisa');

    if (abrirPesquisa) {
        abrirPesquisa.addEventListener('click', function(e) {
            e.stopPropagation();
            barraPesquisa.classList.toggle('ativa');
            if (barraPesquisa.classList.contains('ativa')) {
                inputPesquisa.focus();
            }
        });
    }

    document.addEventListener('click', function(e) {
        if (barraPesquisa && !barraPesquisa.contains(e.target) && e.target !== abrirPesquisa) {
            barraPesquisa.classList.remove('ativa');
        }
    });

    if (inputPesquisa) {
        inputPesquisa.addEventListener('input', function() {
            const termo = this.value.trim();
            if (termo.length > 2) {
                buscarProdutos(termo);
            } else {
                resultadosPesquisa.innerHTML = '';
            }
        });
    }

    function buscarProdutos(termo) {
    console.log('Buscando por:', termo); // Para debug
    
    fetch('buscar_produtos.php?termo=' + encodeURIComponent(termo))
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na rede: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Resposta:', data); // Para debug
            
            if (resultadosPesquisa) {
                resultadosPesquisa.innerHTML = '';
                
                if (data.success && data.produtos && data.produtos.length > 0) {
                    data.produtos.forEach(produto => {
                        const item = document.createElement('div');
                        item.className = 'resultado-item';
                        
                        // Verificar se a imagem existe, caso contrário usar uma padrão
                        const imagemSrc = produto.imagem && produto.imagem !== '' ? 
                            `imgs/${produto.imagem}` : 'imgs/produto-padrao.png';
                        
                        item.innerHTML = `
                            <img src="${imagemSrc}" alt="${produto.nome}" onerror="this.src='imgs/produto-padrao.png'">
                            <div class="resultado-info">
                                <h4>${produto.nome}</h4>
                                <div class="preco">R$ ${parseFloat(produto.preco).toFixed(2)}</div>
                            </div>
                        `;
                        
                        item.addEventListener('click', function() {
                            window.location.href = `produto_detalhe.php?id=${produto.id}`;
                        });
                        
                        resultadosPesquisa.appendChild(item);
                    });
                } else {
                    resultadosPesquisa.innerHTML = `
                        <div style="padding: 20px; text-align: center; color: #666;">
                            <i class="fas fa-search" style="font-size: 24px; margin-bottom: 10px;"></i>
                            <p>Nenhum produto encontrado para "${termo}"</p>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            if (resultadosPesquisa) {
                resultadosPesquisa.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: #e74c3c;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-bottom: 10px;"></i>
                        <p>Erro ao buscar produtos. Tente novamente.</p>
                    </div>
                `;
            }
        });
}

    // --- FORMULÁRIOS AJAX ---
    const formLogin = document.getElementById('formLogin');
    const formCadastro = document.getElementById('formCadastro');
    const formNewsletter = document.getElementById('newsletterForm');

    function mostrarMensagem(mensagem, tipo) {
        const mensagemEl = document.getElementById('mensagemFeedback');
        if (mensagemEl) {
            mensagemEl.textContent = mensagem;
            mensagemEl.className = `mensagem ${tipo}`;
            mensagemEl.style.display = 'block';
            
            setTimeout(() => {
                mensagemEl.style.display = 'none';
            }, 5000);
        }
    }

    if (formLogin) {
        formLogin.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('acao', 'login');
            
            fetch('processa_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensagem(data.message, 'sucesso');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarMensagem(data.message, 'erro');
                }
            });
        });
    }

    if (formCadastro) {
        formCadastro.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('acao', 'cadastro');
            
            fetch('processa_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensagem(data.message, 'sucesso');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarMensagem(data.message, 'erro');
                }
            });
        });
    }

    if (formNewsletter) {
        formNewsletter.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('acao', 'newsletter');
            
            fetch('processa_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensagem(data.message, 'sucesso');
                    this.reset();
                } else {
                    mostrarMensagem(data.message, 'erro');
                }
            });
        });
    }

    // --- MODAL DE CONTATO ---
    const openContact = document.getElementById('openContact');
    const contactOverlay = document.getElementById('contactOverlay');
    const closeX = document.getElementById('closeX');
    const closeModalBtn = document.getElementById('closeModalBtn');

    function openContactModal() {
      if (!contactOverlay) return;
      contactOverlay.style.display = 'flex';
      contactOverlay.setAttribute('aria-hidden', 'false');
      const sel = document.getElementById('locationSelect');
      if (sel) sel.focus();
      document.body.style.overflow = 'hidden';
    }

    function closeContactModal() {
      if (!contactOverlay) return;
      contactOverlay.style.display = 'none';
      contactOverlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (openContact) openContact.focus();
    }

    if (openContact) openContact.addEventListener('click', e => { e.preventDefault(); openContactModal(); });
    if (closeX) closeX.addEventListener('click', closeContactModal);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeContactModal);
    if (contactOverlay) contactOverlay.addEventListener('click', e => { if (e.target === contactOverlay) closeContactModal(); });

    const modalBox = document.querySelector('.contact-modal');
    if (modalBox) modalBox.addEventListener('click', e => e.stopPropagation());

    // --- MODAIS DE LOGIN E CADASTRO ---
    const perfilIcon = document.querySelector('.top-right-icons img[alt="Usuário"]');
    const loginOverlay = document.getElementById('loginOverlay');
    const signupOverlay = document.getElementById('signupOverlay');
    const closeLoginX = document.getElementById('closeLoginX');
    const closeSignupX = document.getElementById('closeSignupX');
    const linkCadastro = document.querySelector('#loginOverlay .link-cadastro');
    const goToLogin = document.getElementById('goToLogin');

    function openLogin() {
      if (!loginOverlay) return;
      loginOverlay.style.display = 'flex';
      loginOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      const firstInput = loginOverlay.querySelector('input');
      if (firstInput) firstInput.focus();
    }

    function closeLogin() {
      if (!loginOverlay) return;
      loginOverlay.style.display = 'none';
      loginOverlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (perfilIcon) perfilIcon.focus();
    }

    if (perfilIcon) perfilIcon.addEventListener('click', e => { e.preventDefault(); openLogin(); });
    if (closeLoginX) closeLoginX.addEventListener('click', closeLogin);
    if (loginOverlay) loginOverlay.addEventListener('click', e => { if (e.target === loginOverlay) closeLogin(); });
    const loginInner = document.querySelector('#loginOverlay .login-modal');
    if (loginInner) loginInner.addEventListener('click', e => e.stopPropagation());

    function openSignup() {
      if (!signupOverlay) return;
      signupOverlay.style.display = 'flex';
      signupOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      const firstInput = signupOverlay.querySelector('input');
      if (firstInput) firstInput.focus();
    }

    function closeSignup() {
      if (!signupOverlay) return;
      signupOverlay.style.display = 'none';
      signupOverlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    if (closeSignupX) closeSignupX.addEventListener('click', closeSignup);
    if (signupOverlay) signupOverlay.addEventListener('click', e => { if (e.target === signupOverlay) closeSignup(); });
    const signupInner = document.querySelector('#signupOverlay .login-modal');
    if (signupInner) signupInner.addEventListener('click', e => e.stopPropagation());

    if (linkCadastro) {
      linkCadastro.addEventListener('click', e => {
        e.preventDefault();
        closeLogin();
        openSignup();
      });
    }

    if (goToLogin) {
      goToLogin.addEventListener('click', e => {
        e.preventDefault();
        closeSignup();
        openLogin();
      });
    }

    // --- NEWSLETTER E ESC ---
    const confirmEmailBtn = document.getElementById('confirmEmailBtn');
    const newsletterCheckbox = document.querySelector('.newsletter-section .checkbox input');

    if (confirmEmailBtn) {
      confirmEmailBtn.addEventListener('click', e => {
        e.preventDefault();
        if (!newsletterCheckbox.checked) {
          alert("Você precisa concordar com a Política de Privacidade para continuar.");
          return;
        }
        openSignup();
      });
    }

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        if (loginOverlay && loginOverlay.style.display === 'flex') closeLogin();
        if (signupOverlay && signupOverlay.style.display === 'flex') closeSignup();
        if (contactOverlay && contactOverlay.style.display === 'flex') closeContactModal();
      }
    });

    // --- ÍCONES E REDIRECIONAMENTOS ---
    const heartIcon = document.getElementById('heartIcon');
    if (heartIcon) {
      heartIcon.addEventListener('click', () => {
        window.location.href = 'favoritos.html';
      });
    }

    const loginForm = document.querySelector('#loginOverlay .login-form');
    if (loginForm) {
      loginForm.addEventListener('submit', e => {
        e.preventDefault();
        window.location.href = 'perfil.html';
      });
    }

    const signupForm = document.querySelector('#signupOverlay .login-form');
    if (signupForm) {
      signupForm.addEventListener('submit', e => {
        e.preventDefault();
        window.location.href = 'perfil.html';
      });
    }
});