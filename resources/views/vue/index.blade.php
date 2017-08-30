@extends('layouts.app')

@section('content')
    <{{ $componentName }}
        @foreach ($componentAttributes as $key => $value)
            {{ (is_object($value) || is_array($value)) ? ':' : '' }}{{ kebab_case($key) }}="{{ is_null($value) ? 'null' : $value }}"
        @endforeach
    ></{{ $componentName }}>
@endsection
