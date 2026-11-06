 <!DOCTYPE html>
 <html>

 <head>
     <meta charset="UTF-8">
     <style>
         table {
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
             var css_selector_classes = ['page', 'frompage', 'topage', 'webpage', 'section', 'subsection', 'date', 'isodate',
                 'time', 'title', 'doctitle', 'sitepage', 'sitepages'
             ];
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
     <!-- Pie discreto del comprobante comercial; la paginación la completa Snappy. -->
     <table style="border-top:1px solid #9aa3a9;font-family:Arial,sans-serif;color:#879198;font-size:10px;padding-top:8px">
         <tr><td align="left">Aeternus Funerales · Venta en general núm. {{ $datos['ventas_generales_id'] }}<br>
         Generado: {{ now('America/Mazatlan')->locale('es')->translatedFormat('d/M/Y H:i') }} · {{ parse_url(config('app.url'), PHP_URL_HOST) }}</td>
         <td align="right" style="text-align:right;vertical-align:bottom">Pág. <span class="page"></span>/<span class="topage"></span></td></tr>
     </table>
 </body>

 </html>
