<?php
class Punicao extends TRecord
{
    const TABLENAME  = 'punicao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ref_equipe');
        parent::addAttribute('pontos');
        parent::addAttribute('descricao');
        parent::addAttribute('ref_partida');
    }
}
?>