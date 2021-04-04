<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Etl\MercadoPublicoETL;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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
