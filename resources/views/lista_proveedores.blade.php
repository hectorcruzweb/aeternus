@extends('layouts.lista_pdf_layout')
@section('title', 'Reporte de proveedores del sistema')
@section('contenido')
  <section class="data">
    <table>
      <thead>
        <tr>
          <th>Clave</th>
          <th>Nombre comercial</th>
          <th>Razon social</th>
          <th>RFC</th>
          <th>Contacto</th>
          <th>Telefono</th>
          <th>Correo</th>
          <th>Estatus</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($usuarios as $item)
          <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->nombre_comercial }}</td>
            <td>{{ $item->razon_social }}</td>
            <td>{{ $item->rfc }}</td>
            <td>{{ $item->nombre_contacto }}</td>
            <td>{{ $item->telefono }}</td>
            <td>{{ $item->email }}</td>
            <td>{{ $item->status == 1 ? 'Activo' : 'Inactivo' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>
@endsection
