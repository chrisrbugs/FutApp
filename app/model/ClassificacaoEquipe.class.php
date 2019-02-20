<?php

class ClassificacaoEquipe extends TRecord
{
    const TABLENAME  = 'classificacao_equipe';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    private $fk_ref_categoria;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('ref_equipe');
        parent::addAttribute('posicao');
        parent::addAttribute('jogos');
        parent::addAttribute('vitorias');
        parent::addAttribute('empates');
        parent::addAttribute('derrotas');
        parent::addAttribute('pontos');
        parent::addAttribute('disciplina');
    }
    
}

