<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TinyMCEImageUploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Upload an image for TinyMCE (email signatures, descriptions, etc.).
     * Stores in storage/app/public/tinymce-images and returns the public URL.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048', // 2MB max
        ], [
            'file.required' => 'No image selected.',
            'file.image'    => 'The file must be an image.',
            'file.mimes'    => 'Allowed formats: JPEG, PNG, GIF, WebP.',
            'file.max'      => 'Image must be under 2MB.',
        ]);

        $file = $request->file('file');
        $name = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'tinymce-images/' . $name;

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $url = Storage::disk('public')->url($path);

        // Return absolute URL so it works in emails and saved content
        $location = url($url);

        return response()->json(['location' => $location]);
    }
}
