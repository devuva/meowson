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

    public function cadastrar(array $dados) {

    $comando = $this->pdo->prepare(
        "INSERT INTO gatos (nome, raca, idade, sexo, descricao, observacoes, vacinas, doencas, usuario_id)
        values (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    return $comando->execute([$dados['nome'], $dados['raca'], $dados['idade'], $dados['sexo'],
    $dados['descricao'], $dados['observacoes'], $dados['vacinas'], $dados['doencas'], $_SESSION['usuario_id']]);
}
}

?>