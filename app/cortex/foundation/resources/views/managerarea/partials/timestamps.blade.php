<div style="padding-top: 5px;">
    @if($model->exists)
        @if($model->created_at)
            <small>
                <strong>{{ trans('cortex/foundation::common.created_at') }}:</strong>
                <time datetime="{{ $model->created_at }}">{{ $model->created_at->format(config('app.date_format')) }}</time>
                @if($model->creator) <a href="{{ route('managerarea.users.edit', ['user' => $model->creator]) }}">{{ $model->creator->username }}</a> @endif
            </small>
        @endif

        @if($model->created_at && $model->updated_at) | @endif

        @if($model->updated_at)
            <small>
                <strong>{{ trans('cortex/foundation::common.updated_at') }}:</strong>
                <time datetime="{{ $model->updated_at }}">{{ $model->updated_at->format(config('app.date_format')) }}</time>
                @if($model->updater) <a href="{{ route('managerarea.users.edit', ['user' => $model->updater]) }}">{{ $model->updater->username }}</a> @endif
            </small>
        @endif
    @endif
</div>
