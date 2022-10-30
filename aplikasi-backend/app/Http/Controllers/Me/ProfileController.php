<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use illuminate\Http\Request;
use App\Http\Requests\Me\Profile\UpdateRequest;
use App\Models\User;
use ImageKit\ImageKit;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'User Data Fetched Successfully.',
            ],
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'picture' => $user->picture,
            ]
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $validated = $request->validated();
        $user = User::find(auth()->id());

        // get semua request
        // cek apakah ada request picture
        // jika iya proses, cara prosesnya buat objek instance imagekit ubah dulu picture ke base 64
        // ubah dulu gambar ke base64
        // upload, masukkan file, file name dan folder
        // dapatkan url nya
        // masukkan url nya nanti ke tabel
        // jika tidak ada request picture maka lanjut proses update
        if ($request->hasFile('picture'))
        {
            $imageKit = new ImageKit(
                env('IMAGEKIT_PUBLIC_KEY'),
                env('IMAGEKIT_PRIVATE_KEY'),
                env('IMAGEKIT_URL_ENDPOINT'),

            );

            $image = base64_encode(file_get_contents($request->file('picture')));

            $uploadImage = $imageKit->uploadFile([
                'file' => $image,
                'fileName' => $user->email,
                'folder' => '/user/profile',
            ]);

            $validated['picture'] = $uploadImage->result->url;
        }

        // masukkan semua request yang sudah di validasi

        $update = $user->update($validated);

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'User Data Updated Successfully.',
            ],
            'data' => [
                'email' => $user->email,
                'name' => $user->name,
                'picture' => $user->picture,
            ]
        ]);
    }
}
