<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/SolicitacaoModel.php";

exigirPerfil(['adotante']);

$pdo = (new Conexao())->conectar();
$model = new SolicitacaoModel($pdo);

$gatoId = (int) ($_POST['gato_id'] ?? 0);
$mensagem = trim($_POST['mensagem_adotante'] ?? '');
$adotanteId = (int) $_SESSION['usuario_id'];

if ($gatoId > 0 && $mensagem !== '') {
    $enviada = $model->solicitar($gatoId, $adotanteId, $mensagem);
    $parametro = $enviada ? 'sucesso=1' : 'duplicada=1';
    header("Location: minhas_solicitacoes.php?{$parametro}");
    exit;
}

header("Location: minhas_solicitacoes.php");
exit;