<?php

namespace App\Filament\Pages;

use App\Support\PackageInstaller;
use App\Support\UploadLimits;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Zentraler ZIP-Installer für Module, Plugins, Widgets und Themes.
 */
class PackagesInstallerPage extends Page
{
    use \App\Filament\Concerns\ChecksCmsPagePermission;
    use WithFileUploads;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $navigationLabel = 'Paket-Installer';

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    protected static ?string $title = 'Paket-Installer';

    protected static ?int $navigationSort = 4;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.packages-installer';

    /** @var TemporaryUploadedFile|null */
    public $packageZip = null;

    public string $packageType = 'auto';

    public bool $overwritePackage = false;

    public bool $enableAfterInstall = true;

    protected static function cmsPagePermission(): string
    {
        return 'manage_modules';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('super-admin')) {
            return true;
        }
        if (! $user->hasPermissionTo('access_admin')) {
            return false;
        }

        return $user->hasPermissionTo('manage_modules')
            || $user->hasPermissionTo('manage_plugins')
            || $user->hasPermissionTo('manage_themes')
            || $user->hasPermissionTo('manage_system_modules');
    }

    public function getViewData(): array
    {
        return [
            'uploadMaxMb' => (int) ceil(min(UploadLimits::fileKb(), (int) floor(PackageInstaller::MAX_ZIP_BYTES / 1024)) / 1024),
            'types' => [
                'auto' => __('zerrocms.packages.type_auto'),
                PackageInstaller::TYPE_MODULE => __('zerrocms.packages.type_module'),
                PackageInstaller::TYPE_PLUGIN => __('zerrocms.packages.type_plugin'),
                PackageInstaller::TYPE_WIDGET => __('zerrocms.packages.type_widget'),
                PackageInstaller::TYPE_THEME => __('zerrocms.packages.type_theme'),
                PackageInstaller::TYPE_SYSTEM_MODULE => __('zerrocms.packages.type_system_module'),
            ],
            'examples' => [
                PackageInstaller::TYPE_MODULE => is_file(resource_path('module-skeletons/example/module.json')),
                PackageInstaller::TYPE_PLUGIN => is_file(resource_path('plugin-skeletons/example/plugin.json')),
                PackageInstaller::TYPE_WIDGET => is_file(resource_path('widget-skeletons/example/widget.json')),
                PackageInstaller::TYPE_THEME => is_file(resource_path('theme-skeletons/example/theme.json')),
                PackageInstaller::TYPE_SYSTEM_MODULE => is_file(resource_path('system-module-skeletons/example/system-module.json')),
            ],
            'modulesUrl' => ModulesPage::getUrl(),
            'pluginsUrl' => PluginsPage::getUrl(),
            'systemModulesUrl' => SystemModulesPage::getUrl(),
            'themesUrl' => ThemesPage::getUrl(),
            'widgetsUrl' => \App\Filament\Resources\WidgetInstanceResource::getUrl('index'),
        ];
    }

    public function installPackageZip(): void
    {
        $maxKb = min(UploadLimits::fileKb(), (int) floor(PackageInstaller::MAX_ZIP_BYTES / 1024));
        $this->validate([
            'packageZip' => ['required', 'file', 'mimes:zip', 'max:'.$maxKb],
            'packageType' => ['required', 'in:auto,module,plugin,widget,theme'],
            'overwritePackage' => ['boolean'],
            'enableAfterInstall' => ['boolean'],
        ], [
            'packageZip.required' => __('zerrocms.packages.zip_required'),
            'packageZip.mimes' => __('zerrocms.packages.zip_type_invalid'),
            'packageZip.max' => __('zerrocms.packages.zip_too_large', ['mb' => (int) ceil($maxKb / 1024)]),
        ]);

        /** @var TemporaryUploadedFile $file */
        $file = $this->packageZip;
        $type = $this->packageType === 'auto' ? null : $this->packageType;

        $result = app(PackageInstaller::class)->installFromUpload(
            $file,
            $type,
            $this->overwritePackage,
            $this->enableAfterInstall
        );

        $this->packageZip = null;
        $this->overwritePackage = false;
        $this->enableAfterInstall = true;
        $this->resetValidation();

        if (! ($result['ok'] ?? false)) {
            Notification::make()
                ->title(__('zerrocms.packages.install_failed'))
                ->body($result['message'] ?? '')
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        $body = $result['message'] ?? '';
        if (! empty($result['type'])) {
            $body .= ' ('.__('zerrocms.packages.type_'.$result['type']).')';
        }

        Notification::make()
            ->title(__('zerrocms.packages.install_ok'))
            ->body($body)
            ->success()
            ->send();
    }

    public function downloadExample(string $type): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        if (! in_array($type, PackageInstaller::TYPES, true)) {
            return redirect()->back();
        }

        $built = app(PackageInstaller::class)->buildExampleZip($type);
        if (! ($built['ok'] ?? false) || empty($built['path'])) {
            Notification::make()
                ->title(__('zerrocms.packages.example_failed'))
                ->body($built['message'] ?? '')
                ->danger()
                ->send();

            return redirect()->back();
        }

        return response()
            ->download($built['path'], $built['filename'] ?? 'zerrocms-example-'.$type.'.zip')
            ->deleteFileAfterSend(true);
    }
}
