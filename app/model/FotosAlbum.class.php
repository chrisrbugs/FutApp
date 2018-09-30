<?php
class FotosAlbum extends TRecord
{
    const TABLENAME  = '_fotos_album';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('caminho_foto');
        parent::addAttribute('ref_album');
    }
}
?>