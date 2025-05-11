@extends('layout.main') @section('content')

<section>
    <div class="container-fluid">
        <form action="{{ route('documents.export') }}" method="GET">
            @csrf
            <label for="start_date">Fecha de Inicio:</label>
            <input type="date" id="start_date" name="start_date" required>
        
            <label for="end_date">Fecha de Fin:</label>
            <input type="date" id="end_date" name="end_date" required>
        
            <button type="submit">Generar PDF</button>
        </form>
    </div>    
</section>
@endsection
