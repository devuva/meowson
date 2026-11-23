<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

exigirPerfil(['adotante', 'protetor', 'ong', 'admin']);

$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);

if (($_SESSION['perfil'] ?? '') === 'admin' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
} else {
    $id = (int) ($_SESSION['usuario_id'] ?? 0);
}

$usuario = $model->buscarPorId($id);
if (!$usuario) {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<title>Editar usuário - Meowson</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="barra">
    <a href="index.html">Meowson</a>
    <nav aria-label="Principal">
        <a href="cadastro.html">Cadastro</a>
        <a href="login.html">Login</a>
    </nav>
</header>

<div class="container">
<form method="post" action="atualizar_usuario.php">

<div class="campo">
<input name="id" type="hidden" value="<?= htmlspecialchars($usuario['id']) ?>">
</div>

<div class="campo">
<label for="nome">Nome</label>
<input required name="nome" type="text" maxlength="150" id="nome" value="<?= htmlspecialchars($usuario['nome']) ?>">
</div>

<div class="campo">
<label for="cpf_cnpj">CPF / CNPJ</label>
<input required name="cpf_cnpj" type="text" maxlength="14" id="cpf_cnpj" value="<?= htmlspecialchars($usuario['cpf_cnpj']) ?>">
</div>

<div class="campo">
<label for="email">E-mail</label>
<input required name="email" type="email" maxlength="150" id="email" value="<?= htmlspecialchars($usuario['email']) ?>">
</div>

<div class="campo">
<label for="telefone">Telefone</label>
<input required name="telefone" type="text" maxlength="15" id="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>">
</div>

<div class="campo">
<label for="cep">CEP</label>
<input required name="cep" type="text" maxlength="8" id="cep" value="<?= htmlspecialchars($usuario['cep']) ?>">
</div>

<div class="campo">
<label for="logradouro">Logradouro</label>
<input required name="logradouro" type="text" maxlength="100" id="logradouro" value="<?= htmlspecialchars($usuario['logradouro']) ?>">
</div>

<div class="campo">
<label for="numero">Número</label>
<input required name="numero" type="text" maxlength="10" id="numero" value="<?= htmlspecialchars($usuario['numero']) ?>">
</div>

<div class="campo">
<label for="complemento">Complemento (opcional)</label>
<input name="complemento" type="text" maxlength="50" id="complemento" value="<?= htmlspecialchars($usuario['complemento'] ?? '') ?>">
</div>

<div class="campo">
<label for="bairro">Bairro</label>
<input required name="bairro" type="text" maxlength="50" id="bairro" value="<?= htmlspecialchars($usuario['bairro']) ?>">
</div>

<div class="campo">
<label for="cidade">Cidade</label>
<input required name="cidade" type="text" maxlength="50" id="cidade" value="<?= htmlspecialchars($usuario['cidade']) ?>">
</div>

<div class="campo">
<label for="estado">Estado (UF)</label>
<input required name="estado" type="text" maxlength="2" id="estado" value="<?= htmlspecialchars($usuario['estado']) ?>">
</div>

<div class="campo">
<label for="senha">Nova senha (deixe em branco para manter a atual)</label>
<input name="senha" type="password" id="senha" placeholder="••••••••">
</div>

<button class="botao" type="submit" name="botao">Enviar</button>
</form>
</div>
</body>
</html>