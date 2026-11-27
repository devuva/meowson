<?php
session_start();
require_once __DIR__ . "/../config/Conexao.php";
require_once __DIR__ . "/../models/GatoModel.php";

$pdo = (new Conexao())->conectar();
$model = new GatoModel($pdo);

$id = (int) ($_GET['id'] ?? 0);
$gato = $model->buscarDetalhesPublico($id);

if (!$gato) {
    header("Location: gatos.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title><?= htmlspecialchars($gato['nome']) ?> - Meowson</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/svg+xml" href="img/favicon.svg">
</head>
<body>

<?php require_once __DIR__ . "/../includes/header.php"; ?>

<div class="container">
    <a href="gatos.php" class="link-voltar">&larr; Voltar para os gatinhos</a>

    <h1><?= htmlspecialchars($gato['nome']) ?></h1>

    <?php if (!empty($gato['fotos'])): ?>
        <div class="galeria-gato">
            <?php foreach ($gato['fotos'] as $foto): ?>
                <img src="uploads/<?= htmlspecialchars($foto) ?>" alt="Foto de <?= htmlspecialchars($gato['nome']) ?>">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p><strong>Raça:</strong> <?= htmlspecialchars($gato['raca'] ?? 'SRD') ?></p>
    <p><strong>Idade:</strong> <?= (int) $gato['idade'] ?> meses</p>
    <p><strong>Sexo:</strong> <?= $gato['sexo'] === 'F' ? 'Fêmea' : 'Macho' ?></p>
    <p><strong>Vacinas:</strong> <?= htmlspecialchars($gato['vacinas'] ?? 'Nenhuma') ?></p>
    <p><strong>Doenças:</strong> <?= htmlspecialchars($gato['doencas'] ?? 'Nenhuma') ?></p>
    <p><strong>Descrição:</strong> <?= htmlspecialchars($gato['descricao'] ?? 'Sem descrição.') ?></p>
    <p><strong>Observações:</strong> <?= htmlspecialchars($gato['observacoes'] ?? 'Nenhuma.') ?></p>
    <p><strong>Responsável:</strong> <?= htmlspecialchars($gato['nome_doador']) ?> (<?= htmlspecialchars($gato['cidade']) ?> - <?= htmlspecialchars($gato['estado']) ?>)</p>

    <section class="campo" aria-label="Localização aproximada do responsável" style="margin: 1.5rem 0;">
        <h2>Região do Protetor / ONG</h2>
        <p class="subtitulo-form">
            Localização aproximada em <strong><?= htmlspecialchars($gato['cidade']) ?> - <?= htmlspecialchars($gato['estado']) ?></strong> para facilitar o planejamento da adoção.
        </p>
        <div id="mapa-regiao" data-cidade="<?= htmlspecialchars($gato['cidade']) ?>" data-estado="<?= htmlspecialchars($gato['estado']) ?>">
            <p id="status-mapa" class="subtitulo-form">Carregando mapa da região...</p>
        </div>
    </section>
    
    <?php if (isset($_SESSION['usuario_id']) && ($_SESSION['perfil'] ?? '') === 'adotante'): ?>
        <form method="post" action="solicitar_adocao.php">
            <input type="hidden" name="gato_id" value="<?= (int) $gato['id'] ?>">
            <div class="campo">
                <label for="mensagem_adotante">Por que você quer adotar <?= htmlspecialchars($gato['nome']) ?>?</label>
                <textarea name="mensagem_adotante" id="mensagem_adotante" required></textarea>
            </div>
            <button class="botao" type="submit">Solicitar adoção</button>
        </form>
    <?php elseif (!isset($_SESSION['usuario_id'])): ?>
        <p><a href="login.html">Faça login como adotante</a> para solicitar a adoção.</p>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . "/../includes/footer.php"; ?>

<script>
document.addEventListener("DOMContentLoaded", async () => {
    const mapaEl = document.getElementById("mapa-regiao");
    if (!mapaEl) return;

    const cidade = mapaEl.dataset.cidade || "";
    const estado = mapaEl.dataset.estado || "";
    const query = encodeURIComponent(`${cidade}, ${estado}, Brasil`);

    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${query}`);
        const resultados = await res.json();

        if (resultados.length > 0) {
            const { lat, lon } = resultados[0];
            const delta = 0.05;
            const bbox = `${Number(lon) - delta},${Number(lat) - delta},${Number(lon) + delta},${Number(lat) + delta}`;

            mapaEl.innerHTML = `
                <iframe
                    title="Mapa aproximado de ${cidade} - ${estado}"
                    width="100%"
                    height="260"
                    style="border: 1px solid var(--cor-borda); border-radius: 0.5rem;"
                    loading="lazy"
                    src="https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${lat},${lon}">
                </iframe>
                <p style="margin-top: 0.5rem;">
                    <a href="https://www.openstreetmap.org/?mlat=${lat}&mlon=${lon}#map=13/${lat}/${lon}" target="_blank" rel="noopener noreferrer">
                        Abrir região no OpenStreetMap &nearr;
                    </a>
                </p>
            `;
        } else {
            mapaEl.innerHTML = `<p class="subtitulo-form">Não foi possível carregar o mapa para ${cidade} - ${estado}.</p>`;
        }
    } catch (erro) {
        mapaEl.innerHTML = `<p class="subtitulo-form">Mapa indisponível no momento.</p>`;
    }
});
</script>
</body>
</html>