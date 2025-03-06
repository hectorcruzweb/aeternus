<div class="page-break">
    <div class="px-2 py-2 mt-4 bg-gray texto-sm bold">
        NOTA / COMENTARIOS:
    </div>
    <div class="texto-base px-2">
        {!! $datos['nota'] !!}
    </div>
</div>
@if ($datos['status'] == 0)
    <div class="page-break">
        <div class="px-2 py-2 mt-4 bg-gray texto-sm bold text-danger uppercase">
            NOTA DE CANCELACIÓN (Canceló:
            {{ $datos['cancelo']['nombre'] }}, {{ $datos['fechahora_cancelacion_texto'] }}):
        </div>
        <div class="texto-base px-2">
            {!! $datos['nota_cancelacion'] !!}
        </div>
    </div>
@endif
