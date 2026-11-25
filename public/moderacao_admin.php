<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/GatoModel.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

exigirPerfil(['admin']);

$pdo = (new Conexao())->conectar();
$gatoModel = new GatoModel($pdo);
$usuarioModel = new UsuarioModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    $acao = $_POST['acao'] ?? '';
    $motivo = trim($_POST['motivo_recusa'] ?? '');

    if ($tipo === 'gato' && $id > 0 && in_array($acao, ['disponivel', 'recusado'], true)) {
        $gatoModel->moderarAnuncio($id, $acao, $acao === 'recusado' ? $motivo : null);
    }

    if ($tipo === 'ong' && $id > 0 && in_array($acao, ['ativo', 'recusado'], true)) {
        $usuarioModel->atualizarStatusAdmin($id, 'ong', $acao, $acao === 'recusado' ? $motivo : null);
    }

    header("Location: moderacao_admin.php");
    exit;
}

$gatosPendentes = $gatoModel->listarPendentesAdmin();
$ongsPendentes = $usuarioModel->listarOngsPendentes();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Moderação - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
<link rel="icon" type="image/svg+xml" href="img/favicon.svg">
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container">
    <h1>Moderação do Administrador</h1>

    <h2>ONGs Pendentes de Aprovação</h2>
    <?php if (empty($ongsPendentes)): ?>
        <p>Nenhuma ONG pendente.</p>
    <?php else: ?>
        <?php foreach ($ongsPendentes as $ong): ?>
            <div class="campo item-moderacao">
                <strong><?= htmlspecialchars($ong['nome']) ?></strong> (CNPJ: <?= htmlspecialchars($ong['cpf_cnpj']) ?>)
                <p>E-mail: <?= htmlspecialchars($ong['email']) ?> | Telefone: <?= htmlspecialchars($ong['telefone']) ?></p>
                <p>Local: <?= htmlspecialchars($ong['cidade']) ?> - <?= htmlspecialchars($ong['estado']) ?></p>

                <div class="acoes">
                    <form method="post" action="moderacao_admin.php">
                        <input type="hidden" name="tipo" value="ong">
                        <input type="hidden" name="id" value="<?= (int) $ong['id'] ?>">
                        <input type="hidden" name="acao" value="ativo">
                        <button class="botao" type="submit">Aprovar ONG</button>
                    </form>

                    <form method="post" action="moderacao_admin.php">
                        <input type="hidden" name="tipo" value="ong">
                        <input type="hidden" name="id" value="<?= (int) $ong['id'] ?>">
                        <input type="hidden" name="acao" value="recusado">
                        <div class="campo">
                            <label for="motivo_ong_<?= (int) $ong['id'] ?>">Motivo da recusa</label>
                            <input type="text" name="motivo_recusa" id="motivo_ong_<?= (int) $ong['id'] ?>" required>
                        </div>
                        <button class="botao" type="submit">Recusar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <h2>Anúncios de Gatos em Análise</h2>
    <?php if (empty($gatosPendentes)): ?>
        <p>Nenhum anúncio aguardando moderação.</p>
    <?php else: ?>
        <?php foreach ($gatosPendentes as $g): ?>
            <div class="campo item-moderacao">
                <strong><?= htmlspecialchars($g['nome']) ?></strong> (<?= htmlspecialchars($g['raca'] ?? 'SRD') ?> - <?= (int) $g['idade'] ?> meses)
                <p>Responsável: <?= htmlspecialchars($g['nome_doador']) ?> (<?= htmlspecialchars($g['perfil_doador']) ?>)</p>
                <p>Descrição: <?= htmlspecialchars($g['descricao'] ?? 'Sem descrição.') ?></p>

                <div class="acoes">
                    <form method="post" action="moderacao_admin.php">
                        <input type="hidden" name="tipo" value="gato">
                        <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                        <input type="hidden" name="acao" value="disponivel">
                        <button class="botao" type="submit">Aprovar anúncio</button>
                    </form>

                    <form method="post" action="moderacao_admin.php">
                        <input type="hidden" name="tipo" value="gato">
                        <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                        <input type="hidden" name="acao" value="recusado">
                        <div class="campo">
                            <label for="motivo_gato_<?= (int) $g['id'] ?>">Motivo da recusa</label>
                            <input type="text" name="motivo_recusa" id="motivo_gato_<?= (int) $g['id'] ?>" required>
                        </div>
                        <button class="botao" type="submit">Recusar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>