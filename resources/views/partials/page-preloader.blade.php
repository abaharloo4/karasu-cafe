<!-- Initial Page Preloader (Immediate render, fades out on page load) -->
<div id="cafe-initial-preloader" class="fixed inset-0 flex flex-col items-center justify-center bg-[#19242B] select-none transition-opacity duration-300 ease-out" style="z-index: 99999;">
    <div class="flex flex-col items-center gap-4 text-center p-6">
        <div class="relative w-20 h-20 flex items-center justify-center">
            <span class="absolute inset-0 rounded-full bg-[#387780]/30 animate-ping opacity-75"></span>
            <span class="absolute -inset-2 rounded-full border-2 border-dashed border-[#387780]/50 animate-spin" style="animation-duration: 4s;"></span>
            <div class="w-16 h-16 rounded-2xl bg-[#141D22] border border-[#387780]/40 shadow-inner flex items-center justify-center relative z-10 p-2.5 animate-pulse">
                <svg viewBox="290 145 410 655" class="w-full h-full text-[#387780] fill-current">
                    <path d="M670,160 C560,185 440,250 385,340 C372,362 366,390 365,415 C420,340 490,285 562,256 C500,292 430,350 400,414 C395,430 392,445 392,462 C470,455 540,430 590,395 C650,350 678,260 670,160 Z"/>
                    <path d="M305,440 C340,442 372,440 395,425 L402,440 C385,470 380,540 388,610 C392,640 400,665 410,680 C375,650 352,590 358,530 C360,500 366,478 375,462 C350,458 325,452 305,440 Z"/>
                    <path fill-rule="evenodd" d="M435,465 L540,465 C620,465 655,500 655,545 C655,580 632,600 596,608 C652,615 682,652 682,696 C682,752 640,785 560,785 L430,785 C428,783 433,778 435,770 Z M495,505 L540,505 C575,505 595,520 595,546 C595,572 575,588 540,588 L495,588 Z M495,632 L555,632 C600,632 622,656 622,695 C622,734 598,752 555,752 L495,752 Z"/>
                </svg>
            </div>
        </div>
        <div class="space-y-1">
            <h4 class="font-black text-white text-sm sm:text-base tracking-wide">بی کافه (B Cafe)</h4>
            <p class="text-[11px] text-slate-400 font-medium">شیراز • بار تخصصی قهوه</p>
        </div>
    </div>
</div>
<script>
(function() {
    function hidePreloader() {
        var el = document.getElementById('cafe-initial-preloader');
        if (el && !el.classList.contains('opacity-0')) {
            el.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(function() { if (el && el.parentNode) el.parentNode.removeChild(el); }, 350);
        }
    }
    if (document.readyState === 'complete') {
        setTimeout(hidePreloader, 250);
    } else {
        window.addEventListener('load', function() { setTimeout(hidePreloader, 200); });
        setTimeout(hidePreloader, 1000);
    }
})();
</script>
