{{-- Add to project modal --}}

<div
    x-show="showProjectBox"
    x-cloak
    x-transition.opacity
    @keydown.escape.window="showProjectBox = false"
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
>

    {{-- BACKDROP --}}
    <div
        class="absolute inset-0 bg-black/40 backdrop-blur-md"
        @click="showProjectBox = false"
    ></div>


    {{-- MODAL --}}
    <div
        x-show="showProjectBox"
        x-transition
        @click.stop
        class="relative z-10 w-full max-w-2xl max-h-[90vh]
               overflow-y-auto
               bg-white
               border border-gray-200
               rounded-2xl
               p-6
               shadow-2xl"
    >

        {{-- HEADER --}}
        <div class="flex items-start justify-between mb-5">

            <div>
                <h4 class="text-base font-semibold text-gray-900 mb-1">
                    Add product to project
                </h4>

                <p class="text-sm text-gray-500">
                    Build product collections and attach them to production projects.
                </p>
            </div>


            {{-- CLOSE --}}
            <button
                type="button"
                @click="showProjectBox = false"
                class="ml-4 flex-shrink-0 flex items-center justify-center
                       w-9 h-9 rounded-full
                       text-gray-500
                       hover:bg-gray-100
                       hover:text-gray-900
                       transition"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>

        </div>


        {{-- INSTRUCTION --}}
        <div class="mb-4 rounded-lg bg-gray-50 border border-gray-200 p-4 text-sm text-gray-700">

            <p class="font-medium mb-1">
                How it works:
            </p>

            <ul class="list-disc list-inside space-y-1 text-gray-600">
                <li>Create a project in your dashboard</li>
                <li>Add products to the project</li>
                <li>Convert project into RFQ for your manufacturer</li>
            </ul>

        </div>


        @auth

            {{-- Для авторизованных пользователей --}}

            <form
                action="{{ route('buyer.rfqs.customization.store') }}"
                method="POST"
            >
                @csrf

                <input
                    type="hidden"
                    name="product_id"
                    value="{{ $product1->id }}"
                >

                <input
                    type="hidden"
                    name="type"
                    value="product"
                >

                <input
                    type="hidden"
                    name="title"
                    value="{{ $product1->name }}"
                >


                <div class="mb-4">

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Select project
                    </label>

                    <select
                        name="project_id"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm
                               focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                        <option value="" selected disabled>
                            Select project
                        </option>

                        @foreach($projects as $project)

                            <option value="{{ $project->id }}">
                                {{ $project->title }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- CTA --}}
                <button
                    type="submit"
                    class="w-full bg-blue-950 hover:bg-blue-900
                           text-white py-3 rounded-lg
                           text-sm font-semibold tracking-wide
                           transition shadow-md"
                >
                    Add product to project
                </button>

            </form>

        @endauth


        @guest

            {{-- Для гостей --}}

            <div class="text-center py-4">

                <p class="text-sm text-gray-500 mb-2">
                    Only registered users can add products to projects.
                </p>

                <select
                    disabled
                    class="w-full border border-gray-300 rounded-lg
                           px-4 py-2.5 text-sm
                           bg-gray-100 cursor-not-allowed"
                >
                    <option>
                        Select project
                    </option>
                </select>

                <button
                    disabled
                    class="mt-2 w-full bg-gray-400 text-white py-3
                           rounded-lg text-sm font-semibold
                           cursor-not-allowed"
                >
                    Add product to project
                </button>

            </div>

        @endguest

    </div>

</div>