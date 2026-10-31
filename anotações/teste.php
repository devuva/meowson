<?php
/*class Gato {
    private string $nome;
    private int $idade;

    public function __construct(string $nome, int $idade) {
        $this->nome = $nome;
        $this->idade = $idade;
    }

    public function classificarIdade() {
        if ($this->idade < 12) {
            return "Filhote";
        }

        else {
            return "Adulto";
        }
    }

    public function getNome() : string {
        return $this->nome;
    }

    public function getIdade() : int {
        return $this->idade;
    }
}

$gato1 = new Gato("Lola", 11);
$gato2 = new Gato("Sheldon", 44);

$gatos = [$gato1, $gato2];

foreach ($gatos as $gato) {
    echo "Nome: " . $gato->getNome() . " - Idade: " . $gato->getIdade() . " meses - " . $gato->classificarIdade() . "<br>";
}*/

$hash = password_hash("password", PASSWORD_DEFAULT);
echo $hash;

?>