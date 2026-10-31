<?php
class UsuarioModel {

private PDO $pdo;

public function __construct(PDO $pdo) {
    $this->pdo = $pdo;
}

public function cadastrar(array $dados) {

$hash = password_hash($dados['senha'], PASSWORD_DEFAULT);

$comando = $this->pdo->prepare(
    "INSERT INTO usuario (nome, cpf_cnpj, numero, cep, logradouro, bairro, cidade, estado, perfil, email, senha_hash, telefone)
    values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
    $comando->execute([$dados['nome'], $dados['cpf_cnpj'], $dados['numero'], $dados['cep'], $dados['logradouro'], $dados['bairro'],
    $dados['cidade'], $dados['estado'], $dados['perfil'], $dados['email'], $hash, $dados['telefone']]);
}
}
?>