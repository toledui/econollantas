<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
 public string $email ='';

 /**
 * Send a password reset link to the provided email address.
 */
 public function sendPasswordResetLink(): void
 {
 $this->validate(['email'=> ['required','string','email'],
 ]);

 $status = Password::sendResetLink(
 $this->only('email')
 );

 if ($status != Password::RESET_LINK_SENT) {
 $this->addError('email', __($status));

 return;
 }

 $this->reset('email');

 session()->flash('status', __($status));
 }
}; ?>

<div>
 <h2 class="text-2xl font-extrabold text-slate-900 text-center mb-1">Recuperar Contraseña</h2>
 <p class="text-sm text-slate-500 text-center mb-8">
 Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
 </p>

 <!-- Session Status -->
 <x-auth-session-status class="mb-4" :status="session('status')"/>

 <form wire:submit="sendPasswordResetLink" class="space-y-6">
 <!-- Email Address -->
 <div>
 <x-input-label for="email" value="Correo Electrónico"/>
 <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required
 autofocus placeholder="tu@correo.com"/>
 <x-input-error :messages="$errors->get('email')" class="mt-2"/>
 </div>

 <button type="submit"
 class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3 px-4 rounded-2xl transition-all shadow-lg shadow-primary/20 text-sm">
 Enviar Enlace de Recuperación
 </button>

 <div class="text-center">
 <a class="text-sm font-medium text-primary hover:text-primary/80 transition-colors"
 href="{{ route('login') }}" wire:navigate>
 ← Volver al Inicio de Sesión
 </a>
 </div>
 </form>
</div>