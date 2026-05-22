<?php
// ============================================================
// app/Http/Controllers/UserController.php  — versi update
// ============================================================
 
namespace App\Http\Controllers;
 
use App\Models\User;
use App\Traits\Searchable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
 
class UserController extends Controller
{
    use Searchable;
 
    public function index(Request $request): View
    {
        $query = User::with('roles');
 
        $this->applySearch(
            $query,
            $request,
            searchColumns: ['name', 'email'],
        );
 
        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }
 
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
 
        $users = $query->latest()->paginate($this->perPage($request))->withQueryString();
        $roles = Role::orderBy('name')->get();
 
        return view('users.index', compact('users', 'roles'));
    }
 
    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }
 
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);
 
        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);
 
        $user->assignRole($validated['role']);
 
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }
 
    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        return view('users.edit', compact('user', 'roles'));
    }
 
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|string|min:8|confirmed',
            'role'      => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);
 
        $data = [
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'is_active' => $request->boolean('is_active', true),
        ];
 
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
 
        $user->update($data);
        $user->syncRoles([$validated['role']]);
 
        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }
 
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
 
    public function show(User $user): RedirectResponse
    {
        return redirect()->route('users.index');
    }
}