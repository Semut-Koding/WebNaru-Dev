<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Setting;
use App\Models\Villa;
use App\Models\VisitorCounter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $featured_villas = Villa::with('media')->take(3)->get();
        $featured_attractions = Attraction::with('media')->where('status', 'active')->orderBy("sort_order")->take(3)->get();

        // Stats data — dynamic from DB with admin override via Settings
        try {
            $statsOverrides = [
                'stats_wahana' => Setting::where('key', 'stats_wahana')->value('value'),
                'stats_villa' => Setting::where('key', 'stats_villa')->value('value'),
                'stats_pengunjung' => Setting::where('key', 'stats_pengunjung')->value('value'),
                'stats_rating' => Setting::where('key', 'stats_rating')->value('value'),
            ];

            // Visitor count = adult_count + teenager_count + child_count
            $monthlyVisitors = VisitorCounter::whereYear('date', Carbon::now()->year)
                ->whereMonth('date', Carbon::now()->month)
                ->selectRaw('COALESCE(SUM(adult_count + teenager_count + child_count), 0) as total')
                ->value('total');

            $stats = [
                [
                    'label' => 'Wahana Aktif',
                    'value' => (int) ($statsOverrides['stats_wahana'] ?: Attraction::where('status', 'active')->count()),
                    'suffix' => '+',
                    'icon' => '🎢',
                ],
                [
                    'label' => 'Villa Tersedia',
                    'value' => (int) ($statsOverrides['stats_villa'] ?: Villa::where('status', 'available')->count()),
                    'suffix' => '+',
                    'icon' => '🏡',
                ],
                [
                    'label' => 'Pengunjung Bulan Ini',
                    'value' => (int) ($statsOverrides['stats_pengunjung'] ?: $monthlyVisitors),
                    'suffix' => '+',
                    'icon' => '👥',
                ],
                [
                    'label' => 'Rating',
                    'value' => (float) ($statsOverrides['stats_rating'] ?: 4.8),
                    'suffix' => '/5',
                    'icon' => '⭐',
                ],
            ];
        } catch (\Exception $e) {
            $stats = [
                ['label' => 'Wahana Aktif', 'value' => 5, 'suffix' => '+', 'icon' => '🎢'],
                ['label' => 'Villa Tersedia', 'value' => 3, 'suffix' => '+', 'icon' => '🏡'],
                ['label' => 'Pengunjung Bulan Ini', 'value' => 120, 'suffix' => '+', 'icon' => '👥'],
                ['label' => 'Rating', 'value' => 4.8, 'suffix' => '/5', 'icon' => '⭐'],
            ];
        }

        return inertia('Public/Home', compact('featured_villas', 'featured_attractions', 'stats'));
    }

    public function about()
    {
        return inertia('Public/About');
    }

    public function attractions()
    {
        $attractions = Attraction::with('media')->orderBy("sort_order")->get();
        return inertia('Public/Attractions', compact('attractions'));
    }

    public function attractionDetail($id)
    {
        $attraction = Attraction::with('media')->findOrFail($id);
        return inertia('Public/AttractionDetail', compact('attraction'));
    }

    public function villas()
    {
        $villas = Villa::with(['media', 'units'])->orderBy('sort_order')->get();
        return inertia('Public/Villas', compact('villas'));
    }

    public function villaDetail($id)
    {
        $villa = Villa::with(['media', 'units'])->findOrFail($id);
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
