<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\EmailController;
use PDF;

/** Motor compartido de documentos heredados: incrusta recursos, conserva
 * opciones Snappy y prepara HTML para pantalla/impresión. No consulta ventas.
 * Cada módulo valida su catálogo y entidad antes de entregar la captura.
 */
class VisorDocumentos
{
    private $prefijo;

    /** Prefijo interno por módulo: separa tokens y evita cruzar documentos. */
    public function __construct($prefijo = 'cementerio_solicitud_') { $this->prefijo = $prefijo; }

    /** Guarda una instantánea privada por 15 minutos y devuelve HTML sin PDF.
     * El vencimiento usa 410; nunca invalida la sesión del usuario. */
    public function preparar(array $captura, $documento, $titulo, $entidadId, $usuario, $subtitulo)
    {
        $definicion = ['titulo' => $titulo];
        $ventaId = $entidadId;
        $html = $this->recursosDocumentoCementerioVisor($captura['html']);
        $opciones = $captura['opciones'];
        foreach (['header-html', 'footer-html'] as $clave) {
            if (!empty($opciones[$clave])) {
                $valor = $opciones[$clave];
                $opciones[$clave] = $this->recursosDocumentoCementerioVisor(
                    $valor instanceof \Illuminate\Contracts\Support\Renderable ? $valor->render() : (string) $valor
                );
                // El acuse reserva un margen corto: ajustar solo su pie para
                // mantener visibles las cinco líneas de contacto en el PDF.
                if ($clave === 'footer-html' && $documento === 'acuse-cancelacion') {
                    $opciones[$clave] = str_replace('</head>', '<style>html body table td.texto-sm{font-size:10px!important;line-height:12px!important}</style></head>', $opciones[$clave]);
                }
            }
        }
        $fecha = Carbon::now('America/Mazatlan');
        $vence = $fecha->copy()->addMinutes(15);
        $token = bin2hex(random_bytes(32));
        $nombre = $definicion['titulo'] . ($ventaId ? ' '.$ventaId : '') . ' ' . $fecha->format('Y-m-d H-i-s') . '.pdf';
        \Illuminate\Support\Facades\Cache::put($this->prefijo . $token, [
            'usuario_id' => (int) $usuario->id,
            'html' => $html,
            'opciones' => $opciones,
            'nombre' => $nombre,
            'venta_id' => $ventaId,
            'documento' => $documento,
        ], $vence);

        // La impresión del navegador incluye el pie dentro del documento; el PDF
        // conserva el footer/header de Snappy y los márgenes originales del contrato.
        $vista = $html;
        $margenes = [];
        foreach (['top' => 5.4, 'right' => 5.4, 'bottom' => 35.4, 'left' => 5.4] as $lado => $defecto) {
            $valor = (float) ($opciones['margin-' . $lado] ?? $defecto);
            $margenes[$lado] = number_format($valor, 2, '.', '');
        }
        $horizontal = strtolower($opciones['orientation'] ?? 'portrait') === 'landscape';
        $a4 = strtoupper($opciones['page-size'] ?? 'LETTER') === 'A4';
        $ancho = $horizontal ? ($a4 ? 297 : 279.4) : ($a4 ? 210 : 215.9);
        $alto = $horizontal ? ($a4 ? 210 : 215.9) : ($a4 ? 297 : 279.4);
        $tamanoPagina = ($a4 ? 'A4' : 'letter') . ' ' . ($horizontal ? 'landscape' : 'portrait');
        $margenPagina = $margenes['top'] . 'mm ' . $margenes['right'] . 'mm ' . $margenes['bottom'] . 'mm ' . $margenes['left'] . 'mm';
        // Adaptación solo del navegador: WebKit/PDF y Chromium distribuyen de
        // forma diferente las tablas con floats. 12px y columnas fijas reproducen
        // la escala del original (prueba comparativa: misma paginación y filas).
        // No forzar 12pt, desactivar smart-shrinking ni cambiar las opciones PDF.
        // Zoom de pantalla en porcentaje desde config/reportes.php (.env). Mantiene
        // el ancho interno carta; impresión al 100% y PDF sin estos estilos.
        $zoomPantalla = number_format(config('reportes.visor_zoom_porcentaje', 135) / 100, 2, '.', '');
        // Recibos y talonarios pueden crecer por firmas o filas. En pantalla,
        // un contenedor flexible ocupa el espacio libre y empuja el pie al fondo.
        // Se limita a estos documentos y a @media screen: PDF/impresión conservan su pie.
        $pieEnFlujo = in_array($documento, ['recibo', 'referencias', 'cuotas', 'planes-venta'], true)
            ? 'html body{display:flex;flex-direction:column;padding-bottom:5mm!important}.solicitud-visor-contenido{display:flow-root;flex:1 0 auto}.solicitud-visor-pie{position:static;flex:0 0 auto;clear:both;margin-top:24px;left:auto;right:auto;bottom:auto}'
            : '';
        $estiloNavegador = '<style id="solicitud-formato-navegador">'
            // Cajas superiores vacías: suprimir fecha y título automáticos de Chromium.
            . '@page{size:' . $tamanoPagina . ';margin:' . $margenPagina . ';@top-left{content:" "}@top-center{content:" "}@top-right{content:" "}}'
            . 'html body{font-size:12px!important;-webkit-print-color-adjust:exact;print-color-adjust:exact}'
            // Ajustes internos aportados por el documento para compensar la
            // escala de WebKit frente a Chromium; no se guardan en el PDF.
            . ($captura['css_navegador'] ?? '')
            . (in_array($documento, ['solicitud', 'convenio'], true) ? 'table{table-layout:fixed!important;width:100%}.float-left>span{white-space:nowrap}' : '')
            . '.solicitud-visor-pie{border-top:0.5pt solid #000;padding-top:8px}'
            . '.solicitud-visor-pie table{width:100%;table-layout:fixed;border-collapse:collapse;padding:0}.solicitud-visor-pie td{width:50%;vertical-align:top;padding:0}'
            . '@media screen{html{background:#eceff1}html body{zoom:' . $zoomPantalla . ';position:relative;box-sizing:border-box;'
            . 'width:' . $ancho . 'mm!important;min-height:' . $alto . 'mm;margin:16px auto!important;'
            . 'padding:calc(' . $margenes['top'] . 'mm + 6px) calc(' . $margenes['right'] . 'mm + 6px) calc(' . $margenes['bottom'] . 'mm + 6px) calc(' . $margenes['left'] . 'mm + 6px)!important;background:#fff;box-shadow:0 1px 8px #0002}'
            . '.solicitud-visor-pie{position:absolute;left:calc(' . $margenes['left'] . 'mm + 6px);right:calc(' . $margenes['right'] . 'mm + 6px);bottom:5mm}' . $pieEnFlujo . '}'
            . '@media print{html{background:#fff}html body{zoom:1!important;width:auto!important;min-height:0!important;'
            . 'margin:6px!important;padding:0!important;background:#fff;box-shadow:none}'
            . '.solicitud-visor-pie{display:none!important}}'
            . '</style>';
        $vista = preg_replace_callback('/<\/head>/i', function () use ($estiloNavegador) {
            return $estiloNavegador . '</head>';
        }, $vista, 1);
        if ($documento === 'orden' && $this->prefijo === 'funeraria_servicios_') {
            // La regla común de marca de agua elimina min-height en el visor.
            // Esta orden conserva una página física completa para que el pie
            // absoluto no suba sobre las observaciones de su contenido escalado.
            $vista = str_replace('</head>', '<style>@media screen{html body.orden-institucional{height:279.4mm!important;min-height:279.4mm!important}.orden-institucional .solicitud-visor-pie{top:246mm!important;bottom:auto!important}}</style></head>', $vista);
        }
        if (!empty($opciones['header-html'])) {
            $aviso = trim(preg_replace('/\s+/u', ' ', strip_tags(preg_replace('/<head\b[^>]*>.*?<\/head>/is', '', $opciones['header-html']))));
            $vista = preg_replace_callback('/(<body\b[^>]*>)/i', function ($m) use ($aviso) {
                return $m[1] . '<div style="color:#b34343;text-align:center;font-weight:bold">' . e(mb_strtoupper($aviso, 'UTF-8')) . '</div>';
            }, $vista, 1);
        }
        if (!empty($opciones['footer-html'])) {
            $pie = new \DOMDocument();
            $anterior = libxml_use_internal_errors(true);
            $pie->loadHTML('<?xml encoding="UTF-8">' . $opciones['footer-html']);
            libxml_clear_errors();
            libxml_use_internal_errors($anterior);
            $xpath = new \DOMXPath($pie);
            foreach ($xpath->query('//td[.//span[contains(concat(" ",normalize-space(@class)," ")," page ")]]') as $nodo) {
                $nodo->parentNode->removeChild($nodo);
            }
            // El footer original tiene una línea con colspan=3. Tras quitar
            // la paginación central para pantalla esa fila seguía creando tres
            // columnas, dejando el contacto derecho en el centro del documento.
            // En el visor la línea ya la dibuja el contenedor: conservar dos columnas.
            foreach ($xpath->query('//tr[count(td)=1 and td[@colspan]]') as $fila) {
                $fila->parentNode->removeChild($fila);
            }
            // Cada página reserva el margen inferior original. Las cajas de
            // margen de Chromium repiten el pie sin posicionarlo sobre los pagos.
            $textoColumna = function ($alineacion) use ($xpath) {
                $celda = $xpath->query('//td[@align="' . $alineacion . '"]')->item(0);
                if (!$celda) return '';
                $lineas = [];
                foreach ($celda->getElementsByTagName('span') as $span) $lineas[] = trim($span->textContent);
                return $lineas ? implode("\n", $lineas) : trim($celda->textContent);
            };
            $literalCss = function ($texto) {
                $literal = '"' . str_replace(
                    ['\\', '"', "\r", "\n", '<', '>'],
                    ['\\\\', '\\"', '', '\\A ', '\\3C ', '\\3E '],
                    $texto
                ) . '"';
                // Escapes CSS Unicode: DOMDocument no debe convertir los acentos
                // de content en entidades HTML que el navegador imprimiría literalmente.
                return preg_replace_callback('/[^\x00-\x7F]/u', function ($m) {
                    return '\\'.dechex(mb_ord($m[0], 'UTF-8')).' ';
                }, $literal);
            };
            // Los pies de cinco líneas deben caber incluso con margen inferior corto.
            $letraPie = (float) $margenes['bottom'] < 25 ? 'font-size:7.5pt;line-height:9pt;' : 'font-size:8.5pt;line-height:11pt;';
            $basePie = 'font-family:"Open Sans",sans-serif;' . $letraPie
                . 'white-space:pre-line;vertical-align:top;padding-top:5pt;border-top:0.5pt solid #000;';
            $reglasPie = '<style>@page{'
                . '@bottom-left{width:45%;' . $basePie . 'text-align:left;content:' . $literalCss($textoColumna('left')) . '}'
                . '@bottom-center{width:10%;' . $basePie . 'text-align:center;vertical-align:middle;content:"P\e1 g. " counter(page) " De " counter(pages)}'
                . '@bottom-right{width:45%;' . $basePie . 'text-align:right;content:' . $literalCss($textoColumna('right')) . '}'
                . '}</style>';
            $vista = preg_replace_callback('/<\/head>/i', function () use ($reglasPie) {
                return $reglasPie . '</head>';
            }, $vista, 1);
            $cuerpo = $pie->getElementsByTagName('body')->item(0);
            $fragmento = '';
            if ($cuerpo) foreach ($cuerpo->childNodes as $nodo) $fragmento .= $pie->saveHTML($nodo);
            $vista = preg_replace('/<\/body>/i', '<div class="solicitud-visor-pie">' . $fragmento . '</div></body>', $vista, 1);
        }
        // Normalizar el HTML heredado antes de separar contenido y pie: algunos
        // Blade dejan contenedores abiertos. El pie debe ser hermano del contenido
        // para que el flex de pantalla lo mantenga abajo sin superponer las firmas.
        // La instantánea del PDF ya se guardó; este envoltorio solo afecta al visor.
        if (in_array($documento, ['recibo', 'referencias', 'cuotas', 'planes-venta'], true)) {
            $domVista = new \DOMDocument();
            $erroresPrevios = libxml_use_internal_errors(true);
            $domVista->loadHTML('<?xml encoding="UTF-8">'.$vista);
            // Mantener UTF-8 al normalizar el HTML heredado.
            $domVista->encoding = 'UTF-8';
            libxml_clear_errors(); libxml_use_internal_errors($erroresPrevios);
            $cuerpoVista = $domVista->getElementsByTagName('body')->item(0);
            $consultaVista = new \DOMXPath($domVista);
            $pieVista = $consultaVista->query('//div[contains(concat(" ",normalize-space(@class)," ")," solicitud-visor-pie ")]')->item(0);
            if ($cuerpoVista && $pieVista) {
                $pieVista->parentNode->removeChild($pieVista);
                $contenidoVista = $domVista->createElement('div');
                $contenidoVista->setAttribute('class', 'solicitud-visor-contenido');
                while ($cuerpoVista->firstChild) $contenidoVista->appendChild($cuerpoVista->firstChild);
                $cuerpoVista->appendChild($contenidoVista);
                $cuerpoVista->appendChild($pieVista);
                $vista = preg_replace('/<\?xml[^>]*>/', '', $domVista->saveHTML(), 1);
            }
        }
        return response()->json([
            'titulo' => $definicion['titulo'],
            'nombreArchivo' => $nombre,
            'subtitulo' => $subtitulo,
            'nota' => 'Vista previa del documento. El PDF se genera al descargarlo o enviarlo. La consulta es válida por 15 minutos; Actualizar obtiene los datos y firmas vigentes.',
            'soloPdf' => true,
            'tokenDescarga' => $token,
            'venceEn' => $vence->toIso8601String(),
            'vistaPreviaDisponible' => true,
            'totalRegistros' => (int) ($captura['total_registros'] ?? 1),
            'hojas' => [['nombre' => $definicion['titulo'], 'html' => $vista]],
        ])->header('Cache-Control', 'no-store, private');
    }

