<?php

namespace App\Http\Controllers\Ami;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AppSettingController extends Controller
{
    public function index(): View
    {
        $settings = AppSetting::query()->whereIn('key', [
            'app_name',
            'app_version',
            'app_tagline',
            'app_organization',
            'app_support_email',
            'app_website',
            'app_copyright',
            'app_logo',
        ])->get()->keyBy('key');

        return view('settings.index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'app_name' => ['required', 'string', 'max:120'],
            'app_version' => ['nullable', 'string', 'max:30'],
            'app_tagline' => ['nullable', 'string', 'max:255'],
            'app_organization' => ['nullable', 'string', 'max:255'],
            'app_support_email' => ['nullable', 'email', 'max:255'],
            'app_website' => ['nullable', 'url', 'max:255'],
            'app_copyright' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        AppSetting::updateOrCreate(['key' => 'app_name'], ['value' => $request->string('app_name')->toString()]);
        AppSetting::updateOrCreate(['key' => 'app_version'], ['value' => $request->string('app_version')->toString()]);
        AppSetting::updateOrCreate(['key' => 'app_tagline'], ['value' => $request->string('app_tagline')->toString()]);
        AppSetting::updateOrCreate(['key' => 'app_organization'], ['value' => $request->string('app_organization')->toString()]);
        AppSetting::updateOrCreate(['key' => 'app_support_email'], ['value' => $request->string('app_support_email')->toString()]);
        AppSetting::updateOrCreate(['key' => 'app_website'], ['value' => $request->string('app_website')->toString()]);
        AppSetting::updateOrCreate(['key' => 'app_copyright'], ['value' => $request->string('app_copyright')->toString()]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('app', 'public');
            AppSetting::updateOrCreate(['key' => 'app_logo'], ['value' => $path]);
        }

        return back()->with('status', 'Pengaturan aplikasi diperbarui.');
    }

    public static function logoUrl(): ?string
    {
        $setting = AppSetting::query()->where('key', 'app_logo')->first();

        if (! $setting?->value) {
            return null;
        }

        $url = Storage::disk('public')->url($setting->value);

        return $setting->updated_at instanceof CarbonInterface
            ? $url.'?v='.$setting->updated_at->timestamp
            : $url;
    }
}
