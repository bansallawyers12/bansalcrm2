<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicCheckInController extends Controller
{
    public function index(): View
    {
        $educationUrl = config('services.education_check_in.url');

        return view('public.check_in', [
            'immigrationCheckInUrl' => (string) config('services.immigration_check_in.url'),
            'educationCheckInUrl' => $educationUrl
                ? (string) $educationUrl
                : route('public.office-check-in.index'),
        ]);
    }
}
