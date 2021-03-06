<?php 
abstract class AppModel {    

    public $validation_errors = array();
    protected $_table;
    protected $_db;

    /**
     * 
     *
     * @param 
     * @return 
     */
    public function __construct($params = array()) {
        $options = array(
            'driver' => DB_DRIVER,
            'host' => DB_HOST,
            'username' => DB_USER,
            'password' => DB_PASS,
            'database' => DB_NAME,
            'charset' => DB_CHARSET);
        $this->_db = new DibiConnection($options);
    }

    public function get_select_columns($params) {
        $select_columns = '*';
        if(array_key_exists('fields', $params) && !empty($params['fields'])) {

            if(is_array($params['fields'])) {
                $select_columns = null;
                foreach($params['fields'] as $field) {
                    $select_columns .= '['. $field .'],';
                }

                $select_columns = substr(trim($select_columns), 0, -1);
            } else {
                $select_columns = sprintf('[%s]', $params['fields']);
            }
        }

        return $select_columns;
    }

    protected function _complete_data($data) {
        // 1. Updated field.
        if(!array_key_exists('updated', $data)) { // Add info about updated.
            $data['updated'] = date('Y-m-d H:i:s');
        }

        // 2. If we insert new record
        if(!array_key_exists('id', $data) || empty($data['id'])) {
            if(!array_key_exists('created', $data)) { // Add info about created.
                $data['created'] = date('Y-m-d H:i:s');
            }            
        }

        return $data;
    }
}