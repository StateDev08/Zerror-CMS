/**
 * CMS Music — globale Wiedergabe über Seitenwechsel (Turbo + sessionStorage).
 */
(function (global) {
    'use strict';

    if (global.CmsMusic && global.CmsMusic.__ready) {
        return;
    }

    var STORAGE_KEY = 'cms_music_state_v1';
    var audio = null;
    var tracks = [];
    var order = [];
    var idx = 0;
    var loopOn = true;
    var shuffleOn = false;
    var lastVol = 70;
    var seeking = false;
    var sig = '';
    var uiRoot = null;
    var bound = false;
    var saveTimer = null;
    var labels = { play: 'Play', pause: 'Pause' };

    function $(sel, root) {
        return (root || document).querySelector(sel);
    }
    function $$(sel, root) {
        return Array.prototype.slice.call((root || document).querySelectorAll(sel));
    }

    function fmt(sec) {
        if (!isFinite(sec) || sec < 0) return '0:00';
        var m = Math.floor(sec / 60);
        var s = Math.floor(sec % 60);
        return m + ':' + (s < 10 ? '0' : '') + s;
    }

    function trackSig(list) {
        return (list || []).map(function (t) { return String(t.url || ''); }).join('\n');
    }

    function ensureAudio() {
        var host = document.getElementById('cms-music-engine');
        if (!host) {
            host = document.createElement('div');
            host.id = 'cms-music-engine';
            host.hidden = true;
            host.setAttribute('data-turbo-permanent', '');
            (document.body || document.documentElement).appendChild(host);
        }
        audio = host.querySelector('audio') || document.getElementById('cms-music-audio');
        if (!audio) {
            audio = document.createElement('audio');
            audio.id = 'cms-music-audio';
            audio.preload = 'metadata';
            host.appendChild(audio);
        }
        if (!audio.__cmsMusicWired) {
            audio.__cmsMusicWired = true;
            audio.addEventListener('play', onPlayPause);
            audio.addEventListener('pause', onPlayPause);
            audio.addEventListener('timeupdate', onProgress);
            audio.addEventListener('loadedmetadata', onProgress);
            audio.addEventListener('ended', onEnded);
            audio.addEventListener('volumechange', syncMuteUi);
        }
        return audio;
    }

    function saveState() {
        if (!audio || !tracks.length) return;
        try {
            var payload = {
                sig: sig,
                idx: order[idx] || 0,
                orderIdx: idx,
                time: audio.currentTime || 0,
                playing: !audio.paused,
                volume: Math.round((audio.muted ? 0 : audio.volume) * 100),
                muted: !!audio.muted,
                lastVol: lastVol,
                loop: loopOn,
                shuffle: shuffleOn,
                order: order.slice()
            };
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
        } catch (e) {}
    }

    function scheduleSave() {
        if (saveTimer) clearTimeout(saveTimer);
        saveTimer = setTimeout(saveState, 250);
    }

    function loadState() {
        try {
            var raw = sessionStorage.getItem(STORAGE_KEY);
            if (!raw) return null;
            return JSON.parse(raw);
        } catch (e) {
            return null;
        }
    }

    function rebuildOrder(keepReal) {
        var currentReal = typeof keepReal === 'number' ? keepReal : (order[idx] || 0);
        if (shuffleOn) {
            order = tracks.map(function (_, i) { return i; });
            for (var i = order.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var tmp = order[i];
                order[i] = order[j];
                order[j] = tmp;
            }
            var pos = order.indexOf(currentReal);
            if (pos > 0) {
                order.splice(pos, 1);
                order.unshift(currentReal);
            }
            idx = Math.max(0, order.indexOf(currentReal));
        } else {
            order = tracks.map(function (_, i) { return i; });
            idx = Math.max(0, Math.min(order.length - 1, currentReal));
        }
    }

    function currentTrack() {
        var real = order[idx];
        return tracks[real] || null;
    }

    function syncToggle() {
        if (!uiRoot || !audio) return;
        var playing = !audio.paused;
        $$('[data-music-toggle]', uiRoot).forEach(function (btn) {
            var playLbl = btn.querySelector('[data-music-label-play]');
            var pauseLbl = btn.querySelector('[data-music-label-pause]');
            if (playLbl) playLbl.hidden = playing;
            if (pauseLbl) pauseLbl.hidden = !playing;
            btn.setAttribute('aria-label', playing ? labels.pause : labels.play);
        });
    }

    function syncProgress() {
        if (!uiRoot || !audio || seeking) return;
        var d = audio.duration || 0;
        var c = audio.currentTime || 0;
        var seek = $('[data-music-seek]', uiRoot);
        var curEl = $('[data-music-cur]', uiRoot);
        var durEl = $('[data-music-dur]', uiRoot);
        if (seek && d > 0) seek.value = String(Math.round((c / d) * 1000));
        if (curEl) curEl.textContent = fmt(c);
        if (durEl) durEl.textContent = fmt(d);
    }

    function syncMuteUi() {
        if (!uiRoot || !audio) return;
        var muteBtn = $('[data-music-mute]', uiRoot);
        if (muteBtn) muteBtn.textContent = audio.muted || audio.volume === 0 ? '🔇' : '🔊';
        var volInput = $('[data-music-vol]', uiRoot);
        if (volInput && !audio.muted) {
            volInput.value = String(Math.round(audio.volume * 100));
        }
    }

    function syncNowPlaying() {
        if (!uiRoot) return;
        var t = currentTrack();
        var titleEl = $('[data-music-title]', uiRoot);
        var artistEl = $('[data-music-artist]', uiRoot);
        var posEl = $('[data-music-pos]', uiRoot);
        var real = order[idx] || 0;
        if (titleEl) titleEl.textContent = (t && t.title) || '';
        if (artistEl) {
            artistEl.textContent = (t && t.artist) || '';
            artistEl.hidden = !(t && t.artist);
        }
        if (posEl) posEl.textContent = (real + 1) + ' / ' + tracks.length;
        $$('[data-music-pick]', uiRoot).forEach(function (btn) {
            btn.classList.toggle('is-active', parseInt(btn.getAttribute('data-music-pick'), 10) === real);
        });
        var loopBtn = $('[data-music-loop]', uiRoot);
        if (loopBtn) {
            loopBtn.classList.toggle('is-on', loopOn);
            loopBtn.setAttribute('aria-pressed', loopOn ? 'true' : 'false');
        }
        var shuffleBtn = $('[data-music-shuffle]', uiRoot);
        if (shuffleBtn) {
            shuffleBtn.classList.toggle('is-on', shuffleOn);
            shuffleBtn.setAttribute('aria-pressed', shuffleOn ? 'true' : 'false');
        }
        syncToggle();
        syncProgress();
        syncMuteUi();
    }

    function setTrackByOrder(orderIndex, autoPlay, seekTime) {
        if (!tracks.length) return;
        ensureAudio();
        idx = ((orderIndex % order.length) + order.length) % order.length;
        var t = currentTrack();
        if (!t) return;
        var nextSrc = t.url;
        var abs;
        try { abs = new URL(nextSrc, window.location.href).href; } catch (e) { abs = nextSrc; }
        var same = audio.src === abs;
        if (!same) {
            audio.src = nextSrc;
        }
        var applySeek = function () {
            if (typeof seekTime === 'number' && isFinite(seekTime) && seekTime > 0) {
                try { audio.currentTime = seekTime; } catch (e) {}
            }
        };
        if (audio.readyState >= 1) applySeek();
        else audio.addEventListener('loadedmetadata', function once() {
            audio.removeEventListener('loadedmetadata', once);
            applySeek();
        });
        syncNowPlaying();
        if (autoPlay) {
            audio.play().catch(function () {});
        }
        scheduleSave();
    }

    function setTrackReal(realIndex, autoPlay) {
        var pos = order.indexOf(realIndex);
        if (pos < 0) {
            rebuildOrder(realIndex);
            pos = order.indexOf(realIndex);
            if (pos < 0) pos = 0;
        }
        setTrackByOrder(pos, autoPlay);
    }

    function onPlayPause() {
        syncToggle();
        scheduleSave();
    }
    function onProgress() {
        syncProgress();
        scheduleSave();
    }
    function onEnded() {
        if (tracks.length > 1) {
            if (idx < order.length - 1 || loopOn) setTrackByOrder(idx + 1, true);
        } else if (loopOn) {
            audio.currentTime = 0;
            audio.play().catch(function () {});
        }
        scheduleSave();
    }

    function unbindUi() {
        if (!uiRoot || !bound) {
            uiRoot = null;
            bound = false;
            return;
        }
        // Clone-replace removes listeners without tracking each one
        bound = false;
        uiRoot = null;
    }

    function bindUi(root) {
        if (!root) return;
        unbindUi();
        uiRoot = root;
        bound = true;
        ensureAudio();

        labels.play = root.getAttribute('data-label-play') || labels.play;
        labels.pause = root.getAttribute('data-label-pause') || labels.pause;

        $$('[data-music-toggle]', root).forEach(function (btn) {
            btn.addEventListener('click', function () {
                ensureAudio();
                if (audio.paused) audio.play().catch(function () {});
                else audio.pause();
            });
        });

        var seek = $('[data-music-seek]', root);
        if (seek) {
            seek.addEventListener('pointerdown', function () { seeking = true; });
            seek.addEventListener('pointerup', function () { seeking = false; });
            seek.addEventListener('change', function () {
                var d = audio.duration || 0;
                if (d > 0) audio.currentTime = (parseInt(seek.value, 10) / 1000) * d;
                seeking = false;
                syncProgress();
                scheduleSave();
            });
            seek.addEventListener('input', function () {
                var d = audio.duration || 0;
                var curEl = $('[data-music-cur]', root);
                if (d > 0 && curEl) curEl.textContent = fmt((parseInt(seek.value, 10) / 1000) * d);
            });
        }

        var volInput = $('[data-music-vol]', root);
        if (volInput) {
            volInput.addEventListener('input', function () {
                var v = Math.max(0, Math.min(100, parseInt(volInput.value, 10) || 0));
                audio.muted = false;
                audio.volume = v / 100;
                if (v > 0) lastVol = v;
                syncMuteUi();
                scheduleSave();
            });
        }

        var muteBtn = $('[data-music-mute]', root);
        if (muteBtn) {
            muteBtn.addEventListener('click', function () {
                if (audio.muted || audio.volume === 0) {
                    audio.muted = false;
                    audio.volume = (lastVol || 70) / 100;
                    if (volInput) volInput.value = String(lastVol || 70);
                } else {
                    lastVol = Math.round(audio.volume * 100) || lastVol;
                    audio.muted = true;
                }
                syncMuteUi();
                scheduleSave();
            });
        }

        var loopBtn = $('[data-music-loop]', root);
        if (loopBtn) {
            loopBtn.addEventListener('click', function () {
                loopOn = !loopOn;
                syncNowPlaying();
                scheduleSave();
            });
        }

        var shuffleBtn = $('[data-music-shuffle]', root);
        if (shuffleBtn) {
            shuffleBtn.addEventListener('click', function () {
                shuffleOn = !shuffleOn;
                rebuildOrder(order[idx]);
                syncNowPlaying();
                scheduleSave();
            });
        }

        var prev = $('[data-music-prev]', root);
        var next = $('[data-music-next]', root);
        if (prev) prev.addEventListener('click', function () { setTrackByOrder(idx - 1, true); });
        if (next) next.addEventListener('click', function () { setTrackByOrder(idx + 1, true); });

        $$('[data-music-pick]', root).forEach(function (btn) {
            btn.addEventListener('click', function () {
                setTrackReal(parseInt(btn.getAttribute('data-music-pick'), 10) || 0, true);
            });
        });

        syncNowPlaying();
    }

    function configure(opts) {
        opts = opts || {};
        ensureAudio();
        var list = Array.isArray(opts.tracks) ? opts.tracks : [];
        if (!list.length) return;

        var nextSig = trackSig(list);
        var samePlaylist = nextSig === sig && tracks.length === list.length;
        var wasPlaying = audio && !audio.paused;
        var saved = loadState();

        tracks = list;
        sig = nextSig;

        if (typeof opts.loop === 'boolean') loopOn = opts.loop;
        if (typeof opts.volume === 'number' && !samePlaylist && !(saved && saved.sig === sig)) {
            lastVol = Math.max(0, Math.min(100, opts.volume));
            audio.volume = lastVol / 100;
        }

        if (!samePlaylist) {
            order = tracks.map(function (_, i) { return i; });
            idx = 0;
            shuffleOn = false;

            if (saved && saved.sig === sig) {
                if (Array.isArray(saved.order) && saved.order.length === tracks.length) {
                    order = saved.order.slice();
                }
                shuffleOn = !!saved.shuffle;
                loopOn = typeof saved.loop === 'boolean' ? saved.loop : loopOn;
                lastVol = saved.lastVol || saved.volume || lastVol;
                audio.volume = Math.max(0, Math.min(100, saved.volume != null ? saved.volume : lastVol)) / 100;
                audio.muted = !!saved.muted;
                var real = typeof saved.idx === 'number' ? saved.idx : 0;
                var pos = order.indexOf(real);
                if (pos < 0) pos = 0;
                idx = pos;
                setTrackByOrder(idx, !!saved.playing, saved.time || 0);
                return;
            }

            setTrackByOrder(0, !!opts.autoplay && !wasPlaying);
            if (wasPlaying) audio.play().catch(function () {});
            return;
        }

        // Gleiche Playlist: nur UI sync / Autoplay falls idle
        if (opts.autoplay && audio.paused && !(saved && saved.playing === false)) {
            audio.play().catch(function () {});
        }
        syncNowPlaying();
    }

    function mountFromUi(root) {
        if (!root || root.getAttribute('data-music-bound') === '1') return;
        root.setAttribute('data-music-bound', '1');

        var list = [];
        try { list = JSON.parse(root.getAttribute('data-tracks') || '[]'); } catch (e) { list = []; }
        var vol = parseInt(root.getAttribute('data-volume') || '70', 10);
        if (isNaN(vol)) vol = 70;

        configure({
            tracks: list,
            volume: vol,
            loop: root.getAttribute('data-loop') === '1',
            autoplay: root.getAttribute('data-autoplay') === '1'
        });
        bindUi(root);
    }

    function scan() {
        $$('[data-music-player] .cms-music[data-tracks]').forEach(mountFromUi);
        // Falls data-music-bound auf ausgetauschtem DOM hängt: frische Roots ohne Flag
        $$('.cms-music[data-tracks]:not([data-music-bound])').forEach(mountFromUi);
    }

    function bootTurboHelpers() {
        if (global.__cmsMusicTurboHelpers) return;
        global.__cmsMusicTurboHelpers = true;

        function disableFormTurbo(root) {
            var scope = root || document;
            Array.prototype.forEach.call(scope.querySelectorAll('form'), function (form) {
                if (!form.hasAttribute('data-turbo')) form.setAttribute('data-turbo', 'false');
            });
            Array.prototype.forEach.call(scope.querySelectorAll('a[download], a[href*="/admin"]'), function (a) {
                a.setAttribute('data-turbo', 'false');
            });
            Array.prototype.forEach.call(scope.querySelectorAll('a[href*="logout"], button[formaction*="logout"]'), function (a) {
                a.setAttribute('data-turbo', 'false');
            });
        }

        document.addEventListener('turbo:load', function () {
            Array.prototype.forEach.call(document.querySelectorAll('.cms-music[data-tracks]'), function (el) {
                el.removeAttribute('data-music-bound');
            });
            uiRoot = null;
            bound = false;
            disableFormTurbo(document);
            scan();
        });

        document.addEventListener('turbo:before-cache', function () {
            saveState();
        });

        disableFormTurbo(document);
    }

    global.CmsMusic = {
        __ready: true,
        configure: configure,
        bindUi: bindUi,
        scan: scan,
        saveState: saveState,
        play: function () { ensureAudio(); return audio.play(); },
        pause: function () { if (audio) audio.pause(); },
        isPlaying: function () { return !!(audio && !audio.paused); }
    };

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') saveState();
    });
    window.addEventListener('pagehide', saveState);

    // Turbo-Hooks sofort, Scan sobald DOM da ist
    bootTurboHelpers();
    ensureAudio();

    function start() {
        scan();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})(window);
