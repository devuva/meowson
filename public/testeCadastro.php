<?php
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);

$dados = [
    "nome" => "maria",
    "cpf_cnpj" => "23423",
    "numero" => 33,
    "cep" => "54445",
    "logradouro" => "Rua Miami",
    "bairro" => "Florida",
    "cidade" => "Vice City",
    "estado" => "CA",
    "perfil" => 'ong',
    "email" => "maria@gmail.com",
    "senha" => "senha1234",
    "telefone" => "2125550182"
];
    $model->cadastrar($dados);
    echo "Cadastrado!";
?>