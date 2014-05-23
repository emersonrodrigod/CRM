<?php

class CategoriasController extends Zend_Controller_Action {

    public function indexAction() {
        $categoria = new Categoria();
        $this->view->categorias = $categoria->getAll();
    }

    public function novoAction() {
        if ($this->_request->isPost()) {

            $data = $this->_request->getPost();
            $categoria = new Categoria();

            try {
                $categoria->gravar($data);
                $this->_helper->flashMessenger(array('success' => 'Categoria gravada com sucesso!'));
                $this->_redirect('/categorias');
            } catch (Zend_Db_Exception $ex) {
                $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $ex->getMessage()));
            }
        }
    }

    public function editarAction() {
        $categoria = new Categoria();
        $atual = $categoria->find(intval($this->_getParam('id')))->current();
        $this->view->categoria = $atual;

        if ($this->_request->isPost()) {

            $data = $this->_request->getPost();

            try {
                $categoria->gravar($data, $this->_getParam('id'));
                $this->_helper->flashMessenger(array('success' => 'Categoria gravada com sucesso!'));
                $this->_redirect('/categorias');
            } catch (Exception $ex) {
                $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $ex->getMessage()));
            }
        }
    }

    public function removerAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        $categoria = new Categoria();
        $search = $categoria->find(intval($this->_getParam('id')));

        if ($search) {
            try {
                $categoria->remover($this->_getParam('id'));
                $this->_helper->flashMessenger(array('success' => 'Categoria Removida com sucesso!'));
                $this->_redirect('/categorias');
            } catch (Exception $exc) {
                $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $exc->getMessage()));
            }
        }
    }

}
