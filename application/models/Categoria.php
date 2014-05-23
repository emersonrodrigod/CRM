<?php

class Categoria extends Zend_Db_Table_Abstract {

    protected $_name = 'categoria';
    protected $_dependentTables = array('Cliente');

    public function getAll() {
        return $this->fetchAll();
    }

    public function gravar(array $dados, $id = null) {

        $validador = $this->validar($dados);

        if ($validador->isValid()) {
            if ($id == null) {
                return $this->insert($dados);
            } else {
                return $this->update($dados, "id = {$id}");
            }
        } else {
            throw new Exception('Ocorreu algum erro: ' . $validador->getErrors());
        }
    }

    public function remover($id) {
        return $this->delete("id = {$id}");
    }

    public function validar(array $dados) {

        $validadores = array(
            'nome' => array(
                'allowEmpty' => false
            )
        );

        return new Zend_Filter_Input(array(), $validadores, $dados);
    }

}
