<div id="accessModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border max-w-md shadow-lg rounded-md bg-white">
        <form id="accessForm" method="POST">
            @csrf
            @method('PUT')
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit User Access</h3>
            
            <!-- Role Selection -->
            <div class="mb-6">
                <h4 class="text-md font-medium text-gray-700 mb-2">Role</h4>
                <div class="space-y-3 max-h-40 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @foreach($roles as $role)
                    <div class="flex items-center">
                        <input 
                            type="radio" 
                            name="role" 
                            value="{{ $role->name }}"
                            id="access_role_{{ $role->id }}"
                            class="h-4 w-4 text-[#71BDBA] border-gray-300 rounded"
                            onchange="updatePermissionsList('{{ $role->name }}')"
                        >
                        <label for="access_role_{{ $role->id }}" class="ml-2 text-sm text-gray-700">{{ $role->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Role-based permissions preview -->
            <div id="rolePermissionsPreview" class="mb-5 p-3 bg-gray-50 rounded-md">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Role Permissions:</h4>
                <div id="rolePermissionsList" class="text-xs text-gray-600"></div>
            </div>
            
            <!-- Direct permissions section (for non-super_admin users) -->
            <div id="directPermissionsSection">
                <h4 class="text-md font-medium text-gray-700 mb-2">Additional Permissions</h4>
                <p class="text-xs text-gray-500 mb-2">These permissions will be assigned directly to the user in addition to role permissions.</p>
                
                <div class="space-y-3 max-h-40 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @foreach($permissions as $permission)
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="permissions[]" 
                            value="{{ $permission->name }}"
                            id="access_permission_{{ $permission->id }}"
                            class="h-4 w-4 text-[#71BDBA] border-gray-300 rounded"
                        >
                        <label for="access_permission_{{ $permission->id }}" class="ml-2 text-sm text-gray-700">{{ $permission->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button 
                    type="button" 
                    onclick="closeAccessModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-[#71BDBA] rounded-md hover:bg-[#5a9997]"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</div>