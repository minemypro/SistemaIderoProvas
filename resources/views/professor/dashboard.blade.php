@extends('layouts.app')
@section('title', 'Dashboar Docente Admin')
@section('content')
<div class="container mt-4">
    <h2>Bem-vindo. {{ Auth::user()->name }} (Professor)</h2>
</div>
@endsection
