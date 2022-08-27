@extends('layouts.lista_pdf_layout')
@section('title', 'Reporte de articulos del sistema')
@section('contenido')
  <section class="data">
    <table>
      <thead>
        <tr>
          <th>Codigo de barras</th>
          <th>Tipo de producto</th>
          <th>Nombre</th>
          <th>Almacen</th>
          <th>Localizacion</th>
          <th>Existencia</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($articulos as $item)
          <tr>
            <td>{{ $item->codigo_barras }}</td>
            <td>{{ $item->tipoProducto->tipo }}</td>
            <td>{{ $item->nombre }}</td>
            <td>{{ $item->almacen ? $item->almacen->almacen : 'N/A' }}</td>
            <td>{{ $item->almacen ? $item->localizacion : 'N/A' }}</td>
            <td>{{ $item->tipos_producto_id == 3 || $item->tipos_producto_id == 2  ? 'N/A' : $item->existencia }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>
@endsection
