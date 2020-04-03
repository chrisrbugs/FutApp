<?php

class SiteForm extends TPage
{
    protected $form;

        // trait with onSave, onClear, onEdit, ...
    use Adianti\Base\AdiantiStandardFormTrait;
    
    // trait with saveFile, saveFiles, ...
    use Adianti\Base\AdiantiFileSaveTrait;

    private $formFields = [];
    // private static $database = 'futapp';
    // private static $activeRecord = 'Campeonato';
    // private static $primaryKey = 'id';
    private static $formName = 'form_Site';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        $param['key'] = 1;
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle('Site');


        $id             = new TEntry('id');
        $banner1        = new TFile('banner1');
        $banner2        = new TFile('banner2');
        $banner3        = new TFile('banner3');
        $banner4        = new TFile('banner4');
        $banner_central = new TFile('banner_central');
        $quem_somos     = new THtmlEditor('quem_somos');
        $quem_somos_img = new TFile('quem_somos_img');
        $contato        = new THtmlEditor('contato');
    
        $banner1->addValidation('banner 1', new TRequiredValidator);
        $banner2->addValidation('banner 2', new TRequiredValidator);
        $banner3->addValidation('banner 3', new TRequiredValidator);
        $banner4->addValidation('banner 4', new TRequiredValidator);
        $banner_central->addValidation('Banner central', new TRequiredValidator);
        $quem_somos->addValidation('Quem somos', new TRequiredValidator);
        $quem_somos_img->addValidation('Quem somos img', new TRequiredValidator);
        $contato->addValidation('Contato', new TRequiredValidator);

        // allow just these extensions
        $banner1->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $banner2->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $banner3->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $banner4->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $banner_central->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $quem_somos_img->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );

        // enable progress bar, preview, and file remove actions
        $banner1->enableFileHandling();
        $banner2->enableFileHandling();
        $banner3->enableFileHandling();
        $banner4->enableFileHandling();
        $banner_central->enableFileHandling();
        $quem_somos_img->enableFileHandling();

        $id->setEditable(false);
        $id->setSize(100);

        $row1 = $this->form->addFields([new TLabel('Id:', null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel('Banner 1:', null, '14px', null)],[$banner1]);
        $row3 = $this->form->addFields([new TLabel('Banner 2:', null, '14px', null)],[$banner2]);
        $row4 = $this->form->addFields([new TLabel('Banner 3:', null, '14px', null)],[$banner3]);
        $row5 = $this->form->addFields([new TLabel('Banner 4:', null, '14px', null)],[$banner4]);
        $row5 = $this->form->addFields([new TLabel('Banner Central:', null, '14px', null)],[$banner_central]);
        $row5 = $this->form->addFields([new TLabel('Quem Somos:', null, '14px', null)],[$quem_somos]);
        $row6 = $this->form->addFields([new TLabel('Quem Somos img:', null, '14px', null)],[$quem_somos_img]);
        $row7 = $this->form->addFields([new TLabel('Contato:', null, '14px', null)],[$contato]);
        

        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(['Cadastros','Campeonatos']));
        $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open('futapp'); // open a transaction

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/

            $messageAction = null;

            $this->form->validate(); // validate form data
            
            $object = new Site($param['id']); // create an empty object 

            $data = $this->form->getData(); // get form data as array

            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // copy file to target folder
            $this->saveFile($object, $data, 'banner1', 'site');
            $this->saveFile($object, $data, 'banner2', 'site');
            $this->saveFile($object, $data, 'banner3', 'site');
            $this->saveFile($object, $data, 'banner4', 'site');
            $this->saveFile($object, $data, 'banner_central', 'site');
            $this->saveFile($object, $data, 'quem_somos_img', 'site');

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 


            $this->form->setData($data); // fill form data
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
            $param['key'] = 1;
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('futapp'); // open a transaction

                $object = new Site($key); // instantiates the Active Record 

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

