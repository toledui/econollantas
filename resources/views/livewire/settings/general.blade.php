<div class="flex-1 overflow-y-auto p-8 relative">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Configuración del Sistema
            </h1>
            <p class="text-slate-500 mt-1">Personaliza los parámetros generales de la plataforma
                EconoLlantas.</p>
        </div>

        <form wire:submit="save" class="space-y-6">
            <!-- General Info Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden transition-colors">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-900 flex items-center">
                        <span class="material-symbols-outlined mr-2 text-primary">settings</span>
                        Información General
                    </h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="site_name" value="Nombre del Sitio / Empresa" />
                            <x-text-input id="site_name" type="text" wire:model="site_name" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('site_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_email" value="Correo de Contacto" />
                            <x-text-input id="contact_email" type="email" wire:model="contact_email"
                                class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_phone" value="Teléfono de Contacto" />
                            <x-text-input id="contact_phone" type="text" wire:model="contact_phone"
                                class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="address" value="Dirección Corporativa" />
                            <textarea id="address" wire:model="address"
                                class="mt-1 block w-full border-slate-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm transition-all"
                                rows="3"></textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Branding & Theme Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden transition-colors">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-900 flex items-center">
                        <span class="material-symbols-outlined mr-2 text-primary">brush</span>
                        Identidad y Colores
                    </h3>
                </div>
                <div class="p-8 space-y-6">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-8 ring-1 ring-slate-200 p-6 rounded-2xl bg-slate-50/30">
                        <div>
                            <x-input-label value="Logo del Sistema" />
                            <div class="mt-4 flex items-center gap-6">
                                <div
                                    class="size-24 rounded-2xl bg-white border border-slate-200 flex items-center justify-center p-4 shadow-inner overflow-hidden relative group">
                                    @if($new_logo)
                                        <img src="{{ $new_logo->temporaryUrl() }}"
                                            class="max-h-full max-w-full object-contain scale-110 group-hover:scale-125 transition-transform duration-500">
                                    @else
                                        <img src="{{ asset('storage/' . $site_logo) }}"
                                            class="max-h-full max-w-full object-contain group-hover:scale-110 transition-transform duration-500">
                                    @endif

                                    <div wire:loading wire:target="new_logo"
                                        class="absolute inset-0 bg-white/80 flex items-center justify-center">
                                        <span class="material-symbols-outlined animate-spin text-primary">sync</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label class="cursor-pointer group">
                                        <span
                                            class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-700 hover:border-primary transition-all inline-flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">cloud_upload</span>
                                            Cambiar Logo
                                        </span>
                                        <input type="file" wire:model="new_logo" class="hidden" accept="image/*">
                                    </label>
                                    <p class="mt-2 text-[10px] text-slate-500">Recomendado: PNG fondo transparente, min
                                        512x512px.</p>
                                    <x-input-error :messages="$errors->get('new_logo')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="theme_color" value="Color Principal (Marca)" />
                            <div class="flex items-center gap-4 mt-4">
                                <input type="color" id="theme_color" wire:model="theme_color"
                                    class="size-12 rounded-xl cursor-pointer border-2 border-slate-200 bg-transparent overflow-hidden">
                                <x-text-input type="text" wire:model="theme_color"
                                    class="flex-1 uppercase font-mono text-sm" placeholder="#363D82" />
                            </div>
                            <p class="mt-3 text-[10px] text-slate-500">Este color se aplicará dinámicamente a botones y
                                elementos destacados.</p>
                            <x-input-error :messages="$errors->get('theme_color')" class="mt-2" />
                        </div>
                    </div>

                    <div>

                        <div>
                            <x-input-label for="footer_text" value="Texto del Pie de Página" />
                            <x-text-input id="footer_text" type="text" wire:model="footer_text"
                                class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('footer_text')" class="mt-2" />
                            <p class="mt-2 text-xs text-slate-500 italic">Este texto aparecerá en la parte inferior de
                                todas las páginas, reportes y correos.</p>
                        </div>
                    </div>
                </div>

                @if(Auth::user()->hasPermission('settings.edit'))
                    <!-- Save Button -->
                    <div class="flex justify-end pt-4">
                        <x-primary-button class="!rounded-2xl !px-12 !py-4 shadow-xl shadow-primary/20">
                            <span class="material-symbols-outlined mr-2">save</span>
                            Guardar Configuración
                        </x-primary-button>
                    </div>
                @endif
        </form>
    </div>
</div>