    /** Captura el Blade ya renderizado y las opciones originales de Snappy sin ejecutar output/inline. */
    public function capturar($pdf)
    {
        $opciones = array_intersect_key($pdf->snappy()->getOptions(), array_flip([
            'title',
            'footer-html',
            'header-html',
            'margin-left',
            'margin-right',
            'margin-top',
            'margin-bottom',
            'page-size',
            'orientation',
            'javascript-delay', // Documentos con ajuste de escala antes del PDF.
        ]));
        return ['html' => (string) $pdf->html, 'opciones' => $opciones];
    }

    /**
     * Recupera la instantánea del token solo para su propietario autenticado.
     * Devuelve datos del documento; 410 indica reporte vencido, no sesión vencida.
     */
    private function datosDocumentoCementerioVisor(Request $request, $token, $documento)
    {
        $usuario = $request->user('api');
        abort_unless($usuario, 401, 'Es necesario iniciar sesión.');
        abort_unless(is_string($token) && preg_match('/^[a-f0-9]{64}$/D', $token), 404);
        $datos = \Illuminate\Support\Facades\Cache::get($this->prefijo . $token);
        abort_unless($datos, 410, 'La consulta del reporte venció. Actualice el reporte para continuar.');
        abort_unless((int) $datos['usuario_id'] === (int) $usuario->id, 403);
        abort_unless(($datos['documento'] ?? 'solicitud') === $documento, 404, 'El documento no corresponde al reporte solicitado.');
        return $datos;
    }

