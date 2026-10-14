<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    //==================================
    /**
     * Swap the current language.
     *
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function swap($locale)
    {
        if (in_array($locale, ['en', 'ar'])) {
            Session::put('locale', $locale);

            if (auth()->check()) {
                try {
                    auth()->user()->update(['locale' => $locale]);
                } catch (\Throwable $e) {
                    // Fail silently if table doesn't support or unauthenticated
                }
            }
        }

        return redirect()->back();
    }
}
