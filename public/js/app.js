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

    const formFiltros = document.querySelector("form.filtros");

    if (formFiltros) {
        const selectSexo = document.getElementById("sexo");
        const inputIdade = document.getElementById("idade_max");
        const inputCidade = document.getElementById("cidade");
        const paramsUrl = new URLSearchParams(window.location.search);

        if (!paramsUrl.has("sexo") && !paramsUrl.has("idade_max") && !paramsUrl.has("cidade")) {
            const salvos = JSON.parse(localStorage.getItem("meowson_filtros") || "null");

            if (salvos && (salvos.sexo || salvos.idade_max || salvos.cidade)) {
                if (selectSexo) selectSexo.value = salvos.sexo || "";
                if (inputIdade) inputIdade.value = salvos.idade_max || "";
                if (inputCidade) inputCidade.value = salvos.cidade || "";
            }
        }

        formFiltros.addEventListener("submit", () => {
            const filtrosAtuais = {
                sexo: selectSexo ? selectSexo.value : "",
                idade_max: inputIdade ? inputIdade.value : "",
                cidade: inputCidade ? inputCidade.value.trim() : ""
            };
            localStorage.setItem("meowson_filtros", JSON.stringify(filtrosAtuais));
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
        atualizarCamposPerfil();
    }
});