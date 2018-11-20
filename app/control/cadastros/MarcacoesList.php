<?php

class MarcacoesList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    private static $database = 'ponto';
    private static $activeRecord = 'Marcacoes';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Marcacoes';

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
        $this->form->setFormTitle('Marcações');

        $ref_pessoa = new TDBUniqueSearch('ref_pessoa', 'ponto', 'Pessoas', 'id', 'nome');
        $data_hora  = new TDateTime('data_hora');

        $data_hora->setMask('dd/mm/yyyy hh:ii');
        $data_hora->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_hora->setSize(150);

        $row1 = $this->form->addFields([new TLabel('Nome:', null, '14px', null)],[$ref_pessoa]);
        $row2 = $this->form->addFields([new TLabel('Data/Hora:', null, '14px', null)],[$data_hora]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction('Buscar', new TAction([$this, 'onSearch']), 'fa:search #ffffff');
        $btn_onsearch->addStyleClass('btn-primary'); 
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_pessoa = new TDataGridColumn('ref_pessoa', 'Pessoa', 'left');
        $column_data_hora = new TDataGridColumn('data_hora', 'Data/Hora', 'left'); 

        $formata_pessoa = function($value)
        {
           $objPessoa = new Pessoas($value);
              return $objPessoa->nome;    
        };

        $column_pessoa->setTransformer( $formata_pessoa );

        $column_data_hora->setTransformer(array($this, 'formatDate'));     

        $this->datagrid->addColumn($column_pessoa);
        $this->datagrid->addColumn($column_data_hora);
      
        if ( TSession::getValue('logged') )
        {
          $btn_onexportcsv = $this->form->addAction('Exportar como CSV', new TAction([$this, 'onExportCsv']), 'fa:file-text-o #000000');

          $btn_onshow = $this->form->addAction('Cadastrar', new TAction(['MarcacoesForm', 'onShow']), 'fa:plus #69aa46');
          $action_onShow = new TDataGridAction(array('MarcacoesForm', 'onEdit'));
          $action_onShow->setUseButton(false);
          $action_onShow->setButtonClass('btn btn-default btn-sm');
          $action_onShow->setLabel('Editar');
          $action_onShow->setImage('fa:pencil-square-o #478fca');
          $action_onShow->setField(self::$primaryKey);

          $this->datagrid->addAction($action_onShow);

          $action_onDelete = new TDataGridAction(array('MarcacoesList', 'onDelete'));
          $action_onDelete->setUseButton(false);
          $action_onDelete->setButtonClass('btn btn-default btn-sm');
          $action_onDelete->setLabel('Excluir');
          $action_onDelete->setImage('fa:trash-o #dd5a43');
          $action_onDelete->setField(self::$primaryKey);

          $this->datagrid->addAction($action_onDelete);
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
        $container->add(TBreadCrumb::create(['Cadastros','Marcações']));
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
                $object = new Marcacoes($key, FALSE); 

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

        if (isset($data->ref_pessoa) AND ( (is_scalar($data->ref_pessoa) AND $data->ref_pessoa !== '') OR (is_array($data->ref_pessoa) AND (!empty($data->ref_pessoa)) )) )
        {

            $filters[] = new TFilter('ref_pessoa', '=', "{$data->ref_pessoa}");// create the filter 
        }

        if (isset($data->data_hora) AND ( (is_scalar($data->data_hora) AND $data->data_hora !== '') OR (is_array($data->data_hora) AND (!empty($data->data_hora)) )) )
        {

            $dt = str_replace('/', '-', $data->data_hora);
            $dt_usa = date('Y-m-d', strtotime($dt));

            $filters[] = new TFilter('data_hora::date', '=', $dt_usa);// create the filter 
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

            // creates a repository for Punicoes
            $repository = new TRepository(self::$activeRecord);
            $limit = 20;
            // creates a criteria
            $criteria = new TCriteria;

            if (empty($param['order']))
            {
                $param['order'] = 'data_hora';    
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

}

