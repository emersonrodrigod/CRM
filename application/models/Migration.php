<?php

class Migration extends Zend_Db_Table_Abstract {

    protected $_name = 'migrations';
    protected $_referenceMap = array(
        'Usuario' => array(
            'refTableClass' => 'Usuario',
            'refColumns' => array('id'),
            'columns' => array('id_usuario')
        )
    );

}
