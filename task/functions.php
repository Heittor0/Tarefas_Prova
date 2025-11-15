<?php
// Funções reutilizáveis para tasks

/**
 * Retorna lista de tasks visíveis pelo usuário.
 * Admin (id == 1) vê todas.
 */
function get_tasks_for_user(PDO $pdo, int $user_id, bool $is_admin = false): array {
    if ($is_admin) {
        $stmt = $pdo->query("SELECT id, user_id, title, texto, created_at, situacao FROM tasks ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare("SELECT id, user_id, title, texto, created_at, situacao FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/**
 * Exclui uma task. Admin pode excluir qualquer task; outros só a própria.
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
function get_tasks_filtered(PDO $pdo, int $user_id, bool $is_admin, string $filtro) {

    if ($is_admin) {
        // admin vê tudo
        $base = "SELECT * FROM tasks";
        $where = "";
    } else {
        // usuário comum só vê as dele
        $base = "SELECT * FROM tasks WHERE user_id = :uid";
        $where = "";
    }

    if ($filtro === "concluida") {
        $where = " AND situacao = TRUE";
    } elseif ($filtro === "nao_concluida") {
        $where = " AND situacao = FALSE";
    }

    $sql = $base . $where . " ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);

    if (!$is_admin) {
        $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>