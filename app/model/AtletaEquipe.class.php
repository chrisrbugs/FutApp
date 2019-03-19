<?php
class AtletaEquipe extends TRecord
{
    const TABLENAME  = 'atleta_equipe';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}
    
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cpf');
        parent::addAttribute('ref_equipe');
        parent::addAttribute('ja_jogou');
    }

    /**
     * Method getAtletasEquipe
     verificar se o atleta ja não esta cadastrado em outra equipe
     */
    public static function isAtletaOutraEquipe($cpf, $ref_campeonato, $ref_equipe)
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cpf', '=', $cpf));
        $criteria->add(new TFilter('cpf', '=', $cpf));  

        $sql = "select * 
                  from equipe a, 
                       atleta_equipe b 
                  where a.id = b.ref_equipe 
		    and b.cpf = '{$cpf}'
                     and b.cpf != '11111111111' 
                    and ref_categoria_campeonato in (select id from categoria_campeonato where ref_campeonato ={$ref_campeonato})
                    and ref_equipe != {$ref_equipe}";
        

        $conn = TTransaction::get();
        $result = $conn->query( $sql );
        $objs = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
      
        if ($objs) 
        {
            $equipe = $objs[0];
            return $equipe->nome;
        }
        else
        {
            return false;
        }
    }
}
?>
