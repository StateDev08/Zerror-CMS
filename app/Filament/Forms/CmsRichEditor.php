<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\RichEditor;

/**
 * Einheitliche, professionelle Rich-Editoren fürs CMS.
 */
class CmsRichEditor
{
    /**
     * Voller Editor für Artikel, News, Regeln, Ankündigungen usw.
     */
    public static function make(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link', 'textColor'],
                ['h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                ['table', 'attachFiles'],
                ['undo', 'redo'],
            ])
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsDirectory('editor')
            ->extraAttributes(['class' => 'zc-rich-editor zc-rich-editor--full'])
            ->columnSpanFull();
    }

    /**
     * Kompakter Editor für Kurztexte / Beschreibungen.
     */
    public static function compact(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'link'],
                ['h3', 'bulletList', 'orderedList'],
                ['undo', 'redo'],
            ])
            ->fileAttachments(false)
            ->extraAttributes(['class' => 'zc-rich-editor zc-rich-editor--compact'])
            ->columnSpanFull();
    }
}
