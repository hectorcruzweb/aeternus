<style>
    body{
        margin:0 !important;
        padding: 0 !important;
    }
    .mail{
        padding: 20px 20px 20px 20px !important;
        min-height: 100vh;
        width: 100% !important;
        background-color:#{{env('GRAYCOLOR')}};
    }

    .informacion{
        padding: 10px 30px 10px 30px;
        width: 600px !important;
        background-color: #fff;
        margin: auto; 
        border: 1px solid #ccc;
    }
    .logo{
        max-width: 140px;
        display: block;
        margin-right: auto ;
       
   }

   .gracias{
        color: #404040!important;
        display: block;
        font-family: 'Proxima Nova','Open Sans','Helvetica Neue',Calibri,Helvetica,sans-serif;
        font-size: 28px;
        font-style: normal;
        font-weight: 100;
        line-height: 140%;
        letter-spacing: normal;
        margin-top: 20px;
        margin-right: 0;
        margin-bottom: 15px;
        margin-left: 0;
        text-align: left;
   }

   .mensaje{
    font-family: sans-serif;
    font-size: 16px;
    line-height: 25px;
    color: #666666;
   }

   .documento{
       text-transform: uppercase;
       font-weight: 600;
   }

   .datos{
    font-family: sans-serif;
    font-size: 16px;
    line-height: 22px;
    color: #666666;
   }

   .dato{
    font-family: sans-serif;
    font-size: 14px;
    line-height:10px;
    color: #666666;
    text-transform: capitalize;
   }

   .dudas{
       margin-top: 15px !important;
   }
</style>
<div class="mail">
    
    <div class="informacion">
     <img class="logo" src="{{asset('images/logo/aeternus.jpg')}}" alt="">
     <h2 class="gracias">
         Hola,  {{$client_name}}.
     </h2>
     <p class="mensaje">
        A continuación te adjuntamos el (los) siguientes documentos: 
        <br>
        <span class="documento">
            {{$name_pdf}}
        </span>
     </p>
    <p class="datos">
             Para dudas o más información puedes contactarnos a través de los siguientes datos:
    </p>
     <div class="dudas">
         
         <p class="dato">
             {{$empresa->nombre_comercial}}, {{$empresa->razon_social}}
         </p>
          <p class="dato">
            {{strtolower($empresa->calle)}} Núm. Ext {{$empresa->num_ext}} , Col. {{strtolower($empresa->colonia)}}. {{strtolower($empresa->ciudad)}}, {{strtolower($empresa->estado)}}.
         </p>
         <p class="dato">
             Teléfono {{$empresa->telefono}} , Correo Electrónico: <span style="text-transform: lowercase;">{{strtolower($empresa->email)}}</span>
         </p>
     </div>
    </div>
</div>

