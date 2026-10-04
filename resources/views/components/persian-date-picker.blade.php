@props([
    'name' => 'date',
    'value' => null,
    'label' => null,
    'placeholder' => 'انتخاب تاریخ...',
    'required' => false,
    'minYear' => 1320,
    'maxYear' => 1405,
])

<div x-data="persianDatePicker({
        initialValue: '{{ $value ?? '' }}',
        minYear: {{ $minYear }},
        maxYear: {{ $maxYear }}
    })"
    class="relative w-full text-right"
    dir="rtl">

    @if($label)
        <label class="block text-xs font-bold text-[#353535] mb-1.5 flex items-center justify-between">
            <span>{{ $label }}</span>
            <template x-if="jalaliDisplay">
                <span class="text-[11px] font-mono font-medium text-[#3C6E71] bg-teal-50 px-2 py-0.5 rounded-lg border border-teal-100" x-text="jalaliDisplay"></span>
            </template>
        </label>
    @endif

    <!-- Hidden Input for Laravel Form Submission (Always standard Gregorian YYYY-MM-DD) -->
    <input type="hidden"
           name="{{ $name }}"
           :value="gregorianValue"
           @if($required) required @endif>

    <!-- Display / Trigger Input -->
    <div class="relative">
        <button type="button"
                @click="openPicker()"
                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border border-[#353535]/20 hover:border-[#3C6E71] rounded-xl text-xs font-bold text-[#353535] transition-all min-h-[44px] shadow-2xs focus:ring-2 focus:ring-[#3C6E71]/20 focus:outline-none">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-[#3C6E71] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span x-text="jalaliDisplay ? jalaliDisplay : '{{ $placeholder }}'"
                      :class="jalaliDisplay ? 'text-[#353535]' : 'text-[#353535]/40'"></span>
            </div>

            <div class="flex items-center gap-1.5">
                <template x-if="gregorianValue">
                    <span @click.stop="clearDate()"
                          class="w-5 h-5 rounded-full hover:bg-slate-100 text-slate-400 hover:text-rose-500 flex items-center justify-center text-xs transition-colors"
                          title="پاک کردن">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </template>
                <svg class="w-4 h-4 text-[#353535]/40 transition-transform" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </button>
    </div>

    <!-- Calendar Dropdown / Modal Popup -->
    <div x-show="isOpen"
         @click.outside="isOpen = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         class="absolute z-50 mt-2 start-0 w-[310px] sm:w-[330px] bg-white border border-[#353535]/15 rounded-2xl shadow-xl p-4 text-xs select-none"
         style="display: none;">

        <!-- Header: Month & Year Controls -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
            <button type="button"
                    @click="prevMonth()"
                    class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-colors btn-tactile min-h-[32px] min-w-[32px]">
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <!-- Month & Year Selector Trigger -->
            <div class="flex items-center gap-1">
                <!-- Month Select Dropdown -->
                <select x-model="viewMonth"
                        class="bg-slate-50 border border-slate-200 text-[#284B63] font-black text-xs rounded-lg px-2 py-1 outline-none focus:border-[#3C6E71] cursor-pointer">
                    <template x-for="(mName, mIdx) in months" :key="mIdx">
                        <option :value="mIdx + 1" x-text="mName" :selected="viewMonth === mIdx + 1"></option>
                    </template>
                </select>

                <!-- Year Select Dropdown -->
                <select x-model="viewYear"
                        class="bg-slate-50 border border-slate-200 text-[#284B63] font-black text-xs rounded-lg px-2 py-1 outline-none focus:border-[#3C6E71] cursor-pointer tabular-nums">
                    <template x-for="y in availableYears" :key="y">
                        <option :value="y" x-text="y" :selected="viewYear === y"></option>
                    </template>
                </select>
            </div>

            <button type="button"
                    @click="nextMonth()"
                    class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-colors btn-tactile min-h-[32px] min-w-[32px]">
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        <!-- Weekday Headers (شنبه تا جمعه) -->
        <div class="grid grid-cols-7 gap-1 text-center font-bold text-slate-400 text-[11px] mb-2">
            <span>ش</span>
            <span>ی</span>
            <span>د</span>
            <span>س</span>
            <span>چ</span>
            <span>پ</span>
            <span class="text-rose-500">ج</span>
        </div>

        <!-- Days Grid -->
        <div class="grid grid-cols-7 gap-1 text-center mb-3">
            <!-- Empty leading cells -->
            <template x-for="blank in firstWeekdayOfMonth" :key="'blank-' + blank">
                <div class="h-8"></div>
            </template>

            <!-- Month days -->
            <template x-for="day in daysInCurrentMonth" :key="'day-' + day">
                <button type="button"
                        @click="selectDay(day)"
                        :class="{
                            'bg-[#284B63] text-white font-black shadow-xs': isSelected(day),
                            'bg-teal-50 text-[#3C6E71] font-bold border border-teal-200': isToday(day) && !isSelected(day),
                            'text-slate-700 hover:bg-slate-100 font-medium': !isSelected(day) && !isToday(day),
                            'text-rose-600': isFriday(day) && !isSelected(day)
                        }"
                        class="h-8 w-8 mx-auto rounded-lg flex items-center justify-center text-xs tabular-nums transition-all btn-tactile">
                    <span x-text="day"></span>
                </button>
            </template>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 text-[11px]">
            <button type="button"
                    @click="goToToday()"
                    class="font-bold text-[#3C6E71] hover:text-[#284B63] hover:underline flex items-center gap-1">
                <span>امروز</span>
            </button>

            <button type="button"
                    @click="isOpen = false"
                    class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
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
        gregorianValue: config.initialValue || '',
        jalaliDisplay: '',
        viewYear: 1403,
        viewMonth: 1,
        selectedYear: null,
        selectedMonth: null,
        selectedDay: null,
        minYear: config.minYear || 1320,
        maxYear: config.maxYear || 1405,
        months: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'],

        init() {
            // Compute current Jalali date for default view
            const today = this.gregorianToJalali(new Date().getFullYear(), new Date().getMonth() + 1, new Date().getDate());
            this.viewYear = today[0];
            this.viewMonth = today[1];

            if (this.gregorianValue) {
                this.parseInitialGregorian(this.gregorianValue);
            }
        },

        get availableYears() {
            const years = [];
            for (let y = this.maxYear; y >= this.minYear; y--) {
                years.push(y);
            }
            return years;
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
            if (this.selectedYear && this.selectedMonth) {
                this.viewYear = this.selectedYear;
                this.viewMonth = this.selectedMonth;
            }
        },

        prevMonth() {
            if (this.viewMonth === 1) {
                this.viewMonth = 12;
                this.viewYear--;
            } else {
                this.viewMonth--;
            }
        },

        nextMonth() {
            if (this.viewMonth === 12) {
                this.viewMonth = 1;
                this.viewYear++;
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
            // Find Gregorian date for day 1 of viewYear / viewMonth
            const g = this.jalaliToGregorian(this.viewYear, this.viewMonth, 1);
            const d = new Date(g[0], g[1] - 1, g[2]);
            // In JS: 0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat
            // In Persian: 0=Sat, 1=Sun, 2=Mon, 3=Tue, 4=Wed, 5=Thu, 6=Fri
            const jsDay = d.getDay();
            return (jsDay + 1) % 7;
        },

        isFriday(day) {
            const g = this.jalaliToGregorian(this.viewYear, this.viewMonth, day);
            const d = new Date(g[0], g[1] - 1, g[2]);
            return d.getDay() === 5; // Friday is 5
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
