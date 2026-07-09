<div
    id="confirm-modal"
    class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-200"
>

    {{-- Overlay --}}
    <div
        id="confirm-modal-overlay"
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
    ></div>

    {{-- Modal --}}
    <div
    id="confirm-modal-window"
    class="relative z-10 w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl scale-95 opacity-0 transition-all duration-200"
>

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-200 bg-gradient-to-r from-slate-50 via-[#f4f1eb] to-[#ebe5dc] px-6 py-5">

            <div class="flex items-center gap-4">

                {{-- Icon --}}
                <div
                    id="confirm-modal-icon"
                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-100 text-red-600"
                >

                    {{-- Danger --}}
                    <svg
                        id="confirm-icon-danger"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="h-6 w-6"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m-8 0l.7 11.2A2 2 0 008.7 20h6.6a2 2 0 002-1.8L18 7M10 11v5m4-5v5"/>
                    </svg>

                    {{-- Warning --}}
                    <svg
                        id="confirm-icon-warning"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="hidden h-6 w-6"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v4m0 4h.01M10.29 3.86L1.82 18A2 2 0 003.53 21h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>

                    {{-- Success --}}
                    <svg
                        id="confirm-icon-success"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="hidden h-6 w-6"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M5 13l4 4L19 7"/>
                    </svg>

                    {{-- Info --}}
                    <svg
                        id="confirm-icon-info"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="hidden h-6 w-6"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9h.01M11 12h1v4h1"/>
                        <circle cx="12" cy="12" r="9"/>
                    </svg>

                </div>

                <div>

                    <h2
                        id="confirm-modal-title"
                        class="text-lg font-semibold text-gray-900"
                    >
                        Confirmation
                    </h2>

                    <p
                        id="confirm-modal-description"
                        class="mt-1 text-sm text-gray-500"
                    >
                    </p>

                </div>

            </div>

            {{-- Close --}}
            <button
                id="confirm-modal-close"
                type="button"
                class="rounded-lg p-2 text-gray-400 transition hover:bg-white hover:text-gray-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="2"
                     stroke="currentColor"
                     class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

        </div>

        {{-- Body --}}
        <div class="px-6 py-6">

            <p
                id="confirm-modal-message"
                class="text-sm leading-7 text-gray-700"
            >
            </p>

        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-5">

            <button
                id="confirm-modal-cancel"
                type="button"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
                Cancel
            </button>

            <button
                id="confirm-modal-confirm"
                type="button"
                class="inline-flex items-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-red-700"
            >
                Confirm
            </button>

        </div>

    </div>

</div>