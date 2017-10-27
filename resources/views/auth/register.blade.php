@extends('layouts.app')

@section('content')
    <div class="grid grid-gap-1 grid-narrow">
        <div class="card p-3">
            <h1 class="title has-text-centered">Register</h1>

            <form method="POST" action="{{ route('register') }}">
                {{ csrf_field() }}

                <b-field label="Username"
                    @if ($errors->has('name'))
                        type="is-danger"
                        message="{{ $errors->first('name') }}"
                    @endif>
                    <b-input name="name"
                        value="{{ old('name') }}"
                        maxlength="20">
                    </b-input>
                </b-field>

                <b-field label="Email Address"
                    @if ($errors->has('email'))
                        type="is-danger"
                        message="{{ $errors->first('email') }}"
                    @endif>
                    <b-input type="email" name="email"
                        value="{{ old('email') }}">
                    </b-input>
                </b-field>

                <b-field label="Password"
                    @if ($errors->has('password'))
                        type="is-danger"
                        message="{{ $errors->first('password') }}"
                    @endif>
                    <b-input type="password" name="password"
                        value="{{ old('password') }}">
                    </b-input>
                </b-field>

                <b-field label="Confirm Password" class="m-b-4"
                    @if ($errors->has('password_confirmation'))
                        type="is-danger"
                        message="{{ $errors->first('password_confirmation') }}"
                    @endif>
                    <b-input type="password" name="password_confirmation"
                        value="{{ old('password_confirmation') }}">
                    </b-input>
                </b-field>

                <b-checkbox name="terms">
                    I agree to the terms &amp; conditions
                </b-checkbox>

                <button type="submit" class="button is-primary is-pulled-right">
                    Register
                </button>
            </form>
        </div>
    </div>
@endsection
