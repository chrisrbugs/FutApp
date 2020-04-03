<?php
class Noticias extends TRecord
{
    const TABLENAME  = 'noticias';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('titulo');
        parent::addAttribute('subtitulo');
        parent::addAttribute('texto');
        parent::addAttribute('foto');
        

    }
}
?>