<?php

class MemoryManager {
    public function __construct() {
    }

    /**
     * Stock une variable en ram pour un accées haute performance
     * @param $key nom de la variable
     * @param $data contenu de la variable
     */
    public function SetOnMemory($key, $data) {
        if (MEMORY_CACHED_SYSTEM == 'apcu') apcu_add($key, $data, 3600);

        if (MEMORY_CACHED_SYSTEM == 'apc') apc_store($key, $data, 3600);

        if (MEMORY_CACHED_SYSTEM == 'session') $_SESSION['PHCache']['varcached'][$key] = $data;
    }

    public function exist($key) {
        $data = $this->GetOnMemory($key);
        if (empty($data)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Récupére une variable en ram
     * @param $key nom de la variable a récupéré
     * @return mixed
     */
    public function GetOnMemory($key) {
        if (MEMORY_CACHED_SYSTEM == 'apcu') return apcu_fetch($key);

        if (MEMORY_CACHED_SYSTEM == 'apc') return apc_fetch($key);

        if (MEMORY_CACHED_SYSTEM == 'session') {
            if (isset($_SESSION['PHCache']['varcached'][$key])) {
                return $_SESSION['PHCache']['varcached'][$key];
            } else {
                return;
            }
        }
    }
}