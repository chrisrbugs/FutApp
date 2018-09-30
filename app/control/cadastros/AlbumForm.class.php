<?php
/**
 * Product Form
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AlbumForm extends TPage
{
    protected $form;
    
    // trait with onSave, onClear, onEdit, ...
    use Adianti\Base\AdiantiStandardFormTrait;
    
    // trait with saveFile, saveFiles, ...
    use Adianti\Base\AdiantiFileSaveTrait;
    
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('album_form');
        $this->form->setFormTitle(_t('Album'));
        
        // define the database and the Active Record
        $this->setDatabase('futapp');
        $this->setActiveRecord('Album');
        
        // create the form fields
        $id          = new TEntry('id');
        $descricao = new TEntry('descricao');
        $photo_path  = new TMultiFile('photo_path');
        
        // allow just these extensions
        $photo_path->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        
        // enable progress bar, preview, and file remove actions
        $photo_path->enableFileHandling();
        
        $id->setEditable( FALSE );
    
        // add the form fields
        $this->form->addFields( [new TLabel('ID', 'red')],          [$id] );
        $this->form->addFields( [new TLabel('Descrição', 'red')], [$descricao] );
        $this->form->addFields( [new TLabel('Fotos', 'red')],  [$photo_path] );
        
        $id->setSize('50%');
        
        $descricao->addValidation('descricao', new TRequiredValidator);
        $photo_path->addValidation('Photo Path', new TRequiredValidator);
        
        // add the actions
        $this->form->addAction( 'Save', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction( 'Clear', new TAction([$this, 'onEdit']), 'fa:eraser red');
        //$this->form->addActionLink( 'List', new TAction(['ProductList', 'onReload']), 'fa:table blue');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', 'ProductList'));
        $vbox->add($this->form);

        parent::add($vbox);
    }
    
    /**
     * Overloaded method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave()
    {
        try
        {
            TTransaction::open('futapp');
            
            // form validations
            $this->form->validate();
            
            // get form data
	    $data   = $this->form->getData();
		
	    // store product
            $album = new Album;
            $album->fromArray( (array) $data);
            $album->store();
		
	    $array_fotos = $data->photo_path;
	    foreach($array_fotos as $foto)
	    {
		$fotos_album = new FotosAlbum();
		    
		$dados_file = json_decode(urldecode($foto));
		$nome_foto = explode('/',$dados_file->fileName)[1];
		
		$fotos_album->caminho_foto = "album/".$nome_foto;
		$fotos_album->ref_album = $album->id;
		
		$fotos_album->store();
		
		$data->photo_path = $foto;
		// copy file to target folder
		$this->saveFile($fotos_album, $data, 'photo_path', 'album');
		
	    }
            
            // send id back to the form
            $data->id = $album->id;
            $this->form->setData($data);
            
            TTransaction::close();
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e)
        {
            $this->form->setData($this->form->getData());
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
