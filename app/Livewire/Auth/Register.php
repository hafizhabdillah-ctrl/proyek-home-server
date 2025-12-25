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
    public $confirmpassword = '';
    public $remember = false;

    // Validation rules
    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'confirmpassword' => 'required|same:password', // Pastikan sama dengan password
    ];

    public function register()
    {

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
