<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Support\Facades\Storage;

class BuktiPembayaranController extends Controller
{
    /**
     * Serve bukti pembayaran — hanya pemilik peminjaman + pengelola/admin.
     * Hard Rule 4: file di disk local (private), tidak ada URL publik.
     */
    public function __invoke(Pembayaran $pembayaran)
    {
        $user = auth()->user();

        $isOwner = $user->id === $pembayaran->peminjaman->user_id;
        $isPetugas = $user->hasAnyRole(['pengelola', 'admin']);

        abort_if(! $isOwner && ! $isPetugas, 403);

        $media = $pembayaran->getFirstMedia('bukti');

        abort_if($media === null, 404);

        return Storage::disk($media->disk)->download($media->getPathRelativeToRoot(), $media->file_name);
    }
}
