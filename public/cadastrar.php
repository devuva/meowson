<?php
if ($_SERVER['REQUEST_METHOD'] !== '$_POST') {
    header("Location: cadastro.html");
    exit;
}

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

$dados = $_POST;
$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);
$erros = [];

if ($dados['senha'] !== $dados['confirmarSenha']) {
    $erros[] = "As senhas devem coincidir!";
}

else {
    if (strlen($dados['senha']) < 8) {
        $erros[] = "A senha deve ter 8 ou mais caracteres!";
    }
}


if (!empty($erros)) {
    foreach ($erros as $erro) {
        echo $erro . "<br>";
    }
}

else {
    $model->cadastrar($dados);
    echo "Salvo!";
}
?>