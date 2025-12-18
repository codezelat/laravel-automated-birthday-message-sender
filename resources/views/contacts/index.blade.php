@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex flex-col lg:flex-row justify-between items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Contacts</h1>
        <div class="flex flex-wrap gap-2 justify-center lg:justify-end">
             <a href="{{ route('logs.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm transition duration-200">
                View Logs
            </a>
            <a href="{{ route('contacts.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm transition duration-200">
                Add Contact
            </a>
            <a href="{{ route('contacts.export') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm transition duration-200">
                Export CSV
            </a>
            <form action="{{ route('contacts.send_today') }}" method="POST" class="inline" id="send-today-form">
                @csrf
                <button type="button" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm transition duration-200" onclick="confirmSendToday()">
                    Send Today's Messages
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 animate-fade-in-down" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate-fade-in-down">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
             <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Import Section -->
    <div class="mb-6 p-4 bg-gray-50 rounded border border-gray-200">
        <h3 class="text-lg font-medium text-gray-700 mb-2">Import Contacts</h3>
        <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-center gap-4">
            @csrf
            <input type="file" name="file" class="block w-full text-sm text-gray-500
              file:mr-4 file:py-2 file:px-4
              file:rounded-full file:border-0
              file:text-sm file:font-semibold
              file:bg-indigo-50 file:text-indigo-700
              hover:file:bg-indigo-100
              transition duration-200
            " required accept=".csv">
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded md:w-auto w-full transition duration-200">
                Import CSV
            </button>
        </form>
        <p class="text-xs text-gray-500 mt-1">Format: Name, Phone, DOB (YYYY-MM-DD)</p>
    </div>

    <!-- Bulk Actions & Search Toolbar -->
    <div class="mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-4 w-full md:w-auto bg-gray-50 p-2 rounded-lg border border-gray-200">
             <div class="flex items-center gap-2 pl-2">
                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-4 w-4">
            </div>
             <div class="flex gap-2">
                <form action="{{ route('contacts.bulk_delete') }}" method="POST" id="bulk-delete-form">
                    @csrf
                    <input type="hidden" name="ids[]" id="bulk-delete-ids">
                    <button type="button" onclick="confirmBulkDelete()" class="px-3 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold rounded-md transition duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-wider" id="btn-delete-selected" disabled>
                        Delete Selected
                    </button>
                </form>
                
                <form action="{{ route('contacts.delete_all') }}" method="POST" id="delete-all-form">
                    @csrf
                    <button type="button" onclick="confirmDeleteAll()" class="px-3 py-1.5 bg-white border border-red-200 text-red-700 hover:bg-red-50 text-xs font-semibold rounded-md transition duration-200 shadow-sm uppercase tracking-wider">
                        Delete All
                    </button>
                </form>
            </div>
        </div>

        <form method="GET" action="{{ route('contacts.index') }}" class="w-full md:w-auto relative flex items-center">
            <div class="relative w-full md:w-72 group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or phone..." class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm transition duration-200 shadow-sm">
                @if(request('search'))
                    <a href="{{ route('contacts.index') }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 cursor-pointer transition duration-200" title="Clear Search">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </div>
            <!-- Hidden sort params to preserve them when searching -->
            @if(request('sort_by'))
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}">
            @endif
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">
                        <!-- Spacer for checkbox column alignment -->
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition duration-150" onclick="window.location='{{ route('contacts.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                        <div class="flex items-center gap-1">
                            Name
                            @if(request('sort_by') == 'name')
                                <span>{{ request('sort_direction') == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </div>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer hover:bg-gray-200 transition duration-150" onclick="window.location='{{ route('contacts.index', array_merge(request()->query(), ['sort_by' => 'phone', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                        <div class="flex items-center gap-1">
                            Phone
                             @if(request('sort_by') == 'phone')
                                <span>{{ request('sort_direction') == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </div>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell cursor-pointer hover:bg-gray-200 transition duration-150" onclick="window.location='{{ route('contacts.index', array_merge(request()->query(), ['sort_by' => 'dob', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                         <div class="flex items-center gap-1">
                            Date of Birth
                             @if(request('sort_by') == 'dob')
                                <span>{{ request('sort_direction') == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </div>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell cursor-pointer hover:bg-gray-200 transition duration-150" onclick="window.location='{{ route('contacts.index', array_merge(request()->query(), ['sort_by' => 'last_message_sent_year', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}'">
                        <div class="flex items-center gap-1">
                            Last Message
                             @if(request('sort_by') == 'last_message_sent_year')
                                <span>{{ request('sort_direction') == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </div>
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <input type="checkbox" name="selected_contacts[]" value="{{ $contact->id }}" class="contact-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 h-5 w-5">
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 font-medium whitespace-no-wrap">{{ $contact->name }}</p>
                        <!-- Mobile View Details -->
                        <div class="sm:hidden text-xs text-gray-500 mt-1">
                            DOB: {{ $contact->dob->format('Y-m-d') }}
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">{{ $contact->phone }}</p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm hidden sm:table-cell">
                        <p class="text-gray-900 whitespace-no-wrap">{{ $contact->dob->format('Y-m-d') }}</p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm hidden md:table-cell">
                        <p class="text-gray-900 whitespace-no-wrap">
                            {{ $contact->last_message_sent_year ?? 'Never' }}
                        </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                onclick="openContactModal(this)" 
                                data-id="{{ $contact->id }}"
                                data-name="{{ $contact->name }}"
                                data-phone="{{ $contact->phone }}"
                                data-dob="{{ $contact->dob->format('Y-m-d') }}"
                                data-last-sent="{{ $contact->last_message_sent_year ?? 'Never' }}"
                                data-public-token="{{ $contact->public_token ?? 'N/A' }}"
                                class="text-blue-600 hover:text-blue-900 font-medium transition duration-150">
                                View
                            </button>
                            <a href="{{ route('contacts.edit', $contact) }}" class="text-indigo-600 hover:text-indigo-900 font-medium transition duration-150">Edit</a>
                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="delete-form inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="text-red-600 hover:text-red-900 font-medium transition duration-150" onclick="confirmDelete(this.form)">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                        No contacts found. Add one manually or import from CSV.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $contacts->links() }}
    </div>
    <div class="mt-4">
        {{ $contacts->links() }}
    </div>
</div>

<form action="{{ route('logout') }}" method="POST" class="mt-6 text-center">
    @csrf
    <button type="submit" class="text-gray-500 hover:text-gray-700 underline transition duration-150">Logout</button>
</form>

<!-- Contact Details Modal - Moved outside main container to prevent Z-index framing issues -->
<div id="contactModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeContactModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal panel -->
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Contact Details</h3>
                            <div class="mt-4 space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Name</label>
                                    <p class="mt-1 text-sm text-gray-900 font-semibold" id="modal-name"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900" id="modal-phone"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Date of Birth</label>
                                    <p class="mt-1 text-sm text-gray-900" id="modal-dob"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Last Message Sent</label>
                                    <p class="mt-1 text-sm text-gray-900" id="modal-last-sent"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Public Token</label>
                                    <p class="mt-1 text-xs text-gray-500 font-mono break-all" id="modal-token"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <form id="force-send-form" method="POST" action="">
                        @csrf
                        <button type="button" onclick="confirmForceSend()" class="inline-flex w-full justify-center rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 sm:ml-3 sm:w-auto">Force Send Message</button>
                    </form>
                    <button type="button" onclick="closeContactModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal Functions
    function openContactModal(element) {
        try {
            document.getElementById('modal-name').textContent = element.dataset.name;
            document.getElementById('modal-phone').textContent = element.dataset.phone;
            document.getElementById('modal-dob').textContent = element.dataset.dob;
            document.getElementById('modal-last-sent').textContent = element.dataset.lastSent;
            document.getElementById('modal-token').textContent = element.dataset.publicToken;
            
            // Set form action
            const id = element.dataset.id;
            const form = document.getElementById('force-send-form');
            form.action = `/contacts/${id}/send-manual`;
            
            document.getElementById('contactModal').classList.remove('hidden');
        } catch (e) {
            console.error('Error opening modal:', e);
            Swal.fire('Error', 'Could not open contact details.', 'error');
        }
    }

    function closeContactModal() {
        document.getElementById('contactModal').classList.add('hidden');
    }

    // Toggle Select All
    const selectAllCheckbox = document.getElementById('select-all');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
    const deleteSelectedBtn = document.getElementById('btn-delete-selected');

    function updateDeleteSelectedBtn() {
        const anyChecked = Array.from(contactCheckboxes).some(cb => cb.checked);
        deleteSelectedBtn.disabled = !anyChecked;
    }

    selectAllCheckbox.addEventListener('change', function() {
        contactCheckboxes.forEach(cb => cb.checked = this.checked);
        updateDeleteSelectedBtn();
    });

    contactCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteSelectedBtn);
    });

    // Confirmation Functions
    function confirmSendToday() {
        Swal.fire({
            title: 'Send Today\'s Messages?',
            text: "This will trigger the sending process for today's birthdays.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d69e2e',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, send them!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Sending...', 'Process triggered in background.', 'success');
                document.getElementById('send-today-form').submit();
            }
        })
    }

    function confirmDelete(form) {
        Swal.fire({
            title: 'Delete Contact?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        })
    }
    
    function confirmForceSend() {
        Swal.fire({
            title: 'Force Send Message?',
            text: "This will send the birthday message IMMEDIATELY, regardless of the date or previous history. Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d69e2e',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Force Send!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('force-send-form').submit();
            }
        })
    }

    function confirmBulkDelete() {
        const selectedIds = Array.from(contactCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: `Delete ${selectedIds.length} Contacts?`,
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Add hidden inputs for ids
                const form = document.getElementById('bulk-delete-form');
                // Clear previous hidden inputs if any (cleaner way)
                form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                form.submit();
            }
        })
    }

    function confirmDeleteAll() {
        Swal.fire({
            title: 'DELETE ALL CONTACTS?',
            text: "This will wipe out the entire database table. This is IRREVERSIBLE!",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, DELETE EVERYTHING!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Double confirmation for safety
                Swal.fire({
                    title: 'Really sure?',
                    text: "Type 'DELETE' to confirm.",
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    confirmButtonColor: '#d33',
                    showLoaderOnConfirm: true,
                    preConfirm: (text) => {
                        if (text !== 'DELETE') {
                            Swal.showValidationMessage('You need to type DELETE')
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-all-form').submit();
                    }
                })
            }
        })
    }
    
    @if(session('success'))
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
        });
    @endif
</script>
@endsection
