<?php

class Anexo extends Zend_Db_Table_Abstract {

    protected $_name = 'anexo';
    protected $_referenceMap = array(
        'Historico' => array(
            'refTableClass' => 'Historico',
            'refColumns' => array('id'),
            'columns' => array('id_historico')
        ),
        'Tarefa' => array(
            'refTableClass' => 'Tarefa',
            'refColumns' => array('id'),
            'columns' => array('id_tarefa')
        ),
        'Usuario' => array(
            'refTableClass' => 'Usuario',
            'refColumns' => array('id'),
            'columns' => array('id_usuario')
        )
    );

    public function adicionar($dados) {
        $transferencia = new Zend_File_Transfer_Adapter_Http();
        $transferencia->setDestination("anexos");
        $transferencia->receive();

        $files = $transferencia->getFileName();

        if (is_array($files)) {
            foreach ($files as $file => $name) {

                $tmp = explode(".", $name);
                $nome = md5(uniqid()) . '.' . strtolower(end($tmp));

                $filterFileRename = new Zend_Filter_File_Rename(array('target' => 'anexos/' . $nome, 'overwrite' => true));
                $filterFileRename->filter($transferencia->getFileName($file));

                $nomeBanco = explode("\\", $name);

                $bd['nome'] = $nomeBanco[1];
                $bd['caminho'] = 'anexos/' . $nome;
                $bd['id_historico'] = $dados['id_historico'];
                $bd['id_usuario'] = $dados['id_usuario'];
                $bd['id_tarefa'] = $dados['id_tarefa'];

                $this->insert($bd);
            }
        } else {
            $tmp = explode(".", $files);
            $nome = md5(uniqid()) . '.' . strtolower(end($tmp));

            $filterFileRename = new Zend_Filter_File_Rename(array('target' => 'anexos/' . $nome, 'overwrite' => true));
            $filterFileRename->filter($transferencia->getFileName());

            $nomeBanco = explode("\\", $files);

            $bd['nome'] = $nomeBanco[1];
            $bd['caminho'] = 'anexos/' . $nome;
            $bd['id_historico'] = $dados['id_historico'];
            $bd['id_usuario'] = $dados['id_usuario'];
            $bd['id_tarefa'] = $dados['id_tarefa'];

            $this->insert($bd);
        }
    }

}
