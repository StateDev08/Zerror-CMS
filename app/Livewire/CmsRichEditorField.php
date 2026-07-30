<?php

namespace App\Livewire;

use App\Filament\Forms\CmsRichEditor;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Frontend-Wrapper für denselben CmsRichEditor wie im ACP.
 *
 * @property-read Schema $form
 */
class CmsRichEditorField extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public string $name = 'body';

    public string $label = '';

    public bool $compact = true;

    /** Auf true setzen, damit Zitate aus dem Thread hier eingefügt werden. */
    public bool $listenQuotes = false;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(
        ?string $name = 'body',
        ?string $value = null,
        string $label = '',
        bool $compact = true,
        bool $listenQuotes = false,
    ): void {
        $this->name = $name ?: 'body';
        $this->label = $label;
        $this->compact = $compact;
        $this->listenQuotes = $listenQuotes;
        $this->form->fill([
            'content' => $value ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $editor = $this->compact
            ? CmsRichEditor::compact('content')
            : CmsRichEditor::make('content');

        if ($this->label !== '') {
            $editor->label($this->label);
        } else {
            $editor->hiddenLabel();
        }

        return $schema
            ->components([$editor])
            ->statePath('data');
    }

    #[On('forum-quote')]
    public function prependQuote(string $author = '', string $body = ''): void
    {
        if (! $this->listenQuotes) {
            return;
        }

        $author = trim(strip_tags($author));
        $body = trim(html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($author === '' && $body === '') {
            return;
        }

        $quote = '<blockquote><p><strong>'.e($author).'</strong></p><p>'.nl2br(e($body), false).'</p></blockquote><p></p>';
        $current = (string) ($this->data['content'] ?? '');
        $this->form->fill([
            'content' => $quote.$current,
        ]);
    }

    public function render(): View
    {
        return view('livewire.cms-rich-editor-field');
    }
}
