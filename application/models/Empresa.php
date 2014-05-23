<?php

class Empresa extends Zend_Db_Table_Abstract {

    protected $_name = 'empresa';
    protected $_dependentTables = array('Usuario');

    public function acceptFromUserData($dados) {

        $validadores = array(
            'nome' => array(
                'allowEmpty' => false
            )
        );

        return new Zend_Filter_Input(array(), $validadores, $dados);
    }

    public function listaAtivos() {
        return $this->fetchAll('ativo = 1');
    }

}
