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
    const inputBusca = document.getElementById("filtro-busca");
    const selectSexo = document.getElementById("filtro-sexo");
    const inputIdade = document.getElementById("filtro-idade");
    const checkFavoritos = document.getElementById("filtro-favoritos");

    let todosGatos = [];
    let favoritos = JSON.parse(localStorage.getItem("meowson_favoritos")) || [];

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
        aplicarFiltros();
    }

    function renderizarGatos(lista) {
        if (lista.length === 0) {
            const temFiltroAtivo = Boolean(
                (inputBusca?.value || "").trim() ||
                (selectSexo?.value || "") ||
                (inputIdade?.value || "") ||
                checkFavoritos?.checked
            );

            containerGatos.innerHTML = temFiltroAtivo
                ? `<div class="estado-vazio">
                       <p>Nenhum gatinho encontrado com esses filtros.</p>
                       <button type="button" class="botao" id="btn-limpar-filtros">Limpar filtros</button>
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
        const termo = (inputBusca?.value || "").toLowerCase().trim();
        const sexo = selectSexo?.value || "";
        const idadeMax = parseInt(inputIdade?.value, 10);
        const soFav = checkFavoritos?.checked || false;

        const filtrados = todosGatos.filter(gato => {
            const bateTexto = !termo ||
                (gato.nome && gato.nome.toLowerCase().includes(termo)) ||
                (gato.raca && gato.raca.toLowerCase().includes(termo)) ||
                (gato.cidade && gato.cidade.toLowerCase().includes(termo));

            const bateSexo = !sexo || gato.sexo === sexo;
            const bateIdade = isNaN(idadeMax) || Number(gato.idade) <= idadeMax;
            const bateFav = !soFav || favoritos.includes(String(gato.id));

            return bateTexto && bateSexo && bateIdade && bateFav;
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
        })
        .catch(() => {
            if (statusEl) statusEl.textContent = "Erro ao carregar os gatinhos.";
        });

    inputBusca?.addEventListener("input", aplicarFiltros);
    selectSexo?.addEventListener("change", aplicarFiltros);
    inputIdade?.addEventListener("input", aplicarFiltros);
    checkFavoritos?.addEventListener("change", aplicarFiltros);
});