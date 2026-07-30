@php
    $glossaryItems = $glossaryItems ?? [
        ['term' => __('zerrocms.glossary.module_term'), 'text' => __('zerrocms.glossary.module_text')],
        ['term' => __('zerrocms.glossary.plugin_term'), 'text' => __('zerrocms.glossary.plugin_text')],
    ];
@endphp
<div class="zc-glossary" role="note">
    @foreach($glossaryItems as $item)
        <p>
            <strong>{{ $item['term'] }}</strong>
            {{ $item['text'] }}
        </p>
    @endforeach
</div>
