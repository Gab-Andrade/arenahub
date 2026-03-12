<?php
session_start();
require_once 'conexao.php';

// Proteção da área do Capitão
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'capitao') {
    header("Location: login.php");
    exit;
}

// 1. Pega o ID do time desse capitão
$stmt_time = $pdo->prepare("SELECT id, nome FROM times WHERE capitao_id = ?");
$stmt_time->execute([$_SESSION['usuario_id']]);
$time = $stmt_time->fetch(PDO::FETCH_ASSOC);

// Se o capitão ainda não criou um time, não tem como ele se inscrever!
if (!$time) {
    $_SESSION['mensagem'] = "<p style='color: red; text-align: center;'>Você precisa criar um time antes de se inscrever em campeonatos!</p>";
    header("Location: painel_capitao.php");
    exit;
}

$time_id = $time['id'];

// 2. Busca todos os campeonatos disponíveis no banco
$stmt_camp = $pdo->query("SELECT * FROM campeonatos ORDER BY data_inicio ASC");
$campeonatos = $stmt_camp->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Inscrição em Campeonatos - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; display: flex; flex-direction: column; align-items: center; padding-top: 40px; }
        .container { width: 90%; max-width: 800px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h2 { border-bottom: 2px solid #27ae60; padding-bottom: 10px; color: #2c3e50; margin-top: 0; }
        .msg { text-align: center; font-weight: bold; margin-bottom: 15px; }
        
        .lista-camp { list-style: none; padding: 0; margin: 0; }
        .lista-camp li { background: #ecf0f1; margin-bottom: 15px; padding: 15px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #27ae60; }
        .camp-info { display: flex; flex-direction: column; }
        .camp-nome { font-weight: bold; font-size: 18px; color: #2c3e50; }
        .camp-detalhes { font-size: 14px; color: #7f8c8d; margin-top: 8px; }
        .badge { display: inline-block; background: #27ae60; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; margin-top: 5px; width: fit-content; }
        
        .btn-inscrever { background: #27ae60; color: white; border: none; padding: 10px 15px; font-weight: bold; cursor: pointer; border-radius: 4px; font-size: 14px; transition: 0.2s; }
        .btn-inscrever:hover { background: #2ecc71; }
        .voltar { display: block; text-align: center; color: #7f8c8d; text-decoration: none; font-weight: bold; margin-top: 10px; }
        .voltar:hover { color: #2c3e50; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <h2>Campeonatos Disponíveis</h2>
            
            <?php if ($mensagem) echo "<div class='msg'>$mensagem</div>"; ?>
            
            <p style="color: #7f8c8d; margin-bottom: 20px;">Time selecionado: <strong><?php echo htmlspecialchars($time['nome']); ?></strong></p>

            <ul class="lista-camp">
                <?php if (count($campeonatos) > 0): ?>
                    <?php foreach ($campeonatos as $camp): ?>
                        <li>
                            <div class="camp-info">
                                <span class="camp-nome"><?php echo htmlspecialchars($camp['nome']); ?></span>
                                <span class="badge"><?php echo htmlspecialchars($camp['modalidade']); ?></span>
                                <span class="camp-detalhes">
                                    Início: <?php echo date('d/m/Y', strtotime($camp['data_inicio'])); ?>
                                </span>
                            </div>
                            
                            <form action="processa_inscricao.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="campeonato_id" value="<?php echo $camp['id']; ?>">
                                <input type="hidden" name="time_id" value="<?php echo $time_id; ?>">
                                <button type="submit" class="btn-inscrever">Solicitar Inscrição</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="justify-content: center; color: #7f8c8d; border-left: none; background: transparent;">
                        Nenhum campeonato com inscrições abertas no momento.
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        
        <a href="painel_capitao.php" class="voltar">← Voltar ao Painel</a>
    </div>

</body>
</html>