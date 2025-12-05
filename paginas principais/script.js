// script.js - VERSÃO FINAL INTEGRADA (CEP + LOGIN + CARRINHO + FAVORITOS)

// === 1. Funções Auxiliares e de Contato ===
function iniciarChat() {
    window.location.href = "chat.php";
}

function mostrarMensagem(mensagem, tipo) {
    const mensagemEl = document.getElementById("mensagemFeedback");
    if (mensagemEl) {
        mensagemEl.textContent = mensagem;
        mensagemEl.className = `mensagem ${tipo}`;
        mensagemEl.style.display = "block";
        setTimeout(() => { mensagemEl.style.display = "none"; }, 5000);
    } else {
        if (tipo === "erro") alert(mensagem);
    }
}

function abrirWhatsApp() {
    const numero = "5511999999999";
    const mensagem = "Olá, gostaria de mais informações sobre as joias YARA.";
    const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensagem)}`;
    window.open(url, "_blank");
}

// === 2. Lógica Principal ao Carregar a Página ===
document.addEventListener("DOMContentLoaded", function () {
    
    // --- A. API DE CEP ---
    const cepInput = document.getElementById("cep");
    if (cepInput) {
        const buscarCEP = (cep) => {
            const campos = ['rua', 'bairro', 'cidade', 'estado'];
            campos.forEach(id => {
                const el = document.getElementById(id);
                if(el) { el.value = "..."; el.disabled = true; }
            });
            
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(r => r.json())
                .then(data => {
                    if (!data.erro) {
                        if(document.getElementById('rua')) document.getElementById('rua').value = data.logradouro;
                        if(document.getElementById('bairro')) document.getElementById('bairro').value = data.bairro;
                        if(document.getElementById('cidade')) document.getElementById('cidade').value = data.localidade;
                        if(document.getElementById('estado')) document.getElementById('estado').value = data.uf;
                        if(document.getElementById('numero')) document.getElementById('numero').focus();
                    } else {
                        alert("CEP não encontrado.");
                        limparCamposEndereco();
                    }
                })
                .catch(() => {
                    alert("Erro ao buscar CEP. Verifique sua conexão.");
                    limparCamposEndereco();
                })
                .finally(() => {
                    campos.forEach(id => {
                        const el = document.getElementById(id);
                        if(el) el.disabled = false;
                    });
                });
        };

        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            if (value.length > 5) {
                e.target.value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            } else {
                e.target.value = value;
            }
            if (value.length === 8) { buscarCEP(value); }
        });

        cepInput.addEventListener('blur', function() {
            let cep = this.value.replace(/\D/g, '');
            if (cep.length === 8 && document.getElementById('rua').value === "") {
                buscarCEP(cep);
            } else if (cep.length > 0 && cep.length < 8) {
                alert("Formato de CEP inválido.");
            }
        });

        function limparCamposEndereco() {
            ['rua', 'bairro', 'cidade', 'estado'].forEach(id => {
                const el = document.getElementById(id);
                if(el) el.value = "";
            });
        }
    }

    // --- B. Formulários com Redirecionamento (Login/Cadastro) ---
    const forms = document.querySelectorAll("form");
    forms.forEach((form) => {
        if (form.id === "formLogin" || form.id === "formCadastro" || form.classList.contains("ajax-form")) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                if (!formData.has("acao")) {
                    if (form.id === "formLogin") formData.append("acao", "login");
                    if (form.id === "formCadastro") formData.append("acao", "cadastro");
                }

                fetch("processa_form.php", { method: "POST", body: formData })
                    .then((r) => r.json())
                    .then((data) => {
                        if (data.success) {
                            if(typeof Swal !== 'undefined') {
                                Swal.fire({ title: 'Sucesso!', text: data.message || "Sucesso!", icon: 'success', confirmButtonColor: '#e91e63' });
                            } else {
                                alert(data.message || "Sucesso!");
                            }
                            
                            setTimeout(() => {
                                if (data.redirect) window.location.href = data.redirect;
                                else window.location.reload();
                            }, 1000);
                        } else {
                            if(typeof Swal !== 'undefined') {
                                Swal.fire({ title: 'Erro', text: data.message, icon: 'error', confirmButtonColor: '#e91e63' });
                            } else {
                                alert(data.message);
                            }
                        }
                    })
                    .catch((err) => console.error(err));
            });
        }
    });

    // --- C. Menu do Usuário e Logout ---
    const usuarioLogado = document.getElementById("usuarioLogado");
    const menuUsuario = document.getElementById("menuUsuario");
    const sairConta = document.getElementById("sairConta");

    if (usuarioLogado && menuUsuario) {
        usuarioLogado.addEventListener("click", function (e) {
            e.stopPropagation();
            menuUsuario.classList.toggle("mostrar");
        });
        document.addEventListener("click", () => menuUsuario.classList.remove("mostrar"));
        if (sairConta) {
            sairConta.addEventListener("click", function (e) {
                e.preventDefault();
                fazerLogout();
            });
        }
    }

    // --- D. Barra de Pesquisa ---
    const abrirPesquisa = document.getElementById("abrirPesquisa");
    const barraPesquisa = document.getElementById("barraPesquisa");
    const inputPesquisa = document.getElementById("inputPesquisa");
    const resultadosPesquisa = document.getElementById("resultadosPesquisa");

    if (abrirPesquisa) {
        abrirPesquisa.addEventListener("click", function (e) {
            e.stopPropagation();
            barraPesquisa.classList.toggle("ativa");
            if (barraPesquisa.classList.contains("ativa")) inputPesquisa.focus();
        });
    }

    document.addEventListener("click", function (e) {
        if (barraPesquisa && !barraPesquisa.contains(e.target) && e.target !== abrirPesquisa) {
            barraPesquisa.classList.remove("ativa");
        }
    });

    if (inputPesquisa) {
        inputPesquisa.addEventListener("input", function () {
            const termo = this.value.trim();
            if (termo.length > 2) buscarProdutos(termo);
            else resultadosPesquisa.innerHTML = "";
        });
    }

    // --- E. Modais ---
    const modalOverlays = document.querySelectorAll(".modal-overlay");
    const closeButtons = document.querySelectorAll(".close-modal, .fa-times, .close-x");

    closeButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            modalOverlays.forEach((m) => {
                m.style.display = "none";
                m.classList.remove("mostrar");
            });
            document.body.style.overflow = "";
        });
    });
});

// === 3. Funções Globais ===

function buscarProdutos(termo) {
    const resultadosPesquisa = document.getElementById("resultadosPesquisa");
    fetch("buscar_produtos.php?termo=" + encodeURIComponent(termo))
        .then((r) => r.json())
        .then((data) => {
            resultadosPesquisa.innerHTML = "";
            if (data.success && data.produtos.length > 0) {
                data.produtos.forEach((p) => {
                    const img = p.imagem ? `imgs/${p.imagem}` : "imgs/produto-padrao.png";
                    const item = document.createElement("div");
                    item.className = "resultado-item";
                    item.innerHTML = `<img src="${img}"><div><h4>${p.nome}</h4><div>R$ ${parseFloat(p.preco).toFixed(2)}</div></div>`;
                    item.onclick = () => (window.location.href = `produto_detalhe.php?id=${p.id}`);
                    resultadosPesquisa.appendChild(item);
                });
            } else {
                resultadosPesquisa.innerHTML = '<div style="padding:20px;text-align:center">Nenhum produto encontrado</div>';
            }
        })
        .catch((e) => console.error(e));
}

// === FUNÇÃO DE CARRINHO (PADRONIZADA COM SWEETALERT) ===
function adicionarAoCarrinho(idProduto) {
    const formData = new FormData();
    formData.append("acao", "adicionar_carrinho");
    formData.append("produto_id", idProduto);

    fetch("processa_form.php", { method: "POST", body: formData })
        .then((r) => r.json())
        .then((data) => {
            if (data.success) {
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        title: "Adicionado!",
                        text: "Produto adicionado à sua sacola com sucesso.",
                        icon: "success",
                        confirmButtonColor: "#e91e63",
                        confirmButtonText: 'Continuar comprando',
                        showCancelButton: true,
                        cancelButtonText: 'Ir para o carrinho',
                        cancelButtonColor: '#333'
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href = 'carrinho.php';
                        }
                    });
                } else {
                    alert("Produto adicionado ao carrinho!");
                    if(confirm("Ir para o carrinho?")) window.location.href = 'carrinho.php';
                }
                
                // Atualiza contadores
                const contadores = document.querySelectorAll(".cart-count");
                contadores.forEach((c) => (c.innerText = data.total_carrinho));
            } else {
                // Tratamento para Login Necessário
                if(data.require_login) {
                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            title: 'Faça Login',
                            text: data.message,
                            icon: 'warning',
                            confirmButtonColor: '#333',
                            confirmButtonText: 'Ir para Login'
                        }).then((result) => {
                            if(result.isConfirmed) {
                                // Tenta abrir o modal se a função existir
                                if(typeof window.openLoginModal === 'function') window.openLoginModal();
                                else window.location.href = 'login.php';
                            }
                        });
                    } else {
                        alert(data.message);
                        window.location.href = 'login.php';
                    }
                } else {
                    if (typeof Swal !== "undefined") {
                        Swal.fire("Erro", data.message || "Erro ao adicionar.", "error");
                    } else {
                        alert("Erro: " + data.message);
                    }
                }
            }
        })
        .catch(() => {
            if (typeof Swal !== "undefined") {
                Swal.fire("Erro", "Erro ao conectar com servidor.", "error");
            } else {
                alert("Erro ao conectar com servidor.");
            }
        });
}

function fazerLogout() {
    fetch("processa_form.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "acao=logout",
    })
    .then((r) => r.json())
    .then((data) => {
        if (data.success) window.location.href = "login.php";
    });
}

// Funções para Favoritos
function toggleFavorito(event, element, idProduto) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }

    const formData = new FormData();
    formData.append("acao", "toggle_favorito");
    formData.append("produto_id", idProduto);

    fetch("processa_form.php", { method: "POST", body: formData })
        .then((r) => r.json())
        .then((data) => {
            if (data.success) {
                if (typeof Swal !== "undefined") {
                    const msg = data.favoritado ? "Adicionado aos favoritos!" : "Removido dos favoritos.";
                    const icon = data.favoritado ? "success" : "info";
                    Swal.fire({ toast: true, position: "top-end", icon: icon, title: msg, showConfirmButton: false, timer: 1500 });
                }

                // Alterna ícone
                const icon = element.querySelector("i");
                if (icon) {
                    if (data.favoritado) {
                        icon.classList.remove("far"); icon.classList.add("fas"); icon.style.color = "#e91e63";
                    } else {
                        icon.classList.remove("fas"); icon.classList.add("far"); icon.style.color = "";
                    }
                }
            } else {
                // Se pedir login
                if(data.message.includes("login")) {
                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            title: 'Faça Login', text: data.message, icon: 'warning', confirmButtonColor: '#333', confirmButtonText: 'Login'
                        }).then((res) => {
                            if(res.isConfirmed) {
                                if(typeof window.openLoginModal === 'function') window.openLoginModal();
                                else window.location.href = 'login.php';
                            }
                        });
                    } else {
                        alert(data.message);
                    }
                }
            }
        })
        .catch(() => alert("Erro ao processar favorito."));
}

// === FUNÇÕES DO CARRINHO (ATUALIZAR QTD/REMOVER) ===
function atualizarQtd(idProduto, delta) {
    const input = document.getElementById(`qtd-${idProduto}`);
    let novaQtd = parseInt(input.value) + delta;

    if (novaQtd <= 0) {
        removerDoCarrinho(idProduto);
        return;
    }

    const formData = new FormData();
    formData.append("acao", "atualizar_carrinho");
    formData.append("produto_id", idProduto);
    formData.append("quantidade", novaQtd);

    fetch("processa_form.php", { method: "POST", body: formData })
        .then((r) => r.json())
        .then((data) => {
            if (data.success) location.reload();
            else alert(data.message);
        });
}

f// Função para remover item com SweetAlert
function removerDoCarrinho(idProduto) {
    Swal.fire({
        title: "Tem certeza?",
        text: "Você quer retirar este produto da sua sacola?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#e91e63", // Rosa YARA
        cancelButtonColor: "#333",     // Preto
        confirmButtonText: "Sim, remover",
        cancelButtonText: "Cancelar",
        reverseButtons: true // Inverte a ordem para ficar mais intuitivo
    }).then((result) => {
        if (result.isConfirmed) {
            executarRemocao(idProduto);
        }
    });
}

// Função auxiliar que faz a remoção real
function executarRemocao(idProduto) {
    const formData = new FormData();
    formData.append("acao", "atualizar_carrinho");
    formData.append("produto_id", idProduto);
    formData.append("quantidade", 0); // 0 significa remover

    fetch("processa_form.php", { method: "POST", body: formData })
        .then((r) => r.json())
        .then((data) => {
            if (data.success) {
                // Sucesso: Recarrega a página para atualizar os totais
                Swal.fire({
                    title: "Removido!",
                    text: "O item foi removido.",
                    icon: "success",
                    confirmButtonColor: "#e91e63",
                    timer: 1000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire("Erro", data.message, "error");
            }
        })
        .catch(() => {
            Swal.fire("Erro", "Erro ao conectar com o servidor.", "error");
        });
}