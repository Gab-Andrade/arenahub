<?php
session_start();

// Proteção: Apenas o Organizador pode acessar essa tela
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'organizador') {
    header("Location: login.php");
    exit;
}

// Pega a mensagem de sucesso ou erro (se houver)
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
    <title>Criar Campeonato - ArenaHub</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; padding-top: 40px; }
        .form-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h2 { border-bottom: 2px solid #3498db; padding-bottom: 10px; color: #2c3e50; }
        .input-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #34495e; }
        input, select { width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #3498db; color: white; border: none; padding: 12px; margin-top: 10px; font-weight: bold; cursor: pointer; border-radius: 4px; font-size: 16px; }
        button:hover { background: #2980b9; }
        .voltar { display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none; }
        .voltar:hover { color: #2c3e50; }
        .msg { text-align: center; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="form-box">
        <h2>Abrir Novo Campeonato</h2>
        
        <?php if ($mensagem) echo "<div class='msg'>$mensagem</div>"; ?>

        <form action="processa_campeonato.php" method="POST">
            <div class="input-group">
                <label for="nome">Nome do Torneio</label>
                <input type="text" id="nome" name="nome" placeholder="Ex: Copa Inverno 2026" required>
            </div>

            <div class="input-group">
                <label for="modalidade">Modalidade (Esporte)</label>
                <input type="text" id="modalidade" name="modalidade" placeholder="Ex: Futsal, Vôlei, CS:GO" required>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="input-group" style="flex: 1;">
                    <label for="data_inicio">Data de Início</label>
                    <input type="date" id="data_inicio" name="data_inicio" required>
                </div>
                <div class="input-group" style="flex: 1;">
                    <label for="data_fim">Data de Término</label>
                    <input type="date" id="data_fim" name="data_fim" required>
                </div>
            </div>

            <div class="input-group">
                <label for="limite_times">Limite de Times Inscritos</label>
                <input type="number" id="limite_times" name="limite_times" min="2" max="64" placeholder="Ex: 8" required>
            </div>

            <button type="submit">Criar Campeonato</button>
        </form>
        
        <a href="painel_organizador.php" class="voltar">← Voltar ao Painel do Admin</a>
    </div>

</body>
</html>