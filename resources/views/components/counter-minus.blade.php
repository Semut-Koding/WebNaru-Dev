{{-- Counter Minus Button - Reusable Blade Component --}}
@props(['min' => 0])

<button type="button"
    onmousedown="event.preventDefault()"
    ontouchstart="event.preventDefault()"
    onclick="
        let input = this.closest('.fi-input-wrp').querySelector('input');
        let current = parseInt(input.value) || 0;
        let newVal = Math.max({{ $min }}, current - 1);
        input.value = newVal;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    "
    ontouchend="
        event.preventDefault();
        let input = this.closest('.fi-input-wrp').querySelector('input');
        let current = parseInt(input.value) || 0;
        let newVal = Math.max({{ $min }}, current - 1);
        input.value = newVal;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    "
    style="touch-action: manipulation;"
    class="inline-flex items-center justify-center rounded-lg p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition select-none">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
        <path fill-rule="evenodd" d="M4.25 12a.75.75 0 0 1 .75-.75h14a.75.75 0 0 1 0 1.5H5a.75.75 0 0 1-.75-.75Z"
            clip-rule="evenodd" />
    </svg>
</button>