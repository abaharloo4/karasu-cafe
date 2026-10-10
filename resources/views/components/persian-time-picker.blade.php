@props([
    'name' => 'time',
    'value' => null,
    'label' => null,
    'placeholder' => 'انتخاب زمان (۲۴ ساعته)...',
    'required' => false,
    'presets' => true,
])

<div x-data="persianTimePicker({
        initialValue: '{{ $value ?? '' }}',
        name: '{{ $name }}'
    })"
    class="relative w-full text-right select-none"
    dir="rtl">

    @if($label)
        <label class="block text-xs font-bold text-[#19242B] mb-1.5 flex items-center justify-between">
            <span>{{ $label }}</span>
            <template x-if="value">
                <span class="text-[11px] font-mono font-black text-[#3C6E71] bg-[#3C6E71]/10 px-2 py-0.5 rounded-lg border border-[#3C6E71]/20 tabular-nums" x-text="formattedFa"></span>
            </template>
        </label>
    @endif

    <!-- Hidden Input for Standard Form Submission (HH:mm in 24h format) -->
    <input type="hidden"
           name="{{ $name }}"
           :value="value"
           @if($required) required @endif>

    <!-- Trigger Input Button (VibeFarsi Graphite Style) -->
    <div class="relative">
        <button type="button"
                @click="openPicker()"
                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border border-[#CBD5E1] hover:border-[#3C6E71] rounded-xl text-xs font-bold text-[#19242B] transition-all min-h-[44px] shadow-2xs focus:ring-2 focus:ring-[#3C6E71]/20 focus:outline-none btn-tactile">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#3C6E71]/10 text-[#3C6E71] flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 2"/>
                    </svg>
                </div>
                <div class="flex items-center gap-1.5">
                    <span x-text="value ? formattedFa : '{{ $placeholder }}'"
                          :class="value ? 'text-[#19242B] font-mono font-black text-sm tabular-nums' : 'text-[#64748B]'"></span>
                    <span x-show="value" class="text-[10px] font-bold text-[#64748B]">(۲۴ ساعته)</span>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                <template x-if="value">
                    <span @click.stop="clearTime()"
                          class="w-6 h-6 rounded-full hover:bg-rose-50 text-slate-400 hover:text-rose-500 flex items-center justify-center text-xs transition-colors"
                          title="پاک کردن زمان">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </template>
                <svg class="w-4 h-4 text-[#64748B] transition-transform duration-200" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
    </div>

    <!-- Visual 24H Clock Dial Popover Modal -->
    <div x-show="isOpen"
         @click.outside="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute z-50 mt-2 start-0 w-[310px] sm:w-[330px] bg-white border border-[#CBD5E1] rounded-3xl shadow-2xl p-4 text-xs select-none backdrop-blur-md"
         style="display: none;">

        <!-- Header: Digital 24H Readout & Mode Switcher -->
        <div class="bg-[#19242B] text-white p-3.5 rounded-2xl flex items-center justify-between mb-3 shadow-inner">
            <div class="space-y-0.5">
                <span class="text-[10px] text-slate-400 font-bold block">زمان انتخابی (۲۴ ساعته)</span>
                <div class="flex items-center gap-1 font-mono text-xl font-black tabular-nums dir-ltr">
                    <!-- Hour Button -->
                    <button type="button"
                            @click="mode = 'hours'"
                            :class="mode === 'hours' ? 'bg-[#3C6E71] text-white ring-2 ring-emerald-400/30' : 'bg-white/10 text-slate-300 hover:bg-white/20'"
                            class="px-2.5 py-1 rounded-xl transition-all font-black text-lg">
                        <span x-text="pad(tempHour)"></span>
                    </button>
                    <span class="text-emerald-400 animate-pulse font-black">:</span>
                    <!-- Minute Button -->
                    <button type="button"
                            @click="mode = 'minutes'"
                            :class="mode === 'minutes' ? 'bg-[#3C6E71] text-white ring-2 ring-emerald-400/30' : 'bg-white/10 text-slate-300 hover:bg-white/20'"
                            class="px-2.5 py-1 rounded-xl transition-all font-black text-lg">
                        <span x-text="pad(tempMinute)"></span>
                    </button>
                </div>
            </div>

            <!-- Mode Switch Tabs -->
            <div class="flex items-center gap-1 bg-white/10 p-1 rounded-xl">
                <button type="button"
                        @click="mode = 'hours'"
                        :class="mode === 'hours' ? 'bg-[#3C6E71] text-white shadow-xs font-black' : 'text-slate-300 hover:text-white font-medium'"
                        class="px-2.5 py-1.5 rounded-lg text-xs transition-all">
                    ساعت
                </button>
                <button type="button"
                        @click="mode = 'minutes'"
                        :class="mode === 'minutes' ? 'bg-[#3C6E71] text-white shadow-xs font-black' : 'text-slate-300 hover:text-white font-medium'"
                        class="px-2.5 py-1.5 rounded-lg text-xs transition-all">
                    دقیقه
                </button>
            </div>
        </div>

        <!-- Mode Indicator / Helper text -->
        <div class="flex items-center justify-between text-[11px] text-[#64748B] mb-2 px-1">
            <span x-show="mode === 'hours'" class="font-bold flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-[#3C6E71]"></span>
                حلقه بیرونی: ۱۲ تا ۲۳ | حلقه داخلی: ۰۰ تا ۱۱
            </span>
            <span x-show="mode === 'minutes'" class="font-bold flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-[#3C6E71]"></span>
                دقیقه مورد نظر را روی ساعت لمس کنید:
            </span>
            <button type="button" @click="setNow()" class="text-[#3C6E71] hover:underline font-black text-[11px]">
                اکنون
            </button>
        </div>

        <!-- Interactive Visual Clock Dial -->
        <div class="relative w-[260px] h-[260px] mx-auto my-1 rounded-full bg-[#F8FAFC] border-2 border-slate-200/80 shadow-inner flex items-center justify-center overflow-hidden select-none">
            
            <!-- Clock Center Pivot -->
            <div class="absolute z-20 w-3 h-3 rounded-full bg-[#3C6E71] border-2 border-white shadow-xs"></div>

            <!-- Visual Clock Hand Indicator -->
            <div class="absolute z-10 w-0.5 origin-bottom bg-[#3C6E71] transition-all duration-200 ease-out pointer-events-none"
                 :style="clockHandStyle">
                <!-- Outer Hand Bubble -->
                <div class="absolute -top-3.5 -left-3.5 w-7 h-7 rounded-full bg-[#3C6E71] opacity-90 shadow-sm"></div>
            </div>

            <!-- Hour Mode Dial (24 Hours) -->
            <div x-show="mode === 'hours'" class="absolute inset-0">
                <!-- Outer Circle: 12 to 23 -->
                <template x-for="item in outerHours" :key="'h-out-' + item.h">
                    <button type="button"
                            @click="selectHour(item.h)"
                            :style="`left: ${item.x}px; top: ${item.y}px;`"
                            :class="tempHour === item.h ? 'bg-[#3C6E71] text-white font-black scale-110 shadow-md ring-2 ring-white z-30' : 'bg-transparent text-slate-800 hover:bg-slate-200/80 font-bold z-20'"
                            class="absolute -translate-x-1/2 -translate-y-1/2 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-mono tabular-nums transition-transform">
                        <span x-text="pad(item.h)"></span>
                    </button>
                </template>

                <!-- Inner Circle: 00 to 11 -->
                <template x-for="item in innerHours" :key="'h-in-' + item.h">
                    <button type="button"
                            @click="selectHour(item.h)"
                            :style="`left: ${item.x}px; top: ${item.y}px;`"
                            :class="tempHour === item.h ? 'bg-[#3C6E71] text-white font-black scale-110 shadow-md ring-2 ring-white z-30' : 'bg-transparent text-slate-500 hover:bg-slate-200/80 font-bold z-20'"
                            class="absolute -translate-x-1/2 -translate-y-1/2 w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-mono tabular-nums transition-transform">
                        <span x-text="pad(item.h)"></span>
                    </button>
                </template>
            </div>

            <!-- Minute Mode Dial (00 to 55 with 5min steps) -->
            <div x-show="mode === 'minutes'" class="absolute inset-0">
                <template x-for="item in minuteSteps" :key="'m-' + item.m">
                    <button type="button"
                            @click="selectMinute(item.m)"
                            :style="`left: ${item.x}px; top: ${item.y}px;`"
                            :class="tempMinute === item.m ? 'bg-[#3C6E71] text-white font-black scale-110 shadow-md ring-2 ring-white z-30' : 'bg-transparent text-slate-800 hover:bg-slate-200/80 font-bold z-20'"
                            class="absolute -translate-x-1/2 -translate-y-1/2 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-mono tabular-nums transition-transform">
                        <span x-text="pad(item.m)"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Fine Minute Stepper (For setting exact minute 0-59) -->
        <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between gap-1 text-[11px]">
            <span class="text-[#64748B] font-bold text-[10px]">تنظیم دقیق:</span>
            <div class="flex items-center gap-1 dir-ltr">
                <button type="button" @click="stepMinute(-5)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold tabular-nums">-۵</button>
                <button type="button" @click="stepMinute(-1)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold tabular-nums">-۱</button>
                <span class="px-2 font-mono font-black text-xs text-[#19242B] tabular-nums" x-text="pad(tempMinute)"></span>
                <button type="button" @click="stepMinute(1)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold tabular-nums">+۱</button>
                <button type="button" @click="stepMinute(5)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold tabular-nums">+۵</button>
            </div>
        </div>

        <!-- Quick Cafe Shift Presets -->
        @if($presets)
            <div class="mt-2.5 pt-2 border-t border-slate-100">
                <span class="text-[10px] text-[#64748B] font-bold block mb-1.5">میانبرهای پرکاربرد:</span>
                <div class="grid grid-cols-5 gap-1 text-[10px] font-mono font-bold text-center">
                    <button type="button" @click="setPreset('08:00')" class="py-1 rounded-lg bg-slate-50 hover:bg-[#3C6E71]/15 hover:text-[#3C6E71] text-slate-700 border border-slate-200 transition-colors">08:00</button>
                    <button type="button" @click="setPreset('12:00')" class="py-1 rounded-lg bg-slate-50 hover:bg-[#3C6E71]/15 hover:text-[#3C6E71] text-slate-700 border border-slate-200 transition-colors">12:00</button>
                    <button type="button" @click="setPreset('16:00')" class="py-1 rounded-lg bg-slate-50 hover:bg-[#3C6E71]/15 hover:text-[#3C6E71] text-slate-700 border border-slate-200 transition-colors">16:00</button>
                    <button type="button" @click="setPreset('20:00')" class="py-1 rounded-lg bg-slate-50 hover:bg-[#3C6E71]/15 hover:text-[#3C6E71] text-slate-700 border border-slate-200 transition-colors">20:00</button>
                    <button type="button" @click="setPreset('23:00')" class="py-1 rounded-lg bg-slate-50 hover:bg-[#3C6E71]/15 hover:text-[#3C6E71] text-slate-700 border border-slate-200 transition-colors">23:00</button>
                </div>
            </div>
        @endif

        <!-- Footer Actions -->
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
            <button type="button"
                    @click="isOpen = false"
                    class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                انصراف
            </button>

            <button type="button"
                    @click="confirmTime()"
                    class="px-5 py-1.5 rounded-xl bg-[#3C6E71] hover:bg-[#284B63] text-white font-black shadow-xs transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>تایید زمان</span>
            </button>
        </div>
    </div>
</div>

@once
<script>
function persianTimePicker(config) {
    return {
        isOpen: false,
        name: config.name || 'time',
        value: config.initialValue || '',
        tempHour: 12,
        tempMinute: 0,
        mode: 'hours', // 'hours' or 'minutes'
        center: 130, // center of 260px container
        outerRadius: 98,
        innerRadius: 62,
        minuteRadius: 94,

        init() {
            if (this.value) {
                const parts = this.value.split(':');
                if (parts.length >= 2) {
                    this.tempHour = Math.min(23, Math.max(0, parseInt(parts[0]) || 0));
                    this.tempMinute = Math.min(59, Math.max(0, parseInt(parts[1]) || 0));
                    this.value = `${this.pad(this.tempHour)}:${this.pad(this.tempMinute)}`;
                }
            }
        },

        pad(n) {
            return String(n).padStart(2, '0');
        },

        get formattedFa() {
            if (!this.value) return '';
            return this.value;
        },

        openPicker() {
            if (this.value) {
                const parts = this.value.split(':');
                this.tempHour = parseInt(parts[0]) || 0;
                this.tempMinute = parseInt(parts[1]) || 0;
            } else {
                const d = new Date();
                this.tempHour = d.getHours();
                this.tempMinute = Math.round(d.getMinutes() / 5) * 5 % 60;
            }
            this.mode = 'hours';
            this.isOpen = true;
        },

        get outerHours() {
            // Hours 12 to 23
            // 12 is at 0 deg (top), 13 at 30 deg, ..., 23 at 330 deg
            const list = [];
            for (let i = 0; i < 12; i++) {
                const h = (i === 0) ? 12 : 12 + i;
                const angleDeg = i * 30 - 90;
                const rad = angleDeg * (Math.PI / 180);
                list.push({
                    h: h,
                    x: Math.round(this.center + this.outerRadius * Math.cos(rad)),
                    y: Math.round(this.center + this.outerRadius * Math.sin(rad))
                });
            }
            return list;
        },

        get innerHours() {
            // Hours 00 to 11
            // 00 is at 0 deg (top), 01 at 30 deg, ..., 11 at 330 deg
            const list = [];
            for (let i = 0; i < 12; i++) {
                const h = i;
                const angleDeg = i * 30 - 90;
                const rad = angleDeg * (Math.PI / 180);
                list.push({
                    h: h,
                    x: Math.round(this.center + this.innerRadius * Math.cos(rad)),
                    y: Math.round(this.center + this.innerRadius * Math.sin(rad))
                });
            }
            return list;
        },

        get minuteSteps() {
            // Minutes 00, 05, 10, ..., 55 (12 ticks)
            const list = [];
            for (let i = 0; i < 12; i++) {
                const m = i * 5;
                const angleDeg = i * 30 - 90;
                const rad = angleDeg * (Math.PI / 180);
                list.push({
                    m: m,
                    x: Math.round(this.center + this.minuteRadius * Math.cos(rad)),
                    y: Math.round(this.center + this.minuteRadius * Math.sin(rad))
                });
            }
            return list;
        },

        get clockHandStyle() {
            if (this.mode === 'hours') {
                const isOuter = this.tempHour >= 12;
                const length = isOuter ? this.outerRadius : this.innerRadius;
                const hIndex = this.tempHour % 12;
                const angleDeg = hIndex * 30; // 0 is top
                return `height: ${length}px; transform: rotate(${angleDeg}deg); bottom: ${this.center}px; left: ${this.center - 1}px;`;
            } else {
                const length = this.minuteRadius;
                const angleDeg = this.tempMinute * 6; // 60 mins in 360 deg
                return `height: ${length}px; transform: rotate(${angleDeg}deg); bottom: ${this.center}px; left: ${this.center - 1}px;`;
            }
        },

        selectHour(h) {
            this.tempHour = h;
            // Smoothly auto switch to minutes after picking hour
            setTimeout(() => {
                this.mode = 'minutes';
            }, 180);
        },

        selectMinute(m) {
            this.tempMinute = m;
        },

        stepMinute(delta) {
            let m = this.tempMinute + delta;
            if (m < 0) m = 59;
            if (m > 59) m = 0;
            this.tempMinute = m;
        },

        setNow() {
            const now = new Date();
            this.tempHour = now.getHours();
            this.tempMinute = now.getMinutes();
        },

        setPreset(presetStr) {
            const parts = presetStr.split(':');
            this.tempHour = parseInt(parts[0]) || 0;
            this.tempMinute = parseInt(parts[1]) || 0;
            this.confirmTime();
        },

        clearTime() {
            this.value = '';
            this.isOpen = false;
        },

        confirmTime() {
            this.value = `${this.pad(this.tempHour)}:${this.pad(this.tempMinute)}`;
            this.isOpen = false;
            this.$dispatch('time-selected', { name: this.name, value: this.value });
        }
    };
}
</script>
@endonce
