<?php

namespace App\Http\Controllers;

use App\Data\Gallery\StoreGalleryData;
use App\Enums\GalleryCategoryEnum;
use App\Models\Blog;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('site.index');
    }
}
