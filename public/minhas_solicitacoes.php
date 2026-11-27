<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/SolicitacaoModel.php";

exigirPerfil(['adotante']);

$pdo = (new Conexao())->conectar();
$model = new SolicitacaoModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_id'])) {
    $model->cancelar((int) $_POST['cancelar_id'], (int) $_SESSION['usuario_id']);
    header("Location: minhas_solicitacoes.php?cancelada=1");
    exit;
}

$solicitacoes = $model->listarPorAdotante((int) $_SESSION['usuario_id']);

$rotulosStatus = [
    'pendente'  => 'Pendente',
    'aprovada'  => 'Aprovada',
    'recusada'  => 'Recusada',
    'encerrada' => 'Encerrada',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Minhas Solicitações - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/svg+xml" href="img/favicon.svg">
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container">
    <h1>Minhas Solicitações de Adoção</h1>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alerta alerta-sucesso">Solicitação de adoção enviada com sucesso!</div>
    <?php elseif (isset($_GET['duplicada'])): ?>
        <div class="alerta alerta-aviso">Você já enviou uma solicitação de adoção para este gato.</div>
    <?php elseif (isset($_GET['cancelada'])): ?>
        <div class="alerta alerta-aviso">Solicitação cancelada.</div>
    <?php endif; ?>

    <?php if (empty($solicitacoes)): ?>
        <div class="estado-vazio">
            <p>Você ainda não enviou nenhuma solicitação de adoção.</p>
            <a href="gatos.php" class="botao">Explorar gatinhos</a>
        </div>
    <?php else: ?>
        <?php foreach ($solicitacoes as $s): ?>
            <?php
                $st = $s['status'] ?? 'pendente';
                $textoStatus = $rotulosStatus[$st] ?? ucfirst($st);
            ?>
            <div class="campo item-solicitacao">
                <h2>Gato: <?= htmlspecialchars($s['nome_gato']) ?></h2>
                <p><strong>Responsável:</strong> <?= htmlspecialchars($s['nome_doador']) ?></p>
                <p><strong>Sua mensagem:</strong> <?= htmlspecialchars($s['mensagem_adotante']) ?></p>
                <p><strong>Status:</strong> <span class="tag-status tag-<?= htmlspecialchars($st) ?>"><?= htmlspecialchars($textoStatus) ?></span></p>

                <?php if ($s['status'] === 'recusada' && !empty($s['motivo_recusa'])): ?>
                    <p><strong>Motivo da recusa:</strong> <?= htmlspecialchars($s['motivo_recusa']) ?></p>
                <?php endif; ?>

                <?php if ($s['status'] === 'pendente'): ?>
                    <form method="post" action="minhas_solicitacoes.php" onsubmit="return confirm('Deseja cancelar esta solicitação?')">
                        <input type="hidden" name="cancelar_id" value="<?= (int) $s['id'] ?>">
                        <button class="botao" type="submit">Cancelar solicitação</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
</body>
</html>