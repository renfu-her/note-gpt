<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'folder_id' => ['required', 'integer', 'exists:folders,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'folder_id.required' => '請選擇資料夾',
            'folder_id.integer' => '資料夾ID必須為整數',
            'folder_id.exists' => '所選資料夾不存在',
            'title.required' => '請輸入標題',
            'title.string' => '標題必須為文字',
            'title.max' => '標題不能超過255個字元',
            'content.required' => '請輸入內容',
            'content.string' => '內容必須為文字',
        ];
    }
} 