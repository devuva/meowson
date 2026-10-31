<?php
class UsuarioModel {

private PDO $pdo;

public function __construct(PDO $pdo) {
    $this->pdo = $pdo;
}

public function cadastrar(array $dados) {

if ($dados['perfil'] === 'ong') {
    $situacao = 'pendente';
}

else {
    $situacao = 'ativo';
}

$hash = password_hash($dados['senha'], PASSWORD_DEFAULT);

$comando = $this->pdo->prepare(
    "INSERT INTO usuario (nome, cpf_cnpj, numero, cep, logradouro, bairro, cidade, estado, perfil, email, senha_hash, telefone, situacao_cadastro)
    values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);
    return $comando->execute([$dados['nome'], $dados['cpf_cnpj'], $dados['numero'], $dados['cep'], $dados['logradouro'], $dados['bairro'],
    $dados['cidade'], $dados['estado'], $dados['perfil'], $dados['email'], $hash, $dados['telefone'], $situacao]);
}

public function buscarPorEmail(string $email) {
    $comando = $this->pdo->prepare("SELECT * FROM usuario WHERE email = ?");
    $comando->execute([$email]);
    return $comando->fetch(PDO::FETCH_ASSOC);
}
}
?>