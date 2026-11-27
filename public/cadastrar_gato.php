<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/GatoModel.php";
require_once __DIR__ . "/../config/auth.php";

exigirPerfil(['protetor', 'ong']);

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = $_POST;
    $dados['usuario_id'] = (int) $_SESSION['usuario_id'];

    $pdo = (new Conexao())->conectar();
    $model = new GatoModel($pdo);

    $fotos = [];
    $pasta = __DIR__ . "/uploads";

    if (isset($_FILES['fotos']) && is_array($_FILES['fotos']['name'])) {
        $leitor = finfo_open(FILEINFO_MIME_TYPE);

        foreach ($_FILES['fotos']['name'] as $i => $nomeOriginal) {
            $temp = $_FILES['fotos']['tmp_name'][$i];

            if ($_FILES['fotos']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            if ($_FILES['fotos']['size'][$i] > (2 * 1024 * 1024)) {
                continue;
            }

            $tipo = finfo_file($leitor, $temp);
            if (!in_array($tipo, ['image/jpeg', 'image/webp', 'image/png'])) {
                continue;
            }

            $hex = bin2hex(random_bytes(8));
            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
            $nome_final = $hex . "." . $extensao;

            if (move_uploaded_file($temp, $pasta . "/" . $nome_final)) {
                $fotos[] = $nome_final;
            }
        }
    }

    if (empty($fotos)) {
        $erro = "Envie pelo menos uma foto válida (JPG, PNG ou WEBP de até 2 MB)!";
    } else {
        $model->cadastrar($dados, $fotos);
        header("Location: meus_anuncios.php?cadastrado=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Cadastrar Gato - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/svg+xml" href="img/favicon.svg">
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container container-cadastro">
    <a href="meus_anuncios.php" class="link-voltar">&larr; Voltar para meus anúncios</a>

    <h1>Cadastrar gato para adoção</h1>

    <?php if ($erro): ?>
        <div class="alerta alerta-aviso"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" action="cadastrar_gato.php" class="form-card">
        <div class="grid-cadastro">
            <div class="campo">
                <label for="nome">Nome do gato</label>
                <input type="text" id="nome" name="nome" maxlength="100" required>
            </div>

            <div class="campo">
                <label for="raca">Raça (opcional)</label>
                <input type="text" id="raca" name="raca" maxlength="60" placeholder="Ex: SRD, Siamês...">
            </div>

            <div class="campo">
                <label for="idade">Idade (em meses)</label>
                <input type="number" id="idade" name="idade" min="0" max="300" required>
            </div>

            <div class="campo">
                <label for="sexo">Sexo</label>
                <select id="sexo" name="sexo" required>
                    <option value="">Selecione</option>
                    <option value="M">Macho</option>
                    <option value="F">Fêmea</option>
                </select>
            </div>

            <div class="campo col-cheia">
                <label for="descricao">Descrição / Temperamento (opcional)</label>
                <textarea id="descricao" name="descricao" rows="3"></textarea>
            </div>

            <div class="campo">
                <label for="vacinas">Vacinas (opcional)</label>
                <textarea id="vacinas" name="vacinas" rows="3" placeholder="Ex: V4, Antirrábica..."></textarea>
            </div>

            <div class="campo">
                <label for="doencas">Doenças / Condições de saúde (opcional)</label>
                <textarea id="doencas" name="doencas" rows="3" placeholder="Ex: FIV/FeLV negativo, nenhuma..."></textarea>
            </div>

            <div class="campo col-cheia">
                <label for="observacoes">Observações adicionais (opcional)</label>
                <textarea id="observacoes" name="observacoes" rows="3"></textarea>
            </div>

            <div class="campo col-cheia">
                <label for="fotos">Fotos do gato (JPG, PNG ou WEBP - máx. 2 MB cada)</label>
                <input type="file" id="fotos" name="fotos[]" accept="image/jpeg,image/png,image/webp" multiple required>
            </div>
        </div>

        <button type="submit" name="botao" class="botao">Cadastrar Gato</button>
    </form>
</div>

</body>
</html>