<?php
if(!defined('_CODE')) {
    die('Access denied...');
}

function query($sql, $data = [], $check = false) {
    
    global $conn;
    $result = false;
    
    
    try{
        $statement = $conn -> prepare($sql);
        if(!empty($data)) {
          $result = $statement -> execute($data);

        }
        else {
            $result = $statement -> execute();
        }

    } catch(Exception $exp) {
        echo $exp -> getMessage().'<br>';
        echo 'Filde: '. $exp -> getFile().'<br>';
        echo 'Line: '. $exp -> getLine().'<br>';
        die();
        
    }
    if($check) {
        return $statement;
    }
    return $result;
}

//insert func
function insert($table, $data) {
    
    $keys = array_keys($data);
    $attrs = implode(',', $keys);
    $values = ':'. implode(',:', $keys);

    $sql = 'INSERT INTO '. $table. '('. $attrs .')'. ' VALUES('. $values. ')';
    
    $result = query($sql, $data);
    return $result;
}
//update func
function update($table, $data, $condition='') {
    $update = '';
    foreach ($data as $key => $value) {
        $update .= $key. '='. ':'.$key. ',';
    }
    $update = trim($update, ',');
    if(!empty($condition)) {
        $sql = 'UPDATE '. $table. ' SET '. $update. ' WHERE '. $condition;
    }
    else {
        $sql = 'UPDATE '. $table. 'SET '. $update;
    }
    $result = query($sql, $data);
    return $result;

}
//delete func
function delete($table, $condition='') {
    if(empty($condition)) {
        $sql = 'DELETE FROM '. $table ;
    }
    else {
        $sql = 'DELETE FROM '. $table. ' WHERE '. $condition ;

    }
    $result= query($sql);
    return $result;
}
// Get Raws
function getRaws($sql) {
    $result = query($sql,'',true);
    if(is_object($result)) {
        $dataFetch = $result -> fetchALL(PDO::FETCH_ASSOC);
        
    }
    return $dataFetch;
}
//Get 1 Raw
function getRaw($sql) {
    $result = query($sql,'',true);
    if(is_object($result)) {
        $dataFetch = $result -> fetch(PDO::FETCH_ASSOC);
        
    }
    return $dataFetch;
}
//Count Raw
function countRow($sql) {
    $result = query($sql,'',true);
    if(!empty($result)) {
        return $result -> rowCount(); 
        
    }
    
}

