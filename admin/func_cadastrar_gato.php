<?php
    require_once '../lib/conn.php';
        require __DIR__ . "/../src/Uploader.php";
        require __DIR__ . "/../src/Image.php";

        $image = new CoffeeCode\Uploader\Image("img", false, false);
        $upload = $image->upload($_FILES['image'], $_POST['name']);

        if ($_FILES) {
            extract($_POST);
            try {
                $novo_nome = $_POST['name'];
                $sql = "INSERT INTO gato VALUES(0, :nome, :idade, :sexo, :descricao, 0, :img)";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':nome', $name);
                $stmt->bindValue(':idade', $idade);
                $stmt->bindValue(':sexo', $sexo);
                $stmt->bindValue(':descricao', $descricao);
                $stmt->bindValue(':img', $novo_nome);
                $stmt->execute();
                echo "Cadastrado com sucesso";

            } catch (Exception $e) {
                echo "<p>(!) {$e->getMessage()}</p>";
            }
        }


?>