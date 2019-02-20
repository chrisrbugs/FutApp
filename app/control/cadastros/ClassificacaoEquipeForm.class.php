<?php

class ClassificacaoEquipeForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'futapp';
    private static $activeRecord = 'ClassificacaoEquipe';
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
        $ref_campeonato = new TDBCombo('ref_campeonato', 'futapp', 'Campeonato', 'id', '{nome}','id asc'  );
        $ref_categoria  = new TCombo('ref_categoria');
        $ref_equipe  = new TCombo('ref_equipe');
        $posicao = new TEntry('posicao');
        $jogos = new TEntry('jogos');
        $vitorias = new TEntry('vitorias');
        $empates = new TEntry('empates');
        $derrotas = new TEntry('derrotas');
        $pontos = new TEntry('pontos');
        $disciplina = new TEntry('disciplina');


        $ref_categoria->addValidation('Ref categoria', new TRequiredValidator()); 

        $id->setEditable(false);
        $id->setSize(100);


        $posicao->setMask('999');
        $jogos->setMask('999');
        $vitorias->setMask('999');
        $empates->setMask('999');
        $derrotas->setMask('999');
        $pontos->setMask('999');
        $disciplina->setMask('999');

        $ref_campeonato->setChangeAction(new TAction([$this,'onMudaCampeonato']));
        $ref_categoria->setChangeAction(new TAction([$this,'onMudaCategoria']));

        $row1 = $this->form->addFields([new TLabel('Id:', null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel('Campeonato:', '#ff0000', '14px', null)],[$ref_campeonato]);
        $row3 = $this->form->addFields([new TLabel('Categoria:', '#ff0000', '14px', null)],[$ref_categoria]);
        $row5 = $this->form->addFields([new TLabel('Equipe:', '#ff0000', '14px', null)],[$ref_equipe]);
        $row6 = $this->form->addFields([new TLabel('Posicao:', null, '14px', null)],[$posicao]);
        $row7 = $this->form->addFields([new TLabel('Jogos:', null, '14px', null)],[$jogos]);
        $row8 = $this->form->addFields([new TLabel('Vitorias:', null, '14px', null)],[$vitorias]);
        $row9 = $this->form->addFields([new TLabel('Empates:', null, '14px', null)],[$empates]);
        $row10 = $this->form->addFields([new TLabel('Derrotas:', null, '14px', null)],[$derrotas]);
        $row11 = $this->form->addFields([new TLabel('Pontos:', null, '14px', null)],[$pontos]);
        $row11 = $this->form->addFields([new TLabel('Disciplina:', null, '14px', null)],[$disciplina]);


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

            $object = new ClassificacaoEquipe(); // create an empty object 

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
            TTransaction::open(self::$database);
            $this->fireEvents( $this->form->getData() );
            TTransaction::close();
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

                $object = new ClassificacaoEquipe($key); // instantiates the Active Record 

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
                TDBCombo::reloadFromModel('form_Classificacao', 'ref_categoria', 'futapp', 'CategoriaCampeonato', 'id', '{nome} ({id})', 'id', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form_Classificacao', 'ref_categoria');
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
                TDBCombo::reloadFromModel('form_Classificacao', 'ref_equipe', 'futapp', 'Equipe', 'id', '{nome} ({id})', 'ref_categoria_campeonato', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form_Classificacao', 'ref_equipe');
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
        TForm::sendData('form_Classificacao', $obj);
    }
}

