<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/GatoModel.php";
exigirPerfil(['protetor', 'ong']);
$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);

$gatos = $model->listarPorUsuario($_SESSION['usuario_id']);
foreach ($gatos as $gato) {
    echo "Nome: " . htmlspecialchars($gato['nome']) . "<br>" . "Idade (em meses): " . htmlspecialchars($gato['idade']) . "<br>" .
    "Sexo: " .  htmlspecialchars($gato['sexo']) . "<br>" . "Status: " .  htmlspecialchars($gato['status']) .
    "<br>";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<title>Login - Meowson</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="barra">
    <a href="index.html">Meowson</a>
    <nav aria-label="Principal">
        <a href="cadastro.html">Cadastro</a>
        <a href="login.html">Login</a>
    </nav>
</header>
    
    <form method="get" action="editar_gato.php" style="display:inline;">
    <input type="hidden" name="id" value="<?= (int) $gato['id'] ?>">
    <button type="submit" class="botao">Editar</button>
    </form>

    <form method="post" action="excluir_gato.php">
    <input type="hidden" name="id" value="<?= (int) $gato['id'] ?>">
    <button type="submit" class="botao" onclick="return confirm('Remover este anúncio?')">Remover</button>
    </form>
    <?php
}
?>