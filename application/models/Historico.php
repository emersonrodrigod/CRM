<?php

class Historico extends Zend_Db_Table_Abstract {

    protected $_name = 'historico';
    protected $_referenceMap = array(
        'Cliente' => array(
            'refTableClass' => 'Cliente',
            'refColumns' => array('id'),
            'columns' => array('id_cliente')
        ),
        'Usuario' => array(
            'refTableClass' => 'Usuario',
            'refColumns' => array('id'),
            'columns' => array('id_usuario')
        )
    );

    public function gravar(array $dados, $id = null) {
        if ($id == null) {
            return $this->insert($dados);
        } else {
            return $this->update($dados, "id = {$id}");
        }
    }

}
