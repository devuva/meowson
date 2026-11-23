<?php
class SolicitacaoModel {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function solicitar(int $gatoId, int $adotanteId, string $mensagem): bool {
        $verifica = $this->pdo->prepare("SELECT id FROM solicitacoes_adocao WHERE gato_id = ? AND adotante_id = ?");
        $verifica->execute([$gatoId, $adotanteId]);
        if ($verifica->fetch()) {
            return false;
        }

        $comando = $this->pdo->prepare(
            "INSERT INTO solicitacoes_adocao (gato_id, adotante_id, status, mensagem_adotante)
             VALUES (?, ?, 'pendente', ?)"
        );
        return $comando->execute([$gatoId, $adotanteId, $mensagem]);
    }

    public function listarRecebidas(int $doadorId): array {
        $comando = $this->pdo->prepare(
            "SELECT s.*, g.nome AS nome_gato, u.nome AS nome_adotante, u.email AS email_adotante, u.telefone AS telefone_adotante, u.cidade, u.estado
             FROM solicitacoes_adocao s
             INNER JOIN gatos g ON s.gato_id = g.id
             INNER JOIN usuario u ON s.adotante_id = u.id
             WHERE g.usuario_id = ?
             ORDER BY s.id DESC"
        );
        $comando->execute([$doadorId]);
        return $comando->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorAdotante(int $adotanteId): array {
        $comando = $this->pdo->prepare(
            "SELECT s.*, g.nome AS nome_gato, u.nome AS nome_doador
             FROM solicitacoes_adocao s
             INNER JOIN gatos g ON s.gato_id = g.id
             INNER JOIN usuario u ON g.usuario_id = u.id
             WHERE s.adotante_id = ?
             ORDER BY s.id DESC"
        );
        $comando->execute([$adotanteId]);
        return $comando->fetchAll(PDO::FETCH_ASSOC);
    }

    public function responder(int $solicitacaoId, int $doadorId, string $decisao, ?string $motivoRecusa = null): bool {
        $busca = $this->pdo->prepare(
            "SELECT s.id, s.gato_id
             FROM solicitacoes_adocao s
             INNER JOIN gatos g ON s.gato_id = g.id
             WHERE s.id = ? AND g.usuario_id = ?"
        );
        $busca->execute([$solicitacaoId, $doadorId]);
        $sol = $busca->fetch(PDO::FETCH_ASSOC);

        if (!$sol) {
            return false;
        }

        $comando = $this->pdo->prepare(
            "UPDATE solicitacoes_adocao SET status = ?, motivo_recusa = ? WHERE id = ?"
        );
        $ok = $comando->execute([$decisao, $motivoRecusa, $solicitacaoId]);

        if ($ok && $decisao === 'aprovada') {
            $updGato = $this->pdo->prepare("UPDATE gatos SET status = 'adotado' WHERE id = ?");
            $updGato->execute([$sol['gato_id']]);

            $encerrarOutras = $this->pdo->prepare(
                "UPDATE solicitacoes_adocao SET status = 'encerrada' WHERE gato_id = ? AND id != ? AND status = 'pendente'"
            );
            $encerrarOutras->execute([$sol['gato_id'], $solicitacaoId]);
        }

        return $ok;
    }

    public function cancelar(int $solicitacaoId, int $adotanteId): bool {
        $comando = $this->pdo->prepare(
            "DELETE FROM solicitacoes_adocao WHERE id = ? AND adotante_id = ? AND status = 'pendente'"
        );
        return $comando->execute([$solicitacaoId, $adotanteId]);
    }
}
?>