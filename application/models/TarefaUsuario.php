<?php

class TarefaUsuario extends Zend_Db_Table_Abstract {

    protected $_name = 'tarefa_usuario';
    protected $_referenceMap = array(
        'Tarefa' => array(
            'refTableClass' => 'Tarefa',
            'refColumns' => array('id'),
            'columns' => array('id_tarefa')
        ),
        'Usuario' => array(
            'refTableClass' => 'Usuario',
            'refColumns' => array('id'),
            'columns' => array('id_usuario')
        )
    );

}
