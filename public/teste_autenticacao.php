<?php
session_start();

require_once __DIR__ . "/../config/auth.php";

exigirPerfil(['ong', 'protetor']);

echo "Acesso liberado";
?>