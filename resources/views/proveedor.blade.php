@extends('layouts.lista_pdf_layout')
@section('title', 'REPORTE DE PROVEEDOR')
@section('contenido')
<style>
#proveedor tbody tr td {
    text-align: left;
    padding: 2px 4px;
    font-size: 12pt;
}

#proveedor tbody tr td:nth-child(1) {
    font-weight: bold;
    color: #{{env('maincolor')}};
}

#proveedor {
    text-transform: uppercase;
}

.header td{
    color: white !important;
    text-align: left;
    font-size: 15pt;
    font-weight: bold;
    padding: 2px 8px;
    background-color: #{{env('maincolor')}};
}

</style>
  <section class="data">
    <table id="proveedor">
            <tbody>
                <tr class="header">
                    <td colspan="2">Información basica</td>
                </tr>
                <tr>
                    <td>Nombre comercial:</td>
                    <td>{{ $proveedor->nombre_comercial }}</td>
                </tr>
                <tr>
                    <td>Razon social:</td>
                    <td>{{ $proveedor->razon_social }}</td>
                </tr>
                <tr>
                    <td>RFC:</td>
                    <td>{{ $proveedor->rfc }}</td>
                </tr>
                <tr>
                    <td>Estado:</td>
                    <td>{{ $proveedor->status == "1" ? 'Activo' : 'Inactivo' }}</td>
                </tr>
                <tr class="header">
                    <td colspan="2">Información de contacto</td>
                </tr>
                <tr>
                    <td>Nombre del contacto:</td>
                    <td>{{ $proveedor->nombre_contacto }}</td>
                </tr>
                <tr>
                    <td>Telefono:</td>
                    <td>{{ $proveedor->telefono }}</td>
                </tr>
                <tr>
                    <td>Sitio WEB del proveedor:</td>
                    <td>{{ $proveedor->pagina_web ?  $proveedor->pagina_web : 'Sin sitio WEB'}}</td>
                </tr>
                <tr>
                    <td>Correo electronico:</td>
                    <td style="text-transform: initial;">{{ $proveedor->email }}</td>
                </tr>
                <tr class="header">
                    <td colspan="2">Direccion</td>
                </tr>
                <tr>
                    <td>Pais:</td>
                    <td>{{ $proveedor->pais }}</td>
                </tr>
                <tr>
                    <td>Estado:</td>
                    <td>{{ $proveedor->estado }}</td>
                </tr>
                <tr>
                    <td>Ciudad:</td>
                    <td>{{ $proveedor->ciudad }}</td>
                </tr>
                <tr>
                    <td>Calle:</td>
                    <td>{{ $proveedor->calle }}</td>
                </tr>
                <tr>
                    <td>Numero Exterior:</td>
                    <td>{{ $proveedor->num_ext }}</td>
                </tr>
                <tr>
                    <td>Numero Interior:</td>
                    <td>{{ $proveedor->num_int }}</td>
                </tr>
                <tr>
                    <td>CP:</td>
                    <td>{{ $proveedor->cp }}</td>
                </tr>
            </tbody>
        </table>
  </section>
@endsection