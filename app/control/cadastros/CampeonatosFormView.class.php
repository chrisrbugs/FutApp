<?php
/**
 * InscricaoFormView
 */
class CampeonatosFormView extends TPage
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
        $this->form = new BootstrapFormBuilder('form_campeonatos');
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
        $this->form->addAction('JOGOS', new TAction(array('PartidaPublicList', 'onSearch')), 'fa:chevron-circle-right green');
        $this->form->addAction('GOLEADORES', new TAction(array('GoleadorPublicList', 'onSearch')), 'fa:chevron-circle-right green');
        $this->form->addAction('PUNIÇÕES', new TAction(array('PunicaoPublicList', 'onSearch')), 'fa:chevron-circle-right green');
        $this->form->addAction('CLASSIFICAÇÃO', new TAction(array('ClassificacaoEquipePublicList', 'onSearch')), 'fa:chevron-circle-right green');
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
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

            // AdiantiCoreApplication::loadPage('EquipeForm', 'onLoadFromForm1', (array) $data);
            
            
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

                    TCombo::reload('form_campeonatos', 'ref_categoria', $options);
                   
                    TTransaction::close();
                }
                else
                {
                    $options = array();
                    TCombo::reload('form_campeonatos', 'ref_categoria', $options);
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
            TCombo::reload('form_campeonatos', 'ref_categoria', $options);
        }
    }
}