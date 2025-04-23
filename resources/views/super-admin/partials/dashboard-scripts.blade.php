
// Role permissions mapping from PHP to JavaScript
const rolePermissions = @json($rolePermissions);

// Function to make fields editable inline
function makeEditable(button) {
    const cell = button.closest('td');
    const content = cell.querySelector('.editable-content');
    const fieldName = cell.getAttribute('data-field');
    const fieldValue = content.textContent.trim();
    const userId = button.closest('tr').getAttribute('data-user-id');
    
    // Replace with input
    content.innerHTML = `
        <div class="flex items-center">
            <input 
                type="text" 
                class="border-gray-300 rounded-md shadow-sm text-sm w-full"
                value="${fieldValue}"
                onkeydown="if(event.key === 'Enter') saveField(this, '${userId}', '${fieldName}')"
            />
            <button type="button" class="ml-2" onclick="saveField(this.previousElementSibling, '${userId}', '${fieldName}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
            <button type="button" class="ml-1" onclick="cancelEdit(this, '${fieldValue}')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    `;
    
    // Hide edit button
    button.classList.add('hidden');
    
    // Focus input
    const input = content.querySelector('input');
    input.focus();
    input.select();
}

// Function to save field changes
function saveField(input, userId, field) {
    const value = input.value.trim();
    if (!value) return;
    
    // Send request to update
    fetch(`{{ url('super-admin/users') }}/${userId}/update-field`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            field,
            value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cell = input.closest('td');
            const content = cell.querySelector('.editable-content');
            const editButton = cell.querySelector('button[onclick^="makeEditable"]');
            
            // Restore display
            content.textContent = value;
            editButton.classList.remove('hidden');
        } else {
            alert(data.message || 'Failed to update field');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating');
    });
}

// Function to cancel editing
function cancelEdit(button, originalValue) {
    const cell = button.closest('td');
    const content = cell.querySelector('.editable-content');
    const editButton = cell.querySelector('button[onclick^="makeEditable"]');
    
    // Restore original content
    content.textContent = originalValue;
    editButton.classList.remove('hidden');
}

// Modal handling functions
function closePasswordModal() {
    // Get the modal by ID
    const modal = document.getElementById('passwordModal');
    
    // Reset the form
    const form = document.getElementById('passwordForm');
    if (form) {
        form.reset();
    }
    
    // Hide the error message
    const errorElement = document.getElementById('password_match_error');
    if (errorElement) {
        errorElement.classList.add('hidden');
    }
    
    // Hide the modal
    modal.classList.add('hidden');
}

