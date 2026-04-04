<div x-data="charCounter({{ $max }})"
     x-init="
        const input = $el.querySelector('input, textarea');
        if(input){
            update(input);
            input.addEventListener('input', () => update(input));
        }
     "
     class="w-full">

    {{ $slot }}

    <!-- <div class="text-[11px] mt-1 text-gray-400 text-right"
     :class="color">

        <span x-text="count"></span>/<span x-text="max"></span>

    </div> -->

</div>