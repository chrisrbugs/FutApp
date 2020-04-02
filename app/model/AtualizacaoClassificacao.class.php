<?php

class AtualizacaoClassificacao extends TRecord
{
    const TABLENAME  = 'atualizacao_classificacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ref_categoria_campeonato');
        parent::addAttribute('dt_atualizacao');
    }
    
}

