@php
    $isActive = $provider?->is_active ?? false;
    $isDefault = $provider?->is_default ?? false;
    $credentials = $provider?->credentials ?? [];
    $config = $provider?->config ?? [];
@endphp

<div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 overflow-hidden {{ !$provider ? 'opacity-60' : '' }}">
    <!-- Header -->
    <div class="bg-gradient-to-r {{ $gradient }} p-4 flex items-center justify-between text-white">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                <i class="{{ $icon }} text-xl"></i>
            </div>
            <div>
                <h3 class="font-bold text-base">{{ $name }}</h3>
                @if($isDefault)
                    <span class="text-[10px] font-bold bg-white/30 px-2 py-0.5 rounded-full">DEFAULT</span>
                @endif
            </div>
        </div>
        @if($provider)
        <div class="flex items-center gap-2">
            <!-- Status Toggle -->
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox"
                       class="sr-only peer provider-toggle"
                       data-provider-id="{{ $provider->id }}"
                       {{ $isActive ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-white/30 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-white/50"></div>
            </label>
        </div>
        @endif
    </div>

    @if($provider)
    <form action="{{ route('admin.settings.provider.update', $provider->id) }}" method="POST" class="p-5 space-y-4">
        @csrf
        @method('PUT')

        @if(!empty($showInfo))
        <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-600 border border-slate-200">
            <i class="ri-information-line mr-1 text-slate-400"></i>
            {{ $showInfo }}
        </div>
        @endif

        @if(!empty($showEnvironment))
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Environment</label>
            <div class="flex gap-2">
                <button type="button"
                        class="flex-1 py-2 rounded-lg font-bold text-xs transition env-btn {{ ($config['environment'] ?? 'sandbox') === 'sandbox' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-500' }}"
                        data-env="sandbox"
                        onclick="setEnvironment(this, 'sandbox')">
                    <i class="ri-bug-line mr-1"></i>Sandbox
                </button>
                <button type="button"
                        class="flex-1 py-2 rounded-lg font-bold text-xs transition env-btn {{ ($config['environment'] ?? 'sandbox') === 'production' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500' }}"
                        data-env="production"
                        onclick="setEnvironment(this, 'production')">
                    <i class="ri-shield-check-line mr-1"></i>Production
                </button>
            </div>
            <input type="hidden" name="config[environment]" value="{{ $config['environment'] ?? 'sandbox' }}" class="env-input">
        </div>
        @endif

        @foreach($fields as $field)
        <div>
            <label class="text-[10px] font-bold text-slate-400 uppercase mb-1.5 block">{{ $field['label'] }}</label>
            <div class="flex items-center px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus-within:border-brand-500 focus-within:bg-white transition">
                @if($field['type'] === 'password')
                    <i class="ri-key-2-line text-slate-400 mr-2"></i>
                    <input type="password"
                           name="{{ $field['name'] }}"
                           value="{{ data_get($credentials, str_replace(['credentials[', ']'], '', $field['name']), data_get($config, str_replace(['config[', ']'], '', $field['name']), $field['default'] ?? '')) }}"
                           class="bg-transparent w-full outline-none font-mono text-slate-700 text-xs"
                           placeholder="********">
                    <button type="button" onclick="togglePasswordVisibility(this)" class="text-slate-400 hover:text-slate-600 ml-2">
                        <i class="ri-eye-line text-sm"></i>
                    </button>
                @else
                    <i class="ri-link text-slate-400 mr-2"></i>
                    <input type="text"
                           name="{{ $field['name'] }}"
                           value="{{ data_get($credentials, str_replace(['credentials[', ']'], '', $field['name']), data_get($config, str_replace(['config[', ']'], '', $field['name']), $field['default'] ?? '')) }}"
                           class="bg-transparent w-full outline-none font-mono text-slate-700 text-xs"
                           placeholder="{{ $field['label'] }}">
                @endif
            </div>
        </div>
        @endforeach

        @if(!$isDefault && count($fields) > 0)
        <div class="flex items-center gap-2 pt-2">
            <input type="checkbox"
                   name="is_default"
                   value="1"
                   id="default-{{ $provider->id }}"
                   class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            <label for="default-{{ $provider->id }}" class="text-xs font-bold text-slate-500">Set as default provider</label>
        </div>
        @endif

        <div class="flex gap-2 pt-2">
            @if(count($fields) > 0)
            <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r {{ $gradient }} text-white rounded-xl font-bold text-xs shadow-lg transition hover:shadow-xl">
                <i class="ri-save-line mr-1"></i>Simpan
            </button>
            <button type="button" onclick="testConnection({{ $provider->id }})" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition">
                <i class="ri-signal-wifi-line"></i>
            </button>
            @endif
            @if($showSync ?? false)
            <button type="button" onclick="syncProducts('{{ $provider->provider_name }}')" class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-xs transition">
                <i class="ri-refresh-line"></i>
            </button>
            @endif
        </div>
    </form>
    @else
    <div class="p-6 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
            <i class="{{ $icon }} text-3xl text-slate-300"></i>
        </div>
        <p class="text-sm font-bold text-slate-400">Provider tidak ditemukan</p>
        <p class="text-xs text-slate-400 mt-1">Jalankan seeder untuk membuat provider</p>
    </div>
    @endif
</div>

@once
@push('scripts')
<script>
function togglePasswordVisibility(btn) {
    const input = btn.parentElement.querySelector('input');
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ri-eye-off-line text-sm';
    } else {
        input.type = 'password';
        icon.className = 'ri-eye-line text-sm';
    }
}

function setEnvironment(btn, env) {
    const container = btn.closest('div');
    const buttons = container.querySelectorAll('.env-btn');
    const input = container.parentElement.querySelector('.env-input');

    buttons.forEach(b => {
        b.classList.remove('bg-amber-500', 'bg-emerald-500', 'text-white');
        b.classList.add('bg-slate-100', 'text-slate-500');
    });

    if (env === 'sandbox') {
        btn.classList.remove('bg-slate-100', 'text-slate-500');
        btn.classList.add('bg-amber-500', 'text-white');
    } else {
        btn.classList.remove('bg-slate-100', 'text-slate-500');
        btn.classList.add('bg-emerald-500', 'text-white');
    }

    input.value = env;
}

// Provider toggle handling
document.querySelectorAll('.provider-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const providerId = this.dataset.providerId;
        const isActive = this.checked;

        fetch(`/admin/settings/provider/${providerId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_active: isActive })
        })
        .then(response => response.json())
        .then(data => {
            // Optionally show toast notification
        })
        .catch(error => {
            // Revert toggle on error
            this.checked = !isActive;
            alert('Failed to update provider status');
        });
    });
});
</script>
@endpush
@endonce
