<?php
require '../config/config.php';
require '../config/verlogar.php';

/**
 * Retorna lista de tasks visíveis pelo usuário.
 * Admin (id == 1) vê todas.
 *
 * @param PDO $pdo
 * @param int $user_id
 * @param bool $is_admin
 * @return array
 */
function get_tasks_for_user(PDO $pdo, int $user_id, bool $is_admin = false): array {
    if ($is_admin) {
        $stmt = $pdo->query("SELECT id, title FROM tasks");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare("SELECT id, title FROM tasks WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/**
 * Exclui uma task. Admin pode excluir qualquer task; outros só a própria.
 *
 * @param PDO $pdo
 * @param int $task_id
 * @param int $user_id
 * @param bool $is_admin
 * @return bool
 */
function delete_task(PDO $pdo, int $task_id, int $user_id, bool $is_admin = false): bool {
    if ($is_admin) {
        $sql = 'DELETE FROM tasks WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $task_id, PDO::PARAM_INT);
    } else {
        $sql = 'DELETE FROM tasks WHERE id = :id AND user_id = :user_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $task_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    }
    return $stmt->execute();
}

// Uso
$id_usuario = $_SESSION['id'] ?? 0;
$is_admin = ($id_usuario === 1);

$tasks = get_tasks_for_user($pdo, $id_usuario, $is_admin);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['task'] ?? 0);
    if ($id > 0) {
        if (delete_task($pdo, $id, $id_usuario, $is_admin)) {
            $message = "<div style='color:#558B6E;'>Task excluída com sucesso!</div>";
        } else {
            $message = "<div style='color:#776472;'>Erro ao excluir task.</div>";
        }
        // atualizar lista depois da exclusão
        $tasks = get_tasks_for_user($pdo, $id_usuario, $is_admin);
    } else {
        $message = "<div style='color:#776472;'>Selecione uma task para excluir.</div>";
    }
}

// Exclusão (admin pode excluir qualquer uma)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['noticia'] ?? '';
    if (!empty($id)) {
        if ($id_usuario == 1) {
            $sql  = 'DELETE FROM noticias WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } else {
            $sql  = 'DELETE FROM noticias WHERE id = :id AND id_usuario = :id_usuario';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        }
        if ($stmt->execute()) {
            echo "<div style='color:#558B6E;'>Notícia excluída com sucesso!</div>";
        } else {
            echo "<div style='color:#776472;'>Erro ao excluir notícia.</div>";
        }
    } else {
        echo "<div style='color:#776472;'>Selecione uma notícia para excluir.</div>";
    }
}
?>