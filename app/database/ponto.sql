CREATE TABLE pessoas( id SERIAL NOT NULL ,nome text not null,rfid text not null,validade timestamp, PRIMARY KEY (id)); 

CREATE TABLE marcacoes(id  SERIAL NOT NULL,data_hora timestamp  NOT NULL,ref_pessoa int not null,PRIMARY KEY (id)); 

ALTER TABLE marcacoes ADD CONSTRAINT fk_pessoas_1 FOREIGN KEY (ref_pessoa) references pessoas(id); 