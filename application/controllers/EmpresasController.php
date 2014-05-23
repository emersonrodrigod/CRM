<?php

class EmpresasController extends Zend_Controller_Action {

    public function indexAction() {
        $empresa = new Empresa();
        $this->view->empresas = $empresa->fetchAll();
    }

    public function novoAction() {
        if ($this->_request->isPost()) {

            $data = $this->_request->getPost();
            $empresa = new Empresa();

            if ($empresa->acceptFromUserData($data)->isValid()) {
                try {
                    $empresa->insert($data);
                    $this->_helper->flashMessenger(array('success' => 'Empresa gravada com sucesso!'));
                    $this->_redirect('/empresas');
                } catch (Zend_Db_Exception $exc) {
                    $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $exc->getMessage()));
                }
            } else {

                foreach ($empresa->acceptFromUserData($data)->getMessages() as $message) {
                    foreach ($message as $m) {
                        $this->_helper->flashMessenger(array('error' => $m));
                    }
                }
            }
        }
    }

    public function editarAction() {
        $empresa = new Empresa();
        $atual = $empresa->find(intval($this->_getParam('id')))->current();
        $this->view->empresa = $atual;

        if ($this->_request->isPost()) {

            $data = $this->_request->getPost();
            if ($empresa->acceptFromUserData($data)->isValid()) {
                try {
                    $empresa->update($data, "id = {$atual->id}");
                    $this->_helper->flashMessenger(array('success' => 'Empresa atualizada com sucesso!'));
                    $this->_redirect('/empresas');
                } catch (Zend_Db_Exception $exc) {
                    $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $exc->getMessage()));
                }
            } else {
                foreach ($empresa->acceptFromUserData($data)->getMessages() as $message) {
                    foreach ($message as $m) {
                        $this->_helper->flashMessenger(array('error' => $m));
                    }
                }
            }
        }
    }

    public function removerAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        $empresa = new Empresa();
        $atual = $empresa->find(intval($this->_getParam('id')))->current();

        try {
            $atual->delete();
            $this->_helper->flashMessenger(array('success' => 'Empresa Removida com sucesso!'));
            $this->_redirect('/empresas');
        } catch (Zend_Db_Exception $exc) {
            $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $exc->getMessage()));
        }
    }

    public function departamentosAction() {
        $empresa = new Empresa();
        $atual = $empresa->find($this->_getParam('empresa'))->current();
        $this->view->empresa = $atual;
    }

    public function listaDepartamentosAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        if ($this->_request->isGet()) {

            $empresa = new Empresa();
            $atual = $empresa->find($this->_getParam('empresa'))->current();

            $dados = array(
                'departamentos' => $atual->findDependentRowset('Departamento')->toArray()
            );

            echo Zend_Json_Encoder::encode($dados);
        }
    }

    public function salvaDepartamentoAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        if ($this->getRequest()->isPost()) {

            $dados = $this->getRequest()->getPost();
            $departamento = new Departamento();

            $data = array(
                'nome' => $dados['nome'],
                'id_empresa' => $dados['id_empresa']
            );

            try {
                if ($dados['id'] > 0) {
                    if ($departamento->acceptFromUserData($data)->isValid()) {
                        $departamento->update($data, "id = {$dados['id']}");
                    }
                } else {
                    if ($departamento->acceptFromUserData($data)->isValid()) {
                        $departamento->insert($data);
                    }
                }
            } catch (Zend_Db_Exception $exc) {
                $exc->getTraceAsString();
            }
        }
    }

    public function removeDepartamentoAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        if ($this->getRequest()->isGet()) {
            $departamento = new Departamento();
            $atual = $departamento->find(intval($this->_getParam('id')))->current();
            try {
                $atual->delete();
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
    }

}