@extends('layouts.app')

@section('title', 'Connexion - Dropshipping Platform')

@section('content')
    <div id="react-login" 
         data-errors='@json($errors->getMessages())'
         data-old='@json(old())'></div>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
    <script src="{{ asset('js/react-login.js') }}" defer></script>
@endsection

