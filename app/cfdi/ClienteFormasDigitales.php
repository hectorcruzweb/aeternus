<?php
namespace App\cfdi;

use DOMDocument;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Storage;
use SoapClient;
use XSLTProcessor;

class ClienteFormasDigitales
{

    private $xml;
    private $autentica;
    private $cadena_original_xslt;

    public function __construct($xmlPath)
    {
        $this->xml = new DOMDocument();
        $this->xml->load($xmlPath) or die("XML invalido");
        $this->cadena_original_xslt = Storage::disk(ENV('STORAGE_DISK_CREDENTIALS'))->path(ENV('CADENA_ORIGINAL_FILE'));
    }

    public function timbrar($parametros)
    {
        /* conexion al web service */
        $web_service=ENV('WEB_SERVICE_DEVELOP');
        if (ENV('APP_ENV') != 'local') {
            $web_service=ENV('WEB_SERVICE_PRODUCTION');
        }

        $client = new SoapClient($web_service, array('encoding' => 'UTF-8'));
        return $client->TimbrarCFDI($parametros);
    }

    public function crear_pem_files($parametros = array())
    {
        $key     = $parametros['key_name'];
        $key_pem = $parametros['key_name'] . '.pem';
        $cer     = $parametros['cer_name'];
        $cer_pem = $parametros['cer_name'] . '.pem';

        $password = $parametros['password'];

        $path_key     = Storage::disk($parametros['disk'])->path($parametros['key_root'] . $parametros['key_name']);
        $path_key_pem = Storage::disk($parametros['disk'])->path($parametros['key_root'] . $parametros['key_name'] . '.pem');

        shell_exec("touch  $path_key_pem | chmod 666  $path_key_pem");
        $crear_pem = "openssl pkcs8 -inform DER -in $path_key -passin pass:$password -out $path_key_pem";
        shell_exec($crear_pem);
        $path_cer     = Storage::disk($parametros['disk'])->path($parametros['cer_root'] . $parametros['cer_name']);
        $path_cer_pem = Storage::disk($parametros['disk'])->path($parametros['cer_root'] . $parametros['cer_name'] . '.pem');
        $crear_pem    = "openssl x509 -inform der -in $path_cer -out $path_cer_pem";
        shell_exec($crear_pem);

        if (!Storage::disk($parametros['disk'])->exists($parametros['key_root'] . $key) || !Storage::disk($parametros['disk'])->exists($parametros['key_root'] . $key_pem) ||
            !Storage::disk($parametros['disk'])->exists($parametros['cer_root'] . $cer) || !Storage::disk($parametros['disk'])->exists($parametros['cer_root'] . $cer_pem)) {
            /**algun archivo no existe */
            return 'error';
        }
    }

    public function sellarXML($certFile, $keyFile)
    {
        $private        = openssl_pkey_get_private(file_get_contents($keyFile));
        $cert           = file_get_contents($certFile);
        $certificado    = str_replace(array('\n', '\r'), '', base64_encode($cert));
        $data           = openssl_x509_parse(file_get_contents($certFile . '.pem'), true);
        $serial_number  = $data['serialNumberHex'];
        $no_certificado = $this->getNoCertificado($serial_number);
        $fecha_actual   = substr(date('c'), 0, 19);
        $comprobante    = $this->xml->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/4', 'Comprobante')->item(0);
        $comprobante->setAttribute('Fecha', $fecha_actual);
        $cadena_original = $this->generarCadenaOriginal();
        openssl_sign($cadena_original, $signature, $private, "sha256WithRSAEncryption");
        $sello = base64_encode($signature);
        $comprobante->setAttribute('Sello', $sello);
        $comprobante->setAttribute('NoCertificado', $no_certificado);
        $comprobante->setAttribute('Certificado', $certificado);
        $retorno['cadena_original'] = $cadena_original;
        $retorno['xml']             = $this->xml->saveXML($this->xml->documentElement);
        return $retorno;
        //return $this->xml->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0)->getAttribute('NoCertificado');
    }

    public function getNoCertificado($serial)
    {
        $noCertificado = "";

        if ((strlen($serial) % 2) == 1) {
            $serial = " " . $serial;
        }
        for ($i = 0; $i < strlen($serial) / 2; $i++) {
            $aux = substr($serial, $i * 2, ($i * 2) + 2);
            $noCertificado .= substr($aux, 1, 1);
        }
        return $noCertificado;
    }

    public function generarCadenaOriginal()
    {
        $XSL = new DOMDocument();
        $XSL->load($this->cadena_original_xslt);
        $proc = new XSLTProcessor();
        @$proc->importStyleSheet($XSL);
        return $proc->transformToXML($this->xml);
    }

    public function consultar($parametros)
    {
        if (ENV('APP_ENV') == 'local') {
            $url_consulta = ENV('WEB_SERVICE_CONSULTA_DEVELOP');
        } else {
            $url_consulta = ENV('WEB_SERVICE_CONSULTA_PRODUCTION');
        }

        /* conexion al web service */
        $client = new SoapClient($url_consulta);
        $result = $client->ConsultarEstatusCFDI_2($parametros);
        // Mostramos el XML Request enviado al WebService
        //echo "<b>Request</b>:<br>" . htmlentities($client->__getLastRequest()) . "\n";
        return $result;
    }
}
