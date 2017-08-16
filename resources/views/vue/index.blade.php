@extends('layouts.app')

@section('content')
    <{{ $componentName }}
        @foreach ($componentAttributes as $key => $value)
            :{{ $key }}="{{ is_null($value) ? 'null' : $value }}"
        @endforeach
    ></{{ $componentName }}>
@endsection
