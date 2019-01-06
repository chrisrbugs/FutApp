<?php
class Equipe extends TRecord
{
    const TABLENAME  = 'equipe';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('escudo');
        parent::addAttribute('usuario');
        parent::addAttribute('ref_categoria_campeonato');
    }

      /**
     * Method getAtletasEquipe
     */
    public function getAtletasEquipe()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ref_equipe', '=', $this->id));
        return AtletaEquipe::getObjects( $criteria );
    }
}
?>