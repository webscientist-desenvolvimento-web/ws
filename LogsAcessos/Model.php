<?php

class WS_LogsAcessos_Model extends WS_Model {

    public function __construct() {
        $this->_db = new WS_LogsAcessos_Db();
        $this->_title = 'Gerenciador de Logs de Acessos';
        $this->_primary = 'la.id';
        $this->_singular = 'Log de Acesso';
        $this->_plural = 'Logs de Acessos';
        parent::__construct();
    }

    public function log($data) {
        $data['created'] = date('Y-m-d H:i:s');
        $this->_db->insert($data);
    }

    public function adjustToView(array $data) {
        $data['created'] = WS_Date::adjustToViewWithHour($data['created']);
        return parent::adjustToView($data);
    }

    public function setBasicSearch() {
        $data = new WS_Date();
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('la' => 'logs_acessos'), array('created', 'ip', 'navegador'))
                ->joinInner(array('u' => 'usuarios'), 'u.id = la.usuario_id', array('nome'))
                ->where('la.created >= ?', $data->sub(60, WS_Date::DAY)->toString('yyyy-MM-dd'))
                ->order('la.created DESC');
    }

    public function getLastAccess($usuario_id) {
        $query = $this->_db->select()
                ->from('logs_acessos', array('created'))
                ->order('created DESC')
                ->limit(1)
                ->where('usuario_id = ?', $usuario_id);
        $item = $query->query()->fetch();
        return $item;
    }

}
