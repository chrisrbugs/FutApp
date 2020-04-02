<?php

class ClassificacaoEquipeList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    private static $database = 'futapp';
    private static $activeRecord = 'ClassificacaoEquipe';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Classificacao';

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle('Classificação');


        $ref_campeonato = new TDBCombo('ref_campeonato', 'futapp', 'Campeonato', 'id', '{nome}','id asc'  );
        $ref_categoria  = new TCombo('ref_categoria');
        $ref_fase       = new TCombo('ref_fase');

        $dt_atualizacao = new TEntry('dt_atualizacao');
        $dt_atualizacao->setMask('dd/mm/yyyy');
        $ref_campeonato->setChangeAction(new TAction([$this,'onMudaCampeonato']));
        $ref_categoria->setChangeAction(new TAction([$this,'onMudaCategoria']));

        $row1 = $this->form->addFields([new TLabel('Campeonato:', null, '14px', null)],[$ref_campeonato]);
        $row2 = $this->form->addFields([new TLabel('Categoria:', null, '14px', null)],[$ref_categoria]);
        $row3 = $this->form->addFields([new TLabel('Fase:', null, '14px', null)],[$ref_fase]);


        $row4 = $this->form->addFields([new TLabel('Atualizado em:', null, '14px', null)],[$dt_atualizacao]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        $this->fireEvents(TSession::getValue(__CLASS__.'_filter_data'));
        $btn_onsearch = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search #ffffff');
        $btn_onsearch->addStyleClass('btn-primary'); 
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', 'id', 'left');
        $column_posicao = new TDataGridColumn('posicao', 'Posicao', 'left');
        $column_time = new TDataGridColumn('ref_equipe', 'Time', 'left');
        $column_pontos = new TDataGridColumn('pontos', 'Pontos', 'left');
        $column_jogos = new TDataGridColumn('jogos', 'Jogos', 'left');
        $column_vitorias = new TDataGridColumn('vitorias', 'Vitorias', 'left');
        $column_empates = new TDataGridColumn('empates', 'Empates', 'left');
        $column_derrotas = new TDataGridColumn('derrotas', 'Derrotas', 'left');
        $column_gp = new TDataGridColumn('gols_pro', 'Gols Pro', 'left');
        $column_gc = new TDataGridColumn('gols_contra', 'Gols Contra', 'left');
        $column_sg = new TDataGridColumn('saldo_gols', 'Saldo de Gols', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');
        $column_fase = new TDataGridColumn('ref_fase', 'Fase', 'left');
        $column_obs = new TDataGridColumn('obs', 'Obs', 'left');
        $column_eliminado = new TDataGridColumn('fl_eliminado', 'Eliminado', 'left');

        $formata_equipe = function($value)
        {
           $objEquipe = new Equipe($value);
	          return $objEquipe->nome;    
        };

        $formata_fase = function($value)
        {
           $objFase = new FaseCategoria($value);
              return $objFase->descricao;    
        };

        $column_time->setTransformer( $formata_equipe );
        $column_fase->setTransformer( $formata_fase );
        $column_eliminado->setTransformer(array($this, 'formataLinha'));

        $this->datagrid->addColumn($column_posicao);
        $this->datagrid->addColumn($column_time);
        $this->datagrid->addColumn($column_pontos);
        $this->datagrid->addColumn($column_jogos);
        $this->datagrid->addColumn($column_vitorias);
        $this->datagrid->addColumn($column_empates);
        $this->datagrid->addColumn($column_derrotas);
        $this->datagrid->addColumn($column_gp);
        $this->datagrid->addColumn($column_gc);
        $this->datagrid->addColumn($column_sg);
        $this->datagrid->addColumn($column_disciplina);
        $this->datagrid->addColumn($column_obs);
        $this->datagrid->addColumn($column_eliminado);

        // creates the edit action
        $editaction = new TDataGridAction(array($this, 'onEdit'));
        $editaction->setField('id');
        $column_posicao->setEditAction($editaction);
        $column_pontos->setEditAction($editaction);
        $column_jogos->setEditAction($editaction);
        $column_vitorias->setEditAction($editaction);
        $column_empates->setEditAction($editaction);
        $column_derrotas->setEditAction($editaction);
        $column_gp->setEditAction($editaction);
        $column_gc->setEditAction($editaction);
        $column_sg->setEditAction($editaction);
        $column_disciplina->setEditAction($editaction);
        $column_obs->setEditAction($editaction);
        
      
        if ( TSession::getValue('logged') )
        {
          // $btn_onexportcsv = $this->form->addAction('Exportar como CSV', new TAction([$this, 'onExportCsv']), 'fa:file-text-o #000000');

          $btn_onshow = $this->form->addAction('Cadastrar', new TAction(['ClassificacaoEquipeForm', 'onShow']), 'fa:plus #69aa46');
          $action_onShow = new TDataGridAction(array('ClassificacaoEquipeForm', 'onEdit'));
          $action_onShow->setUseButton(false);
          $action_onShow->setButtonClass('btn btn-default btn-sm');
          $action_onShow->setLabel('Editar');
          $action_onShow->setImage('fa:pencil-square-o #478fca');
          $action_onShow->setField(self::$primaryKey);

          $this->datagrid->addAction($action_onShow);

          $action_onDelete = new TDataGridAction(array('ClassificacaoEquipeList', 'onDelete'));
          $action_onDelete->setUseButton(false);
          $action_onDelete->setButtonClass('btn btn-default btn-sm');
          $action_onDelete->setLabel('Excluir');
          $action_onDelete->setImage('fa:trash-o #dd5a43');
          $action_onDelete->setField(self::$primaryKey);

          $this->datagrid->addAction($action_onDelete);

          $this->form->addAction('Salvar Data de Atualização', new TAction([$this, 'onSalvaAtualizacao']), 'fa:floppy-o #00000');
        

        }
      
        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup;
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TBreadCrumb::create(['Cadastros','Classificação']));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function formataLinha($value, $object, $row)
    {

        if ($value == 't') 
        {
            $row->style = "background: #F84040";
            return 'Sim';
            
        }
        else
        {
            return 'Não';
        }   
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
                $object = new ClassificacaoEquipe($key, FALSE); 

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


    function onSalvaAtualizacao($param)
    {
        try
        {
            // get the parameter $key
            // $field = $param['field'];
            $ref_categoria  = $param['ref_categoria'];
            $dt_atualizacao = $param['dt_atualizacao'];

            if (!$ref_categoria) 
            {
               new TMessage('error', 'selecione a categoria');
               return false;
            }
            
            // open a transaction with database 'samples'
            TTransaction::open('futapp');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('ref_categoria_campeonato', '=', $ref_categoria));
        
            $atualizacao = AtualizacaoClassificacao::getObjects($criteria)[0];

            if ($atualizacao) 
            {
                $classificacao = new AtualizacaoClassificacao($atualizacao->id);
            }
            else
            {
                $classificacao = new AtualizacaoClassificacao();
            }
            
            // instantiates object Customer
            $classificacao->ref_categoria_campeonato = $ref_categoria;
            $classificacao->dt_atualizacao           = $dt_atualizacao;
            $classificacao->store();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload($param);
            // shows the success message
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function formatEliminidao($stock, $object, $row)
    {
        
    }

    function onEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            // open a transaction with database 'samples'
            TTransaction::open('futapp');
            
            // instantiates object Customer
            $classificacao = new ClassificacaoEquipe($key);
            $classificacao->{$field} = $value;
            $classificacao->store();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload($param);
            // shows the success message
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->ref_campeonato) AND ( (is_scalar($data->ref_campeonato) AND $data->ref_campeonato !== '') OR (is_array($data->ref_campeonato) AND (!empty($data->ref_campeonato)) )) )
        {
            $filters[] = new TFilter('ref_equipe', 'in', "(select id 
                                                                from equipe 
                                                               where ref_categoria_campeonato in (select id 
                                                                                                    from categoria_campeonato
                                                                                                   where ref_campeonato =  {$data->ref_campeonato}))");// create the filter 
        }

        if (isset($data->ref_categoria) AND ( (is_scalar($data->ref_categoria) AND $data->ref_categoria !== '') OR (is_array($data->ref_categoria) AND (!empty($data->ref_categoria)) )) )
        {
            $filters[] = new TFilter('ref_equipe', 'in', "(select id 
                                                                from equipe 
                                                               where ref_categoria_campeonato = {$data->ref_categoria})");// create the filter 
        }

        if (isset($data->ref_fase) AND ( (is_scalar($data->ref_fase) AND $data->ref_fase !== '') OR (is_array($data->ref_fase) AND (!empty($data->ref_fase)) )) )
        {
            $filters[] = new TFilter('ref_fase', '=', $data->ref_fase);// create the filter 
        }


        $param = array();
        $param['offset']     = 0;
        $param['first_page'] = 1;

        // fill the form with data again
        $this->form->setData($data);

        TTransaction::open(self::$database);
        $this->fireEvents($data);
        TTransaction::close();
        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload($param);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'futapp'
		
	       TTransaction::open(self::$database);

            // creates a repository for Punicoes
            $repository = new TRepository(self::$activeRecord);
            $limit = 20;
            // creates a criteria
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'posicao';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'asc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }
          
  	    // load the objects according to criteria	    
            $objects = $repository->load($criteria, FALSE);
	    
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid

                    $this->datagrid->addItem($object);

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        // if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        // {
        //     if (func_num_args() > 0)
        //     {
        //         $this->onReload( func_get_arg(0) );
        //     }
        //     else
        //     {
        //         $this->onReload();
        //     }
        // }
        parent::show();
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
                TDBCombo::reloadFromModel('formList_Classificacao', 'ref_categoria', 'futapp', 'CategoriaCampeonato', 'id', '{nome} ({id})', 'id', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('formList_Classificacao', 'ref_categoria');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

        static function onMudaCategoria( $param )
    {
        try
        {
            TTransaction::open('futapp');
            if (!empty($param['ref_categoria']))
            {
                $criteria = TCriteria::create( ['ref_categoria_campeonato' => $param['ref_categoria'] ] );
                
                // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                TDBCombo::reloadFromModel('formList_Classificacao', 'ref_fase', 'futapp', 'FaseCategoria', 'id', '{descricao} ({id})', 'ref_categoria_campeonato', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('formList_Classificacao', 'ref_fase');
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
        $obj = new stdClass;
        if (isset($object->ref_campeonato)) 
        {
            $obj->ref_campeonato = $object->ref_campeonato;
        }
        
        if (isset($object->ref_categoria)) 
        {
            $obj->ref_categoria  = $object->ref_categoria;

            $criteria = new TCriteria;
            $criteria->add(new TFilter('ref_categoria_campeonato', '=', $obj->ref_categoria));
            TTransaction::open('futapp');
            $atualizacao = AtualizacaoClassificacao::getObjects($criteria);

            TTransaction::close();
            if ($atualizacao) 
            {
                $atualizacao = $atualizacao[0];
               $obj->dt_atualizacao  =  TDate::date2br($atualizacao->dt_atualizacao);
            }
        }

        if (isset($object->ref_fase)) 
        {
            $obj->ref_fase  = $object->ref_fase;
        }

        TForm::sendData('formList_Classificacao', $obj);
    }

}

