{{-- Master Layout --}}
@extends('cortex/foundation::frontarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ extract_title(Breadcrumbs::render()) }}
@endsection

{{-- Scripts --}}
@push('inline-scripts')
    {!! JsValidator::formRequest(Cortex\Auth\Http\Requests\Frontarea\EmailVerificationSendRequest::class)->selector('#frontarea-verification-email-request-form')->ignore('.skip-validation') !!}
@endpush

@section('body-attributes')class="auth-page"@endsection

{{-- Main Content --}}
@section('content')

    <div class="container">

        <div class="row">

            <div class="col-md-4 col-md-offset-4">

                <section class="auth-form">

                    {{ Form::open(['url' => route('frontarea.verification.email.send'), 'id' => 'frontarea-verification-email-request-form', 'role' => 'auth']) }}

                        <div class="centered"><strong>{{ trans('cortex/auth::common.account_verification_email') }}</strong></div>

                        <div class="form-group has-feedback{{ $errors->has('email') ? ' has-error' : '' }}">
                            {{ Form::email('email', old('email', $currentUser->email ?? ''), ['class' => 'form-control input-lg', 'placeholder' => trans('cortex/auth::common.email'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                            @if ($errors->has('email'))
                                <span class="help-block">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        {{ Form::button('<i class="fa fa-envelope"></i> '.trans('cortex/auth::common.verification_email_request'), ['class' => 'btn btn-lg btn-primary btn-block', 'type' => 'submit']) }}

                        <div>
                            {{ Html::link(route('frontarea.login'), trans('cortex/auth::common.account_login')) }}
                            {{ trans('cortex/foundation::common.or') }}
                            {{ Html::link(route('frontarea.register.member'), trans('cortex/auth::common.account_register')) }}
                        </div>

                    {{ Form::close() }}

                </section>

            </div>

        </div>

    </div>

@endsection
