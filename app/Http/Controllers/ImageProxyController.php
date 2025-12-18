<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageProxyController extends Controller
{
    /**
     * Proxy image from S3 or local storage
     *
     * - Nhận path đã được base64 encode, decode và validate cẩn thận.
     * - Ưu tiên lấy ảnh từ local 'public', sau đó mới tới S3.
     * - Trả về 404 nếu path không hợp lệ hoặc file không tồn tại.
     */
    public function proxy(Request $request, string $path)
    {
        try {
            // Decode the path in strict mode and validate
            $decodedPath = base64_decode($path, true);

            // Must be a non-empty string
            if (!is_string($decodedPath) || $decodedPath === '') {
                abort(404);
            }

            // Must start with the expected prefix
            if (!str_starts_with($decodedPath, 'images/')) {
                abort(404);
            }

            // Prevent path traversal and disallow dangerous characters
            if (
                str_contains($decodedPath, '..') ||
                str_starts_with($decodedPath, '/') ||
                str_starts_with($decodedPath, '\\') ||
                preg_match('/[\x00-\x1F]/', $decodedPath)
            ) {
                abort(404);
            }

            // Enforce an allow-list of characters in the path
            if (!preg_match('#^images/[A-Za-z0-9_\-./]+$#', $decodedPath)) {
                abort(404);
            }

            // Try local storage first
            if (Storage::disk('public')->exists($decodedPath)) {
                $file = Storage::disk('public')->get($decodedPath);
                $mimeType = Storage::disk('public')->mimeType($decodedPath);
                
                return response($file, 200)
                    ->header('Content-Type', $mimeType)
                    ->header('Cache-Control', 'public, max-age=31536000');
            }

            // Try S3 (configuration read via config, not env())
            $s3Configured = !empty(config('filesystems.disks.s3.key')) &&
                           !empty(config('filesystems.disks.s3.secret')) &&
                           !empty(config('filesystems.disks.s3.bucket'));
            
            if ($s3Configured && Storage::disk('s3')->exists($decodedPath)) {
                $file = Storage::disk('s3')->get($decodedPath);
                $mimeType = Storage::disk('s3')->mimeType($decodedPath);
                
                return response($file, 200)
                    ->header('Content-Type', $mimeType)
                    ->header('Cache-Control', 'public, max-age=31536000');
            }

            abort(404);
        } catch (\Exception $e) {
            \Log::error('Image proxy error: ' . $e->getMessage());
            abort(404);
        }
    }
}

