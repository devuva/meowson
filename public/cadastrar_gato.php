<?php
session_start();
if (isset($_POST['botao'])) {
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/GatoModel.php";
require_once __DIR__ . "/../config/auth.php";
$dados = $_POST;
$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);
$erros = [];
exigirPerfil(['protetor', 'ong']);

$fotos = [];
$pasta = __DIR__ . "/uploads";

if (isset($_FILES['fotos'])) {

$leitor = finfo_open(FILEINFO_MIME_TYPE);

foreach($_FILES['fotos']['name'] as $i => $nomeOriginal) {

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
$extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
$nome_final = $hex . "." . $extensao;

if (move_uploaded_file($temp, $pasta . "/" . $nome_final)) {
    $fotos[] = $nome_final;
}
}
}

$model->cadastrar($dados, $fotos);
echo "Salvo!";
}
?>