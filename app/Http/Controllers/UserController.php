<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function getData()
    {
        $users = User::select(['id', 'name', 'email', 'mobile', 'gender', 'status', 'profile_image']);

        return datatables()->of($users)
            ->addIndexColumn()
            ->addColumn('profile_image', function ($user) {
                if ($user->profile_image) {
                    return '<img src="' . asset('storage/' . $user->profile_image) . '" class="user-thumb">';
                }
                return '<img src="' . asset('storage/images/default-user.png') . '" class="user-thumb">';
            })
            ->addColumn('blogs_count', function ($user) {
                return $user->blogs->count();
            })
            ->addColumn('action', function ($user) {
                return '
                <a href="' . route('users.edit', $user->id) . '" class="btn btn-warning btn-sm">Edit</a>
                <form action="' . route('users.destroy', $user->id) . '" method="POST" style="display:inline;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure want to delete?\')">Delete</button>
                </form>
            ';
            })
            ->rawColumns(['profile_image', 'action'])
            ->make(true);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        try {

            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $newFileName = $filename . '_' . time() . '.' . $extension;
                $profileImagePath = $file->storeAs('profiles', $newFileName, 'public');
            }

            User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'mobile'        => $request->mobile,
                'dob'           => $request->dob,
                'gender'        => $request->gender,
                'profile_image' => $profileImagePath,
                'status'        => $request->status,
            ]);

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'      => ['nullable', 'string', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*[0-9]).+$/'],
            'mobile'        => ['required', 'digits:10'],
            'dob'           => ['required', 'date'],
            'gender'        => ['required', 'in:Male,Female,Other'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
            'status'        => ['required', 'in:Active,Inactive'],
        ]);

        try {
            $data = $request->only(['name', 'email', 'mobile', 'dob', 'gender', 'status']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $newFileName = $filename . '_' . time() . '.' . $extension;
                $newPath = $file->storeAs('profiles', $newFileName, 'public');

                if ($newPath) {
                    if ($user->profile_image && $user->profile_image !== 'images/default-user.png'
                        && Storage::disk('public')->exists($user->profile_image)) {
                        Storage::disk('public')->delete($user->profile_image);
                    }

                    $data['profile_image'] = $newPath;
                }
            }

            $user->update($data);

            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->id == 1) {
                return redirect()->route('users.index')
                    ->with('error', 'Admin user cannot be deleted.');
            }

            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $user->delete();
            return redirect()->route('users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }
}
