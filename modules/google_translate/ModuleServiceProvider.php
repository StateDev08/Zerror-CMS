<?php

use App\Support\PluginManager;
use Illuminate\Support\ServiceProvider;

if (! class_exists('ZerroGoogleTranslateModuleServiceProvider', false)) {
    class ZerroGoogleTranslateModuleServiceProvider extends ServiceProvider
    {
        public function register(): void {}

        public function boot(): void
        {
            $views = base_path('modules/google_translate/views');
            if (is_dir($views)) {
                $this->loadViewsFrom($views, 'mod_google_translate');
                view()->addNamespace('mod_google_translate', $views);
            }

            app(PluginManager::class)->registerHeadHtml(function () {
                if (request()->is('admin', 'admin/*')) {
                    return '';
                }

                $href = e(asset('css/google-translate-nav.css').'?v=8');

                return '<link rel="stylesheet" href="'.$href.'">';
            });

            app(PluginManager::class)->registerBodyStartHtml(function () {
                if (request()->is('admin', 'admin/*')) {
                    return '';
                }

                $cfg = module_config('google_translate');
                $pageLang = preg_replace('/[^a-zA-Z\-]/', '', (string) ($cfg['page_language'] ?? 'de')) ?: 'de';
                $included = trim((string) ($cfg['included_languages'] ?? ''));
                $includedJs = $included !== ''
                    ? json_encode(preg_replace('/\s+/', '', $included))
                    : 'undefined';

                return '<script>'
                    .'window.zerroGTranslate={pageLanguage:'.json_encode($pageLang).',includedLanguages:'.$includedJs.'};'
                    .'function googleTranslateElementInit(){'
                    .'if(!window.google||!google.translate)return;'
                    .'var el=document.getElementById("google_translate_element");'
                    .'if(!el){el=document.createElement("div");el.id="google_translate_element";el.className="gt-nav__engine";el.setAttribute("aria-hidden","true");document.body.appendChild(el);}'
                    .'var o={pageLanguage:window.zerroGTranslate.pageLanguage,autoDisplay:false,'
                    .'layout:google.translate.TranslateElement.InlineLayout.SIMPLE};'
                    .'if(window.zerroGTranslate.includedLanguages){o.includedLanguages=window.zerroGTranslate.includedLanguages;}'
                    .'new google.translate.TranslateElement(o,"google_translate_element");'
                    .'}'
                    .'</script>'
                    .'<script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" defer></script>';
            });
        }
    }
}

return ZerroGoogleTranslateModuleServiceProvider::class;
