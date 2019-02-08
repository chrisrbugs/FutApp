
CREATE TABLE IF NOT EXISTS campeonato (
  id SERIAL NOT NULL,
  dt_inicio DATE NOT NULL,
  dt_fim DATE NOT NULL,
  nome VARCHAR(45) NOT NULL,
  descricao TEXT NOT NULL,
  logo TEXT NOT NULL,
  PRIMARY KEY (id))
;


-- -----------------------------------------------------
-- Table categoria_campeonato
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS categoria_campeonato (
  id SERIAL NOT NULL,
  nome VARCHAR(45) NOT NULL,
  numero_vagas INT NULL,
  ref_campeonato INT NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_campeonato_categoria_campeonato
    FOREIGN KEY (ref_campeonato)
    REFERENCES campeonato (id)
    )
;


-- -----------------------------------------------------
-- Table equipe
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS equipe (
  id SERIAL NOT NULL,
  nome TEXT NOT NULL,
  escudo TEXT NULL,
  usuario TEXT not null,
  ref_categoria_campeonato SERIAL NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_equipe_categoria_campeonato1
    FOREIGN KEY (ref_categoria_campeonato)
    REFERENCES categoria_campeonato (id)
    )
;


-- -----------------------------------------------------
-- Table atleta_equipe
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS atleta_equipe (
  id SERIAL NOT NULL,
  nome VARCHAR(45) NOT NULL,
  cpf VARCHAR(45) NOT NULL,
  ref_equipe INT NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_atleta_equipe_equipe1
    FOREIGN KEY (ref_equipe)
    REFERENCES equipe (id)
    )
;


-- -----------------------------------------------------
-- Table partida
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS partida (
  id SERIAL NOT NULL,
  ref_categoria_campeonato INT NOT NULL,
  ref_equipe_local INT NOT NULL,
  ref_equipe_visitante INT NOT NULL,
  dt_partida DATE NOT NULL,
  numero_gols_local INT NULL,
  numero_gols_visitante INT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_partida_categoria_campeonato1
    FOREIGN KEY (ref_categoria_campeonato)
    REFERENCES categoria_campeonato (id)
    ,
  CONSTRAINT fk_partida_equipe1
    FOREIGN KEY (ref_equipe_local)
    REFERENCES equipe (id)
    ,
  CONSTRAINT fk_partida_equipe2
    FOREIGN KEY (ref_equipe_visitante)
    REFERENCES equipe (id)
    )
;

alter table categoria_campeonato add column limite_atletas int not null;

-- 11/01/2019
alter table partida drop column ref_categoria_campeonato ;

CREATE TABLE punicao (
  id INT NOT NULL,
  ref_equipe INT NOT NULL,
  pontos VARCHAR(45) NULL,
  descricao VARCHAR(45) NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_punições_equipe1
    FOREIGN KEY (ref_equipe)
    REFERENCES equipe (id));


-- 05/02/2019
Drop table punicao;

CREATE TABLE punicao (
  id serial NOT NULL,
  ref_equipe INT NOT NULL,
  pontos VARCHAR(45) NULL,
  descricao VARCHAR(45) NULL,
  ref_partida INT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_punições_equipe1
    FOREIGN KEY (ref_equipe)
    REFERENCES equipe (id),
  CONSTRAINT fk_punicao_partida1
    FOREIGN KEY (ref_partida)
    REFERENCES partida (id));

-- 07/02/2019
alter table atleta_equipe add column ja_jogou boolean not null default false;