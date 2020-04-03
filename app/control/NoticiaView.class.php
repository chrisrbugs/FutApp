<?php
/**
 * PublicView
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class NoticiaView extends TPage
{
    public function __construct($param)
    {      
        parent::__construct();
        

        $panel = new TPanelGroup('Notícias');

        TTransaction::open('futapp');
        $noticia = new Noticias($param['id']);
        TTransaction::close();
        
        
        $html = new THtmlRenderer('app/resources/noticia.html');
        
        $replaces = [];
        $replaces['titulo']  = $noticia->titulo;
        $replaces['subtitulo']  = $noticia->subtitulo;
        $replaces['texto']  = $noticia->texto;
        $replaces['img']  = $noticia->foto;   
        
        // replace the main section variables
        $html->enableSection('main', $replaces);
        
        $panel->add($html);
        


        $vbox = TVBox::pack($panel);
        $vbox->style = 'display:block; width: 90%';
        
        // add the template to the page
        parent::add( $vbox );
    }
}
