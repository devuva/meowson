<?php 
function exigirLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.html");
        exit;
    }
}

function exigirPerfil(array $perfis) {
    exigirLogin();

    if (!in_array($_SESSION['perfil'], $perfis)) {
        header("Location: negado.html");
        exit;
    }
}

?>