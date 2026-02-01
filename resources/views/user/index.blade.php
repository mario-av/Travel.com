@extends('layouts.app')

@section('title', 'Manage Users - Admin')

@section('modal')
{{-- Delete Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="text-center">
            <i class="bi bi-exclamation-triangle text-red-500 text-5xl mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Confirm Deletion</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to delete user <span id="modal-user-name" class="font-bold"></span>?</p>
        </div>
        <div class="flex space-x-4">
            <button onclick="document.getElementById('deleteModal').classList.add('hidden')"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </button>
            <button onclick="document.getElementById('form-delete').submit()"
                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Delete
            </button>
        </div>
    </div>
</div>
@endsection

@section('content')
@include('partials.admin_nav')

<div class="py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manage Users</h1>
            <a href="{{ route('user.create') }}" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-2 rounded-lg transition font-semibold">
                <i class="bi bi-plus-lg me-1"></i>Add User
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
        @endif

        {{-- Bulk Delete Form --}}
        <form id="form-delete-group" action="{{ route('admin.user.delete.group') }}" method="POST">
            @csrf
            @method('DELETE')

            {{-- Toolbar --}}
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <div class="flex items-center justify-between">
                    <button type="submit" class="text-red-600 hover:text-red-700 disabled:opacity-50"
                        onclick="return confirm('Delete selected users?')">
                        <i class="bi bi-trash me-1"></i>Delete Selected
                    </button>
                    <span class="text-gray-500">{{ $users->total() }} total users</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Role</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="item-checkbox rounded border-gray-300">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-rose-400 to-rose-600 rounded-full flex items-center justify-center shrink-0 shadow-sm">
                                            <span class="text-white font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.user.role', $user) }}" method="POST" class="flex items-center">
                                        @csrf
                                        @php
                                        $roleClasses = match($user->rol) {
                                        'admin' => 'bg-red-50 text-red-700 border-red-100',
                                        'advanced' => 'bg-purple-50 text-purple-700 border-purple-100',
                                        default => 'bg-blue-50 text-blue-700 border-blue-100'
                                        };
                                        @endphp
                                        <select name="rol" onchange="this.form.submit()"
                                            class="text-xs border-gray-200 rounded-lg focus:ring-rose-500 focus:border-rose-500 py-1 pl-2 pr-8 {{ $roleClasses }}">
                                            <option value="normal" {{ $user->rol === 'normal' ? 'selected' : '' }}>User</option>
                                            <option value="advanced" {{ $user->rol === 'advanced' ? 'selected' : '' }}>Advanced</option>
                                            <option value="admin" {{ $user->rol === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2 text-xs">
                                        @if($user->hasVerifiedEmail())
                                        <span class="flex items-center text-green-600 font-medium">
                                            <i class="bi bi-patch-check-fill me-1"></i>Verified
                                        </span>
                                        @else
                                        <span class="flex items-center text-gray-400 font-medium">
                                            <i class="bi bi-clock-history me-1"></i>Unverified
                                        </span>
                                        <form action="{{ route('admin.user.verify', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-rose-500 hover:text-rose-600 underline font-bold">Approve</button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('user.edit', $user) }}" class="text-gray-400 hover:text-yellow-600 transition" title="Edit">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </a>
                                        @if($user->id !== Auth::id())
                                        <button type="button"
                                            onclick="openDeleteModal('{{ addslashes($user->name) }}', '{{ route('user.destroy', $user) }}')"
                                            class="text-gray-400 hover:text-red-600 transition" title="Delete">
                                            <i class="bi bi-trash3 text-lg"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-sm text-gray-600">Total users:</td>
                                <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-800">{{ $users->total() }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </form>

        {{-- Single Delete Form --}}
        <form id="form-delete" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // Delete modal
    function openDeleteModal(name, action) {
        document.getElementById('modal-user-name').textContent = name;
        document.getElementById('form-delete').action = action;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
</script>
@endsection