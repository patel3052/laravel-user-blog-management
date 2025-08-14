<div class="mb-3">
    <label>Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}">
    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}">
    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="mb-3">
    <label>Password {{ isset($user) ? '(Leave blank to keep current)' : '' }}</label>
    <input type="password" name="password" class="form-control">
    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="mb-3">
    <label>Mobile</label>
    <input type="number" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile ?? '') }}">
    @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="mb-3">
    <label>Date of Birth</label>
    <input type="date" name="dob" class="form-control" value="{{ old('dob', $user->dob ?? '') }}">
    @error('dob') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="mb-3">
    <label>Gender</label>
    <select name="gender" class="form-control">
        <option value="">-- Select --</option>
        <option value="Male" {{ old('gender', $user->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
        <option value="Female" {{ old('gender', $user->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
        <option value="Other" {{ old('gender', $user->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
    </select>
    @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="mb-3">
    <label>Profile Image</label>
    <input type="file" name="profile_image" class="form-control">
    @if(isset($user) && $user->profile_image)
        <img src="{{ asset('storage/' . $user->profile_image) }}" width="80" class="mt-2">
    @endif
    @error('profile_image') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control">
        <option value="Active" {{ old('status', $user->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
        <option value="Inactive" {{ old('status', $user->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') <small class="text-danger">{{ $message }}</small> @enderror
</div>
