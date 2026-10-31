<?php
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);

$dados = [
    "nome" => "flavio",
    "cpf_cnpj" => "520.754.810-75",
    "numero" => 3333,
    "cep" => "6873435",
    "logradouro" => "Rua Miami",
    "bairro" => "Florida",
    "cidade" => "Vice City",
    "estado" => "CA",
    "perfil" => 'adotante',
    "email" => "flaviofujao@gmail.com",
    "senha" => "trump",
    "telefone" => "2125550182"
];
    $model->cadastrar($dados);
    echo "Cadastrado!";
?>