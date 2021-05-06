<?php

namespace App\Services\MercadoPublico\Mutex;

use RuntimeException;

class Mutex {
    private const ARCHIVO_BLOQUEO = 'framework/locks/etlmp.txt';

    private $file;

    public function __construct() {
        $this->file = fopen(storage_path(self::ARCHIVO_BLOQUEO), 'r+');
    }

    public function bloquear() {
        if (!flock($this->file, LOCK_EX | LOCK_NB)) {
            fclose($this->file);
            throw new RuntimeException('Una ejecución del proceso ya está en curso');
        }
    }

    public function desbloquear() {
        flock($this->file, LOCK_UN);
        fclose($this->file);
    }
}
