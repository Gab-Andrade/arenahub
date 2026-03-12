<?php
// Trazemos a conexão com o banco de dados logo na primeira linha
require_once 'conexao.php';

// (Futuramente, faremos os comandos SQL SELECT aqui para buscar os times e partidas)
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArenaHub - Acompanhe seu Torneio</title>
    <style>
        /* Estrutura visual básica usando propriedades CSS clássicas */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        /* Cabeçalho usando Flexbox para alinhar a logo e o botão de login */
        header {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header a.btn-login {
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        header a.btn-login:hover {
            background-color: #2ecc71;
        }

        /* Container central para segurar as seções */
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
        }

        /* Estilo dos blocos de conteúdo */
        section {
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h2 {
            color: #34495e;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }

        /* Estilização da tabela de classificação */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ecf0f1;
        }

        th {
            background-color: #ecf0f1;
            color: #2c3e50;
        }
    </style>
</head>
<body>

    <header>
        <h1>ArenaHub</h1>
        <a href="login.php" class="btn-login">Login / Cadastro</a>
    </header>

    <div class="container">
        
        <section id="classificacao">
            <h2>Tabela de Classificação</h2>
            <table>
                <thead>
                    <tr>
                        <th>Posição</th>
                        <th>Time</th>
                        <th>Pontos</th>
                        <th>Vitórias</th>
                        <th>Derrotas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" style="color: #7f8c8d;">Nenhum dado disponível ainda. O torneio não começou!</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section id="calendario">
            <h2>Próximos Jogos</h2>
            <div class="lista-jogos">
                <p style="text-align: center; color: #7f8c8d;">Nenhuma partida agendada para os próximos dias.</p>
            </div>
        </section>

        <section id="resultados">
            <h2>Resultados Recentes</h2>
            <div class="lista-resultados">
                <p style="text-align: center; color: #7f8c8d;">Nenhum jogo foi encerrado ainda.</p>
            </div>
        </section>

    </div>

</body>
</html>