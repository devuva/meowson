<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/GatoModel.php";

exigirPerfil(['protetor', 'ong']);

$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);
$gatos = $model->listarPorUsuario((int) $_SESSION['usuario_id']);

$rotulosStatus = [
    'em_analise' => 'Em análise',
    'disponivel' => 'Disponível',
    'adotado'    => 'Adotado',
    'recusada'   => 'Recusada',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Meus Anúncios - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/svg+xml" href="img/favicon.svg">
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container">
    <h1>Meus Anúncios</h1>

    <?php if (isset($_GET['cadastrado'])): ?>
        <div class="alerta alerta-sucesso">Gato cadastrado com sucesso! O anúncio foi enviado para análise.</div>
    <?php elseif (isset($_GET['editado'])): ?>
        <div class="alerta alerta-sucesso">Anúncio atualizado com sucesso!</div>
    <?php elseif (isset($_GET['removido'])): ?>
        <div class="alerta alerta-aviso">Anúncio removido.</div>
    <?php endif; ?>

    <p style="margin-bottom: 1.5rem;">
        <a href="cadastrar_gato.php" class="botao">+ Cadastrar novo gato</a>
    </p>

    <?php if (empty($gatos)): ?>
        <div class="estado-vazio">
            <p>Você ainda não possui gatos cadastrados.</p>
        </div>
    <?php else: ?>
        <div class="lista-gatos">
            <?php foreach ($gatos as $gato): ?>
                <?php
                    $st = $gato['status'] ?? 'em_analise';
                    $textoStatus = $rotulosStatus[$st] ?? ucfirst(str_replace('_', ' ', $st));
                ?>
                <div class="campo card-gato">
                    <h2><?= htmlspecialchars($gato['nome']) ?></h2>
                    <p class="card-info">
                        <?= htmlspecialchars($gato['raca'] ?? 'SRD') ?> •
                        <?= (int) $gato['idade'] ?> meses •
                        <?= $gato['sexo'] === 'F' ? 'Fêmea' : 'Macho' ?>
                    </p>
                    <p class="card-info">
                        <span class="tag-status tag-<?= htmlspecialchars($st) ?>"><?= htmlspecialchars($textoStatus) ?></span>
                    </p>

                    <?php if (!empty($gato['motivo_recusa'])): ?>
                        <p class="card-info">Motivo da recusa: <?= htmlspecialchars($gato['motivo_recusa']) ?></p>
                    <?php endif; ?>

                    <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                        <form method="get" action="editar_gato.php">
                            <input type="hidden" name="id" value="<?= (int) $gato['id'] ?>">
                            <button type="submit" class="botao">Editar</button>
                        </form>

                        <form method="post" action="excluir_gato.php">
                            <input type="hidden" name="id" value="<?= (int) $gato['id'] ?>">
                            <button type="submit" class="botao" onclick="return confirm('Remover este anúncio?')">Remover</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>