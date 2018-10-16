<?php
/**
 * JqueryGalleryView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class GaleriaFotosView extends TPage
{
    /**
     * Page constructor
     */
    function __construct($param)
    {
        parent::__construct();
        
        // loads the galleria javascript library
        TPage::include_js('app/lib/jquery/galleria/galleria-1.5.7.min.js');
        
        // creates the DIV element with the images
        $galleria = new TElement('div');
        $galleria->id    = 'images';
        $galleria->style = "width:100%;height:460px";

        TTransaction::open('futapp');
        $fotosAlbum = FotosAlbum::where('ref_album', ' = ', $param['id'])->load();
        TTransaction::close();
        
        foreach ($fotosAlbum as $fotoAlbum) 
        {
            $img  = new TElement('img');
            $img->src = $fotoAlbum->caminho_foto;
            $galleria->add($img);
        }
     
        // creates the script element
        $script =new TElement('script');
        $script->type = 'text/javascript';
        $script->add('Galleria.loadTheme("app/lib/jquery/galleria/themes/classic/galleria.classic.min.js");
                      
                      setTimeout( function() { $("#images").galleria()}, 50 ); ');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb(null,'teste'));
        $vbox->add($galleria);
        $vbox->add($script);

        parent::add($vbox);
    }

    public function onShow($param = null)
    {

    }
}
