<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/GatoModel.php";

exigirPerfil(['protetor', 'ong']);

$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);

$id = (int) ($_GET['id'] ?? 0);
$gato = $model->buscarPorId($id, $_SESSION['usuario_id']);

if (!$gato) {
    header("Location: meus_anuncios.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Editar anúncio - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/svg+xml" href="img/favicon.svg">
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container container-cadastro">
    <a href="meus_anuncios.php" class="link-voltar">&larr; Voltar para meus anúncios</a>

    <h1>Editar anúncio</h1>

    <form method="post" action="atualizar_gato.php" class="form-card">
        <input name="id" type="hidden" value="<?= (int) $gato['id'] ?>">

        <div class="grid-cadastro">
            <div class="campo">
                <label for="nome">Nome</label>
                <input required name="nome" type="text" minlength="1" maxlength="50" id="nome" value="<?= htmlspecialchars($gato['nome']) ?>">
            </div>

            <div class="campo">
                <label for="raca">Raça</label>
                <input required name="raca" type="text" maxlength="30" id="raca" value="<?= htmlspecialchars($gato['raca'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="idade">Idade (em meses)</label>
                <input required name="idade" type="number" min="0" max="300" id="idade" value="<?= (int) $gato['idade'] ?>">
            </div>

            <div class="campo">
                <label for="sexo">Sexo</label>
                <select name="sexo" id="sexo" required>
                    <option value="F" <?= $gato['sexo'] === 'F' ? 'selected' : '' ?>>Fêmea</option>
                    <option value="M" <?= $gato['sexo'] === 'M' ? 'selected' : '' ?>>Macho</option>
                </select>
            </div>

            <div class="campo col-cheia">
                <label for="descricao">Descrição (opcional)</label>
                <textarea name="descricao" id="descricao" rows="3"><?= htmlspecialchars($gato['descricao'] ?? '') ?></textarea>
            </div>

            <div class="campo">
                <label for="vacinas">Vacinas (opcional)</label>
                <textarea name="vacinas" id="vacinas" rows="3"><?= htmlspecialchars($gato['vacinas'] ?? '') ?></textarea>
            </div>

            <div class="campo">
                <label for="doencas">Doenças (opcional)</label>
                <textarea name="doencas" id="doencas" rows="3"><?= htmlspecialchars($gato['doencas'] ?? '') ?></textarea>
            </div>

            <div class="campo col-cheia">
                <label for="observacoes">Observações (opcional)</label>
                <textarea name="observacoes" id="observacoes" rows="3"><?= htmlspecialchars($gato['observacoes'] ?? '') ?></textarea>
            </div>
        </div>

        <button class="botao" type="submit" name="botao">Salvar alterações</button>
    </form>
</div>

</body>
</html>