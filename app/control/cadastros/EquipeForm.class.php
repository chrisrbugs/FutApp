<?php
/**
 * SaleForm Registration
 * @author  <your name here>
 */
class EquipeForm extends TPage
{
    protected $form; // form

    // trait with onSave, onClear, onEdit, ...
    use Adianti\Base\AdiantiStandardFormTrait;
    
    // trait with saveFile, saveFiles, ...
    use Adianti\Base\AdiantiFileSaveTrait;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();

        $usuario_logado = TSession::getValue('login');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Equipe');
        $this->form->setFormTitle('Inscrição');
        
        // master fields
        $id            = new TEntry('id');
        $nome          = new TEntry('nome');
        $ref_campeonato = new TDBCombo('ref_campeonato', 'futapp', 'Campeonato', 'id', '{nome}','id asc'  );
        $ref_categoria  = new TCombo('ref_categoria');
        $escudo        = new TFile('escudo');
        $usuario       = new THidden('usuario');
        
        // detail fields
        $id_atleta   = new THidden('id_atleta');
        $nome_atleta = new TEntry('nome_atleta');
        $cpf         = new TEntry('cpf');

        if(TSession::getValue('login') == 'J30EVENTOS' || TSession::getValue('login') == 'admin' || TSession::getValue('login') == 'Roni')
        {
            $ja_jogou = new TCombo('ja_jogou');

            $ja_jogou->addItems( [ 't' => 'Sim', 'f' => 'Não' ] );   
            $label_ja  = new TLabel('Já Jogou ?');         
        }
        else
        {
            $ja_jogou = new THidden('ja_jogou');
            $label_ja  = new TLabel(' ');
        }


        $ref_campeonato->setChangeAction(new TAction([$this,'onMudaCampeonato']));
        $usuario->setValue($usuario_logado);

        $ref_campeonato->setEditable(false);
        
        // adjust field properties
        $id->setEditable(false);
        $id_atleta->setEditable(false);
        $nome->setSize('100%');
        $nome_atleta->setSize('100%');
        $cpf->setSize('100%');
        $cpf->setMask('99999999999');

        // allow just these extensions
        $escudo->setAllowedExtensions( ['png', 'jpg', 'jpeg'] );
        
        // enable progress bar, preview, and file remove actions
        $escudo->enableFileHandling();
        
        // add validations
        $nome->addValidation('Nome', new TRequiredValidator);
        
        // add master form fields
        $this->form->addFields( [new TLabel('ID')], [$id]);
        $this->form->addFields( [new TLabel('Nome da Equipe')], [$nome ] );
        $this->form->addFields( [new TLabel('Campeonato')], [$ref_campeonato ] );
        $this->form->addFields( [new TLabel('Categoria')], [$ref_categoria ] );
        $this->form->addFields( [new TLabel('Escudo')], [$escudo] );
        $this->form->addFields( [new TLabel('')], [$usuario] );
        
        
        $add_atleta = TButton::create('add_atleta', [$this, 'onProductAdd'], 'Adicionar', 'fa:save');
        
        $Label_id = new TLabel('');
        $Label_nome = new TLabel('Nome Completo (*)');
        $label_cpf  = new TLabel('CPF (*)');

        $Label_nome->setFontColor('#FF0000');
        $label_cpf->setFontColor('#FF0000');
        
        $this->form->addContent( ['<h4>Atletas</h4><hr>'] );
        $this->form->addContent( ['Digite o Nome e CPF do atleta e clique em ADICIONAR para inserir o atleta na equipe.'] );
        $this->form->addContent( ['Para editar os dados do atleta clique no icone do lapis azul.'] );
        $this->form->addContent( ['Para remover o atleta clique no icone da lixeira vermelha.'] );
        $this->form->addFields( [$Label_id], [$id_atleta]);
        $this->form->addFields( [$Label_nome], [$nome_atleta]);
        $this->form->addFields( [$label_cpf], [$cpf]);
        $this->form->addFields( [$label_ja], [$ja_jogou]);
        $this->form->addFields( [], [$add_atleta] )->style = 'background: whitesmoke; padding: 5px; margin: 1px;';
        
