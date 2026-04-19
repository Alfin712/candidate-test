@props(['items' => []])

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex items-center flex-wrap gap-1 text-sm text-gray-500">
        @foreach ($items as $i => $item)
            @php
                $isLast = $i === count($items) - 1;
                $label = $item['label'] ?? '';
                $url = $item['url'] ?? null;
            @endphp
            <li class="flex items-center gap-1">
                @if ($url && !$isLast)
                    <a href="{{ $url }}" class="hover:text-emerald-700 focus:outline-none focus:text-emerald-700">{{ $label }}</a>
                @else
                    <span class="text-gray-900 font-medium" @if($isLast) aria-current="page" @endif>{{ $label }}</span>
                @endif
                @unless ($isLast)
                    <span class="text-gray-300" aria-hidden="true">&rsaquo;</span>
                @endunless
            </li>
        @endforeach
    </ol>
</nav>
