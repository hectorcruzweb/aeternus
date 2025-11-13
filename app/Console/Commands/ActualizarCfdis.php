<?php

namespace App\Console\Commands;

use App\Cfdis;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\FacturacionController;

class ActualizarCfdis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facturas:verificar {--limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica el estatus de facturas con el proveedor externo.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        parent::__construct();
    }*/

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $facturas = Cfdis::select('id', 'uuid')->where('status', '<>', 0)
            //->limit($limit)
            ->get();
        if ($facturas->isEmpty()) {
            return $this->info('No hay facturas pendientes por verificar.');
        }
        $FacturacionController = new FacturacionController();
        foreach ($facturas as $factura) {
            try {
                $this->info("Verificando factura ID#{$factura->id} / UUID: {$factura->uuid}");
                $cfdi = $FacturacionController->get_cfdi_status_sat($factura->id, Request::create('/'));
                $this->info('CFDI: ' . json_encode($cfdi));
                // Evitar saturar el servicio
                sleep(1.5); // 1.5 segundos de pausa entre peticiones
            } catch (\Throwable $e) {
                Log::error("Error verificando {$factura->uuid}: " . $e->getMessage());
                $this->error("❌ Excepción con factura {$factura->uuid}");
                sleep(3); // pequeña pausa antes de continuar
            }
        }
        $this->info('Verificación completada.');
    }
}
