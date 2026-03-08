@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @php
                $siteLogo = \App\Modules\Settings\Models\Setting::get('site_logo', 'econollantaslogo.png');
                $logoUrl = asset('storage/' . $siteLogo);
            @endphp
            <img src="{{ $logoUrl }}" class="logo" alt="{{ config('app.name', 'EconoLlantas') }}"
                style="max-height: 50px; width: auto;">
        </a>
    </td>
</tr>