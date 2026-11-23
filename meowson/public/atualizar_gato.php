<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/GatoModel.php";

exigirPerfil(['protetor', 'ong']);

$dados = $_POST;
$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);

$gato_id = (int) $_POST['id'];
$id = $_SESSION['usuario_id'];

$model->atualizar($gato_id, $id, $dados);

header('Location: meus_anuncios.php');
exit;
?>