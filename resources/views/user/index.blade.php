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
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manage Users</h1>
            <a href="{{ route('user.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="bi bi-plus-lg me-1"></i>Add User
            </a>
        </div>

        {{-- Bulk Delete Form --}}
        <form id="form-delete-group" action="{{ route('admin.user.delete.group') }}" method="POST">
            @csrf
            @method('DELETE')

            {{-- Toolbar --}}
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <div class="flex items-center justify-between">
                    <button type="submit" class="text-red-600 hover:text-red-700"
                        onclick="return confirm('Delete selected users?')">
                        <i class="bi bi-trash me-1"></i>Delete Selected
                    </button>
                    <span class="text-gray-500">{{ $users->total() }} total users</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">#</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Role</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Verified</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="item-checkbox rounded border-gray-300">
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $user->id }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs 
                                    {{ $user->rol === 'admin' ? 'bg-red-100 text-red-600' : 
                                       ($user->rol === 'advanced' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600') }}">
                                    {{ ucfirst($user->rol) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($user->hasVerifiedEmail())
                                <span class="text-green-600"><i class="bi bi-check-circle-fill"></i></span>
                                @else
                                <span class="text-gray-400"><i class="bi bi-x-circle"></i></span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('user.show', $user) }}" class="text-gray-500 hover:text-blue-600" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('user.edit', $user) }}" class="text-gray-500 hover:text-yellow-600" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->id !== Auth::id())
                                    <button type="button"
                                        onclick="openDeleteModal('{{ $user->name }}', '{{ route('user.destroy', $user) }}')"
                                        class="text-gray-500 hover:text-red-600" title="Delete">
                                        <i class="bi bi-trash"></i>
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