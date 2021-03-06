<?php

class WS_LogsAlteracoes_Model extends WS_Model {

    public function __construct() {
        $this->_db = new WS_LogsAlteracoes_Db();
        $this->_title = 'Gerenciador de Logs de Alterações';
        $this->_primary = 'la.id';
        $this->_singular = 'Log de Alteração';
        $this->_plural = 'Logs de Alterações';
        parent::__construct();
    }

    public function log($usuario_id, $acao, $dados, $table_name, $action) {

        $data['usuario_id'] = $usuario_id;
        $data['pagina'] = $_SERVER['REQUEST_URI'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['dados'] = serialize($dados);
        $data['acao'] = $acao;
        $data['created'] = date('Y-m-d H:i:s');
        $data['action'] = $action;

        $data['table'] = $table_name;
        $data['object_id'] = $dados['id'];

        $this->_db->insert($data);
    }

    public function adjustToView(array $data) {
        $data['created'] = WS_Date::adjustToViewWithHour($data['created']);
        if (!empty($data['dados'])):
            $data['dados'] = unserialize($data['dados']);
        endif;
        return parent::adjustToView($data);
    }

    public function setBasicSearch() {
        $data = new WS_Date();
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('la' => 'logs_alteracoes'), array('*'))
                ->joinInner(array('u' => 'usuarios'), 'u.id = la.usuario_id', array('nome'))
                ->where('la.created >= ?', $data->sub(15, WS_Date::DAY)->toString('yyyy-MM-dd'))
                ->order('la.created DESC');
    }

    public function getByTable() {
        $query = $this->_db->select()
                ->from('logs_alteracoes', array('created', 'table'))
                ->order('created DESC')
                ->group('table');
        $items = $query->query()->fetchAll();
        return $items;
    }

}
