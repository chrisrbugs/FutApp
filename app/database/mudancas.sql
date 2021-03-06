
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

--11/02/2019
alter table punicao add column ref_atleta int;

ALTER TABLE punicao ADD CONSTRAINT ref_atleta_fk FOREIGN KEY (ref_atleta) REFERENCES atleta_equipe(id);

--13-02-2019
alter table punicao alter COLUMN descricao type text;
-- -----------------------------------------------------
CREATE TABLE disciplina (
  id serial NOT NULL,
  ref_equipe INT NOT NULL,
  pontos VARCHAR(45) NULL,
  ref_partida INT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_punições_equipe1
    FOREIGN KEY (ref_equipe)
    REFERENCES equipe (id),
  CONSTRAINT fk_punicao_partida1
    FOREIGN KEY (ref_partida)
    REFERENCES partida (id));

alter table punicao drop COLUMN pontos ;

-- 17-02-2019
CREATE TABLE goleador (
  id serial NOT NULL,
  numero_gols int NULL,
  ref_atleta int NULL,
  PRIMARY KEY (id),
  CONSTRAINT ref_atleta_fk
    FOREIGN KEY (ref_atleta)
    REFERENCES atleta_equipe (id));


-- 19-02-2019
CREATE TABLE classificacao_equipe (
  id serial NOT NULL,
  ref_equipe INT NOT NULL,
  posicao int not  NULL,
  jogos int not  NULL,
  vitorias int not  NULL,
  empates int not  NULL,
  derrotas int not  NULL,
  pontos int not  NULL,
  disciplina int not  NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_classificacao_equipe1
    FOREIGN KEY (ref_equipe)
    REFERENCES equipe (id));

alter table campeonato add column regulamento text;
alter table campeonato add column jogos text;

alter table classificacao_equipe add column gols_pro int;
alter table classificacao_equipe add column gols_contra int;
alter table classificacao_equipe add column saldo_gols int;

-- 23-02-2019
alter table  classificacao_equipe alter COLUMN saldo_gols type text;

CREATE TABLE IF NOT EXISTS fase_categoria (
  id SERIAL NOT NULL,
  descricao VARCHAR(45) NOT NULL,
  ref_categoria_campeonato INT NOT NULL,
  PRIMARY KEY (id),
   CONSTRAINT fk_fase_categoria_categoria_campeonato1
    FOREIGN KEY (ref_categoria_campeonato)
    REFERENCES categoria_campeonato (id)
    )
;

alter table classificacao_equipe add column ref_fase int;

ALTER TABLE classificacao_equipe ADD CONSTRAINT ref_fase_fk FOREIGN KEY (ref_fase) REFERENCES fase_categoria(id);

alter table campeonato add column dt_limite_inscricao date ;

----------------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS atualizacao_classificacao (
  id SERIAL NOT NULL,
  ref_categoria_campeonato int,
  dt_atualizacao date,
  PRIMARY KEY (id)
   );

alter table classificacao_equipe add column obs text;

alter table classificacao_equipe add column fl_eliminado VARCHAR(1);

CREATE TABLE IF NOT EXISTS atualizacao_goleador (
  id SERIAL NOT NULL,
  ref_categoria_campeonato int,
  dt_atualizacao date,
  PRIMARY KEY (id)
   );

CREATE TABLE IF NOT EXISTS site (
  id SERIAL NOT NULL,
  banner1 TEXT NOT NULL,
  banner2 TEXT NOT NULL,
  banner3 TEXT NOT NULL,
  banner4 TEXT NOT NULL,
  quem_somos TEXT NOT NULL,
  quem_somos_img TEXT NOT NULL,
  contato TEXT NOT NULL, 
  
  PRIMARY KEY (id))
;


CREATE TABLE IF NOT EXISTS noticias (
  id SERIAL NOT NULL,
  foto TEXT NOT NULL,
  titulo TEXT NOT NULL,
  subtitulo TEXT NOT NULL,
  texto TEXT NOT NULL,
  
  PRIMARY KEY (id))
;

alter table site add column banner_central text;