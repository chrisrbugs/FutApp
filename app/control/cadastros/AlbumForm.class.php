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
        $this->form->setFormTitle('Album');
        
        // define the database and the Active Record
        $this->setDatabase('futapp');
        $this->setActiveRecord('Album');
        
        // create the form fields
        $id          = new TEntry('id');
        $descricao = new TEntry('descricao');
        $dt_album = new TDateTime('dt_album');
        $photo_path  = new TMultiFile('photo_path');
        
        // allow just these extensions
        $photo_path->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $dt_album->setMask('dd/mm/yyyy hh:ii');
        $dt_album->setDatabaseMask('yyyy-mm-dd hh:ii');
        
        // enable progress bar, preview, and file remove actions
        $photo_path->enableFileHandling();
        
        $id->setEditable( FALSE );
    
        // add the form fields
        $this->form->addFields( [new TLabel('ID', 'red')],          [$id] );
        $this->form->addFields( [new TLabel('Descrição', 'red')], [$descricao] );
        $this->form->addFields( [new TLabel('Data', 'red')], [$dt_album] );
        $this->form->addFields( [new TLabel('Fotos', 'red')],  [$photo_path] );
        
        $id->setSize('50%');
        
        $descricao->addValidation('descricao', new TRequiredValidator);
        
        // add the actions
        $this->form->addAction( _t('Save'), new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction( _t('Clear'), new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addActionLink( _t('List'), new TAction(['AlbumList', 'onReload']), 'fa:table blue');

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

        if ($array_fotos) 
        {
    	    foreach($array_fotos as $foto)
    	    {
        		$fotos_album = new FotosAlbum();
        		    
        		$dados_file = json_decode(urldecode($foto));
        		$nome_foto = explode('/',$dados_file->fileName)[1];
        		
                if(! is_dir("album/".$album->id)) 
                {   
                    mkdir("album/".$album->id, 0700);
                }
                
        		$fotos_album->caminho_foto = "album/".$album->id."/".uniqid().$nome_foto;    
        		$fotos_album->ref_album = $album->id;
        		
        		$fotos_album->store();
        		    
        		rename( $dados_file->fileName ,  $fotos_album->caminho_foto);
    		
    	    }
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
    public function onShow($param = null)
    {

    }
        public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('futapp'); // open a transaction

                $object = new Album($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
