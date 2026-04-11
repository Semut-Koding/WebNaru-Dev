<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $featured_villas = \App\Models\Villa::with('media')->take(3)->get();
        $featured_attractions = \App\Models\Attraction::with('media')->where('status', 'active')->orderBy("sort_order")->take(3)->get();
        return inertia('Public/Home', compact('featured_villas', 'featured_attractions'));
    }

    public function about()
    {
        return inertia('Public/About');
    }

    public function attractions()
    {
        $attractions = \App\Models\Attraction::with('media')->orderBy("sort_order")->get();
        return inertia('Public/Attractions', compact('attractions'));
    }

    public function attractionDetail($id)
    {
        $attraction = \App\Models\Attraction::with('media')->findOrFail($id);
        return inertia('Public/AttractionDetail', compact('attraction'));
    }

    public function villas()
    {
        $villas = \App\Models\Villa::with(['media', 'units'])->orderBy('sort_order')->get();
        return inertia('Public/Villas', compact('villas'));
    }

    public function villaDetail($id)
    {
        $villa = \App\Models\Villa::with(['media', 'units'])->findOrFail($id);
        return inertia('Public/VillaDetail', compact('villa'));
    }

    public function pricing()
    {
        return inertia('Public/Pricing');
    }

    public function faq()
    {
        return inertia('Public/Faq');
    }

    public function contact()
    {
        return inertia('Public/Contact');
    }

    public function gallery()
    {
        $galleries = \App\Models\Gallery::where('is_active', true)->orderBy('sort_order')->get();
        return inertia('Public/Gallery', compact('galleries'));
    }
}
