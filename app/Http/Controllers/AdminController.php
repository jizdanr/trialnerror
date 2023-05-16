<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokter;
use App\Models\User;
use App\Models\Konsultasi;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Exception;
use Storage;
use Session;
session_start();

class AdminController extends Controller
{
    public function index()
    {
        echo 'Ini adalah halaman berandanya Administrator';       
    }

    // DOKTER CRUD
    public function form_tambah() {
        return view('dokter-form-tambah');
    }

    public function form_save(Request $request)
    {
        $params = $request->all();
        // validate the input data
        $validatedData = $request->validate([
            'nama_dokter' => 'required',
            'email' => 'required|email|unique:dokters',
            'password' => 'required',
            'foto' => 'required'
        ]);

        $image = $request->file('foto');
        $image->store('upload/dokter', ['disk' => 'public_uploads']);

        // create a new dokter instance with the validated data
        $dokter = new Dokter([
            'nama_dokter' => $validatedData['nama_dokter'],
            'spesialis' => $params['spesialis'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'foto' => $image->hashName(),
            'alamat' => $params['alamat'],
            'no_telp' => $params['no_telp'],
            'gender' => $params['gender'],
            'tanggal_lahir' => $params['tanggal_lahir'],
            'role' => 'dokter',
            'jam_buka' => $params['jam_buka'],
            'jam_tutup' => $params['jam_tutup']
        ]);

        // save the dokter instance to the database
        $dokter->save();

        // insert the credentials into user table
        $user = new User([
            'email' => $validatedData['email'],
            'phone' => $params['no_telp'],
            'password' => Hash::make($validatedData['password']),
            'name' => $validatedData['nama_dokter'],
            'roles' => 2
        ]);

        $user->save();

        // return a response indicating success
        // return response()->json(['message' => 'User created successfully'], 201);
        return redirect()->to('dokter')->with('success', 'Data berhasil ditambahkan');
    }

    public function form_edit($id) {
        $id = Crypt::decryptString($id);

        $dokter = Dokter::findOrFail($id);
        if($dokter){
            return view('dokter-form-edit', compact('dokter'));
        }
    }

    public function form_update(Request $request, User $user, $id) {
        $id = Crypt::decryptString($id);

        $dokter = Dokter::findOrFail($id);
        $params = $request->all();
        if($dokter) {
            // validate the input data
            $validatedData = $request->validate([
                'nama_dokter' => 'required',
                'email' => 'required|email|unique:dokters'.',id,'.$user->id,
            ]);

            if($request->hasFile('foto')) 
            {
                $image = $request->file('foto');
                // $image->store('upload/dokter', ['disk' => 'public']);
                $image->store('upload/dokter', ['disk' => 'public_uploads']);

                //delete old image
                if($dokter->foto != null)
                {
                    // $path = storage_path('app/public/upload/dokter/'.$dokter->foto);
                    $path = public_path('upload/dokter/'.$dokter->foto);
                    if(file_exists($path)) 
                    {
                        unlink($path);

                        // Storage::delete('public/upload/dokter/'.$dokter->foto);
                    } else {
                        return response()->json(['message' => 'File not found.'], 404);
                    }
                }

                $res = Dokter::where('id', $id)->update([
                    'nama_dokter' => $validatedData['nama_dokter'],
                    'spesialis' => $params['spesialis'],
                    'email' => $validatedData['email'],
                    'foto' => $image->hashName(),
                    'alamat' => $params['alamat'],
                    'no_telp' => $params['no_telp'],
                    'gender' => $params['gender'],
                    'tanggal_lahir' => $params['tanggal_lahir'],
                    'role' => 'dokter',
                    'jam_buka' => $params['jam_buka'],
                    'jam_tutup' => $params['jam_tutup']
                ]);
            } else {
                $res = Dokter::where('id', $id)->update([
                    'nama_dokter' => $validatedData['nama_dokter'],
                    'spesialis' => $params['spesialis'],
                    'email' => $validatedData['email'],
                    'alamat' => $params['alamat'],
                    'no_telp' => $params['no_telp'],
                    'gender' => $params['gender'],
                    'tanggal_lahir' => $params['tanggal_lahir'],
                    'role' => 'dokter',
                    'jam_buka' => $params['jam_buka'],
                    'jam_tutup' => $params['jam_tutup']
                ]);
            }

            return redirect()->to('dokter')->with('success', 'Data berhasil diubah');
        }
    }

    public function form_delete($id) {
        $id = Crypt::decryptString($id);

        $dokter = Dokter::findOrFail($id);
        $email = $dokter['email'];

        if($dokter) {
            //delete old image
            try {
                // Delete akun dokter
                User::where('email', $email)->delete();

                if($dokter->foto != null)
                {
                    // $path = storage_path('app/public/upload/dokter/'.$dokter->foto);
                    $path = public_path('upload/dokter/'.$dokter->foto);
                    if(file_exists($path)) 
                    {
                        unlink($path);

                        // Storage::delete('public/upload/dokter/'.$dokter->foto);
                    } else {
                        return response()->json(['message' => 'File not found.'], 404);
                    }
                }
                

                // Delete data dokter
                Dokter::where('id', $id)->delete();

                return redirect()->to('dokter')->with('success', 'Data berhasil dihapus');
            } catch(\Illuminate\Database\QueryException $e) { 
                return redirect()->to('dokter')->with('warning', 'Gagal menghapus dokter karena data dokter sedang digunakan!');
            }
        }
    }
    // END OF DOKTER CRUD
}
