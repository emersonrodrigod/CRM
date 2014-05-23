<?php

class UsuariosController extends Zend_Controller_Action {

    public function indexAction() {
        $usuario = new Usuario();
        $this->view->usuarios = $usuario->fetchAll();
    }

    public function novoAction() {

        $empresa = new Empresa();
        $this->view->empresas = $empresa->listaAtivos();

        if ($this->_request->isPost()) {

            $data = $this->_request->getPost();

            unset($data['confirma']);
            $data['senha'] = sha1($data['senha']);

            $usuario = new Usuario();

            if ($usuario->acceptFromUserData($data)->isValid()) {
                try {
                    $usuario->insert($data);
                    $this->_helper->flashMessenger(array('success' => 'Usuário gravado com sucesso!'));
                    $this->_redirect('/usuarios');
                } catch (Zend_Db_Exception $exc) {
                    $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $exc->getMessage()));
                }
            } else {

                foreach ($usuario->acceptFromUserData($data)->getMessages() as $message) {
                    foreach ($message as $m) {
                        $this->_helper->flashMessenger(array('error' => $m));
                    }
                }
            }
        }
    }

    public function editarAction() {

        $empresa = new Empresa();
        $this->view->empresas = $empresa->listaAtivos();

        $usuario = new Usuario();
        $atual = $usuario->find(intval($this->_getParam('id')))->current();
        $this->view->usuario = $atual;

        $departamento = new Departamento();
        $this->view->departamentos = $departamento->listaAtivos($atual->id_empresa);

        if ($this->_request->isPost()) {

            $data = $this->_request->getPost();
            if ($usuario->acceptFromUserData($data)->isValid()) {
                try {
                    $usuario->update($data, "id = {$atual->id}");
                    $this->_helper->flashMessenger(array('success' => 'Usuário atualizado com sucesso!'));
                    $this->_redirect('/usuarios');
                } catch (Zend_Db_Exception $exc) {
                    $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $exc->getMessage()));
                }
            } else {

                foreach ($usuario->acceptFromUserData($data)->getMessages() as $message) {
                    foreach ($message as $m) {
                        $this->_helper->flashMessenger(array('error' => $m));
                    }
                }
            }
        }
    }

    public function departamentosAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        $departamento = new Departamento();
        $departamentos = $departamento->getByEmpresa($this->_getParam('empresa'))->toArray();
        echo json_encode($departamentos);
    }

    public function senhaAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        $usuario = new Usuario();
        $search = $usuario->find(intval($this->_getParam('id'))); 
        
        if ($search) {
            $dados['senha'] = sha1('123mudar');

            try {
                $usuario->update($dados, "id = {$this->_getParam('id')}");
                $this->_helper->flashMessenger(array('success' => 'Senha do Usuário '. $search->current()->nome . ' foi definida para 123mudar'));
                $this->_redirect('/usuarios');
            } catch (Exception $exc) {
                $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $exc->getMessage()));
            }
        }
    }

}