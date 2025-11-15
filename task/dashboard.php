<?php
require '../config/config.php';
require '../config/verlogar.php';
require 'functions.php'; // adiciona as funções reutilizáveis
$id_usuario = $_SESSION['id'] ?? 0;
$is_admin = ($id_usuario === 1);

$filtro = $_POST['filtro'] ?? 'todas';

$noticias = get_tasks_filtered($pdo, $id_usuario, $is_admin, $filtro);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id'];
    $is_admin = ($id_usuario === 1);
   



    $message = '';
    // tratar pedido de exclusão
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task'])) {
        $delete_id = intval($_POST['delete_task']);
        if ($delete_id > 0) {
            if (delete_task($pdo, $delete_id, $id_usuario, $is_admin)) {
                $message = "<div style='color:#558B6E;'>Task excluída com sucesso!</div>";
            } else {
                $message = "<div style='color:#776472;'>Erro ao excluir task.</div>";
            }
        } else {
            $message = "<div style='color:#776472;'>ID inválido.</div>";
        }
    }

    // busca tasks usando a função
    $filtro = $_POST['filtro'] ?? 'todas';

$noticias = get_tasks_filtered($pdo, $id_usuario, $is_admin, $filtro);

}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
</head>

<body>
    <a href="add_task.php">Adicionar task</a>
    <a href="../config/sair.php">Sair</a>
    <h1>Tasks</h1>
<form action="" method="post">
    <select name="filtro" id="filtro">
        <option value="todas">Todas</option>
        <option value="concluida">Concluídas</option>
        <option value="nao_concluida">Não concluídas</option>
    </select>
    <button type="submit">Pesquisar</button>
</form>

<form action="" method="post">
    <input type="hidden" name="filtro" value="todas">
    <button type="submit">Limpar filtro</button>
</form>



    <?= $message ?? '' ?>

    <ul>
        <?php foreach ($noticias as $not): ?>
            <li>
                <h2><?= htmlspecialchars($not['title']) ?></h2>
                <p><strong>Autor (user_id):</strong> <?= htmlspecialchars($not['user_id']) ?></p>
                <textarea readonly rows="6" cols="60"><?= htmlspecialchars($not['texto']) ?></textarea>
                <br>
                <small><strong>Publicado em:</strong> <?= htmlspecialchars($not['created_at']) ?></small><br>

                <form action="bool.php" method="post">
                    <input type="hidden" name="id" value="<?= intval($not['id']) ?>">
                    <button type="submit">
                        <?= $not['situacao'] ? 'Marcar como nao concluido' : 'Marcar como concluido' ?>
                    </button>
                </form>


                <form action="" method="post" style="display:inline">
                    <input type="hidden" name="delete_task" value="<?= intval($not['id']) ?>">
                    <button type="submit" onclick="return confirm('Confirmar exclusão?')">Excluir</button>
                </form>

                <hr>
            </li>
        <?php endforeach; ?>
    </ul>


    </script>
</body>

</html>