<?php

namespace Tests\Unit;

use App\Services\MercadoPublico\Mutex\Mutex;
use RuntimeException;
use Tests\TestCase;

class MutexTest extends TestCase
{

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_Should_ThrowRuntimeException_When_FileIsAlreadyLocked()
    {
        $this->expectException(RuntimeException::class);
        
        $mutex1 = new Mutex();
        $mutex2 = new Mutex();
        $mutex1->bloquear();
        $mutex2->bloquear();
    }
}
