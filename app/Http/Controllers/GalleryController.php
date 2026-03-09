<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use App\Models\GalleryImage;

class GalleryController extends Controller
{
    public function index()
    {
        $albums = GalleryAlbum::withCount('images')->orderBy('order')->get();
        return view('theme::gallery.index', ['albums' => $albums]);
    }

    public function showAlbum(GalleryAlbum $album)
    {
        $images = $album->images()->orderBy('order')->get();
        return view('theme::gallery.album', ['album' => $album, 'images' => $images]);
    }
}
