<?php

namespace App\Http\Controllers;

use App\Mail\KontakMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class KontakController extends Controller
{
    /**
     * Terima pesan dari formulir kontak publik dan teruskan ke sekretariat.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150'],
            'subjek' => ['nullable', 'string', 'max:150'],
            'pesan' => ['required', 'string', 'max:2000'],
        ]);

        Mail::to(config('mail.from.address'))->queue(new KontakMessage(
            nama: $data['nama'],
            email: $data['email'],
            subjek: $data['subjek'] ?? null,
            pesan: $data['pesan'],
        ));

        return redirect()->back()->with('status', __('Pesan berhasil dikirim. Kami akan menghubungi Anda segera.'));
    }
}
