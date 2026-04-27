{{-- TABS --}}
<div class="mb-5 border-b border-gray-200">

    <div class="flex gap-6 text-sm">

        @php
            function tabClass($active) {
                return $active
                    ? 'border-gray-900 text-gray-900'
                    : 'border-transparent text-gray-500 hover:text-gray-900';
            }
        @endphp

        <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'overview']) }}"
           class="pb-2 border-b-2 {{ tabClass($activeTab === 'overview') }}">
            Overview
        </a>

        <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'requirements']) }}"
           class="pb-2 border-b-2 {{ tabClass($activeTab === 'requirements') }}">
            Requirements
        </a>

        <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'participants', 'suppliers' => $suppliers, 'categories' => $categories]) }}"
           class="pb-2 border-b-2 {{ tabClass($activeTab === 'participants') }}">
            Participants
        </a>

        <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'offers']) }}"
           class="pb-2 border-b-2 {{ tabClass($activeTab === 'offers') }}">
            Offers
        </a>

        <a href="{{ route('rfqs.workspace', ['rfq' => $rfq->id, 'tab' => 'audit']) }}"
           class="pb-2 border-b-2 {{ tabClass($activeTab === 'audit') }}">
            Timeline
        </a>

    </div>

</div>