<?php
/**
 * WelcomeView
 *
 * @version    1.0
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class ContatoView extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $html = new THtmlRenderer('app/resources/contato.html');

        // replace the main section variables
    
        
        $panel = new TPanelGroup('Telefones e email para contato');

        TTransaction::open('futapp');
        $site = new Site(1);
        TTransaction::close();
        
        $replaces = [];
        $replaces['texto']  = $site->contato;
        
        // replace the main section variables
        $html->enableSection('main', $replaces);

        $panel->add($html);
        $vbox = TVBox::pack($panel);
        $vbox->style = 'display:block; width: 90%';
        
        // add the template to the page
        parent::add( $vbox );
    }
}
