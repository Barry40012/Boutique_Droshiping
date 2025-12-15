@extends('layouts.app')

@section('title', 'Checkout - Dropshipping Platform')

@section('content')
    <div id="react-checkout" 
         data-cart='@json($cartArray ?? [])'
         data-total="{{ $total }}"
         data-errors='@json($errors->toArray() ?? [])'
         data-old='@json(old() ?? [])'></div>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
    <script src="{{ asset('js/react-checkout.js') }}" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
@endsection

