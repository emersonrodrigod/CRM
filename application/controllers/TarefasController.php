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

}
