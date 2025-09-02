@php
    $personalize = $classes();
    $hasTransObj       = isset($transition) && (is_object($transition) || is_array($transition));
    $transitionIsEmpty = empty($transition) || ($hasTransObj && method_exists($transition, 'isEmpty') && $transition->isEmpty());
    $transitionHasAny  = !empty($transition) && (!$hasTransObj || (method_exists($transition, 'isNotEmpty') ? $transition->isNotEmpty() : true));
@endphp

<div x-show="{{ $attributes->get('x-show', 'show') }}"
     x-cloak
     x-on:click.outside="{{ $attributes->get('x-show', 'show') }} = false"
     x-on:keydown.escape.window="{{ $attributes->get('x-show', 'show') }} = false"
     x-intersect:leave="{{ $attributes->get('x-show', 'show') }} = false"
     {{ $anchor() }}="{{ $attributes->get('x-anchor', '$refs.anchor') }}"
     {{ $attributes->whereStartsWith('x-on') }}

     @if (
        method_exists($attributes, 'isEmpty')
        && count($attributes->whereStartsWith('x-transition')->getAttributes()) === 0
        && $transitionIsEmpty
     )
         x-transition:enter="transition duration-100 ease-out"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
     @elseif ($transitionHasAny)
         {{ $transition }}
     @else
         {!! $attributes->except(['x-show', 'x-anchor', 'class']) !!}
     @endif

     {{ $attributes->except(['floating', 'x-anchor'])->merge(['class' => $attributes->get('floating', $personalize['wrapper'])]) }}>
    {{-- override check: floating override ACTIVE --}}
    {{ $slot }}
    {{ $footer }}
</div>
