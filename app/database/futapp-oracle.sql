begin; 

CREATE TABLE categorias( 
      id number(10)    NOT NULL , 
      descricao CLOB   , 
 PRIMARY KEY (id)); 

 CREATE TABLE classificacao( 
      id number(10)    NOT NULL , 
      ref_categoria number(10)    NOT NULL , 
      time CLOB   , 
      posicao number(10)   , 
      jogos number(10)   , 
      vitorias number(10)   , 
      empates number(10)   , 
      derrotas number(10)   , 
      pontos number(10)   , 
      disciplina number(10)   , 
 PRIMARY KEY (id)); 

 CREATE TABLE fotos( 
      id number(10)    NOT NULL , 
      caminho number(10)   , 
 PRIMARY KEY (id)); 

 CREATE TABLE goleadores( 
      id number(10)    NOT NULL , 
      ref_categoria number(10)    NOT NULL , 
      nome CLOB   , 
      time CLOB   , 
      num_gols number(10)   , 
 PRIMARY KEY (id)); 

 CREATE TABLE partidas( 
      id number(10)    NOT NULL , 
      ref_categoria number(10)    NOT NULL , 
      time_local CLOB   , 
      time_visitante CLOB   , 
      dt_jogo timestamp(0)   , 
 PRIMARY KEY (id)); 

 CREATE TABLE punicoes( 
      id number(10)    NOT NULL , 
      ref_categoria number(10)    NOT NULL , 
      nome_jogador CLOB   , 
      descricao CLOB   , 
      time CLOB   , 
 PRIMARY KEY (id)); 

  
 ALTER TABLE classificacao ADD CONSTRAINT fk_classificacao_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE goleadores ADD CONSTRAINT fk_goleadores_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE partidas ADD CONSTRAINT fk_partidas_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE punicoes ADD CONSTRAINT fk_punicoes_1 FOREIGN KEY (ref_categoria) references categorias(id); 
 CREATE SEQUENCE categorias_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER categorias_id_seq_tr 

BEFORE INSERT ON categorias FOR EACH ROW 

WHEN 

(NEW.id IS NULL) 

BEGIN 

SELECT categorias_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE classificacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER classificacao_id_seq_tr 

BEFORE INSERT ON classificacao FOR EACH ROW 

WHEN 

(NEW.id IS NULL) 

BEGIN 

SELECT classificacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE fotos_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER fotos_id_seq_tr 

BEFORE INSERT ON fotos FOR EACH ROW 

WHEN 

(NEW.id IS NULL) 

BEGIN 

SELECT fotos_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE goleadores_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER goleadores_id_seq_tr 

BEFORE INSERT ON goleadores FOR EACH ROW 

WHEN 

(NEW.id IS NULL) 

BEGIN 

SELECT goleadores_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE partidas_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER partidas_id_seq_tr 

BEFORE INSERT ON partidas FOR EACH ROW 

WHEN 

(NEW.id IS NULL) 

BEGIN 

SELECT partidas_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE punicoes_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER punicoes_id_seq_tr 

BEFORE INSERT ON punicoes FOR EACH ROW 

WHEN 

(NEW.id IS NULL) 

BEGIN 

SELECT punicoes_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
  
 commit;