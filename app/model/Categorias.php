<?php

class Categorias extends TRecord
{
    const TABLENAME  = 'categorias';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
    }

    
    /**
     * Method getClassificacaos
     */
    public function getClassificacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ref_categoria', '=', $this->id));
        return Classificacao::getObjects( $criteria );
    }
    /**
     * Method getGoleadoress
     */
    public function getGoleadoress()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ref_categoria', '=', $this->id));
        return Goleadores::getObjects( $criteria );
    }
    /**
     * Method getPunicoess
     */
    public function getPunicoess()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ref_categoria', '=', $this->id));
        return Punicoes::getObjects( $criteria );
    }
    /**
     * Method getPartidass
     */
    public function getPartidass()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('ref_categoria', '=', $this->id));
        return Partidas::getObjects( $criteria );
    }
}

