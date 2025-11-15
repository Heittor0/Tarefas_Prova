<?php 
require '../config/config.php';
require '../config/verlogar.php';

/**
 * Cria uma task no banco.
 *
 * @param PDO $pdo
 * @param int $user_id
 * @param string $title
 * @param string $texto
 * @param bool 
 * @return bool true se inseriu, false caso contrário
 */
function create_task(PDO $pdo, int $user_id, string $title, string $texto, bool $situacao): bool {
    $sql = "INSERT INTO tasks (user_id, title, texto, created_at, situacao) 
            VALUES (:user_id, :title, :texto, NOW(), :situacao)";
    
    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':texto', $texto, PDO::PARAM_STR);
    $stmt->bindValue(':situacao', $situacao ? 1 : 0, PDO::PARAM_INT);

    return $stmt->execute();
}


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = $_POST['nome'] ?? '';
    $texto = $_POST['texto'] ?? '';
    $user_id = $_SESSION['id'];
    $situacao = false;

    if (create_task($pdo, $user_id, $nome, $texto,$situacao)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $erro = "Erro ao adicionar a tarefa.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form action="" method="post">

        <label for="nome">Nome da tarefa</label>
        <input type="text" name="nome" id="nome" required value="<?= htmlspecialchars($nome ?? '') ?>">

         <label for="texto">Texto da tarefa</label>
        <textarea name="texto" id="texto" required><?= htmlspecialchars($texto ?? '') ?></textarea>

        <button type="submit">Enviar</button>
    </form>

</body>
</html>