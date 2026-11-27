document.addEventListener("DOMContentLoaded", () => {
    const campoCep = document.getElementById("cep");

    if (campoCep) {
        campoCep.addEventListener("blur", async () => {
            const cepLimpo = campoCep.value.replace(/\D/g, "");
            if (cepLimpo.length !== 8) return;

            try {
                const resposta = await fetch(`https://viacep.com.br/ws/${cepLimpo}/json/`);
                const dados = await resposta.json();

                if (dados.erro) {
                    alert("CEP não encontrado.");
                    return;
                }

                const logradouro = document.getElementById("logradouro");
                const bairro = document.getElementById("bairro");
                const cidade = document.getElementById("cidade");
                const estado = document.getElementById("estado");

                if (logradouro && dados.logradouro) logradouro.value = dados.logradouro;
                if (bairro && dados.bairro) bairro.value = dados.bairro;
                if (cidade && dados.localidade) cidade.value = dados.localidade;
                if (estado && dados.uf) estado.value = dados.uf;
            } catch (erro) {
                console.error("Erro ao consultar CEP:", erro);
            }
        });
    }

    const selectPerfil = document.getElementById("perfil");
    const labelCpf = document.querySelector('label[for="cpf"]');
    const blocoDocumento = document.getElementById("bloco-documento");
    const inputDocumento = document.getElementById("documento");

    if (selectPerfil && labelCpf && blocoDocumento && inputDocumento) {
        const atualizarCamposPerfil = () => {
            if (selectPerfil.value === "ong") {
                labelCpf.textContent = "CNPJ";
                blocoDocumento.hidden = false;
                inputDocumento.required = true;
            } else {
                labelCpf.textContent = "CPF";
                blocoDocumento.hidden = true;
                inputDocumento.required = false;
                inputDocumento.value = "";
            }
        };

        selectPerfil.addEventListener("change", atualizarCamposPerfil);
        window.addEventListener("pageshow", atualizarCamposPerfil);
        atualizarCamposPerfil();
    }

    const containerGatos = document.getElementById("lista-gatos");
    if (!containerGatos) return;

    const statusEl = document.getElementById("status-carregamento");
    const statusLocEl = document.getElementById("status-localizacao");
    const inputBusca = document.getElementById("filtro-busca");
    const selectSexo = document.getElementById("filtro-sexo");
    const inputIdade = document.getElementById("filtro-idade");
    const checkFavoritos = document.getElementById("filtro-favoritos");
    const btnLocalizacao = document.getElementById("btn-localizacao");

    let todosGatos = [];
    let favoritos = JSON.parse(localStorage.getItem("meowson_favoritos")) || [];
    let cidadeUsuario = "";
    let filtrarPorCidade = false;

    function normalizarTexto(texto) {
        return (texto || "")
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .trim();
    }

    function salvarFavoritos() {
        localStorage.setItem("meowson_favoritos", JSON.stringify(favoritos));
    }

    function alternarFavorito(id) {
        id = String(id);
        if (favoritos.includes(id)) {
            favoritos = favoritos.filter(favId => favId !== id);
        } else {
            favoritos.push(id);
        }
        salvarFavoritos();
        aplicarFiltros();
    }

    function limparFiltros() {
        if (inputBusca) inputBusca.value = "";
        if (selectSexo) selectSexo.value = "";
        if (inputIdade) inputIdade.value = "";
        if (checkFavoritos) checkFavoritos.checked = false;
        filtrarPorCidade = false;
        atualizarBannerLocalizacao();
        aplicarFiltros();
    }

    function atualizarBannerLocalizacao() {
        if (!statusLocEl || !btnLocalizacao) return;

        if (filtrarPorCidade && cidadeUsuario) {
            statusLocEl.innerHTML = `Mostrando gatinhos em <strong>${cidadeUsuario}</strong>. <button type="button" class="botao btn-inline" id="btn-todas-cidades">Ver todas as cidades</button>`;
            btnLocalizacao.textContent = `📍 ${cidadeUsuario}`;
            document.getElementById("btn-todas-cidades")?.addEventListener("click", () => {
                filtrarPorCidade = false;
                atualizarBannerLocalizacao();
                aplicarFiltros();
            });
        } else if (cidadeUsuario) {
            statusLocEl.textContent = "Exibindo gatinhos de todas as cidades.";
            btnLocalizacao.textContent = `📍 Filtrar: ${cidadeUsuario}`;
        } else {
            statusLocEl.textContent = "";
            btnLocalizacao.textContent = "📍 Minha cidade";
        }
    }

    function detectarCidadeUsuario(silencioso = false) {
        if (!("geolocation" in navigator)) {
            if (!silencioso && statusLocEl) {
                statusLocEl.textContent = "Seu navegador não suporta geolocalização.";
            }
            return;
        }

        if (statusLocEl) {
            statusLocEl.textContent = "Detectando sua cidade...";
        }

        navigator.geolocation.getCurrentPosition(
            async (posicao) => {
                const { latitude, longitude } = posicao.coords;
                try {
                    const url = `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${latitude}&longitude=${longitude}&localityLanguage=pt`;
                    const res = await fetch(url);
                    const dados = await res.json();
                    const cidadeDetectada = dados.city || dados.locality || "";

                    if (cidadeDetectada) {
                        cidadeUsuario = cidadeDetectada;
                        filtrarPorCidade = true;
                        atualizarBannerLocalizacao();
                        aplicarFiltros();
                    } else if (statusLocEl) {
                        statusLocEl.textContent = "Não foi possível identificar sua cidade.";
                    }
                } catch (erro) {
                    console.error("Erro na geocodificação reversa:", erro);
                    if (statusLocEl) statusLocEl.textContent = "";
                }
            },
            () => {
                filtrarPorCidade = false;
                if (statusLocEl) {
                    statusLocEl.textContent = silencioso
                        ? ""
                        : "Permissão de localização negada. Exibindo todas as cidades.";
                }
                aplicarFiltros();
            },
            { timeout: 8000 }
        );
    }

    function renderizarGatos(lista) {
        if (lista.length === 0) {
            const temFiltroAtivo = Boolean(
                (inputBusca?.value || "").trim() ||
                (selectSexo?.value || "") ||
                (inputIdade?.value || "") ||
                checkFavoritos?.checked ||
                filtrarPorCidade
            );

            containerGatos.innerHTML = temFiltroAtivo
                ? `<div class="estado-vazio">
                       <p>Nenhum gatinho encontrado${filtrarPorCidade && cidadeUsuario ? ` em ${cidadeUsuario}` : " com esses filtros"}.</p>
                       <button type="button" class="botao" id="btn-limpar-filtros">Ver todos os gatinhos</button>
                   </div>`
                : `<div class="estado-vazio">
                       <p>Ainda não há gatinhos disponíveis para adoção no momento.</p>
                   </div>`;

            document.getElementById("btn-limpar-filtros")?.addEventListener("click", limparFiltros);
            return;
        }

        containerGatos.innerHTML = lista.map(gato => {
            const ehFavorito = favoritos.includes(String(gato.id));
            const midia = gato.foto
                ? `<img src="uploads/${gato.foto}" alt="Foto de ${gato.nome}">`
                : `<div class="sem-foto" aria-label="Sem foto cadastrada"></div>`;

            const simboloSexo = gato.sexo === "F"
                ? `<span class="simbolo-sexo femea" title="Fêmea" aria-label="Fêmea">&#9792;</span>`
                : `<span class="simbolo-sexo macho" title="Macho" aria-label="Macho">&#9794;</span>`;

            return `
                <div class="campo card-gato">
                    ${midia}
                    <div class="card-topo">
                        <h2>${gato.nome} ${simboloSexo}</h2>
                        <button type="button" class="btn-fav ${ehFavorito ? "ativo" : ""}" data-id="${gato.id}" aria-label="Favoritar ${gato.nome}">
                            ${ehFavorito ? "♥" : "♡"}
                        </button>
                    </div>
                    <p class="card-info">${gato.raca || "SRD"} • ${gato.idade} meses</p>
                    <p class="card-info">${gato.cidade || ""} - ${gato.estado || ""}</p>
                    <form method="get" action="detalhes_gato.php">
                        <input type="hidden" name="id" value="${gato.id}">
                        <button class="botao" type="submit">Ver detalhes</button>
                    </form>
                </div>
            `;
        }).join("");

        containerGatos.querySelectorAll(".btn-fav").forEach(btn => {
            btn.addEventListener("click", () => alternarFavorito(btn.dataset.id));
        });
    }

    function aplicarFiltros() {
        const termo = normalizarTexto(inputBusca?.value);
        const sexo = selectSexo?.value || "";
        const idadeMax = parseInt(inputIdade?.value, 10);
        const soFav = checkFavoritos?.checked || false;
        const cidadeAlvo = normalizarTexto(cidadeUsuario);

        const filtrados = todosGatos.filter(gato => {
            const bateTexto = !termo ||
                normalizarTexto(gato.nome).includes(termo) ||
                normalizarTexto(gato.raca).includes(termo) ||
                normalizarTexto(gato.cidade).includes(termo);

            const bateSexo = !sexo || gato.sexo === sexo;
            const bateIdade = isNaN(idadeMax) || Number(gato.idade) <= idadeMax;
            const bateFav = !soFav || favoritos.includes(String(gato.id));
            const bateCidade = !filtrarPorCidade || !cidadeAlvo ||
                normalizarTexto(gato.cidade) === cidadeAlvo;

            return bateTexto && bateSexo && bateIdade && bateFav && bateCidade;
        });

        if (statusEl) {
            statusEl.style.display = "block";
            statusEl.textContent = todosGatos.length === 0
                ? ""
                : `Mostrando ${filtrados.length} de ${todosGatos.length} gatinho(s)`;
        }

        renderizarGatos(filtrados);
    }

    fetch("api_gatos.php")
        .then(res => res.json())
        .then(dados => {
            todosGatos = dados;
            if (statusEl) statusEl.style.display = "none";
            aplicarFiltros();
            detectarCidadeUsuario(true);
        })
        .catch(() => {
            if (statusEl) statusEl.textContent = "Erro ao carregar os gatinhos.";
        });

    btnLocalizacao?.addEventListener("click", () => {
        if (cidadeUsuario) {
            filtrarPorCidade = !filtrarPorCidade;
            atualizarBannerLocalizacao();
            aplicarFiltros();
        } else {
            detectarCidadeUsuario(false);
        }
    });

    inputBusca?.addEventListener("input", aplicarFiltros);
    selectSexo?.addEventListener("change", aplicarFiltros);
    inputIdade?.addEventListener("input", aplicarFiltros);
    checkFavoritos?.addEventListener("change", aplicarFiltros);
});