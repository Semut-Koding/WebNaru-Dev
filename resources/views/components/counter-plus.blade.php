{{-- Counter Plus Button - Reusable Blade Component --}}

<button type="button"
    onmousedown="event.preventDefault()"
    onclick="
        let input = this.closest('.fi-input-wrp').querySelector('input');
        let current = parseInt(input.value) || 0;
        input.value = current + 1;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    "
    style="touch-action: manipulation;"
    class="inline-flex items-center justify-center rounded-lg p-2 text-green-500 hover:bg-green-50 dark:hover:bg-green-500/10 transition select-none">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
        <path fill-rule="evenodd"
            d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z"
            clip-rule="evenodd" />
    </svg>
</button>