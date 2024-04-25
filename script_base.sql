CREATE TABLE clubes (
    id INTEGER NOT NULL AUTO_INCREMENT,
    nome VARCHAR(70) NOT NULL,
    cidade VARCHAR(70) NOT NULL,
    imagem VARCHAR(1000) NOT NULL,
    CONSTRAINT pk_clubes PRIMARY KEY (id)
);

CREATE TABLE jogadores (
    id INTEGER NOT NULL AUTO_INCREMENT,
    nome VARCHAR(70) NOT NULL,
    posicao VARCHAR(70) NOT NULL,
    numero INTEGER NOT NULL,
    imagem VARCHAR(1000) NOT NULL,
    id_clube INTEGER NOT NULL,
    CONSTRAINT pk_jogadores PRIMARY KEY (id)
);
ALTER TABLE jogadores ADD CONSTRAINT fk_jogadores_clubes FOREIGN KEY (id_clube) REFERENCES clubes (id);


CREATE TABLE usuarios (
    id INTEGER NOT NULL AUTO_INCREMENT,
    nome VARCHAR(70) NOT NULL,
    login VARCHAR(30) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    CONSTRAINT pk_usuarios PRIMARY KEY (id)
);
INSERT INTO usuarios (nome, login, senha) VALUES ("Sr. Administrador", "admin", "$2y$10$ox60hFa.Am8h0JySNSRPOeJLcsc.jtTJXGlrTEi8HAUBIWN1aUGSC");
INSERT INTO usuarios (nome, login, senha) VALUES ("Sr. Root", "root", "$2y$10$HAcQbccHfVZMcDUTqlYI8OJv0BcwR7VeL9z2gUMf.KMgSUxOHmPF.");

ALTER TABLE usuarios ADD COLUMN foto_perfil VARCHAR(255);