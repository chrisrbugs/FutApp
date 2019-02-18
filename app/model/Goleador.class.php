<?php

class Goleador extends TRecord
{
    const TABLENAME  = 'goleador';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    private $fk_ref_categoria;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('numero_gols');
        parent::addAttribute('ref_atleta');
    }    
}