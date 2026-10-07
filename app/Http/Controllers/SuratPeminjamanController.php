<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratPeminjamanController extends Controller
{
    /**
     * Serve surat peminjaman resmi — hanya pemilik peminjaman + pengelola/admin.
     * Hard Rule 4: file di disk local (private), tidak ada URL publik.
     */
    public function __invoke(Peminjaman $peminjaman): StreamedResponse
    {
        $user = auth()->user();

        $isOwner = $user->id === $peminjaman->user_id;
        $isPetugas = $user->hasAnyRole(['pengelola', 'admin']);

        abort_if(! $isOwner && ! $isPetugas, 403);

        $media = $peminjaman->getFirstMedia('surat_peminjaman');

        abort_if($media === null, 404);

        return Storage::disk($media->disk)->download($media->getPathRelativeToRoot(), $media->file_name);
    }
}
