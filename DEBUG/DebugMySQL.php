<?php

/**
 * Created by PhpStorm.
 * User: Marc Moreau
 * Date: 03/06/2016
 * Time: 01:40
 */
class DebugMySQL {
    private $errorCount = 0;
    private $warningCount = 0;
    private $nbRequest = 0;
    private $returnArray;

    public function __construct() {
        global $GLOBAL_QUERY, $GLOBAL_QUERY_ARG, $GLOBAL_QUERY_TIME, $GLOBAL_QUERY_ERROR;
        $RETURNED = null;
        if (is_array($GLOBAL_QUERY)) {

            foreach ($GLOBAL_QUERY as $query) {
                $RETURNED['request'][$this->nbRequest]['query'] = $query;
                $RETURNED['request'][$this->nbRequest]['args'] = $GLOBAL_QUERY_ARG[$this->nbRequest];
                $final_query = $this->emulatePrepare($query, $GLOBAL_QUERY_ARG[$this->nbRequest]);
                $RETURNED['request'][$this->nbRequest]['final_query'] = $final_query;

                if ($GLOBAL_QUERY_ERROR[$this->nbRequest] == false) {
                    $RETURNED['request'][$this->nbRequest]['time'] = $GLOBAL_QUERY_TIME[$this->nbRequest];
                    $RETURNED['request'][$this->nbRequest]['error'] = false;
                    $warning = $this->warningChecker($final_query);
                    if ($warning != false) {
                        $RETURNED['request'][$this->nbRequest]['warning'] = $warning;
                        $this->warningCount++;
                    } else {
                        $RETURNED['request'][$this->nbRequest]['warning'] = false;
                    }
                } else {

                    $RETURNED['request'][$this->nbRequest]['time'] = 0;
                    $RETURNED['request'][$this->nbRequest]['error'] = $GLOBAL_QUERY_ERROR[$this->nbRequest];
                    $this->errorCount++;
                }
                $this->nbRequest++;
            }
        }

        $RETURNED['global']['countError'] = $this->errorCount;
        $RETURNED['global']['countWarning'] = $this->warningCount;
        $RETURNED['global']['countRequest'] = $this->nbRequest;

        $this->returnArray = $RETURNED;
    }

    public function getDebugger() {
        return $this->returnArray;
    }

    private function emulatePrepare($request, $args) {
        $nombre_argument = substr_count($request, "?");
        $argument = 0;
        if ($nombre_argument == 1) {
            return str_replace("?", '`' . $args[0] . '`', $request);
        } elseif ($nombre_argument >= 2) {
            while (count($args) != $argument) {
                return substr_replace($request, '`' . $args[$argument] . '`', strpos($request, '?'), 1);
                $iarg++;
            }
        } else {
            return $request;
        }
        return '';
    }

    private function warningChecker($final_query) {
        if (strstr(strtolower($final_query), 'count(*)')) {
            return 'Utilisé count(\'champ\') au lieu de count(\'*\')';
        }

        return false;
    }
}