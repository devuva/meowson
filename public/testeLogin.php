<?php
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

session_start();

$email = "maria@gmail.com";
$senha = "senha1234";

$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);
$usuario = $model->buscarPorEmail($email);

if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
    $_SESSION['usuario_id'] = $usuario['id'];
    echo "Usuário logado!<br>";
}

else {
    echo "E-mail ou senha incorretos!<br>";
}

var_dump($_SESSION['usuario_id']);
?>