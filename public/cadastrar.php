<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cadastro.html");
    exit;
}

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

$dados = $_POST;
$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);
$erros = [];

$dados['cpf_cnpj'] = preg_replace('/\D/', '', $dados['cpf_cnpj']);
$dados['cep'] = preg_replace('/\D/', '', $dados['cep']);
$dados['telefone'] = preg_replace('/\D/', '', $dados['telefone']);


if ($dados['senha'] !== $dados['confirmarSenha']) {
    $erros[] = "As senhas devem coincidir!";
}

else {
    if (strlen($dados['senha']) < 8) {
        $erros[] = "A senha deve ter 8 ou mais caracteres!";
    }
}

if (strlen($dados['cpf_cnpj']) !== 11 && strlen($dados['cpf_cnpj']) !== 14) {
    $erros[] = "O CPF/CNPJ deve ter 11 ou 14 digitos!";
}

if (strlen($dados['cep']) !== 8) {
    $erros[] = "O CEP deve ter 8 caracteres";
}

if (strlen($dados['telefone']) !== 10 && strlen($dados['telefone']) !== 11) {
    $erros[] = "O telefone deve ter 10 ou 11 digitos (com DDD)!";
}

if (!in_array($dados['perfil'], ['adotante', 'protetor', 'ong'])) {
    $erros[] = "Selecione um perfil entre Adotante, Protetor e ONG!";
}

if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
    $erros[] = "Insira um e-mail válido!";
}

if (!empty($erros)) {
    foreach ($erros as $erro) {
        echo $erro . "<br>";
    }
}

else {

try {
    $model->cadastrar($dados);
    echo "Salvo!";
    } catch (PDOEXCEPTION $e) {
    if ($e->errorInfo[1] === 1062) {
        echo "E-mail ou CPF/CNPJ já cadastrado!";
    }

    else {
        echo "Erro ao salvar. Tente novamente.";
    }
}
}
?>