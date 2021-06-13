<?php

namespace App\Services\MercadoPublico\Helpers;

use App\Services\MercadoPublico\Settings\ClassLoaderSettings;

class ClassLoaderHelper {
    private $classLoaderSettings;

    public function __construct() {
        $this->classLoaderSettings = app(ClassLoaderSettings::class);
    }

    public function getClasses($classGroup) {
        return explode(';', $this->classLoaderSettings->$classGroup);
    }

    public function loadClasses($classes, $namespace) {
        $classesArray = [];

        foreach ($classes as $class) {
            $className = $namespace . $class;

            if(class_exists($className)) {
                $classesArray[] = new $className;
            }
        }

        return $classesArray;
    }
}
