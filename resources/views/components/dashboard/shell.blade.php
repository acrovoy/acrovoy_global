<div class="bg-[#F7F3EA] py-8">

    <div class="max-w-7xl mx-auto flex gap-6 px-4">

        {{-- SIDEBAR --}}
        

            <x-dashboard.sidebar />

       


        {{-- RIGHT CONTENT --}}
        <main class="w-full lg:w-3/4 bg-white shadow-sm rounded-lg p-6 min-h-[400px]">

           

            {{ $slot }}

        </main>

    </div>

</div>