<?php
$ERROR_LOGGER_COUNT = 0;
function FrameworkErrorHandler($errno, $errstr, $errfile, $errline)
{
    global $ERROR_LOGGER, $ERROR_LOGGER_COUNT;
    if (!(error_reporting() & $errno)) {
        // Ce code d'erreur n'est pas inclus dans error_reporting()
        return;
    }

    switch ($errno) {
        case E_USER_ERROR:
            $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['error'] = $errstr .' dans le fichier ' . $errfile . ' || Ligne ' . $errline;
            $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['color'] = "#FF0000";

            if(DEBUG == true) {
                echo 'ERREUR CRITIQUE'. $errstr . $errline . $errfile;
            }
            exit(1);
            break;

        case E_USER_WARNING:
            $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['error'] = $errstr .' dans le fichier ' . $errfile . ' || Ligne ' . $errline;
            $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['color'] = "#DF3A01";
            break;

        case E_USER_NOTICE:
            $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['error'] =  $errstr .' dans le fichier ' . $errfile . ' || Ligne ' . $errline;
            $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['color'] = "#FFFF00";
            break;

        default:
            $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['error'] = $errstr .' dans le fichier ' . $errfile . ' || Ligne ' . $errline;
            $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['color'] = "#F7BE81";
            break;
    }
    $ERROR_LOGGER_COUNT++;
    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}
$old_error_handler = set_error_handler("FrameworkErrorHandler");

function frmlogerror($msg, $type) {
    global $ERROR_LOGGER, $ERROR_LOGGER_COUNT;
    $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['error'] = $msg;
    if ($type == 'warning') {
        $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['color'] = "#DF3A01";
    } else {
        $ERROR_LOGGER[$ERROR_LOGGER_COUNT]['color'] = "#FF0000";
    }
    $ERROR_LOGGER_COUNT++;
}