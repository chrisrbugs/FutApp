begin; 

CREATE TABLE categorias( 
      id  INT  AUTO_INCREMENT    NOT NULL  , 
      descricao text   , 
 PRIMARY KEY (id)); 

 CREATE TABLE classificacao( 
      id  INT  AUTO_INCREMENT    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      time text   , 
      posicao int   , 
      jogos int   , 
      vitorias int   , 
      empates int   , 
      derrotas int   , 
      pontos int   , 
      disciplina int   , 
 PRIMARY KEY (id)); 

 CREATE TABLE fotos( 
      id  INT  AUTO_INCREMENT    NOT NULL  , 
      caminho int   , 
 PRIMARY KEY (id)); 

 CREATE TABLE goleadores( 
      id  INT  AUTO_INCREMENT    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      nome text   , 
      time text   , 
      num_gols int   , 
 PRIMARY KEY (id)); 

 CREATE TABLE partidas( 
      id  INT  AUTO_INCREMENT    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      time_local text   , 
      time_visitante text   , 
      dt_jogo datetime   , 
 PRIMARY KEY (id)); 

 CREATE TABLE punicoes( 
      id  INT  AUTO_INCREMENT    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      nome_jogador text   , 
      descricao text   , 
      time text   , 
 PRIMARY KEY (id)); 

  
 ALTER TABLE classificacao ADD CONSTRAINT fk_classificacao_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE goleadores ADD CONSTRAINT fk_goleadores_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE partidas ADD CONSTRAINT fk_partidas_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE punicoes ADD CONSTRAINT fk_punicoes_1 FOREIGN KEY (ref_categoria) references categorias(id); 
 
 commit;