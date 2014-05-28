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

    public function getAll($idUsuario) {
        $usuario = new Usuario();
        $usuarioAtual = $usuario->find($idUsuario)->current();
        $tarefas = $usuarioAtual->findManyToManyRowset('Tarefa', 'TarefaUsuario');

        return $tarefas;
    }

    public function adicionarUsuarioTarefa($idUsuario, $idTarefa) {
        $TarefaUsuario = new TarefaUsuario();

        $dados = array(
            'id_usuario' => $idUsuario,
            'id_tarefa' => $idTarefa
        );

        $TarefaUsuario->insert($dados);
    }

    public function verificaPermissao($idUsuario, $idTarefa) {
        $tarefa = $this->find($idTarefa)->current();
        $usuarios = $tarefa->findDependentRowset('TarefaUsuario');

        foreach ($usuarios as $u) {
            if ($u->id == $idUsuario) {
                return true;
            }
        }

        return false;
    }

    public function getSituacao($situacao) {
        switch ($situacao) {
            case 'PEN' : return 'Pendente';
            case 'CON' : return 'Concluída';
        }
    }

}
