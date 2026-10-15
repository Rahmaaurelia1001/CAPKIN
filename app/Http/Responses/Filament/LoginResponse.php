<?php
namespace App\Http\Responses\Filament;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = auth()->user();

        if ($user?->role === 'admin') {
            return redirect()->to('/admin/users');
        }

        return redirect()->intended(filament()->getUrl());
    }}