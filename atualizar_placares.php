<?php
session_start();
require_once 'conexao.php';

// Proteção do Organizador
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

$organizador_id = $_SESSION['usuario_id'];

// Busca todos os campeonatos desse organizador
$stmt_camp = $pdo->prepare("SELECT id, nome FROM campeonatos WHERE organizador_id = ?");
$stmt_camp->execute([$organizador_id]);
$campeonatos = $stmt_camp->fetchAll(PDO::FETCH_ASSOC);

// Se selecionou um campeonato, busca as partidas dele
$campeonato_selecionado = isset($_GET['campeonato_id']) ? $_GET['campeonato_id'] : '';
$partidas = [];

if ($campeonato_selecionado) {
    $stmt_partidas = $pdo->prepare("
        SELECT p.id, t1.nome AS time_a, t2.nome AS time_b, p.placar_a, p.placar_b, p.data_hora 
        FROM partidas p
        JOIN times t1 ON p.time_a_id = t1.id
        JOIN times t2 ON p.time_b_id = t2.id
        WHERE p.campeonato_id = ?
        ORDER BY p.data_hora ASC
    ");
    $stmt_partidas->execute([$campeonato_selecionado]);
    $partidas = $stmt_partidas->fetchAll(PDO::FETCH_ASSOC);
}

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
    <title>Atualizar Placares - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .container { width: 100%; max-width: 800px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-top: 0; }
        .form-grupo { margin-bottom: 20px; }
        label { font-weight: bold; margin-bottom: 5px; color: #34495e; display: block; }
        select { width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 16px; }
        
        .partida-card { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: flex; flex-direction: column; align-items: center; }
        .data-hora { font-size: 13px; color: #7f8c8d; margin-bottom: 10px; font-weight: bold; }
        
        .placar-form { display: flex; align-items: center; gap: 15px; justify-content: center; width: 100%; flex-wrap: wrap; }
        .time-nome { font-size: 18px; font-weight: bold; color: #2c3e50; width: 120px; text-align: center; }
        .input-placar { width: 60px; padding: 10px; text-align: center; font-size: 18px; font-weight: bold; border: 2px solid #3498db; border-radius: 4px; }
        .x-vermelho { font-weight: bold; color: #e74c3c; font-size: 20px; }
        
        button.btn-salvar { background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer; transition: 0.2s; }
        button.btn-salvar:hover { background: #2ecc71; }
        
        .voltar { display: block; text-align: center; color: #7f8c8d; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .msg { text-align: center; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Atualizar Placares</h2>
        
        <?php if ($mensagem) echo "<div class='msg'>$mensagem</div>"; ?>

        <form method="GET" action="atualizar_placares.php">
            <div class="form-grupo">
                <label>Selecione o Campeonato:</label>
                <select name="campeonato_id" onchange="this.form.submit()" required>
                    <option value="">Escolha um torneio...</option>
                    <?php foreach ($campeonatos as $camp): ?>
                        <option value="<?php echo $camp['id']; ?>" <?php if ($campeonato_selecionado == $camp['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($camp['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if ($campeonato_selecionado): ?>
            <h3 style="color: #34495e; border-bottom: 1px solid #ecf0f1; padding-bottom: 5px; margin-top: 30px;">Jogos Agendados</h3>
            
            <?php if (count($partidas) > 0): ?>
                <?php foreach ($partidas as $p): ?>
                    <div class="partida-card">
                        <div class="data-hora">🗓️ <?php echo date('d/m/Y \à\s H:i', strtotime($p['data_hora'])); ?></div>
                        
                        <form class="placar-form" action="processa_placares.php" method="POST">
                            <input type="hidden" name="campeonato_id" value="<?php echo $campeonato_selecionado; ?>">
                            <input type="hidden" name="partida_id" value="<?php echo $p['id']; ?>">
                            
                            <div class="time-nome"><?php echo htmlspecialchars($p['time_a']); ?></div>
                            
                            <input type="number" class="input-placar" name="placar_a" min="0" 
                                   value="<?php echo isset($p['placar_a']) ? $p['placar_a'] : ''; ?>" required placeholder="0">
                            
                            <div class="x-vermelho">X</div>
                            
                            <input type="number" class="input-placar" name="placar_b" min="0" 
                                   value="<?php echo isset($p['placar_b']) ? $p['placar_b'] : ''; ?>" required placeholder="0">
                            
                            <div class="time-nome"><?php echo htmlspecialchars($p['time_b']); ?></div>
                            
                            <button type="submit" class="btn-salvar">Salvar</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #7f8c8d;">Nenhum jogo agendado neste campeonato ainda.</p>
            <?php endif; ?>
        <?php endif; ?>

        <a href="painel_organizador.php" class="voltar">← Voltar ao Painel do Admin</a>
    </div>

</body>
</html>