    /** Genera el PDF desde la instantánea, con márgenes, pie y firmas originales. */
    private function pdfDocumentoCementerioVisor(array $datos)
    {
        $pdf = PDF::loadHTML($datos['html'])->setOptions($datos['opciones']);
        // La orden ya calcula su propia escala. La reducción adicional de Qt
        // dejaba contenido pequeño y una franja lateral de marca de agua.
        if (in_array(($datos['documento'] ?? ''), ['orden', 'certificado'], true) && $this->prefijo === 'funeraria_servicios_') {
            $pdf->setOption('disable-smart-shrinking', true);
        }
        return $pdf;
    }

    /** Genera bajo demanda el PDF del tipo esperado; nunca se invoca al visualizar HTML. */
    public function descargar(Request $request, $token, $documento)
    {
        $datos = $this->datosDocumentoCementerioVisor($request, $token, $documento);
        return response($this->pdfDocumentoCementerioVisor($datos)->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $datos['nombre'] . '"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /** Valida destinatarios/mensaje y envía la instantánea del tipo esperado, solo PDF. */
    public function enviarCorreo(Request $request, $token, $documento)
    {
        $datos = $this->datosDocumentoCementerioVisor($request, $token, $documento);
        $request->validate([
            'destinatarios' => 'required|array|min:1|max:20',
            'destinatarios.*.email' => 'required|email|max:75',
            'destinatarios.*.nombre' => 'nullable|string|max:75',
            'asunto' => 'required|string|max:150',
            'mensaje' => 'nullable|string|max:30000',
            'formato' => 'required|in:pdf',
            'completo' => 'sometimes|boolean',
        ]);
        $destinatarios = [];
        $vistos = [];
        foreach ($request->input('destinatarios') as $persona) {
            $correo = mb_strtolower(trim($persona['email']), 'UTF-8');
            if (isset($vistos[$correo])) throw \Illuminate\Validation\ValidationException::withMessages([
                'destinatarios' => 'Hay correos duplicados en la lista.',
            ]);
            $vistos[$correo] = true;
            $destinatarios[] = ['email' => $correo, 'nombre' => trim($persona['nombre'] ?? '')];
        }
        $mensaje = $request->input('mensaje', '');
        $texto = html_entity_decode(strip_tags($mensaje), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (mb_strlen(trim($texto), 'UTF-8') > 5000) throw \Illuminate\Validation\ValidationException::withMessages([
            'mensaje' => 'El mensaje no debe superar 5000 caracteres.',
        ]);
        abort_if($request->boolean('completo'), 422, 'Este documento no tiene una variante de detalle completo.');
        return app(EmailController::class)->reporte_email([
            'destinatarios' => $destinatarios,
            'asunto' => trim($request->input('asunto')),
            'mensaje' => $mensaje,
        ], $this->pdfDocumentoCementerioVisor($datos)->output(), $datos['nombre'], 'application/pdf');
    }

    /**
     * Hace autocontenido el Blade original: CSS de reportes e imágenes locales
     * se incrustan para funcionar en el iframe sin permitir recursos externos.
     * No modifica el Blade. No descarga URLs ni permite leer fuera de public.
     */
    private function recursosDocumentoCementerioVisor($html)
    {
        $html = preg_replace_callback('/<link\b[^>]*>/i', function ($m) {
            if (!preg_match('/\bhref\s*=\s*([\'"])(.*?)\1/i', $m[0], $enlace)) return '';
            $ruta = parse_url(html_entity_decode($enlace[2], ENT_QUOTES, 'UTF-8'), PHP_URL_PATH);
            $esperada = parse_url(asset('css/blade_css.css'), PHP_URL_PATH);
            if ($ruta !== '/css/blade_css.css' && $ruta !== $esperada) return '';
            $css = public_path('css/blade_css.css');
            return is_file($css) ? '<style>' . file_get_contents($css) . '</style>' : '';
        }, (string) $html);
        $html = preg_replace_callback('/\bsrc\s*=\s*([\'"])(.*?)\1/i', function ($m) {
            return 'src="' . $this->imagenDocumentoCementerioVisor(html_entity_decode($m[2], ENT_QUOTES, 'UTF-8')) . '"';
        }, $html);
        $html = preg_replace_callback('/url\(\s*([\'"]?)(.*?)\1\s*\)/i', function ($m) {
            $imagen = $this->imagenDocumentoCementerioVisor($m[2]);
            // El data URI no lleva comillas: puede estar dentro de style="...".
            // Introducir comillas dobles aquí cortaba el atributo y ocultaba el fondo.
            return $imagen ? 'url(' . $imagen . ')' : 'none';
        }, $html);
        // Conservar la tipografía de los documentos originales. Las variantes se
        // incrustan: el iframe/PDF no depende de fuentes instaladas ni de Google.
        $fuentes = '';
        foreach ([400 => 'OpenSans-Regular.ttf', 700 => 'OpenSans-Bold.ttf'] as $peso => $archivo) {
            $ruta = public_path('fonts/reportes-open-sans/' . $archivo);
            abort_unless(is_file($ruta), 422, 'Falta la fuente Open Sans del reporte.');
            $fuentes .= '@font-face{font-family:"Open Sans";font-style:normal;font-weight:' . $peso . ';'
                . 'src:url(data:font/ttf;base64,' . base64_encode(file_get_contents($ruta)) . ') format("truetype");}';
        }
        return preg_replace_callback('/<\/head>/i', function () use ($fuentes) {
            return '<style>' . $fuentes . 'html body{font-family:"Open Sans",sans-serif!important}</style></head>';
        }, $html, 1);
    }

    /** Devuelve un data URI de imagen pública local o vacío para recursos no permitidos. */
    private function imagenDocumentoCementerioVisor($ruta)
    {
        if (preg_match('#^data:image/(?:png|jpeg|gif|webp);base64,[a-z0-9+/=\r\n]+$#i', $ruta)) return $ruta;
        if (preg_match('#^https?://#i', $ruta)) {
            if (parse_url($ruta, PHP_URL_HOST) !== parse_url(config('app.url'), PHP_URL_HOST)) return '';
            $ruta = public_path(ltrim(parse_url($ruta, PHP_URL_PATH) ?: '', '/'));
        } elseif (!preg_match('#^(?:[a-z]:[\\\\/]|/)#i', $ruta)) {
            $ruta = public_path($ruta);
        }
        $real = realpath($ruta);
        $publico = realpath(public_path());
        if (!$real || !$publico || !is_file($real)) return '';
        $normal = str_replace('\\', '/', $real);
        $raiz = rtrim(str_replace('\\', '/', $publico), '/') . '/';
        if (strncasecmp($normal, $raiz, strlen($raiz)) !== 0) return '';
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($real);
        if (!in_array($mime, ['image/png', 'image/jpeg', 'image/gif', 'image/webp'], true)) return '';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($real));
    }


}
