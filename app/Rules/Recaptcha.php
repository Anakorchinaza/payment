<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $g_response = Http::withOptions(['verify' => false])->asForm()->post("https://www.google.com/recaptcha/api/siteverify", [
            'secret' => config(key: 'services.recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => \request()->ip()
        ]);
        // dd( $g_response->json());
        
        if (!$g_response->json(key: 'success')) {
            $fail("The {$attribute} is invalid.");
        }
    }
}
