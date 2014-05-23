<?php

class PrivacidadeHistorico extends Zend_Db_Table_Abstract {

    protected $_name = 'privacidade_historico';
    protected $_referenceMap = array(
        'Usuario' => array(
            'refTableClass' => 'Usuario',
            'refColumns' => array('id'),
            'columns' => array('id_usuario')
        ),
        'Cliente' => array(
            'refTableClass' => 'Cliente',
            'refColumns' => array('id'),
            'columns' => array('id_cliente')
        )
    );

}
