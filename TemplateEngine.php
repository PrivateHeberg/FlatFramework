<?php

class TemplateEngine
{
    private $Model;
    private $Render;

    public function __construct($Vue, $Model)
    {
        $this->Model = $Model;
        $this->Render = $Vue;
    }

    public function F_var()
    {
        if (count($this->Model) >= 1) {
            foreach ($this->Model as $key => $data) {
                if (!is_array($data)) {
                    $this->Render = str_replace("{{ $key }}.lower", $data, strtoupper($this->Render));
                    $this->Render = str_replace("{{ $key }}.upper", $data, strtolower($this->Render));
                    $this->Render = str_replace("{{ $key }}.fl_upper", $data, ucfirst($this->Render));
                    $this->Render = str_replace("{{ $key }}", $data, $this->Render);
                }
            }
        }
    }

    public function F_asset_url()
    {
        $this->Render = preg_replace("/{{ asset\('(.*)'\) }}/U", Page::urlMapping("$1"), $this->Render);
        $this->Render = preg_replace("/{{ url\('(.*)'\) }}/U", Page::urlMapping("$1"), $this->Render);
    }

    public function F_user()
    {
        $this->Render = preg_replace_callback("/{{ user\('(.*)'\) }}/U", function ($matches) {

            $key = $matches[0];
            if (isset($_SESSION['id'])) {
                $key = str_replace("{{ user('", '', $key);
                $key = str_replace("') }}", '', $key);
                $db = Page::getDatabase(0);
                $dbname = USER_DATABASE_NAME;
                $user_data = $db->$dbname()->select('*')->where('id = ?', $_SESSION['id'])->fetch();

                return $user_data[$key];
            } else
                return null;
        }, $this->Render);
    }

    public function F_if()
    {
        $i = 0;

        preg_match_all("/{{ if\((.*)\) }}(.*){{ endif }}/SsU", $this->Render, $matches, null, 0);
        if (count($matches[0]) != 0) {
            while (count($matches[0]) != $i) {
                $executeif = false;
                $matches[1][$i]; //Args du if
                $matches[2][$i]; //matchable content :)

                if ($matches[1][$i] == '!isConnected()') {
                    if (!isset($_SESSION['id'])) {
                        $executeif = true;
                    }
                } elseif ($matches[1][$i] == 'isConnected()') {

                    if (isset($_SESSION['id'])) {
                        $executeif = true;
                    }
                } else {
                    $var = $matches[1][$i];
                    if (isset($this->Model[$var])) {

                        if ($matches[1][$i][0] == '!') {
                            if ($this->Model[$var] == false) {
                                $executeif = true;
                            }
                        } else {
                            if ($this->Model[$var] == true) {
                                $executeif = true;
                            }
                        }
                    } else {

                        frmlogerror('Erreur la valeur ' . $var . ' n\'existe pas dans if(' . $var . ')', "warning");
                    }
                }

                if ($executeif) {
                    $this->Render = str_replace($matches[0][$i], $matches[2][$i], $this->Render);
                } else {
                    $this->Render = str_replace($matches[0][$i], '', $this->Render);
                }
                $i++;
            }
        }
    }

    public function F_foreach()
    {
        // Détéction du foreach
        preg_match_all("/{{ foreach\((.*) as (.*)\) }}(.*){{ endforeach }}/SsU", $this->Render, $matches, null, 0);
        $nombre_foreach = count($matches[0]);
        if ($nombre_foreach >= 2) {
            //plusieurs

        } elseif ($nombre_foreach != 0) {
            $monforeach = $matches[0][0];
            $varname = $matches[1][0];

            $var_dest = $matches[2][0];
            $html = $matches[3][0];

            $render_foreach = null;

            if (is_array($this->Model[$varname])) {
                foreach ($this->Model[$varname] as $acctable) {
                    $html_new = $html;
                    foreach ($acctable as $key => $data) {
                        if (is_array($acctable)) {
                            if (isset($acctable[$key])) {
                                $html_new = str_replace('{{ ' . $var_dest . '.' . $key . ' }}', $acctable[$key], $html_new);
                            }
                        } else {
                            frmlogerror('Erreur vous utilisé foreach alors que votre variable n\'est pas un array dans {{ foreach(' . $varname . ' as ' . $var_dest . ') }} ', "error");
                        }
                    }
                    $render_foreach .= $html_new;
                }
                $this->Render = str_replace($monforeach, $render_foreach, $this->Render);
                $this->Render;
            }
        }
    }

    public function Result()
    {
        return $this->Render;
    }
}