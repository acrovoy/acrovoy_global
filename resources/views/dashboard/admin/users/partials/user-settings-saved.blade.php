<div class="bg-white border rounded-lg">

        <div class="border-b px-5 py-3 font-semibold">
            User Settings Saved
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50">

                    <tr>
                        <th class="text-left px-4 py-2">Key</th>
                        <th class="text-left px-4 py-2">Value</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($user->settings as $setting)

                    <tr class="border-t">

                        <td class="px-4 py-2 font-mono">
                            {{ $setting->key }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $setting->value }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-gray-500">
                            No settings found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>