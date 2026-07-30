<x-filament-panels::page>
    <div class="zc-page">
        <div class="zc-help">
            <p>Sende eine E-Mail an alle Newsletter-Abonnenten.</p>
            <p class="zc-help-tip">Betreff und Inhalt mit dem Editor formatieren — der Versand geht an die hinterlegte Abonnentenliste.</p>
        </div>

        <form wire:submit="send" class="zc-page">
            {{ $this->form }}

            <div class="zc-actions">
                <x-filament::button type="submit">
                    {{ __('newsletter.broadcast_send') }}
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
