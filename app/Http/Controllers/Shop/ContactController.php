<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'message' => 'required|string|max:2000',
        ]);

        Mail::send([], [], function ($mail) use ($data) {
            $mail->to('mouna.boumaza@gmail.com')
                 ->replyTo($data['email'], $data['name'])
                 ->subject('Nouveau message - Boutique Kutch')
                 ->html(
                     '<h2>Nouveau message depuis la boutique</h2>' .
                     '<p><strong>Nom :</strong> ' . e($data['name']) . '</p>' .
                     '<p><strong>Email :</strong> ' . e($data['email']) . '</p>' .
                     '<p><strong>Téléphone :</strong> ' . e($data['phone'] ?? 'Non renseigné') . '</p>' .
                     '<p><strong>Message :</strong><br>' . nl2br(e($data['message'])) . '</p>'
                 );
        });

        return response()->json(['success' => true]);
    }
}
