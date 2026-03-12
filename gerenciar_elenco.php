<?php
session_start();
require_once 'conexao.php';

// Proteção da página
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'capitao') {
    header("Location: login.php");
    exit;
}

// 1. Descobrir qual é o time desse capitão
$stmt_time = $pdo->prepare("SELECT id, nome FROM times WHERE capitao_id = ?");
$stmt_time->execute([$_SESSION['usuario_id']]);
$time = $stmt_time->fetch(PDO::FETCH_ASSOC);

// Se o cara chegou aqui por engano sem ter criado o time, manda ele cadastrar
if (!$time) {
    header("Location: cadastrar_time.php");
    exit;
}

$time_id = $time['id'];
$nome_time = $time['nome'];

// 2. Buscar os jogadores que já estão nesse time
$stmt_jogadores = $pdo->prepare("SELECT id, nome FROM jogadores WHERE time_id = ? ORDER BY nome ASC");
$stmt_jogadores->execute([$time_id]);
$jogadores = $stmt_jogadores->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Gerenciar Elenco - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; border-bottom: 2px solid #27ae60; padding-bottom: 10px; }
        .form-add { display: flex; gap: 10px; margin-bottom: 20px; }
        .form-add input { flex: 1; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; }
        .form-add button { background: #27ae60; color: white; border: none; padding: 10px 20px; font-weight: bold; cursor: pointer; border-radius: 4px; }
        ul { list-style: none; padding: 0; }
        li { background: #ecf0f1; margin-bottom: 10px; padding: 15px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
        .btn-remover { color: #e74c3c; text-decoration: none; font-weight: bold; font-size: 14px; }
        .btn-remover:hover { text-decoration: underline; }
        .voltar { display: block; text-align: center; margin-top: 20px; color: #7f8c8d; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>Elenco: <?php echo htmlspecialchars($nome_time); ?></h2>
    
    <?php if ($mensagem) echo $mensagem; ?>

    <form class="form-add" action="processa_elenco.php" method="POST">
        <input type="hidden" name="acao" value="adicionar">
        <input type="hidden" name="time_id" value="<?php echo $time_id; ?>">
        <input type="text" name="nome_jogador" placeholder="Nome completo do participante" required>
        <button type="submit">Adicionar</button>
    </form>

    <ul>
        <?php if (count($jogadores) > 0): ?>
            <?php foreach ($jogadores as $jogador): ?>
                <li>
                    <span><?php echo htmlspecialchars($jogador['nome']); ?></span>
                    <a href="processa_elenco.php?acao=remover&id=<?php echo $jogador['id']; ?>" class="btn-remover" onclick="return confirm('Tem certeza que deseja remover este integrante?');">Remover</a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li style="justify-content: center; color: #7f8c8d;">Nenhum integrante cadastrado ainda.</li>
        <?php endif; ?>
    </ul>

    <a href="painel_capitao.php" class="voltar">← Voltar ao Painel</a>
</div>

</body>
</html>