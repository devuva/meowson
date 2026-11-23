<?php
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/GatoModel.php";

$pdo = (new Conexao())->conectar();
$sexo = $_GET['sexo'] ?? '';

if ($sexo === 'M' || $sexo === 'F') {
    $model = new GatoModel($pdo);
    $lista = $model->listarPorSexo($sexo);
    foreach ($lista as $gato) {
        echo $gato['nome'] . "<br>";
    }
}

else {
    echo "Escolha um sexo.";
}


?>