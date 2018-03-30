<?php
namespace Tinymce4\Services;

class DbService 
{

    public $db;
    public $container;
    public $last_error = '';
    
    public function __construct($container){ 
        $this->container = $container;
        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );
        $dsn = 'mysql:dbname='. $container->getParameter('db_name'). ';host='. $container->getParameter('db_host');
        $this->db = new \PDO($dsn, $container->getParameter('db_user'), $container->getParameter('db_pass'), $options);
    }

    public function lastInsertId(){
        return $this->db->lastInsertId();
    }
    public function getLastError() {
        return $this->last_error;
    }

    public function fetchAll($sql, $params = array()){
        $sth = $this->db->prepare($sql);
        if( false === $sth ) return 'error '. implode(', ', $this->db->errorInfo());
        $res = $sth->execute($params);
        if( false === $res ) return 'error '. implode(', ', $sth->errorInfo());

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchArray($sql, $params = array()){
        $sth = $this->db->prepare($sql);
        if( false === $sth ) return 'error '. implode(', ', $this->db->errorInfo());
        $res = $sth->execute($params);
        if( false === $res ) return 'error '. implode(', ', $sth->errorInfo());

        return $sth->fetch(\PDO::FETCH_NUM);
    }

    public function fetchAssoc($sql, $params = array()){
        $sth = $this->db->prepare($sql);
        if( false === $sth ) return 'error '. implode(', ', $this->db->errorInfo());
        $res = $sth->execute($params);
        if( false === $res ) return 'error '. implode(', ', $sth->errorInfo());

        return $sth->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchColumn($sql, $params = array()){
        $sth = $this->db->prepare($sql);
        if( false === $sth ) return 'error '. implode(', ', $this->db->errorInfo());
        $res = $sth->execute($params);
        if( false === $res ) return 'error '. implode(', ', $sth->errorInfo());

        return $sth->fetchColumn();
    }

    public function query($sql){
        return $this->db->exec($sql);
    }
    
    public function insert($table, $data){
        $sql = "INSERT INTO `$table` (`".
            implode('`,`', array_keys($data))."`)
            VALUES (". implode(',', array_fill(0, count($data), '?')).")";

        $sth = $this->db->prepare($sql);
        if( false === $sth ) return 'error '. implode(', ', $this->db->errorInfo());
        $res = $sth->execute(array_values($data));
        if( false === $res ) return 'error '. implode(', ', $sth->errorInfo());

        return '';
    }

    public function update($table, $data, $where){
        $params = array();
        $sep = ' SET ';
        $sql = "UPDATE `$table`";
        foreach($data as $key=>$val){
            $sql .= $sep.$key.'=?';
            $binds[] = $val;
            $sep = ',';
        }
        $sep = ' WHERE ';
        foreach($where as $key=>$val){
            $sql.= $sep.$key."=?";
            $binds[] = $val;
            $sep = " AND ";
        }
        $sth = $this->db->prepare($sql);
        if( false === $sth ) return 'error '. implode(', ', $this->db->errorInfo());
        $res = $sth->execute($binds);
        if( false === $res ) return 'error '. implode(', ', $sth->errorInfo());

        return '';
        
    }

    public function delete($table, $where){
        $sql = "DELETE FROM $table WHERE ";
        $sep = '';
        $binds = array();
        foreach($where as $key=>$val){
            $sql.= $sep.$key.'=?';
            $sep = ' AND ';
            $binds[] = $val;
        }
        $sth = $this->db->prepare($sql);
        if( false === $sth ) return 'error '. implode(', ', $this->db->errorInfo());
        $res = $sth->execute($binds);
        if( false === $res ) return 'error '. implode(', ', $sth->errorInfo());

        return '';
    }

    public function errorInfo(){
        return $this->db->errorInfo();
    }

}


