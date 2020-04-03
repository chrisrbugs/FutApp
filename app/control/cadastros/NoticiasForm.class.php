<?php

class NoticiasForm extends TPage
{
    protected $form; // form
        // trait with onSave, onClear, onEdit, ...
    use Adianti\Base\AdiantiStandardFormTrait;
    
    // trait with saveFile, saveFiles, ...
    use Adianti\Base\AdiantiFileSaveTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder('form_Noticias');
        // define the form title
        $this->form->setFormTitle('Notícias');


        $id        = new TEntry('id');
        $titulo    = new TEntry('titulo');
        $subtitulo = new TEntry('subtitulo');
        $texto     = new THtmlEditor('texto');
        $foto      = new TFile('foto');

        $titulo->addValidation('Titulo', new TRequiredValidator()); 
        $subtitulo->addValidation('Subtitulo', new TRequiredValidator()); 
        $texto->addValidation('Texto', new TRequiredValidator()); 
        $foto->addValidation('Foto', new TRequiredValidator()); 

         // allow just these extensions
        $foto->setAllowedExtensions( ['png', 'jpg', 'jpeg'] );
        
        // enable progress bar, preview, and file remove actions
        $foto->enableFileHandling();


        $id->setEditable(false);
        $id->setSize(100);


        $row1 = $this->form->addFields([new TLabel('Id:', null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel('Titulo:', '#ff0000', '14px', null)],[$titulo]);
        $row3 = $this->form->addFields([new TLabel('Subtitulo:', '#ff0000', '14px', null)],[$subtitulo]);
        $row4 = $this->form->addFields([new TLabel('Texto:', '#ff0000', '14px', null)],[$texto]);
        $row5 = $this->form->addFields([new TLabel('Foto:', null, '14px', null)],[$foto]);


        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');
        $this->form->addAction(_t('Back'),new TAction(array('NoticiasList','onReload')),'fa:arrow-circle-o-left blue');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(['Cadastros','Notícias']));
        $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open('futapp');

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Noticias(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data

            $this->saveFile($object, $data, 'foto', 'noticias'); 
            
            TTransaction::close(); // close the transaction
            /**
            // To define an action to be executed on the message close event:
            $messageAction = new TAction(['className', 'methodName']);
            **/

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $messageAction);

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                TTransaction::open('futapp'); // open a transaction
                $key = $param['key'];  // get the parameter $key

                $object = new Noticias($key); // instantiates the Active Record 

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

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

    } 
}

