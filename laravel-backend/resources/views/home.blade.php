@extends('layouts.app')

@section('title', 'Accueil - Boutique Dropshipping')

@section('content')
    <div id="react-home" data-products='@json($products)'></div>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
    <script src="{{ asset('js/react-home.js') }}" defer></script>
@endsection

