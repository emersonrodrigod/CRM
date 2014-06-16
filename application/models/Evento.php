<?php

class Evento {

    private $id;
    private $title;
    private $start;
    private $url;
    private $solicitante;

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return substr($this->title, 0, 40) . "...";
    }

    public function getStart() {
        return $this->start;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setStart($start) {
        $this->start = $start;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getSolicitante() {
        return $this->solicitante;
    }

    public function setSolicitante($solicitante) {
        $this->solicitante = $solicitante;
    }

    public function toArray() {
        $return = array();

        $return['id'] = $this->getId();
        $return['title'] = $this->getTitle();
        $return['start'] = $this->getStart();
        $return['url'] = $this->getUrl();
        $return['solicitante'] = $this->getSolicitante();

        return $return;
    }

}
