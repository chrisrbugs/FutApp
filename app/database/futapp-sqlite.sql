begin; 

PRAGMA foreign_keys=OFF; 

CREATE TABLE categorias( 
      id  INTEGER    NOT NULL  , 
      descricao text   , 
 PRIMARY KEY (id)); 

 CREATE TABLE classificacao( 
      id  INTEGER    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      time text   , 
      posicao int   , 
      jogos int   , 
      vitorias int   , 
      empates int   , 
      derrotas int   , 
      pontos int   , 
      disciplina int   , 
 PRIMARY KEY (id),
FOREIGN KEY(ref_categoria) REFERENCES categorias(id)); 

 CREATE TABLE fotos( 
      id  INTEGER    NOT NULL  , 
      caminho int   , 
 PRIMARY KEY (id)); 

 CREATE TABLE goleadores( 
      id  INTEGER    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      nome text   , 
      time text   , 
      num_gols int   , 
 PRIMARY KEY (id),
FOREIGN KEY(ref_categoria) REFERENCES categorias(id)); 

 CREATE TABLE partidas( 
      id  INTEGER    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      time_local text   , 
      time_visitante text   , 
      dt_jogo datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(ref_categoria) REFERENCES categorias(id)); 

 CREATE TABLE punicoes( 
      id  INTEGER    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      nome_jogador text   , 
      descricao text   , 
      time text   , 
 PRIMARY KEY (id),
FOREIGN KEY(ref_categoria) REFERENCES categorias(id)); 

  
 commit;