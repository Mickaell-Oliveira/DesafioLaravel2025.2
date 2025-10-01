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
    public function index() // exibe a lista de usuários
    {
        $users = User::where('type', 'user')->orderBy('created_at', 'desc')->paginate(10);
        return view('usersManagement.index', compact('users'));
    }

    public function profile() // exibe o perfil do usuário autenticado
    {
        $user = Auth::user();
        return view('profilePage.index', compact('user'));
    }

    public function destroy() // exclui a conta do usuário autenticado
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/')->with('status', 'Sua conta e todos os dados foram excluídos.');
    }
    public function update(Request $request) // atualiza o perfil do usuário autenticado
    {
        $user = User::find(Auth::id());

        $validated = $request->validate([ // valida os dados do formulário
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
            'complemento' => 'nullable|string|max:255',
        ]);

        $addressData = $request->only(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento']); // dados do endereço
        if ($user->address) {
            $user->address->update($addressData); // atualiza o endereço se existir
        }

        $user->fill($validated); // atualiza os dados do usuário
        if ($request->hasFile('photo')) {
            // Remove foto antiga se existir
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('users', 'public'); // armazena a nova foto
            $user->photo = $path; // atualiza o caminho da foto
        }
        $user->updated_at = now(); // atualiza o timestamp
        $user->save(); // salva as alterações

        return redirect()->route('profilePage.index')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function store(Request $request) // cria um novo usuário
    {
        $validated = $request->validate([ // valida os dados do formulário
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
            'complemento' => 'nullable|string|max:255',
        ]);

        $addressData = $request->only(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento']); // dados do endereço

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('users', 'public'); // armazena a foto
        }

        $validated['password'] = Hash::make($validated['password']); // hash da senha
        $validated['type'] = 'user'; // tipo de usuário
        $validated['created_by'] = Auth::user()->id; // id do admin que criou o usuário

        $user = User::create($validated); // cria o usuário

        $user->address()->create($addressData); // cria o endereço na tabela de endereços

        return redirect()->route('usersManagement.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function adminUpdate(Request $request, User $user) // Permite ao admin atualizar um usuário
    {
        $validated = $request->validate([ // valida os dados do formulário
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'cpf' => 'required|string|max:20|unique:users,cpf,' . $user->id,
            'saldo' => 'required|numeric',
            'cep' => 'required|string|max:10',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'complemento' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $addressData = $request->only(['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento']); // dados do endereço

        if ($user->address) {
            $user->address->update($addressData); // atualiza o endereço se existir
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo); // remove a foto antiga
            }

            $path = $request->file('photo')->store('users', 'public'); // armazena a nova foto
            $validated['photo'] = $path; // atualiza o caminho da foto
        }

        $user->updated_at = now(); // atualiza o timestamp

        $user->update($validated); // atualiza os dados do usuário

        return redirect()->route('usersManagement.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function adminDestroy(User $user) // Permite ao admin excluir um usuário
    {
        $user->delete(); // exclui o usuário

        return redirect()->route('usersManagement.index')->with('success', 'Usuário excluído com sucesso!');
    }

    public function sendEmail(Request $request, User $user) // Permite ao admin enviar um email para um usuário
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255', // assunto do email
            'message' => 'required|string', // corpo do email
        ]);

        Mail::raw($validated['message'], function ($mail) use ($user, $validated) {
            $mail->to($user->email)->subject($validated['subject']); // envia o email
        });

        Email::create([ // registra o email na tabela de emails
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'subject' => $validated['subject'],
            'body' => $validated['message'],
        ]);

        return redirect()->route('usersManagement.index')->with('success', 'Email enviado com sucesso para ' . $user->email);
    }

    public function changePassword()
    {
        return view('profilePage.changePassword');
    }

}
