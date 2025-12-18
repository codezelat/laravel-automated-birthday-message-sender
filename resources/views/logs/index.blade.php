@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Activity Logs</h1>
        <a href="{{ route('contacts.index') }}" class="text-indigo-600 hover:text-indigo-900">Back to Contacts</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Date & Time
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Action
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Details
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-medium">
                        {{ $log->action }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm truncate max-w-xs">
                        {{ $log->description }}
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <span class="relative inline-block px-3 py-1 font-semibold leading-tight
                            @if($log->status == 'success') text-green-900 @elseif($log->status == 'error') text-red-900 @else text-blue-900 @endif">
                            <span aria-hidden class="absolute inset-0 opacity-50 rounded-full
                                @if($log->status == 'success') bg-green-200 @elseif($log->status == 'error') bg-red-200 @else bg-blue-200 @endif"></span>
                            <span class="relative">{{ ucfirst($log->status) }}</span>
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <button onclick="openModal({{ $log->id }})" class="text-indigo-600 hover:text-indigo-900 underline">View</button>
                        
                        <!-- Hidden Modal Content -->
                        <div id="modal-content-{{ $log->id }}" class="hidden">
                            <h3 class="text-lg font-bold mb-2">{{ $log->action }}</h3>
                            <p class="text-sm text-gray-500 mb-4">{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
                            
                            <div class="mb-4">
                                <strong class="block text-gray-700">Description:</strong>
                                <p class="text-gray-600">{{ $log->description }}</p>
                            </div>
                            
                            @if($log->payload)
                            <div class="mb-4">
                                <strong class="block text-gray-700">Payload:</strong>
                                <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto">{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            @endif

                             <div class="mb-4">
                                <strong class="block text-gray-700">Status:</strong>
                                <span class="{{ $log->status == 'success' ? 'text-green-600' : ($log->status == 'error' ? 'text-red-600' : 'text-blue-600') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                        No logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>

<!-- Modal Backdrop -->
<div id="logModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="mt-3">
             <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Log Details</h3>
                 <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-2 px-7 py-3" id="modalBody">
                <!-- Content injected via JS -->
            </div>
            <div class="items-center px-4 py-3">
                <button id="ok-btn" onclick="closeModal()" class="px-4 py-2 bg-indigo-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        const content = document.getElementById('modal-content-' + id).innerHTML;
        document.getElementById('modalBody').innerHTML = content;
        document.getElementById('logModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('logModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('logModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection
