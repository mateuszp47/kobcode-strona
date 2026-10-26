<?php
namespace App\Http\Controllers;
use App\Models\Inquiry;
use Illuminate\Http\Request;
class ContactController extends Controller {
    public function store(Request $request) {
        abort_unless(config('site.contact_enabled'), 503);
        $data = $request->validate([
            'name'=>['required','string','max:100'], 'email'=>['required','email','max:255'],
            'company'=>['nullable','string','max:150'], 'phone'=>['nullable','string','max:40'],
            'message'=>['required','string','min:10','max:5000'], 'website'=>['nullable','string','max:0'],
        ], [
            'name.required'=>'Podaj imię.', 'email.required'=>'Podaj adres e-mail.',
            'email.email'=>'Podaj poprawny adres e-mail.', 'message.required'=>'Opisz swój projekt.',
            'message.min'=>'Opis powinien mieć przynajmniej 10 znaków.', 'website.max'=>'Nie udało się przyjąć formularza.',
        ]);
        unset($data['website']);
        Inquiry::create($data);
        return to_route('contact')->with('success', 'Dziękujemy. Twoje zapytanie zostało zapisane i jest dostępne dla zespołu KOBCODE.');
    }
}
