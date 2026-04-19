@props([
    'action',
    'title' => 'Delete item',
    'message' => 'Are you sure you want to delete this item? This action cannot be undone.',
    'confirmLabel' => 'Delete',
    'triggerClass' => 'text-xs text-red-600 hover:text-red-800 px-2 py-1 rounded hover:bg-red-50',
    'triggerLabel' => 'Delete',
])

<div x-data="{ open: false }" class="inline-block">
    <button type="button"
            @click="open = true"
            class="{{ $triggerClass }}"
            aria-haspopup="dialog">
        {{ $triggerLabel }}
    </button>

    <template x-teleport="body">
        <div x-show="open"
             x-cloak
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center px-4"
             role="dialog"
             aria-modal="true"
             aria-labelledby="delete-title-{{ md5($action) }}"
             @keydown.escape.window="open = false">
            <div class="absolute inset-0 bg-gray-900/50" @click="open = false" aria-hidden="true"></div>

            <div class="relative bg-white w-full max-w-md rounded-lg shadow-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100 flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 id="delete-title-{{ md5($action) }}" class="font-semibold text-gray-900">{{ $title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $message }}</p>
                    </div>
                </div>
                <div class="px-5 py-3 bg-gray-50 flex items-center justify-end gap-2 rounded-b-lg">
                    <button type="button" @click="open = false"
                            class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 min-h-[36px]">
                        Cancel
                    </button>
                    <form method="POST" action="{{ $action }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="px-3 py-2 bg-rose-600 text-white text-sm font-medium rounded-md hover:bg-rose-700 min-h-[36px]">
                            {{ $confirmLabel }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
