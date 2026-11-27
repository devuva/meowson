<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cadastro.html");
    exit;
}

require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/UsuarioModel.php";

$dados = $_POST;
$pdo = (new Conexao())->conectar();
$model = new UsuarioModel($pdo);
$erros = [];

$dados['cpf_cnpj'] = preg_replace('/\D/', '', $dados['cpf_cnpj'] ?? '');
$dados['cep'] = preg_replace('/\D/', '', $dados['cep'] ?? '');
$dados['telefone'] = preg_replace('/\D/', '', $dados['telefone'] ?? '');

if ($dados['senha'] !== $dados['confirmarSenha']) {
    $erros[] = "As senhas devem coincidir!";
} else {
    if (strlen($dados['senha']) < 8) {
        $erros[] = "A senha deve ter 8 ou mais caracteres!";
    }
}

if (!in_array($dados['perfil'], ['adotante', 'protetor', 'ong'])) {
    $erros[] = "Selecione um perfil entre Adotante, Protetor e ONG!";
}

if ($dados['perfil'] === 'ong') {
    if (strlen($dados['cpf_cnpj']) !== 14) {
        $erros[] = "Para ONG, o CNPJ deve ter 14 dígitos!";
    }
} else {
    if (strlen($dados['cpf_cnpj']) !== 11) {
        $erros[] = "O CPF deve ter 11 dígitos!";
    }
}

if (strlen($dados['cep']) !== 8) {
    $erros[] = "O CEP deve ter 8 caracteres!";
}

if (strlen($dados['telefone']) !== 10 && strlen($dados['telefone']) !== 11) {
    $erros[] = "O telefone deve ter 10 ou 11 dígitos (com DDD)!";
}

if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
    $erros[] = "Insira um e-mail válido!";
}

if (strlen($dados['estado']) != 2) {
    $erros[] = "O estado é formado por uma sigla de 2 letras!";
}

$endereco = ['nome', 'numero', 'logradouro', 'bairro', 'cidade'];

foreach ($endereco as $campo) {
    if (trim($dados[$campo] ?? '') === '') {
        $erros[] = "Preencha o campo $campo!";
    }
}

$nomeArquivoDoc = null;
$pastaDocs = __DIR__ . "/uploads";

if ($dados['perfil'] === 'ong') {
    if (!isset($_FILES['documento']) || $_FILES['documento']['error'] === UPLOAD_ERR_NO_FILE) {
        $erros[] = "Envie a documentação da ONG!";
    } elseif ($_FILES['documento']['error'] !== UPLOAD_ERR_OK) {
        $erros[] = "Erro no upload do arquivo (código " . $_FILES['documento']['error'] . "). Tente um arquivo de até 2 MB.";
    } elseif ($_FILES['documento']['size'] > (2 * 1024 * 1024)) {
        $erros[] = "O documento deve ter no máximo 2 MB.";
    } else {
        $leitor = finfo_open(FILEINFO_MIME_TYPE);
        $tipo = finfo_file($leitor, $_FILES['documento']['tmp_name']);
        $tiposPermitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($tipo, $tiposPermitidos)) {
            $erros[] = "Formato inválido! Envie PDF, JPG, PNG ou WEBP.";
        } else {
            $extensao = strtolower(pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION));
            $nomeArquivoDoc = "doc_" . bin2hex(random_bytes(8)) . "." . $extensao;

           if (!move_uploaded_file($_FILES['documento']['tmp_name'], $pastaDocs . "/" . $nomeArquivoDoc)) {
                $erros[] = "Falha ao salvar em: " . $pastaDocs . " | Existe: " . (is_dir($pastaDocs) ? 'sim' : 'não') . " | Gravável: " . (is_writable($pastaDocs) ? 'sim' : 'não');
                $nomeArquivoDoc = null;
            }
        }
    }
}

if (!empty($erros)) {
    if ($nomeArquivoDoc && file_exists($pastaDocs . "/" . $nomeArquivoDoc)) {
        unlink($pastaDocs . "/" . $nomeArquivoDoc);
    }
    foreach ($erros as $erro) {
        echo $erro . "<br>";
    }
} else {
    $dados['documento_ong'] = $nomeArquivoDoc;
    try {
        $model->cadastrar($dados);
        header("Location: login.html");
        exit;
    } catch (PDOException $e) {
        if ($nomeArquivoDoc && file_exists($pastaDocs . "/" . $nomeArquivoDoc)) {
            unlink($pastaDocs . "/" . $nomeArquivoDoc);
        }
        if ($e->errorInfo[1] === 1062) {
            echo "E-mail ou CPF/CNPJ já cadastrado!";
        } else {
            echo "Erro ao salvar. Tente novamente.";
        }
    }
}
?>