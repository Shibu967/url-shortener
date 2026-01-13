<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortUrlController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            $shortUrls = ShortUrl::with(['user', 'company'])->latest()->get();
        } elseif ($user->isAdmin()) {
            $shortUrls = ShortUrl::with(['user', 'company'])
                ->where('company_id', '!=', $user->company_id)
                ->latest()
                ->get();
        } elseif ($user->isMember()) {
            $shortUrls = ShortUrl::with(['user', 'company'])
                ->where('user_id', '!=', $user->id)
                ->latest()
                ->get();
        } else {
            $shortUrls = collect();
        }

        return view('short_urls.index', compact('shortUrls'));
    }

    public function create()
    {
        return view('short_urls.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return back()->withErrors(['error' => 'SuperAdmin cannot create short URLs']);
        }

        if (!$user->isAdmin() && !$user->isMember()) {
            return back()->withErrors(['error' => 'You do not have permission to create short URLs']);
        }

        if (!$user->company_id) {
            return back()->withErrors(['error' => 'You must belong to a company to create short URLs']);
        }

        $request->validate([
            'original_url' => 'required|url',
        ]);

        ShortUrl::create([
            'original_url' => $request->original_url,
            'user_id' => $user->id,
            'company_id' => $user->company_id,
        ]);

        return redirect()->route('short-urls.index')->with('success', 'Short URL created');
    }

    public function redirect($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->first();

        if (!$shortUrl) {
            abort(404);
        }

        return redirect($shortUrl->original_url);
    }
}
