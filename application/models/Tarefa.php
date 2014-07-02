<?php

class Tarefa extends Zend_Db_Table_Abstract {

    protected $_name = 'tarefa';
    protected $_dependentTables = array('TarefaUsuario', 'Anexo', 'HistoricoTarefa');
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
        ),
        'Historico' => array(
            'refTableClass' => 'Historico',
            'refColumns' => array('id'),
            'columns' => array('id_historico')
        )
    );

    public function adicionar($dados, $usuarios) {
        $id = $this->insert($dados);

        $usuario = new Usuario();

        foreach ($usuarios as $usuario => $novo) {
            $this->adicionarUsuarioTarefa($novo, $id);
        }

        $inserido = $this->find($id)->current();

        $this->enviaEmail(
                "Tarefa aberta", "Você foi mencionado em uma tarefa", $inserido, $this->getEmailList($id)
        );

        return $id;
    }

    public function getAll($idUsuario, $situacao) {
        $usuario = new Usuario();

        $select = $this->select()->where("situacao = '$situacao'")->order("id desc");

        $usuarioAtual = $usuario->find($idUsuario)->current();
        $tarefas = $usuarioAtual->findManyToManyRowset('Tarefa', 'TarefaUsuario', null, null, $select);
        return $tarefas;
    }

    public function getJson($idUsuario, $situacao) {
        $usuario = new Usuario();

        $toReturn = array();

        $select = $this->select()->where("situacao = '$situacao'")->order("id desc");

        $usuarioAtual = $usuario->find($idUsuario)->current();
        $tarefas = $usuarioAtual->findManyToManyRowset('Tarefa', 'TarefaUsuario', null, null, $select);

        foreach ($tarefas as $tarefa) {
            $evento = new Evento();
            $evento->setId($tarefa->id);
            $evento->setTitle($tarefa->texto);
            $evento->setStart($tarefa->dtTarefa . ' ' . $tarefa->horaTarefa);
            $evento->setUrl('/tarefas/visualizar/id/' . $tarefa->id);
            $evento->setSolicitante($tarefa->findParentRow('Usuario')->nome);

            $toReturn[] = $evento->toArray();
        }

        $toReturn = Zend_Json_Encoder::encode($toReturn);

        return $toReturn;
    }

    public function adicionarUsuarioTarefa($idUsuario, $idTarefa) {
        $TarefaUsuario = new TarefaUsuario();

        $dados = array(
            'id_usuario' => $idUsuario,
            'id_tarefa' => $idTarefa
        );

        $TarefaUsuario->insert($dados);
    }

    public function verificaPermissao($idUsuario, $idTarefa) {
        $tarefa = $this->find($idTarefa)->current();
        $usuarios = $tarefa->findDependentRowset('TarefaUsuario');

        foreach ($usuarios as $u) {
            if ($u->id_usuario == $idUsuario) {
                return true;
            }
        }

        return false;
    }

    public function adicionaHistorico($dados) {
        $historico = new HistoricoTarefa();

        $inserido = $this->find($dados['id_tarefa'])->current();
        $inserido->texto = $dados['texto'];
        $this->enviaEmail(
                "Acompanhamento adicionado a tarefa", "Uma tarefa recebeu um acompanhamento", $inserido, $this->getEmailList($dados['id_tarefa'])
        );

        return $historico->insert($dados);
    }

    public function getHistorico($idTarefa) {
        $atual = $this->find($idTarefa)->current();
        $historico = new HistoricoTarefa();
        $select = $historico->select()->order('id desc');
        return $atual->findDependentRowset('HistoricoTarefa', null, $select);
    }

    public function removerHistorico($idHistorico) {
        $historico = new HistoricoTarefa();
        $atual = $historico->find($idHistorico)->current();
        $atual->delete();
    }

    public function concluir($dados) {
        $dadosTarefa = array(
            'situacao' => 'CON',
            'conclusao' => date("Y-m-d H:i:s")
        );

        $this->update($dadosTarefa, "id = " . $dados['id_tarefa']);

        $dadosHistorico = array(
            'texto' => 'Tarefa Concluída',
            'id_tarefa' => $dados['id_tarefa'],
            'id_usuario' => $dados['id_usuario']
        );

        $this->adicionaHistorico($dadosHistorico);
    }

    public function cancelar($dados) {
        $dadosTarefa = array(
            'situacao' => 'CAN',
            'conclusao' => date("Y-m-d H:i:s")
        );

        $this->update($dadosTarefa, "id = " . $dados['id_tarefa']);

        $dadosHistorico = array(
            'texto' => 'Tarefa Cancelada',
            'id_tarefa' => $dados['id_tarefa'],
            'id_usuario' => $dados['id_usuario']
        );

        $this->adicionaHistorico($dadosHistorico);
    }

    public function getTasksByPediod($idUsuario, $initialDate, $finalDate) {
        $usuario = new Usuario();
        $initialDate = Util::dataMysql($initialDate);
        $finalDate = Util::dataMysql($finalDate);

        $select = $this->select()->where("dtTarefa >= '$initialDate' and dtTarefa <= '$finalDate' and situacao = 'PEN'")->order("id desc");

        $usuarioAtual = $usuario->find($idUsuario)->current();
        $tarefas = $usuarioAtual->findManyToManyRowset('Tarefa', 'TarefaUsuario', null, null, $select);
        return $tarefas;
    }

    public function getSituacao($situacao) {
        switch ($situacao) {
            case 'PEN' : return 'Pendente';
            case 'CON' : return 'Concluída';
            case 'CAN' : return 'Cancelada';
        }
    }

    public function getEmailList($idTarefa) {
        $atual = $this->find($idTarefa)->current();
        $usuarios = $atual->findManyToManyRowset('Usuario', 'TarefaUsuario');

        foreach ($usuarios as $usuario) {
            $emails[] = $usuario->email;
        }

        return $emails;
    }

    public function enviaEmail($titulo, $texto, $ssi, $from = array()) {
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/views/scripts/tarefas/');

        $html->assign('titulo', $titulo);
        $html->assign('texto', $texto);
        $html->assign('registro', $ssi);

        $bodyText = $html->render('notificacao.phtml');

        $util = new Util();

        $util->sendMail("CRM - Notificação Automática", $bodyText, $from);
    }

}
