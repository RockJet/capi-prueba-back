<?php

namespace App\Http\Controllers;
use App\Models\Contact;
use App\Models\Phone;
use App\Models\Email;
use App\Models\Address;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request){
    // Get the search query parameter from the request
    $searchQuery = $request->query('search');

    // Query contacts where any related model (phones, emails, addresses) has a field containing the search query
    $contacts = Contact::with('phones', 'emails', 'addresses')
        ->whereHas('phones', function ($query) use ($searchQuery) {
            $query->where('phone', 'like', '%' . $searchQuery . '%');
        })
        ->orWhereHas('emails', function ($query) use ($searchQuery) {
            $query->where('email', 'like', '%' . $searchQuery . '%');
        })
        ->orWhereHas('addresses', function ($query) use ($searchQuery) {
            $query->where('address', 'like', '%' . $searchQuery . '%');
        })
        ->orWhere('name', 'like', '%' . $searchQuery . '%')
        ->paginate(10);
        return response()->json($contacts);
    }

    public function create(Request $request){
        $customMessages = [
            "required" => ":attribute es un campo necesario, favor de ingresar información.",
            "max" => "Favor de colocar máximo 20 caracteres",
            "min" => "Favor de colocar mínimo 1 carácter"
        ];

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string','max:150', 'min:1'],
        ], $customMessages);

        if ($validator->fails())
            return response()->json($validator, 400);
        else {

            try {
                DB::transaction(function () use ($request) {
                    $contact = new Contact;
                    $contact->name = $request->name;
                    $contact->updated_at = now();
                    $contact->save();

                    // Collecting all phone numbers, emails and addresses. Adding them
                    // to 'Contact' ONLY if not null
                    $phones = [];
                    $emails = [];
                    $addresses = [];
                    foreach($request->phones as $phone){
                        if($phone) $phones[] = new Phone(['phone' => $phone]);
                    }
                    foreach($request->emails as $email){
                        if($email) $emails[] = new Email(['email' => $email]);
                    }
                    foreach($request->addresses as $address){
                        if($address) $addresses[] = new Address(['address' => $address]);
                    }

                    $contact->phones()->saveMany($phones);
                    $contact->emails()->saveMany($emails);
                    $contact->addresses()->saveMany($addresses);
                });

                return response()->json("El contacto ha sido actualizado correctamente");

            } catch (ModelNotFoundException $e) {
                return response()->json('Problemas al guardar el contacto, intenta de nuevo.');
            }
        }
    }

    public function edit($id){
        $contact = Contact::findOrFail($id);

        $contact = Contact::with('phones', 'emails', 'addresses')->get();

        return response()->json($contact);
    }

    public function update(Request $request, $id){
        $customMessages = [
            "required" => ":attribute es un campo necesario, favor de ingresar información.",
            "max" => "Favor de colocar máximo 20 caracteres",
            "min" => "Favor de colocar mínimo 1 carácter"
        ];

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string','max:150', 'min:1'],
        ], $customMessages);

        if ($validator->fails())
            return back()->withErrors($validator);
        else {

            try {
                DB::transaction(function () use ($request) {
                    $contact = Contact::findOrFail($request->id);
                    $contact->name = $request->name;
                    $contact->updated_at = now();
                    $contact->save();

                    // Collecting all phone numbers, emails and addresses. Adding them
                    // to 'Contact' ONLY if not null
                    $contact->phones()->delete();
                    $contact->emails()->delete();
                    $contact->addresses()->delete();
                    $phones = [];
                    $emails = [];
                    $addresses = [];

                    foreach($request->phones as $phone){
                        if($phone) $phones[] = new Phone(['phone' => $phone]);
                    }
                    foreach($request->emails as $email){
                        if($email) $emails[] = new Email(['email' => $email]);
                    }
                    foreach($request->addresses as $address){
                        if($address) $addresses[] = new Address(['address' => $address]);
                    }

                    $contact->phones()->saveMany($phones);
                    $contact->emails()->saveMany($emails);
                    $contact->addresses()->saveMany($addresses);
                });

                return response()->json("El contacto ha sido actualizado correctamente");
            } catch (ModelNotFoundException $e) {
                return back()->withErrors('Problemas al guardar el contacto, intenta de nuevo.');
            }
        }
    }

    public function delete($contact_id){
        $contact_id = Contact::where('id', $contact_id)->delete();

        return response()->json("El contacto ha sido eliminado correctamente");
    }
}
