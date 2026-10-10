@props([
    'name' => 'date',
    'value' => null,
    'label' => null,
    'placeholder' => 'انتخاب تاریخ شمسی...',
    'required' => false,
    'minYear' => 1320,
    'maxYear' => 1410,
])

<div x-data="persianDatePicker({
        initialValue: '{{ $value ?? '' }}',
        name: '{{ $name }}',
        minYear: {{ $minYear }},
        maxYear: {{ $maxYear }}
    })"
    class="relative w-full text-right select-none"
    dir="rtl">

    @if($label)
        <label class="block text-xs font-bold text-[#19242B] mb-1.5 flex items-center justify-between">
            <span>{{ $label }}</span>
            <template x-if="jalaliDisplay">
                <span class="text-[11px] font-mono font-black text-[#3C6E71] bg-[#3C6E71]/10 px-2.5 py-0.5 rounded-lg border border-[#3C6E71]/20 tabular-nums" x-text="jalaliDisplay"></span>
            </template>
        </label>
    @endif

    <!-- Hidden Input for Laravel Form Submission (Gregorian YYYY-MM-DD) -->
    <input type="hidden"
           name="{{ $name }}"
           :value="gregorianValue"
           @if($required) required @endif>

    <!-- Trigger Input Button (Desktop/Mobile Calendar Look) -->
    <div class="relative">
        <button type="button"
                @click="openPicker()"
                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border border-[#CBD5E1] hover:border-[#3C6E71] rounded-xl text-xs font-bold text-[#19242B] transition-all min-h-[44px] shadow-2xs focus:ring-2 focus:ring-[#3C6E71]/20 focus:outline-none btn-tactile">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#3C6E71]/10 text-[#3C6E71] flex items-center justify-center flex-shrink-0">
                    <!-- Calendar Icon -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="3" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-width="2" d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                </div>
                <div class="flex items-center gap-1.5">
                    <span x-text="jalaliDisplay ? jalaliDisplay : '{{ $placeholder }}'"
                          :class="jalaliDisplay ? 'text-[#19242B] font-mono font-black text-sm tabular-nums' : 'text-[#64748B]'"></span>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                <template x-if="gregorianValue">
                    <span @click.stop="clearDate()"
                          class="w-6 h-6 rounded-full hover:bg-rose-50 text-slate-400 hover:text-rose-500 flex items-center justify-center text-xs transition-colors"
                          title="پاک کردن تاریخ">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </template>
                <svg class="w-4 h-4 text-[#64748B] transition-transform duration-200" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
    </div>

    <!-- Calendar Sheet Popover (Authentic Desktop Desk-Calendar Design) -->
    <div x-show="isOpen"
         @click.outside="isOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute z-50 mt-2 start-0 w-[315px] sm:w-[335px] bg-white border border-[#CBD5E1] rounded-3xl shadow-2xl p-4 text-xs select-none backdrop-blur-md"
         style="display: none;">

        <!-- Calendar Header (Red Spiral / Binder Top Aesthetic) -->
        <div class="bg-[#19242B] text-white p-3 rounded-2xl mb-3 shadow-inner">
            <div class="flex items-center justify-between">
                <!-- Year Navigation (Fast jump by years) -->
                <div class="flex items-center gap-1">
                    <button type="button" @click="changeYear(-1)" title="یک سال عقب"
                            class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-[10px] font-bold transition-colors">
                        &laquo;
                    </button>
                    <!-- Clickable Year Badge to open Year Selector Grid -->
                    <button type="button" @click="toggleView('years')"
                            :class="calendarView === 'years' ? 'bg-[#3C6E71] text-white ring-2 ring-emerald-400/40' : 'bg-white/15 text-white hover:bg-white/25'"
                            class="px-2.5 py-1 rounded-lg text-xs font-black font-mono tabular-nums transition-all flex items-center gap-1">
                        <span x-text="viewYear"></span>
                        <svg class="w-3 h-3 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <button type="button" @click="changeYear(1)" title="یک سال جلو"
                            class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-[10px] font-bold transition-colors">
                        &raquo;
                    </button>
                </div>

                <!-- Month Navigation -->
                <div class="flex items-center gap-1">
                    <button type="button" @click="prevMonth()" title="ماه قبل"
                            class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <!-- Clickable Month Badge to open Month Selector Grid -->
                    <button type="button" @click="toggleView('months')"
                            :class="calendarView === 'months' ? 'bg-[#3C6E71] text-white ring-2 ring-emerald-400/40' : 'bg-white/15 text-white hover:bg-white/25'"
                            class="px-3 py-1 rounded-lg text-xs font-black transition-all flex items-center gap-1">
                        <span x-text="months[viewMonth - 1]"></span>
                        <svg class="w-3 h-3 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <button type="button" @click="nextMonth()" title="ماه بعد"
                            class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                        <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- 1. DAYS VIEW (Default Calendar Sheet) -->
        <div x-show="calendarView === 'days'">
            <!-- Weekday Headers (شنبه تا جمعه) -->
            <div class="grid grid-cols-7 gap-1 text-center font-bold text-slate-400 text-[11px] mb-2 border-b border-slate-100 pb-1">
                <span>ش</span>
                <span>ی</span>
                <span>د</span>
                <span>س</span>
                <span>چ</span>
                <span>پ</span>
                <span class="text-rose-500 font-black">ج</span>
            </div>

            <!-- Days Grid -->
            <div class="grid grid-cols-7 gap-1 text-center mb-3">
                <!-- Blank days before 1st of month -->
                <template x-for="blank in firstWeekdayOfMonth" :key="'blank-' + blank">
                    <div class="h-8"></div>
                </template>

                <!-- Month days (1 to 29/30/31) -->
                <template x-for="day in daysInCurrentMonth" :key="'day-' + day">
                    <button type="button"
                            @click="selectDay(day)"
                            :class="{
                                'bg-[#3C6E71] text-white font-black scale-105 shadow-md ring-2 ring-emerald-300/40': isSelected(day),
                                'bg-teal-50 text-[#3C6E71] font-bold border border-teal-200': isToday(day) && !isSelected(day),
                                'text-slate-700 hover:bg-slate-100 font-medium': !isSelected(day) && !isToday(day),
                                'text-rose-600 font-bold': isFriday(day) && !isSelected(day)
                            }"
                            class="h-8 w-8 mx-auto rounded-xl flex items-center justify-center text-xs tabular-nums font-mono transition-all btn-tactile">
                        <span x-text="day"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- 2. FAST YEAR PICKER VIEW (گرید سال‌ها - پرش سریع به دهه‌های گذشته مثل ۱۳۵۰ تا ۱۴۱۰) -->
        <div x-show="calendarView === 'years'" class="space-y-2 py-1">
            <div class="flex items-center justify-between text-[11px] font-bold text-[#64748B] px-1">
                <span>انتخاب سریع سال (از ۱۳۲۰ تا ۱۴۱۰):</span>
                <div class="flex items-center gap-1 font-mono text-[10px]">
                    <button type="button" @click="yearPageOffset -= 12" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">&lt; قبل</button>
                    <button type="button" @click="yearPageOffset += 12" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700">بعد &gt;</button>
                </div>
            </div>

            <!-- 12-Year Grid for selected window -->
            <div class="grid grid-cols-3 gap-2">
                <template x-for="y in visibleYearsPage" :key="'yr-' + y">
                    <button type="button"
                            @click="pickYear(y)"
                            :class="viewYear === y ? 'bg-[#3C6E71] text-white font-black shadow-md' : 'bg-slate-50 text-slate-800 hover:bg-slate-100 border border-slate-200'"
                            class="py-2.5 rounded-xl text-center text-xs font-mono font-bold transition-all tabular-nums">
                        <span x-text="y"></span>
                    </button>
                </template>
            </div>

            <!-- Quick Decade Buttons -->
            <div class="pt-2 border-t border-slate-100 flex flex-wrap gap-1 justify-center text-[10px] font-mono">
                <button type="button" @click="jumpDecade(1350)" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-[#3C6E71] hover:text-white font-bold transition-colors">۱۳۵۰</button>
                <button type="button" @click="jumpDecade(1360)" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-[#3C6E71] hover:text-white font-bold transition-colors">۱۳۶۰</button>
                <button type="button" @click="jumpDecade(1370)" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-[#3C6E71] hover:text-white font-bold transition-colors">۱۳۷۰</button>
                <button type="button" @click="jumpDecade(1380)" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-[#3C6E71] hover:text-white font-bold transition-colors">۱۳۸۰</button>
                <button type="button" @click="jumpDecade(1390)" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-[#3C6E71] hover:text-white font-bold transition-colors">۱۳۹۰</button>
                <button type="button" @click="jumpDecade(1400)" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-[#3C6E71] hover:text-white font-bold transition-colors">۱۴۰۰</button>
                <button type="button" @click="jumpDecade(1403)" class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-800 hover:bg-[#3C6E71] hover:text-white font-bold transition-colors">۱۴۰۳</button>
            </div>
        </div>

        <!-- 3. FAST MONTH PICKER VIEW (گرید ۱۲ ماه سال) -->
        <div x-show="calendarView === 'months'" class="py-1">
            <span class="text-[11px] font-bold text-[#64748B] block mb-2 px-1">انتخاب ماه سال:</span>
            <div class="grid grid-cols-3 gap-2">
                <template x-for="(mName, mIdx) in months" :key="'mo-' + mIdx">
                    <button type="button"
                            @click="pickMonth(mIdx + 1)"
                            :class="viewMonth === (mIdx + 1) ? 'bg-[#3C6E71] text-white font-black shadow-md' : 'bg-slate-50 text-slate-800 hover:bg-slate-100 border border-slate-200'"
                            class="py-2.5 rounded-xl text-center text-xs font-bold transition-all">
                        <span x-text="mName"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 text-[11px]">
            <button type="button"
                    @click="goToToday()"
                    class="font-bold text-[#3C6E71] hover:text-[#284B63] hover:underline flex items-center gap-1">
                <span>امروز (برو به تاریخ فعلی)</span>
            </button>

            <button type="button"
                    @click="isOpen = false"
                    class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                بستن
            </button>
        </div>
    </div>
</div>

@once
<script>
function persianDatePicker(config) {
    return {
        isOpen: false,
        name: config.name || 'date',
        gregorianValue: config.initialValue || '',
        jalaliDisplay: '',
        calendarView: 'days', // 'days', 'years', 'months'
        viewYear: 1403,
        viewMonth: 1,
        selectedYear: null,
        selectedMonth: null,
        selectedDay: null,
        minYear: config.minYear || 1320,
        maxYear: config.maxYear || 1410,
        yearPageOffset: 0,
        months: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'],

        init() {
            const today = this.gregorianToJalali(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate());
            this.viewYear = today[0];
            this.viewMonth = today[1];

            if (this.gregorianValue) {
                this.parseInitialGregorian(this.gregorianValue);
            }
        },

        parseInitialGregorian(str) {
            try {
                const parts = str.split('-');
                if (parts.length === 3) {
                    const gy = parseInt(parts[0]);
                    const gm = parseInt(parts[1]);
                    const gd = parseInt(parts[2]);
                    if (!isNaN(gy) && !isNaN(gm) && !isNaN(gd)) {
                        const j = this.gregorianToJalali(gy, gm, gd);
                        this.selectedYear = j[0];
                        this.selectedMonth = j[1];
                        this.selectedDay = j[2];
                        this.viewYear = j[0];
                        this.viewMonth = j[1];
                        this.updateDisplay();
                    }
                }
            } catch (e) {}
        },

        openPicker() {
            this.isOpen = true;
            this.calendarView = 'days';
            if (this.selectedYear && this.selectedMonth) {
                this.viewYear = this.selectedYear;
                this.viewMonth = this.selectedMonth;
            }
            this.yearPageOffset = Math.floor(this.viewYear / 12) * 12;
        },

        toggleView(view) {
            if (this.calendarView === view) {
                this.calendarView = 'days';
            } else {
                this.calendarView = view;
                if (view === 'years') {
                    this.yearPageOffset = Math.floor(this.viewYear / 12) * 12;
                }
            }
        },

        get visibleYearsPage() {
            const start = this.yearPageOffset;
            const years = [];
            for (let i = 0; i < 12; i++) {
                const y = start + i;
                if (y >= this.minYear && y <= this.maxYear) {
                    years.push(y);
                }
            }
            return years;
        },

        jumpDecade(targetYear) {
            this.viewYear = targetYear;
            this.yearPageOffset = Math.floor(targetYear / 12) * 12;
            this.calendarView = 'days';
        },

        pickYear(y) {
            this.viewYear = y;
            this.calendarView = 'months'; // Flow: Pick year -> pick month -> pick day!
        },

        pickMonth(m) {
            this.viewMonth = m;
            this.calendarView = 'days';
        },

        changeYear(delta) {
            const target = this.viewYear + delta;
            if (target >= this.minYear && target <= this.maxYear) {
                this.viewYear = target;
            }
        },

        prevMonth() {
            if (this.viewMonth === 1) {
                this.viewMonth = 12;
                this.changeYear(-1);
            } else {
                this.viewMonth--;
            }
        },

        nextMonth() {
            if (this.viewMonth === 12) {
                this.viewMonth = 1;
                this.changeYear(1);
            } else {
                this.viewMonth++;
            }
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
            return this.selectedYear === this.viewYear &&
                   this.selectedMonth === this.viewMonth &&
                   this.selectedDay === day;
        },

        selectDay(day) {
            this.selectedYear = parseInt(this.viewYear);
            this.selectedMonth = parseInt(this.viewMonth);
            this.selectedDay = parseInt(day);

            const g = this.jalaliToGregorian(this.selectedYear, this.selectedMonth, this.selectedDay);
            this.gregorianValue = g[0] + '-' + String(g[1]).padStart(2, '0') + '-' + String(g[2]).padStart(2, '0');
            this.updateDisplay();
            this.isOpen = false;
        },

        goToToday() {
            const t = new Date();
            const tj = this.gregorianToJalali(t.getFullYear(), t.getMonth() + 1, t.getDate());
            this.viewYear = tj[0];
            this.viewMonth = tj[1];
            this.calendarView = 'days';
            this.selectDay(tj[2]);
        },

        clearDate() {
            this.selectedYear = null;
            this.selectedMonth = null;
            this.selectedDay = null;
            this.gregorianValue = '';
            this.jalaliDisplay = '';
        },

        updateDisplay() {
            if (this.selectedYear && this.selectedMonth && this.selectedDay) {
                const mm = String(this.selectedMonth).padStart(2, '0');
                const dd = String(this.selectedDay).padStart(2, '0');
                this.jalaliDisplay = `${this.selectedYear}/${mm}/${dd}`;
            } else {
                this.jalaliDisplay = '';
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
                jump = jm - jp;
                if (jy < jm) break;
                jp = jm;
            }
            let n = jy - jp;
            if (jump - n < 6) n = n - jump + Math.floor((jump + 4) / 33) * 33;
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
                if (gDayNo < gDaysInMonth[i]) {
                    gm = i + 1;
                    break;
                }
                gDayNo -= gDaysInMonth[i];
            }
            const gd = gDayNo + 1;
            return [gy, gm, gd];
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
            jm += 1;
            let jd = jDayNo + 1;
            return [jy, jm, jd];
        }
    };
}
</script>
@endonce
