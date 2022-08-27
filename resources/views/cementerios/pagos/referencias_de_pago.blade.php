<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"
    rel="stylesheet">
  <title>Reportes</title>
  <style>
    .logos {
      min-width: 200px;
      max-width: 200px;
    }

    .banco {
      border: 2px solid #E5E8E8 !important;
    }

    .logo {
      display: block;
      margin-right: auto;
    }

    .santander {
      color: #D31413 !important;
    }

    .digito {
      padding: 3px 7px 3px 7px;
      border: 1px solid #dae1e7;
      font-size: 1em;
      line-height: 1.5em;
    }

    .barcode div {
      min-height: 40px !important;
    }

    .bg-total {
      background-color: #FE0000;
      color: #fff;
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
  </style>
</head>

<body>
  @include('layouts.estilos')


  @foreach ($datos['pagos_programados'] as $key=>$pago)
  @php
  if($id_pago!=''){
  if($id_pago!=$pago['id']){
  continue;
  }
  }
  @endphp


  <div class="pagos relative">
    <table class="w-100">
      <thead>
        <tr>
          <th class="w-70">
            <img class="logo logos -mt-6" src="{{asset('images/aeternus/LogoEmp.jpg')}}" alt="">
          </th>
          <th class="w-30">
            <div class="numeros-contrato">
              <div class="control bg-gray size-13px">
                solicitud de servicio
              </div>
              <p class="control-valor">
                {{ $datos['numero_solicitud_texto'] }}
              </p>

              <div style=""></div>
              <div class="control bg-gray size-13px">
                Número de convenio
              </div>
              <p class="control-valor">
                {{ $datos['numero_convenio'] }}
              </p>
            </div>
          </th>
        </tr>
      </thead>
    </table>
    <div class="ficha">
      <span class="bold size-25px letter-spacing-1">Ficha de Pago</span>
      <p class="texto-base justificar line-base">
        Puede realizar el pago en la sucursal de <span class="capitalize bold">{{$empresa->nombre_comercial}}</span>
        llevando este documento o en
        cualquier sucursal
        <span class="santander">Santander</span> (depósito o transferencia), debiendo tomar en cuenta
        los plazos y cantidades establecidas según su contrato.
      </p>

      <table class="w-100 texto-base">
        <tbody>
          <tr>
            <td width="21%">
              <div class="py-1 bg-gray-dark">
                <div class="px-7px bold"> Nombre del Titular</div>
              </div>
            </td>
            <td width="42%">
              <div class="px-1px">
                <div class="bg-gray px-7px py-1">
                  {{$datos['nombre']}}
                </div>
              </div>
            </td>
            <td width="27%">
              <div class="px-1px">
                <div class="bg-gray-dark px-7px py-1 bold">
                  Núm. Venta (Cementerio)
                </div>
              </div>

            </td>
            <td width="10%">
              <div class="bg-gray px-6px py-1 center">
                {{$datos['ventas_terrenos_id']}}
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="banco mt-4 texto-sm">
        <div class="bg-gray-dark ">
          <table class="w-100">
            <tbody>
              <tr>
                <td width="100%" class="py-1 px-1 semibold texto-sm">
                  Ficha de pago Banco <span class="santander">Santander</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <table class="w-100 mt-1">
          <tbody>
            <tr>
              <td width="50%" class="py-1 px-2">
                <img class="logos" src="{{asset('images/santander.png')}}" alt="">
              </td>
              <td width="50%" class="py-1 px-2">
                <div class="barcode w-100 right">
                  <div class="w-content center w-normal">
                    {!!DNS1D::getBarcodeHTML($pago['id']), "C128")!!}
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        @if ($pago['status_pago']==2)
        <!--pagado-->
        <table class="w-100">
          <tbody>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Estatus:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px text-success">pagado</span>
              </td>
            </tr>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Fecha Límite de Pago:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px">{{fecha_no_day($pago['fecha_a_pagar'])}}</span>(Solicitar una nueva ficha
                en caso de vencimiento).
              </td>
            </tr>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Concepto:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px">{{$pago['concepto_texto']}}</span>
              </td>
            </tr>

            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Monto pago:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px">$ {{number_format($pago['total'],2)}} Pesos MXN </span>
              </td>
            </tr>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Penalización:</span>
              </td>
              <td width="74%" class=" px-2 uppercase">
                @if ($pago['dias_vencido']>0)
                <span class="bold size-15px">$ {{number_format($pago['intereses_a_pagar'],2)}} Pesos MXN
                  ({{$pago['dias_vencido']}} Días de retraso)</span>
                @else
                <span class="bold size-15px">$ {{number_format($pago['intereses_a_pagar'],2)}} Pesos MXN</span>
                @endif
              </td>
            </tr>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-18px">total a pagar:</span>
              </td>
              <td width="74%" class=" px-2 uppercase">
                <span class="bold size-21px">$ {{number_format($pago['total_a_pagar'],2)}} Pesos MXN </span>
              </td>
            </tr>
            <tr>
              <td width="100%" colspan="2" class="px-2 uppercase pt-2">
                ({{numeros_a_letras($pago['total_a_pagar'])}} Pesos MXN, incluye IVA, descuentos y las penalizaciones
                que apliquen)
              </td>
            </tr>
          </tbody>
        </table>
        @else
        <table class="w-100">
          <tbody>
            @if ($pago['status_pago']==0)
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Estatus:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px text-danger">pendiente vencido</span>
              </td>
            </tr>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Fecha Programada:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px">{{fecha_no_day($pago['fecha_programada'])}}</span>.
              </td>
            </tr>
            @else
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Estatus:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px text-black">pendiente</span>
              </td>
            </tr>
            @endif

            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Fecha Límite de Pago:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px">{{fecha_no_day($pago['fecha_a_pagar'])}}</span>(Solicitar una nueva ficha
                en caso de vencimiento).
              </td>
            </tr>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Concepto:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px">{{$pago['concepto_texto']}}</span>
              </td>
            </tr>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Monto pago:</span>
              </td>
              <td width="74%" class="px-2 uppercase">
                <span class="bold size-15px">$
                  {{number_format($pago['monto_programado']-$pago['abonado_capital']-$pago['descontado_pronto_pago']-$pago['descontado_capital']-$pago['complementado_cancelacion'],2)}}
                  Pesos MXN
                </span>
              </td>
            </tr>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-14px">Penalización:</span>
              </td>
              <td width="74%" class=" px-2 uppercase">
                @if ($pago['dias_vencido']>0)
                <span class="bold size-15px">$ {{number_format($pago['intereses'],2)}} Pesos MXN
                  ({{$pago['dias_vencido']}} Días de retraso)</span>
                @else
                <span class="bold size-15px">$ {{number_format($pago['intereses'],2)}} Pesos MXN</span>
                @endif
              </td>
            </tr>
            <tr>
              <td width="26%" class="px-2 uppercase py-1">
                <span class="bg-gray px-1 bold size-18px">total a pagar:</span>
              </td>
              <td width="74%" class=" px-2 uppercase">
                <span class="bold size-21px">$ {{number_format($pago['saldo_neto'],2)}} Pesos MXN </span>
              </td>
            </tr>
            <tr>
              <td width="100%" colspan="2" class="px-2 uppercase pt-2">
                ({{numeros_a_letras($pago['saldo_neto'])}} Pesos MXN, incluye IVA, descuentos y las penalizaciones
                que apliquen)
              </td>
            </tr>
          </tbody>
        </table>
        @endif

        <table class="w-100">
          <tbody>
            <tr>
              <td width="30%" class="py-1 px-2 uppercase">
                <span class="bg-gray bold px-1">Número de Referencia pago</span>
              </td>
              <td width="70%" class="py-1 px-2">
                <p class="digitos right">
                  @for($i = 0; $i < strlen($pago['referencia_pago']); $i++) <span class="digito">
                    {{$pago['referencia_pago'][$i]}}</span>
                    @endfor
                </p>
              </td>
            </tr>
          </tbody>
        </table>
        <table class="w-100">
          <tbody>
            <tr>
              <td width="60%" class=" px-2 uppercase">
                <div class="pt-1">
                  <div class="bg-gray-dark px-1 bold w-content">para depósitos en ventanilla de banco</div>
                  <div class=" mt-3 px-1"><span class="bold">Número de cuenta</span>: {{$empresa->cuenta}}</div>
                </div>
                <div class="pt-3">
                  <div class="bg-gray-dark px-1 bold w-content">para transferencias electrónicas</div>
                  <div class=" mt-3 px-1"> <span class="bold">Clabe: </span> {{$empresa->clabe}}</div>
                  <div class=" mt-3 px-1"> <span class="bold">Beneficiario:</span> {{$empresa->razon_social}}</div>
                </div>
              </td>
              <td width="40%" class="py-1 px-2 uppercase">
                <table class="w-100">
                  <tr>
                    <td class="w-100 px-2 pt-2 center">
                      <div class="w-100 mr-auto ml-auto border-top-black-1 pt-1 mt-20">
                        firma o sello del banco
                      </div>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="instrucciones">
      <div class="mt-3">
        <span class="bold uppercase">nota importante:</span>
      </div>

      <p class="texto-sm justificar line-base">
        Este documento no es un comprobante de pago si no cuenta con el sello del banco o la firma del personal de
        <span class="capitalize bold">{{$empresa->nombre_comercial}}</span> que haya registrado el pago.
      </p>
      <p class="texto-sm justificar line-base">
        El titular deberá hacer llegar una copia del comprobante de pago al personal responsable de <span
          class="capitalize bold">{{$empresa->nombre_comercial}}</span>.
      </p>
      <p class="texto-sm justificar line-base">
        El titular de este convenio deberá guardar las copias de los recibos para futuras aclaraciones.
      </p>
      <p class="texto-sm justificar line-base">
        Para solicitar su factura, debe acudir a las instalaciones de <span
          class="capitalize bold">{{$empresa->nombre_comercial}}</span> a más tardar a fin de mes que haya realizado la
        operación de pago.
      </p>


      <p class="texto-sm justificar line-base">
        <span class="uppercase bold texto-sm underline pr-2">cláusula Vigésima tercera.- </span>
        En caso de retrasarse en el pago mensual de las aportaciones, “El Cliente” deberá cubrir una pena
        convencional sobre el total del monto de la mensualidad vencida,
        importe que se considerará como aportación
        adicional complementaria al cliente. El contratante se obliga a pagar a la agencia funeraria interés
        moratorio del <span class="bold">{{$datos['ajustes_politicas']['tasa_fija_anual']}}</span>%
        ({{ NumerosEnLetras::convertir($datos['ajustes_politicas']['tasa_fija_anual'],'',false) }} por ciento) fija
        anual, la que se calculará y liquidará sobre
        cantidades que adeude el Contratante a la Agencia Funeraria. Los intereses moratorios se
        calcularán multiplicando el monto de lo que adeude el contratante por la tasa de interés
        anual, dividida entre 365, este resultado se multiplica por el número de días transcurridos
        entre la fecha de pago que debió ser hecho y la fecha que el contratante liquide el adeudo.
      </p>

    </div>
  </div>
  @php
  if($pago['id']==$id_pago){
  break;
  }
  @endphp
  @isset($datos['pagos_programados'][($key+1)])
  <div style="display:block; clear:both; page-break-after:always;"></div>
  @endisset

  @endforeach

</body>

</html>