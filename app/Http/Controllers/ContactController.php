<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Artisan;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortColumn = $request->get('sort_by', 'created_at'); // Default sort
        $sortDirection = $request->get('sort_direction', 'desc');

        // Allowable columns for sorting to prevent SQL injection
        $allowableColumns = ['name', 'phone', 'dob', 'last_message_sent_year', 'created_at'];
        if (in_array($sortColumn, $allowableColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $contacts = $query->paginate(10)->withQueryString(); // Preserve query params
        
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

    public function sendManual(Contact $contact, \App\Services\SmsService $smsService)
    {
        // Ensure public token exists
        if (!$contact->public_token) {
            $contact->public_token = \Illuminate\Support\Str::random(10);
            $contact->save();
        }

        $url = route('birthday.show', $contact->public_token);
        $name = strtoupper($contact->name);
        $message = "HAPPY BIRTHDAY {$name}!\n\nSITC Campus wishes you a year filled with success, knowledge and new opportunities.\n\nYour Birthday Card: {$url}\n\nKeep learning, growing and shining bright!";

        if ($smsService->send($contact->phone, $message)) {
            \App\Services\LoggerService::log('Manual Single Send', "Forced message to {$contact->name}.", 'success', ['phone' => $contact->phone]);
            return redirect()->back()->with('success', "Message sent to {$contact->name}.");
        } else {
            \App\Services\LoggerService::log('Manual Single Send Failed', "Failed to send to {$contact->name}.", 'error', ['phone' => $contact->phone]);
            return redirect()->back()->with('error', "Failed to send message to {$contact->name}.");
        }
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

    public function sendToday()
    {
        \App\Services\LoggerService::log('Manual Trigger', 'User triggered manual birthday check.', 'info', ['user' => session('admin_username') ?? 'admin']);
        Artisan::call('birthday:send');
        // Capture output if needed: Artisan::output();
        return redirect()->route('contacts.index')->with('success', 'Manual sending process triggered. Check logs for details.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No contacts selected.');
        }

        $count = count($ids);
        Contact::whereIn('id', $ids)->delete();
        \App\Services\LoggerService::log('Bulk Delete', "Deleted {$count} contacts.", 'success', ['count' => $count]);

        return redirect()->route('contacts.index')->with('success', "{$count} contacts deleted successfully.");
    }

    public function deleteAll()
    {
        $count = Contact::count();
        Contact::truncate();
        \App\Services\LoggerService::log('Delete All', "Deleted all {$count} contacts.", 'warning', ['count' => $count]);

        return redirect()->route('contacts.index')->with('success', 'All contacts have been deleted.');
    }
}
