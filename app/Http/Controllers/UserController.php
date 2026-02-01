<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * UserController - Admin controller for user management.
 * Requires admin role for all operations.
 */
class UserController extends Controller
{
    /**
     * Display a listing of all users.
     *
     * @param Request $request The incoming request with optional filters.
     * @return View The user management view.
     */
    public function index(Request $request): View
    {
        try {
            $query = User::query();

            // Search by name or email
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by role
            if ($request->filled('role')) {
                $query->where('rol', $request->input('role'));
            }

            $users = $query->orderBy('created_at', 'desc')->paginate(20);

            return view('user.index', compact('users'));
        } catch (\Exception $e) {
            return view('user.index', [
                'users' => collect(),
                'error' => 'Error loading users: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new user.
     *
     * @return View The create user form.
     */
    public function create(): View
    {
        return view('user.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param Request $request The incoming request with user data.
     * @return RedirectResponse Redirect to user index.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|in:normal,advanced,admin',
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);
            User::create($validated);

            return redirect()
                ->route('user.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param User $user The user to edit.
     * @return View The edit user form.
     */
    public function edit(User $user): View
    {
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param Request $request The incoming request with updated data.
     * @param User $user The user to update.
     * @return RedirectResponse Redirect to user index.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'rol' => 'required|in:normal,advanced,admin',
        ]);

        try {
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return redirect()
                ->route('user.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user The user to delete.
     * @return RedirectResponse Redirect to user index.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return redirect()
                    ->back()
                    ->with('error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()
                ->route('user.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Delete multiple selected users.
     *
     * @param Request $request The request with user IDs.
     * @return RedirectResponse Redirect to user index.
     */
    public function deleteGroup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        try {
            // Exclude current user from deletion
            $userIds = array_filter($validated['users'], fn($id) => $id != auth()->id());

            User::whereIn('id', $userIds)->delete();

            return redirect()
                ->route('user.index')
                ->with('success', count($userIds) . ' user(s) deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error deleting users: ' . $e->getMessage());
        }
    }
    /**
     * Verify a user's email address manually.
     *
     * @param User $user The user to verify.
     * @return RedirectResponse
     */
    public function verifyEmail(User $user): RedirectResponse
    {
        try {
            $user->markEmailAsVerified();

            return redirect()
                ->route('user.index')
                ->with('success', "Email for {$user->name} has been verified.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error verifying email: ' . $e->getMessage());
        }
    }

    /**
     * Update a user's role.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'rol' => 'required|in:normal,advanced,admin',
        ]);

        try {
            // Prevent changing own role (safety measure)
            if ($user->id === auth()->id() && $validated['rol'] !== 'admin') {
                return redirect()
                    ->back()
                    ->with('error', 'You cannot downgrade your own role.');
            }

            $user->update(['rol' => $validated['rol']]);

            return redirect()
                ->route('user.index')
                ->with('success', "Role for {$user->name} updated to " . ucfirst($validated['rol']));
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }
}
