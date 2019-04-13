<?php
/**
 * PublicView
 *
 * @version    1.0
 * @package    control
 * @subpackage public
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PublicView extends TPage
{
    public function __construct()
    {
        parent::__construct();
        
        $html = new THtmlRenderer('app/resources/public.html');

        // replace the main section variables
        $html->enableSection('main', array());

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        // $column_time_local = new TDataGridColumn('ref_equipe_local', 'Time local', 'right');
        $column_img_local = new TDataGridColumn('ref_equipe_local', 'Time local', 'right');
        $column_resultado = new TDataGridColumn('id', ' ', 'center');
        $column_img_visitante = new TDataGridColumn('ref_equipe_visitante', 'Time Visitante', 'left');
        $column_categoria = new TDataGridColumn('ref_equipe_local', 'Campeonato(Categoria)', 'left');
        $column_dt_partida = new TDataGridColumn('dt_partida', 'Data jogo', 'left');
        $column_etapa = new TDataGridColumn('etapa', 'Etapa', 'left');


        $column_resultado->setTransformer(array($this, 'formatResultado'));
        $column_img_local->setTransformer(array($this, 'formatLogoLocal'));
        $column_img_visitante->setTransformer(array($this, 'formatLogoVisitante'));
        $column_categoria->setTransformer(array($this, 'formatCampeonato'));
        $column_dt_partida->setTransformer(array($this, 'formatDate'));


        // $this->datagrid->addColumn($column_time_local);
        $this->datagrid->addColumn($column_img_local);
        $this->datagrid->addColumn($column_resultado);
        $this->datagrid->addColumn($column_img_visitante);
        $this->datagrid->addColumn($column_categoria);
        $this->datagrid->addColumn($column_etapa);
        $this->datagrid->addColumn($column_dt_partida);

        $this->datagrid->createModel();
        
        $panel = new TPanelGroup('Jogos de hoje!');
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        $container = new TVBox;
        // $container->style = 'width: 100%';
        // $container->add(TBreadCrumb::create(['Cadastros','Partidas']));
        // $container->add($this->form);
        // $container->add($panel);
        $container->add($html);
        
        
        // add the template to the page
        parent::add( $panel );
        parent::add( $container );
    }

        public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'futapp'
            TTransaction::open('futapp');

            // creates a repository for Partidas
            $repository = new TRepository('Partida');
            $limit = 20;
            // creates a criteria
            $criteria = new TCriteria;

            $param['order'] = 'dt_partida';                     

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            $hoje = date('Y-m-d');
            $criteria->add(new TFilter('dt_partida::date','=', $hoje));     
                

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

            // $this->pageNavigation->setCount($count); // count of records
            // $this->pageNavigation->setProperties($param); // order, page
            // $this->pageNavigation->setLimit($limit); // limit

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

    public function formatNome($id)
    {
        $equipe = new Equipe($id);

        return $equipe->nome;

    }

    public function formatResultado($id)
    {
        $objPartida = new Partida($id);

        return $objPartida->numero_gols_local." X ".$objPartida->numero_gols_visitante;

    }



    public function formatCampeonato($id)
    {
        TTransaction::open('futapp');
        $Equipe = new Equipe($id);
        $Categoria = new CategoriaCampeonato($Equipe->ref_categoria_campeonato);
        $Campeonato = new Campeonato($Categoria->ref_campeonato);

        TTransaction::close();

        return $Campeonato->nome.' ('.$Categoria->nome.')';
    }

    public function formatLogoLocal($id)
    {
        TTransaction::open('futapp');
        $Equipe = Equipe::where('id', ' = ', $id)->load();
        TTransaction::close();
        $Equipe = $Equipe[0];

        if ($Equipe->escudo) 
        {
            $image = new TImage($Equipe->escudo);
        }
        else
        {
            $image = new TImage('equipes/padrao.png');
        }
        $image->style = 'max-width: 50px';
        
        return $Equipe->nome.' '. $image;
    }

    public function formatLogoVisitante($id)
    {
        TTransaction::open('futapp');
        $Equipe = Equipe::where('id', ' = ', $id)->load();
        TTransaction::close();
        $Equipe = $Equipe[0];
        if ($Equipe->escudo) 
        {
            $image = new TImage($Equipe->escudo);
        }
        else
        {
            $image = new TImage('equipes/padrao.png');
        }
        $image->style = 'max-width: 50px';

        return $image.' '.$Equipe->nome;         
    }

    public function formatDate($date)
    {
        return date('d/m/Y G:i:s', strtotime($date));
    }
}
