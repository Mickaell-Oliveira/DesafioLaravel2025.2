<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where('type', 'admin')->paginate(10);
        return view('adminManagement.index', compact('admins'));
    }

    public function adminStore(Request $request)
    {
        $admin= User::find(Auth::id());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'cpf' => 'nullable|string|max:20|unique:users,cpf',
            'photo' => 'nullable|image|max:2048',
        ]);

        $addressData = $request->only(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento']);
        if ($admin->address) {
            $admin->address->update($addressData);
        }else {
            $admin->address()->create($addressData);
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('admins', 'public');
            $validated['photo'] = $path;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['created_by'] =  Auth::user()->id;
        $validated['type'] = 'admin';

        $user = User::create(Arr::except($validated, ['cep','numero','logradouro','bairro','cidade','estado','complemento']));

        $addressFields = ['cep','numero','logradouro','bairro','cidade','estado','complemento'];
        $addressData = $request->only($addressFields);
        if (collect($addressData)->filter()->isNotEmpty()) {
            $user->address()->create($addressData);
        }

        return redirect()->route('adminManagement.index')->with('success', 'Administrador criado com sucesso.');
    }

    public function adminUpdate(Request $request, User $admin)
    {
        $admin= User::find(Auth::id());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'cpf' => 'nullable|string|max:20|unique:users,cpf',
            'photo' => 'nullable|image|max:2048',
        ]);

        $addressData = $request->only(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento']);
        if ($admin->address) {
            $admin->address->update($addressData);
        }else {
            $admin->address()->create($addressData);
        }
        if ($request->hasFile('photo')) {
            if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
                Storage::disk('public')->delete($admin->photo);
            }
            $path = $request->file('photo')->store('admins', 'public');
            $validated['photo'] = $path;
        }

        $admin->update(Arr::except($validated, ['cep','numero','logradouro','bairro','cidade','estado','complemento']));

        return redirect()->route('adminManagement.index')->with('success', 'Administrador atualizado com sucesso.');
    }

    public function adminDestroy(User $admin)
    {
        if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
            Storage::disk('public')->delete($admin->photo);
        }
        $admin->delete();

        return redirect()->route('adminManagement.index')->with('success', 'Administrador excluído com sucesso.');
    }


}
