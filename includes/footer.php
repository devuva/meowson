<?php
$curiosidades = [
    "Você sabia? Conviver com gatos ajuda a reduzir o estresse e a ansiedade no dia a dia.",
    "Você sabia? O ronronar dos gatos vibra em frequências que promovem sensação de calma e bem-estar.",
    "Você sabia? Adotar um gato adulto permite já conhecer sua personalidade e nível de energia.",
    "Você sabia? Gatos castrados vivem mais anos e têm menor risco de doenças graves.",
    "Você sabia? Ao adotar um gato resgatado, você abre vaga para que uma ONG salve outro animal."
];
$curiosidadeDoDia = $curiosidades[array_rand($curiosidades)];
?>
<footer class="rodape-curiosidade">
    <p><?= htmlspecialchars($curiosidadeDoDia) ?></p>
</footer>