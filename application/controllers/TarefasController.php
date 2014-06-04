<?php

class TarefasController extends Zend_Controller_Action {

    private $idUsuario;

    public function init() {
        $storage = new Zend_Auth_Storage_Session("usuario");
        $this->idUsuario = $storage->read()->id;
        $this->view->tarefa = new Tarefa();
    }

    public function indexAction() {
        $tarefa = new Tarefa();
        $this->view->tarefas = $tarefa->getAll($this->idUsuario);
    }

    public function visualizarAction() {
        $this->view->objTarefa = new Tarefa();
        $tarefa = new Tarefa();
        $atual = $tarefa->find($this->_getParam('id'))->current();

        if (!$tarefa->verificaPermissao($this->idUsuario, $atual->id)) {
            $this->_redirect('/error/access-denied');
        }
        $this->view->tarefa = $atual;
    }

}
