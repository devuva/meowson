<?php
class GatoModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function listarPorSexo(string $sexo): array {
        $comando = $this->pdo->prepare('select * from gatos where sexo = ?');
        $comando->execute([$sexo]);
        return $comando->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>