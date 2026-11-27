<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Gatos para Adoção - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/svg+xml" href="img/favicon.svg">
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<main class="container">
    <div class="topo-vitrine">
        <h1>Encontre seu gato</h1>
        <p class="subtitulo-form">Conheça os gatinhos disponíveis para adoção responsável e filtre pelo perfil ideal para o seu lar.</p>
    </div>

    <section class="filtros" aria-label="Filtrar gatos">
        <div class="campo">
            <label for="filtro-busca">Busca</label>
            <input type="text" id="filtro-busca" placeholder="Nome, raça ou cidade...">
        </div>

        <div class="campo">
            <label for="filtro-sexo">Sexo</label>
            <select id="filtro-sexo">
                <option value="">Todos os sexos</option>
                <option value="M">Macho</option>
                <option value="F">Fêmea</option>
            </select>
        </div>

        <div class="campo">
            <label for="filtro-idade">Idade máxima (meses)</label>
            <input type="number" id="filtro-idade" placeholder="Ex: 12" min="1">
        </div>

        <div class="campo">
            <label class="check-favoritos" for="filtro-favoritos">
                <input type="checkbox" id="filtro-favoritos"> Só favoritos ♥
            </label>
        </div>
    </section>

    <p id="status-carregamento" class="status-vitrine">Carregando gatinhos...</p>
    <div id="lista-gatos" class="lista-gatos"></div>
</main>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>

<script src="js/app.js"></script>
</body>
</html>