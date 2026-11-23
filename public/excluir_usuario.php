<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

exigirPerfil(['adotante', 'protetor', 'ong', 'admin']);

$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);

$idGet = (int) ($_GET['id'] ?? 0);
$idLogado = (int) ($_SESSION['usuario_id'] ?? 0);
$isAdmin = (($_SESSION['perfil'] ?? '') === 'admin');

$idAlvo = ($isAdmin && $idGet > 0) ? $idGet : $idLogado;

if ($idAlvo > 0) {
    $model->excluir($idAlvo);
}

if ($idAlvo === $idLogado) {
    session_unset();
    session_destroy();
    header("Location: login.html");
    exit;
}

header("Location: gerenciar_usuarios.php");
exit;