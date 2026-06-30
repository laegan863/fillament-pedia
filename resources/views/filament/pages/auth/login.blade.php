<x-filament-panels::page.simple>
    <div class="custom-login-shell">
        <section class="custom-login-brand-panel" aria-label="HBCR PEDIA registry access">
            <div class="custom-login-brand">
                <span class="custom-login-brand-mark">
                    <img
                        src="{{ asset('bg.png') }}"
                        alt="Hospital registry seal"
                    >
                </span>

                <div>
                    <p class="custom-login-kicker">HBCR PEDIA</p>
                    <h1>HOSPITAL-BASED CANCER REGISTRY</h1>
                    <p>
                        Secure workspace for registry, classification, diagnosis, and follow-up records.
                    </p>
                </div>
            </div>

            <div class="custom-login-highlights" aria-label="Registry workflow highlights">
                <div class="custom-login-highlight">
                    <span>Registry</span>
                    <strong>Patient demographics</strong>
                </div>

                <div class="custom-login-highlight">
                    <span>Clinical</span>
                    <strong>Diagnosis records</strong>
                </div>

                <div class="custom-login-highlight">
                    <span>Follow-up</span>
                    <strong>Treatment tracking</strong>
                </div>
            </div>
        </section>

        <section class="custom-login-form-panel" aria-labelledby="custom-login-heading">
            <div class="custom-login-heading">
                <img
                    src="{{ asset('bg.png') }}"
                    alt="Hospital registry seal"
                    class="custom-login-logo"
                >

                <p class="custom-login-eyebrow">Secure administrator access</p>
                <h2 id="custom-login-heading">Welcome back</h2>
                <p>Sign in to continue managing registry records.</p>
            </div>

            <form wire:submit="authenticate" class="custom-login-form">
                {{ $this->form }}

                <x-filament::button type="submit" class="w-full custom-login-button">
                    Sign in
                </x-filament::button>
            </form>
        </section>
    </div>
</x-filament-panels::page.simple>
