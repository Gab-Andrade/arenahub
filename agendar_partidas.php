<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

$organizador_id = $_SESSION['usuario_id'];

// Busca todos os campeonatos desse organizador
$stmt_camp = $pdo->prepare("SELECT id, nome FROM campeonatos WHERE organizador_id = ?");
$stmt_camp->execute([$organizador_id]);
$campeonatos = $stmt_camp->fetchAll(PDO::FETCH_ASSOC);

// Se o usuário selecionou um campeonato, busca os times inscritos nele
$campeonato_selecionado = isset($_GET['campeonato_id']) ? $_GET['campeonato_id'] : '';
$times_inscritos = [];

if ($campeonato_selecionado) {
    // Aqui removemos a trava do status! Qualquer time inscrito aparece.
    $stmt_times = $pdo->prepare("
        SELECT t.id, t.nome 
        FROM inscricoes i 
        JOIN times t ON i.time_id = t.id 
        WHERE i.campeonato_id = ?
        ORDER BY t.nome ASC
    ");
    $stmt_times->execute([$campeonato_selecionado]);
    $times_inscritos = $stmt_times->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Agendar Partidas - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; padding: 40px 20px; display: flex; flex-direction: column; align-items: center; }
        .container { width: 100%; max-width: 700px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-top: 0; }
        .form-grupo { margin-bottom: 15px; display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 5px; color: #34495e; }
        select, input { padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 16px; }
        button { background: #3498db; color: white; border: none; padding: 12px; border-radius: 4px; font-weight: bold; cursor: pointer; width: 100%; font-size: 16px; margin-top: 10px; }
        button:hover { background: #2980b9; }
        .voltar { display: block; text-align: center; color: #7f8c8d; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .msg { text-align: center; font-weight: bold; margin-bottom: 15px; }
        .confronto-box { display: flex; gap: 15px; align-items: center; justify-content: space-between; margin-bottom: 15px; }
        .confronto-box .form-grupo { flex: 1; margin-bottom: 0; }
        .x-vermelho { font-weight: bold; color: #e74c3c; font-size: 20px; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Agendar Nova Partida</h2>
        
        <?php if ($mensagem) echo "<div class='msg'>$mensagem</div>"; ?>

        <form method="GET" action="agendar_partidas.php" style="margin-bottom: 30px;">
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
            <?php if (count($times_inscritos) >= 2): ?>
                
                <form action="processa_agendamento.php" method="POST" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
                    <input type="hidden" name="campeonato_id" value="<?php echo $campeonato_selecionado; ?>">
                    
                    <div class="confronto-box">
                        <div class="form-grupo">
                            <label>Time Mandante (A)</label>
                            <select name="time_a_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($times_inscritos as $t): ?>
                                    <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="x-vermelho">X</div>

                        <div class="form-grupo">
                            <label>Time Visitante (B)</label>
                            <select name="time_b_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($times_inscritos as $t): ?>
                                    <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-grupo">
                        <label>Data e Hora do Jogo</label>
                        <input type="datetime-local" name="data_hora" required>
                    </div>

                    <button type="submit">Salvar Agendamento</button>
                </form>

            <?php else: ?>
                <p style="color: #e67e22; text-align: center; font-weight: bold;">
                    Este campeonato precisa de pelo menos 2 times inscritos para gerar jogos.
                </p>
            <?php endif; ?>
        <?php endif; ?>

        <a href="painel_organizador.php" class="voltar">← Voltar ao Painel do Admin</a>
    </div>

</body>
</html>