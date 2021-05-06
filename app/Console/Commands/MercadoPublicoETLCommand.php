<?php

namespace App\Console\Commands;

use App\Services\MercadoPublico\MercadoPublicoETL;
use Illuminate\Console\Command;

class MercadoPublicoETLCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpetl:generar {--sendToSalesforce}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta ETL de Mercado Publico';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(MercadoPublicoETL $mercadoPublicoETL)
    {
        $sendToSalesforce = $this->option('sendToSalesforce');
        $mercadoPublicoETL->generarETL($sendToSalesforce);
        return 0;
    }
}
