<footer class="bg-gray-900 text-gray-300 py-12 text-sm">
    <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-5 gap-8">
        <!-- Логотип / Название -->
        <div>
            <h2 class="text-xl font-bold mb-3">ACROVOY</h2>

            <!-- Голубая табличка с фразой и внутренней тенью -->
<span class="inline-block bg-blue-100 border border-blue-600 text-black text-xs px-2 py-1 font-semibold rounded"
      style="box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);">
    Serving hotels & restaurants worldwide
</span>


            <p class="text-orange-300 text-sm mt-4">Global Hospitality Furniture & Décor Marketplace</p>
        </div>

        <!-- Quick Links -->
        <div>
            <h3 class="text-lg font-semibold mb-3">Quick Links</h3>
            <ul class="space-y-1">
                <li><a href="{{ route('main') }}" class="hover:text-white transition">Home</a></li>
                <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition">Suppliers Catalog</a></li>
                <li><a href="{{ route('suppliers.index') }}" class="hover:text-white transition">All Suppliers</a></li>
                <li><a href="" class="hover:text-white transition">Request a Quote</a></li>
                <li><a href="" class="hover:text-white transition">About Us</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
            </ul>
        </div>

        <!-- Partner / Manufacturer Links -->
        <div>
            <h3 class="text-lg font-semibold mb-3">For Partners</h3>
            <ul class="space-y-1">
                <li><a href="{{ route('manufacturer.home') }}" class="hover:text-white transition">Dashboard</a></li>
                <li><a href="{{ route('manufacturer.products.index') }}" class="hover:text-white transition">My Products</a></li>
                <li><a href="{{ route('manufacturer.orders') }}" class="hover:text-white transition">Orders</a></li>
                <li><a href="" class="hover:text-white transition">RFQs</a></li>
                <li><a href="{{ route('manufacturer.company.profile') }}" class="hover:text-white transition">Company Profile</a></li>
                <li><a href="{{ route('manufacturer.premium-plans') }}" class="text-orange-500 hover:text-orange-600 transition">Premium Plans</a></li>
            </ul>
        </div>

        <!-- Resources / Buyer Support -->
        <div>
            <h3 class="text-lg font-semibold mb-3">Resources</h3>
            <ul class="space-y-1">
                <li><a href="{{ route('help.index') }}" class="hover:text-white transition">Help Center</a></li>
                <li><a href="{{ route('help.category', ['slug' => 'guides']) }}" class="hover:text-white transition">Buyer Guides</a></li>
                <li><a href="{{ route('help.category', ['slug' => 'policies']) }}" class="hover:text-white transition">Policies</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
                <li><a href="" class="hover:text-white transition">Contact Support</a></li>
            </ul>
        </div>

        <!-- Contact / Social строгий блок с утонченными иконками -->
<div class="border border-gray-700 bg-gray-800 p-4 rounded-md space-y-3 text-gray-200 text-sm">
    <h3 class="text-lg font-semibold mb-2">Contact</h3>

    <!-- Email -->
    <p class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m0 0v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8m18 0L12 13 3 8" />
        </svg>
        <a href="mailto:info@acrovoy.com" class="hover:text-white transition">info@acrovoy.com</a>
    </p>

    <!-- WhatsApp -->
    <p class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 5a2 2 0 012-2h3.28a2 2 0 011.95 1.553l.84 3.36a2 2 0 01-.45 1.98l-1.27 1.27a16.014 16.014 0 006.586 6.586l1.27-1.27a2 2 0 011.98-.45l3.36.84A2 2 0 0121 17.72V21a2 2 0 01-2 2h-1C7.373 23 1 16.627 1 9V8a2 2 0 012-2h0z" />
        </svg>
        <a href="https://wa.me/8617554207115" target="_blank" class="hover:text-white transition">
            +86 175 5420 7115
        </a>
    </p>

    <!-- Social links -->
    <div class="flex space-x-4 mt-2">
        <a href="#" class="hover:text-white transition border-b border-gray-500 pb-0.5">Facebook</a>
        <a href="#" class="hover:text-white transition border-b border-gray-500 pb-0.5">Instagram</a>
        <a href="#" class="hover:text-white transition border-b border-gray-500 pb-0.5">LinkedIn</a>
    </div>
</div>

    </div>

    <div class="mt-10 border-t border-gray-700 pt-4 text-center text-gray-500 text-xs">
        &copy; {{ date('Y') }} ACROVOY. All rights reserved.
    </div>
</footer>
