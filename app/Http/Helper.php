<?php

use App\Http\Resources\AttachmentResource;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManagerStatic as Image;

if (!function_exists('upload_image')) {
    function upload_image($file)
    {
        // Kiểm tra xem biến $file có phải là một đối tượng tệp hợp lệ không
        if (!is_a($file, 'Illuminate\Http\UploadedFile')) {
            throw new \InvalidArgumentException('Invalid file type.');
        }

        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = public_path('uploads/images/' . $filename);

        // Kiểm tra và tạo thư mục nếu chưa tồn tại
        if (!file_exists(public_path('uploads/images/'))) {
            mkdir(public_path('uploads/images/'), 0755, true);
        }
        // Resize và lưu ảnh
        Image::make($file->getRealPath())->resize(300, 200)->save($path);

        // Trả về đường dẫn của hình ảnh
        return 'uploads/images/' . $filename;
    }
}

if (!function_exists('upload_file')) {
    function upload_file($file)
    {
        $newFilename = now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $relativePath = "uploads/documents/{$newFilename}";

        $file->move(public_path('uploads/documents'), $newFilename);

        if (!file_exists(public_path('uploads/documents/' . $newFilename))) {
            throw new \Exception('Không tìm thấy tập tin sau khi di chuyển.');
        }
        return $relativePath;
    }
}

if (!function_exists('convertText')) {
    function convertText($string)
    {
        return ucwords(str_replace('_', ' ', $string));
    }
}

