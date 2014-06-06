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
        $this->view->tarefas = $tarefa->getAll($this->idUsuario, 'PEN');

        if ($this->_getParam('situacao') != null) {
            $this->view->situacao = $this->_getParam('situacao');
            $this->view->tarefas = $tarefa->getAll($this->idUsuario, $this->_getParam('situacao'));
        }
    }

    public function visualizarAction() {
        $this->view->objTarefa = new Tarefa();
        $tarefa = new Tarefa();
        $atual = $tarefa->find($this->_getParam('id'))->current();

        if (!$tarefa->verificaPermissao($this->idUsuario, $atual->id)) {
            $this->_redirect('/error/access-denied');
        }
        $this->view->tarefa = $atual;
        $this->view->idUsuario = $this->idUsuario;
    }

    public function acompanhamentoAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        if ($this->_request->isPost()) {

            $tarefa = new Tarefa();
            $dados = $this->_request->getPost();

            try {
                $arquivo = $_FILES['arquivo'];
                unset($dados['arquivo']);

                $tarefa->adicionaHistorico($dados);

                if ($arquivo['name'] != '') {
                    $anexo = new Anexo();

                    $dadosAnexo = array(
                        'id_historico' => null,
                        'id_usuario' => $dados['id_usuario'],
                        'id_tarefa' => $dados['id_tarefa']
                    );

                    $anexo->adicionar($dadosAnexo);
                }

                $this->_helper->flashMessenger(array('success' => 'Acompanhamento gravado com sucesso!'));
            } catch (Zend_Db_Exception $e) {
                $this->_helper->flashMessenger(array('error' => 'Ocorreu um erro: ' . $e->getMessage()));
            }

            $this->_redirect('/tarefas/visualizar/id/' . $dados['id_tarefa']);
        }
    }

    public function removeAcompanhamentoAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        try {
            $tarefa = new Tarefa();
            $tarefa->removerHistorico($this->_getParam('id'));
            $this->_helper->flashMessenger(array('success' => 'Acompanhamento Removido com sucesso!'));
        } catch (Zend_Db_Exception $e) {
            $this->_helper->flashMessenger(array('error' => 'Ocorreu um erro: ' . $e->getMessage()));
        }

        $this->_redirect('/tarefas/visualizar/id/' . $this->_getParam('tarefa'));
    }

    public function concluirAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        if ($this->_request->isPost()) {
            $dados = $this->_request->getPost();
            try {
                $tarefa = new Tarefa();

                $tarefa->concluir($dados);

                $this->_helper->flashMessenger(array('success' => 'Tarefa Concluída com sucesso!'));
            } catch (Zend_Db_Exception $e) {
                $this->_helper->flashMessenger(array('error' => 'Ocorreu um erro: ' . $e->getMessage()));
            }

            $this->_redirect('/tarefas/visualizar/id/' . $dados['id_tarefa']);
        }
    }

    public function cancelarAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        if ($this->_request->isPost()) {
            $dados = $this->_request->getPost();
            try {
                $tarefa = new Tarefa();
                $tarefa->cancelar($dados);

                $this->_helper->flashMessenger(array('success' => 'Tarefa Cancelada com sucesso!'));
            } catch (Zend_Db_Exception $e) {
                $this->_helper->flashMessenger(array('error' => 'Ocorreu um erro: ' . $e->getMessage()));
            }

            $this->_redirect('/tarefas/visualizar/id/' . $dados['id_tarefa']);
        }
    }

}
