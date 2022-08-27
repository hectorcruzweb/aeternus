<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
  <title>Reportes</title>
  <style>
    body{
      font-family: 'Open Sans' !important;
    }

    #header,#header section table{
      margin-top: -30px !important;
      width: 100%;
      padding-top: 0px;
    }

    #header section table {
      border-collapse: collapse;
      color: #{{env('TEXTOSCOLOR')}};
    }

    #header section table, #header section table th, #header section table td {
      border-bottom: 1px solid #{{env('MAINCOLOR')}};
    }

     #header section table td {
       padding:10px;
     }

    .logo{
      max-width: 270px;
    }

    h1{
      font-size: 1.1em;
      line-height: .8em !important;
      text-transform: uppercase;
      text-align: right;
      color:  #{{env('MAINCOLOR')}};
    }

    .datos{
      text-align: right !important;
      font-size: .8em;
      line-height: 0.7em !important;
      text-transform: capitalize !important;
    }



     .data table thead tr th{
        padding: 6px;
     }
    .data table thead tr th{
      font-size: .9em;
      text-align: center;
      color:  #{{env('MAINCOLOR')}};
    }

    .data table tr td{
      text-align: center;
    }

    .data table {
      margin-top: 30px !important;
      width: 100%;
      border-collapse: collapse;
    }

    .data table th,  .data table tr,.data table td {
      border: 1px solid #{{env('SECONDCOLOR')}};
    }

  </style>
</head>
<body>
  <header id="header">
    <section>
      <table>
        <tr>
          <td style="width:40%;">
          <img src="{{public_path(env('LOGOJPG'))}}" alt="" class="logo">
          </td>
          <td style="width:60%;">
            <h1>
              @yield('title')
            </h1>
            <p class="datos">
              {{$empresa->razon_social}}
            </p>
            <p class="datos">
              {{strtolower($empresa->calle)}} Núm. Ext {{$empresa->num_ext}} , Col. {{strtolower($empresa->colonia)}}.
            </p>
            <p class="datos">
              {{$empresa->ciudad}}  {{$empresa->estado}} Tel. {{$empresa->telefono}}. <br>
            </p>
             <p class="datos">
             Impresión, {{fechahora_completa()}}.
            </p>
          </td>
        </tr>
      </table>
    </section>
  </header>
  @yield('contenido')
</body>
</html>