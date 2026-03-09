<?php

namespace App\Modules\Settings\Livewire;

use App\Modules\Settings\Models\Setting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

class GeneralSettings extends Component
{
    use WithFileUploads;

    public $site_name;
    public $contact_email;
    public $contact_phone;
    public $address;
    public $footer_text;
    public $theme_color;
    public $site_logo; // Path for viewing
    public $new_logo;  // File for uploading
    public $dashboard_banner; // Path for viewing
    public $new_dashboard_banner; // File for uploading

    public function mount()
    {
        abort_if(!auth()->user()->hasPermission('settings.view'), 403, 'No tienes permisos para ver configuración.');
        $this->site_name = Setting::get('site_name', 'EconoLlantas');
        $this->contact_email = Setting::get('contact_email', 'contacto@econollantas.com');
        $this->contact_phone = Setting::get('contact_phone', '');
        $this->address = Setting::get('address', '');
        $this->footer_text = Setting::get('footer_text', '© ' . date('Y') . ' EconoLlantas. Todos los derechos reservados.');
        $this->theme_color = Setting::get('theme_color', '#363d82');
        $this->site_logo = Setting::get('site_logo', 'econollantaslogo.png');
        $this->dashboard_banner = Setting::get('dashboard_banner', 'fondo-econollantas.jpeg');
    }

    public function save()
    {
        abort_if(!auth()->user()->hasPermission('settings.edit'), 403);
        Setting::set('site_name', $this->site_name);
        Setting::set('contact_email', $this->contact_email);
        Setting::set('contact_phone', $this->contact_phone);
        Setting::set('address', $this->address);
        Setting::set('footer_text', $this->footer_text);
        Setting::set('theme_color', $this->theme_color);

        if ($this->new_logo) {
            $path = $this->new_logo->store('logos', 'public');
            Setting::set('site_logo', $path);
            $this->site_logo = $path;
            $this->new_logo = null;
        }

        if ($this->new_dashboard_banner) {
            $path = $this->new_dashboard_banner->store('banners', 'public');
            Setting::set('dashboard_banner', $path);
            $this->dashboard_banner = $path;
            $this->new_dashboard_banner = null;
        }

        $this->dispatch('theme-updated', color: $this->theme_color);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Configuración guardada correctamente.'
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.settings.general');
    }
}
