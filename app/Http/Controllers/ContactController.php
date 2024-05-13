<?php

namespace App\Http\Controllers;
use App\Models\Contact;
use App\Models\Phone;
use App\Models\Email;
use App\Models\Address;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(){
        $contacts = Contact::get();

        $contacts->phones()->get();
        $contacts->emails()->get();
        $contacts->addresses()->get();

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
                $contact = new Contact;
                $contact->name = $request->name;
                $contact->updated_at = now();
                $contact->save();

                return response()->json("El contacto ha sido actualizado correctamente");
            } catch (ModelNotFoundException $e) {
                return back()->withErrors('Problemas al guardar el contacto, intenta de nuevo.');
            }
        }
    }

    public function update(Request $request){
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
                $contact = Contact::where('id', $id)->first();
                $contact->name = $request->name;
                $contact->updated_at = now();
                $contact->save();

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
