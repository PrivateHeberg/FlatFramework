<?php

class DebugToolBuilder
{
    public $AntiConflitKey;
    private $html;
    private $panel;

    public function __construct()
    {
        $this->AntiConflitKey = Page::randomString(32);
        return $this->AntiConflitKey;
    }

    public function Btn($name, $css, $identifier)
    {
        $cssname = $this->AntiConflitKey . $identifier;

        $this->html .= '
        <div onclick="$(\'.' . $cssname . '\').toggle();  $(\'.panel-' . $this->AntiConflitKey . '\').not(\'.' . $cssname . '\').hide(\'fast\');" class="btn-' . $this->AntiConflitKey . '"  style="' . $css . '">
            ' . $name . '
        </div>';
    }

    public function Panel($identifier, $fx)
    {
        $cssname = $this->AntiConflitKey . $identifier;
        $a = $fx();
        $this->panel .= '<div class="' . $cssname . ' panel-' . $this->AntiConflitKey . '">' . $a . '</div>';
    }

    public function getDebugId()
    {
        return $this->AntiConflitKey;
    }

    public function Result()
    {
        $css = $this->getCSS();
        return $css . '<div class="ToolBar-' . $this->AntiConflitKey . '">' . $this->html . '</div>' . $this->panel;
    }



    private function getCSS()
    {
        return ' <style type="text/css"> @import url(https://fonts.googleapis.com/css?family=Montserrat); @import url(https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css); .ToolBar-' . $this->AntiConflitKey . '{position: fixed; font-family: "Montserrat", sans-serif; bottom: 0; background: #333333; width:100%; height:50px; color:#fff; opacity:0.95; z-index:99999999;}.btn-' . $this->AntiConflitKey . '{position: relative; height:50px; line-height: 50px; padding-left:10px; padding-right:10px; float:left; width:auto; transition: all 0.3s ease; border-right: #2f2f2f solid 1px;}.btn-' . $this->AntiConflitKey . ':hover{background: #3c3c3c; cursor: pointer; border-top: #7AE87A solid 5px;}.panel-' . $this->AntiConflitKey . '{display:none; border: 1px solid #3c3c3c; background:#444; width:1000px; height:300px; padding:10px; border-top-right-radius: 5px; position:fixed; bottom:50px; color:#fff; overflow:auto; word-wrap:break-word; z-index:99999999;}.line-' . $this->AntiConflitKey . '{margin-top : 5px; background : #222; border-radius: 3px; padding: 6px;}.param_sql-' . $this->AntiConflitKey . '{color:lightskyblue;}</style><script type="text/javascript">function includeJs(jsFilePath){var js=document.createElement("script"); js.type="text/javascript"; js.src=jsFilePath; document.body.appendChild(js);}if (!window.jQuery){includeJs("https://code.jquery.com/jquery-3.0.0.min.js");}</script>';
    }
}