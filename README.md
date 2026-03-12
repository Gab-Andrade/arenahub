# ArenaHub

Aplicação web em PHP para gestão de campeonatos esportivos, permitindo organização de torneios, cadastro de times/jogadores e acompanhamento de tabela e jogos em uma interface amigável.

## Funcionalidades principais

- Página pública com listagem dos campeonatos ativos, tabela de classificação e jogos/resultados.
- Autenticação de usuários (organizadores e capitães de time).
- Painel do organizador:
  - Criação e gerenciamento de campeonatos.
  - Aprovação de inscrições de times.
  - Agendamento de partidas.
  - Atualização de placares.
- Painel do capitão:
  - Cadastro/edição de time.
  - Gerenciamento de elenco (jogadores).
  - Inscrição do time em campeonatos.
  - Acompanhamento de histórico de partidas.

## Tecnologias utilizadas

- PHP (estilo procedural, com PDO para acesso ao banco)
- MySQL / MariaDB
- HTML/CSS (layout simples, sem framework front-end)
- XAMPP ou similar para ambiente local (Apache + PHP + MySQL)

## Estrutura do banco de dados

O script de criação do banco está em:

- [arenahub/banco.sql](arenahub/banco.sql)

Ele cria as principais tabelas:

- `usuarios` – perfis de usuário (organizador, capitão etc.).
- `times` – times cadastrados, vinculados a um capitão.
- `jogadores` – jogadores vinculados aos times.
- `campeonatos` – campeonatos criados pelos organizadores.
- `inscricoes` – inscrições de times em campeonatos (pendente/aprovada).
- `partidas` – partidas agendadas e seus resultados.

## Configuração do ambiente

1. **Clonar o repositório**
   ```bash
   git clone https://github.com/Gab-Andrade/arenahub.git
   ```

2. **Mover para o diretório do projeto** (caso use XAMPP em Linux, por exemplo):
   ```bash
   cd /opt/lampp/htdocs/arenahub
   ```

3. **Criar o banco de dados** no MySQL/MariaDB:
   ```sql
   CREATE DATABASE arenahub CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

4. **Importar o script** [arenahub/banco.sql](arenahub/banco.sql) dentro do banco `arenahub`.

5. **Configurar a conexão** em [arenahub/conexao.php](arenahub/conexao.php) se necessário (host, usuário, senha):
   ```php
   $host = 'localhost';
   $dbname = 'arenahub';
   $usuario = 'root';
   $senha = '';
   ```

## Como usar

- Acesse a página inicial pública pelo navegador:
  - `http://localhost/arenahub/index.php`
- Use a opção **Login do Sistema** para acessar os painéis de **organizador** ou **capitão** (conforme usuários cadastrados no banco).
- No painel do organizador, crie campeonatos, aprove inscrições, agende partidas e registre placares.
- No painel do capitão, cadastre/edite seu time, gerencie jogadores e inscreva o time nos campeonatos disponíveis.

## Observações

- Este projeto foi desenvolvido para ambiente local (como XAMPP). Para uso em produção, é recomendado configurar variáveis de ambiente para credenciais de banco, usar HTTPS e revisar regras de segurança/autorização.
