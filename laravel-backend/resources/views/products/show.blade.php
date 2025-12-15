@extends('layouts.app')

@section('title', $product->name . ' - Dropshipping Platform')

@section('content')
    <div id="react-product-detail" 
         data-product='@json($product)'></div>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
    <script src="{{ asset('js/react-product-detail.js') }}" defer></script>
@endsection

