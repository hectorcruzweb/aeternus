@extends('layouts.lista_pdf_layout')
@section('title', 'Reporte de usuarios del sistema')
@section('contenido')
  <section class="data">
    <table>
      <thead>
        <tr>
          <th>Clave</th>
          <th>Nombre</th>
          <th>Usuario</th>
          <th>Rol</th>
          <th>Genero</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($usuarios as $item)
          <tr>
            <td>{{$item->id_user}}</td>
            <td>{{$item->nombre}}</td>
            <td>{{$item->email}}</td>
            <td>{{$item->rol}}</td>
            <td>{{$item->genero_des}}</td>
            <td>{{$item->status_des}}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>
@endsection
