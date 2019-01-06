<?php
class CategoriaCampeonato extends TRecord
{
    const TABLENAME  = 'categoria_campeonato';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('numero_vagas');
        parent::addAttribute('ref_campeonato');
    }
}
?>