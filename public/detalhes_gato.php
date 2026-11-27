<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/GatoModel.php";

$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);

$id = (int) ($_GET['id'] ?? 0);
$gato = $model->buscarDetalhesPublico($id);

if (!$gato) {
    header("Location: gatos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title><?= htmlspecialchars($gato['nome']) ?> - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/svg+xml" href="img/favicon.svg">
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container">
    <a href="gatos.php" class="link-voltar">&larr; Voltar para os gatinhos</a>

    <h1><?= htmlspecialchars($gato['nome']) ?></h1>

    <?php if (!empty($gato['fotos'])): ?>
        <div class="galeria-gato">
            <?php foreach ($gato['fotos'] as $foto): ?>
                <img src="uploads/<?= htmlspecialchars($foto) ?>" alt="Foto de <?= htmlspecialchars($gato['nome']) ?>">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p><strong>Raça:</strong> <?= htmlspecialchars($gato['raca'] ?? 'SRD') ?></p>
    <p><strong>Idade:</strong> <?= (int) $gato['idade'] ?> meses</p>
    <p><strong>Sexo:</strong> <?= $gato['sexo'] === 'F' ? 'Fêmea' : 'Macho' ?></p>
    <p><strong>Vacinas:</strong> <?= htmlspecialchars($gato['vacinas'] ?? 'Nenhuma') ?></p>
    <p><strong>Doenças:</strong> <?= htmlspecialchars($gato['doencas'] ?? 'Nenhuma') ?></p>
    <p><strong>Descrição:</strong> <?= htmlspecialchars($gato['descricao'] ?? 'Sem descrição.') ?></p>
    <p><strong>Observações:</strong> <?= htmlspecialchars($gato['observacoes'] ?? 'Nenhuma.') ?></p>
    <p><strong>Responsável:</strong> <?= htmlspecialchars($gato['nome_doador']) ?> (<?= htmlspecialchars($gato['cidade']) ?> - <?= htmlspecialchars($gato['estado']) ?>)</p>

    <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['perfil'] ?? '') === 'adotante'): ?>
        <form method="post" action="solicitar_adocao.php">
            <input type="hidden" name="gato_id" value="<?= (int) $gato['id'] ?>">
            <div class="campo">
                <label for="mensagem_adotante">Por que você quer adotar <?= htmlspecialchars($gato['nome']) ?>?</label>
                <textarea name="mensagem_adotante" id="mensagem_adotante" required></textarea>
            </div>
            <button class="botao" type="submit">Solicitar adoção</button>
        </form>
    <?php elseif (!isset($_SESSION['usuario_id'])): ?>
        <p><a href="login.html">Faça login como adotante</a> para solicitar a adoção.</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>
</body>
</html>