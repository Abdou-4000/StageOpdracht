<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Confirm Deletion</h3>
            
            <p class="text-sm text-gray-500 mb-4">
                Are you sure you want to delete this user? This action cannot be undone.
            </p>
            
            <div class="mt-4 flex justify-end space-x-3">
                <button 
                    type="button" 
                    onclick="closeDeleteModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-[#ff3521] rounded-md hover:bg-[#920000]"
                >
                    Delete User
                </button>
            </div>
        </form>
    </div>
</div>