<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/GatoModel.php";

$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);

$filtros = [
    'sexo' => $_GET['sexo'] ?? '',
    'idade_max' => $_GET['idade_max'] ?? '',
    'cidade' => trim($_GET['cidade'] ?? '')
];

$gatos = $model->listarPublico($filtros);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Gatos para Adoção - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="barra">
    <a href="index.html">Meowson</a>
    <nav aria-label="Principal">
        <a href="gatos.php">Adotar</a>
        <a href="cadastro.html">Cadastro</a>
        <a href="login.html">Login</a>
    </nav>
</header>

<div class="container">
    <h1>Gatos Disponíveis para Adoção</h1>

    <form method="get" action="gatos.php" class="filtros">
        <div class="campo">
            <label for="sexo">Sexo</label>
            <select name="sexo" id="sexo">
                <option value="">Todos</option>
                <option value="F" <?= $filtros['sexo'] === 'F' ? 'selected' : '' ?>>Fêmea</option>
                <option value="M" <?= $filtros['sexo'] === 'M' ? 'selected' : '' ?>>Macho</option>
            </select>
        </div>

        <div class="campo">
            <label for="idade_max">Idade máxima (meses)</label>
            <input type="number" name="idade_max" id="idade_max" min="1" value="<?= htmlspecialchars($filtros['idade_max']) ?>">
        </div>

        <div class="campo">
            <label for="cidade">Cidade</label>
            <input type="text" name="cidade" id="cidade" value="<?= htmlspecialchars($filtros['cidade']) ?>">
        </div>

        <button class="botao" type="submit">Filtrar</button>
    </form>

    <?php if (empty($gatos)): ?>
        <p>Nenhum gato encontrado com esses filtros.</p>
    <?php else: ?>
        <div class="lista-gatos">
            <?php foreach ($gatos as $g): ?>
                <div class="campo card-gato">
                    <?php if (!empty($g['foto'])): ?>
                        <img src="uploads/<?= htmlspecialchars($g['foto']) ?>" alt="Foto de <?= htmlspecialchars($g['nome']) ?>">
                    <?php endif; ?>

                    <h2><?= htmlspecialchars($g['nome']) ?></h2>
                    <p>Raça: <?= htmlspecialchars($g['raca'] ?? 'SRD') ?> | Sexo: <?= htmlspecialchars($g['sexo']) ?> | Idade: <?= (int) $g['idade'] ?> meses</p>
                    <p>Local: <?= htmlspecialchars($g['cidade']) ?> - <?= htmlspecialchars($g['estado']) ?></p>

                    <form method="get" action="detalhes_gato.php">
                        <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                        <button class="botao" type="submit">Ver detalhes</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>