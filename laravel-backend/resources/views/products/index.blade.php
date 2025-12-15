@extends('layouts.app')

@section('title', 'Produits - Dropshipping Platform')

@section('content')
    <div id="react-products" 
         data-products='@json($products->items())'
         data-pagination='@json($pagination)'></div>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
    <script src="{{ asset('js/react-products.js') }}" defer></script>
@endsection

