<div class="bg-white border rounded-lg">

            <div class="border-b px-5 py-3 font-semibold">
                Personal Information
            </div>

            <div class="p-5 space-y-4">

                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <div class="text-sm text-gray-500">First Name</div>
                        <div>{{ $user->name ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Last Name</div>
                        <div>{{ $user->last_name ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Email</div>
                        <div>{{ $user->email }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Platform Role</div>
                        <div>{{ $user->role ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Timezone</div>
                        <div>{{ $user->timezone ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Currency</div>
                        <div>{{ $user->currency ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Purchase Country</div>
                        <div>{{ $user->purchase_country ?: '-' }}</div>
                    </div>

                </div>

            </div>

        </div>