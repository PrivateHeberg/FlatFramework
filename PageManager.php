<?php

class Page {
    private $header_args = array();
    private $footer_args = array();
    private $enableHeader = true; //Active le Footer (default : true)
    private $enableFooter = true; //Active le header (default : true)
    private $requireConnect = true; //Nessesite d'etre connecté (default: true)
    private $array = array();
    private $log;

    /**
     * Récupére l'objet NoORM databse
     * @param $dbid Id de la database
     * @return object NoORM databse
     */
    public static function getDatabase($dbid = 0) {
        global $PDO, $PDOCACHE;


        if(isset($PDOCACHE[$dbid])) {
            Page::Looger("GETTING DATABASE CONNEXION WITH CACHE", 1);
            return $PDOCACHE[$dbid];
        } else {
            $PDOCACHE[$dbid] = new NotORM($PDO[$dbid]);
            return $PDOCACHE[$dbid];
        }

    }

    public static function  randomString($length = 16)
    {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    /**
     * Récupére l'objet Email
     * @param $dbid Id du compte email
     * @return object NoORM databse
     */
    public static function getEmail($mailid = 0) {
        global $PHPMAILER;
        Page::Looger("GET EMAIL CONNEXION DIRECTLY", 1);
       return  new EmailSender($PHPMAILER[$mailid]['host'],$PHPMAILER[$mailid]['port'], $PHPMAILER[$mailid]['email'], $PHPMAILER[$mailid]['passwd'], $PHPMAILER[$mailid]['name']);;
    }

    /**
     * Transforme une url relative en url physique
     * @param $uri Url relative
     * @return string Retourne l'url physique
     */
    public static function urlMapping($uri = "") {
        return URL_WEBSITE . '/' . $uri;
    }

    public function render($link) {
        Page::Looger("STARTING RENDER", 1);
        if ($this->requireConnect == true) {
            Page::Looger("THIS PAGE REQUIRE CONNEXION", 1);
            if (!isset($_SESSION['id'])) {
                Page::Looger("ERROR USER IS NOT CONNECTED", 1);
                header('Location: /' . CONNEXION_PAGE);
                exit();
            }
        }

        $content = null;
        $fichier = $this->traitement($this->GetPageData($link));;
        $content .= $this->GetHeader();
        $content .= $fichier;
        $content .= $this->GetFooter();

        if (DEBUG == true) {
            $debugger = new DebugManager();
            $content .= $debugger->ToolBar();
        }
        Page::Looger("RENDER FLUSH", 1);
        return $content;

    }

    private function traitement($vue) {
        Page::Looger("PREPARE TO GENERATE FINAL RENDING", 1);
        $templateEngine = new TemplateEngine($vue, $this->array);

        Page::Looger("ANALYSIS VAR", 1);
        $templateEngine->F_var();
        Page::Looger("ANALYSIS ASSET AND URL", 1);
        $templateEngine->F_asset_url();
        Page::Looger("ANALYSIS USER", 1);
        $templateEngine->F_user();
        Page::Looger("ANALYSIS IF", 1);
        $templateEngine->F_if();
        Page::Looger("ANALYSIS FOREACH", 1);
        $templateEngine->F_foreach();


        Page::Looger("RENDER PAGE", 1);
        return $templateEngine->Result();

        Page::Looger("RENDER IS FINISHED", 1);
    }

    private function GetPageData($link) {
        /** Path ou sont stocké les vues */
        $file_path = __DIR__ . '/../../Vue/' . $link;

        /** Path ou sont stocké les vues en cache */
        $file_path_cached = __DIR__ . '/Cache/' . md5($link) . '.tmp';

        /** On récupére le gestionaire de mémoire */
        $MemoryManager = new MemoryManager();

        /** Si la vue existe */
        if (file_exists($file_path)) {
            if(NoCache == true) {
                return file_get_contents($file_path);
            }
            if (DEBUG == true) {
                Page::Looger("DEBUG MOD ENABLED : DIRECTLY GETTING VIEW", 1);
                $fichier = file_get_contents($file_path);
            } else {
                if ($MemoryManager->exist(md5($link))) {
                    Page::Looger("LOADING PAGE FROM RAM", 1);
                    $fichier = $MemoryManager->GetOnMemory(md5($link));
                } elseif (file_exists($file_path_cached)) {
                    Page::Looger("LOADING PAGE FROM FILE CACHE", 1);
                    $fichier = file_get_contents($file_path_cached);
                    $MemoryManager->SetOnMemory(md5($link), MinifyEngine::Minify($fichier));
                } else {
                    Page::Looger("CACHE IS NOT CREATED ", 1);
                    Page::Looger("LOADING FROM ORIGINAL FILE", 1);
                    Page::Looger("MINIFY FILE", 1);
                    Page::Looger("SET VIEW ON CACHE MEMORY", 1);
                    Page::Looger("SET VIEW ON CACHE HDD", 1);
                    $fichier = MinifyEngine::Minify(file_get_contents($file_path));
                    $MemoryManager->SetOnMemory(md5($link), $fichier);
                    $handle = fopen($file_path_cached, "w+");
                    fwrite($handle, $fichier);
                }
            }
        } else {

            header('Location:  /404');
            exit();
        }

        return $fichier;
    }

    public static function Looger($string, $log_type = 0) {
            global $ERROR_LOGGER_GLOBAL;
        if($log_type == 1) //PAGE LOADING LOG
    $ERROR_LOGGER_GLOBAL[] = '<div style="color:deepskyblue;display:inline;">'.$string.'</div>';
        elseif($log_type == 2) //IS A SQL LOG
            $ERROR_LOGGER_GLOBAL[] = '<div style="color:greenyellow;display:inline;">'.$string.'</div>';
        elseif($log_type == 3) //IS A SQL LOG
            $ERROR_LOGGER_GLOBAL[] = '<div style="color:darksalmon;display:inline;">'.$string.'</div>';
    }

    private function GetHeader() {
        $content = null;
        if ($this->enableHeader == true) {
            $obj = new Header();
            $new_array = array_merge($this->header_args, $this->array);
            $content = call_user_func_array(array($obj, "Main"), array($new_array));
        }
        return $content;
    }

    private function GetFooter() {
        $content = null;
        if ($this->enableFooter == true) {
            $obj = new Footer();
            $new_array = array_merge($this->footer_args, $this->array);
            $content = call_user_func_array(array($obj, "Main"), array($new_array));
        }
        return $content;
    }

    public function isLogged() {
        if (isset($_SESSION['id'])) return true; else return false;
    }

    public function Config_Page($page) {
        if (isset($page['header_args']) and isset($page['footer_args']) and isset($page['enableHeader']) and isset($page['enableFooter']) and isset($page['requireConnect'])) {

            $this->header_args = $page['header_args'];
            $this->footer_args = $page['footer_args'];
            $this->enableHeader = $page['enableHeader'];
            $this->enableFooter = $page['enableFooter'];
            $this->requireConnect = $page['requireConnect'];
            if($page['requireConnect']) {
                if(!isset($_SESSION['id'])) {
                    exit();
                }
            }
        } else {
            echo 'Les varibles de configuration de la page ne sont pas tous initialisé';
        }
    }
    public function getUserInfo($info) {
        if (isset($_SESSION['id'])) {
            $db = Page::getDatabase(0);
            $dbname = USER_DATABASE_NAME;
            $user_data = $db->$dbname()->select('*')->where('id = ?', $_SESSION['id'])->fetch();
            return $user_data[$info];
        }
    }

    public function setData($key, $data, $isHTML = 0) {
        if($isHTML == 0)
            $this->array[$key] = $data;
        else {
            $this->array[$key] = MinifyEngine::Minify($data);
        }
    }

    public function renderOne($link) {
        $content = null;

        $content = $this->traitement($this->GetPageData($link));;

        return $content;
    }
    
}

