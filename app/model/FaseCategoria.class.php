<?php

class FaseCategoria extends TRecord
{
    const TABLENAME  = 'fase_categoria';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    private $fk_ref_categoria;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ref_categoria_campeonato');
        parent::addAttribute('descricao');
    }
    
}

