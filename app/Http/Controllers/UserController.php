<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('type', 'user')->orderBy('created_at', 'desc')->paginate(10);
        return view('usersManagement.index', compact('users'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profilePage.index', compact('user'));
    }

    public function destroy()
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/')->with('status', 'Sua conta e todos os dados foram excluídos.');
    }
    public function update(Request $request)
    {
        $user = User::find(Auth::id());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'cpf' => 'required|string|max:20|unique:users,cpf,' . $user->id,
            'photo' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'cep' => 'required|string|max:10',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'complemento' => 'required|string|max:255',
        ]);

        $addressData = $request->only(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento']);
        if ($user->address) {
            $user->address->update($addressData);
        }else {
            $user->address()->create($addressData);
        }

        $user->fill($validated);
        if ($request->hasFile('photo')) {
            // Remove foto antiga se existir
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('users', 'public');
            $user->photo = $path;
        }
        $user->save();

        return redirect()->route('profilePage.index')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'cpf' => 'required|string|max:14|unique:users,cpf',
            'saldo' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cep' => 'required|string|max:10',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'complemento' => 'required|string|max:255',
        ]);

        $addressData = $request->only(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento']);



        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['type'] = 'user';
        $validated['created_by'] = Auth::user()->id;

        $user = User::create($validated);

        $user->address()->create($addressData);

        return redirect()->route('usersManagement.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function adminUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'cpf' => 'nullable|string|max:20|unique:users,cpf,' . $user->id,
            'saldo' => 'nullable|numeric',
            'cep' => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:10',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'complemento' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $addressData = $request->only(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento']);

        if ($user->address) {
            $user->address->update($addressData);
        } else {
            $user->address()->create($addressData);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $request->file('photo')->store('users', 'public');
            $validated['photo'] = $path;
        }

        $user->update($validated);

        return redirect()->route('usersManagement.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function adminDestroy(User $user)
    {
        $user->delete();

        return redirect()->route('usersManagement.index')->with('success', 'Usuário excluído com sucesso!');
    }

    public function sendEmail(Request $request, User $user)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Mail::raw($validated['message'], function ($mail) use ($user, $validated) {
            $mail->to($user->email)->subject($validated['subject']);
        });

        Email::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'subject' => $validated['subject'],
            'body' => $validated['message'],
        ]);

        return redirect()->route('usersManagement.index')->with('success', 'Email enviado com sucesso para ' . $user->email);
    }

}
