<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::with('roles')->orderBy('created_at', 'desc')->get();
        $roles = Role::all();

        return view('admin.users.index', compact('admins', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string',
            'role' => 'required|exists:roles,name',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => trim($request->input('name')),
            'email' => strtolower(trim($request->input('email'))),
            'mobile' => $request->input('mobile'),
            'password' => Hash::make($request->input('password')),
            'status' => 'ACTIVE',
            'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80',
        ]);

        $user->assignRole($request->input('role'));

        AuditLog::log('CREATE_ADMIN', 'SECURITY', (string) $user->id, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $request->input('role'),
        ]);

        return redirect()->route('admin.users.index')->with('success', "Admin user '{$user->name}' created with role '{$request->input('role')}'.");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        $newStatus = $user->status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $user->update(['status' => $newStatus]);

        AuditLog::log('STATUS_TOGGLE', 'SECURITY', (string) $user->id, [
            'admin' => $user->name,
            'new_status' => $newStatus,
        ]);

        return redirect()->back()->with('success', "Admin user {$user->name} is now {$newStatus}.");
    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        if ($user->id === auth()->id() && $request->input('role') !== 'Super Admin') {
            return back()->withErrors(['error' => 'You cannot demote your own Super Admin account.']);
        }

        $user->syncRoles([$request->input('role')]);

        AuditLog::log('ROLE_CHANGE', 'SECURITY', (string) $user->id, [
            'admin' => $user->name,
            'new_role' => $request->input('role'),
        ]);

        return redirect()->back()->with('success', "Role for {$user->name} updated to {$request->input('role')}.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $name = $user->name;
        $user->delete();

        AuditLog::log('DELETE_ADMIN', 'SECURITY', (string) $id, ['name' => $name]);

        return redirect()->route('admin.users.index')->with('success', "Admin {$name} deleted.");
    }
}
