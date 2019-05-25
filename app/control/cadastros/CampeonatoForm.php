<?php

class CampeonatoForm extends TPage
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
    private static $formName = 'form_Campeonato';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle('Campeonato');


        $id          = new TEntry('id');
        $nome        = new TEntry('nome');
        $descricao   = new TText('descricao');
        $dt_inicio   = new TDate('dt_inicio');
        $dt_fim      = new TDate('dt_fim');
        $dt_limite_inscricao      = new TDate('dt_limite_inscricao');
        $logo        = new TFile('logo');
        $regulamento = new TFile('regulamento');
        $jogos       = new TFile('jogos');

        $dt_inicio->setMask('dd/mm/yyyy');
        $dt_inicio->setDatabaseMask('yyyy-mm-dd');
        
        $dt_fim->setMask('dd/mm/yyyy');
        $dt_fim->setDatabaseMask('yyyy-mm-dd');

        $dt_limite_inscricao->setMask('dd/mm/yyyy');
        $dt_limite_inscricao->setDatabaseMask('yyyy-mm-dd');

        $nome->addValidation('Nome', new TRequiredValidator);
        $descricao->addValidation('Descrição', new TRequiredValidator);
        $logo->addValidation('Logo', new TRequiredValidator);
        $dt_inicio->addValidation('Dt inicio', new TRequiredValidator);
        $dt_fim->addValidation('Dt fim', new TRequiredValidator);
        $dt_limite_inscricao->addValidation('Dt limite inscrição', new TRequiredValidator);

        // allow just these extensions
        $logo->setAllowedExtensions( ['gif', 'png', 'jpg', 'jpeg'] );
        $jogos->setAllowedExtensions( ['pdf'] );
        $regulamento->setAllowedExtensions( ['pdf'] );
        
        // enable progress bar, preview, and file remove actions
        $logo->enableFileHandling();
        $regulamento->enableFileHandling();
        $jogos->enableFileHandling();

        $id->setEditable(false);
        $id->setSize(100);
        $nome->setSize('70%');
        $descricao->setSize('70%');
        $dt_inicio->setSize('15%');
        $dt_fim->setSize('15%');
        $dt_limite_inscricao->setSize('15%');

        $row1 = $this->form->addFields([new TLabel('Id:', null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel('Nome:', null, '14px', null)],[$nome]);
        $row3 = $this->form->addFields([new TLabel('Descrição:', null, '14px', null)],[$descricao]);
        $row4 = $this->form->addFields([new TLabel('Data de Inicio:', null, '14px', null)],[$dt_inicio]);
        $row5 = $this->form->addFields([new TLabel('Data de Fim:', null, '14px', null)],[$dt_fim]);
        $row5 = $this->form->addFields([new TLabel('Data limite para inscrição:', null, '14px', null)],[$dt_limite_inscricao]);
        $row6 = $this->form->addFields([new TLabel('Logo:', null, '14px', null)],[$logo]);
        $row7 = $this->form->addFields([new TLabel('Regulamento:', null, '14px', null)],[$regulamento]);
        $row8 = $this->form->addFields([new TLabel('Jogos:', null, '14px', null)],[$jogos]);

        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');

        $this->form->addAction(_t('Back'),new TAction(array('CampeonatoList','onReload')),'fa:arrow-circle-o-left blue');

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

            $object = new Campeonato(); // create an empty object 

            $data = $this->form->getData(); // get form data as array

            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // copy file to target folder
            $this->saveFile($object, $data, 'logo', 'campeonatos');
            $this->saveFile($object, $data, 'jogos', 'campeonatos');
            $this->saveFile($object, $data, 'regulamento', 'campeonatos');


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
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('futapp'); // open a transaction

                $object = new Campeonato($key); // instantiates the Active Record 

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

