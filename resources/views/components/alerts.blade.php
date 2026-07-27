<div
    x-data="alertComponent()"
    x-cloak
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
    x-init="init()"
    class="fixed top-6 right-6 z-[9999] w-full max-w-md pointer-events-none"
>
    <div
        class="pointer-events-auto relative overflow-hidden rounded-2xl border border-gray-200 bg-white/95 backdrop-blur-xl shadow-[0_18px_60px_rgba(15,23,42,.12)]">

        {{-- Цветная полоска --}}
        <div
            class="absolute left-0 top-0 bottom-0 w-1"
            :class="{
                'bg-emerald-500': type === 'success',
                'bg-red-500': type === 'error',
                'bg-amber-500': type === 'warning'
            }">
        </div>

        <div class="flex items-start gap-4 px-6 py-5">

            {{-- Иконка --}}
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border"
                :class="{
                    'bg-emerald-50 border-emerald-100': type === 'success',
                    'bg-red-50 border-red-100': type === 'error',
                    'bg-amber-50 border-amber-100': type === 'warning'
                }">

                {{-- success --}}
                <svg
                    x-show="type === 'success'"
                    class="w-5 h-5 text-emerald-600"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 13l4 4L19 7"/>
                </svg>

                {{-- error --}}
                <svg
                    x-show="type === 'error'"
                    class="w-5 h-5 text-red-600"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/>
                </svg>

                {{-- warning --}}
                <svg
                    x-show="type === 'warning'"
                    class="w-5 h-5 text-amber-600"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 8v4m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.66 18h16.68a1 1 0 00.87-1.5l-7.5-13a1 1 0 00-1.74 0z"/>
                </svg>

            </div>

            <div class="flex-1 min-w-0">

                <div
                    class="text-sm font-semibold text-gray-900"
                    x-text="
                        type === 'success'
                            ? 'Success'
                            : (type === 'error'
                                ? 'Something went wrong'
                                : 'Please check the form')
                    ">
                </div>

                <div
                    class="mt-1 text-sm leading-relaxed text-gray-600 whitespace-pre-line"
                    x-text="message">
                </div>

            </div>

            <button
                @click="show=false"
                class="shrink-0 text-gray-300 hover:text-gray-600 transition">
                ✕
            </button>

        </div>

    </div>
</div>

<script>
function alertComponent() {

    return {

        show: false,

        type: 'success',

        message: '',

        timer: null,

        init() {

            @if(session('success'))
                this.showAlert('success', @js(session('success')));
            @endif

            @if(session('error'))
                this.showAlert('error', @js(session('error')));
            @endif

            @if($errors->any())
                this.showAlert('warning', @js($errors->implode("\n")));
            @endif

            window.addEventListener('app-alert', (e) => {

                this.showAlert(
                    e.detail.type,
                    e.detail.message
                );

            });

        },

        showAlert(type, message) {

            this.type = type;
            this.message = message;
            this.show = true;

            clearTimeout(this.timer);

            this.timer = setTimeout(() => {

                this.show = false;

            }, 5000);

        }

    };

}

window.dispatchAlert = function(type, message) {

    window.dispatchEvent(new CustomEvent('app-alert', {

        detail: {
            type,
            message
        }

    }));

};
</script>