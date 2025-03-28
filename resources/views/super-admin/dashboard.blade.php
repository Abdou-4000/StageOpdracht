@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Super Admin Dashboard</h1>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold mb-4">Teacher Management</h2>
            
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Roles
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($teachers as $teacher)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @foreach($teacher->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button 
                                type="button"
                                class="text-sm text-blue-600 hover:text-blue-900"
                                onclick="editRoles({{ $teacher->id }})"
                            >
                                Edit Roles
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Role Edit Modal -->
<div id="roleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form id="roleForm" method="POST">
            @csrf
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Roles</h3>
            
            <div class="space-y-3">
                @foreach($roles as $role)
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="roles[]" 
                        value="{{ $role->name }}"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                    >
                    <label class="ml-2 text-sm text-gray-700">{{ $role->name }}</label>
                </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-end space-x-3">
                <button 
                    type="button" 
                    onclick="closeModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editRoles(userId) {
    const modal = document.getElementById('roleModal');
    const form = document.getElementById('roleForm');
    form.action = `/super-admin/teachers/${userId}/roles`;
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('roleModal');
    modal.classList.add('hidden');
}
</script>
@endpush
@endsection