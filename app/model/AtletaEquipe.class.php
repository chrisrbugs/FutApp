<?php
class AtletaEquipe extends TRecord
{
    const TABLENAME  = 'atleta_equipe';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cpf');
        parent::addAttribute('ref_equipe');
    }
}
?>