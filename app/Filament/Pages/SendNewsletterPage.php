<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\ChecksCmsPagePermission;
use App\Filament\Forms\CmsRichEditor;
use App\Mail\NewsletterBroadcastMail;
use App\Models\NewsletterSubscriber;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @property-read Schema $form
 */
class SendNewsletterPage extends Page
{
    use ChecksCmsPagePermission;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationLabel = 'Newsletter senden';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';

    protected static ?string $title = 'Newsletter senden';

    protected static ?int $navigationSort = 85;

    protected string $view = 'filament.pages.send-newsletter';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    protected static function cmsPagePermission(): string
    {
        return 'send_newsletter';
    }

    public function mount(): void
    {
        $this->form->fill([
            'subject' => '',
            'body' => '',
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('subject')
                ->label(__('newsletter.broadcast_subject'))
                ->required()
                ->maxLength(255),
            CmsRichEditor::make('body')
                ->label(__('newsletter.broadcast_body'))
                ->required(),
        ]);
    }

    public function send(): void
    {
        $data = $this->form->getState();

        $count = 0;

        NewsletterSubscriber::query()->orderBy('id')->chunkById(50, function ($subscribers) use ($data, &$count) {
            foreach ($subscribers as $subscriber) {
                $token = $subscriber->token ?: Str::random(32);
                if (! $subscriber->token) {
                    $subscriber->update(['token' => $token]);
                }

                Mail::to($subscriber->email)->send(new NewsletterBroadcastMail(
                    $data['subject'],
                    $data['body'],
                    route('newsletter.unsubscribe', ['token' => $token]),
                ));
                $count++;
            }
        });

        Notification::make()
            ->title(__('newsletter.broadcast_sent', ['count' => $count]))
            ->success()
            ->send();

        $this->form->fill([
            'subject' => '',
            'body' => '',
        ]);
    }
}
