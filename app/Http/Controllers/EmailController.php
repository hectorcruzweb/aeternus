<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmailController extends ApiController
{
    /**
     * Reportes del dashboard con mensaje opcional y adjunto Excel/PDF.
     * Reutiliza remitente, datos de empresa y mails.mail. No altera pdf_email.
     * El módulo llamante valida permisos, destinatario, asunto y longitud del mensaje.
     */
    public function reporte_email(array $datos, $contenido, $nombreArchivo, $mime)
    {
        $empresa = app(EmpresaController::class)->get_empresa_data();
        $destinatarios = $datos['destinatarios'] ?? [['email' => $datos['email_address'], 'nombre' => $datos['destinatario']]];
        // Mantener el saludo del reporte; solo sustituir su destino durante las pruebas.
        $nombreSaludo = count($destinatarios) === 1 ? ($destinatarios[0]['nombre'] ?? '') : '';
        // app.env viene de APP_ENV y respeta config:cache. Únicamente production envía a clientes.
        // Se reemplaza la lista completa: ningún destinatario capturado queda en To/CC/BCC.
        // Esta regla solo afecta al envío nuevo de reportes; pdf_email conserva su flujo.
        if (config('app.env') !== 'production') {
            $destinatarios = [['email' => 'hectorcrzprz@gmail.com', 'nombre' => 'Héctor']];
        }
        $data = [
            'email' => $destinatarios[0]['email'], 'client_name' => $nombreSaludo,
            'subject' => $datos['asunto'], 'mensaje_html' => $this->mensajeReporteSeguro($datos['mensaje'] ?? ''),
            'address' => config('services.mailsistema.address'),
            'empresa' => $empresa, 'name_pdf' => $nombreArchivo,
        ];
        Mail::send('mails.mail', $data, function ($message) use ($empresa, $data, $contenido, $nombreArchivo, $mime, $destinatarios) {
            $message->from($data['address'], strtoupper($empresa->nombre_comercial));
            if (count($destinatarios) === 1) {
                $message->to($destinatarios[0]['email'], $destinatarios[0]['nombre'] ?? '');
            } else {
                // Copia oculta para no compartir correos de clientes entre destinatarios.
                foreach ($destinatarios as $persona) $message->bcc($persona['email'], $persona['nombre'] ?? '');
            }
            $message->subject($data['subject']);
            $message->attachData($contenido, $nombreArchivo, ['mime' => $mime]);
        });
        if (Mail::failures()) return $this->errorResponse('Ocurrió un error al enviar el correo.', 500);
        return $this->successResponse(1, 200);
    }

    /** HTML permitido del editor; elimina scripts, imágenes y atributos peligrosos. */
    private function mensajeReporteSeguro($mensaje)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('Cache.DefinitionImpl', null);
        $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,ol,ul,li,a[href|title]');
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
        $purificador = new \HTMLPurifier($config);
        return $purificador->purify($mensaje);
    }

    /**
     * parameters lista de la funcion
     * to destinatario
     * to_name nombre del destinatario
     * subject motivo del correo
     * name_pdf nombre del pdf
     * pdf archivo pdf a enviar
     */
    public function pdf_email($to = '', $to_name = '', $subject = '', $name_pdf = '', $pdf = null, $type = 'default', $id = '')
    {
        /**validar que el correo tiene formato correcto */

        $request = new Request([
            'email' => $to,
        ]);

        $this->validate($request, [
            'email' => 'required|email',
        ], [
            'email'    => 'El email debe ser un correo válido.',
            'required' => 'Ingrese un email válido.',
        ]);

        $get_funeraria       = new EmpresaController();
        $empresa             = $get_funeraria->get_empresa_data();
        $data["email"]       = $to;
        $data["client_name"] = $to_name;
        $data["subject"]     = $subject;
        $data["address"]     = config('services.mailsistema.address');
        $data["empresa"]     = $empresa;
        $data["name_pdf"]    = $name_pdf;
        try {
            //return view('mails.mail', $data);
            Mail::send('mails.mail', $data, function ($message) use ($empresa, $data, $name_pdf, $pdf, $type, $id) {
                $message->from($data["address"], strtoupper($empresa->nombre_comercial));

                /**archivos adjuntos */
                $message
                    ->to($data["email"], $data["client_name"])
                    ->subject($data["subject"]);

                $message->attachData($pdf->output(), $name_pdf);

                if ($type == 'cfdi') {
                    $storage_disk_xmls = ENV('STORAGE_DISK_XML');
                    $ruta_xml          = Storage::disk($storage_disk_xmls)->path($id . '.xml');
                    /**es de tipo cfdi y debe adjuntar el xml */
                    $xml_parametros = [
                        'as'   => 'XML FOLIO ' . $id . '.xml',
                        'mime' => 'application/xml',
                    ];
                    $message->attach($ruta_xml, $xml_parametros);
                }
            });
        } catch (JWTException $exception) {
            //$this->serverstatuscode = "0";
            //$this->serverstatusdes = $exception->getMessage();
            return $this->errorResponse($exception->getMessage(), 0);
        }
        if (Mail::failures()) {
            $this->statusdesc = "Ocurrió un error al enviar el correo.";
            $this->statuscode = "0";
            return $this->errorResponse($this->statusdesc, $this->statuscode);
        } else {
            return $this->successResponse(1, 200);
        }
    }
}
