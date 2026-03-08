<?php

namespace App\Modules\Settings\Livewire;

use App\Modules\Settings\Models\Setting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Mail\TestMail;

class MailSettings extends Component
{
    public $mail_mailer = 'smtp';
    public $mail_host;
    public $mail_port;
    public $mail_username;
    public $mail_password;
    public $mail_encryption;
    public $mail_from_address;
    public $mail_from_name;
    public $test_email;

    public function mount()
    {
        $this->mail_mailer = Setting::get('mail_mailer', config('mail.default'));
        $this->mail_host = Setting::get('mail_host', config('mail.mailers.smtp.host'));
        $this->mail_port = Setting::get('mail_port', config('mail.mailers.smtp.port'));
        $this->mail_username = Setting::get('mail_username', config('mail.mailers.smtp.username'));
        $this->mail_password = Setting::get('mail_password', config('mail.mailers.smtp.password'));
        $this->mail_encryption = Setting::get('mail_encryption', config('mail.mailers.smtp.encryption'));
        $this->mail_from_address = Setting::get('mail_from_address', config('mail.from.address'));
        $this->mail_from_name = Setting::get('mail_from_name', config('mail.from.name'));
        $this->test_email = auth()->user()->email;
    }

    public function save()
    {
        Setting::set('mail_mailer', $this->mail_mailer, 'mail');
        Setting::set('mail_host', $this->mail_host, 'mail');
        Setting::set('mail_port', $this->mail_port, 'mail');
        Setting::set('mail_username', $this->mail_username, 'mail');
        Setting::set('mail_password', $this->mail_password, 'mail');
        Setting::set('mail_encryption', $this->mail_encryption, 'mail');
        Setting::set('mail_from_address', $this->mail_from_address, 'mail');
        Setting::set('mail_from_name', $this->mail_from_name, 'mail');

        \Illuminate\Support\Facades\Cache::forget('mail_settings');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Configuración de correo guardada.'
        ]);
    }

    public function sendTestMail()
    {
        $this->validate([
            'test_email' => 'required|email'
        ]);

        try {
            // Aplicar configuración en tiempo de ejecución para el test
            Config::set('mail.mailers.smtp.host', $this->mail_host);
            Config::set('mail.mailers.smtp.port', $this->mail_port);
            Config::set('mail.mailers.smtp.username', $this->mail_username);
            Config::set('mail.mailers.smtp.password', $this->mail_password);
            Config::set('mail.mailers.smtp.encryption', $this->mail_encryption);
            Config::set('mail.from.address', $this->mail_from_address);
            Config::set('mail.from.name', $this->mail_from_name);

            Mail::to($this->test_email)->send(new TestMail());

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Correo de prueba enviado correctamente.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Error al enviar: ' . $e->getMessage()
            ]);
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.settings.mail');
    }
}
