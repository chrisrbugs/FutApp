<?php
/**
 * InscricaoFormView
 */
class InscricaoFormView extends TPage
{
    protected $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        TSession::setValue('sale_items', array());
        TSession::setValue('atletas_excluidos', array());
        
        $this->form = new BootstrapFormBuilder('form_inscricao');
        $this->form->setFormTitle('Escolha o campeonato e a categoria');
        $this->form->setColumnClasses(2, ['col-sm-3', 'col-sm-9']);
        
        // create the form fields
        $ref_campeonato = new TDBCombo('ref_campeonato', 'futapp', 'Campeonato', 'id', '{nome}','id asc'  );
        $ref_categoria  = new TCombo('ref_categoria');

        $this->form->addFields(['Campeonato'], [$ref_campeonato] );
        $this->form->addFields(['Categoria'], [$ref_categoria] );

        // validations
        $ref_campeonato->addValidation('Campeonato', new TRequiredValidator);
        $ref_categoria->addValidation('Categoria', new TRequiredValidator);

        $ref_campeonato->setChangeAction(new TAction([$this,'onMudaCampeonato']));

        // add a form action
        $this->form->addAction('Next', new TAction(array($this, 'onNextForm')), 'fa:chevron-circle-right green');
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        
        // add the form to the page
        parent::add($vbox);
    }
    
    /**
     * Load form from session
     */
    public function onLoadFromSession()
    {
        $data = TSession::getValue('form_step1_data');
        $this->form->setData($data);
    }
    
    /**
     * onNextForm
     */
    public function onNextForm()
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            
            // store data in the session
            TSession::setValue('form_step1_data', $data);

            // Load another page
            if (TSession::getValue('login') == 'J30EVENTOS' || TSession::getValue('login') == 'admin' || TSession::getValue('login') == 'Roni')
            {
                AdiantiCoreApplication::loadPage('AdminEquipeList', 'onShow', (array) $data);
            }
            else
            {
                AdiantiCoreApplication::loadPage('EquipeForm', 'onLoadFromForm1', (array) $data);
            }
            
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * On muda campeonato
     */
    static function onMudaCampeonato( $params )
    {
        if( !empty($params['ref_campeonato']) )
        {
            try
            {
                TTransaction::open('futapp');
                
                $categoriasCampeonato = CategoriaCampeonato::where('ref_campeonato', ' = ', $params['ref_campeonato'])->load();
                
                $options = array();
                if ($categoriasCampeonato) 
                {
                    foreach ($categoriasCampeonato as $categoriaCampeonato) 
                    {
                        $options[$categoriaCampeonato->id] = $categoriaCampeonato->nome;
                        
                    }

                    TCombo::reload('form_inscricao', 'ref_categoria', $options);
                   
                    TTransaction::close();
                }
                else
                {
                    $options = array();
                    TCombo::reload('form_inscricao', 'ref_categoria', $options);
                }
            }
            catch (Exception $e) // in case of exception
            {
                new TMessage('error', $e->getMessage());
                TTransaction::rollback();
            }
        }
        else
        {
            $options = array();
            TCombo::reload('form_inscricao', 'ref_categoria', $options);
        }
    }
}