<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (isset($_POST['botao']))
{
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/GatoModel.php";
require_once __DIR__ . "/../config/auth.php";
$dados = $_POST;
$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);
$erros = [];
exigirPerfil(['protetor', 'ong']);

$model->cadastrar($dados);
echo "Salvo!";
}
?>