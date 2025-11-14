<?php

namespace App\Console\Commands;

use App\Cfdis;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FacturacionController;

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
        $limit = $this->option('limit');
        $id    = $this->option('id');
        $from  = $this->option('from');
        $to    = $this->option('to');

        // Build query
        $facturas = Cfdis::select('id', 'uuid');

        if ($id) {
            $facturas->where('id', $id);
        } elseif ($from && $to) {
            $facturas->whereBetween('id', [$from, $to]);
        }

        if ($limit) {
            $facturas->limit($limit);
        }

        $facturas = $facturas->get();

        if ($facturas->isEmpty()) {
            return $this->info('No hay facturas pendientes por verificar.');
        }

        // Clean XML directory
        $storage_disk_xmls = env('STORAGE_DISK_XML');
        $xmlDir = Storage::disk($storage_disk_xmls)->path('/');

        File::delete(glob($xmlDir . '*.xml'));

        $this->line("\e[34m✔ INICIO → XML eliminados de: $xmlDir\e[0m");

        $FacturacionController = app(FacturacionController::class);

        foreach ($facturas as $factura) {
            try {
                $this->info("Verificando factura ID#{$factura->id} / UUID: {$factura->uuid}");

                $cfdi = $FacturacionController->get_cfdi_status_sat(
                    $factura->id,
                    Request::create('/')
                );

                if (is_array($cfdi) && isset($cfdi['estado'])) {

                    if ($cfdi['estado'] === 'Vigente') {
                        $this->info("🟩 Estado {$factura->uuid}: {$cfdi['estado']}");
                    } else {
                        $this->warn("🟨 Estado {$factura->uuid}: {$cfdi['estado']}");
                    }
                } else {
                    $this->warn(json_encode($cfdi));
                }

                $this->line("\e[35mCFDI: " . json_encode($cfdi) . "\e[0m");

                // Sleep to avoid rate-limiting
                // usleep(500000); // 0.5s
            } catch (\Throwable $e) {
                Log::error("Error verificando {$factura->uuid}: {$e->getMessage()}");
                $this->error("❌ Excepción {$factura->uuid}: " . $e->getMessage());
                sleep(1);
            }
        }
        // FINAL: clean XMLs again
        File::delete(glob($xmlDir . '*.xml'));
        $this->line("\e[34m✔ FIN → XML eliminados de: $xmlDir\e[0m");
        $this->info('Verificación completada.');
    }
}
