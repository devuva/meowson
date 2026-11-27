<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/GatoModel.php";

$db = (new Conexao())->conectar();
$gatoModel = new GatoModel($db);

echo json_encode($gatoModel->listarPublico());
?>