@props([
    'name' => 'time',
    'value' => null,
    'label' => null,
    'placeholder' => 'انتخاب زمان (ساعت مچی ۲۴ ساعته)...',
    'required' => false,
    'presets' => true,
])

<div x-data="persianWristwatchPicker({
        initialValue: '{{ $value ?? '' }}',
        name: '{{ $name }}'
    })"
    class="relative w-full text-right select-none"
    dir="rtl">

    @if($label)
        <label class="block text-xs font-bold text-[#19242B] mb-1.5 flex items-center justify-between">
            <span>{{ $label }}</span>
            <template x-if="value">
                <span class="text-[11px] font-mono font-black text-[#3C6E71] bg-[#3C6E71]/10 px-2 py-0.5 rounded-lg border border-[#3C6E71]/20 tabular-nums" x-text="formattedDisplay"></span>
            </template>
        </label>
    @endif

    <!-- Hidden Input for Form Submission (HH:mm) -->
    <input type="hidden"
           name="{{ $name }}"
           :value="value"
           @if($required) required @endif>

    <!-- Trigger Button -->
    <div class="relative">
        <button type="button"
                @click="openPicker()"
                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border border-[#CBD5E1] hover:border-[#3C6E71] rounded-xl text-xs font-bold text-[#19242B] transition-all min-h-[44px] shadow-2xs focus:ring-2 focus:ring-[#3C6E71]/20 focus:outline-none btn-tactile">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#3C6E71]/10 text-[#3C6E71] flex items-center justify-center flex-shrink-0">
                    <!-- Wristwatch SVG Icon -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="7" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3l2 1.5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5V3h6v2M9 19v2h6v-2"/>
                    </svg>
                </div>
                <div class="flex items-center gap-1.5">
                    <span x-text="value ? formattedDisplay : '{{ $placeholder }}'"
                          :class="value ? 'text-[#19242B] font-mono font-black text-sm tabular-nums' : 'text-[#64748B]'"></span>
                    <span x-show="value" class="text-[10px] font-bold text-[#64748B]">(ساعت ۲۴ ساعته)</span>
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

    <!-- WRISTWATCH (ساعت مچی) POPOVER MODAL -->
    <div x-show="isOpen"
         @click.outside="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute z-50 mt-2 start-0 w-[320px] sm:w-[340px] bg-slate-900/95 border border-slate-700/80 rounded-3xl shadow-2xl p-4 text-xs select-none backdrop-blur-md"
         style="display: none;">

        <!-- Wristwatch Header & Mode Indicator -->
        <div class="flex items-center justify-between mb-2 px-1 text-white">
            <div>
                <span class="text-[10px] text-slate-400 font-bold block">تنظیم ساعت مچی (۲۴ ساعته)</span>
                <span class="text-xs font-black text-emerald-400" x-text="activeHand === 'hour' ? 'در حال تنظیم: عقربه ساعت‌شمار' : 'در حال تنظیم: عقربه دقیقه‌شمار'"></span>
            </div>

            <!-- Hand Switcher (ساعت‌شمار یا دقیقه‌شمار) -->
            <div class="flex items-center gap-1 bg-slate-800 p-1 rounded-xl border border-slate-700">
                <button type="button"
                        @click="activeHand = 'hour'"
                        :class="activeHand === 'hour' ? 'bg-[#3C6E71] text-white font-black' : 'text-slate-400 hover:text-white'"
                        class="px-2.5 py-1 rounded-lg text-[11px] transition-colors">
                    ساعت
                </button>
                <button type="button"
                        @click="activeHand = 'minute'"
                        :class="activeHand === 'minute' ? 'bg-[#3C6E71] text-white font-black' : 'text-slate-400 hover:text-white'"
                        class="px-2.5 py-1 rounded-lg text-[11px] transition-colors">
                    دقیقه
                </button>
            </div>
        </div>

        <!-- THE AUTHENTIC WRISTWATCH COMPONENT -->
        <div class="relative py-3 flex flex-col items-center justify-center">
            
            <!-- Top Watch Strap (بند چرمی بالای ساعت) -->
            <div class="w-28 h-5 rounded-t-xl bg-gradient-to-b from-[#201410] via-[#3A2219] to-[#2B1710] border-t-2 border-x-2 border-[#150D0A] shadow-md flex items-center justify-center">
                <div class="w-24 h-px border-t border-dashed border-amber-900/50"></div>
            </div>

            <!-- Wristwatch Bezel & Case (قاب استیل و تیتانیوم ساعت مچی) -->
            <div class="relative w-[260px] h-[260px] rounded-full bg-gradient-to-br from-slate-600 via-slate-800 to-slate-900 p-2 shadow-2xl border-4 border-slate-600 flex items-center justify-center">
                
                <!-- Watch Crown / Winder Knob (پیچ کوک ساعت مچی سمت راست) -->
                <button type="button"
                        @click="toggleHand()"
                        title="پیچ کوک ساعت مچی (کلیک برای سوئیچ ساعت و دقیقه)"
                        class="absolute -end-3 top-1/2 -translate-y-1/2 w-3.5 h-8 bg-gradient-to-r from-slate-400 via-slate-200 to-slate-500 rounded-e-md border border-slate-700 shadow-md flex flex-col items-center justify-center gap-1 z-30 cursor-pointer hover:scale-105 active:scale-95 transition-transform">
                    <span class="w-2 h-0.5 bg-slate-700/60 rounded-full"></span>
                    <span class="w-2 h-0.5 bg-slate-700/60 rounded-full"></span>
                    <span class="w-2 h-0.5 bg-slate-700/60 rounded-full"></span>
                </button>

                <!-- Inner Dial Bezel (حلقه داخلی مدرج) -->
                <div class="relative w-full h-full rounded-full bg-[#0C1318] border-2 border-slate-700/60 shadow-inner flex items-center justify-center overflow-hidden">
                    
                    <!-- 60 Minute Tick Marks (حلقه دقیقه‌ای دور صفحه ساعت) -->
                    <template x-for="i in 12" :key="'tick-' + i">
                        <div class="absolute w-0.5 h-2 bg-slate-600/70 pointer-events-none"
                             :style="`transform: rotate(${i * 30}deg) translateY(-114px);`"></div>
                    </template>

                    <!-- Digital Sub-dial Window (پنجره دیجیتال ساعت مچی) -->
                    <div class="absolute top-12 z-20 px-3 py-1 rounded-lg bg-[#141F27] border border-slate-700 shadow-inner text-center">
                        <div class="font-mono text-xs font-black text-emerald-400 tracking-widest tabular-nums dir-ltr">
                            <span :class="activeHand === 'hour' ? 'underline font-black text-white' : ''" x-text="pad(hour)"></span>
                            <span class="text-emerald-400 animate-pulse">:</span>
                            <span :class="activeHand === 'minute' ? 'underline font-black text-white' : ''" x-text="pad(minute)"></span>
                        </div>
                    </div>

                    <!-- Center Pivot Pin (پین یا نگین متالیک مرکزی ساعت مچی) -->
                    <div class="absolute z-30 w-3.5 h-3.5 rounded-full bg-gradient-to-br from-amber-200 via-amber-400 to-amber-600 border border-white shadow-md pointer-events-none"></div>

                    <!-- Hour Watch Hand (عقربه ساعت‌شمار) -->
                    <div class="absolute z-20 w-1 rounded-full bg-white shadow-md pointer-events-none origin-bottom transition-transform duration-200"
                         :style="`height: 58px; transform: rotate(${hourAngle}deg); bottom: 119px; left: 118.5px;`">
                        <div class="absolute -top-1.5 -left-1 w-3 h-3 rounded-full bg-white"></div>
                    </div>

                    <!-- Minute Watch Hand (عقربه دقیقه‌شمار - باریک‌تر و کشیده‌تر با رنگ سبز کافه) -->
                    <div class="absolute z-20 w-0.5 rounded-full bg-emerald-400 shadow-md pointer-events-none origin-bottom transition-transform duration-200"
                         :style="`height: 85px; transform: rotate(${minuteAngle}deg); bottom: 119px; left: 119px;`">
                        <div class="absolute -top-1.5 -left-1.5 w-3.5 h-3.5 rounded-full bg-emerald-400 opacity-90"></div>
                    </div>

                    <!-- HOUR MODE: 24 Numbers Placed On Watch Dial Face -->
                    <div x-show="activeHand === 'hour'" class="absolute inset-0 z-10">
                        <!-- Outer Hour Ring: 12 to 23 -->
                        <template x-for="item in outerHours" :key="'wh-out-' + item.h">
                            <button type="button"
                                    @click="selectHour(item.h)"
                                    :style="`left: ${item.x}px; top: ${item.y}px;`"
                                    :class="hour === item.h ? 'bg-emerald-500 text-white font-black scale-110 shadow-lg ring-2 ring-white z-30' : 'bg-transparent text-slate-300 hover:bg-white/20 font-bold z-20'"
                                    class="absolute -translate-x-1/2 -translate-y-1/2 w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-mono tabular-nums transition-transform">
                                <span x-text="pad(item.h)"></span>
                            </button>
                        </template>

                        <!-- Inner Hour Ring: 00 to 11 -->
                        <template x-for="item in innerHours" :key="'wh-in-' + item.h">
                            <button type="button"
                                    @click="selectHour(item.h)"
                                    :style="`left: ${item.x}px; top: ${item.y}px;`"
                                    :class="hour === item.h ? 'bg-emerald-500 text-white font-black scale-110 shadow-lg ring-2 ring-white z-30' : 'bg-transparent text-slate-400 hover:bg-white/20 font-bold z-20'"
                                    class="absolute -translate-x-1/2 -translate-y-1/2 w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-mono tabular-nums transition-transform">
                                <span x-text="pad(item.h)"></span>
                            </button>
                        </template>
                    </div>

                    <!-- MINUTE MODE: Minute Numbers Placed On Watch Dial Face -->
                    <div x-show="activeHand === 'minute'" class="absolute inset-0 z-10">
                        <template x-for="item in minuteSteps" :key="'wh-m-' + item.m">
                            <button type="button"
                                    @click="selectMinute(item.m)"
                                    :style="`left: ${item.x}px; top: ${item.y}px;`"
                                    :class="minute === item.m ? 'bg-emerald-500 text-white font-black scale-110 shadow-lg ring-2 ring-white z-30' : 'bg-transparent text-slate-200 hover:bg-white/20 font-bold z-20'"
                                    class="absolute -translate-x-1/2 -translate-y-1/2 w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-mono tabular-nums transition-transform">
                                <span x-text="pad(item.m)"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Bottom Watch Strap (بند چرمی پایین ساعت) -->
            <div class="w-28 h-5 rounded-b-xl bg-gradient-to-t from-[#201410] via-[#3A2219] to-[#2B1710] border-b-2 border-x-2 border-[#150D0A] shadow-md flex items-center justify-center">
                <div class="w-24 h-px border-t border-dashed border-amber-900/50"></div>
            </div>
        </div>

        <!-- Minute Steppers / Fine Adjustment -->
        <div class="pt-1 flex items-center justify-between text-[11px] text-slate-400">
            <span class="text-[10px] font-bold">تنظیم دقیق دقیقه:</span>
            <div class="flex items-center gap-1 dir-ltr">
                <button type="button" @click="stepMinute(-5)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold tabular-nums">-۵</button>
                <button type="button" @click="stepMinute(-1)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold tabular-nums">-۱</button>
                <span class="px-1.5 font-mono font-black text-xs text-emerald-400 tabular-nums" x-text="pad(minute)"></span>
                <button type="button" @click="stepMinute(1)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold tabular-nums">+۱</button>
                <button type="button" @click="stepMinute(5)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold tabular-nums">+۵</button>
            </div>
        </div>

        <!-- Quick Presets -->
        @if($presets)
            <div class="mt-2.5 pt-2 border-t border-slate-800">
                <div class="grid grid-cols-5 gap-1 text-[10px] font-mono font-bold text-center">
                    <button type="button" @click="setPreset('08:00')" class="py-1 rounded bg-slate-800/80 hover:bg-emerald-600 text-slate-300 hover:text-white transition-colors">08:00</button>
                    <button type="button" @click="setPreset('12:00')" class="py-1 rounded bg-slate-800/80 hover:bg-emerald-600 text-slate-300 hover:text-white transition-colors">12:00</button>
                    <button type="button" @click="setPreset('16:00')" class="py-1 rounded bg-slate-800/80 hover:bg-emerald-600 text-slate-300 hover:text-white transition-colors">16:00</button>
                    <button type="button" @click="setPreset('20:00')" class="py-1 rounded bg-slate-800/80 hover:bg-emerald-600 text-slate-300 hover:text-white transition-colors">20:00</button>
                    <button type="button" @click="setPreset('23:00')" class="py-1 rounded bg-slate-800/80 hover:bg-emerald-600 text-slate-300 hover:text-white transition-colors">23:00</button>
                </div>
            </div>
        @endif

        <!-- Footer Actions -->
        <div class="mt-3 pt-2 border-t border-slate-800 flex items-center justify-between gap-2">
            <button type="button"
                    @click="isOpen = false"
                    class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-colors">
                انصراف
            </button>

            <button type="button"
                    @click="confirmTime()"
                    class="px-5 py-1.5 rounded-xl bg-[#3C6E71] hover:bg-emerald-600 text-white font-black shadow-md transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>تایید زمان ساعت</span>
            </button>
        </div>
    </div>
</div>

@once
<script>
function persianWristwatchPicker(config) {
    return {
        isOpen: false,
        name: config.name || 'time',
        value: config.initialValue || '',
        hour: 12,
        minute: 0,
        activeHand: 'hour', // 'hour' or 'minute'
        dialCenter: 120, // center of 240px dial
        outerRadius: 90,
        innerRadius: 58,
        minuteRadius: 88,

        init() {
            if (this.value) {
                const parts = this.value.split(':');
                if (parts.length >= 2) {
                    this.hour = Math.min(23, Math.max(0, parseInt(parts[0]) || 0));
                    this.minute = Math.min(59, Math.max(0, parseInt(parts[1]) || 0));
                    this.value = `${this.pad(this.hour)}:${this.pad(this.minute)}`;
                }
            }
        },

        pad(n) {
            return String(n).padStart(2, '0');
        },

        get formattedDisplay() {
            if (!this.value) return '';
            return this.value;
        },

        openPicker() {
            if (this.value) {
                const parts = this.value.split(':');
                this.hour = parseInt(parts[0]) || 0;
                this.minute = parseInt(parts[1]) || 0;
            } else {
                const d = new Date();
                this.hour = d.getHours();
                this.minute = Math.round(d.getMinutes() / 5) * 5 % 60;
            }
            this.activeHand = 'hour';
            this.isOpen = true;
        },

        toggleHand() {
            this.activeHand = (this.activeHand === 'hour') ? 'minute' : 'hour';
        },

        get hourAngle() {
            // Standard watch hour angle: 12 hours cycle = 360 degrees (30 deg/hour)
            // Plus minute contribution for realistic watch hand!
            const h = this.hour % 12;
            return (h * 30) + (this.minute * 0.5);
        },

        get minuteAngle() {
            // Minute angle: 60 minutes = 360 degrees (6 deg/minute)
            return this.minute * 6;
        },

        get outerHours() {
            // Outer Ring: 12 to 23
            const list = [];
            for (let i = 0; i < 12; i++) {
                const h = (i === 0) ? 12 : 12 + i;
                const rad = (i * 30 - 90) * (Math.PI / 180);
                list.push({
                    h: h,
                    x: Math.round(this.dialCenter + this.outerRadius * Math.cos(rad)),
                    y: Math.round(this.dialCenter + this.outerRadius * Math.sin(rad))
                });
            }
            return list;
        },

        get innerHours() {
            // Inner Ring: 00 to 11
            const list = [];
            for (let i = 0; i < 12; i++) {
                const h = i;
                const rad = (i * 30 - 90) * (Math.PI / 180);
                list.push({
                    h: h,
                    x: Math.round(this.dialCenter + this.innerRadius * Math.cos(rad)),
                    y: Math.round(this.dialCenter + this.innerRadius * Math.sin(rad))
                });
            }
            return list;
        },

        get minuteSteps() {
            // 00, 05, 10, ..., 55
            const list = [];
            for (let i = 0; i < 12; i++) {
                const m = i * 5;
                const rad = (i * 30 - 90) * (Math.PI / 180);
                list.push({
                    m: m,
                    x: Math.round(this.dialCenter + this.minuteRadius * Math.cos(rad)),
                    y: Math.round(this.dialCenter + this.minuteRadius * Math.sin(rad))
                });
            }
            return list;
        },

        selectHour(h) {
            this.hour = h;
            // Smoothly auto switch to minute hand
            setTimeout(() => {
                this.activeHand = 'minute';
            }, 180);
        },

        selectMinute(m) {
            this.minute = m;
        },

        stepMinute(delta) {
            let m = this.minute + delta;
            if (m < 0) m = 59;
            if (m > 59) m = 0;
            this.minute = m;
        },

        setPreset(presetStr) {
            const parts = presetStr.split(':');
            this.hour = parseInt(parts[0]) || 0;
            this.minute = parseInt(parts[1]) || 0;
            this.confirmTime();
        },

        clearTime() {
            this.value = '';
            this.isOpen = false;
        },

        confirmTime() {
            this.value = `${this.pad(this.hour)}:${this.pad(this.minute)}`;
            this.isOpen = false;
            this.$dispatch('time-selected', { name: this.name, value: this.value });
        }
    };
}
</script>
@endonce
