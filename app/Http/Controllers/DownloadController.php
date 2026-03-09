<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\DownloadCategory;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function index()
    {
        $categories = DownloadCategory::with('downloads')->orderBy('order')->get();
        return view('theme::downloads.index', ['categories' => $categories]);
    }

    public function file(Download $download)
    {
        $path = $download->file_path;
        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }
        $download->increment('download_count');
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $name = $download->name . ($download->version ? '-' . $download->version : '') . ($ext ? '.' . $ext : '');
        return Storage::disk('public')->download($path, $name);
    }
}
