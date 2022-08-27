<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reportes</title>

    <style>
        #header,
        #header section table {
            width: 100% !important;
            padding-top: 0px;
        }

        #header section table {
            border-collapse: collapse !important;
        }

        .logo {
            max-width: 100% !important;
        }


        h1 {
            font-size: 1em;
            line-height: .8em !important;
            text-transform: uppercase;
            text-align: center;
        }

        .datos-header {
            text-align: center !important;
            font-size: .9em;
            line-height: 1em !important;
            text-transform: uppercase !important;
        }

        .numeros-contrato {
            width: 100% !important;
        }

        .numeros-contrato .control {
            text-align: center;
            text-transform: uppercase !important;
            font-size: .8em;
            line-height: 1.9em !important;
            font-weight: 600 !important;
        }

        .control-valor {
            text-align: center;
            font-size: .9em;
            line-height: .3em !important;
            text-transform: uppercase;
        }

        /*parrafos**/
        .contenido {
            padding: 10px 0 0 0 !important;
            margin: 0 !important;
        }

        .tabla_dato {
            border-collapse: collapse;
        }

        .tabla_dato .border-bottom {
            border-bottom: 1px solid #ddd;
        }


        .datos_tabla {
            border-collapse: collapse;
        }

        .datos_tabla tr th,
        .datos_tabla td {
            border: 1px solid #ddd;
        }

        /*fin de parrafos*/

    </style>
</head>

