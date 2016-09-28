<?php

/**
 * Created by PhpStorm.
 * User: Marc Moreau
 * Date: 30/05/2016
 * Time: 18:10
 */
class MinifyEngine {
    public static function JS_PAGE($html) {

        header('Content-Type: text/javascript');
        $memoryManager = new MemoryManager();
        $html = str_replace('\\', '/', $html);
        $content = file_get_contents(__DIR__ . '/../../Web' . $html . '.js');

        if (DEBUG == true) {
            $content = "//LoadHDD | DEBUG MODE \n" . $content;
        } else {
            if ($memoryManager->exist(md5($html))) {
                $content = $memoryManager->GetOnMemory(md5($html));
            } else {
                $mini = MinifyEngine::Minify($content);
                $memoryManager->SetOnMemory(md5($html), $mini);
                $content = $mini;
            }
        }

        echo $content;
    }

    public static function Minify($html) {

        $search = array('/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s', // shorten multiple whitespace sequences
            '/> </s', '/<!--(.*)-->/Uis');

        $replace = array('>', '<', '\\1', '><', '');

        return preg_replace($search, $replace, $html);
    }

    public static function CSS_PAGE($html) {
        header('Content-Type: text/css');
        $memoryManager = new MemoryManager();
        $html = str_replace('\\', '/', $html);
        $content = file_get_contents(__DIR__ . '/../../Web' . $html . '.css');

        if(NoCache == true) {
            echo $content;
            return;
        }
        if (DEBUG == true) {
            echo $content;
            return;
        } else {
            if ($memoryManager->exist(md5($html))) {
                echo $memoryManager->GetOnMemory(md5($html));
                return;
            } else {
                $mini = MinifyEngine::Minify($content);
                $memoryManager->SetOnMemory(md5($html), $mini);
                echo $mini;
                return;
            }
        }

    }
}