<?php

namespace App\Http\Requests;

use App\Models\Notice;
use Illuminate\Foundation\Http\FormRequest;

class StoreNoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isTeacher();
    }

    public function rules(): array
    {
        return [
            'title'      => ['required', 'string', 'max:200'],
            'body'       => ['required', 'string', 'max:5000'],
            'audience'   => ['required', 'in:'.implode(',', Notice::AUDIENCES)],
            'publish_at' => ['nullable', 'date'],
            'is_active'  => ['boolean'],
        ];
    }
}
