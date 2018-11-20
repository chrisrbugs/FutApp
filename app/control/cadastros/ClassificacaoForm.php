<?php

class ClassificacaoForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'futapp';
    private static $activeRecord = 'Classificacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_Classificacao';

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
        $this->form->setFormTitle('Classificação');


        $id = new TEntry('id');
        $ref_categoria = new TDBCombo('ref_categoria', 'futapp', 'Categorias', 'id', '{descricao}','id asc'  );
        $time = new TEntry('time');
        $posicao = new TEntry('posicao');
        $jogos = new TEntry('jogos');
        $vitorias = new TEntry('vitorias');
        $empates = new TEntry('empates');
        $derrotas = new TEntry('derrotas');
        $pontos = new TEntry('pontos');
        $disciplina = new TEntry('disciplina');

        $eliminado = new TRadioGroup('eliminado');
        $options = ['t'=>'Sim','f'=>'Não'];
        $eliminado->addItems($options);

        $ref_categoria->addValidation('Ref categoria', new TRequiredValidator()); 

        $id->setEditable(false);
        $id->setSize(100);
        $time->setSize('70%');
        $jogos->setSize('70%');
        $pontos->setSize('70%');
        $posicao->setSize('70%');
        $empates->setSize('70%');
        $vitorias->setSize('70%');
        $derrotas->setSize('70%');
        $disciplina->setSize('70%');
        $ref_categoria->setSize('70%');


        $row1 = $this->form->addFields([new TLabel('Id:', null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel('Categoria:', '#ff0000', '14px', null)],[$ref_categoria]);
        $row3 = $this->form->addFields([new TLabel('Time:', null, '14px', null)],[$time]);
        $row4 = $this->form->addFields([new TLabel('Posicao:', null, '14px', null)],[$posicao]);
        $row5 = $this->form->addFields([new TLabel('Jogos:', null, '14px', null)],[$jogos]);
        $row6 = $this->form->addFields([new TLabel('Vitorias:', null, '14px', null)],[$vitorias]);
        $row7 = $this->form->addFields([new TLabel('Empates:', null, '14px', null)],[$empates]);
        $row8 = $this->form->addFields([new TLabel('Derrotas:', null, '14px', null)],[$derrotas]);
        $row9 = $this->form->addFields([new TLabel('Pontos:', null, '14px', null)],[$pontos]);
        $row10 = $this->form->addFields([new TLabel('Disciplina:', null, '14px', null)],[$disciplina]);
        $row11 = $this->form->addFields([new TLabel('Eliminado:', null, '14px', null)],[$eliminado]);

        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(['Cadastros','Classificação']));
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

            $object = new Classificacao(); // create an empty object 

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

                $object = new Classificacao($key); // instantiates the Active Record 

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
  
    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);

                // instantiates object
                $object = new Punicoes($key, FALSE); 

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
    }
    
}