function closeAccessModal() {
    document.getElementById('accessModal').classList.add('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Function to handle password reset
function resetPassword(userId) {
    const modal = document.getElementById('passwordModal');
    const form = document.getElementById('passwordForm');
    
    // Update form action
    form.action = `{{ url('super-admin/users') }}/${userId}/reset-password`;
    
    // Clear fields
    document.getElementById('new_password').value = '';
    document.getElementById('confirm_password').value = '';
    document.getElementById('password_match_error').classList.add('hidden');
    
    // Remove existing listeners by cloning and replacing elements
    const newPasswordInput = document.getElementById('new_password');
    const confirmInput = document.getElementById('confirm_password');
    
    // Create new confirm input (to remove old event listeners)
    const newConfirmInput = confirmInput.cloneNode(true);
    confirmInput.parentNode.replaceChild(newConfirmInput, confirmInput);
    
    // Add listener to new element
    newConfirmInput.addEventListener('input', function() {
        if (this.value !== newPasswordInput.value) {
            document.getElementById('password_match_error').classList.remove('hidden');
        } else {
            document.getElementById('password_match_error').classList.add('hidden');
        }
    });
    
    // Update form validation
    form.onsubmit = function(e) {
        if (newPasswordInput.value !== newConfirmInput.value) {
            document.getElementById('password_match_error').classList.remove('hidden');
            e.preventDefault();
            return false;
        }
        return true;
    };
    
    // Show modal
    modal.classList.remove('hidden');
}

// Function to confirm deletion
function confirmDelete(userId) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    
    // Update form action
    form.action = "{{ url('super-admin/users') }}/" + userId;
    
    // Show modal
    modal.classList.remove('hidden');
}

// Function to edit user access (roles & permissions)
function editUserAccess(userId, currentRole, userPermissions) {
    const modal = document.getElementById('accessModal');
    const form = document.getElementById('accessForm');
    
    // Update form action
    form.action = `{{ url('super-admin/users') }}/${userId}/access`;
    
    // Get the user from the table
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    const roleBadges = userRow.querySelector('td:nth-child(4)').querySelectorAll('span');
    
    // Check if user is super_admin
    const isSuperAdmin = Array.from(roleBadges).some(badge => 
        badge.textContent.trim() === 'super_admin'
    );
    
    // Reset all radio buttons
    form.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
    
    // Reset all checkboxes
    form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.disabled = false; // Make sure all are enabled initially
    });
    
    // Check user's current role if any
    if (currentRole) {
        const radioButton = form.querySelector(`input[value="${currentRole}"]`);
        if (radioButton) {
            radioButton.checked = true;
            updatePermissionsList(currentRole);
        }
    }
    
    // If role changed from super_admin or to super_admin, updatePermissionsList handles it
    // For other cases, we need to check direct permissions that aren't part of the role
    if (!isSuperAdmin && currentRole !== 'super_admin') {
        setTimeout(() => {
            // Get role-based permissions
            let roleBasedPermissions = [];
            if (currentRole && rolePermissions[currentRole]) {
                roleBasedPermissions = rolePermissions[currentRole];
            }
            
            // Check all direct user permissions
            userPermissions.forEach(permissionName => {
                const checkbox = form.querySelector(`input[value="${permissionName}"]`);
                if (checkbox) {
                    // If it's a role permission, it will already be checked and disabled
                    // Only need to check non-role direct permissions
                    if (!roleBasedPermissions.includes(permissionName)) {
                        checkbox.checked = true;
                    }
                }
            });
        }, 10); // Small delay to ensure updatePermissionsList has finished
    }
    
    // Show modal
    modal.classList.remove('hidden');
}

// Function to update permissions list based on role
function updatePermissionsList(roleName) {
    const container = document.getElementById('rolePermissionsPreview');
    const list = document.getElementById('rolePermissionsList');
    const directPermissionsSection = document.getElementById('directPermissionsSection');
    const form = document.getElementById('accessForm');
    
    // Update permissions list based on selected role
    const permissions = rolePermissions[roleName] || [];
    
    if (roleName === 'super_admin') {
        list.innerHTML = '<span class="text-gray-500">All permissions granted by default</span>';
        directPermissionsSection.innerHTML = `
            <div class="p-4 bg-[#f8f8f8] rounded-md text-center">
                <p class="text-gray-700 font-medium">All permissions inherited by default</p>
                <p class="text-gray-500 text-sm mt-2">Super admins have full system access</p>
            </div>
        `;
    } else {
        // Display role permissions as badges
        if (permissions.length > 0) {
            list.innerHTML = permissions.map(perm => 
                `<span class="inline-block px-2 py-1 mr-1 mb-1 bg-[#71BDBA] text-white rounded-full">${perm}</span>`
            ).join('');
        } else {
            list.innerHTML = '<span class="text-gray-500">No specific permissions for this role</span>';
        }
        
        // Restore the direct permissions section HTML structure
        directPermissionsSection.innerHTML = `
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
        `;
        
        // Pre-check any permissions that are part of this role
        setTimeout(() => {
            const checkboxes = directPermissionsSection.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                // If this permission is part of the selected role, pre-check it
                if (permissions.includes(checkbox.value)) {
                    checkbox.checked = true;
                    checkbox.disabled = false; // Optional: disable editing role permissions
                }
            });
        }, 0);
    }
    
    container.classList.remove('hidden');
}

// Function to show role permissions when creating a user
function showCreateRolePermissions(roleName) {
    const container = document.getElementById('createRolePermissionsContainer');
    const list = document.getElementById('createRolePermissionsList');
    
    // Get permissions for this role
    const permissions = rolePermissions[roleName] || [];
    
    if (permissions.length > 0) {
        list.innerHTML = permissions.map(perm => 
            `<span class="inline-block px-2 py-1 mr-1 mb-1 bg-[#71BDBA] text-white rounded-full">${perm}</span>`
        ).join('');
        container.classList.remove('hidden');
    } else {
        list.innerHTML = '<span class="text-gray-500">Role has been granted All permissions by default</span>';
        container.classList.remove('hidden');
    }
}

// Function to open create modal
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}
