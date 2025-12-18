<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BirthdayController extends Controller
{
    public function show($token)
    {
        $contact = Contact::where('public_token', $token)->firstOrFail();
        return view('birthday.show', compact('contact'));
    }

    public function card($token)
    {
        $contact = Contact::where('public_token', $token)->firstOrFail();
        $image = $this->getBirthdayCardImage($contact);
        return response($image->toJpeg())->header('Content-Type', 'image/jpeg');
    }
    
    public function download($token)
    {
         $contact = Contact::where('public_token', $token)->firstOrFail();
         $image = $this->getBirthdayCardImage($contact);

         return response()->streamDownload(function() use ($image) {
             echo $image->toJpeg();
         }, 'birthday-card.jpg', ['Content-Type' => 'image/jpeg']);
    }

    private function getBirthdayCardImage($contact)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read(public_path('images/SITC Birthday Card.jpg'));
        
        $name = strtoupper($contact->short_name);
        $length = strlen($name);
        $fontSize = 60;
        
        // Smart wrapping and sizing for long names
        if ($length > 20) {
            $name = wordwrap($name, 15, "\n"); // Wrap every ~15 chars
            $fontSize = 40;
        } elseif ($length > 12) {
            $fontSize = 50;
        }

        $image->text($name, $image->width() / 2, $image->height() / 2 - 40, function ($font) use ($fontSize) {
            $font->filename(public_path('fonts/EmilysCandy-Regular.ttf'));
            $font->color('#D32F2F'); // Nice Red
            $font->size($fontSize);
            $font->align('center');
            $font->valign('middle');
        });

        return $image;
    }
}
