<?php

class PartidasForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'futapp';
    private static $activeRecord = 'Partidas';
    private static $primaryKey = 'id';
    private static $formName = 'form_Partidas';

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
        $this->form->setFormTitle('Partidas');


        $id = new TEntry('id');
        $ref_categoria = new TDBCombo('ref_categoria', 'futapp', 'Categorias', 'id', '{descricao}','id asc'  );
        $time_local = new TEntry('time_local');
        $time_visitante = new TEntry('time_visitante');
	    $dt_jogo = new TDateTime('dt_jogo');
	    $gols_visitante = new TEntry('gols_visitante');
	    $gols_local     = new TEntry('gols_local'); 

        $ref_categoria->addValidation('Ref categoria', new TRequiredValidator()); 

        $id->setEditable(false);
        $dt_jogo->setMask('dd/mm/yyyy hh:ii');
        $dt_jogo->setDatabaseMask('yyyy-mm-dd hh:ii');
        $id->setSize(100);
        $dt_jogo->setSize(150);
        $time_local->setSize('70%');
        $ref_categoria->setSize('70%');
        $time_visitante->setSize('70%');



        $row1 = $this->form->addFields([new TLabel('Id:', null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel('Categoria:', '#ff0000', '14px', null)],[$ref_categoria]);
	    $row3 = $this->form->addFields([new TLabel('Time local:', null, '14px', null)],[$time_local]);
	    $row4 = $this->form->addFields([new TLabel('Gols Local:', null, '14px', null)],[$gols_local]);
	    $row5 = $this->form->addFields([new TLabel('Time visitante:', null, '14px', null)],[$time_visitante]);
	    $row6 = $this->form->addFields([new TLabel('Gols Visitante:', null, '14px', null)],[$gols_visitante]);
        $row7 = $this->form->addFields([new TLabel('Data do jogo:', null, '14px', null)],[$dt_jogo]);
     
        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(['Cadastros','Partidas']));
        $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Partidas(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

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
                TTransaction::open(self::$database); // open a transaction

                $object = new Partidas($key); // instantiates the Active Record 

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

