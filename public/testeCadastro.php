<?php
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);

$dados = [
    "nome" => "seu madruga",
    "cpf_cnpj" => "86i237864",
    "numero" => 1234,
    "cep" => "90347984",
    "logradouro" => "dfngjdfgvbngv",
    "bairro" => "uidfbdvhiodf",
    "cidade" => "fnherhfbndfuivn",
    "estado" => "RR",
    "perfil" => 'protetor',
    "email" => "fjkdhvjkkfjdbvjkfbjvbvfjd",
    "senha_hash" => "291024",
    "telefone" => "9233428723498"
];
    echo "Cadastrado!";

?>