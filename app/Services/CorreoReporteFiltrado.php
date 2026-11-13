<?php

namespace App\Services;

use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;

class CorreoReporteFiltrado
{
    /**
     * Genera el mismo adjunto que la descarga del visor. El generador valida
     * el propietario de la consulta, su vigencia y los límites del reporte.
     */
    public function enviar(Request $request, callable $descargar, array $formatos = ['pdf', 'excel'])
    {
        $datos = $request->validate([
            'destinatarios' => 'required|array|min:1|max:20',
            'destinatarios.*.email' => 'required|email|max:75',
            'destinatarios.*.nombre' => 'nullable|string|max:75',
            'asunto' => ['required', 'string', 'max:150', 'not_regex:/[\r\n]/'],
            'mensaje' => 'nullable|string|max:30000',
            'formato' => 'required|in:' . implode(',', $formatos),
            'completo' => 'sometimes|boolean',
        ]);
        abort_if($request->boolean('completo'), 422, 'Este reporte no tiene una variante de detalle completo.');

        $destinatarios = [];
        foreach ($datos['destinatarios'] as $persona) {
            $correo = mb_strtolower(trim($persona['email']), 'UTF-8');
            if (isset($destinatarios[$correo])) {
                throw ValidationException::withMessages(['destinatarios' => 'Hay correos duplicados en la lista.']);
            }
            $destinatarios[$correo] = ['email' => $correo, 'nombre' => trim($persona['nombre'] ?? '')];
        }

        $mensaje = $datos['mensaje'] ?? '';
        $texto = html_entity_decode(strip_tags($mensaje), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (mb_strlen(trim($texto), 'UTF-8') > 5000) {
            throw ValidationException::withMessages(['mensaje' => 'El mensaje no debe superar 5000 caracteres.']);
        }

        $archivo = $descargar($datos['formato']);
        if (!$archivo->isSuccessful()) {
            return $archivo;
        }

        $disposicion = HeaderUtils::combine(HeaderUtils::split($archivo->headers->get('Content-Disposition', ''), ';='));
        $nombre = isset($disposicion['filename*'])
            ? rawurldecode(explode("''", $disposicion['filename*'], 2)[1])
            : $disposicion['filename'];

        // Excel genera un archivo temporal que se elimina al descargarlo. Aquí
        // se lee como adjunto y se limpia explícitamente porque no se descarga.
        if ($archivo instanceof BinaryFileResponse) {
            $ruta = $archivo->getFile()->getPathname();
            try {
                $contenido = file_get_contents($ruta);
            } finally {
                unlink($ruta);
            }
        } else {
            $contenido = $archivo->getContent();
        }

        abort_if($contenido === false || $contenido === '', 500, 'No se pudo generar el archivo adjunto.');

        return app(EmailController::class)->reporte_email([
            'destinatarios' => array_values($destinatarios),
            'asunto' => $datos['asunto'],
            'mensaje' => $mensaje,
        ], $contenido, $nombre, $datos['formato'] === 'pdf'
            ? 'application/pdf'
            : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}

