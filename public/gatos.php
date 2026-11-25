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
    <a href="gatos.php">Meowson</a>
    <nav aria-label="Principal">
        <a href="gatos.php">Adotar</a>
        <?php if (!isset($_SESSION['usuario_id'])): ?>
            <a href="cadastro.html">Cadastro</a>
            <a href="login.html">Login</a>
        <?php else: ?>
            <?php $perfil = $_SESSION['perfil'] ?? ''; ?>
            <?php if ($perfil === 'adotante'): ?>
                <a href="minhas_solicitacoes.php">Minhas Solicitações</a>
            <?php elseif ($perfil === 'ong' || $perfil === 'protetor'): ?>
                <a href="meus_anuncios.php">Meus Anúncios</a>
                <a href="gerenciar_solicitacoes.php">Solicitações</a>
            <?php elseif ($perfil === 'admin'): ?>
                <a href="moderacao_admin.php">Moderação</a>
                <a href="gerenciar_usuarios.php">Usuários</a>
            <?php endif; ?>
            <a href="logout.php">Sair</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h1>Encontre seu gato</h1>

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
                    <?php else: ?>
                        <div class="sem-foto" aria-label="Sem foto cadastrada"></div>
                    <?php endif; ?>

                    <div class="card-topo">
                        <h2><?= htmlspecialchars($g['nome']) ?></h2>
                        <?php if ($g['sexo'] === 'F'): ?>
                            <span class="simbolo-sexo femea" title="Fêmea" aria-label="Fêmea">&#9792;</span>
                        <?php else: ?>
                            <span class="simbolo-sexo macho" title="Macho" aria-label="Macho">&#9794;</span>
                        <?php endif; ?>
                    </div>

                    <p class="card-info"><?= htmlspecialchars($g['raca'] ?? 'SRD') ?> • <?= (int) $g['idade'] ?> meses</p>
                    <p class="card-info"><?= htmlspecialchars($g['cidade']) ?> - <?= htmlspecialchars($g['estado']) ?></p>

                    <form method="get" action="detalhes_gato.php">
                        <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                        <button class="botao" type="submit">Ver detalhes</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<script src="js/app.js"></script>
</body>
</html>