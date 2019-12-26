{{-- Master Layout --}}
@extends('cortex/foundation::managerarea.layouts.auth')

{{-- Page Title --}}
@section('title')
    {{ extract_title(Breadcrumbs::render()) }}
@endsection

{{-- Main Content --}}
@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('frontarea.home') }}"><b>{{ $currentTenant->name }}</b></a>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">{{ trans('cortex/auth::common.reauthentication.password') }}</p>

            {{ Form::open(['url' => route('managerarea.reauthentication.password.process'), 'id' => 'managerarea-reauthentication-form', 'role' => 'auth']) }}

                <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                    {{ Form::password('password', ['class' => 'form-control input-lg', 'placeholder' => trans('cortex/auth::common.password'), 'required' => 'required']) }}

                    @if ($errors->has('password'))
                        <span class="help-block">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                {{ Form::button('<i class="fa fa-sign-in"></i> '.trans('cortex/auth::common.login'), ['class' => 'btn btn-lg btn-primary btn-block', 'type' => 'submit']) }}

            {{ Form::close() }}

        </div>

    </div>

@endsection
