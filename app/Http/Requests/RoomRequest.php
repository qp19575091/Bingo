<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'room_id' => 'string|required',
            'nickname' => 'string|required',
            'win_line' => 'integer|required|min:1|max:10',
            'size' => 'integer|required|min:3|max:10',
        ];
    }
}
