@extends('layouts.app')

@section('title', 'Panier - Dropshipping Platform')

@section('content')
    <div id="react-cart" 
         data-cart='@json($cartArray ?? [])'
         data-total="{{ $total }}"></div>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
    <script src="{{ asset('js/react-cart.js') }}" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
@endsection

