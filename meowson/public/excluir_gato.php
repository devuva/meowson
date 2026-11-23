<?php 
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit;
}

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/GatoModel.php";
exigirPerfil(['protetor', 'ong']);
$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);

$id_gato = (int) $_POST['id'];
$usuario_id = $_SESSION['usuario_id'];

$model->excluir($id_gato, $usuario_id);
header("Location: meus_anuncios.php");
exit;
?>

