<?php

class PartidaPublicList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    private static $database = 'futapp';
    private static $activeRecord = 'Partida';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Partida';

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
        $this->form->setFormTitle('Partidas');


        $ref_campeonato    = new TDBCombo('ref_campeonato', 'futapp', 'Campeonato', 'id', '{nome}','id asc'  );
        $ref_categoria     = new TCombo('ref_categoria');
        $ref_equipe        = new TCombo('ref_equipe');
        $dt_partida        = new TDateTime('dt_partida');

        $ref_campeonato->setChangeAction(new TAction([$this,'onMudaCampeonato']));
        $ref_categoria->setChangeAction(new TAction([$this,'onMudaCategoria']));

        $dt_partida->setMask('dd/mm/yyyy hh:ii');
        $dt_partida->setDatabaseMask('yyyy-mm-dd hh:ii');
        $dt_partida->setSize(150);
        $ref_categoria->setSize('70%');

        $row0 = $this->form->addFields([new TLabel('Campeonato:', null, '14px', null)],[$ref_campeonato]);
        $row1 = $this->form->addFields([new TLabel('Categoria:', null, '14px', null)],[$ref_categoria]);
        $row1 = $this->form->addFields([new TLabel('Equipe:', null, '14px', null)],[$ref_equipe]);
        $row2 = $this->form->addFields([new TLabel('Data jogo:', null, '14px', null)],[$dt_partida]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search #ffffff');
        $btn_onsearch->addStyleClass('btn-primary'); 
      
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_time_local = new TDataGridColumn('ref_equipe_local', 'Time local', 'left');
        
        $column_resultado = new TDataGridColumn('id', 'Resultado', 'left');

        $column_time_visitante = new TDataGridColumn('ref_equipe_visitante', 'Time visitante', 'left');
        $column_dt_partida = new TDataGridColumn('dt_partida', 'Dt jogo', 'left');

        $column_dt_partida->setTransformer(array($this, 'formatDate'));
        $column_resultado->setTransformer(array($this, 'formatResultado'));
        $column_time_visitante->setTransformer(array($this, 'formatNome'));
        $column_time_local->setTransformer(array($this, 'formatNome'));

        $this->datagrid->addColumn($column_time_local);
        $this->datagrid->addColumn($column_resultado);
        $this->datagrid->addColumn($column_time_visitante);
        $this->datagrid->addColumn($column_dt_partida);

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
        // $container->add(TBreadCrumb::create(['Cadastros','Partidas']));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

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

            $filters[] = new TFilter('id', 'in', "(select id from partida where ref_equipe_local in (select id from equipe where ref_categoria_campeonato in (select id from categoria_campeonato where ref_campeonato = {$data->ref_campeonato})) OR ref_equipe_visitante in (select id from equipe where ref_categoria_campeonato in (select id from categoria_campeonato where ref_campeonato = {$data->ref_campeonato})) )");
        }

        if (isset($data->ref_categoria) AND ( (is_scalar($data->ref_categoria) AND $data->ref_categoria !== '') OR (is_array($data->ref_categoria) AND (!empty($data->ref_categoria)) )) )
        {

             $filters[] = new TFilter('id', 'in', "(select id from partida where ref_equipe_local in (select id from equipe where ref_categoria_campeonato = $data->ref_categoria ) OR ref_equipe_visitante in (select id from equipe where ref_categoria_campeonato =  $data->ref_categoria) )");
        }

        if (isset($data->ref_equipe) AND ( (is_scalar($data->ref_equipe) AND $data->ref_equipe !== '') OR (is_array($data->ref_equipe) AND (!empty($data->ref_equipe)) )) )
        {

             $filters[] = new TFilter('id', 'in', "(select id from partida where ref_equipe_local in (select id from equipe where id = $data->ref_equipe ) OR ref_equipe_visitante in (select id from equipe where id =  $data->ref_equipe) )");
        }


        if (isset($data->dt_partida) AND ( (is_scalar($data->dt_partida) AND $data->dt_partida !== '') OR (is_array($data->dt_partida) AND (!empty($data->dt_partida)) )) )
        {

            $dt = str_replace('/', '-', $data->dt_partida);
            $dt_usa = date('Y-m-d', strtotime($dt));

            $filters[] = new TFilter('dt_partida::date', '=', $dt_usa);// create the filter 
        }

        $param = array();
        $param['offset']     = 0;
        $param['first_page'] = 1;

        // fill the form with data again
        $this->form->setData($data);

        $obj = new stdClass;
        $obj->ref_campeonato = $data->ref_campeonato;
        $obj->ref_categoria  = $data->ref_categoria;
        $obj->ref_equipe     = $data->ref_equipe;

        $this->fireEvents( $obj );

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload($param);
    }

        /**
     * Fire form events
     * @param $param Request
     */
    public function fireEvents( $object )
    {
        $obj = new stdClass;

        $obj->ref_campeonato   = $object->ref_campeonato;
        $obj->ref_categoria    = $object->ref_categoria;
        $obj->ref_equipe       = $object->ref_equipe;
        TForm::sendData('formList_Partida', $obj);
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

            // creates a repository for Partidas
            $repository = new TRepository(self::$activeRecord);
            $limit = 20;
            // creates a criteria
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'dt_partida';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
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
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public function formatDate($date)
    {
        return date('d/m/Y G:i:s', strtotime($date));
    }

    public function formatResultado($id)
    {
        $partida = new Partida($id);

        return $partida->numero_gols_local.' x '.$partida->numero_gols_visitante;

    }

    public function formatNome($id)
    {
        $equipe = new Equipe($id);

        return $equipe->nome;

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
                TDBCombo::reloadFromModel('formList_Partida', 'ref_categoria', 'futapp', 'CategoriaCampeonato', 'id', '{nome} ({id})', 'id', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('formList_Partida', 'ref_categoria');
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
                TDBCombo::reloadFromModel('formList_Partida', 'ref_equipe', 'futapp', 'Equipe', 'id', '{nome} ({id})', 'ref_categoria_campeonato', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('formList_Partida', 'ref_equipe');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}