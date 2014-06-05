<?php

class Historico extends Zend_Db_Table_Abstract {

    protected $_name = 'historico';
    protected $_dependentTables = array('Tarefa', 'Anexo');
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

    public function remover($id) {
        $atual = $this->find($id)->current();

        foreach ($atual->findDependentRowset('Anexo') as $anexo) {
            $a = new Anexo();
            $anexoAtual = $a->find($anexo->id)->current();
            unlink($anexoAtual->caminho);
            $anexoAtual->delete();
        }

        foreach ($atual->findDependentRowset('Tarefa') as $tarefa) {
            $t = new Tarefa();
            $tarefaAtual = $t->find($tarefa->id)->current();

            foreach ($tarefaAtual->findDependentRowset('TarefaUsuario') as $usuario) {
                $u = new TarefaUsuario();
                $uAtual = $u->find($usuario->id)->current();
                $uAtual->delete();
            }

            $tarefaAtual->delete();
        }

        $atual->delete();
    }

}
