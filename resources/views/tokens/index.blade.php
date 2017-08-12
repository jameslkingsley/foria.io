@extends('layouts.app')

@section('content')
    <f-token-checkout :user="{{ auth()->user() }}"></f-token-checkout>
@endsection
