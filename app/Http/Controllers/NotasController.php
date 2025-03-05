<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotasController extends ApiController
{
    public function upload_image(Request $req)
    {
        if ($req->hasFile('image')) {
            $filename = $req->file('image')->getClientOriginalName(); // get the file name
            $getfilenamewitoutext = pathinfo($filename, PATHINFO_FILENAME); // get the file name without extension
            $getfileExtension = $req->file('image')->getClientOriginalExtension(); // get the file extension
            $createnewFileName = time() . '_' . str_replace(' ', '_', $getfilenamewitoutext) . '.' . $getfileExtension; // create new random file name
            $img_path = $req->file('image')->storeAs("public", $createnewFileName); // get the image path
            return response()->json([
                'url' => ENV('APP_URL') . Storage::url($img_path)
            ]);
        } else {
            return $this->errorResponse("Error", 409);
        }

    }
}
