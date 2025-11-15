<?php 
require '../config/config.php';
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql= "SELECT COUNT(*) FROM users WHERE email = :email";
    $stmt = $pdo -> prepare($sql);
     $stmt->bindParam(':email', $email);
            
            $stmt->execute();
            
            $existe = $stmt->fetchColumn();
            if($existe > 0 ){
                echo "<div style='color:#776472;'>Este email já existe.</div>";
            } else {
                $sql = "INSERT INTO users (nome, email, senha) VALUES (:nome, :email, :senha)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nome', $nome);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':senha', $senha);
                if ($stmt->execute()) {
                    echo "<div style='color:#558B6E;'>Cadastro realizado com sucesso!</div>";
                } else {
                    echo "<div style='color:#776472;'>Erro ao cadastrar.</div>";
                }
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
    
    <div name="div1" id="div1">

    <form action="" method="post">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">email</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">senha</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Cadastrar</button>

    </form>
        <a href="index.php">logar</a>
    </div>

</body>
</html>