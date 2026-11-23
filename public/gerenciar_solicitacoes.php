<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/SolicitacaoModel.php";

exigirPerfil(['ong', 'protetor']);

$pdo = (new Conexao())->conectar();
$model = new SolicitacaoModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $decisao = $_POST['decisao'] ?? '';
    $motivo = trim($_POST['motivo_recusa'] ?? '');

    if ($id > 0 && in_array($decisao, ['aprovada', 'recusada'], true)) {
        $model->responder($id, (int) $_SESSION['usuario_id'], $decisao, $decisao === 'recusada' ? $motivo : null);
    }

    header("Location: gerenciar_solicitacoes.php");
    exit;
}

$solicitacoes = $model->listarRecebidas((int) $_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Solicitações Recebidas - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="barra">
    <a href="index.html">Meowson</a>
    <nav aria-label="Principal">
        <a href="meus_anuncios.php">Meus Anúncios</a>
        <a href="gerenciar_solicitacoes.php">Solicitações</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">
    <h1>Solicitações de Adoção Recebidas</h1>

    <?php if (empty($solicitacoes)): ?>
        <p>Nenhuma solicitação recebida até o momento.</p>
    <?php else: ?>
        <?php foreach ($solicitacoes as $s): ?>
            <div class="campo item-solicitacao">
                <h2>Gato: <?= htmlspecialchars($s['nome_gato']) ?></h2>
                <p><strong>Candidato:</strong> <?= htmlspecialchars($s['nome_adotante']) ?> (<?= htmlspecialchars($s['cidade']) ?> - <?= htmlspecialchars($s['estado']) ?>)</p>
                <p><strong>Contato:</strong> <?= htmlspecialchars($s['email_adotante']) ?> | <?= htmlspecialchars($s['telefone_adotante']) ?></p>
                <p><strong>Mensagem:</strong> <?= htmlspecialchars($s['mensagem_adotante']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($s['status']) ?></p>

                <?php if ($s['status'] === 'pendente'): ?>
                    <div class="acoes">
                        <form method="post" action="gerenciar_solicitacoes.php">
                            <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                            <input type="hidden" name="decisao" value="aprovada">
                            <button class="botao" type="submit">Aprovar adoção</button>
                        </form>

                        <form method="post" action="gerenciar_solicitacoes.php">
                            <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                            <input type="hidden" name="decisao" value="recusada">
                            <div class="campo">
                                <label for="motivo_<?= (int) $s['id'] ?>">Motivo da recusa</label>
                                <input type="text" name="motivo_recusa" id="motivo_<?= (int) $s['id'] ?>" required>
                            </div>
                            <button class="botao" type="submit">Recusar</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>