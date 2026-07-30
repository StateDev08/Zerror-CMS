{{-- Persistente Musik-Engine: läuft über interne Seitenwechsel weiter (Turbo). --}}
@if(module_enabled('music_player'))
    <div id="cms-music-persist" data-turbo-permanent>
        <div id="cms-music-engine" hidden>
            <audio id="cms-music-audio" preload="metadata"></audio>
        </div>
        <script src="{{ asset('js/cms-music.js') }}?v=2"></script>
        <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.13/dist/turbo.es2017-umd.js" defer></script>
    </div>
@endif
