<?php

class PunicaoForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'futapp';
    private static $activeRecord = 'Punicao';
    private static $primaryKey = 'id';
    private static $formName = 'form_Punicao';

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
        $this->form->setFormTitle('Punições');


        $id = new TEntry('id');
        $ref_campeonato = new TDBCombo('ref_campeonato', 'futapp', 'Campeonato', 'id', '{nome}','id asc'  );
        $ref_categoria  = new TCombo('ref_categoria');
        $ref_equipe  = new TCombo('ref_equipe');
        $ref_atleta = new TCombo('ref_atleta');
        $descricao = new TEntry('descricao');

        $ref_categoria->addValidation('Ref categoria', new TRequiredValidator()); 

        $id->setEditable(false);
        $id->setSize(100);
        $descricao->setSize('70%');
        $ref_atleta->setSize('70%');
        $ref_categoria->setSize('70%');


        $ref_campeonato->setChangeAction(new TAction([$this,'onMudaCampeonato']));
        $ref_categoria->setChangeAction(new TAction([$this,'onMudaCategoria']));
        $ref_equipe->setChangeAction(new TAction([$this,'onMudaEquipe']));


        $row1 = $this->form->addFields([new TLabel('Id:', null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel('Campeonato:', '#ff0000', '14px', null)],[$ref_campeonato]);
        $row3 = $this->form->addFields([new TLabel('Categoria:', '#ff0000', '14px', null)],[$ref_categoria]);
        $row4 = $this->form->addFields([new TLabel('Equipe:', '#ff0000', '14px', null)],[$ref_equipe]);
        $row5 = $this->form->addFields([new TLabel('Nome jogador:', null, '14px', null)],[$ref_atleta]);
        $row6 = $this->form->addFields([new TLabel('Descricao:', null, '14px', null)],[$descricao]);


        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');
        $this->form->addAction(_t('Back'),new TAction(array('PunicaoList','onReload')),'fa:arrow-circle-o-left blue');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(['Cadastros','Punições']));
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

            $object = new Punicao(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            $this->fireEvents( $data );
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

                $object = new Punicao($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form
                // var_dump($object);die;
                $this->fireEvents( $object ); 

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

    static function onMudaCampeonato( $param )
    {
        try
        {
            TTransaction::open('futapp');
            if (!empty($param['ref_campeonato']))
            {
                $criteria = TCriteria::create( ['ref_campeonato' => $param['ref_campeonato'] ] );
                
                // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                TDBCombo::reloadFromModel('form_Punicao', 'ref_categoria', 'futapp', 'CategoriaCampeonato', 'id', '{nome} ({id})', 'id', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form_Punicao', 'ref_categoria');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

         /**
     * On muda campeonato
     */
    static function onMudaCategoria( $param )
    {
        try
        {
            TTransaction::open('futapp');
            if (!empty($param['ref_categoria']))
            {
                $criteria = TCriteria::create( ['ref_categoria_campeonato' => $param['ref_categoria'] ] );
                
                // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                TDBCombo::reloadFromModel('form_Punicao', 'ref_equipe', 'futapp', 'Equipe', 'id', '{nome} ({id})', 'ref_categoria_campeonato', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form_Punicao', 'ref_equipe');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }


    static function onMudaEquipe( $param )
    {
        try
        {
            TTransaction::open('futapp');
            if (!empty($param['ref_equipe']))
            {
                $criteria = TCriteria::create( ['ref_equipe' => $param['ref_equipe'] ] );
                
                // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                TDBCombo::reloadFromModel('form_Punicao', 'ref_atleta', 'futapp', 'AtletaEquipe', 'id', '{nome} ({id})', 'ref_equipe', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form_Punicao', 'ref_atleta');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

        /**
     * Fire form events
     * @param $param Request
     */
    public function fireEvents( $object )
    {
        $Equipe = new Equipe($object->ref_equipe);

        $categoriaCampeonato = new CategoriaCampeonato($Equipe->ref_categoria_campeonato);

        
        $obj = new stdClass;

        $obj->ref_campeonato = $categoriaCampeonato->ref_campeonato;
        $obj->ref_categoria  = $categoriaCampeonato->id;
        $obj->ref_equipe     = $Equipe->id;
        $obj->ref_atleta     = $object->ref_atleta;
        TForm::sendData('form_Punicao', $obj);
    }
}

