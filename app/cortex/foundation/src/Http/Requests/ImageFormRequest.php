<?php

declare(strict_types=1);

namespace Cortex\Foundation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $mediaSize = config('cortex.foundation.media.size');
        $mediaMimetypes = config('cortex.foundation.media.mimetypes');

        return [
            'file' => 'required|mimetypes:'.$mediaMimetypes.'|max:'.$mediaSize,
        ];
    }
}