        $this->product_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->product_list->setId('products_list');
        $this->product_list->style = "min-width: 700px; width:100%;margin-bottom: 10px";
        
        $col_id   = new TDataGridColumn( 'id_atleta', 'ID', 'left', '10%');
        $col_nome = new TDataGridColumn( 'nome_atleta', 'Nome', 'left', '45%');
        $col_cpf  = new TDataGridColumn( 'cpf', 'CPF', 'left', '45%');
        $col_jogou  = new TDataGridColumn( 'ja_jogou', 'Já Jogou?', 'left', '45%');
        
        $this->product_list->addColumn($col_id);
        $this->product_list->addColumn($col_nome);
        $this->product_list->addColumn($col_cpf);
        $this->product_list->addColumn($col_jogou);

        $col_jogou->setTransformer( function($ja_jogou) 
        {
            if ($ja_jogou == 't') 
            {
                return 'Sim';
            }
            else
            {
                return 'Não';
            }
        });
        
        // creates two datagrid actions
        $action1 = new TDataGridAction([$this, 'onEditItemProduto']);
        $action1->setLabel('Editar');
        $action1->setImage('fa:edit blue');
        $action1->setField('id_atleta');
        $action1->setField('nome_atleta');
        $action1->setField('cpf');
        $action1->setField('ja_jogou');
        
        $action2 = new TDataGridAction([$this, 'onDeleteItem']);
        $action2->setLabel('Excluir');
        $action2->setImage('fa:trash red');
        $action2->setField('id_atleta');
        // $action2->setField('nome_atleta');
        // $action2->setField('cpf');
        
        // add the actions to the datagrid
        $this->product_list->addAction($action1);
        $this->product_list->addAction($action2);
        
        $this->product_list->createModel();
        
        $panel = new TPanelGroup;
        $panel->add($this->product_list);
        $panel->getBody()->style = 'overflow-x:auto';
        $this->form->addContent( [$panel] );
                
