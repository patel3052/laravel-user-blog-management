<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*[0-9]).+$/'],
            'mobile' => ['required', 'digits:10'],
            'dob' => ['required', 'date'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'profile_image' => ['nullable', 'image'],
            'status' => ['required', 'in:Active,Inactive'],
        ]);
        // dd($request->all());

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $newFileName = $filename . '_' . time() . '.' . $extension;

            $profileImagePath = $file->storeAs('profiles', $newFileName, 'public');
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'mobile'        => $request->mobile,
            'dob'           => $request->dob,
            'gender'        => $request->gender,
            'profile_image' => $profileImagePath,
            'status'        => $request->status,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
