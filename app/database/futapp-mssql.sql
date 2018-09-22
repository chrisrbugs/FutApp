begin; 

CREATE TABLE categorias( 
      id  INT IDENTITY    NOT NULL  , 
      descricao nvarchar(max)   , 
 PRIMARY KEY (id)); 

 CREATE TABLE classificacao( 
      id  INT IDENTITY    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      time nvarchar(max)   , 
      posicao int   , 
      jogos int   , 
      vitorias int   , 
      empates int   , 
      derrotas int   , 
      pontos int   , 
      disciplina int   , 
 PRIMARY KEY (id)); 

 CREATE TABLE fotos( 
      id  INT IDENTITY    NOT NULL  , 
      caminho int   , 
 PRIMARY KEY (id)); 

 CREATE TABLE goleadores( 
      id  INT IDENTITY    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      nome nvarchar(max)   , 
      time nvarchar(max)   , 
      num_gols int   , 
 PRIMARY KEY (id)); 

 CREATE TABLE partidas( 
      id  INT IDENTITY    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      time_local nvarchar(max)   , 
      time_visitante nvarchar(max)   , 
      dt_jogo datetime2   , 
 PRIMARY KEY (id)); 

 CREATE TABLE punicoes( 
      id  INT IDENTITY    NOT NULL  , 
      ref_categoria int   NOT NULL  , 
      nome_jogador nvarchar(max)   , 
      descricao nvarchar(max)   , 
      time nvarchar(max)   , 
 PRIMARY KEY (id)); 

  
 ALTER TABLE classificacao ADD CONSTRAINT fk_classificacao_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE goleadores ADD CONSTRAINT fk_goleadores_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE partidas ADD CONSTRAINT fk_partidas_1 FOREIGN KEY (ref_categoria) references categorias(id); 
ALTER TABLE punicoes ADD CONSTRAINT fk_punicoes_1 FOREIGN KEY (ref_categoria) references categorias(id); 
 
 commit;