<?php
class UsuarioModel {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function cadastrar(array $dados) {
        if ($dados['perfil'] === 'ong') {
            $situacao = 'pendente';
        } else {
            $situacao = 'ativo';
        }

        $hash = password_hash($dados['senha'], PASSWORD_DEFAULT);

        $comando = $this->pdo->prepare(
            "INSERT INTO usuario (nome, cpf_cnpj, numero, cep, logradouro, bairro, cidade, estado, perfil, email, senha_hash, telefone, situacao_cadastro)
            values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $comando->execute([
            $dados['nome'], $dados['cpf_cnpj'], $dados['numero'], $dados['cep'],
            $dados['logradouro'], $dados['bairro'], $dados['cidade'], $dados['estado'],
            $dados['perfil'], $dados['email'], $hash, $dados['telefone'], $situacao
        ]);
    }

    public function buscarPorEmail(string $email) {
        $comando = $this->pdo->prepare("SELECT * FROM usuario WHERE email = ?");
        $comando->execute([$email]);
        return $comando->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id): array|false {
        $comando = $this->pdo->prepare("SELECT * FROM usuario WHERE id = ?");
        $comando->execute([$id]);
        return $comando->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodos(): array {
        $comando = $this->pdo->prepare("SELECT id, nome, email, cpf_cnpj, perfil, telefone, cidade, estado, situacao_cadastro FROM usuario ORDER BY id DESC");
        $comando->execute();
        return $comando->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizar(int $id, array $dados): bool {
        if (!empty($dados['senha'])) {
            $hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
            $comando = $this->pdo->prepare(
                "UPDATE usuario SET nome = ?, cpf_cnpj = ?, numero = ?, cep = ?, logradouro = ?, bairro = ?, cidade = ?, estado = ?, complemento = ?, email = ?, telefone = ?, senha_hash = ? WHERE id = ?"
            );
            return $comando->execute([
                $dados['nome'], $dados['cpf_cnpj'], $dados['numero'], $dados['cep'],
                $dados['logradouro'], $dados['bairro'], $dados['cidade'], $dados['estado'],
                $dados['complemento'] ?? null, $dados['email'], $dados['telefone'], $hash, $id
            ]);
        }

        $comando = $this->pdo->prepare(
            "UPDATE usuario SET nome = ?, cpf_cnpj = ?, numero = ?, cep = ?, logradouro = ?, bairro = ?, cidade = ?, estado = ?, complemento = ?, email = ?, telefone = ? WHERE id = ?"
        );
        return $comando->execute([
            $dados['nome'], $dados['cpf_cnpj'], $dados['numero'], $dados['cep'],
            $dados['logradouro'], $dados['bairro'], $dados['cidade'], $dados['estado'],
            $dados['complemento'] ?? null, $dados['email'], $dados['telefone'], $id
        ]);
    }

    public function atualizarStatusAdmin(int $id, string $perfil, string $situacao, ?string $motivoRecusa = null): bool {
        $comando = $this->pdo->prepare(
            "UPDATE usuario SET perfil = ?, situacao_cadastro = ?, motivo_recusa = ? WHERE id = ?"
        );
        return $comando->execute([$perfil, $situacao, $motivoRecusa, $id]);
    }

    public function excluir(int $id): bool {
        $comando = $this->pdo->prepare("DELETE FROM usuario WHERE id = ?");
        return $comando->execute([$id]);
    }

    public function listarOngsPendentes(): array {
        $comando = $this->pdo->prepare(
            "SELECT id, nome, cpf_cnpj, email, telefone, cidade, estado
             FROM usuario
             WHERE perfil = 'ong' AND situacao_cadastro = 'pendente'
             ORDER BY id ASC"
        );
        $comando->execute();
        return $comando->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>