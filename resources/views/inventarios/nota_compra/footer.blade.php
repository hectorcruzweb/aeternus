 <!DOCTYPE html>
  <html><head>
  <meta charset="UTF-8">
    <style>
    table{
      width: 100%;
      padding: 0px 0px 5px 0px;
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
  </script></head>
  @include('layouts.estilos')
  <body class="m-0 p-0" onload="subst()">
  <table class="italic">
    <tr>
      <td align="left" class="texto-sm">
 Impresión, {{fechahora_completa()}}. 
      </td>
      <td style="text-align:right">
       Pág. <span class="page"></span> de <span class="topage"></span>
      </td>
    </tr>
  </table>
  </body></html>
