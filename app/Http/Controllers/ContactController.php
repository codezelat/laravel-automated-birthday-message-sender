<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('name')->paginate(10);
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Contact::create($request->all());

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $contact->update($request->all());

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        
        // Simple CSV import
        if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
            $header = fgetcsv($handle); // Skip header or use it to map
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                // Determine column indices based on standard format: Name, Phone, DOB
                // Assuming simple structure: Name, Phone, DOB (YYYY-MM-DD)
                // Better implementation would map headers
                
                if (count($data) >= 3) {
                     Contact::create([
                        'name' => $data[0],
                        'phone' => $data[1],
                        'dob' => $data[2],
                    ]);
                }
            }
            fclose($handle);
        }

        return redirect()->route('contacts.index')->with('success', 'Contacts imported successfully.');
    }

    public function export()
    {
        $fileName = 'contacts.csv';
        $contacts = Contact::all();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Name', 'Phone', 'DOB', 'Last Message Year');

        $callback = function() use($contacts, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($contacts as $contact) {
                fputcsv($file, array($contact->name, $contact->phone, $contact->dob->format('Y-m-d'), $contact->last_message_sent_year));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
