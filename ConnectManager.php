<?php

class ConnectManager {
    public static function mySQL($username, $passwd, $database, $host = '127.0.0.1') {
        try {

            return new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $username, $passwd, array(PDO::ATTR_TIMEOUT => 1, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
            echo "Erreur MYSQL Connection " . $username . "@" . $host . ' for db '.$database.'<br>';
            if (DEBUG == true) echo $e;
            exit();
        }
    }
    public static function ssh() {

    }
    public static function email($host,$port, $email, $passwd, $name) {

        $RETURN['host'] = $host;
        $RETURN['port'] = $port;
        $RETURN['email'] = $email;
        $RETURN['passwd'] = $passwd;
        $RETURN['name'] = $name;
        return $RETURN;
        
    }
}