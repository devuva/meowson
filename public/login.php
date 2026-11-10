<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit;
}

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);


$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';
$usuario = $model->buscarPorEmail($email);

if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
    if ($usuario['situacao_cadastro'] === 'ativo') {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['perfil'] = $usuario['perfil'];
        header("Location: cadastro.html");
        exit;
    }

    else {
        echo "A conta não pode ser usada!";
    }
}

else {
    echo "E-mail ou senha incorretos!";
}

?>