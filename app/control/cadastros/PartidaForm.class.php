<?php

class PartidaForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'futapp';
    private static $activeRecord = 'Partida';
    private static $primaryKey = 'id';
    private static $formName = 'form_Partida';

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
        $ref_campeonato = new TDBCombo('ref_campeonato', 'futapp', 'Campeonato', 'id', '{nome}','id asc'  );
        $ref_categoria  = new TCombo('ref_categoria');
        $ref_equipe_local     = new TCombo('ref_equipe_local');
        $ref_equipe_visitante = new TCombo('ref_equipe_visitante');
	    $dt_partida        = new TDateTime('dt_partida');
	    $numero_gols_visitante = new TEntry('numero_gols_visitante');
	    $numero_gols_local     = new TEntry('numero_gols_local'); 
        $etapa     = new TEntry('etapa'); 

        $ref_categoria->addValidation('Categoria', new TRequiredValidator()); 
        $ref_equipe_visitante->addValidation('Time local', new TRequiredValidator()); 
        $ref_equipe_local->addValidation('Time visitante', new TRequiredValidator()); 
        $etapa->addValidation('Etapa', new TRequiredValidator()); 

        $id->setEditable(false);
        $dt_partida->setMask('dd/mm/yyyy hh:ii');
        $dt_partida->setDatabaseMask('yyyy-mm-dd hh:ii');
        $id->setSize(100);
        $dt_partida->setSize(150);
        $ref_equipe_local->setSize('70%');
        $ref_categoria->setSize('70%');
        $ref_equipe_visitante->setSize('70%');

        $ref_campeonato->setChangeAction(new TAction([$this,'onMudaCampeonato']));
        $ref_categoria->setChangeAction(new TAction([$this,'onMudaCategoria']));

        $row1 = $this->form->addFields([new TLabel('Id:', null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel('Campeonato:', '#ff0000', '14px', null)],[$ref_campeonato]);
        $row3 = $this->form->addFields([new TLabel('Categoria:', '#ff0000', '14px', null)],[$ref_categoria]);
        $row9 = $this->form->addFields([new TLabel('Etapa:', '#ff0000', '14px', null)],[$etapa]);
	    $row4 = $this->form->addFields([new TLabel('Time local:', null, '14px', null)],[$ref_equipe_local]);
	    $row5 = $this->form->addFields([new TLabel('Gols Local:', null, '14px', null)],[$numero_gols_local]);
	    $row6 = $this->form->addFields([new TLabel('Time visitante:', null, '14px', null)],[$ref_equipe_visitante]);
	    $row7 = $this->form->addFields([new TLabel('Gols Visitante:', null, '14px', null)],[$numero_gols_visitante]);
        $row8 = $this->form->addFields([new TLabel('Data do jogo:', null, '14px', null)],[$dt_partida]);

        $this->form->addContent( ['<h4>Punições equipe local</h4><hr>'] );
        $pts_punicao_local       = new TEntry('pts_punicao_local');

        $pts_punicao_local->setMask('999');

        $row9 = $this->form->addFields([new TLabel('Pontos:', '#ff0000', '14px', null)],[$pts_punicao_local]);

        $this->form->addContent( ['<h4>Punições equipe visitante</h4><hr>'] );
        $pts_punicao_visitante       = new TEntry('pts_punicao_visitante');

        $pts_punicao_visitante->setMask('999');

        $row9 = $this->form->addFields([new TLabel('Pontos:', '#ff0000', '14px', null)],[$pts_punicao_visitante]);

     
        // create the form actions
        $btn_onsave = $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction('Limpar formulário', new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');
        $this->form->addAction(_t('Back'),new TAction(array('PartidaList','onReload')),'fa:arrow-circle-o-left blue');

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

            $data = $this->form->getData(); // get form data as array

            $ObjPartida = new Partida($data->id); // create an empty object 
            $ObjPartida->ref_equipe_local = $data->ref_equipe_local;
            $ObjPartida->ref_equipe_visitante = $data->ref_equipe_visitante;
            $ObjPartida->dt_partida = $data->dt_partida;
            $ObjPartida->numero_gols_local = $data->numero_gols_local;
            $ObjPartida->numero_gols_visitante = $data->numero_gols_visitante;
            $ObjPartida->etapa = $data->etapa;

            $ObjPartida->store(); 

            $ObjDisciplinaLocal = Disciplina::where('ref_partida', '=',$ObjPartida->id)
                                      ->where('ref_equipe','=',$ObjPartida->ref_equipe_local)
                                      ->load();
            
            if ($ObjDisciplinaLocal) 
            {
                $disciplinaLocal = new Disciplina($ObjDisciplinaLocal[0]->id);
                $disciplinaLocal->pontos = $data->pts_punicao_local;
                $disciplinaLocal->store();

            }
            else
            {
                $disciplinaLocal = new Disciplina();
                $disciplinaLocal->ref_equipe = $data->ref_equipe_local;
                $disciplinaLocal->ref_partida = $ObjPartida->id;
                $disciplinaLocal->pontos = $data->pts_punicao_local;
                $disciplinaLocal->store();
            }

            $ObjDisciplinaVisitante = Disciplina::where('ref_partida', '=',$ObjPartida->id)
                                          ->where('ref_equipe','=',$ObjPartida->ref_equipe_visitante)
                                      ->load();
            
            if ($ObjDisciplinaVisitante) 
            {
                $disciplinaVisitante = new Disciplina($ObjDisciplinaVisitante[0]->id);
                $disciplinaVisitante->pontos = $data->pts_punicao_visitante;
                $disciplinaVisitante->store();

            }
            else
            {
                $disciplinaVisitante = new Disciplina();
                $disciplinaVisitante->ref_equipe = $data->ref_equipe_visitante;
                $disciplinaVisitante->ref_partida = $ObjPartida->id;
                $disciplinaVisitante->pontos = $data->pts_punicao_visitante;
                $disciplinaVisitante->store();
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $ObjPartida->id; 

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

    /**
     * Fire form events
     * @param $param Request
     */
    public function fireEvents( $object )
    {
        $obj = new stdClass;

        $obj->ref_campeonato     = $object->ref_campeonato;
        $obj->ref_categoria    = $object->ref_categoria;
        $obj->ref_equipe_local = $object->ref_equipe_local;
        $obj->ref_equipe_visitante = $object->ref_equipe_visitante;
        TForm::sendData('form_Partida', $obj);
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $objPartida = new Partida($key); // instantiates the Active Record 

                $objEquipeLocal =  new Equipe($objPartida->ref_equipe_local);

                $objCategoria =  new CategoriaCampeonato($objEquipeLocal->ref_categoria_campeonato);

                $ObjPunicaoLocal = Disciplina::where('ref_partida', '=',$objPartida->id)
                                      ->where('ref_equipe','=',$objPartida->ref_equipe_local)
                                      ->load();

                $ObjPunicaoVisitante = Disciplina::where('ref_partida', '=',$objPartida->id)
                                      ->where('ref_equipe','=',$objPartida->ref_equipe_visitante)
                                      ->load();

                if ($ObjPunicaoLocal) 
                {
                    $objPartida->pts_punicao_local = $ObjPunicaoLocal[0]->pontos;
                }

                if ($ObjPunicaoVisitante) 
                {
                    $objPartida->pts_punicao_visitante = $ObjPunicaoVisitante[0]->pontos;
                }

                
                $obj = new stdClass;

                $obj->ref_campeonato       = $objCategoria->ref_campeonato;
                $obj->ref_categoria        = $objCategoria->id;
                $obj->ref_equipe_local     = $objPartida->ref_equipe_local;
                $obj->ref_equipe_visitante = $objPartida->ref_equipe_visitante;


                $this->form->setData($objPartida); // fill the form 
                $this->fireEvents( $obj );

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

     /**
     * On muda campeonato
     */
     
    static function onMudaCampeonato( $param )
    {
        try
        {
            TTransaction::open('futapp');
            if (!empty($param['ref_campeonato']))
            {
                $criteria = TCriteria::create( ['ref_campeonato' => $param['ref_campeonato'] ] );
                
                // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                TDBCombo::reloadFromModel('form_Partida', 'ref_categoria', 'futapp', 'CategoriaCampeonato', 'id', '{nome} ({id})', 'id', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form_Partida', 'ref_categoria');
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
                TDBCombo::reloadFromModel('form_Partida', 'ref_equipe_local', 'futapp', 'Equipe', 'id', '{nome} ({id})', 'ref_categoria_campeonato', $criteria, TRUE);

                TDBCombo::reloadFromModel('form_Partida', 'ref_equipe_visitante', 'futapp', 'Equipe', 'id', '{nome} ({id})', 'ref_categoria_campeonato', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form_Partida', 'ref_equipe_local');
                TCombo::clearField('form_Partida', 'ref_equipe_visitante');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}