@extends('layouts.app')

@section('content')
    <div class="grid grid-gap-1 grid-narrow">
        <div class="card p-3">
            <h1 class="title has-text-centered">Login</h1>

            <form method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}

                <input type="hidden" name="referrer" value="{{ request('ref', '') }}">

                <b-field label="Email Address"
                    @if ($errors->has('email'))
                        type="is-danger"
                        message="{{ $errors->first('email') }}"
                    @endif>
                    <b-input type="email" name="email"
                        value="{{ old('email') }}">
                    </b-input>
                </b-field>

                <b-field label="Password" class="m-b-4"
                    @if ($errors->has('password'))
                        type="is-danger"
                        message="{{ $errors->first('password') }}"
                    @endif>
                    <b-input type="password" name="password"
                        value="{{ old('password') }}">
                    </b-input>
                </b-field>

                <b-checkbox name="remember">
                    Remember Me
                </b-checkbox>

                <button type="submit" class="button is-primary is-pulled-right">
                    Login
                </button>

                <div class="field">
                    <a href="{{ route('password.request') }}">
                        <small>Forgot Your Password?</small>
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
