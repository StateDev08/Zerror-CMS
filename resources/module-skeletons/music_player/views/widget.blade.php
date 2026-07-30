<div class="cms-widget cms-widget--music" data-music-player>
    <header class="cms-widget__head">
        <h3 class="cms-widget__title">{{ $title }}</h3>
    </header>

    @if(count($tracks) === 0)
        <p class="cms-widget__hint">{{ __('widgets.music_player_empty') }}</p>
    @else
        <div
            class="cms-music"
            data-volume="{{ $volume }}"
            data-autoplay="{{ $autoplay ? '1' : '0' }}"
            data-loop="{{ $playlistLoop ? '1' : '0' }}"
            data-tracks='@json($tracks)'
            data-label-play="{{ __('widgets.music_player_play') }}"
            data-label-pause="{{ __('widgets.music_player_pause') }}"
        >
            <div class="cms-music__now">
                <p class="cms-music__now-title" data-music-title>{{ $tracks[0]['title'] }}</p>
                <p class="cms-music__now-artist" data-music-artist>{{ $tracks[0]['artist'] ?? '' }}</p>
            </div>

            <div class="cms-music__transport">
                <button type="button" class="cms-music__btn" data-music-prev title="{{ __('widgets.music_player_prev') }}" aria-label="{{ __('widgets.music_player_prev') }}">⏮</button>
                <button type="button" class="cms-music__btn cms-music__btn--main" data-music-toggle aria-label="{{ __('widgets.music_player_play') }}">
                    <span data-music-label-play>▶</span>
                    <span data-music-label-pause hidden>❚❚</span>
                </button>
                <button type="button" class="cms-music__btn" data-music-next title="{{ __('widgets.music_player_next') }}" aria-label="{{ __('widgets.music_player_next') }}">⏭</button>
                <button type="button" class="cms-music__btn{{ $playlistLoop ? ' is-on' : '' }}" data-music-loop title="{{ __('widgets.music_player_loop') }}" aria-pressed="{{ $playlistLoop ? 'true' : 'false' }}">↻</button>
                <button type="button" class="cms-music__btn" data-music-shuffle title="{{ __('widgets.music_player_shuffle') }}" aria-pressed="false">⇄</button>
            </div>

            <div class="cms-music__progress">
                <span class="cms-music__time" data-music-cur>0:00</span>
                <input type="range" class="cms-music__seek" data-music-seek min="0" max="1000" value="0" step="1" aria-label="Seek">
                <span class="cms-music__time" data-music-dur>0:00</span>
            </div>

            <div class="cms-music__volume">
                <button type="button" class="cms-music__btn cms-music__btn--sm" data-music-mute aria-label="Mute">🔊</button>
                <input type="range" class="cms-music__vol" data-music-vol min="0" max="100" value="{{ $volume }}" step="1" aria-label="Volume">
                <span class="cms-music__meta" data-music-pos>1 / {{ count($tracks) }}</span>
            </div>

            @if(count($tracks) > 1)
                <ul class="cms-music__list" role="list">
                    @foreach($tracks as $i => $track)
                        <li>
                            <button type="button" class="cms-music__list-item{{ $i === 0 ? ' is-active' : '' }}" data-music-pick="{{ $i }}">
                                <span class="cms-music__list-num">{{ $i + 1 }}</span>
                                <span class="cms-music__list-body">
                                    <span class="cms-music__list-title">{{ $track['title'] }}</span>
                                    @if(($track['artist'] ?? '') !== '')
                                        <span class="cms-music__list-artist">{{ $track['artist'] }}</span>
                                    @endif
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <script>
            (function () {
                function bind() {
                    if (window.CmsMusic && typeof window.CmsMusic.scan === 'function') {
                        window.CmsMusic.scan();
                        return true;
                    }
                    return false;
                }
                if (!bind()) {
                    var n = 0;
                    var t = setInterval(function () {
                        n += 1;
                        if (bind() || n > 40) clearInterval(t);
                    }, 50);
                }
            })();
        </script>
    @endif
</div>
