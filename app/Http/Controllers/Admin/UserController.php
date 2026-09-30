<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Staff accounts: who can sign in to the dashboard and under which role.
 */
class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'pharmacy'])->whereNotNull('role_id')->latest('id')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', [
            'user'  => new User(),
            'roles' => $this->assignableRoles(),
            'pharmacies' => Pharmacy::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => ['required', Rule::in($this->assignableRoles()->pluck('id'))],
            'pharmacy_id' => 'nullable|exists:pharmacies,id',
        ]);

        $data = $this->pharmacyForRole($data);

        User::create($data + ['type' => 'employee']);

        return redirect()
            ->route('admin.users.index')
            ->with('msg', __('admin.user_added'))
            ->with('type', 'success');
    }

    public function edit(User $user)
    {
        $this->guardOwner($user);

        return view('admin.users.edit', [
            'user'  => $user,
            'roles' => $this->assignableRoles(),
            'pharmacies' => Pharmacy::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->guardOwner($user);

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id'  => ['required', Rule::in($this->assignableRoles()->pluck('id'))],
            'pharmacy_id' => 'nullable|exists:pharmacies,id',
        ]);

        $data = $this->pharmacyForRole($data);

        // An empty password box means "leave the current one alone"
        if (blank($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('msg', __('admin.user_updated'))
            ->with('type', 'success');
    }

    public function destroy(User $user)
    {
        $this->guardOwner($user);

        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('msg', __('admin.cannot_delete_self'))
                ->with('type', 'warning');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('msg', __('admin.user_deleted'))
            ->with('type', 'info');
    }

    /**
     * Roles a manager may hand out. The owner role is never assignable from
     * this screen, so nobody can promote themselves to it.
     */
    protected function assignableRoles()
    {
        return Role::where('slug', '!=', Role::SUPER_ADMIN)->orderBy('id')->get();
    }

    /** The owner's own account is edited from the account page, not here. */
    protected function guardOwner(User $user): void
    {
        abort_if($user->isSuperAdmin(), 403, __('admin.cannot_touch_owner'));
    }

    /** A pharmacist account always has one manager-assigned pharmacy. */
    protected function pharmacyForRole(array $data): array
    {
        $isPharmacist = Role::whereKey($data['role_id'])->value('slug') === Role::PHARMACIST;
        if ($isPharmacist && empty($data['pharmacy_id'])) {
            throw ValidationException::withMessages([
                'pharmacy_id' => __('admin.pharmacy_assignment_required'),
            ]);
        }

        if (! $isPharmacist) {
            $data['pharmacy_id'] = null;
        }

        return $data;
    }
}
