<?php

namespace App\Livewire\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Register extends Component
{
    #[Layout('components.layouts.guest')]

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $remember = false;

    public function register()
    {

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $messages = [
            'name.required'     => 'Nama tidak boleh kosong',

            'email.required'    => 'Email tidak boleh kosong',
            'email.email'       => 'Format email salah',
            'email.unique'      => 'Email ini sudah terdaftar',

            'password.required' => 'Password harus diisi',
            'password.min'      => 'Password minimal 8 karakter',
            'password.confirmed'=> 'Password konfirmasi tidak cocok'
        ];

        $validated = $this->validate($rules, $messages);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);
        return redirect()->route('chat');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
