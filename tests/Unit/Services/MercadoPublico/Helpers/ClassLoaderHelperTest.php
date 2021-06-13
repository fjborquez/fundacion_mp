<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Helpers\ClassLoaderHelper;
use Tests\TestCase;

class ClassLoaderHelperTest extends TestCase
{
    protected $classLoaderHelper;

    public function setUp(): void
    {
        parent::setUp();
        $this->classLoaderHelper = new ClassLoaderHelper();
    }

    public function test_Should_ReturnArrayLengthOne_When_ClassExists()
    {
        $classArray = $this->classLoaderHelper->loadClasses(['Exception'], '\\');

        $this->assertIsArray($classArray);
        $this->assertCount(1, $classArray);
    }

    public function test_Should_ReturnEmptyArray_When_ClassNotExists()
    {
        $classArray = $this->classLoaderHelper->loadClasses(['Exception'] , 'App\\');

        $this->assertIsArray($classArray);
        $this->assertCount(0, $classArray);
    }

    public function test_Should_ReturnArrayLengthOne_When_OneClassExists()
    {
        $classArray = $this->classLoaderHelper->loadClasses(['Exception', 'Prueba'], '\\');

        $this->assertIsArray($classArray);
        $this->assertCount(1, $classArray);
    }
}