<body>
    @include('layouts.estilos')
    <header id="header">
        <section>
            <table>
                <tr>
                    <td style="width:23%;">
                        <img src="{{ public_path(env('LOGOJPG')) }}" alt="" class="logo">
                    </td>
                    <td style="width:53%;">
                        <h1>
                            {{ $empresa->razon_social }}
                        </h1>
                        <p class="datos-header">
                            {{ strtolower($empresa->calle) }} Núm. Ext {{ $empresa->num_ext }}
                        </p>
                        <p class="datos-header">
                            Col. {{ strtolower($empresa->colonia) }}. cp. {{ $empresa->cp }}.
                            {{ $empresa->ciudad }}
                            {{ $empresa->estado }}
                        </p>
                        <p class="datos-header">
                            Tel. {{ $empresa->telefono }}, fax {{ $empresa->fax }}
                        </p>
                    </td>
                    <td style="width:25%;">
                        <div class="numeros-contrato">
                            <div class="control bg-gray">
                                Número Servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['servicio_id'] }}
                            </p>

                            <div style=""></div>
                            <div class="control bg-gray">
                                Tipo Servicio
                            </div>
                            <p class="control-valor">
                                {{ $datos['tipo_solicitud_texto'] }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </section>
    </header>
    <p class="fecha  right">
        <span class="bg-gray bold uppercase texto-sm  pl-2 pr-1">{{ $empresa->ciudad }}, {{ $empresa->estado }} a
            {{ fecha_only(now()) }}.</span>
    </p>
    <div class="contenido w-100">
        <p class="justificar line-17 size-13px uppercase">
            CONTRATO DE PRESTACIÓN DE SERVICIOS FUNERARIOS DE USO INMEDIATO, QUE CELEBRAN POR UNA PARTE <span
                class="bold">{{ $empresa->razon_social }}</span>
            REPRESENTADA EN ESTE ACTO POR EL <span class="bold">SR. {{ $registro->rep_legal }}</span> A QUIEN EN
            LO SUCESIVO SE LE DENOMINARÁ <span class="bold">"EL PROVEEDOR"</span>, Y POR LA OTRA <span
                class="bold">{{ $datos['operacion']['cliente']['nombre'] }}</span>, A QUIEN EN LO SUCESIVO
            SE LE
            DENOMINARÁ <span class="bold">"EL CONSUMIDOR"</span>, A QUIENES DE MANERA CONJUNTA SE LES DENOMINARÁ COMO
            <span class="bold">"LAS PARTES"</span>. AL TENOR DEL
            SIGUIENTE GLOSARIO, ASI COMO DE LAS SIGUIENTES DECLARACIONES Y CLÁUSULAS:
        </p>

        <p class="justificar line-base size-13px uppercase">
            GLOSARIO.- Para los efectos de este Contrato y su Anexo, se entiende por:
        </p>


        <div class="lista pl-11 -mt-1 line-base size-14px">
            <p class="justificar ">
                <span class=" bold  -ml-6">a.</span>
                <span class="ml-1">
                    <span class="bold">Agencia Funeraria:</span> El establecimiento mercantil autorizado, en el que
                    proveedor prestará o proveerá
                    el Servicio
                    Funerario de uso
                    inmediato.
                </span>
            </p>
            <p class="justificar -mt-3">
                <span class=" bold  -ml-6">b.</span>
                <span class="ml-1">
                    <span class="bold">Anexo:</span> La orden de servicio, en la cual se enlistan los productos
                    contratados que se incluyen en el
                    servicio, así como
                    el costo total y
                    cualquier observación relativa a la prestación del servicio objeto del presente Contrato.
                </span>
            </p>
            <p class="justificar -mt-3">
                <span class=" bold  -ml-6">c.</span>
                <span class="ml-1">
                    <span class="bold">Consumidor:</span> A la persona física o moral que adquiere o utiliza bienes y
                    servicios funerarios.
                </span>
            </p>
            <p class="justificar -mt-3">
                <span class=" bold  -ml-6">d.</span>
                <span class="ml-1">
                    <span class="bold">Proveedor:</span> A la persona física o moral que es comercializador de servicios
                    funerarios, de
                    conformidad con lo dispuesto
                    en el
                    presente Contrato.
                </span>
            </p>
            <p class="justificar -mt-3">
                <span class=" bold  -ml-6">e.</span>
                <span class="ml-1">
                    <span class="bold">Servicio:</span> es el servicio funerario de uso inmediato que ofrece el
                    proveedor y que contrata el
                    consumidor para ser
                    utilizado a la
                    firma del presente contrato y que incluirá los productos enlistados en el anexo del presente
                    Contrato.
                </span>
            </p>
        </div>

        <p class="justificar line-17 size-13px uppercase center bold">
            declaraciones
        </p>
        <p class="justificar line-17 size-13px bold">
            I. Declara "EL PROVEEDOR":
        </p>
        <p class="justificar line-17 size-13px">
            <span class="bold">a) </span> Ser una persona moral, legalmente constituida conforme a las leyes mexicanas
            lo
            que se acredita con
            testimonio de la
            escritura pública
            número <span class="bold">{{ $registro->t_nep }}</span>, de fecha <span
                class="bold">{{ fecha_no_day($registro->fecha_rpc) }}</span>, otorgada ante la
            fé del <span class="bold uppercase">Lic. {{ $registro->fe_lic }}</span>,
            Notario Público
            número <span class="bold uppercase">{{ $registro->num_np }}</span>, en <span
                class="bold uppercase">{{ $registro->ciudad_np }}, {{ $registro->estado }}</span> e inscrita en el
            Registro Público de Comercio de No. <span class="bold">{{ $registro->num_rpc }}</span>.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">b) </span> Cuenta con correo electrónico: <span class="bold">{{ $empresa->email }}</span>
            y teléfono: <span class="bold">{{ $empresa->telefono }}</span>.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">c) </span> Dentro de las actividades que constituyen su objeto social, se encuentra
            prevista la de <span class="bold">AGENCIA FUNERARIA</span>, así como
            posee los elementos adecuados y experiencia suficiente para obligarse a lo estipulado en el presente
            Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">d) </span> Su domicilio se encuentra ubicado en la calle <span
                class="bold">{{ $empresa->calle }}</span> No. <span class="bold">{{ $empresa->num_ext }}</span> COL.
            <span class="bold">{{ $empresa->colonia }}</span> C.P. <span class="bold">{{ $empresa->cp }}</span>, en
            <span class="bold">{{ $empresa->ciudad }}</span>,
            <span class="bold">{{ $empresa->estado }}</span>., el cual señala como domicilio convencional para todos los
            efectos legales del presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">e)</span> Que se encuentra inscrita en el Registro Federal de Contribuyentes con la
            clave: <span class="bold">{{ $empresa->rfc }}</span>.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">f) </span> Cuenta con la infraestructura, los elementos propios, los recursos técnicos y
            humanos suficientes para cumplir con sus
            obligaciones conforme a lo establecido en el presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">g)</span> Cumple con las licencias, permisos, avisos certificados y autorizaciones
            previstas en las disposiciones legales y normas
            vigentes que
            corresponden para llevar a cabo las actividades que comprende la prestación del Servicio objeto de este
            Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">h) </span> Para la atención de dudas, aclaraciones, reclamaciones o para proporcionar
            servicios de orientación señala el teléfono
            <span class="bold">{{ $empresa->telefono }}</span>
            y correo electrónico; <span class="bold">{{ $empresa->email }}</span>, con un horario de atención al público
            LAS 24 HORAS
        </p>
        <p class="justificar line-17 size-13px bold">
            II. Declara "EL CONSUMIDOR":
        </p>
        <p class="justificar line-17 size-13px">
            <span class="bold">a)</span>Ser una persona de nacionalidad <span
                class="bold">{{ $datos['nacionalidad']['nacionalidad'] }}</span> y suficiente capacidad para obligarse
            en los términos del
            presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">b)</span> Cuenta con correo electrónico <span
                class="bold">{{ $datos['operacion']['cliente']['email'] != '' ? $datos['operacion']['cliente']['email'] : 'N/A' }}</span>
            y teléfono <span
                class="bold">{{ $datos['operacion']['cliente']['telefono'] != '' ? $datos['operacion']['cliente']['telefono'] : 'N/A' }}</span>.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">c)</span> Se encuentra registrado en Registro Federal de Contribuyente con la clave <span
                class="bold">{{ $datos['operacion']['cliente']['rfc'] != '' ? $datos['operacion']['cliente']['rfc'] : 'N/A' }}</span>.
        </p>
        <p class="justificar line-17 size-13px ">
            <span class="bold">d)</span>Su domicilio se encuentra ubicado <span
                class="bold">{{ $datos['operacion']['cliente']['direccion'] != '' ? $datos['operacion']['cliente']['direccion'] : 'N/A' }}</span>.
            El cual señala como domicilio
            convencional para todos los efectos legales del presente Contrato.
        </p>

        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">e)</span> Recibió de <span class="bold">“El proveedor”</span> toda la información
            relativa al servicio objeto del presente contrato en virtud de las
            Declaraciones anteriores,
            <span class="bold">“Las partes”</span> convienen en obligarse conforme a las siguientes:.
        </p>

        <p class="justificar line-17 size-13px bold center">
            CLÁUSULAS
        </p>
        <p class="justificar line-17 size-13px">
            <span class="bold">PRIMERA. CONSENTIMIENTO.- </span>
            ELas partes manifiestan su voluntad para celebrar el presente Contrato cuya naturaleza jurídica es la
            prestación de Servicios Funerarios de uso inmediato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">SEGUNDA. OBJETO DEL CONTRATO.- </span>
            El objeto del presente Contrato es la prestación de Servicios Funerarios de uso inmediato; por lo
            que “El proveedor” se obliga a prestar el Servicio a “El consumidor” y este en Consecuencia, se Obliga a
            pagar como contraprestación un
            precio cierto y determinado.
        </p>

        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">TERCERA. LUGAR DE LA PRESTACIÓN DE LOS SERVICIOS.- </span>
            Los Servicios objeto del presente Contrato se realizarán en <span
                class="bold uppercase">“{{ $empresa->nombre_comercial }}”</span> ubicada en la calle <span
                class="bold uppercase">{{ $empresa->calle }}</span> No. <span class="bold"
                uppercase>{{ $empresa->num_ext }}</span> COL.
            <span class="bold uppercase">{{ $empresa->colonia }}</span> C.P. <span
                class="bold uppercase">{{ $empresa->cp }}</span>, en
            <span class="bold uppercase">{{ $empresa->ciudad }}</span>,
            <span class="bold uppercase">{{ $empresa->estado }}</span>. “EL proveedor” proporcionará el Servicio
            estipulado en el
            presente contrato para el cuerpo de la
            persona que en vida llevó el
            nombre de <span class="bold">{{ $datos['nombre_afectado'] }}</span>.
        </p>

        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">CUARTA. PAGO DEL SERVICIO.- </span>
            “El consumidor” pagará como contraprestación a “El proveedor” por el Servicio objeto del presente
            Contrato, la cantidad de <span class="bold uppercase">
                $
                {{ number_format($datos['operacion']['subtotal']-$datos['operacion']['descuento'], 2) }}
                ({{ numeros_a_letras($datos['operacion']['subtotal']-$datos['operacion']['descuento']) }}</span>),
            más el Importe
            al Valor
            Agregado de $
            <span class="bold uppercase">{{ number_format($datos['operacion']['impuestos'], 2) }}
                ({{ numeros_a_letras($datos['operacion']['impuestos']) }}</span>), sumando un total de <span
                class="bold uppercase">$
                {{ number_format($datos['operacion']['total'], 2) }}
                ({{ numeros_a_letras($datos['operacion']['total']) }}</span>). El importe señalado en esta Clausula
            contempla todas las cantidades y conceptos referentes al Servicio y a lo señalado en su Anexo; por lo
            que “El proveedor” se obliga a respetar en todo momento dicho costo sin poder cobrar otra cantidad no
            estipulada en el presente Contrato
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">QUINTA. FORMA Y LUGAR DEL PAGO.- </span>
            “El consumidor” efectuará el pago pactado por el Servicio en los términos y condiciones
            acordadas pudiendo ser en efectivo, con tarjeta de débito, tarjeta de crédito, transferencia bancaria, y/o
            cheque en el domicilio de “El
            proveedor”, en moneda nacional.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">SEXTA. OBLIGACIONES DE LAS PARTES.- </span>
            “El Proveedor” se obliga a:
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">a) </span> Informar a “El consumidor” sobre los paquetes de servicios con los que cuenta,
            los precios y tarifas de cada uno de ellos.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">b) </span> Proporcionar a “El consumidor” el catálogo de productos y servicios con que
            cuenta.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">c) </span> Contar con las instalaciones adecuadas para la prestación del Servicio y con
            los vehículos adecuados para la realización del traslado del
            cadáver en caso de que este sea requerido por “El consumidor”.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">d) </span> Contar con las licencias, permisos, autorizaciones, avisos y demás
            documentación que establezca la legislación nacional que regule las
            actividades que comprende el Servicio objeto del presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">e) </span> Contar con el personal calificado para llevar a cabo al Servicio.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">f) </span> Abstenerse de condicionar la prestación del Servicio a la adquisición de un
            paquete o ventas de productos.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">g) </span> Prestar el Servicio conforme a lo ofrecido y/o publicado por “El proveedor”,
            por lo que los costos del Servicio deberán de respetarse a la
            firma del presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">h) </span> Entregar a “El consumidor” la factura, recibo o comprobante en el consten los
            datos específicos del Servicio objeto del presente Contrato.
        </p>

        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">
                “El consumidor”</span> se obliga a:
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">a) </span> Cumplir con lo establecido en el presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">b) </span> Entregar a “El Proveedor” la documentación que este le solicite para la
            presentación del Servicio.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">c) </span> Realizar el pago correspondiente del Servicio conforme a lo establecido en la
            Cláusula Quinta de este Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">SÉPTIMA. VENTA DE DERECHOS DE USO DE LOTES O FOSAS, NICHOS OSARIOS O GAVETAS.- </span>
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            En caso de que en la prestación del
            Servicio se contemple la venta de derechos de uso de lotes o fosas, nichos, osarios o gavetas y de así
            requerirlo “El consumidor”, deberá especificarlos en el Anexo al presente Contrato, por lo que “El
            Proveedor” proporcionara a “El consumidor”
            la siguiente información:
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">a) </span> Datos de localización de lote, fosa, nicho, osario o gaveta, anexando el plano
            de ubicación correspondiente, el cual deberá estar firmado
            y sellado por el concesionario autorizado.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">b) </span> Vigencia de los derechos de uso de lotes o fosas de cementerio o panteón.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">c) </span> Procedimiento para que los restos inhumados en lote temporal tengan la opción
            de pasar a perpetuidad.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">d) </span> En su caso deberá de indicar el monto, periodicidad y lugar de pago de las
            cuotas por concepto de mantenimiento.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">e) </span> Indicar a “El consumidor” que debe cumplir con el Reglamento interno del
            panteón o cementerio respectivo y hacer la entrega de dichos
            reglamento.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">f) </span> Los datos del título de concesión, autorización o contrato que faculte y
            autorice al Proveedor llevar a cabo la venta.
        </p>

        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">OCTAVA. CAUSAS DE RESCISION.- </span>
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            Este Contrato podrá rescindirse por las siguientes causas:
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">a) </span> Que “Las partes” no cumplan con lo estipulado en el presente Contrato.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">b) </span> Si el Servicio prestado no corresponde con lo pactado y/o solicitado por el
            “El consumidor”. En el caso de rescisión del presente Contrato,
            “La parte” que incumpla deberá de pagar la pena convencional establecida en la Cláusula Novena, para lo cual
            deberá informar por escrito a
            la parte que incumplió la causa de rescisión del presente Contrato, para que esta, de así proceder, haga el
            pago correspondiente a la pena
            convencional.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">NOVENA. PENA CONVENCIONAL.- </span>
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            En caso de incumplimiento por alguna de las “Las partes” a las obligaciones objeto del Presente
            contrato, la pena convencional será equivalente al 20(veinte) % del precio total del Servicio, sin incluir
            el Impuesto
            al Valor Agregado (IVA).
            En caso de que alguna de las “Las partes” requiera el pago de la Pena convencional por cualquier
            incumplimiento a lo
            establecido en el
            presente Contrato, deberá de solicitar por escrito el pago de dicha pena, debiendo “La parte” que incumplió
            hacer el
            pago en los 5 (cinco)
            días hábiles siguientes de haber recibido el escrito de incumplimiento.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">NOVENA PRIMERA. AVISO DE PRIVACIDAD.- </span>
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            Previo a la firma del presente Contrato y en cumplimiento a lo dispuesto en la Ley Federal
            de Protección de datos Personales en Posesión de los Particulares, el Proveedor hizo del conocimiento al
            Consumidor del
            aviso de
            privacidad, así como del procedimiento para ejercer los derechos de acceso, rectificación, cancelación y
            oposición al
            tratamiento de sus
            datos personales en adelante, derechos ARCO.
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            <span class="bold">NOVENA SEGUNDA. JURISDICCION.- </span>
        </p>
        <p class="justificar line-17 size-13px -mt-3">
            Para todo lo relativo a la interpretación, aplicación y cumplimiento del presente Contrato, “Las
            partes” acuerdan someterse en la vía administrativa a la Procuraduría Federal del Consumidor, y en caso de
            subsistir las
            diferencias, a la
            jurisdicción de los tribunales competentes del lugar donde se celebra este contrato.
            Leído que fue y una vez hecha la explicación de su alcance legal y contenido, este Contrato se firma por
            duplicado en
            cada una de sus hojas
            y al calce, en la Ciudad de Mazatlan, Sinaloa , el día <span class="bold">{{ fechahora(now()) }}</span>.
            Entregándosele una copia del
            mismo
            a “El
            consumidor”.
        </p>
        @if ($datos['inhumacion_b']==1)
        <p class="center line-17 size-13px bold uppercase">
            sobre la ubicación para inhumar
        </p>
        <div>
            <table class="w-100 size-13px mt-2 datos_tabla uppercase">
                @if ($datos['cementerios_servicio_id']==1)
                <tr class="size-15px center uppercase">
                    <td class="py-1 bold">ubicación</td>
                    <td class="px-1">{{ $datos['terreno']['ubicacion_servicio'] }}</td>
                </tr>
                <tr class="size-15px center uppercase">
                    <td class="py-1 bold" colspan="2">titular del convenio</td>
                </tr>
                <tr class="size-15px center uppercase">
                    <td class="py-1" colspan="2">{{ $datos['terreno']['nombre'] }}</td>
                </tr>
                @else
                <tr class="size-15px center uppercase">
                    <td class="py-1 bold">ubicación</td>
                    <td class="px-1">{{ $datos['nota_ubicacion'] }}</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        @if ($datos['plan_funerario_futuro_b']==1 || $datos['plan_funerario_inmediato_b']==1)
        <p class="center line-17 size-13px bold uppercase">
            sobre el plan de servicios funerarios
        </p>
        @if ($datos['plan_funerario_futuro_b']==1)
        <table class="w-100 size-13px mt-2 datos_tabla uppercase">
            <tr class="size-15px center uppercase">
                <td class="py-1 bold">plan funerario de uso a futuro</td>
                <td class="px-1">{{ $datos['plan_funerario_futuro'] }}</td>
            </tr>
            <tr class="size-15px center uppercase">
                <td class="py-1 bold" colspan="2">titular del convenio</td>
            </tr>
            <tr class="size-15px center uppercase">
                <td class="py-1" colspan="2">{{ $datos['nombre_titular_plan_funerario_futuro'] }}</td>
            </tr>
        </table>
        @else
        <table class="w-100 size-13px mt-2 datos_tabla uppercase">
            <tr class="size-15px center uppercase">
                <td class="py-1 bold">plan funerario de uso inmediato</td>
                <td class="px-1">{{ $datos['plan_funerario_original'] }}</td>
            </tr>
        </table>
        @endif

        @endif


        <p class="center line-17 size-13px bold uppercase">
            anexos (artículos y servicios del contrato)
        </p>

        <div>
            <table class="w-100 size-13px mt-5 datos_tabla uppercase">
                <thead>
                    <tr class="center">
                        <td class="bg-gray bold">No.</td>
                        <td class="bg-gray bold">Cantidad</td>
                        <td class="bg-gray bold">Concepto</td>
                        <td class="bg-gray bold">$ Des.</td>
                        <td class="bg-gray bold">$ Base</td>
                        <td class="bg-gray bold">$ IVA</td>
                        <td class="bg-gray bold">$ Total</td>
                        <td class="bg-gray bold">$ Imp.</td>
                        <td class="bg-gray bold">Facturable</td>
                        <td class="bg-gray bold">P. Funerario</td>
                    </tr>
                </thead>
                @if (count($datos['operacion']['movimientoinventario']['articulosserviciofunerario'] )>0)
                @foreach ($datos['operacion']['movimientoinventario']['articulosserviciofunerario'] as $index=>
                $concepto)
                <tr class="size-15px center">
                    <td class="py-1">{{$index+1}}</td>
                    <td class="px-1">{{ $concepto['cantidad'] }}</td>
                    <td class="px-1">{{ $concepto['descripcion'] }}</td>
                    <td class="px-1">{{ number_format($concepto['descuento'], 2) }}</td>
                    <td class="py-1">{{number_format($concepto['subtotal'], 2) }}</td>
                    <td class="px-1">{{ number_format($concepto['impuestos'], 2) }}</td>
                    <td class="px-1">{{ number_format($concepto['costo_neto'], 2) }}</td>
                    <td class="px-1">{{ number_format($concepto['importe'], 2) }}</td>
                    <td class="px-1">@if ($concepto['facturable_b']==1)
                        SI
                        @else
                        NO
                        @endif</td>
                    <td class="px-1">
                        @if ($concepto['plan_b']==1)
                        SI
                        @else
                        NO
                        @endif
                    </td>
                </tr>
                @endforeach
                @else
                <tr class="size-15px center">
                    <td class="w-100 py-1 px-2" colspan="10">No se han agregado conceptos a este contrato</td>
                </tr>
                @endif
            </table>
            <table class="w-100 size-13px mt-5 datos_tabla">
                <tr class="size-15px">
                    <td class="py-1 w-85 right px-2 bold">Subtotal $</td>
                    <td class="py-1 center">{{number_format($datos['operacion']['subtotal'],2)}}</td>
                </tr>
                <tr class="size-15px center">
                    <td class="py-1 w-85 right px-2 bold">Descuento $</td>
                    <td class="py-1 center">{{number_format($datos['operacion']['descuento'],2)}}</td>
                </tr>
                <tr class="size-15px center">
                    <td class="py-1 w-85 right px-2 bold">TASA IVA</td>
                    <td class="py-1 center">{{$datos['operacion']['tasa_iva']/100}}</td>
                </tr>
                <tr class="size-15px center">
                    <td class="py-1 w-85 right px-2 bold">IVA $</td>
                    <td class="py-1 center">{{number_format($datos['operacion']['impuestos'],2)}}</td>
                </tr>
                <tr class="size-15px center">
                    <td class="py-1 w-85 right px-2 bold">Total $</td>
                    <td class="py-1 center">{{number_format($datos['operacion']['total'],2)}}</td>
                </tr>
            </table>
        </div>

        <div class="w-100 center mt-50">
            <div class="w-50 float-left mt-20">
                 <img src="{{ $firmas['gerente'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class=" pb-1"><span class="texto-base bold uppercase center">
                            <p class=" line-17 size-13px">
                                {{ $empresa['razon_social'] }}
                            </p>
                            <p class=" line-17 size-13px -mt-3">
                                {{ $registro['rep_legal'] }}(REPRESENTANTE LEGAL)
                            </p>

                        </span></div>
                </div>
            </div>
            <div class="w-50 float-right mt-20">
                 <img src="{{ $firmas['contratante'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class=" pb-1"><span class="texto-base bold uppercase center">
                            <p class=" line-17 size-13px">
                                "EL CONSUMIDOR"
                            </p>
                            <p class=" line-17 size-13px -mt-3">
                                {{ $datos['operacion']['cliente']['nombre'] }}
                            </p>

                        </span></div>
                </div>
            </div>
        </div>



        <div class="w-100 center pt-30">
            <p class="justificar line-17 size-13px mt-30">
                <span class="bold">Autorización con fines mercadotécnicos o publicitarios.-</span> “El consumidor” Si (
                ) NO
                ( ) acepta que “El
                Proveedor” Ceda O
                transmita a
                terceros, con fines mercadotécnicos o publicitarios, la información proporcionada con motivo del
                presente
                Contrato y SI
                acepta ( ) NO acepta
                ( ) que “El proveedor” le envié publicidad sobre bienes y servicios.
            </p>
            <div class="w-50 float-left mt-10">
                <img src="{{ $firmas['publicidad'] }}" class="firma">
                <div class="w-90 mr-auto ml-auto border-top pt-1">
                    <div class=" pb-1"><span class="texto-base bold uppercase center">
                            <p class=" line-17 size-13px">
                                Firma de autorización de"El Consumidor"
                            </p>
                        </span></div>
                </div>
            </div>
        </div>


    </div>
</body>

</html>
<span class="uppercase bold texto-sm"></span>
