<header class="barra">
    <a href="gatos.php" class="marca">
        <img src="img/logo.svg" alt="Logo Meowson">
        <span>Meowson</span>
    </a>
    <nav aria-label="Principal">
        <a href="gatos.php">Adotar</a>
        <?php if (!isset($_SESSION['usuario_id'])): ?>
            <a href="cadastro.html">Cadastro</a>
            <a href="login.html">Login</a>
        <?php else: ?>
            <?php $perfil = $_SESSION['perfil'] ?? ''; ?>
            <?php if ($perfil === 'adotante'): ?>
                <a href="minhas_solicitacoes.php">Minhas Solicitações</a>
            <?php elseif ($perfil === 'ong' || $perfil === 'protetor'): ?>
                <a href="meus_anuncios.php">Meus Anúncios</a>
                <a href="gerenciar_solicitacoes.php">Solicitações</a>
            <?php elseif ($perfil === 'admin'): ?>
                <a href="moderacao_admin.php">Moderação</a>
                <a href="gerenciar_usuarios.php">Usuários</a>
            <?php endif; ?>
            <a href="logout.php">Sair</a>
        <?php endif; ?>
    </nav>
</header>