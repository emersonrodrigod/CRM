<?php

class Usuario extends Zend_Db_Table_Abstract {

    protected $_name = 'usuario';
    protected $_dependentTables = array('PrivacidadeHistorico','Cliente','Historico','Tarefa','TarefaUsuario','Migration','Anexo');
    protected $_referenceMap = array(
        'Empresa' => array(
            'refTableClass' => 'Empresa',
            'refColumns' => array('id'),
            'columns' => array('id_empresa')
        ),
        'Departamento' => array(
            'refTableClass' => 'Departamento',
            'refColumns' => array('id'),
            'columns' => array('id_departamento')
        )
    );

    public function acceptFromUserData($dados) {

        $validadores = array(
            'nome' => array(
                'allowEmpty' => false
            ),
            'email' => array(
                'allowEmpty' => false,
                'emailAdrress' => true
            ),
            'senha' => array(
                'allowEmpty' => false
            ),
            'role' => array(
                'allowEmpty' => false
            )
        );

        return new Zend_Filter_Input(array(), $validadores, $dados);
    }
    
    public function getByDepartamento($idDepartamento){
        return $this->fetchAll("ativo = 1 and id_departamento = {$idDepartamento}");
    }

}
