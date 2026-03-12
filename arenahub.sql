-- 1. DELETAR TABELAS ANTIGAS (Ordem inversa para não quebrar as chaves estrangeiras)
DROP TABLE IF EXISTS partidas;
DROP TABLE IF EXISTS inscricoes;
DROP TABLE IF EXISTS campeonatos;
DROP TABLE IF EXISTS jogadores;
DROP TABLE IF EXISTS times;
DROP TABLE IF EXISTS usuarios;

-- 2. CRIAR TABELA DE USUÁRIOS
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    perfil VARCHAR(50) NOT NULL
);

-- 3. CRIAR TABELA DE TIMES
CREATE TABLE times (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    contato VARCHAR(50),
    caminho_logo VARCHAR(255),
    capitao_id INT NOT NULL,
    CONSTRAINT fk_times_capitao FOREIGN KEY (capitao_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 4. CRIAR TABELA DE JOGADORES (Versão multiesportes, apenas com nome)
CREATE TABLE jogadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    time_id INT NOT NULL,
    CONSTRAINT fk_jogadores_time FOREIGN KEY (time_id) REFERENCES times(id) ON DELETE CASCADE
);

-- 5. CRIAR TABELA DE CAMPEONATOS
CREATE TABLE campeonatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    modalidade VARCHAR(50) NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    limite_times INT NOT NULL,
    organizador_id INT NOT NULL,
    CONSTRAINT fk_campeonatos_organizador FOREIGN KEY (organizador_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 6. CRIAR TABELA DE INSCRIÇÕES
CREATE TABLE inscricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    time_id INT NOT NULL,
    campeonato_id INT NOT NULL,
    status VARCHAR(50) DEFAULT 'pendente',
    CONSTRAINT fk_inscricoes_time FOREIGN KEY (time_id) REFERENCES times(id) ON DELETE CASCADE,
    CONSTRAINT fk_inscricoes_campeonato FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id) ON DELETE CASCADE
);

-- 7. CRIAR TABELA DE PARTIDAS
CREATE TABLE partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campeonato_id INT NOT NULL,
    time_a_id INT NOT NULL,
    time_b_id INT NOT NULL,
    placar_a INT NULL,
    placar_b INT NULL,
    data_hora DATETIME NOT NULL,
    CONSTRAINT fk_partidas_campeonato FOREIGN KEY (campeonato_id) REFERENCES campeonatos(id) ON DELETE CASCADE,
    CONSTRAINT fk_partidas_time_a FOREIGN KEY (time_a_id) REFERENCES times(id) ON DELETE CASCADE,
    CONSTRAINT fk_partidas_time_b FOREIGN KEY (time_b_id) REFERENCES times(id) ON DELETE CASCADE
);