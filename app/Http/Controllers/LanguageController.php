<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application locale and redirect back.
     *
     * @param  string  $locale  'fr' or 'en'
     */
    public function switch(Request $request, string $locale)
    {
        // Only accept supported locales
        abort_if(!in_array($locale, ['fr', 'en']), 404, 'Locale not supported.');

        Session::put('locale', $locale);

        return redirect()->back()->withHeaders([
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
