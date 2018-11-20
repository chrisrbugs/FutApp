<?php

class Marcacoes extends TRecord
{
    const TABLENAME  = 'marcacoes';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_hora');
        parent::addAttribute('ref_pessoa');
    }
}

