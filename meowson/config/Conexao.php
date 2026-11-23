<?php 

class Conexao {
    public function conectar(): PDO {
        $user = "root";
        $senha = "";
        $pdo = new PDO('mysql:host=localhost;dbname=meowson;charset=utf8mb4', $user, $senha);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
?>