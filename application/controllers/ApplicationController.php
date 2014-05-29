<?php

class ApplicationController extends Zend_Controller_Action {

    private $idUsuario = null;

    public function init() {
        $storage = new Zend_Auth_Storage_Session("usuario");
        $this->idUsuario = $storage->read()->id;
    }

    public function migrationsAction() {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $bootstrap->bootstrap('db');
        $config = $bootstrap->getResource('db')->getConfig();

        $passwordString = ($config["password"] == '' ? '' : " -p '" . $config["password"] . "' ");

        $migration = new Migration();

        $dir = new DirectoryIterator(dirname(APPLICATION_PATH) . '\data\db\migrations');
        foreach ($dir as $item) {
            if ($item->isDot()) {
                continue;
            }

            $filename = $item->getFilename();

            $registro = $migration->fetchRow("nome = '$filename'");

            if ($registro) {
                echo '<div class="alert alert-info">' . $item->getFilename() . " já foi executado dia " . date("d/m/Y H:i:s", strtotime($registro->quando)) . " pelo usuário " . $registro->findParentRow("Usuario")->nome . "</div>";
            } else {
                $runCommand = "mysql -h " . $config["host"] . " -u '" . $config["username"] . "' " . $passwordString . $config["dbname"] . " < " . dirname(APPLICATION_PATH) . "\data\db\migrations\\" . $filename;
                system($runCommand);
                echo $runCommand;

                $dados = array(
                    'nome' => $item->getFilename(),
                    'id_usuario' => $this->idUsuario
                );

                $migration->insert($dados);

                echo '<div class="alert alert-success">' . $item->getFilename() . " executado com sucesso!</div>";
            }
        }
    }

}
