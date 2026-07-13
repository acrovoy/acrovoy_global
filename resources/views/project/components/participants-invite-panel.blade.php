<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-4">

    <div class="text-sm font-semibold">
        Invite Suppliers
    </div>

    {{-- ========================= --}}
    {{-- PRIVATE MODE --}}
    {{-- ========================= --}}
    @if($visibility === 'private')

        <div class="text-xs text-gray-500">
            Private Project — invite suppliers manually only
        </div>

        {{-- INVITE FROM PLATFORM --}}
        <form method="POST"
              action="{{ route('buyer.projects.participants.store', $project) }}"
              class="flex gap-2">

            @csrf

            <input type="hidden"
                   name="participant_type"
                   id="participant_type"
                   value="">

            <select name="participant_id"
                    id="participant_id"
                    class="border border-gray-300 rounded px-3 py-2 text-sm text-gray-600 w-full"
                    required>

                <option value="">
                    Select participant
                </option>

                @foreach($allparticipants as $p)

                    <option
                        value="{{ $p['id'] }}"
                        data-type="{{ $p['type'] }}">

                        {{ $p['label'] }}

                    </option>

                @endforeach

            </select>

            <button
                class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
                Invite
            </button>

        </form>

    {{-- ========================= --}}
    {{-- CATEGORY MODE --}}
    {{-- ========================= --}}
    @elseif($visibility === 'category')

        <div class="text-xs text-gray-500 mb-2">
            Select supplier categories that can view this project
        </div>

        <form method="POST"
              action="{{ route('buyer.projects.visibility.category.update', $project) }}"
              class="space-y-3">

            @csrf
            @method('PATCH')

            <div class="border border-gray-200 rounded p-3 max-h-64 overflow-y-auto space-y-2">

                @foreach($allCategories ?? [] as $category)

                    <label class="flex items-center gap-2 text-sm text-gray-700">

                        <input
                            type="checkbox"
                            name="category_ids[]"
                            value="{{ $category->id }}"
                            class="rounded border-gray-300"

                            @checked(in_array($category->id, $selectedCategoryIds ?? []))
                        >

                        <span>{{ $category->name }}</span>

                    </label>

                @endforeach

            </div>

            <button
                class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
                Save Categories
            </button>

        </form>

        {{-- OPTIONAL MANUAL INVITE --}}
        <div class="pt-2 border-t border-gray-200">

            <form method="POST"
                  action="{{ route('buyer.projects.participants.store', $project) }}"
                  class="flex gap-2">

                @csrf

                <input type="hidden"
                       name="participant_type"
                       id="participant_type_category"
                       value="">

                <select name="participant_id"
                        id="participant_id_category"
                        class="border border-gray-300 rounded px-3 py-2 text-sm text-gray-600 w-full">

                    <option value="">
                        Select participant
                    </option>

                    @foreach($allparticipants as $p)

                        <option
                            value="{{ $p['id'] }}"
                            data-type="{{ $p['type'] }}">

                            {{ $p['label'] }}

                        </option>

                    @endforeach

                </select>

                <button
                    class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
                    Invite
                </button>

            </form>

        </div>

    {{-- ========================= --}}
    {{-- PLATFORM MODE --}}
    {{-- ========================= --}}
    @elseif($visibility === 'platform')

        <div class="text-xs text-gray-500">
            Platform project — every supplier on the platform can discover this project.
        </div>

    {{-- ========================= --}}
    {{-- OPEN MODE --}}
    {{-- ========================= --}}
    @elseif($visibility === 'open')

        <div class="text-xs text-gray-400">
            Open Project — public discovery mode (future feature)
        </div>

    @endif

    {{-- ========================= --}}
    {{-- EMAIL INVITE --}}
    {{-- ========================= --}}
    <form method="POST"
          action="{{ route('buyer.projects.participants.store', $project) }}"
          class="flex gap-2 pt-3 border-t border-gray-200">

        @csrf

        <input type="email"
               name="email"
               placeholder="supplier@email.com"
               class="border border-gray-300 rounded px-3 py-2 text-sm w-full">

        <button
            class="px-4 py-2 bg-gray-800 text-white text-sm rounded">
            Invite by Email
        </button>

    </form>

</div>

<script>

const participantSelect = document.getElementById('participant_id');

if (participantSelect) {

    participantSelect.addEventListener('change', function () {

        const selected = this.options[this.selectedIndex];

        document.getElementById('participant_type').value =
            selected.dataset.type || '';

    });

}

const participantSelectCategory = document.getElementById('participant_id_category');

if (participantSelectCategory) {

    participantSelectCategory.addEventListener('change', function () {

        const selected = this.options[this.selectedIndex];

        document.getElementById('participant_type_category').value =
            selected.dataset.type || '';

    });

}

</script>