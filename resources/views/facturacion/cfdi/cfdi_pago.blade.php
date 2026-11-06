{{-- CFDI tipo P: reutiliza encabezado, receptor, marcas de agua y pie fiscal
     del formato comercial. La rama esCfdiPago muestra DoctoRelacionado y Monto
     del complemento, conservando los saldos e importes recibidos desde el XML. --}}
@include('facturacion.cfdi.cfdi_ingreso_egreso', ['esCfdiPago' => true])
