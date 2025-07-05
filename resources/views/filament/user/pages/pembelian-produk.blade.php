@extends('layouts.app')

@section('content')
    @if ($isStockis)
        @livewire('user.pembelian-produk-stockis')
    @else
        @livewire('user.pembelian-produk-mitra')
    @endif
@endsection
