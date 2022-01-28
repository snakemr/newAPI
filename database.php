<?php

return new mysql_connection('newapi');

class mysql_connection
{
    private $mysqli;
    function __construct($database, $password='', $user='root', $host='localhost') {
        $this->mysqli = mysqli_connect($host, $user, $password, $database)
            or die(json_encode(array('errno' => mysqli_connect_errno(), 'error' => mysqli_connect_error())));
    }
    function __destruct() {
        //echo "~db";
        if($this->mysqli) mysqli_close($this->mysqli);
    }
    function query($query) {
        $result = mysqli_query($this->mysqli, $query)
            or die(json_encode(array('errno' => mysqli_errno($this->mysqli), 'error' => mysqli_error($this->mysqli))));
        return new mysql_connection_result($result);
    }
    private function stmt($query, $types, ...$params) {
        $stmt = mysqli_prepare($this->mysqli, $query)
            or die(json_encode(array('errno' => mysqli_errno($this->mysqli), 'error' => mysqli_error($this->mysqli))));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt)
            or die(json_encode(array('errno' => mysqli_stmt_errno($stmt), 'error' => mysqli_stmt_error($stmt))));
        return $stmt;
    }
    function query_($query, $types, ...$params): mysql_connection_result {
        $stmt = $this->stmt($query, $types, ...$params);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        return new mysql_connection_result($result);
    }
    function exec($query, $types, ...$params) {
        $stmt = $this->stmt($query, $types, ...$params);
        $result = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
    function json($query) {
        return $this->query($query)->json();
    }
    function json_($query, $types, ...$params) {
        return $this->query_($query, $types, ...$params)->json();
    }
    function xml($query, $rootTag = 'root', $keyTag = 'item') {
        return $this->query($query)->xml($rootTag, $keyTag);
    }
    function xml_($query, $rootTag, $keyTag,  $types, ...$params) {
        return $this->query_($query, $types, ...$params)->xml($rootTag, $keyTag);
    }
}

class mysql_connection_result {
    private $result;
    function __construct($result) {
        $this->result = $result;
    }
    function __destruct() {
        //echo "~res";
        if($this->result) mysqli_free_result($this->result);
    }
    function rows() {
        return mysqli_num_rows($this->result);
    }
    function row() {
        return mysqli_fetch_assoc($this->result);
    }
    function json() {
        $response = array();
        while ($rec = mysqli_fetch_assoc($this->result))
            $response[] = $rec;
        return json_encode($response);
    }
    function xml($rootTag = 'root', $keyTag = 'item') {
        $response = array();
        while ($rec = mysqli_fetch_assoc($this->result))
            $response[] = $rec;
        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" ?><$rootTag/>");
        $this->to_xml($xml, $response, $keyTag);
        return $xml->asXML();
    }
    private function to_xml(SimpleXMLElement $object, array $data, $keyTag = null) {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) $key = $keyTag ?: 'key_' . $key;
            if (is_array($value)) {
                $new_object = $object->addChild($key);
                $this->to_xml($new_object, $value, $keyTag);
            } else {
                $object->addChild($key, $value);
            }
        }
    }
}

function header_json() {
    header('Content-Type: application/json; charset=utf-8');
}
function header_xml() {
    header('Content-Type: application/xml; charset=utf-8');
}
function header_error($code=400) {
    switch ($code) {
        case 401: header("HTTP/1.1 401 Unauthorized"); break;
        case 402: header("HTTP/1.1 402 Payment Required"); break;
        case 403: header("HTTP/1.1 403 Forbidden"); break;
        case 404: header("HTTP/1.1 404 Not Found"); break;
        case 405: header("HTTP/1.1 405 Method Not Allowed"); break;
        case 406: header("HTTP/1.1 406 Not Acceptable"); break;
        case 407: header("HTTP/1.1 407 Proxy Authentication Required"); break;
        case 408: header("HTTP/1.1 408 Request Timeout"); break;
        case 409: header("HTTP/1.1 409 Conflict"); break;
        default: header("HTTP/1.1 400 Bad Request");
    }
}

function decoded_json() {
    if (substr($_SERVER["CONTENT_TYPE"],0,16) != 'application/json') {
        header_error(); die();
    }
    return json_decode( file_get_contents('php://input'), true);
}
