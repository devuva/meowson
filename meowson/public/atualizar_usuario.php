<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

exigirPerfil(['adotante', 'protetor', 'ong', 'admin']);

$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);

$idPost = (int) ($_POST['id'] ?? 0);
$idLogado = (int) ($_SESSION['usuario_id'] ?? 0);
$isAdmin = (($_SESSION['perfil'] ?? '') === 'admin');

$idAlvo = $isAdmin ? $idPost : $idLogado;

if ($idAlvo > 0) {
    $model->atualizar($idAlvo, $_POST);
}

if ($isAdmin && $idAlvo !== $idLogado) {
    header("Location: gerenciar_usuarios.php");
} else {
    header("Location: editar_usuario.php");
}
exit;