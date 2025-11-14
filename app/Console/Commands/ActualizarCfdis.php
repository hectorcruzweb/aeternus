<?php

namespace App\Console\Commands;

use App\Cfdis;
use App\Http\Controllers\FacturacionController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ActualizarCfdis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facturas:verificar {--limit=} {--id=} {--from=} {--to=}';

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
        $limit    = $this->option('limit');
        $id    = $this->option('id');
        $from    = $this->option('from');
        $to    = $this->option('to');
        $facturas = Cfdis::select('id', 'uuid');
        if (!empty($id)) {
            $facturas = $facturas->where('id', $id);
        } else {
            if (!empty($to) && !empty($from)) {
                $facturas = $facturas->where('id', '>=', $from)->where('id', '<=', $to);
            }
        }
        if (!empty($limit)) {
            $facturas = $facturas->limit($limit);
        }
        $facturas = $facturas->get();
        if ($facturas->isEmpty()) {
            return $this->info('No hay facturas pendientes por verificar.');
        }
        $FacturacionController = new FacturacionController();
        foreach ($facturas as $factura) {
            try {
                $this->info("Verificando factura ID#{$factura->id} / UUID: {$factura->uuid}");
                $cfdi = $FacturacionController->get_cfdi_status_sat($factura->id, Request::create('/'));
                if (is_array($cfdi) && isset($cfdi['estado'])) {
                    if ($cfdi['estado'] === 'Vigente') {
                        $this->info("✅ Estado de {$factura->uuid}: " . ($cfdi['estado']));
                    } else {
                        $this->warn("✅ Estado de {$factura->uuid}: " . ($cfdi['estado']));
                    }
                } else {
                    $this->warn(json_encode($cfdi));
                }
                $cfdi = json_encode($cfdi);
                $this->line("\e[35m CFDI: '{$cfdi}' \e[0m");
                // Evitar saturar el servicio
                sleep(.5); // 1.5 segundos de pausa entre peticiones
            } catch (\Throwable $e) {
                Log::error("Error verificando {$factura->uuid}: " . $e->getMessage());
                $this->error("❌ Excepción con factura {$factura->uuid}: " . $e->getMessage());
                sleep(3); // pequeña pausa antes de continuar
            }
        }
        $this->info('Verificación completada.');
    }
}
