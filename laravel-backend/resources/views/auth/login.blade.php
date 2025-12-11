@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <div class="max-w-md mx-auto bg-white border rounded-lg p-6 shadow">
        <h1 class="text-2xl font-bold mb-4">Connexion</h1>

        @if ($errors->any())
            <div class="mb-4 text-red-600 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Mot de passe</label>
                <input name="password" type="password" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex items-center gap-2">
                <input id="remember" type="checkbox" name="remember" class="rounded">
                <label for="remember" class="text-sm text-gray-600">Se souvenir de moi</label>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Se connecter</button>
        </form>
        <p class="text-sm text-gray-600 mt-4">
            Pas de compte ? <a href="{{ url('/register') }}" class="text-blue-600 hover:underline">Créer un compte</a>
        </p>
    </div>
@endsection

