<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        
    }

    public function configuracoesAction() {
        $storage = new Zend_Auth_Storage_Session("usuario");

        $usuario = new Usuario();
        $atual = $usuario->find($storage->read()->id)->current();

        $this->view->dados = $atual;

        if ($this->_request->isPost()) {

            $data = $this->_request->getPost();

            if ($usuario->acceptFromUserData($data)->isValid()) {
                try {
                    $usuario->update($data, "id = {$atual->id}");
                    $this->_helper->flashMessenger(array('success' => 'Dados Atualizados com sucesso!'));
                    $this->_redirect('/index/configuracoes');
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

}
