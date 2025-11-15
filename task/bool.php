<?php 
    require '../config/config.php';
    require '../config/verlogar.php';

    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
    // O animal, esse codigo pega o id do usuario, para pegar o campo situaçao no seu banco de dados 
    $stmt = $pdo->prepare("SELECT situacao FROM tasks WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task) {
        $novoValor = $task['situacao'] ? 0 : 1;

        // Ja esse daqui, atualiza o campo situaçao para  'true'
        $stmt2 = $pdo->prepare("UPDATE tasks SET situacao = :s WHERE id = :id");
        $stmt2->bindValue(':s', $novoValor, PDO::PARAM_INT);
        $stmt2->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
    }
}

header("Location: dashboard.php");
exit;