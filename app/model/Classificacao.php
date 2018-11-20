<?php

class Classificacao extends TRecord
{
    const TABLENAME  = 'classificacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    private $fk_ref_categoria;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ref_categoria');
        parent::addAttribute('time');
        parent::addAttribute('posicao');
        parent::addAttribute('jogos');
        parent::addAttribute('vitorias');
        parent::addAttribute('empates');
        parent::addAttribute('derrotas');
        parent::addAttribute('pontos');
        parent::addAttribute('disciplina');
        parent::addAttribute('eliminado');
    }

    /**
     * Method set_categorias
     * Sample of usage: $var->categorias = $object;
     * @param $object Instance of Categorias
     */
    public function set_fk_ref_categoria(Categorias $object)
    {
        $this->fk_ref_categoria = $object;
        $this->ref_categoria = $object->id;
    }
    
    /**
     * Method get_fk_ref_categoria
     * Sample of usage: $var->fk_ref_categoria->attribute;
     * @returns Categorias instance
     */
    public function get_fk_ref_categoria()
    {
        
        // loads the associated object
        if (empty($this->fk_ref_categoria))
            $this->fk_ref_categoria = new Categorias($this->ref_categoria);
        
        // returns the associated object
        return $this->fk_ref_categoria;
    }
    
}

