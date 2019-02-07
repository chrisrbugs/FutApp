<?php

class AdminEquipeList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    private static $database = 'futapp';
    private static $activeRecord = 'Equipe';
    private static $primaryKey = 'id';
    private static $formName = 'formList_AdminEquipe';

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
        $this->form->setFormTitle('Equipes');        

        //define the form title
        $this->form->setFormTitle('Equipes');
        $ref_campeonato = new TEntry('ref_campeonato');
        $ref_categoria  = new TEntry('ref_categoria');


        // $row1 = $this->form->addFields([new TLabel('Nome Equipe:', null, '14px', null)],[$nome]);
        $row2 = $this->form->addFields([new TLabel('Ndsde:', null, '14px', null)],[$ref_campeonato]);
        $row3 = $this->form->addFields([new TLabel('Nosdas:', null, '14px', null)],[$ref_categoria]);
        // keep the form filled during navigation with session data
        // $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // $btn_onsearch = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search #ffffff');
        // $btn_onsearch->addStyleClass('btn-primary'); 
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_nome      = new TDataGridColumn('nome', 'Nome', 'left');
        $column_usuario   = new TDataGridColumn('usuario', 'Usuario', 'left');

        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_usuario);

        $action_onShow = new TDataGridAction(array('EquipeForm', 'onLoadFromForm1'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel('Editar');
        $action_onShow->setImage('fa:pencil-square-o #478fca');
        $action_onShow->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onShow);

        //$btn_onexportcsv = $this->form->addAction('Exportar como CSV', new TAction([$this, 'onExportCsv']), 'fa:file-text-o #000000');

        $btn_onshow = $this->form->addAction('Cadastrar', new TAction(['EquipeForm', 'onLoadFromForm1']), 'fa:plus #69aa46');
        // $action_onShow = new TDataGridAction(array('CampeonatoForm', 'onEdit'));
        // $action_onShow->setUseButton(false);
        // $action_onShow->setButtonClass('btn btn-default btn-sm');
        // $action_onShow->setLabel('Editar');
        // $action_onShow->setImage('fa:pencil-square-o #478fca');
        // $action_onShow->setField(self::$primaryKey);

        // $this->datagrid->addAction($action_onShow);

        // $action_onDelete = new TDataGridAction(array('CampeonatoList', 'onDelete'));
        // $action_onDelete->setUseButton(false);
        // $action_onDelete->setButtonClass('btn btn-default btn-sm');
        // $action_onDelete->setLabel('Excluir');
        // $action_onDelete->setImage('fa:trash-o #dd5a43');
        // $action_onDelete->setField(self::$primaryKey);

        // $this->datagrid->addAction($action_onDelete);        
      
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
        $container->add(TBreadCrumb::create(['Cadastros','Campeonatos']));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

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

            // creates a repository for Campeonatos
            $repository = new TRepository(self::$activeRecord);
            $limit = 20;
            // creates a criteria
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
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
          
            if ($param['ref_categoria']) 
            {
              $criteria->add(new TFilter('ref_categoria_campeonato','=', $param['ref_categoria']));
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
            
            $objCategoria =  new CategoriaCampeonato($param['ref_categoria']);

            $obj = new stdClass;

            $obj->ref_campeonato  = $objCategoria->ref_campeonato;
            $obj->ref_categoria   = $objCategoria->id;
            $this->form->setData($obj);
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

}

