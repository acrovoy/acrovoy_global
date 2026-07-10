<aside class="w-full lg:w-1/4 max-w-[320px] flex-shrink-0 ">

 
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm sticky top-20">
        <div class="shadow-lg bg-gradient-to-b from-white via-gray-50 to-gray-100 rounded-lg">
            <div class="pt-4 px-4">


                {{-- HEADER --}}
                <div class="mb-4">
                    <div class="flex justify-between items-start">

                        <div>
                            <div class="font-semibold text-gray-900">
                                {{ $project->public_id }}
                            </div>

                            <div class="text-[10px] {{ $project->status->badgeClasses() }} tracking-wide">
                                {{ $project->status->label() }}
                            </div>
                        </div>

                        {{-- TIMER --}}
                        <div
                            x-data="countdown('{{ $project->closed_at->toIso8601String() }}')"
                            x-init="start()"
                            class="flex flex-col items-end gap-1">

                            {{-- TIMER --}}
                            <div class="flex gap-1 text-center font-mono">

                                <div class="bg-gray-900 text-white px-2 py-1 rounded leading-tight">
                                    <div class="text-sm" x-text="days"></div>
                                    <div class="text-[8px] text-gray-400 tracking-wide">DAYS</div>
                                </div>

                                <div class="bg-gray-900 text-white px-2 py-1 rounded leading-tight">
                                    <div class="text-sm" x-text="hours"></div>
                                    <div class="text-[8px] text-gray-400 tracking-wide">HOURS</div>
                                </div>

                                <div class="bg-gray-900 text-white px-2 py-1 rounded leading-tight">
                                    <div
                                        class="text-sm transition-all duration-300"
                                        :class="blink ? 'opacity-50 scale-95' : 'opacity-100 scale-100'"
                                        x-text="minutes"></div>
                                    <div class="text-[8px] text-gray-400 tracking-wide">MINUTES</div>
                                </div>

                            </div>

                            {{-- STATUS --}}
                            <div
                                class="text-xs font-medium"
                                :class="statusColor"
                                x-text="statusText"></div>

                        </div>

                    </div>

                    <div class="text-xs uppercase text-amber-600 mt-2">
                    
                    <span class="text-gray-600 normal-case">{{ $project->title }}</span>
                    </div>

                    <div class="text-gray-800/50 text-xs mt-3 flex justify-between pb-1">
                        <div></div>
                        <div>
                            <span class="text-gray-600/50">Visibility:</span>

                            <span class="font-medium">
                                {{ $project->visibility_type->label() }}
                            </span>
                        </div>
                    </div>


                </div>

            </div>
        </div>
        <div class="px-4 pb-4">



           
            

        </div>
    </div>



</aside>

<script>
window.countdown = function(deadline) {
    return {
        deadline: new Date(deadline),

        days: '00',
        hours: '00',
        minutes: '00',

        prevMinutes: null,
        blink: false,

        statusText: '',
        statusColor: 'text-gray-500',

        start() {
            this.update();
            setInterval(() => this.update(), 1000);
        },

        update() {
            let now = new Date();
            let diff = this.deadline - now;

            if (diff <= 0) {
                this.days = this.hours = this.minutes = '00';
                this.statusText = 'Closed';
                this.statusColor = 'text-gray-400';
                return;
            }

            let totalSeconds = Math.floor(diff / 1000);

            let d = Math.floor(totalSeconds / 86400);
            let h = Math.floor((totalSeconds % 86400) / 3600);
            let m = Math.floor((totalSeconds % 3600) / 60);

            let newMinutes = String(m).padStart(2, '0');

            // 🔁 анимация
            if (this.prevMinutes !== null && this.prevMinutes !== newMinutes) {
                this.blink = true;
                setTimeout(() => this.blink = false, 300);
            }

            this.prevMinutes = newMinutes;

            this.days = String(d).padStart(2, '0');
            this.hours = String(h).padStart(2, '0');
            this.minutes = newMinutes;

            // 🎯 статус
            if (d >= 1) {
                this.statusText = 'Active';
                this.statusColor = 'text-gray-500';
            } else if (h >= 1) {
                this.statusText = 'Closing today';
                this.statusColor = 'text-yellow-600';
            } else {
                this.statusText = 'Closing soon';
                this.statusColor = 'text-red-600';
            }
        }
    }
}
</script>