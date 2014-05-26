<?php

class Cliente extends Zend_Db_Table_Abstract {

    protected $_name = 'cliente';
    protected $_dependentTables = array('PrivacidadeHistorico','Historico','Cliente');
    protected $_referenceMap = array(
        'Reponsavel' => array(
            'refTableClass' => 'Usuario',
            'refColumns' => array('id'),
            'columns' => array('id_responsavel')
        ),
        'Categoria' => array(
            'refTableClass' => 'Categoria',
            'refColumns' => array('id'),
            'columns' => array('id_categoria')
        )
    );

    public function getAll() {
        return $this->fetchAll(null, 'nome asc');
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

    public function filtrar(array $parametros) {
        $where = "1 = 1";

        if ($parametros['cnpj'] != '') {
            $where .= " and cnpj = '{$parametros['cnpj']}'";
        }

        if ($parametros['nome'] != '') {
            $where .= " and nome like '%{$parametros['nome']}%'";
        }

        if ($parametros['categoria'] != '') {
            $where .= " and id_categoria = {$parametros['categoria']}";
        }
        
        if ($parametros['responsavel'] != '') {
            $where .= " and id_responsavel = {$parametros['responsavel']}";
        }
        
        if ($parametros['estado'] != '') {
            $where .= " and estado = '{$parametros['estado']}'";
        }

        return $this->fetchAll($where, 'nome asc');
    }

    public function validar(array $dados) {

        $validadores = array(
            'nome' => array(
                'allowEmpty' => false
            ),
            'codigo' => array(
                'allowEmpty' => false
            )
        );

        return new Zend_Filter_Input(array(), $validadores, $dados);
    }

}
