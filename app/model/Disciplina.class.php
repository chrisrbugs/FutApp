<?php
class Disciplina extends TRecord
{
    const TABLENAME  = 'disciplina';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pontos');
        parent::addAttribute('ref_equipe');
        parent::addAttribute('ref_partida');
    }
}
?>