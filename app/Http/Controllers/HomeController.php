<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * HomeController - User dashboard and profile management.
 */
class HomeController extends Controller
{
    /**
     * Constructor - Apply middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->except(['index']);
    }

    /**
     * Display the user dashboard.
     *
     * @return View The dashboard view.
     */
    public function index(): View
    {
        return view('auth.home');
    }

    /**
     * Show the profile edit form.
     *
     * @return View The profile edit view.
     */
    public function edit(): View
    {
        return view('auth.edit');
    }

    /**
     * Update the user's profile.
     *
     * @param Request $request The incoming request with profile data.
     * @return RedirectResponse Redirect to dashboard.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $rules = [
            'current-password' => 'current_password',
            'email' => 'required|max:255|email|unique:users,email,' . $user->id,
            'name' => 'required|max:255',
            'password' => 'nullable|min:8|confirmed',
        ];

        $messages = [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'email.unique' => 'This email is already in use.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
            'current-password.current_password' => 'Current password is incorrect.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $user->name = $request->name;

        // If email changes, require re-verification
        if ($user->email != $request->email) {
            $user->email_verified_at = null;
            $user->email = $request->email;
        }

        // Change password only if provided
        if ($request->password != null) {
            $user->password = Hash::make($request->password);
        }

        try {
            $user->save();
            $message = 'Profile updated successfully.';
        } catch (\Exception $e) {
            $message = 'Error saving profile.';
        }

        return redirect()->route('home')->with(['general' => $message]);
    }
}
