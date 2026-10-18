@props([
    'name' => 'datetime',
    'value' => null,
    'label' => null,
    'placeholder' => null,
    'required' => false,
    'minYear' => 1320,
    'maxYear' => 1410,
    'showTime' => true,
])

@php
    $placeholder = $placeholder ?? ($showTime ? 'انتخاب تاریخ و زمان...' : 'انتخاب تاریخ شمسی...');
@endphp

<div x-data="persianDateTimePicker({
        initialValue: '{{ $value ?? '' }}',
        name: '{{ $name }}',
        minYear: {{ $minYear }},
        maxYear: {{ $maxYear }},
        showTime: {{ $showTime ? 'true' : 'false' }}
    })"
    class="relative w-full text-right select-none"
    dir="rtl">

    @if($label)
        <label class="block text-xs font-bold text-[#19242B] mb-1.5 flex items-center justify-between">
            <span>{{ $label }}</span>
            <template x-if="displayFa">
                <span class="text-[11px] font-mono font-black text-[#3C6E71] bg-[#3C6E71]/10 px-2.5 py-0.5 rounded-lg border border-[#3C6E71]/20 tabular-nums" x-text="displayFa"></span>
            </template>
        </label>
    @endif

    <!-- Hidden Input for Laravel Form Submission (Gregorian YYYY-MM-DDTHH:mm) -->
    <input type="hidden"
           name="{{ $name }}"
           :value="gregorianIsoValue"
           @if($required) required @endif>

    <!-- Trigger Button -->
    <div class="relative">
        <button type="button"
                @click="openPicker()"
                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border border-[#CBD5E1] hover:border-[#3C6E71] rounded-xl text-xs font-bold text-[#19242B] transition-all min-h-[44px] shadow-2xs focus:ring-2 focus:ring-[#3C6E71]/20 focus:outline-none btn-tactile">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#3C6E71]/10 text-[#3C6E71] flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="3" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-width="2" d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                </div>
                <div class="flex items-center gap-1.5">
                    <span x-text="displayFa ? displayFa : '{{ $placeholder }}'"
                          :class="displayFa ? 'text-[#19242B] font-bold text-xs' : 'text-[#64748B]'"></span>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                <template x-if="gregorianIsoValue">
                    <span @click.stop="clearValue()"
                          class="w-6 h-6 rounded-full hover:bg-rose-50 text-slate-400 hover:text-rose-500 flex items-center justify-center text-xs transition-colors"
                          title="پاک کردن">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </template>
                <svg class="w-4 h-4 text-[#64748B] transition-transform duration-200" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
    </div>

    <!-- DateTime Combined Popover Modal -->
    <div x-show="isOpen"
         @click.outside="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute z-50 mt-2 start-0 w-[325px] sm:w-[350px] bg-slate-900 border border-slate-700 rounded-3xl shadow-2xl p-4 text-xs select-none backdrop-blur-md"
         style="display: none;">

        <!-- Header Tabs: Calendar Sheet vs Wristwatch -->
        <template x-if="showTime">
            <div class="bg-slate-800 text-white p-2.5 rounded-2xl flex items-center justify-between mb-3 border border-slate-700">
                <div class="flex items-center gap-1.5 w-full">
                    <!-- Date Tab (Calendar) -->
                    <button type="button"
                            @click="activeTab = 'date'"
                            :class="activeTab === 'date' ? 'bg-[#3C6E71] text-white font-black shadow-md' : 'bg-slate-700/60 text-slate-300 hover:bg-slate-700'"
                            class="flex-1 py-2 px-2 rounded-xl text-center text-xs transition-all flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <span x-text="selectedDateText || 'تقویم روز'"></span>
                    </button>

                    <!-- Time Tab (Wristwatch) -->
                    <button type="button"
                            @click="activeTab = 'time'"
                            :class="activeTab === 'time' ? 'bg-[#3C6E71] text-white font-black shadow-md' : 'bg-slate-700/60 text-slate-300 hover:bg-slate-700'"
                            class="flex-1 py-2 px-2 rounded-xl text-center text-xs transition-all flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="7" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3l2 1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5V3h6v2M9 19v2h6v-2"/></svg>
                        <span class="font-mono dir-ltr" x-text="`ساعت ${pad(hour)}:${pad(minute)}`"></span>
                    </button>
                </div>
            </div>
        </template>

        <!-- 1. CALENDAR SHEET VIEW -->
        <div x-show="activeTab === 'date'" class="bg-white p-3 rounded-2xl shadow-inner text-[#19242B]">
            <!-- Calendar Header (Month & Year Prominent Selectors) -->
            <div class="bg-[#19242B] text-white p-2.5 rounded-xl flex items-center justify-between mb-2">
                <!-- Year Button -->
                <button type="button"
                        @click="toggleView('years')"
                        :class="calendarView === 'years' ? 'bg-[#3C6E71] ring-2 ring-emerald-400' : 'bg-white/10 hover:bg-white/20'"
                        class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold flex items-center gap-1 transition-all">
                    <span class="text-[10px] text-slate-300">سال:</span>
                    <span x-text="viewYear" class="text-emerald-300"></span>
                    <svg class="w-3 h-3 text-emerald-300 transition-transform duration-200" :class="calendarView === 'years' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <!-- Month Button -->
                <button type="button"
                        @click="toggleView('months')"
                        :class="calendarView === 'months' ? 'bg-[#3C6E71] ring-2 ring-emerald-400' : 'bg-white/10 hover:bg-white/20'"
                        class="px-2.5 py-1 rounded-lg text-xs font-bold flex items-center gap-1 transition-all">
                    <span class="text-[10px] text-slate-300">ماه:</span>
                    <span x-text="months[viewMonth - 1]" class="text-emerald-300"></span>
                    <svg class="w-3 h-3 text-emerald-300 transition-transform duration-200" :class="calendarView === 'months' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <!-- Month Steppers -->
                <div class="flex items-center gap-1">
                    <button type="button" @click="prevMonth()" class="w-6 h-6 rounded bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-[10px]">&lsaquo;</button>
                    <button type="button" @click="nextMonth()" class="w-6 h-6 rounded bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-[10px]">&rsaquo;</button>
                </div>
            </div>

            <!-- Days Grid -->
            <div x-show="calendarView === 'days'">
                <div class="grid grid-cols-7 gap-1 text-center font-bold text-slate-400 text-[10px] mb-1">
                    <span>ش</span><span>ی</span><span>د</span><span>س</span><span>چ</span><span>پ</span><span class="text-rose-500">ج</span>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center">
                    <template x-for="blank in firstWeekdayOfMonth" :key="'blank-' + blank">
                        <div class="h-7"></div>
                    </template>
                    <template x-for="day in daysInCurrentMonth" :key="'day-' + day">
                        <button type="button"
                                @click="selectDay(day)"
                                :class="{
                                    'bg-[#3C6E71] text-white font-black scale-105 shadow-sm': isSelected(day),
                                    'bg-teal-50 text-[#3C6E71] font-bold border border-teal-200': isToday(day) && !isSelected(day),
                                    'text-slate-700 hover:bg-slate-100 font-medium': !isSelected(day) && !isToday(day),
                                    'text-rose-600': isFriday(day) && !isSelected(day)
                                }"
                                class="h-7 w-7 mx-auto rounded-lg flex items-center justify-center text-[11px] tabular-nums font-mono transition-all">
                            <span x-text="day"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Year Picker Grid with Decade Navigation -->
            <div x-show="calendarView === 'years'" class="space-y-2 py-1">
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-700 bg-slate-100 p-1.5 rounded-lg">
                    <button type="button" @click="stepDecade(-10)" class="px-2 py-0.5 rounded bg-white text-slate-800 text-[10px]">‹ ۱۰ سال قبل</button>
                    <span class="font-mono text-xs font-bold text-[#3C6E71]" x-text="`${yearPageOffset} تا ${yearPageOffset + 11}`"></span>
                    <button type="button" @click="stepDecade(10)" class="px-2 py-0.5 rounded bg-white text-slate-800 text-[10px]">۱۰ سال بعد ›</button>
                </div>
                <div class="grid grid-cols-3 gap-1.5">
                    <template x-for="y in visibleYearsPage" :key="'y-pg-' + y">
                        <button type="button" @click="pickYear(y)" :class="viewYear === y ? 'bg-[#3C6E71] text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-800'" class="py-1.5 rounded-lg text-xs font-mono tabular-nums">
                            <span x-text="y"></span>
                        </button>
                    </template>
                </div>
                <!-- Decade pills -->
                <div class="pt-1 border-t border-slate-100 flex flex-wrap gap-1 justify-center text-[10px] font-mono">
                    <template x-for="dec in availableDecades" :key="'dt-dec-' + dec">
                        <button type="button" @click="jumpDecade(dec)" :class="yearPageOffset === dec ? 'bg-[#3C6E71] text-white' : 'bg-slate-100 text-slate-700'" class="px-1.5 py-0.5 rounded">
                            <span x-text="dec"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Month Picker Grid -->
            <div x-show="calendarView === 'months'" class="py-1 space-y-1.5">
                <div class="flex items-center justify-between text-[10px] text-slate-500 bg-slate-100 p-1 rounded-lg">
                    <span>انتخاب ماه سال:</span>
                    <button type="button" @click="calendarView = 'days'" class="text-[#3C6E71] font-bold">بازگشت</button>
                </div>
                <div class="grid grid-cols-3 gap-1.5">
                    <template x-for="(mName, mIdx) in months" :key="'m-pg-' + mIdx">
                        <button type="button" @click="pickMonth(mIdx + 1)" :class="viewMonth === (mIdx + 1) ? 'bg-[#3C6E71] text-white font-bold' : 'bg-slate-100 hover:bg-slate-200 text-slate-800'" class="py-2 rounded-lg text-xs font-bold">
                            <span x-text="mName"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- 2. WRISTWATCH VIEW (ساعت مچی ۲۴ ساعته) -->
        <div x-show="showTime && activeTab === 'time'" class="py-1 flex flex-col items-center justify-center text-white">
            <div class="flex items-center justify-between w-full mb-1.5 text-[11px] px-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-sm font-black text-emerald-400 tabular-nums dir-ltr bg-slate-800 px-2 py-0.5 rounded-lg border border-slate-700">
                        <span x-text="pad(hour)"></span>:<span x-text="pad(minute)"></span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-bold" x-text="activeHand === 'hour' ? 'عقربه ساعت' : 'عقربه دقیقه'"></span>
                </div>
                <div class="flex items-center gap-1 bg-slate-800 p-0.5 rounded-lg">
                    <button type="button" @click="activeHand = 'hour'" :class="activeHand === 'hour' ? 'bg-[#3C6E71] text-white font-bold' : 'text-slate-400'" class="px-2 py-0.5 rounded text-[10px]">ساعت</button>
                    <button type="button" @click="activeHand = 'minute'" :class="activeHand === 'minute' ? 'bg-[#3C6E71] text-white font-bold' : 'text-slate-400'" class="px-2 py-0.5 rounded text-[10px]">دقیقه</button>
                </div>
            </div>

            <!-- Mini Wristwatch Design -->
            <div class="w-24 h-3.5 rounded-t-lg bg-gradient-to-b from-[#1C120C] to-[#2E1A11]"></div>
            <div class="relative w-[230px] h-[230px] rounded-full bg-gradient-to-br from-slate-500 via-slate-800 to-slate-900 p-2 shadow-2xl border-4 border-slate-600 flex items-center justify-center">
                <!-- Crown button -->
                <button type="button" @click="toggleHand()" title="کوک ساعت" class="absolute -end-3 top-1/2 -translate-y-1/2 w-3.5 h-7 bg-slate-300 rounded-e border border-slate-600 z-30"></button>
                
                <!-- Dial -->
                <div class="relative w-full h-full rounded-full bg-[#090E12] border border-slate-700 shadow-inner flex items-center justify-center overflow-hidden">
                    <!-- Pivot -->
                    <div class="absolute z-30 w-3.5 h-3.5 rounded-full bg-amber-400 border border-white" style="top: 98px; left: 98px;"></div>
                    <!-- Hour Hand -->
                    <div class="absolute z-20 w-1 bg-white origin-bottom pointer-events-none transition-transform duration-200"
                         :style="`height: 48px; transform: rotate(${hourAngle}deg); bottom: 105px; left: 104.5px;`"></div>
                    <!-- Minute Hand -->
                    <div class="absolute z-20 w-0.5 bg-emerald-400 origin-bottom pointer-events-none transition-transform duration-200"
                         :style="`height: 72px; transform: rotate(${minuteAngle}deg); bottom: 105px; left: 104.75px;`"></div>

                    <!-- Hours -->
                    <div x-show="activeHand === 'hour'" class="absolute inset-0 z-10">
                        <template x-for="item in outerHours" :key="'dt-wh-out-' + item.h">
                            <button type="button" @click="selectHour(item.h)" :style="`left: ${item.x}px; top: ${item.y}px;`"
                                    :class="hour === item.h ? 'bg-emerald-500 text-white font-black scale-110 ring-2 ring-white z-30' : 'text-slate-200 hover:bg-white/20 font-bold z-20'"
                                    class="absolute -translate-x-1/2 -translate-y-1/2 w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-mono tabular-nums">
                                <span x-text="pad(item.h)"></span>
                            </button>
                        </template>
                        <template x-for="item in innerHours" :key="'dt-wh-in-' + item.h">
                            <button type="button" @click="selectHour(item.h)" :style="`left: ${item.x}px; top: ${item.y}px;`"
                                    :class="hour === item.h ? 'bg-emerald-500 text-white font-black scale-110 ring-2 ring-white z-30' : 'text-slate-400 hover:bg-white/20 font-bold z-20'"
                                    class="absolute -translate-x-1/2 -translate-y-1/2 w-4 h-4 rounded-full flex items-center justify-center text-[8px] font-mono tabular-nums">
                                <span x-text="pad(item.h)"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Minutes -->
                    <div x-show="activeHand === 'minute'" class="absolute inset-0 z-10">
                        <template x-for="item in minuteSteps" :key="'dt-wh-m-' + item.m">
                            <button type="button" @click="selectMinute(item.m)" :style="`left: ${item.x}px; top: ${item.y}px;`"
                                    :class="minute === item.m ? 'bg-emerald-500 text-white font-black scale-110 ring-2 ring-white z-30' : 'text-slate-200 hover:bg-white/20 font-bold z-20'"
                                    class="absolute -translate-x-1/2 -translate-y-1/2 w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-mono tabular-nums">
                                <span x-text="pad(item.m)"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            <div class="w-24 h-3.5 rounded-b-lg bg-gradient-to-t from-[#1C120C] to-[#2E1A11]"></div>

            <!-- Stepper -->
            <div class="mt-2 flex items-center justify-center gap-1 dir-ltr text-[11px]">
                <button type="button" @click="stepMinute(-5)" class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">-۵</button>
                <button type="button" @click="stepMinute(-1)" class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">-۱</button>
                <span class="px-1.5 font-mono font-black text-xs text-emerald-400" x-text="pad(minute)"></span>
                <button type="button" @click="stepMinute(1)" class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">+۱</button>
                <button type="button" @click="stepMinute(5)" class="px-2 py-0.5 rounded bg-slate-800 text-slate-300">+۵</button>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="mt-3 pt-2.5 border-t border-slate-700 flex items-center justify-between gap-2 text-white">
            <button type="button" @click="isOpen = false" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-colors">
                انصراف
            </button>

            <button type="button" @click="confirmAll()" class="px-5 py-1.5 rounded-xl bg-[#3C6E71] hover:bg-emerald-600 text-white font-black shadow-md transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span x-text="showTime ? 'تایید تاریخ و ساعت' : 'تایید تاریخ'"></span>
            </button>
        </div>
    </div>
