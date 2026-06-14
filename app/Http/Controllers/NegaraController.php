<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Favorite;

class NegaraController extends Controller
{
    private function apiHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . config('services.restcountries.key'),
        ];
    }

    public function index()
    {
        $favorites = Favorite::where('user_id', auth()->id())->get();
        return view('negara.index', compact('favorites'));
    }

    // Cari negara via RestCountries API v5
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
        ], [
            'query.required' => 'Masukkan nama negara.',
            'query.min'      => 'Minimal 2 karakter.',
        ]);

        $query = trim($request->query('query'));

        try {
            $response = Http::timeout(15)
                ->withHeaders($this->apiHeaders())
                ->get('https://api.restcountries.com/countries/v5/name', [
                    'q'     => $query,
                    'limit' => 10,
                ]);

            if (!$response->successful()) {
                return response()->json(['error' => 'Gagal mengambil data. Status: ' . $response->status()], 500);
            }

            $body = $response->json();
            $objects = collect($body['data']['objects'] ?? []);

            if ($objects->isEmpty()) {
                return response()->json(['error' => 'Negara tidak ditemukan. Coba nama lain.'], 404);
            }

            $countries = $objects->map(function ($c) {
                // Currencies
                $currencies = '';
                if (!empty($c['currencies']) && is_array($c['currencies'])) {
                    $currencies = collect($c['currencies'])
                        ->map(function ($cur, $code) {
                            $name = is_array($cur) ? ($cur['name'] ?? $code) : $code;
                            $symbol = is_array($cur) ? ($cur['symbol'] ?? '-') : '-';
                            return $name . ' (' . $symbol . ')';
                        })
                        ->implode(', ');
                }

                // Languages
                $languages = '-';
                if (!empty($c['languages']) && is_array($c['languages'])) {
                    $languages = collect($c['languages'])
                        ->map(fn($lang) => is_array($lang) ? ($lang['name'] ?? '') : $lang)
                        ->filter()
                        ->implode(', ');
                }

                // Capital
                $capital = '-';
                if (!empty($c['capitals']) && is_array($c['capitals'])) {
                    $capital = collect($c['capitals'])
                        ->map(fn($cap) => is_array($cap) ? ($cap['name'] ?? '') : $cap)
                        ->filter()
                        ->implode(', ');
                    if (empty($capital)) $capital = '-';
                }

                $pop = $c['population'] ?? 0;

                return [
                    'code'        => $c['codes']['alpha_3'] ?? ($c['codes']['alpha_2'] ?? ''),
                    'name'        => $c['names']['common'] ?? '-',
                    'official'    => $c['names']['official'] ?? ($c['names']['common'] ?? '-'),
                    'flag_png'    => $c['flag']['url_png'] ?? '',
                    'flag_svg'    => $c['flag']['url_svg'] ?? '',
                    'coat'        => null, // tidak tersedia di v5
                    'capital'     => $capital,
                    'population'  => $pop,
                    'pop_format'  => number_format($pop, 0, ',', '.'),
                    'region'      => $c['region'] ?? '-',
                    'subregion'   => $c['subregion'] ?? '-',
                    'currencies'  => $currencies ?: '-',
                    'languages'   => $languages,
                    'area'        => number_format($c['area']['kilometers'] ?? 0, 0, ',', '.') . ' km²',
                    'timezones'   => !empty($c['timezones']) ? implode(', ', $c['timezones']) : '-',
                    'map_google'  => $c['links']['google_maps'] ?? null,
                    'map_osm'     => $c['links']['open_street_maps'] ?? null,
                ];
            });

            return response()->json(['countries' => $countries]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Koneksi gagal: ' . $e->getMessage()], 500);
        }
    }

    // CREATE - simpan favorit
    public function storeFavorite(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|max:3',
            'country_name' => 'required|string|max:100',
            'flag_url'     => 'required|url',
            'capital'      => 'nullable|string|max:100',
            'population'   => 'nullable|integer',
            'currency'     => 'nullable|string|max:200',
        ]);

        $exists = Favorite::where('user_id', auth()->id())
            ->where('country_code', $request->country_code)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Negara sudah ada di favorit!'], 409);
        }

        $favorite = Favorite::create([
            'user_id'      => auth()->id(),
            'country_code' => $request->country_code,
            'country_name' => $request->country_name,
            'flag_url'     => $request->flag_url,
            'capital'      => $request->capital,
            'population'   => $request->population,
            'currency'     => $request->currency,
        ]);

        return response()->json(['message' => 'Ditambahkan ke favorit!', 'id' => $favorite->id]);
    }

    // READ - halaman kelola favorit
    public function favorites()
    {
        $favorites = Favorite::where('user_id', auth()->id())
            ->orderBy('country_name')
            ->get();

        return view('negara.favorites', compact('favorites'));
    }

    // UPDATE - form edit
    public function editFavorite($id)
    {
        $favorite = Favorite::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('negara.edit', compact('favorite'));
    }

    // UPDATE - proses
    public function updateFavorite(Request $request, $id)
    {
        $favorite = Favorite::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'capital'    => 'nullable|string|max:100',
            'population' => 'nullable|integer|min:0',
            'currency'   => 'nullable|string|max:200',
        ]);

        $favorite->update([
            'capital'    => $request->capital,
            'population' => $request->population,
            'currency'   => $request->currency,
        ]);

        return redirect()->route('negara.favorites')->with('success', 'Data favorit diperbarui!');
    }

    // DELETE
    public function destroyFavorite($id)
    {
        Favorite::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail()
            ->delete();

        return response()->json(['message' => 'Dihapus dari favorit.']);
    }
}