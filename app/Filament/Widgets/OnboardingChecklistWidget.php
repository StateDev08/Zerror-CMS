<?php

namespace App\Filament\Widgets;

use App\Support\OnboardingChecklist;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class OnboardingChecklistWidget extends Widget
{
    protected string $view = 'filament.widgets.onboarding-checklist';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -20;

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user || (! $user->hasRole('super-admin') && ! $user->hasPermissionTo('access_admin'))) {
            return false;
        }

        return app(OnboardingChecklist::class)->shouldShow();
    }

    /**
     * @return array{steps: list<array>, done: int, total: int}
     */
    public function getChecklistData(): array
    {
        $checklist = app(OnboardingChecklist::class);

        return [
            'steps' => $checklist->steps(),
            'done' => $checklist->doneCount(),
            'total' => $checklist->totalCount(),
        ];
    }

    public function dismissOnboarding(): void
    {
        app(OnboardingChecklist::class)->dismiss();
        Notification::make()
            ->title(__('zerrocms.onboarding.dismissed'))
            ->success()
            ->send();
    }
}
