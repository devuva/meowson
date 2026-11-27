# 🐱 Meowson — Plataforma de Adoção Responsável de Gatos

Sistema web desenvolvido para conectar protetores independentes e ONGs a adotantes interessados em adoção responsável de gatos, oferecendo moderação de anúncios, filtros dinâmicos, gestão de solicitações e controle de acesso por perfil.

---

## 📋 Funcionalidades Principais

### Visitantes e Adotantes
- **Vitrine de Gatos (`gatos.php`):** Listagem dinâmica via API JSON (`api_gatos.php`) com filtros em tempo real por nome/raça/cidade, sexo, idade máxima (em meses) e favoritos salvos no navegador (`localStorage`).
- **Detalhes do Animal (`detalhes_gato.php`):** Exibição de galeria de fotos, histórico de vacinas, condições de saúde, temperamento, localização e dados do responsável.
- **Solicitação de Adoção (`solicitar_adocao.php`):** Envio de mensagem de interesse para adotantes autenticados, com prevenção contra solicitações duplicadas para o mesmo animal.
- **Minhas Solicitações (`minhas_solicitacoes.php`):** Acompanhamento de status (`Pendente`, `Aprovada`, `Recusada`, `Encerrada`), visualização de motivo de recusa e opção de cancelamento enquanto pendente.

### Protetores Independentes e ONGs
- **Cadastro e Edição de Anúncios (`cadastrar_gato.php`, `editar_gato.php`):** Formulários com upload múltiplo de imagens (validação de MIME type real via `finfo` para JPG, PNG e WEBP de até 2 MB).
- **Meus Anúncios (`meus_anuncios.php`):** Painel de acompanhamento do status de moderação (`Em análise`, `Disponível`, `Adotado`, `Recusada`), edição e remoção de anúncios.
- **Gestão de Solicitações Recebidas (`gerenciar_solicitacoes.php`):** Aprovação ou recusa (com justificativa) das candidaturas enviadas pelos adotantes.

### Administração e Segurança
- **Controle de Acesso por Perfil (`config/auth.php`):** Proteção de rotas por sessão PHP (`adotante`, `protetor`, `ong`, `admin`).
- **Autopreenchimento de Endereço (`js/app.js`):** Integração com a API pública **ViaCEP** no formulário de cadastro de usuários (`cadastro.html`), além de alternância dinâmica entre CPF e CNPJ conforme o perfil selecionado.
- **Segurança de Dados:** Consultas parametrizadas com **PDO** (prevenção contra SQL Injection), limpeza de saída com `htmlspecialchars` (prevenção contra XSS) e hash seguro de senhas (`password_hash`).

---

## 🛠️ Tecnologias Utilizadas

- **Back-end:** PHP 8+ (arquitetura organizada em `config/`, `models/`, `includes/` e `public/`)
- **Banco de Dados:** MySQL / MariaDB com PDO
- **Front-end:** HTML5, CSS3 (Grid e Flexbox responsivos) e JavaScript (ES6+ / Fetch API)
- **Servidor Local:** Apache (XAMPP)

---

## 📁 Estrutura de Diretórios

```text
meowson/
├── config/
│   ├── Conexao.php          # Configuração e conexão PDO com o MySQL
│   └── auth.php             # Controle de sessão e permissão por perfil
├── includes/
│   ├── header.php           # Barra de navegação dinâmica conforme o usuário logado
│   └── footer.php           # Rodapé com curiosidades aleatórias sobre gatos
├── models/
│   ├── GatoModel.php        # Regras de negócio e consultas de gatos/fotos
│   └── SolicitacaoModel.php # Regras de negócio de solicitações de adoção
├── public/
│   ├── css/
│   │   └── style.css        # Folha de estilos global
│   ├── img/                 # Ícones e identidade visual (SVG)
│   ├── js/
│   │   └── app.js           # ViaCEP, alternância de perfil e filtros da vitrine
│   ├── uploads/             # Armazenamento local das fotos dos gatos
│   ├── api_gatos.php        # Endpoint JSON para listagem pública
│   ├── cadastrar_gato.php   # Formulário e processamento de novo anúncio
│   ├── cadastro.html        # Cadastro de novos usuários
│   ├── detalhes_gato.php    # Página de detalhes e formulário de adoção
│   ├── editar_gato.php      # Edição de anúncio existente
│   ├── gatos.php            # Vitrine principal de adoção
│   ├── gerenciar_solicitacoes.php # Aprovação/recusa de pedidos recebidos
│   ├── login.html           # Autenticação de usuários
│   ├── meus_anuncios.php    # Painel do protetor/ONG
│   ├── minhas_solicitacoes.php # Painel do adotante
│   ├── negado.html          # Página de acesso não autorizado
│   └── solicitar_adocao.php # Processamento de pedido de adoção
└── README.md