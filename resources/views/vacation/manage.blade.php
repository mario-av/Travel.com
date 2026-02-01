@extends('layouts.app')

@section('title', 'Manage Vacations - Admin')

@section('modal')
{{-- Delete Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6">
        <div class="text-center">
            <i class="bi bi-exclamation-triangle text-red-500 text-5xl mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">Confirm Deletion</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to delete <span id="modal-vacation-name" class="font-bold"></span>?</p>
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
            <h1 class="text-2xl font-bold text-gray-800">Manage Vacations</h1>
            <a href="{{ route('vacation.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="bi bi-plus-lg me-1"></i>Add Vacation
            </a>
        </div>

        {{-- Bulk Delete Form --}}
        <form id="form-delete-group" action="{{ route('admin.vacation.delete.group') }}" method="POST">
            @csrf
            @method('DELETE')

            {{-- Toolbar --}}
            <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
                <div class="flex items-center justify-between">
                    <button type="submit" class="text-red-600 hover:text-red-700 disabled:opacity-50"
                        onclick="return confirm('Delete selected vacations?')">
                        <i class="bi bi-trash me-1"></i>Delete Selected
                    </button>
                    <span class="text-gray-500">{{ $vacations->total() }} total vacations</span>
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
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Vacation</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Category</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Price</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Slots</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($vacations as $vacation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="{{ $vacation->id }}" class="item-checkbox rounded border-gray-300">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden">
                                        @if($vacation->photos->count() > 0)
                                        <img src="{{ url('storage/' . $vacation->photos->first()->path) }}"
                                            alt="{{ $vacation->title }}" class="w-full h-full object-cover">
                                        @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="bi bi-image text-gray-400"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ Str::limit($vacation->title, 30) }}</p>
                                        <p class="text-sm text-gray-500">{{ $vacation->location }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $vacation->category->name ?? '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">${{ number_format($vacation->price, 0) }}</td>
                            <td class="px-4 py-3 {{ $vacation->available_slots < 5 ? 'text-red-600' : 'text-gray-600' }}">
                                {{ $vacation->available_slots }}
                            </td>
                            <td class="px-4 py-3">
                                @if($vacation->active)
                                <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs">Active</span>
                                @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">Inactive</span>
                                @endif
                                @if($vacation->featured)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs ms-1">Featured</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('vacation.show', $vacation) }}" class="text-gray-500 hover:text-blue-600" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('vacation.edit', $vacation) }}" class="text-gray-500 hover:text-yellow-600" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                        onclick="openDeleteModal('{{ $vacation->title }}', '{{ route('vacation.destroy', $vacation) }}')"
                                        class="text-gray-500 hover:text-red-600" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No vacations found. <a href="{{ route('vacation.create') }}" class="text-blue-600 hover:underline">Create one</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
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
            {{ $vacations->links() }}
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
    function openDeleteModal(title, action) {
        document.getElementById('modal-vacation-name').textContent = title;
        document.getElementById('form-delete').action = action;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }
</script>
@endsection