<?php
class Dbconfig_Mysql {
    protected $ServerName;
    protected $ServerUserName;
    protected $ServerUserPassword;
    protected $ServerDb;

    function Dbconfig_Mysql() {
        $this -> ServerName = 'localhost';
        $this -> ServerUserName = 'root';
        $this -> ServerUserPassword = 'sacred';
        $this -> ServerDb = 'fcpc_school_portal';
    }
}
class Dbconfig_Mysqli {
    protected $ServerName;
    protected $ServerUserName;
    protected $ServerUserPassword;
    protected $ServerDb;

    function Dbconfig_Mysqli() {
        $this -> ServerName = 'localhost';
        $this -> ServerUserName = 'root';
        $this -> ServerUserPassword = 'sacred';
        $this -> ServerDb = 'fcpc_school_portal';
    }
}
?>