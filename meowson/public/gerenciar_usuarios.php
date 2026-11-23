<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

exigirPerfil(['admin']);

$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);
$usuarios = $model->listarTodos();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Gerenciar Usuários - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="barra">
    <a href="index.html">Meowson</a>
    <nav aria-label="Principal">
        <a href="gerenciar_usuarios.php">Usuários</a>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">
    <h1>Usuários Cadastrados</h1>

    <?php if (empty($usuarios)): ?>
        <p>Nenhum usuário encontrado.</p>
    <?php else: ?>
        <?php foreach ($usuarios as $u): ?>
            <div class="campo item-usuario">
                <strong><?= htmlspecialchars($u['nome']) ?></strong> (<?= htmlspecialchars($u['perfil']) ?>)
                <p>E-mail: <?= htmlspecialchars($u['email']) ?> | CPF/CNPJ: <?= htmlspecialchars($u['cpf_cnpj']) ?></p>
                <p>Cidade: <?= htmlspecialchars($u['cidade']) ?> - <?= htmlspecialchars($u['estado']) ?> | Situação: <strong><?= htmlspecialchars($u['situacao_cadastro']) ?></strong></p>

                <div class="acoes">
                    <form method="get" action="editar_usuario.php">
                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                        <button class="botao" type="submit">Editar</button>
                    </form>

                    <?php if ((int) $u['id'] !== (int) $_SESSION['usuario_id']): ?>
                        <form method="get" action="excluir_usuario.php" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                            <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                            <button class="botao" type="submit">Excluir</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>