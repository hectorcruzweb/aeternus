<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <style>
    table {
      width: 100%;
      padding: 0px 0px 10px 0px;
    }
  </style>
  <script>
    function subst() {
      var vars = {};
      var query_strings_from_url = document.location.search.substring(1).split('&');
      for (var query_string in query_strings_from_url) {
          if (query_strings_from_url.hasOwnProperty(query_string)) {
              var temp_var = query_strings_from_url[query_string].split('=', 2);
              vars[temp_var[0]] = decodeURI(temp_var[1]);
          }
      }
      var css_selector_classes = ['page', 'frompage', 'topage', 'webpage', 'section', 'subsection', 'date', 'isodate', 'time', 'title', 'doctitle', 'sitepage', 'sitepages'];
      for (var css_class in css_selector_classes) {
          if (css_selector_classes.hasOwnProperty(css_class)) {
              var element = document.getElementsByClassName(css_selector_classes[css_class]);
              for (var j = 0; j < element.length; ++j) {
                  element[j].textContent = vars[css_selector_classes[css_class]];
              }
          }
      }
  }
  </script>
</head>
@include('layouts.estilos')

<body class="m-0 p-0" onload="subst()">
  <table class="capitalize pt-3">
    <tr>
      <td colspan="2">
        <div class="w-100 border-top-black-1">

        </div>
      </td>
    </tr>
    <tr>
      <td align="left" class="texto-sm">
        <span>Salas de Velación</span><br>
        <span>{{ $empresa->calle }} # {{ $empresa->num_ext }}</span><br>
        <span>Col. {{ $empresa->colonia }} C.P. {{ $empresa->cp }} </span><br>
        <span>Tel. {{ $empresa->telefono }}</span><br>
        <span>Fax. {{ $empresa->fax }}</span>
      </td>
      <td align="right" class="texto-sm">
        <span>Parque Funerario y Horno Crematorio</span><br>
        <span>{{ $empresa->cementerio['calle'] }} # {{ $empresa->cementerio['num_ext'] }}</span><br>
        <span>{{ $empresa->cementerio['ciudad'] }}, {{ $empresa->cementerio['estado'] }} </span><br>
        <span>Tel. {{ $empresa->cementerio['telefono'] }}</span><br>
        <span>Fax. {{ $empresa->cementerio['fax'] }}</span>
      </td>
    </tr>
  </table>
</body>

</html>