<?php

class ClientesController extends Zend_Controller_Action {

    public function indexAction() {
        
    }

    public function listaAction() {
        $this->_helper->layout()->disableLayout();

        $sessionPesquisa = new Zend_Session_Namespace('pesquisa');

        if (isset($sessionPesquisa->parametros)) {
            $cliente = new Cliente();
            $this->view->clientes = $cliente->filtrar($sessionPesquisa->parametros);
        }

        if ($this->_request->isPost()) {
            $pesquisa = new Zend_Session_Namespace('pesquisa');
            $pesquisa->parametros = $this->_request->getPost();
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
        $cliente = new Cliente();
        $atual = $cliente->find($this->_getParam('id'))->current();
        $this->view->cliente = $atual;

        $storage = new Zend_Auth_Storage_Session("usuario");
        $this->view->idUsuario = $storage->read()->id;

        $usuario = new Usuario();
        $this->view->usuarios = $usuario->fetchAll('ativo = 1');
    }

    public function gravaHistoricoAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        if ($this->_request->isPost()) {
            $historico = new Historico();
            $dados = $this->_request->getPost();

            if ($dados['dtAgendamento'] != '') {
                $dados['tarefa'] = 1;
            }

            $dadosTarefa = array(
                'dtTarefa' => Util::dataMysql($dados['dtAgendamento']),
                'horaTarefa' => $dados['horaAgendamento'],
                'texto' => $dados['texto'],
                'id_usuario' => $dados['id_usuario'],
                'id_cliente' => $dados['id_cliente'],
            );

            $usuariosTarefa = $dados['usuario'];

            unset($dados['dtAgendamento']);
            unset($dados['horaAgendamento']);
            unset($dados['usuario']);


            try {

                if ($idHistorico = $historico->gravar($dados)) {
                    $dadosTarefa['id_historico'] = $idHistorico;
                    if ($dadosTarefa['dtTarefa'] != '') {
                        $tarefa = new Tarefa();
                        $tarefa->adicionar($dadosTarefa, $usuariosTarefa);
                    }
                }

                if ($_FILES['arquivo']['name'] != '') {
                    $anexo = new Anexo();

                    $dadosAnexo = array(
                        'id_historico' => $idHistorico,
                        'id_usuario' => $dados['id_usuario'],
                        'id_tarefa' => null
                    );

                    $anexo->adicionar($dadosAnexo);
                }


                $this->_helper->flashMessenger(array('success' => 'Histórico gravado com sucesso!'));
                $this->_redirect('/clientes/historico/id/' . $dados['id_cliente']);
            } catch (Exception $ex) {
                $this->_helper->flashMessenger(array('error' => 'Desculpe, ocorreu um erro: ' . $ex->getMessage()));
            }
        }
    }

    public function removeHistoricoAction() {
        $this->_helper->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();
        $historico = new Historico();

        try {
            $historico->remover($this->_getParam('id'));
            $this->_helper->flashMessenger(array('success' => 'Histórico removido com sucesso!'));
            $this->_redirect('/clientes/historico/id/' . $this->_getParam('cliente'));
        } catch (Zend_Db_Exception $e) {
            $this->_helper->flashMessenger(array('error' => 'Não é possivel excluir um histórico cujo qual a tarefa contem acompanhamentos. ' . $e->getMessage()));
            $this->_redirect('/clientes/historico/id/' . $this->_getParam('cliente'));
        }
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
