<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderSetting;
use App\Models\Setting;
use App\Models\Fee;
use App\Jobs\SyncProductsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'general');

        $topupProviders = ProviderSetting::topup()->get();
        $paymentProviders = ProviderSetting::payment()->get();

        // Get general settings
        $generalSettings = [
            'site_title' => Setting::get('site_title', 'Marspedia Store'),
            'site_description' => Setting::get('site_description', ''),
            'site_keywords' => Setting::get('site_keywords', ''),
            'footer_text' => Setting::get('footer_text', ''),
            'whatsapp' => Setting::get('whatsapp', ''),
            'logo' => Setting::get('logo', ''),
        ];

        // Get social media settings
        $socialSettings = [
            'whatsapp_url' => Setting::get('whatsapp_url', ''),
            'facebook' => Setting::get('facebook', ''),
            'instagram' => Setting::get('instagram', ''),
            'tiktok' => Setting::get('tiktok', ''),
            'telegram' => Setting::get('telegram', ''),
            'youtube' => Setting::get('youtube', ''),
            'email' => Setting::get('contact_email', ''),
        ];

        // Get profit settings
        $profitSettings = [
            'visitor' => Setting::get('profit_visitor', 10),
            'reseller' => Setting::get('profit_reseller', 8),
            'reseller_vip' => Setting::get('profit_reseller_vip', 6),
            'reseller_vvip' => Setting::get('profit_reseller_vvip', 4),
        ];

        // Get fees
        $fees = Fee::all();

        return view('admin.settings.index', compact(
            'topupProviders',
            'paymentProviders',
            'generalSettings',
            'socialSettings',
            'profitSettings',
            'fees',
            'tab'
        ));
    }

    public function update(Request $request)
    {
        $section = $request->input('section');

        if ($section === 'general') {
            $validated = $request->validate([
                'site_title' => 'required|string|max:255',
                'site_description' => 'nullable|string',
                'site_keywords' => 'nullable|string',
                'footer_text' => 'nullable|string',
                'whatsapp' => 'nullable|string|max:20',
                'logo' => 'nullable|image|max:2048',
            ]);

            foreach (['site_title', 'site_description', 'site_keywords', 'footer_text', 'whatsapp'] as $key) {
                if (isset($validated[$key])) {
                    Setting::set($key, $validated[$key], 'text', 'general');
                }
            }

            if ($request->hasFile('logo')) {
                $oldLogo = Setting::get('logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                $path = $request->file('logo')->store('settings', 'public');
                Setting::set('logo', $path, 'image', 'general');
            }
        } elseif ($section === 'social') {
            $validated = $request->validate([
                'whatsapp_url' => 'nullable|string',
                'facebook' => 'nullable|string',
                'instagram' => 'nullable|string',
                'tiktok' => 'nullable|string',
                'telegram' => 'nullable|string',
                'youtube' => 'nullable|string',
                'contact_email' => 'nullable|email',
            ]);

            foreach ($validated as $key => $value) {
                Setting::set($key, $value, 'text', 'social');
            }
        } elseif ($section === 'profit') {
            $validated = $request->validate([
                'profit_visitor' => 'required|numeric|min:0|max:100',
                'profit_reseller' => 'required|numeric|min:0|max:100',
                'profit_reseller_vip' => 'required|numeric|min:0|max:100',
                'profit_reseller_vvip' => 'required|numeric|min:0|max:100',
            ]);

            foreach ($validated as $key => $value) {
                Setting::set($key, $value, 'number', 'profit');
            }
        } elseif ($section === 'fees') {
            $validated = $request->validate([
                'fees' => 'array',
                'fees.*.name' => 'required|string|max:255',
                'fees.*.amount' => 'required|numeric|min:0',
            ]);

            // Delete all existing fees and recreate
            Fee::query()->forceDelete();
            foreach ($validated['fees'] ?? [] as $fee) {
                Fee::create([
                    'name' => $fee['name'],
                    'amount' => $fee['amount'],
                    'type' => 'fixed',
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('admin.settings.index', ['tab' => $section])
            ->with('success', 'Pengaturan berhasil disimpan');
    }

    public function updateProvider(Request $request, $id)
    {
        $provider = ProviderSetting::findOrFail($id);

        $validated = $request->validate([
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'credentials' => 'array',
            'config' => 'nullable|array',
        ]);

        DB::transaction(function () use ($provider, $validated) {
            // If setting as default, unset other defaults
            if (isset($validated['is_default']) && $validated['is_default']) {
                ProviderSetting::where('provider_type', $provider->provider_type)
                    ->where('id', '!=', $provider->id)
                    ->update(['is_default' => false]);
            }

            $provider->update($validated);
        });

        return redirect()->route('admin.settings.index')
            ->with('success', 'Provider settings updated successfully');
    }

    public function syncProducts(Request $request)
    {
        $provider = $request->input('provider', 'apigames');

        SyncProductsJob::dispatch($provider);

        return response()->json([
            'success' => true,
            'message' => "Product sync from {$provider} has been queued"
        ]);
    }

    public function testConnection(Request $request, $id)
    {
        $provider = ProviderSetting::findOrFail($id);

        try {
            if ($provider->provider_name === 'apigames') {
                $service = app(\App\Services\ApiGamesService::class);
                $result = $service->checkConnection();

                if (isset($result['status']) && $result['status'] === 'success') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Connection successful!',
                        'data' => $result
                    ]);
                }
            } elseif ($provider->provider_name === 'digiflazz') {
                // Test DigiFlazz connection
                return response()->json([
                    'success' => true,
                    'message' => 'DigiFlazz connection test not implemented yet'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
