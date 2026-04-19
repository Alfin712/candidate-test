@php
    $toastSuccess = session('success') ?? session('status');
    $toastError = session('error');
@endphp

@if ($toastSuccess || $toastError)
    <div
        x-data="{
            visible: true,
            type: @js($toastError ? 'error' : 'success'),
            message: @js($toastError ?: $toastSuccess),
            init() {
                setTimeout(() => { this.visible = false; }, 4500);
            }
        }"
        x-show="visible"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        role="status"
        aria-live="polite"
        class="fixed top-6 right-6 z-50 max-w-sm w-[calc(100%-3rem)] sm:w-auto rounded-md shadow-lg border px-4 py-3 text-sm flex items-start gap-3"
        :class="type === 'error'
            ? 'bg-red-50 border-red-200 text-red-800'
            : 'bg-emerald-50 border-emerald-200 text-emerald-800'"
    >
        <svg x-show="type === 'success'" class="w-5 h-5 flex-shrink-0 mt-0.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <svg x-show="type === 'error'" class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div class="flex-1 font-medium" x-text="message"></div>
        <button type="button" @click="visible = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0" aria-label="Dismiss">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
@endif
