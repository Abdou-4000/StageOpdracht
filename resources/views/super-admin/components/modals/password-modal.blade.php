<div id="passwordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form id="passwordForm" method="POST">
            @csrf
            @method('PUT')
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Reset Password</h3>
            
            <div class="mb-4">
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input 
                    type="password" 
                    name="password" 
                    id="new_password" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                    required
                    minlength="8"
                >
                <p class="text-xs text-gray-500 mt-1">Password must be at least 8 characters</p>
            </div>
            
            <div class="mb-4">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                    required
                >
                <p id="password_match_error" class="text-xs text-red-500 mt-1 hidden">Passwords do not match</p>
            </div>

            <div class="mt-4 flex justify-end space-x-3">
                <button 
                    type="button" 
                    onclick="closePasswordModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-[#ff3521] rounded-md hover:bg-[#920000]"
                >
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>