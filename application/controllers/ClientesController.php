<?php

class ClientesController extends Zend_Controller_Action {

    public function indexAction() {
        
    }

    public function listaAction() {
        $this->_helper->layout()->disableLayout();

        if ($this->_request->isPost()) {
            $cliente = new Cliente();
            $this->view->clientes = $cliente->filtrar($this->_request->getPost());
        }
    }

    public function novoAction() {
        $usuario = new Usuario();
        $this->view->usuarios = $usuario->fetchAll();

        $categoria = new Categoria();
        $this->view->categorias = $categoria->getAll();

        if ($this->_request->isPost()) {

            $data = $this->_request->getPost();
            $cliente = new Cliente();

            try {
                $cliente->gravar($data);
                $this->_helper->flashMessenger(array('success' => 'Cliente gravado com sucesso!'));
                $this->_redirect('/clientes');
            } catch (Zend_Db_Exception $ex) {
                $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $ex->getMessage()));
            }
        }
    }

    public function editarAction() {

        $usuario = new Usuario();
        $this->view->usuarios = $usuario->fetchAll();

        $categoria = new Categoria();
        $this->view->categorias = $categoria->getAll();

        $cliente = new Cliente();
        $atual = $cliente->find(intval($this->_getParam('id')))->current();
        $this->view->cliente = $atual;

        if ($this->_request->isPost()) {

            $data = $this->_request->getPost();

            try {
                $cliente->gravar($data, $this->_getParam('id'));
                $this->_helper->flashMessenger(array('success' => 'Cliente gravado com sucesso!'));
                $this->_redirect('/clientes');
            } catch (Exception $ex) {
                $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $ex->getMessage()));
            }
        }
    }

    public function historicoAction() {
        
    }

    public function importarAction() {
        if ($this->_request->isPost()) {

            $cli = new Cliente();

            $processados = 0;
            $erros = array();

            $adapter = new Zend_File_Transfer_Adapter_Http();
            $adapter->setDestination('temp');
            $info = $adapter->getFileInfo();

            if ($adapter->receive()) {

                $xml = simplexml_load_file("temp/" . $info['arquivo']['name']);

                foreach ($xml->FCFO as $cliente) {

                    if ($cli->fetchRow('codigo = ' . $cliente->CODCFO)) {
                        $erros[] = "Cliente $cliente->NOME já está cadastrado!!";
                    } else {

                        $dados = array(
                            'nome' => $cliente->NOME,
                            'codigo' => $cliente->CODCFO,
                            'telefone' => $cliente->TELEFONE,
                            'email' => $cliente->EMAIL,
                            'cnpj' => $cliente->CGCCFO,
                            'estado' => $cliente->CODETD
                        );

                        $insert = $cli->insert($dados);

                        if ($insert) {
                            $processados ++;
                        } else {
                            $erros[] = "Cliente $cliente->NOME não pode ser inserido.";
                        }
                    }
                }
            }

            $this->view->processados = $processados;
            $this->view->erros = $erros;
        }
    }

}
