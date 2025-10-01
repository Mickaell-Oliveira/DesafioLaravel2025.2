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
    public function index() // lista todos os administradores
    {
        $admins = User::where('type', 'admin')->orderBy('created_at', 'desc')->paginate(10);
        return view('adminManagement.index', compact('admins'));
    }

    public function adminStore(Request $request) // cria um novo administrador
    {

        $validated = $request->validate([ // valida os dados do formulário
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
            'complemento' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ]);

        $userData = Arr::except($validated, ['cep', 'numero', 'logradouro', 'bairro', 'cidade', 'estado', 'complemento']); // extrai os dados do usuário
        $addressData = Arr::only($validated, ['cep', 'numero', 'logradouro', 'bairro', 'cidade', 'estado', 'complemento']); // extrai os dados do endereço

        if ($request->hasFile('photo')) { // se houver uma foto, armazena
            $path = $request->file('photo')->store('admins', 'public');
            $userData['photo'] = $path;
        }

        $userData['password'] = Hash::make($validated['password']); // hash da senha
        $userData['created_by'] = Auth::id(); // id do usuário que criou
        $userData['type'] = 'admin'; // define o tipo como admin

        $user = User::create($userData); // cria o usuário

        if (count(array_filter($addressData)) > 0) { // Cria o endereço na tabela de endereços
            $user->address()->create($addressData);
        }

        return redirect()->route('adminManagement.index')->with('success', 'Administrador criado com sucesso.');
    }

    public function adminUpdate(Request $request, User $admin) // atualiza um administrador
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
            'complemento' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ]);

        // Atualiza os dados do administrador e do endereço
        $addressData = $request->only(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento']);
        if ($admin->address) {
            $admin->address->update($addressData);
        }else {
            $admin->address()->create($addressData);
        }
        if ($request->hasFile('photo')) { // trata a foto
            if ($admin->photo && Storage::disk('public')->exists($admin->photo)) {
                Storage::disk('public')->delete($admin->photo);
            }
            $path = $request->file('photo')->store('admins', 'public');
            $validated['photo'] = $path;
        }

        $admin->updated_at = now(); // atualiza o timestamp

        $admin->update(Arr::except($validated, ['cep','numero','logradouro','bairro','cidade','estado','complemento'])); // atualiza o administrador

        return redirect()->route('adminManagement.index')->with('success', 'Administrador atualizado com sucesso.');
    }

    public function adminDestroy(User $admin) // exclui um administrador
    {
        if ($admin->photo && Storage::disk('public')->exists($admin->photo)) { // exclui a foto
            Storage::disk('public')->delete($admin->photo);
        }
        $admin->delete();

        return redirect()->route('adminManagement.index')->with('success', 'Administrador excluído com sucesso.');
    }


}
