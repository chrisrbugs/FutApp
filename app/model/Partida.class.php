<?php
class Partida extends TRecord
{
    const TABLENAME  = 'partida';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ref_equipe_local');
        parent::addAttribute('ref_equipe_visitante');
        parent::addAttribute('dt_partida');
        parent::addAttribute('numero_gols_local');
        parent::addAttribute('numero_gols_visitante');
        parent::addAttribute('etapa');
    }
}
?>