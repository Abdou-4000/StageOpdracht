@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Super Admin Dashboard</h1>
        <button 
            type="button"
            class="px-4 py-2 bg-[#ff3521] text-white rounded-md hover:bg-[#920000] transition duration-150"
            onclick="openCreateModal()"
        >
            Create User
        </button>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold mb-4">User Management</h2>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#22262d]">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-centre text-xs font-medium text-white uppercase tracking-wider w-1/5">
                                ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-centre text-xs font-medium text-white uppercase tracking-wider w-1/5">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-centre text-xs font-medium text-white uppercase tracking-wider w-1/5">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-centre text-xs font-medium text-white uppercase tracking-wider w-1/5">
                                Roles
                            </th>
                            <th scope="col" class="px-6 py-3 text-centre text-xs font-medium text-white uppercase tracking-wider w-1/5">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50" data-user-id="{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">{{ $user->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap" data-field="name">
                                <div class="flex items-center group">
                                    <span class="editable-content">{{ $user->name }}</span>
                                    <button type="button" class="ml-2 invisible group-hover:visible" onclick="makeEditable(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" data-field="email">
                                <div class="flex items-center group">
                                    <span class="editable-content">{{ $user->email }}</span>
                                    <button type="button" class="ml-2 invisible group-hover:visible" onclick="makeEditable(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-1 text-xs rounded-full bg-[#9C91C5] text-white">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                            <button 
                                type="button"
                                class="text-sm text-[#71BDBA] hover:text-[#ff3521] mr-2"
                                onclick="editUserAccess({{ $user->id }}, '{{ $user->roles->first() ? $user->roles->first()->name : '' }}', {{ json_encode($user->permissions->pluck('name')) }})"
                            >
                                Edit Access
                            </button>
                                
                                <button 
                                    type="button"
                                    class="text-sm text-[#FBBA00] hover:text-[#ff3521] mr-2"
                                    onclick="resetPassword({{ $user->id }})"
                                >
                                    Reset Password
                                </button>


                                @if(auth()->id() !== $user->id)
                                    <button 
                                        type="button" 
                                        class="text-sm text-[#ff3521] hover:text-[#920000]"
                                        onclick="confirmDelete({{ $user->id }})"
                                    >
                                        Delete
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> 
        </div>
    </div>
</div>


@include('super-admin.components.modals.access-modal')
@include('super-admin.components.modals.delete-modal')
@include('super-admin.components.modals.password-modal')
@include('super-admin.components.modals.create-modal')


@push('scripts')
<script>
    @include('super-admin.partials.dashboard-scripts')
</script>
@endpush

@endsection