        $this->form->addAction( 'Salvar',  new TAction([$this, 'onSave']), 'fa:save green');
        // $this->form->addAction( 'Clear', new TAction([$this, 'onClear']), 'fa:eraser red');
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }

    public function validaCpf($value, $parameters = NULL)
    {
        $label = 'CPF';
        // cpfs inválidos
        $nulos = array("12345678909","11111111111","22222222222","33333333333",
                       "44444444444","55555555555","66666666666","77777777777",
                       "88888888888","99999999999","00000000000");
        // Retira todos os caracteres que nao sejam 0-9
        $cpf = preg_replace("/[^0-9]/", "", $value);
        
        if (strlen($cpf) <> 11)
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF'. $cpf, $label));
        }
        
        // Retorna falso se houver letras no cpf
        if (!(preg_match("/[0-9]/",$cpf)))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF'. $cpf, $label));
        }

        // Retorna falso se o cpf for nulo
        if( in_array($cpf, $nulos) )
        {
            throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF'. $cpf, $label));
        }

        // Calcula o penúltimo dígito verificador
        $acum=0;
        for($i=0; $i<9; $i++)
        {
          $acum+= $cpf[$i]*(10-$i);
        }

        $x=$acum % 11;
        $acum = ($x>1) ? (11 - $x) : 0;
        // Retorna falso se o digito calculado eh diferente do passado na string
        if ($acum != $cpf[9])
        {
          throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF'. $cpf, $label));
        }
        // Calcula o último dígito verificador
        $acum=0;
        for ($i=0; $i<10; $i++)
        {
          $acum+= $cpf[$i]*(11-$i);
        }  

        $x=$acum % 11;
        $acum = ($x > 1) ? (11-$x) : 0;
        // Retorna falso se o digito calculado eh diferente do passado na string
        if ( $acum != $cpf[10])
        {
          throw new Exception(AdiantiCoreTranslator::translate('The field ^1 has not a valid CPF'. $cpf, $label));
        }  
    }
    
    /**
     * Pre load some data
     */
    public function onLoad($param)
    {
        $data = new stdClass;
        $data->nome   = $param['nome'];
        $this->form->setData($data);
    }    
    
    /**
     * Clear form
     * @param $param URL parameters
     */
    function onClear($param)
    {
        $this->form->clear();
        TSession::setValue('sale_items', array());
        TSession::setValue('atletas_excluidos', array());
        $this->onReload( $param );
    }
    
    /**
     * Add a product into item list
     * @param $param URL parameters
     */
    public function onProductAdd( $param )
    {
        try
        {
            TTransaction::open('futapp');
            $data = $this->form->getData();
            
            if( (! $data->nome_atleta) || (! $data->cpf))
                throw new Exception('O nome e o CPF são obrigatorios');
            
           
            
            $sale_items = TSession::getValue('sale_items');
            
            if ($data->id_atleta) 
            {
                $key = $data->id_atleta;
            }
            else
            {
                $key = uniqid();
            }

            $sale_items[ $key ] = ['id_atleta'   => $key,
                                   'nome_atleta' => $data->nome_atleta,
                                   'cpf'         => $data->cpf,
                                   'ja_jogou'    => $data->ja_jogou];
            
            TSession::setValue('sale_items', $sale_items);
            
            // clear product form fields after add
            $data->id_atleta = '';
            $data->nome_atleta = '';
            $data->cpf = '';
            $data->ja_jogou = '';
            TTransaction::close();
            $this->form->setData($data);

            TTransaction::open('futapp');
            $objCategoria =  new CategoriaCampeonato($data->ref_categoria);

            $obj = new stdClass;

            $obj->ref_campeonato  = $objCategoria->ref_campeonato;
            $obj->ref_categoria   = $objCategoria->id;
            $this->fireEvents( $obj );
            TTransaction::close();
                
            $this->onReload( $param ); // reload the sale items
        }
        catch (Exception $e)
        {
            $data = $this->form->getData();

            $this->form->setData( $data);
            TTransaction::open('futapp');
            $objCategoria =  new CategoriaCampeonato($data->ref_categoria);

            $obj = new stdClass;

            $obj->ref_campeonato  = $objCategoria->ref_campeonato;
            $obj->ref_categoria   = $objCategoria->id;
            $this->fireEvents( $obj );
            TTransaction::close();
            new TMessage('error', $e->getMessage());
        }
    }

       public function fireEvents( $object )
    {
        $obj = new stdClass;

        $obj->ref_campeonato     = $object->ref_campeonato;
        $obj->ref_categoria    = $object->ref_categoria;

        TForm::sendData('form_Equipe', $obj);
    }
    
    /**
     * Edit a product from item list
     * @param $param URL parameters
     */
    public static function onEditItemProduto( $param )
    {
        // read session items
        $sale_items = TSession::getValue('sale_items');
        
        // get the session item
        $sale_item = $sale_items[$param['id_atleta'] ];

        if (is_numeric($param['id_atleta'])) 
        {
            TTransaction::open('futapp');
            $atleta = new AtletaEquipe($param['id_atleta']);
            TTransaction::close();

            if (isset($atleta->ja_jogou) &&  $atleta->ja_jogou == 't' && (TSession::getValue('login') != 'J30EVENTOS' && TSession::getValue('login') != 'admin' && TSession::getValue('login') != 'Roni') )
            {
                new TMessage('error','atleta ja jogou');
                return;
            }
        }
        
        $data = new stdClass;
        $data->id_atleta   = $param['id_atleta'];
        $data->nome_atleta = $param['nome_atleta'];
        $data->ja_jogou    = $param['ja_jogou'];
        $data->cpf         = $param['cpf'];
        
        // fill product fields
        TForm::sendData( 'form_Equipe', $data );
    }
    
    /**
     * Delete a product from item list
     * @param $param URL parameters
     */
    public static function onDeleteItem( $param )
    {
        $data = new stdClass;
        $data->nome_atleta = '';
        $data->cpf = '';
        $data->id_atleta = '';
        $data->ja_jogou = '';
        
        // clear form data
        TForm::sendData('form_Equipe', $data );
        
        // read session items
        $sale_items = TSession::getValue('sale_items');
        $excluidos = array();
        
        $id_atleta = $param['id_atleta'];

        if (is_numeric($id_atleta)) 
        {
            TTransaction::open('futapp');
            $atleta = new AtletaEquipe($id_atleta);
            TTransaction::close();

            if (isset($atleta->ja_jogou) &&  $atleta->ja_jogou == 't')
            {
                new TMessage('error','atleta ja jogou');
                return;
            }
        }

        $excluidos[] = $id_atleta;

        unset($sale_items[ $id_atleta ] );
        
        // store the product list back to the session
        TSession::setValue('sale_items', $sale_items);

        TSession::setValue('atletas_excluidos', $excluidos);
        
        // delete item from screen
        TScript::create("ttable_remove_row_by_id('products_list', '{$id_atleta}')");
    }
    
    /**
     * Reload the item list
     * @param $param URL parameters
     */
    public function onReload($param)
    {
        // read session items
        $sale_items = TSession::getValue('sale_items');
        
        $this->product_list->clear(); // clear product list
        
        if ($sale_items)
        {
            // iterate session items
            foreach ($sale_items as $list_nome_atleta => $list_product)
            {
                // add into the details list
                $row = $this->product_list->addItem( (object) $list_product );
                
                // define an id for the table row
                $row->id = $list_product['id_atleta'];
            }
        }
        
        $this->loaded = TRUE;
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            TTransaction::open('futapp');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $object = new Equipe($key);
                $object->ref_categoria = $object->ref_categoria_campeonato;
                $atletas = $object->getAtletasEquipe();
                
                $items = array();
                foreach( $atletas as $atleta )
                {
                    $items[$atleta->id]['id_atleta']   = $atleta->id;
                    $items[$atleta->id]['nome_atleta'] = $atleta->nome;
                    $items[$atleta->id]['cpf']         = $atleta->cpf;
                    $items[$atleta->id]['ja_jogou']    = $atleta->ja_jogou;
                }
                TSession::setValue('sale_items', $items);
                
                $this->form->setData($object); // fill the form with the active record data

                $objCategoria =  new CategoriaCampeonato($object->ref_categoria);
                $obj = new stdClass;

                $obj->ref_campeonato  = $objCategoria->ref_campeonato;
                $obj->ref_categoria   = $object->ref_categoria;
                $this->fireEvents( $obj );

                $this->onReload( $param ); // reload sale items list
                TTransaction::close(); // close transaction
            }
            else
            {
                $this->form->clear();
                TSession::setValue('sale_items', null);
                $this->onReload( $param );
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Save the sale and the sale items
     */
    function onSave()
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('futapp');
            
            $data = $this->form->getData();

            $this->form->validate(); // form validation

  
            if ($data->id) 
            {
                $equipe = new Equipe($data->id); // create an empty object 

                $partidas = Partida::where('id', 'in', "(select id 
                                                        from partida 
                                                        where ref_equipe_local in (select id 
                                                                                     from equipe 
                                                                                    where id = $equipe->id ) 
                                                          OR ref_equipe_visitante in (select id 
                                                                                   from equipe
                                                                                   where id =  $equipe->id) )")->load();
                if ($equipe->ref_categoria_campeonato != $data->ref_categoria) 
                {
                    if (!is_null($partidas) && ! empty($partidas)) 
                    {
                        throw new Exception( "A EQUIPE JA TEM PARTIDAS CADASTRADAS" );
                    }


                    $objEquipe = Equipe::where('usuario','=',$data->usuario)
                                        ->where('ref_categoria_campeonato','=', $data->ref_categoria)->load();

                    if ((!is_null($objEquipe) && ! empty($objEquipe)) && (TSession::getValue('login') != 'J30EVENTOS' && TSession::getValue('login') != 'admin' && TSession::getValue('login') != 'Roni') ) 
                    {
                        throw new Exception( "Ja existe uma equipe sua na categoria!" );
                    }

                   
                }
            }
            else
            {
                $objEquipe = Equipe::where('usuario','=',$data->usuario)
                                        ->where('ref_categoria_campeonato','=', $data->ref_categoria)->load();

                if ((!is_null($objEquipe) && ! empty($objEquipe)) && (TSession::getValue('login') != 'J30EVENTOS' && TSession::getValue('login') != 'admin' && TSession::getValue('login') != 'Roni') ) 
                {
                    throw new Exception( "Ja existe uma equipe sua na categoria!" );
                }
                $equipe = new Equipe();
                $equipe->usuario = $data->usuario;
            }

            $equipe->nome = $data->nome;
            $equipe->ref_categoria_campeonato = $data->ref_categoria;

            $equipe->store(); // save the object

            $this->saveFile($equipe, $data, 'escudo', 'equipes'); 

            // get session items
            $atletas = TSession::getValue('sale_items');
            $excluidos = TSession::getValue('atletas_excluidos');

            if ($excluidos) 
            {
                foreach ($excluidos as $excluido) 
                {
                    if (is_numeric($excluido) ) 
                    {
                        $atletaExcluido = new AtletaEquipe($excluido);

                        $atletaExcluido->delete(); // save the object 
                    }
                }
                
                TSession::setValue('atletas_excluidos', null);
            }

            $items = array();
            if ($atletas) 
            {
                $numero_atletas = sizeof($atletas) - 2 ;

                $objCategoria = new CategoriaCampeonato($data->ref_categoria);

                if ($numero_atletas > $objCategoria->limite_atletas) 
                {
                   throw new Exception( "O limite de atletas é {$objCategoria->limite_atletas} e você cadastrou {$numero_atletas}" );
                }

                foreach ($atletas as $atleta) 
                {
                    if (is_numeric($atleta['id_atleta']) ) 
                    {
                        $atletaEquipe = new AtletaEquipe($atleta['id_atleta']);
                    }
                    else
                    {
                        $atletaEquipe = new AtletaEquipe();
                    }

                    if (!strstr($atleta['nome_atleta'],' ') && $atleta['cpf'] != '11111111111') 
                    {
                       throw new Exception( "Digite o nome completo" );
                    }

                    $this->validaCpf($atleta['cpf']);

                    //verificar se o atleta ja não esta em uma equipe
                    $isAtletaOutraEquipe = AtletaEquipe::isAtletaOutraEquipe($atleta['cpf'],$objCategoria->ref_campeonato,$equipe->id);
                    
                    if ($isAtletaOutraEquipe) 
                    {
                       throw new Exception( " O atleta ".$atleta['nome_atleta']." ja esta em outra equipe ");
                    }

                    if (is_null($atleta['ja_jogou']) || !isset($atleta['ja_jogou']) || $atleta['ja_jogou'] == '') 
                    {
                        $atleta['ja_jogou'] = 'f';
                    }

                    $atletaEquipe->nome       = $atleta['nome_atleta'];
                    $atletaEquipe->cpf        = $atleta['cpf'];
                    $atletaEquipe->ja_jogou   = $atleta['ja_jogou'];
                    $atletaEquipe->ref_equipe = $equipe->id;

                    $atletaEquipe->store(); // save the object

                    $items[$atletaEquipe->id]['id_atleta']   = $atletaEquipe->id;
                    $items[$atletaEquipe->id]['nome_atleta'] = $atletaEquipe->nome;
                    $items[$atletaEquipe->id]['ja_jogou']    = $atletaEquipe->ja_jogou;
                    $items[$atletaEquipe->id]['cpf']         = $atletaEquipe->cpf;
                }

                TSession::setValue('sale_items', $items);
            }
        

            TTransaction::close(); // close the transaction
            $this->form->setData($equipe); // keep form data

            TTransaction::open('futapp');
            $equipe = new Equipe($equipe->id);

            $objCategoria =  new CategoriaCampeonato($equipe->ref_categoria_campeonato);
            TTransaction::close();

            $obj = new stdClass;

            $obj->ref_campeonato  = $objCategoria->ref_campeonato;
            $obj->ref_categoria   = $objCategoria->id;

            $this->fireEvents( $obj );
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            $data = $this->form->getData();
            $this->form->setData( $this->form->getData() ); // keep form data
            $objCategoria =  new CategoriaCampeonato($data->ref_categoria);

            $obj = new stdClass;

            $obj->ref_campeonato  = $objCategoria->ref_campeonato;
            $obj->ref_categoria   = $objCategoria->id;
            $this->fireEvents( $obj );
            TTransaction::rollback();
        }
    }
    
    /**
     * Show the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR $_GET['method'] !== 'onReload' OR $_GET['method'] !== 'onEdit') )
        {
            $this->onReload( func_get_arg(0) );
        }
        parent::show();
    }


    /**
     * load the previous data
     */
    public function onLoadFromForm1($data)
    {

        if(isset($data['id']))
        {
            TTransaction::open('futapp');
            
            $equipe = new Equipe($data['id']);

            $data['ref_categoria'] = $equipe->ref_categoria_campeonato;

            $param = array();
            $param['key'] = $equipe->id;

            $this->onEdit($param);
            
            TTransaction::close();
        }
        else
        {    
            TTransaction::open('futapp');
            
            $usuario_logado = TSession::getValue('login');

            $equipe = Equipe::where('usuario', '=', $usuario_logado)
                            ->where('ref_categoria_campeonato', '=', $data['ref_categoria'])->load();
            
            TTransaction::close();
            if ($equipe && (TSession::getValue('login') != 'J30EVENTOS' && TSession::getValue('login') != 'admin' && TSession::getValue('login') != 'Roni')) 
            {
                $equipe = $equipe[0];

                $param = array();
                $param['key'] = $equipe->id;

                $this->onEdit($param);
            }
        }
        
        $obj = new StdClass;
        $obj->ref_categoria = $data['ref_categoria'];


        $this->form->setData($obj);
        TTransaction::open('futapp');
        $objCategoria =  new CategoriaCampeonato($obj->ref_categoria);

        $obj = new stdClass;

        $obj->ref_campeonato  = $objCategoria->ref_campeonato;
        $obj->ref_categoria   = $objCategoria->id;
        $this->fireEvents( $obj );
        TTransaction::close();
    }
    
    /**
     * Load the previous form
     */
    public function onBackForm()
    {
        // Load another page
        AdiantiCoreApplication::loadPage('MultiStepMultiFormView', 'onLoadFromSession');
    }
    
    /**
     * confirmation screen
     */
    public function onConfirm()
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            $this->form->setData($data);
            
            $form1_data = TSession::getValue('form_step1_data');
            $data->password = $form1_data->password;
            new TMessage('info', str_replace(',', '<br>', json_encode($data)));
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
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
                TDBCombo::reloadFromModel('form_Equipe', 'ref_categoria', 'futapp', 'CategoriaCampeonato', 'id', '{nome} ({id})', 'id', $criteria, false);
            }
            else
            {
                TCombo::clearField('form_Equipe', 'ref_categoria');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
