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

    public function cadastrar(array $dados, array $fotos = []) {

    $comando = $this->pdo->prepare(
        "INSERT INTO gatos (nome, raca, idade, sexo, descricao, observacoes, vacinas, doencas, usuario_id)
        values (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $ok = $comando->execute([$dados['nome'], $dados['raca'], $dados['idade'], $dados['sexo'],
    $dados['descricao'], $dados['observacoes'], $dados['vacinas'], $dados['doencas'], $_SESSION['usuario_id']]);

    if (!$ok) {
        return false;
    }

    $gatoId = (int) $this->pdo->lastInsertId();

    if ($gatoId === 0) {
        return false;
    }

    if (!empty($fotos)) {
    $comandoFoto = $this->pdo->prepare(
        "INSERT INTO fotos_gato (gato_id, arquivo)
        values (?, ?)"
        );

    foreach ($fotos as $arquivo) {
        $comandoFoto->execute([$gatoId, $arquivo]);
    }
}

    return $gatoId;
}

public function listarPorUsuario(int $usuario_id) : array {
    $comando = $this->pdo->prepare('SELECT * FROM gatos where usuario_id = ?');
    $comando->execute([$usuario_id]);
    return $comando->fetchAll(PDO::FETCH_ASSOC);
}

public function excluir(int $id, int $usuario_id) {
    $comando = $this->pdo->prepare('DELETE FROM gatos where usuario_id = ? AND id = ?');
    return $comando->execute([$usuario_id, $id]);
}

public function buscarPorId(int $id, int $usuario_id) : array|false {
    $comando = $this->pdo->prepare('SELECT * FROM gatos where usuario_id = ? AND id = ?');
    $comando->execute([$usuario_id, $id]);
    return $comando->fetch(PDO::FETCH_ASSOC);
}

public function atualizar(int $id, int $usuario_id, array $dados) {
    $comando = $this->pdo->prepare('UPDATE gatos SET nome = ?, raca = ?,
    idade = ?, sexo = ?, descricao = ?, observacoes = ?, vacinas = ?,
    doencas = ? where usuario_id = ? AND id = ?');
    return $comando->execute([$dados['nome'], $dados['raca'], $dados['idade'], $dados['sexo'], $dados['descricao'],
    $dados['observacoes'], $dados['vacinas'], $dados['doencas'], $usuario_id, $id]);
}

    public function listarPublico(array $filtros = []): array {
        $sql = "SELECT g.*, u.cidade, u.estado,
                (SELECT fg.arquivo FROM fotos_gato fg WHERE fg.gato_id = g.id LIMIT 1) AS foto
                FROM gatos g
                INNER JOIN usuario u ON g.usuario_id = u.id
                WHERE g.status = 'disponivel'";
        $params = [];

        if (!empty($filtros['sexo'])) {
            $sql .= " AND g.sexo = ?";
            $params[] = $filtros['sexo'];
        }

        if (!empty($filtros['idade_max'])) {
            $sql .= " AND g.idade <= ?";
            $params[] = (int) $filtros['idade_max'];
        }

        if (!empty($filtros['cidade'])) {
            $sql .= " AND u.cidade LIKE ?";
            $params[] = '%' . $filtros['cidade'] . '%';
        }

        $sql .= " ORDER BY g.id DESC";

        $comando = $this->pdo->prepare($sql);
        $comando->execute($params);
        return $comando->fetchAll(PDO::FETCH_ASSOC);
    }
    public function buscarDetalhesPublico(int $id): array|false {
        $comando = $this->pdo->prepare(
            "SELECT g.*, u.nome AS nome_doador, u.perfil AS perfil_doador, u.cidade, u.estado
             FROM gatos g
             INNER JOIN usuario u ON g.usuario_id = u.id
             WHERE g.id = ? AND g.status = 'disponivel'"
        );
        $comando->execute([$id]);
        $gato = $comando->fetch(PDO::FETCH_ASSOC);

        if (!$gato) {
            return false;
        }

        $comandoFotos = $this->pdo->prepare("SELECT arquivo FROM fotos_gato WHERE gato_id = ?");
        $comandoFotos->execute([$id]);
        $gato['fotos'] = $comandoFotos->fetchAll(PDO::FETCH_COLUMN);

        return $gato;
    }
}

?>