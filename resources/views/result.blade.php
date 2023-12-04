@extends('layouts.app')

@section('content')
<h2>Resultado del Procesamiento</h2>
<p>{{ $message }}</p>

@if(isset($importVentas) && $importVentas->count() > 0)
<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo Comprobante</th>
            <th>Punto Venta</th>
            <th>Número Comprobante</th>
            <th>Importe Venta</th>
            <th>CUIT Cliente</th>
        </tr>
    </thead>
    <tbody>
        @foreach($importVentas as $importVenta)
        <tr>
            <td>{{ $importVenta->Fecha }}</td>
            <td>{{ $importVenta->TipoComprobante }}</td>
            <td>{{ $importVenta->PuntoVenta }}</td>
            <td>{{ $importVenta->NumeroComprobante }}</td>
            <td>{{ $importVenta->ImporteVenta }}</td>
            <td>{{ $importVenta->CUITCliente }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>No hay datos para mostrar.</p>
@endif
@endsection
