<?php

use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
 use WithFileUploads;

 public string $name ='';
 public string $email ='';
 public $avatar = null;
 public ?string $current_avatar = null;

 /**
 * Mount the component.
 */
 public function mount(): void
 {
 $this->name = Auth::user()->name;
 $this->email = Auth::user()->email;
 $this->current_avatar = Auth::user()->avatar;
 }

 /**
 * Update the profile information for the currently authenticated user.
 */
 public function updateProfileInformation(): void
 {
 $user = Auth::user();

 $validated = $this->validate(['name'=> ['required','string','max:255'], 'email'=> ['required','string','lowercase','email','max:255', Rule::unique(User::class)->ignore($user->id)], 'avatar'=> ['nullable','image','max:1024'],
 ]);

 $user->fill(['name'=> $validated['name'], 'email'=> $validated['email'],
 ]);

 if ($user->isDirty('email')) {
 $user->email_verified_at = null;
 }

 if ($this->avatar) {
 $path = $this->avatar->store('avatars','public');
 $user->avatar = $path;
 $this->current_avatar = $path;
 $this->avatar = null;
 }

 $user->save();

 $this->dispatch('profile-updated', name: $user->name);
 }

 /**
 * Send an email verification notification to the current user.
 */
 public function sendVerification(): void
 {
 $user = Auth::user();

 if ($user->hasVerifiedEmail()) {
 $this->redirectIntended(default: route('dashboard', absolute: false));

 return;
 }

 $user->sendEmailVerificationNotification();

 Session::flash('status','verification-link-sent');
 }
}; ?>

<section>
 <header>
 <h2 class="text-lg font-bold text-gray-900">
 {{ __('Información del Perfil') }}
 </h2>

 <p class="mt-1 text-sm text-gray-600">
 {{ __("Actualiza la información de tu cuenta, tu correo electrónico y tu foto de perfil.") }}
 </p>
 </header>

 <form wire:submit="updateProfileInformation" class="mt-6 space-y-8">
 <!-- Avatar Section -->
 <div class="flex items-center gap-6">
 <div class="relative group">
 <div class="size-24 rounded-full overflow-hidden border-4 border-primary/20 bg-background-light flex items-center justify-center">
 @if ($avatar)
 <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
 @elseif ($current_avatar)
 <img src="{{ asset('storage/'. $current_avatar) }}" class="w-full h-full object-cover">
 @else
 <span class="material-symbols-outlined text-4xl text-gray-400">person</span>
 @endif
 </div>
 <label for="avatar_input" class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
 <span class="material-symbols-outlined">photo_camera</span>
 </label>
 <input type="file" wire:model="avatar" id="avatar_input" class="hidden" accept="image/*">
 </div>
 
 <div>
 <h3 class="font-bold text-gray-900">Foto de Perfil</h3>
 <p class="text-xs text-gray-500 mt-1">PNG, JPG hasta 1MB. Haz clic en la imagen para cambiarla.</p>
 <x-input-error class="mt-2" :messages="$errors->get('avatar')"/>
 
 <div wire:loading wire:target="avatar" class="mt-2 text-primary text-xs font-bold">
 Subiendo archivo...
 </div>
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <x-input-label for="name" :value="__('Nombre Completo')"/>
 <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name"/>
 <x-input-error class="mt-2" :messages="$errors->get('name')"/>
 </div>

 <div>
 <x-input-label for="email" :value="__('Correo Electrónico')"/>
 <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username"/>
 <x-input-error class="mt-2" :messages="$errors->get('email')"/>

 @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
 <div>
 <p class="text-sm mt-2 text-gray-800">
 {{ __('Your email address is unverified.') }}

 <button wire:click.prevent="sendVerification"
 class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 {{ __('Click here to re-send the verification email.') }}
 </button>
 </p>

 @if (session('status') ==='verification-link-sent')
 <p class="mt-2 font-medium text-sm text-green-600">
 {{ __('A new verification link has been sent to your email address.') }}
 </p>
 @endif
 </div>
 @endif
 </div>

 <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
 <x-primary-button>{{ __('Guardar Cambios') }}</x-primary-button>

 <x-action-message class="me-3" on="profile-updated">
 {{ __('Guardado correctamente.') }}
 </x-action-message>
 </div>
 </form>
</section>