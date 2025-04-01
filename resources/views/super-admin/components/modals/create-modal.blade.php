<div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form id="createForm" method="POST" action="{{ route('super-admin.users.create') }}">
            @csrf
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Create User</h3>
            
            <div class="space-y-3">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <div class="mt-2 space-y-2 max-h-40 overflow-y-auto">
                        @foreach($roles as $role)
                        <div class="flex items-center">
                            <input 
                                type="radio" 
                                name="role" 
                                value="{{ $role->name }}"
                                id="create_role_{{ $role->id }}"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                                onchange="showCreateRolePermissions('{{ $role->name }}')"
                            >
                            <label for="create_role_{{ $role->id }}" class="ml-2 text-sm text-gray-700">{{ $role->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    
                    <div id="createRolePermissionsContainer" class="mt-4 p-3 bg-gray-50 rounded-md hidden">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Permissions included:</h4>
                        <div id="createRolePermissionsList" class="text-xs text-gray-600"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-end space-x-3">
                <button 
                    type="button" 
                    onclick="closeCreateModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                >
                    Create
                </button>
            </div>
        </form>
    </div>
</div>