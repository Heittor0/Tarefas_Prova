<?php 
require '../config/config.php';
session_start();
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $nome = trim($_POST['nome'] ?? "");
    $senha = $_POST['senha'] ?? "";
    
    $sql = "SELECT * FROM users WHERE nome = :nome";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
  
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id'] = $usuario['id']; // ou 'id' se for esse o nome do campo
        header("Location:../index.php");
        exit;
    } else {
        echo "<div style='color:#776472;'>Nome, email ou senha incorretos.</div>";
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
    <header>

        <a href="cadastrar.php">Cadastrar</a>


    </header>
    <div>
        <form action="" method="post">
            <label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" required>

            <label for="nome">senha</label>
            <input type="password" name="senha" id="senha" required>

        <button type="submit">Login</button>

        </form>

    </div>

</body>
</html>
