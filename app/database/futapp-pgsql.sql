begin; 

CREATE TABLE categorias( 
      id  SERIAL    NOT NULL  , 
      descricao text   , 
 PRIMARY KEY (id)); 

 CREATE TABLE classificacao( 
      id  SERIAL    NOT NULL  , 
      ref_categoria integer   NOT NULL  , 
      time text   , 
      posicao integer   , 
      jogos integer   , 
      vitorias integer   , 
      empates integer   , 
      derrotas integer   , 
      pontos integer   , 
      disciplina integer   , 
 PRIMARY KEY (id)); 

 CREATE TABLE fotos( 
      id  SERIAL    NOT NULL  , 
      caminho integer   , 
 PRIMARY KEY (id)); 

 CREATE TABLE goleadores( 
      id  SERIAL    NOT NULL  , 
      ref_categoria integer   NOT NULL  , 
      nome text   , 
      time text   , 
      num_gols integer   , 
 PRIMARY KEY (id)); 

 CREATE TABLE partidas( 
      id  SERIAL    NOT NULL  , 
      ref_categoria integer   NOT NULL  , 
      time_local text   , 
      time_visitante text   , 
      dt_jogo timestamp   , 
 PRIMARY KEY (id)); 

 CREATE TABLE punicoes( 
      id  SERIAL    NOT NULL  , 
      ref_categoria integer   NOT NULL  , 
      nome_jogador text   , 
      descricao text   , 
      time text   , 
 PRIMARY KEY (id)); 

  
 ALTER TABLE classificacao ADD CONSTRAINT fk_classificacao_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE goleadores ADD CONSTRAINT fk_goleadores_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE partidas ADD CONSTRAINT fk_partidas_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE punicoes ADD CONSTRAINT fk_punicoes_1 FOREIGN KEY (ref_categoria) references categorias(id); 
 
 commit;