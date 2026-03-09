<div class="flex-1 overflow-y-auto p-8 relative">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Servicios de Correo (SMTP)
            </h1>
            <p class="text-slate-500 mt-1">Configura los servidores de envío para notificaciones y
                alertas del sistema.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Section -->
            <div class="lg:col-span-2 space-y-6">
                <form wire:submit="save" class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900 flex items-center">
                                <span class="material-symbols-outlined mr-2 text-primary">mail</span>
                                Servidor Saliente
                            </h3>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-input-label for="mail_host" value="Host SMTP (Servidor)" />
                                    <x-text-input id="mail_host" type="text" wire:model="mail_host"
                                        class="mt-1 block w-full" placeholder="smtp.mailtrap.io" />
                                </div>

                                <div>
                                    <x-input-label for="mail_port" value="Puerto" />
                                    <x-text-input id="mail_port" type="text" wire:model="mail_port"
                                        class="mt-1 block w-full" placeholder="587" />
                                </div>

                                <div>
                                    <x-input-label for="mail_encryption" value="Cifrado" />
                                    <select wire:model="mail_encryption"
                                        class="mt-1 block w-full border-slate-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm">
                                        <option value="">Ninguno</option>
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label for="mail_username" value="Usuario de Autenticación" />
                                    <x-text-input id="mail_username" type="text" wire:model="mail_username"
                                        class="mt-1 block w-full" />
                                </div>

                                <div>
                                    <x-input-label for="mail_password" value="Contraseña / Token" />
                                    <x-text-input id="mail_password" type="password" wire:model="mail_password"
                                        class="mt-1 block w-full" />
                                </div>

                                <div class="md:col-span-2 border-t border-slate-100 pt-6">
                                    <h4 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider">
                                        Remitente Predeterminado</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <x-input-label for="mail_from_address" value="Correo Remitente" />
                                            <x-text-input id="mail_from_address" type="email"
                                                wire:model="mail_from_address" class="mt-1 block w-full" />
                                        </div>
                                        <div>
                                            <x-input-label for="mail_from_name" value="Nombre Remitente" />
                                            <x-text-input id="mail_from_name" type="text" wire:model="mail_from_name"
                                                class="mt-1 block w-full" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(Auth::user()->hasPermission('settings.edit'))
                        <div class="flex justify-end">
                            <x-primary-button class="!rounded-2xl !px-12 !py-4 shadow-xl shadow-primary/20">
                                <span class="material-symbols-outlined mr-2">save</span>
                                Guardar Conexión
                            </x-primary-button>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Testing Section -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-primary/5 rounded-3xl p-8 border border-primary/20">
                    <h3 class="font-bold text-slate-900 flex items-center mb-4">
                        <span class="material-symbols-outlined mr-2 text-primary">send</span>
                        Test de Envío
                    </h3>
                    <p class="text-xs text-slate-500 mb-6">
                        Verifica que los datos ingresados sean correctos enviando un correo de prueba ahora mismo.
                    </p>

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="test_email" value="Enviar prueba a:" />
                            <x-text-input id="test_email" type="email" wire:model="test_email"
                                class="mt-1 block w-full text-sm" />
                            @if(Auth::user()->hasPermission('settings.edit'))
                                <button wire:click="sendTestMail" wire:loading.attr="disabled"
                                    class="w-full bg-white text-slate-900 font-bold py-3 px-4 rounded-2xl border-2 border-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="sendTestMail"
                                        class="material-symbols-outlined text-sm">rocket_launch</span>
                                    <span wire:loading wire:target="sendTestMail"
                                        class="size-4 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>
                                    {{ __('Enviar Correo de Prueba') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="bg-amber-50 rounded-3xl p-6 border border-amber-200">
                        <div class="flex gap-3">
                            <span class="material-symbols-outlined text-amber-600">info</span>
                            <div>
                                <h4 class="text-sm font-bold text-amber-900">¿Problemas de conexión?
                                </h4>
                                <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                    Asegúrate que los puertos 587 (TLS) o 465 (SSL) estén abiertos en tu servidor. Para
                                    Gmail, recuerda generar un"App Password".
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>