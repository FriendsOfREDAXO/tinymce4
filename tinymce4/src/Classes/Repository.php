<?php
namespace Tinymce4\Classes;

/*
 * 16.7.15 allow NULL values in findBy, findOneBy
 * 5.10.15 add moveUp, moveDown
 * 5.10.15 add findWhere
 * 13.10.15 multiple order_by
 */

class Repository {
    public $table;       // string table name
    public $primary;     // string name of the primary key
    public $model;       // Full Model Class Name including namespace
    public $pos;         // Field for positioning
    public $pos_group;   // Field to position in
    public $multilang = false;
    public $multilang_table; // table name for language data
    public $multilang_model; // model name for language data
    public $multilang_primary; // primary for language data (combined with this->primary)
    public $languages = array();


    public $db;
    public $container;

    public function __construct($container){
        $this->container = $container;
        $this->db = $container->get('DbService');
        if ($this->multilang) {
            $this->languages = array_keys($this->container->get('LanguageService')->getLanguageChoices());
        }
    }

    public function find($id){
        $sql = "SELECT * FROM ".
            $this->table. " WHERE ".$this->primary. "=?";
        $data = $this->db->fetchAssoc($sql, array($id));
        if(is_array($data)){
            return $this->getModel($data);
        }else{
            return NULL;
        }
    }

   
    public function findAll($orderby = array()){
        $sql = "SELECT * FROM ".$this->table."
            WHERE 1";
        if(0 < count($orderby)){
            $sql.= ' ORDER BY ';
            $sep = '';
            foreach($orderby as $field => $direction){
                $sql.= $sep.$field.' '. $direction;
                $sep = ',';
            }
        }
        $aData = $this->db->fetchAll($sql);

        return $this->getModels($aData);
    }
    public function findBy($params, $orderby = array()){
        $sql = "SELECT * FROM `". $this->table. "` WHERE "; 
        $sep = '';
        $binds = array();
        foreach($params as $key => $val){
            if(NULL === $val){
                $sql.= $sep. " `$key` IS NULL ";
            }else{
                $sql.= $sep. " `$key`=?";
                $binds[] = $val;
            }
            $sep = ' AND';
        }
        if(0 < count($orderby)){
            $sql.= ' ORDER BY ';
            $sep = '';
            foreach($orderby as $field => $direction){
                $sql.= $sep.$field.' '. $direction;
                $sep = ',';
            }
        }
        $aData  = $this->db->fetchAll($sql, $binds);
        if(!$aData){
            return array();
        }else{
            return $this->getModels($aData);
        }
    }

    public function findOneBy($params, $orderby = array()){
        $sql = "SELECT * FROM `". $this->table. "` WHERE "; 
        $sep = '';
        $binds = array();
        foreach($params as $key => $val){
            if(NULL === $val){
                $sql.= $sep. " `$key` IS NULL ";
            }else{
                $sql.= $sep. " `$key`=?";
                $binds[] = $val;
            }
            $sep = ' AND';
        }
        if(0 < count($orderby)){
            $sql.= ' ORDER BY ';
            $sep = '';
            foreach($orderby as $field => $direction){
                $sql.= $sep.$field.' '. $direction;
                $sep = ',';
            }
        }
        $data  = $this->db->fetchAssoc($sql, $binds);
        if(!$data || !is_array($data) || 0 == count($data)){
            return NULL;
        }else{
            return $this->getModel($data);
        }
    }
    public function findWhere($where, $binds, $orderby = array(), $limit = null, $offset = null){
        $sql = "SELECT * FROM `". $this->table. "` WHERE $where"; 
        if(0 < count($orderby)){
            $sql.= ' ORDER BY ';
            $sep = '';
            foreach($orderby as $field => $direction){
                $sql.= $sep.$field.' '. $direction;
                $sep = ',';
            }
        }
        if (null != $limit){
            $sql.= ' LIMIT '. (int) $limit;
        }
        if (null != $offset){
            $sql.= ' OFFSET '. (int) $offset;
        }
        $aData  = $this->db->fetchAll($sql, $binds);
        if(!$aData){
            return array();
        }else{
            return $this->getModels($aData);
        }
    }
    public function countWhere($where, $binds, $limit=null, $offset=null) {
        $sql = "SELECT count(*) FROM `". $this->table. "` WHERE $where"; 
        if (null != $limit){
            $sql.= ' LIMIT '. (int) $limit;
        }
        if (null != $offset){
            $sql.= ' OFFSET '. (int) $limit;
        }
        return $this->db->fetchColumn($sql, $binds);

    }

    public function insert($model){
        $data = get_object_vars($model);
        if ($this->multilang) {
            $lang = $data['lang'];
            unset($data['lang']);
        }
        $res = $this->db->insert($this->table, $data);
        if('' == $res){
            $model->{$this->primary} = $this->db->lastInsertId();
        }
        if ($this->multilang) {
            foreach ($lang as $clang_id => $ml_mod) {
                $ml_mod->{$this->primary} = $model->{$this->primary};
                $ml_dat = get_object_vars($ml_mod);
                $this->db->insert($this->multilang_table, $ml_dat);
            }
        }
        return $res;
    }

    public function update($model){
        $data = get_object_vars($model);
        if ($this->multilang) {
            $lang = $data['lang'];
            unset($data['lang']);
        }
        $res = $this->db->update($this->table, $data, array($this->primary => $model->{$this->primary}));
        if ($this->multilang) {
            foreach ($lang as $clang_id => $ml_mod) {
                if (0 == intval($ml_mod->{$this->primary})) {
                    $ml_mod->{$this->primary} = $model->{$this->primary};
                    $ml_dat = get_object_vars($ml_mod);
                    $this->db->insert($this->multilang_table, $ml_dat);
                } else {
                    $ml_dat = get_object_vars($ml_mod);
                    $this->db->update($this->multilang_table, $ml_dat, array(
                        $this->primary => $model->{$this->primary},
                        $this->multilang_primary => $ml_mod->{$this->multilang_primary},
                    ));
                }
            }
        }
        return $res;
    }

