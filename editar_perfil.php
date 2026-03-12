<?php
session_start();
require_once 'conexao.php';

// Proteção: apenas usuário logado pode editar o perfil
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Busca dados atuais do usuário
$stmt = $pdo->prepare("SELECT nome, email, perfil FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    // Se por algum motivo não encontrar, força logout
    header("Location: logout.php");
    exit;
}

// Mensagem vinda da sessão
$mensagem = '';
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { width: 90%; max-width: 500px; }
        .card { background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; margin-top: 0; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; margin-bottom: 20px; }
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; color: #34495e; font-weight: bold; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; box-sizing: border-box; }
        .info-perfil { font-size: 14px; color: #7f8c8d; margin-bottom: 15px; }
        button { width: 100%; background-color: #27ae60; color: white; padding: 12px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #2ecc71; }
        .voltar { display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none; }
        .voltar:hover { color: #2c3e50; }
        .msg { text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2>Editar Perfil</h2>

        <?php if ($mensagem) echo "<div class='msg'>$mensagem</div>"; ?>

        <p class="info-perfil">Você está logado como <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong> (perfil: <strong><?php echo htmlspecialchars($usuario['perfil']); ?></strong>).</p>

        <form action="processa_perfil.php" method="POST">
            <div class="input-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
            </div>

            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>

            <div class="input-group">
                <label for="senha">Nova Senha (opcional)</label>
                <input type="password" id="senha" name="senha" placeholder="Deixe em branco para manter a atual">
            </div>

            <div class="input-group">
                <label for="senha_confirma">Confirmar Nova Senha</label>
                <input type="password" id="senha_confirma" name="senha_confirma" placeholder="Repita a nova senha">
            </div>

            <button type="submit">Salvar Alterações</button>
        </form>

        <?php
            // Link de voltar depende do perfil
            $link_voltar = ($_SESSION['perfil'] === 'organizador') ? 'painel_organizador.php' : 'painel_capitao.php';
        ?>
        <a href="<?php echo $link_voltar; ?>" class="voltar">← Voltar ao painel</a>
    </div>
</div>

</body>
</html>
