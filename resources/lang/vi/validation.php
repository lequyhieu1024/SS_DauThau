<?php

return [
    'required' => ':attribute là bắt buộc.',
    'string' => ':attribute phải là chuỗi.',
    'max' => [
        'string' => ':attribute không được dài quá :max ký tự.',
    ],
    'integer' => ':attribute phải là số nguyên.',
    'min' => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :min.',
    ],
    'boolean' => ':attribute phải là giá trị đúng hoặc sai.',
    'exists' => ':attribute không tồn tại.',
    'unique' => ':attribute đã được sử dụng.',
    'custom' => [
        'code' => [
            'unique' => 'Mã đã được sử dụng.',
        ],
        'parent_id' => [
            'same_as_current' => 'ID cha không thể trùng với ID hiện tại.',
        ],
    ],
    'attributes' => [
        'name' => 'Tên',
        'description' => 'Mô tả',
        'code' => 'Mã',
        'is_active' => 'Trạng thái',
        'parent_id' => 'ID cha',
    ],
];
