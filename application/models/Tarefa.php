<?php

class Tarefa extends Zend_Db_Table_Abstract {

    protected $_name = 'tarefa';
    protected $_dependentTables = array('TarefaUsuario');
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
        ),
        'Historico' => array(
            'refTableClass' => 'Historico',
            'refColumns' => array('id'),
            'columns' => array('id_historico')
        )
    );

    public function adicionar($dados, $usuarios) {
        $id = $this->insert($dados);

        foreach ($usuarios as $usuario => $novo) {
            $this->adicionarUsuarioTarefa($novo, $id);
        }
    }

    public function adicionarUsuarioTarefa($idUsuario, $idTarefa) {
        $TarefaUsuario = new TarefaUsuario();

        $dados = array(
            'id_usuario' => $idUsuario,
            'id_tarefa' => $idTarefa
        );

        $TarefaUsuario->insert($dados);
    }

}
