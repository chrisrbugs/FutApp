<?php

class ClassificacaoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    private static $database = 'futapp';
    private static $activeRecord = 'Classificacao';
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


        $ref_categoria = new TDBCombo('ref_categoria', 'futapp', 'Categorias', 'id', '{descricao}','id asc'  );

        $ref_categoria->setSize('70%');

        $row1 = $this->form->addFields([new TLabel('Categoria:', null, '14px', null)],[$ref_categoria]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search #ffffff');
        $btn_onsearch->addStyleClass('btn-primary'); 
      
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_posicao = new TDataGridColumn('posicao', 'Posicao', 'left');
        $column_time = new TDataGridColumn('time', 'Time', 'left');
        $column_pontos = new TDataGridColumn('pontos', 'Pontos', 'left');
        $column_jogos = new TDataGridColumn('jogos', 'Jogos', 'left');
        $column_vitorias = new TDataGridColumn('vitorias', 'Vitorias', 'left');
        $column_empates = new TDataGridColumn('empates', 'Empates', 'left');
        $column_derrotas = new TDataGridColumn('derrotas', 'Derrotas', 'left');
        $column_disciplina = new TDataGridColumn('disciplina', 'Disciplina', 'left');

        $this->datagrid->addColumn($column_posicao);
        $this->datagrid->addColumn($column_time);
        $this->datagrid->addColumn($column_pontos);
        $this->datagrid->addColumn($column_jogos);
        $this->datagrid->addColumn($column_vitorias);
        $this->datagrid->addColumn($column_empates);
        $this->datagrid->addColumn($column_derrotas);
        $this->datagrid->addColumn($column_disciplina);
      
        
        if ( TSession::getValue('logged') )
        {
          $btn_onexportcsv = $this->form->addAction('Exportar como CSV', new TAction([$this, 'onExportCsv']), 'fa:file-text-o #000000');

          $btn_onedit = $this->form->addAction('Cadastrar', new TAction(['ClassificacaoForm', 'onEdit']), 'fa:plus #69aa46');
          $action_onEdit = new TDataGridAction(array('ClassificacaoForm', 'onEdit'));
          $action_onEdit->setUseButton(false);
          $action_onEdit->setButtonClass('btn btn-default btn-sm');
          $action_onEdit->setLabel('Editar');
          $action_onEdit->setImage('fa:pencil-square-o #478fca');
          $action_onEdit->setField(self::$primaryKey);

          $this->datagrid->addAction($action_onEdit);

          $action_onEdit = new TDataGridAction(array('ClassificacaoForm', 'onDelete'));
          $action_onEdit->setUseButton(false);
          $action_onEdit->setButtonClass('btn btn-default btn-sm');
          $action_onEdit->setLabel('Excluir');
          $action_onEdit->setImage('fa:trash-o #dd5a43');
          $action_onEdit->setField(self::$primaryKey);

          $this->datagrid->addAction($action_onEdit);
        }

        

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup;
        $panel->add($this->datagrid);

        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TBreadCrumb::create(['Cadastros','Classificação']));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function onExportCsv($param = null) 
    {
        try
        {
            $this->onSearch();

            TTransaction::open(self::$database); // open a transaction
            $repository = new TRepository(self::$activeRecord); // creates a repository for Customer
            $criteria = new TCriteria; // creates a criteria

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            $records = $repository->load($criteria); // load the objects according to criteria
            if ($records)
            {
                $file = 'tmp/'.uniqid().'.csv';
                $handle = fopen($file, 'w');
                $columns = $this->datagrid->getColumns();

                $csvColumns = [];
                foreach($columns as $column)
                {
                    $csvColumns[] = $column->getLabel();
                }
                fputcsv($handle, $csvColumns, ';');

                foreach ($records as $record)
                {
                    $csvColumns = [];
                    foreach($columns as $column)
                    {
                        $name = $column->getName();
                        $csvColumns[] = $record->{$name};
                    }
                    fputcsv($handle, $csvColumns, ';');
                }
                fclose($handle);

                TPage::openFile($file);
            }
            else
            {
                new TMessage('info', _t('No records found'));       
            }

            TTransaction::close(); // close the transaction
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
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

        if (isset($data->ref_categoria) AND ( (is_scalar($data->ref_categoria) AND $data->ref_categoria !== '') OR (is_array($data->ref_categoria) AND (!empty($data->ref_categoria)) )) )
        {

            $filters[] = new TFilter('ref_categoria', '=', $data->ref_categoria);// create the filter 
        }

        $param = array();
        $param['offset']     = 0;
        $param['first_page'] = 1;

        // fill the form with data again
        $this->form->setData($data);

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

            // creates a repository for Classificacao
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

}

