<?php

class HistoricoTarefa extends Zend_Db_Table_Abstract {

    protected $_name = 'historico_tarefa';
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
