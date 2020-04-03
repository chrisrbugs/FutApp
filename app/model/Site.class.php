<?php
class Site extends TRecord
{
    const TABLENAME  = 'site';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('banner1');
        parent::addAttribute('banner2');
        parent::addAttribute('banner3');
        parent::addAttribute('banner4');
        parent::addAttribute('quem_somos');
        parent::addAttribute('quem_somos_img');
        parent::addAttribute('contato');
        parent::addAttribute('banner_central');
        

    }
}
?>