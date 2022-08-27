<!DOCTYPE html>
  <html><head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
    <style>
    body{
      font-family: 'Open Sans' !important;
    }

    table{
      width: 100%;
      padding: 0px 0px 10px 0px;
      border-bottom: 1px solid #{{env('maincolor')}};
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
  </script></head><body onload="subst()">
  <table>
    <tr>
      <td style="text-align:right">
        Pág. <span class="page"></span> de <span class="topage"></span>
      </td>
    </tr>
  </table>
  </body></html>