</div>

@once
<script>
function persianDateTimePicker(config) {
    return {
        isOpen: false,
        name: config.name || 'datetime',
        showTime: config.showTime !== false,
        gregorianIsoValue: config.initialValue || '',
        displayFa: '',
        activeTab: 'date', // 'date' or 'time'
        calendarView: 'days', // 'days', 'years', 'months'
        activeHand: 'hour', // 'hour' or 'minute'
        selectedYear: null,
        selectedMonth: null,
        selectedDay: null,
        hour: 12,
        minute: 0,
        viewYear: 1403,
        viewMonth: 1,
        minYear: config.minYear || 1320,
        maxYear: config.maxYear || 1410,
        yearPageOffset: 0,
        months: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'],
        dialCenter: 105,
        outerRadius: 78,
        innerRadius: 50,
        minuteRadius: 76,

        init() {
            const today = this.gregorianToJalali(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate());
            this.viewYear = today[0];
            this.viewMonth = today[1];

            if (this.gregorianIsoValue) {
                this.parseInitialIso(this.gregorianIsoValue);
            }
        },

        pad(n) {
            return String(n).padStart(2, '0');
        },

        parseInitialIso(str) {
            try {
                const clean = str.replace('T', ' ');
                const [datePart, timePart] = clean.split(' ');
                if (datePart) {
                    const [gy, gm, gd] = datePart.split('-').map(Number);
                    if (gy && gm && gd) {
                        const j = this.gregorianToJalali(gy, gm, gd);
                        this.selectedYear = j[0];
                        this.selectedMonth = j[1];
                        this.selectedDay = j[2];
                        this.viewYear = j[0];
                        this.viewMonth = j[1];
                    }
                }
                if (timePart) {
                    const [th, tm] = timePart.split(':').map(Number);
                    this.hour = th || 0;
                    this.minute = tm || 0;
                }
                this.updateDisplay();
            } catch (e) {}
        },

        get selectedDateText() {
            if (this.selectedYear && this.selectedMonth && this.selectedDay) {
                return `${this.selectedYear}/${this.pad(this.selectedMonth)}/${this.pad(this.selectedDay)}`;
            }
            return '';
        },

        openPicker() {
            if (!this.selectedYear) {
                const today = this.gregorianToJalali(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate());
                this.selectedYear = today[0];
                this.selectedMonth = today[1];
                this.selectedDay = today[2];
            }
            this.isOpen = true;
            this.activeTab = 'date';
            this.calendarView = 'days';
            this.yearPageOffset = Math.floor(this.viewYear / 10) * 10;
        },

        toggleView(view) {
            this.calendarView = (this.calendarView === view) ? 'days' : view;
            if (view === 'years') {
                this.yearPageOffset = Math.floor(this.viewYear / 10) * 10;
            }
        },

        get visibleYearsPage() {
            const start = this.yearPageOffset;
            const years = [];
            for (let i = 0; i < 12; i++) {
                const y = start + i;
                if (y >= this.minYear && y <= this.maxYear) years.push(y);
            }
            return years;
        },

        get availableDecades() {
            const decades = [];
            const minDec = Math.floor(this.minYear / 10) * 10;
            const maxDec = Math.floor(this.maxYear / 10) * 10;
            for (let d = maxDec; d >= minDec; d -= 10) {
                decades.push(d);
            }
            return decades;
        },

        stepDecade(delta) {
            let next = this.yearPageOffset + delta;
            const minDec = Math.floor(this.minYear / 10) * 10;
            const maxDec = Math.floor(this.maxYear / 10) * 10;
            if (next < minDec) next = minDec;
            if (next > maxDec) next = maxDec;
            this.yearPageOffset = next;
        },

        jumpDecade(targetDecade) {
            this.yearPageOffset = targetDecade;
        },

        pickYear(y) {
            this.viewYear = y;
            this.calendarView = 'days';
        },

        pickMonth(m) {
            this.viewMonth = m;
            this.calendarView = 'days';
        },

        changeYear(delta) {
            const target = this.viewYear + delta;
            if (target >= this.minYear && target <= this.maxYear) this.viewYear = target;
        },

        prevMonth() {
            if (this.viewMonth === 1) { this.viewMonth = 12; this.changeYear(-1); }
            else { this.viewMonth--; }
        },

        nextMonth() {
            if (this.viewMonth === 12) { this.viewMonth = 1; this.changeYear(1); }
            else { this.viewMonth++; }
        },

        get daysInCurrentMonth() {
            const m = parseInt(this.viewMonth);
            if (m <= 6) return 31;
            if (m <= 11) return 30;
            return this.isLeapJalaliYear(this.viewYear) ? 30 : 29;
        },

        get firstWeekdayOfMonth() {
            const g = this.jalaliToGregorian(this.viewYear, this.viewMonth, 1);
            const d = new Date(g[0], g[1] - 1, g[2]);
            return (d.getDay() + 1) % 7;
        },

        isFriday(day) {
            const g = this.jalaliToGregorian(this.viewYear, this.viewMonth, day);
            const d = new Date(g[0], g[1] - 1, g[2]);
            return d.getDay() === 5;
        },

        isToday(day) {
            const t = new Date();
            const tj = this.gregorianToJalali(t.getFullYear(), t.getMonth() + 1, t.getDate());
            return tj[0] === this.viewYear && tj[1] === this.viewMonth && tj[2] === day;
        },

        isSelected(day) {
            return this.selectedYear === this.viewYear && this.selectedMonth === this.viewMonth && this.selectedDay === day;
        },

        selectDay(day) {
            this.selectedYear = parseInt(this.viewYear);
            this.selectedMonth = parseInt(this.viewMonth);
            this.selectedDay = parseInt(day);
            if (this.showTime) {
                setTimeout(() => { this.activeTab = 'time'; }, 180);
            } else {
                this.confirmAll();
            }
        },

        // Wristwatch Angles & Positioning
        toggleHand() {
            this.activeHand = (this.activeHand === 'hour') ? 'minute' : 'hour';
        },

        get hourAngle() {
            const h = this.hour % 12;
            return (h * 30) + (this.minute * 0.5);
        },

        get minuteAngle() {
            return this.minute * 6;
        },

        get outerHours() {
            const list = [];
            for (let i = 0; i < 12; i++) {
                const h = (i === 0) ? 12 : 12 + i;
                const rad = (i * 30 - 90) * (Math.PI / 180);
                list.push({ h, x: Math.round(this.dialCenter + this.outerRadius * Math.cos(rad)), y: Math.round(this.dialCenter + this.outerRadius * Math.sin(rad)) });
            }
            return list;
        },

        get innerHours() {
            const list = [];
            for (let i = 0; i < 12; i++) {
                const h = i;
                const rad = (i * 30 - 90) * (Math.PI / 180);
                list.push({ h, x: Math.round(this.dialCenter + this.innerRadius * Math.cos(rad)), y: Math.round(this.dialCenter + this.innerRadius * Math.sin(rad)) });
            }
            return list;
        },

        get minuteSteps() {
            const list = [];
            for (let i = 0; i < 12; i++) {
                const m = i * 5;
                const rad = (i * 30 - 90) * (Math.PI / 180);
                list.push({ m, x: Math.round(this.dialCenter + this.minuteRadius * Math.cos(rad)), y: Math.round(this.dialCenter + this.minuteRadius * Math.sin(rad)) });
            }
            return list;
        },

        selectHour(h) {
            this.hour = h;
            setTimeout(() => { this.activeHand = 'minute'; }, 180);
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

        clearValue() {
            this.selectedYear = null;
            this.selectedMonth = null;
            this.selectedDay = null;
            this.gregorianIsoValue = '';
            this.displayFa = '';
            this.isOpen = false;
        },

        confirmAll() {
            if (!this.selectedYear || !this.selectedMonth || !this.selectedDay) {
                const today = this.gregorianToJalali(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate());
                this.selectedYear = today[0];
                this.selectedMonth = today[1];
                this.selectedDay = today[2];
            }
            const g = this.jalaliToGregorian(this.selectedYear, this.selectedMonth, this.selectedDay);
            const gy = g[0];
            const gm = String(g[1]).padStart(2, '0');
            const gd = String(g[2]).padStart(2, '0');
            if (this.showTime) {
                const th = this.pad(this.hour);
                const tm = this.pad(this.minute);
                this.gregorianIsoValue = `${gy}-${gm}-${gd}T${th}:${tm}`;
            } else {
                this.gregorianIsoValue = `${gy}-${gm}-${gd}`;
            }
            this.updateDisplay();
            this.isOpen = false;
        },

        updateDisplay() {
            if (this.selectedYear && this.selectedMonth && this.selectedDay) {
                if (this.showTime) {
                    const th = this.pad(this.hour);
                    const tm = this.pad(this.minute);
                    this.displayFa = `${this.selectedYear}/${this.pad(this.selectedMonth)}/${this.pad(this.selectedDay)} ساعت ${th}:${tm}`;
                } else {
                    this.displayFa = `${this.selectedYear}/${this.pad(this.selectedMonth)}/${this.pad(this.selectedDay)}`;
                }
            } else {
                this.displayFa = '';
            }
        },

        isLeapJalaliYear(jy) {
            const breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
            const bl = breaks.length;
            let jp = breaks[0];
            let jump = 0;
            if (jy < jp || jy >= breaks[bl - 1]) return false;
            for (let i = 1; i < bl; i++) {
                const jm = breaks[i];
                if (jy < jm) break;
                jp = jm;
            }
            let n = jy - jp;
            let leap = (((n + 1) % 33) - 1) % 4;
            if (leap === -1) leap = 4;
            return leap === 0;
        },

        jalaliToGregorian(jy, jm, jd) {
            jy = parseInt(jy) - 979;
            jm = parseInt(jm) - 1;
            jd = parseInt(jd) - 1;
            let jDayNo = 365 * jy + Math.floor(jy / 33) * 8 + Math.floor(((jy % 33) + 3) / 4);
            for (let i = 0; i < jm; ++i) jDayNo += (i < 6) ? 31 : 30;
            jDayNo += jd;
            let gDayNo = jDayNo + 79;
            let gy = 1600 + 400 * Math.floor(gDayNo / 146097);
            gDayNo %= 146097;
            if (gDayNo >= 36525) {
                gDayNo--;
                gy += 100 * Math.floor(gDayNo / 36524);
                gDayNo %= 36524;
                if (gDayNo >= 365) gDayNo++;
            }
            gy += 4 * Math.floor(gDayNo / 1461);
            gDayNo %= 1461;
            if (gDayNo >= 366) {
                gDayNo--;
                gy += Math.floor(gDayNo / 365);
                gDayNo %= 365;
            }
            const gDaysInMonth = [31, (gy % 4 === 0 && gy % 100 !== 0) || (gy % 400 === 0) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            let gm = 0;
            for (let i = 0; i < 12; i++) {
                if (gDayNo < gDaysInMonth[i]) { gm = i + 1; break; }
                gDayNo -= gDaysInMonth[i];
            }
            return [gy, gm, gDayNo + 1];
        },

        gregorianToJalali(gy, gm, gd) {
            gy = parseInt(gy) - 1600;
            gm = parseInt(gm) - 1;
            gd = parseInt(gd) - 1;
            const gDaysInMonth = [31, (gy % 4 === 0 && gy % 100 !== 0) || (gy % 400 === 0) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            let gDayNo = 365 * gy + Math.floor((gy + 3) / 4) - Math.floor((gy + 99) / 100) + Math.floor((gy + 399) / 400);
            for (let i = 0; i < gm; ++i) gDayNo += gDaysInMonth[i];
            gDayNo += gd;
            let jDayNo = gDayNo - 79;
            let jNp = Math.floor(jDayNo / 12053);
            jDayNo %= 12053;
            let jy = 979 + 33 * jNp + 4 * Math.floor(jDayNo / 1461);
            jDayNo %= 1461;
            if (jDayNo >= 366) {
                jy += Math.floor((jDayNo - 1) / 365);
                jDayNo = (jDayNo - 1) % 365;
            }
            let jm = 0;
            for (let i = 0; i < 11 && jDayNo >= (i < 6 ? 31 : 30); ++i) {
                jDayNo -= (i < 6 ? 31 : 30);
                jm = i + 1;
            }
            return [jy, jm + 1, jDayNo + 1];
        }
    };
}
</script>
@endonce
