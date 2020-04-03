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
class PublicView extends TPage
{
    public function __construct()
    {
        
        parent::__construct();
        

        $panel = new TPanelGroup('Notícias');

        TTransaction::open('futapp');
        $repository = new TRepository('Noticias');
        $limit = 5;
        
        // creates a criteria
        $criteria = new TCriteria;

        $criteria->setProperty('order', 'id desc');
        $criteria->setProperty('limit', $limit);

          
        // load the objects according to criteria       
        $noticias = $repository->load($criteria, FALSE);


        TTransaction::close();
        if ($noticias) 
        {
            foreach ($noticias as $noticia) 
            {
                $html = new THtmlRenderer('app/resources/public.html');
                
                $replaces = [];
                $replaces['titulo']  = $noticia->titulo;
                $replaces['subtitulo']  = $noticia->subtitulo;
                $replaces['img']  = $noticia->foto;
                $replaces['id_noticia']  = $noticia->id;   
                
                // replace the main section variables
                $html->enableSection('main', $replaces);
                
                $panel->add($html);
            }
        }
        


        $vbox = TVBox::pack($panel);
        $vbox->style = 'display:block; width: 90%';
        
        // add the template to the page
        parent::add( $vbox );
    }
}
