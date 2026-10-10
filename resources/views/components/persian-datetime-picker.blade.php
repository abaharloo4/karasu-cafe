@props([
    'name' => 'datetime',
    'value' => null,
    'label' => null,
    'placeholder' => 'انتخاب تاریخ و زمان...',
    'required' => false,
    'minYear' => 1400,
    'maxYear' => 1410,
])

<div x-data="persianDateTimePicker({
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
            <template x-if="displayFa">
                <span class="text-[11px] font-mono font-black text-[#3C6E71] bg-[#3C6E71]/10 px-2 py-0.5 rounded-lg border border-[#3C6E71]/20 tabular-nums" x-text="displayFa"></span>
            </template>
        </label>
    @endif

    <!-- Hidden Input for Laravel Form Submission (Gregorian YYYY-MM-DDTHH:mm or YYYY-MM-DD HH:mm:ss) -->
    <input type="hidden"
           name="{{ $name }}"
           :value="gregorianIsoValue"
           @if($required) required @endif>

    <!-- Trigger Input Button (VibeFarsi Graphite Style) -->
    <div class="relative">
        <button type="button"
                @click="openPicker()"
                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border border-[#CBD5E1] hover:border-[#3C6E71] rounded-xl text-xs font-bold text-[#19242B] transition-all min-h-[44px] shadow-2xs focus:ring-2 focus:ring-[#3C6E71]/20 focus:outline-none btn-tactile">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-[#3C6E71]/10 text-[#3C6E71] flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
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
         class="absolute z-50 mt-2 start-0 w-[320px] sm:w-[350px] bg-white border border-[#CBD5E1] rounded-3xl shadow-2xl p-4 text-xs select-none backdrop-blur-md"
         style="display: none;">

        <!-- Header Tabs: Date (Jalali) vs Visual Time (24h) -->
        <div class="bg-[#19242B] text-white p-3 rounded-2xl flex items-center justify-between mb-3 shadow-inner">
            <div class="flex items-center gap-1.5 w-full">
                <!-- Date Tab -->
                <button type="button"
                        @click="activeTab = 'date'"
                        :class="activeTab === 'date' ? 'bg-[#3C6E71] text-white font-black shadow-xs' : 'bg-white/10 text-slate-300 hover:bg-white/20'"
                        class="flex-1 py-2 px-2 rounded-xl text-center text-xs transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span x-text="selectedDateText || 'انتخاب تاریخ'"></span>
                </button>

                <!-- Time Tab -->
                <button type="button"
                        @click="activeTab = 'time'"
                        :class="activeTab === 'time' ? 'bg-[#3C6E71] text-white font-black shadow-xs' : 'bg-white/10 text-slate-300 hover:bg-white/20'"
                        class="flex-1 py-2 px-2 rounded-xl text-center text-xs transition-all flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 2"/></svg>
                    <span class="font-mono dir-ltr" x-text="`${pad(tempHour)}:${pad(tempMinute)}`"></span>
                </button>
            </div>
        </div>

        <!-- 1. DATE PICKER VIEW -->
        <div x-show="activeTab === 'date'" class="space-y-3">
            <!-- Month & Year Controls -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <button type="button" @click="prevMonth()" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <div class="flex items-center gap-1">
                    <select x-model="viewMonth" class="bg-slate-50 border border-slate-200 text-[#284B63] font-black text-xs rounded-lg px-2 py-1 outline-none cursor-pointer">
                        <template x-for="(mName, mIdx) in months" :key="mIdx">
                            <option :value="mIdx + 1" x-text="mName" :selected="viewMonth === mIdx + 1"></option>
                        </template>
                    </select>

                    <select x-model="viewYear" class="bg-slate-50 border border-slate-200 text-[#284B63] font-black text-xs rounded-lg px-2 py-1 outline-none cursor-pointer tabular-nums">
                        <template x-for="y in availableYears" :key="y">
                            <option :value="y" x-text="y" :selected="viewYear === y"></option>
                        </template>
                    </select>
                </div>

                <button type="button" @click="nextMonth()" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Weekdays -->
            <div class="grid grid-cols-7 gap-1 text-center font-bold text-slate-400 text-[11px]">
                <span>ش</span><span>ی</span><span>د</span><span>س</span><span>چ</span><span>پ</span><span class="text-rose-500">ج</span>
            </div>

            <!-- Days Grid -->
            <div class="grid grid-cols-7 gap-1 text-center">
                <template x-for="blank in firstWeekdayOfMonth" :key="'blank-' + blank">
                    <div class="h-8"></div>
                </template>

                <template x-for="day in daysInCurrentMonth" :key="'day-' + day">
                    <button type="button"
                            @click="selectDay(day)"
                            :class="{
                                'bg-[#3C6E71] text-white font-black shadow-xs': isSelected(day),
                                'bg-teal-50 text-[#3C6E71] font-bold border border-teal-200': isToday(day) && !isSelected(day),
                                'text-slate-700 hover:bg-slate-100 font-medium': !isSelected(day) && !isToday(day),
                                'text-rose-600': isFriday(day) && !isSelected(day)
                            }"
                            class="h-8 w-8 mx-auto rounded-lg flex items-center justify-center text-xs tabular-nums transition-all btn-tactile">
                        <span x-text="day"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- 2. VISUAL TIME PICKER VIEW (24-Hour Dial) -->
        <div x-show="activeTab === 'time'" class="space-y-2">
            <!-- Mode Switcher (Hours vs Minutes) -->
            <div class="flex items-center justify-between text-[11px] text-[#64748B] px-1">
                <div class="flex items-center gap-1 bg-slate-100 p-0.5 rounded-lg">
                    <button type="button" @click="timeMode = 'hours'" :class="timeMode === 'hours' ? 'bg-[#3C6E71] text-white font-black' : 'text-slate-600'" class="px-2 py-0.5 rounded text-[11px] transition-colors">ساعت (۲۴h)</button>
                    <button type="button" @click="timeMode = 'minutes'" :class="timeMode === 'minutes' ? 'bg-[#3C6E71] text-white font-black' : 'text-slate-600'" class="px-2 py-0.5 rounded text-[11px] transition-colors">دقیقه</button>
                </div>
                <span class="font-bold text-[10px] text-slate-500" x-show="timeMode === 'hours'">حلقه بیرونی: ۱۲-۲۳ | داخلی: ۰۰-۱۱</span>
            </div>

            <!-- Clock Dial (240px) -->
            <div class="relative w-[240px] h-[240px] mx-auto rounded-full bg-[#F8FAFC] border-2 border-slate-200/80 shadow-inner flex items-center justify-center overflow-hidden">
                <div class="absolute z-20 w-3 h-3 rounded-full bg-[#3C6E71] border-2 border-white"></div>
                <div class="absolute z-10 w-0.5 origin-bottom bg-[#3C6E71] transition-all duration-200 ease-out pointer-events-none"
                     :style="clockHandStyle">
                    <div class="absolute -top-3 -left-3 w-6 h-6 rounded-full bg-[#3C6E71] opacity-90 shadow-sm"></div>
                </div>

                <!-- Hours Mode -->
                <div x-show="timeMode === 'hours'" class="absolute inset-0">
                    <template x-for="item in outerHours" :key="'dt-h-out-' + item.h">
                        <button type="button" @click="selectHour(item.h)" :style="`left: ${item.x}px; top: ${item.y}px;`"
                                :class="tempHour === item.h ? 'bg-[#3C6E71] text-white font-black scale-110 shadow-md ring-2 ring-white z-30' : 'bg-transparent text-slate-800 hover:bg-slate-200/80 font-bold z-20'"
                                class="absolute -translate-x-1/2 -translate-y-1/2 w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-mono tabular-nums">
                            <span x-text="pad(item.h)"></span>
                        </button>
                    </template>
                    <template x-for="item in innerHours" :key="'dt-h-in-' + item.h">
                        <button type="button" @click="selectHour(item.h)" :style="`left: ${item.x}px; top: ${item.y}px;`"
                                :class="tempHour === item.h ? 'bg-[#3C6E71] text-white font-black scale-110 shadow-md ring-2 ring-white z-30' : 'bg-transparent text-slate-500 hover:bg-slate-200/80 font-bold z-20'"
                                class="absolute -translate-x-1/2 -translate-y-1/2 w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-mono tabular-nums">
                            <span x-text="pad(item.h)"></span>
                        </button>
                    </template>
                </div>

                <!-- Minutes Mode -->
                <div x-show="timeMode === 'minutes'" class="absolute inset-0">
                    <template x-for="item in minuteSteps" :key="'dt-m-' + item.m">
                        <button type="button" @click="selectMinute(item.m)" :style="`left: ${item.x}px; top: ${item.y}px;`"
                                :class="tempMinute === item.m ? 'bg-[#3C6E71] text-white font-black scale-110 shadow-md ring-2 ring-white z-30' : 'bg-transparent text-slate-800 hover:bg-slate-200/80 font-bold z-20'"
                                class="absolute -translate-x-1/2 -translate-y-1/2 w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-mono tabular-nums">
                            <span x-text="pad(item.m)"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Stepper -->
            <div class="flex items-center justify-center gap-1 dir-ltr text-[11px]">
                <button type="button" @click="stepMinute(-5)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 font-bold">-۵</button>
                <button type="button" @click="stepMinute(-1)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 font-bold">-۱</button>
                <span class="px-2 font-mono font-black text-xs text-[#19242B]" x-text="pad(tempMinute)"></span>
                <button type="button" @click="stepMinute(1)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 font-bold">+۱</button>
                <button type="button" @click="stepMinute(5)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 font-bold">+۵</button>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
            <button type="button" @click="isOpen = false" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                انصراف
            </button>

            <button type="button" @click="confirmAll()" class="px-5 py-1.5 rounded-xl bg-[#3C6E71] hover:bg-[#284B63] text-white font-black shadow-xs transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>تایید تاریخ و ساعت</span>
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
        gregorianIsoValue: config.initialValue || '',
        displayFa: '',
        activeTab: 'date', // 'date' or 'time'
        timeMode: 'hours', // 'hours' or 'minutes'
        selectedYear: null,
        selectedMonth: null,
        selectedDay: null,
        tempHour: 12,
        tempMinute: 0,
        viewYear: 1403,
        viewMonth: 1,
        minYear: config.minYear || 1400,
        maxYear: config.maxYear || 1410,
        months: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'],
        center: 120, // center of 240px container
        outerRadius: 90,
        innerRadius: 56,
        minuteRadius: 86,

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
                // e.g. "2026-10-15T14:30" or "2026-10-15 14:30:00"
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
                    this.tempHour = th || 0;
                    this.tempMinute = tm || 0;
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

        get availableYears() {
            const list = [];
            for (let y = this.maxYear; y >= this.minYear; y--) list.push(y);
            return list;
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
        },

        prevMonth() {
            if (this.viewMonth === 1) { this.viewMonth = 12; this.viewYear--; }
            else { this.viewMonth--; }
        },

        nextMonth() {
            if (this.viewMonth === 12) { this.viewMonth = 1; this.viewYear++; }
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
            // Smoothly switch to time picker tab
            setTimeout(() => {
                this.activeTab = 'time';
            }, 180);
        },

        // Time clock math
        get outerHours() {
            const list = [];
            for (let i = 0; i < 12; i++) {
                const h = (i === 0) ? 12 : 12 + i;
                const rad = (i * 30 - 90) * (Math.PI / 180);
                list.push({ h, x: Math.round(this.center + this.outerRadius * Math.cos(rad)), y: Math.round(this.center + this.outerRadius * Math.sin(rad)) });
            }
            return list;
        },

        get innerHours() {
            const list = [];
            for (let i = 0; i < 12; i++) {
                const h = i;
                const rad = (i * 30 - 90) * (Math.PI / 180);
                list.push({ h, x: Math.round(this.center + this.innerRadius * Math.cos(rad)), y: Math.round(this.center + this.innerRadius * Math.sin(rad)) });
            }
            return list;
        },

        get minuteSteps() {
            const list = [];
            for (let i = 0; i < 12; i++) {
                const m = i * 5;
                const rad = (i * 30 - 90) * (Math.PI / 180);
                list.push({ m, x: Math.round(this.center + this.minuteRadius * Math.cos(rad)), y: Math.round(this.center + this.minuteRadius * Math.sin(rad)) });
            }
            return list;
        },

        get clockHandStyle() {
            if (this.timeMode === 'hours') {
                const isOuter = this.tempHour >= 12;
                const length = isOuter ? this.outerRadius : this.innerRadius;
                const angleDeg = (this.tempHour % 12) * 30;
                return `height: ${length}px; transform: rotate(${angleDeg}deg); bottom: ${this.center}px; left: ${this.center - 1}px;`;
            } else {
                const angleDeg = this.tempMinute * 6;
                return `height: ${this.minuteRadius}px; transform: rotate(${angleDeg}deg); bottom: ${this.center}px; left: ${this.center - 1}px;`;
            }
        },

        selectHour(h) {
            this.tempHour = h;
            setTimeout(() => { this.timeMode = 'minutes'; }, 180);
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
            const th = this.pad(this.tempHour);
            const tm = this.pad(this.tempMinute);

            this.gregorianIsoValue = `${gy}-${gm}-${gd}T${th}:${tm}`;
            this.updateDisplay();
            this.isOpen = false;
        },

        updateDisplay() {
            if (this.selectedYear && this.selectedMonth && this.selectedDay) {
                const th = this.pad(this.tempHour);
                const tm = this.pad(this.tempMinute);
                this.displayFa = `${this.selectedYear}/${this.pad(this.selectedMonth)}/${this.pad(this.selectedDay)} ساعت ${th}:${tm}`;
            } else {
                this.displayFa = '';
            }
        },

        isLeapJalaliYear(jy) {
            const breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
            const bl = breaks.length;
            let jp = breaks[0];
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
