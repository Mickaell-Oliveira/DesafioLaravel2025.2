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
        $admins = User::where('type', 'admin')->orderBy('created_at', 'desc')->paginate(10);
        return view('adminManagement.index', compact('admins'));
    }

    public function adminStore(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'cpf' => 'required|string|max:20|unique:users,cpf',
            'cep' => 'required|string|max:20',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'complemento' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ]);

        $userData = Arr::except($validated, ['cep', 'numero', 'logradouro', 'bairro', 'cidade', 'estado', 'complemento']);
        $addressData = Arr::only($validated, ['cep', 'numero', 'logradouro', 'bairro', 'cidade', 'estado', 'complemento']);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('admins', 'public');
            $userData['photo'] = $path;
        }

        $userData['password'] = Hash::make($validated['password']);
        $userData['created_by'] = Auth::id();
        $userData['type'] = 'admin';

        $user = User::create($userData);

        if (count(array_filter($addressData)) > 0) {
            $user->address()->create($addressData);
        }

        return redirect()->route('adminManagement.index')->with('success', 'Administrador criado com sucesso.');
    }

    public function adminUpdate(Request $request, User $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $admin->id,
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'cpf' => 'required|string|max:20|unique:users,cpf,' . $admin->id,
            'cep' => 'required|string|max:20',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
            'complemento' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
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
