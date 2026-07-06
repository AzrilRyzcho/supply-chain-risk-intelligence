<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WatchlistController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $watchlists = DB::table('watchlists')
            ->join('users', 'watchlists.user_id', '=', 'users.id')
            ->join('countries', 'watchlists.country_id', '=', 'countries.id')
            ->select('watchlists.id', 'users.name as user_name', 'users.email as user_email', 'countries.name as country_name', 'countries.code as country_code', 'watchlists.created_at')
            ->when($search, function ($query, $search) {
                return $query->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('countries.name', 'like', "%{$search}%");
            })
            ->orderBy('users.name', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.watchlists.index', compact('watchlists', 'search'));
    }

    public function destroy(int $id)
    {
        DB::table('watchlists')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Entri watchlist berhasil dihapus.');
    }
}