    public function delete($model){
        if ($this->multilang) {
            $this->db->delete($this->multilang_table, array($this->primary => $model->{$this->primary}));
        }
        return $this->db->delete($this->table, array($this->primary => $model->{$this->primary}));
    }

    public function moveUp($model){
        $id = $model->{$this->primary};
        $where = " 1 ";
        if(null  !== $this->pos_group){
            $id_group = $model->{$this->pos_group};
            $where.= " AND `{$this->pos_group}`=$id_group ";
        }
        // do renumbering first in case somebody deleted or moved an image
        $sql =  "SET @new_pos=0;\n".
          " UPDATE `{$this->table}`".
          " SET `{$this->pos}`=(@new_pos:=@new_pos + 1) WHERE $where".
          " ORDER BY `{$this->pos}` ASC;";
        // set the max pos
        $sql.= "SET @max_pos = (SELECT max({$this->pos}) 
            FROM `{$this->table}` WHERE $where);\n";
        // aktuelle position 
        $sql.= "SET @act_pos = (SELECT max({$this->pos}) 
            FROM `{$this->table}` 
            WHERE `{$this->primary}`=$id);\n";
        // tausch-id
        $sql.= "SET @other_id = (SELECT `{$this->primary}`
            FROM `{$this->table}` 
            WHERE `{$this->pos}`=IF(@act_pos>1, @act_pos-1, 0) LIMIT 1);\n";
        // update sql
        $sql.= "UPDATE `{$this->table}` 
            SET `{$this->pos}`=IF(@act_pos>1,@act_pos-1,@max_pos + 1)
            WHERE `{$this->primary}`= $id;\n";
        // update the previous position
        $sql.= "UPDATE `{$this->table}` SET `{$this->pos}`=@act_pos".
          " WHERE `{$this->primary}`=@other_id;\n";
        // renumber again
        $sql.=  "SET @new_pos=0;\n".
            " UPDATE `{$this->table}`
             SET `{$this->pos}`=(@new_pos:=@new_pos + 1) WHERE $where
             ORDER BY `{$this->pos}` ASC;";
        $this->db->query($sql);
      } 

    public function moveDown($model){
        $id = $model->{$this->primary};
        $where = " 1 ";
        if(null  !== $this->pos_group){
            $id_group = $model->{$this->pos_group};
            $where.= " AND `{$this->pos_group}`=$id_group ";
        }
        $sql =  "SET @new_pos=0;\n".
            " UPDATE `{$this->table}`
             SET `{$this->pos}`=(@new_pos:=@new_pos + 1) WHERE $where
           ORDER BY `{$this->pos}` ASC;";
        // set the max pos
        $sql.= "SET @max_pos = (SELECT max({$this->pos}) 
            FROM `{$this->table}` WHERE $where);\n";
        // aktuelle position 
        $sql.= "SET @act_pos = (SELECT max({$this->pos}) 
            FROM `{$this->table}` 
            WHERE `{$this->primary}`=$id);\n";
        // tausch-id
        $sql.= "SET @other_id = (SELECT `{$this->primary}` 
            FROM `{$this->table}` 
            WHERE $where 
            AND `{$this->pos}`=IF(@act_pos<@max_pos,@act_pos+1,0) LIMIT 1);\n";
        // update sql
        $sql.= "UPDATE `{$this->table}` 
            SET `{$this->pos}`=IF(@act_pos<@max_pos,@act_pos+1,0)".
          " WHERE `{$this->primary}`=$id;\n";
        // update the previous position
        $sql.= "UPDATE `".$this->table."` SET `pos`=@act_pos
          WHERE `{$this->primary}`=@other_id;\n";
        // renumber again
        $sql.=  "SET @new_pos=0;\n".
            " UPDATE `{$this->table}`
            SET `{$this->pos}`=(@new_pos:=@new_pos + 1) 
            WHERE $where
            ORDER BY `{$this->pos}` ASC;";
        $this->db->query($sql);
    }


    public function getModel($data){
        $model = new $this->model();
        foreach($data as $key => $val){
            $model->{$key} = $val;
        }
        if ($this->multilang) {
            $sql = "SELECT * FROM ". $this->multilang_table."
                WHERE ".$this->primary."=?";
            $ml_data = $this->db->fetchAll($sql, array($model->{$this->primary}));
            foreach ($ml_data as $ml_row) {
                $ml_mod = new $this->multilang_model();
                foreach ($ml_row as $ml_key => $ml_val) {
                    $ml_mod->{$ml_key} = $ml_val;
                }
                $model->lang[$ml_mod->{$this->multilang_primary}] = $ml_mod;
            }
            // Prüfen ob alle Sprachen da
            foreach ($this->languages as $clang_id) {
                if (!isset($model->lang[$clang_id])) {
                    $ml_mod = new $this->multilang_model();
                    $ml_mod->{$this->primary} = $model->{$this->primary};
                    $model->lang[$clang_id] = $ml_mod;
                }
            }
        }
        return $model;
    }

    public function getModels($aData){
        $a = array();
        if(is_array($aData)){
            foreach($aData as $data){
                if(is_array($data)){
                    $a[] = $this->getModel($data);
                }
            }
        }
        return $a;
    }
}
