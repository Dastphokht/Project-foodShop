// wallet.js

// ---------------------------------------------------
// توابع کمکی تبدیل و فرمت‌بندی
// ---------------------------------------------------
function formatFarsi(num) {
    if (!num) return "۰";
    // اضافه کردن کاما (هزارگان)
    // ابتدا عدد را به رشته انگلیسی تبدیل کرده و کاما می‌زنیم
    let english = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
    // سپس اعداد انگلیسی را به فارسی تبدیل می‌کنیم
    return english.replace(/\d/g, d => "۰۱۲۳۴۵۶۷۸۹"[d]);
}

function persianToEnglish(str) {
    // تبدیل اعداد فارسی به انگلیسی برای محاسبه ریاضی
    return str.replace(/[۰-۹]/g, d => "0123456789"["۰۱۲۳۴۵۶۷۸۹".indexOf(d)]);
}

function stripToDigits(str) {
    // فقط رقم‌ها (فارسی و انگلیسی) رو نگه می‌داره
    return String(str).replace(/[^0-9۰-۹]/g, '');
}

function removeToman(str) {
    return String(str).replace(/تومان/g, '').trim();
}


// ---------------------------------------------------
// تعریف متغیرهای DOM و موجودی اولیه
// ---------------------------------------------------
const balanceEl = document.getElementById("balance");



const amountInput = document.getElementById('amountInput');
const confirmBtn = document.getElementById('confirmBtn');
const errorMsg = document.getElementById('errorMsg');
const quickButtons = document.querySelectorAll('.quick-amounts button');

// ---------------------------------------------------
// مدیریت ورودی مبلغ (فرمت‌بندی و اعتبارسنجی)
// ---------------------------------------------------
amountInput.addEventListener("input", () => {
    // تومان رو حذف کن (اگه وجود داشت)
    let val = removeToman(amountInput.value);

    // فقط رقم‌ها رو نگه دار
    let clean = stripToDigits(val);

    // انگلیسی → فارسی
    clean = clean.replace(/\d/g, d => "۰۱۲۳۴۵۶۷۸۹"[d]);

    // کاماگذاری و نمایش
    amountInput.value = formatFarsi(clean);

    errorMsg.style.display = 'none';
});

amountInput.addEventListener("focus", () => {
    amountInput.value = removeToman(amountInput.value);
});

amountInput.addEventListener("blur", () => {
    let val = removeToman(amountInput.value);
    let clean = stripToDigits(val);

    if (clean.length > 0) {
        amountInput.value = formatFarsi(clean) + " تومان";
    } else {
        amountInput.value = "";
    }
});


quickButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        let val = btn.getAttribute('data-amount');
        amountInput.value = formatFarsi(val) + " تومان";
        errorMsg.style.display = 'none';
    });
});


// ---------------------------------------------------
// منطق دکمه پرداخت
// ---------------------------------------------------
confirmBtn.addEventListener('click', () => {

    // تبدیل مبلغ وارد شده (فارسی و بدون کاما) به عدد انگلیسی برای استفاده در localStorage
    let amountStr = removeToman(amountInput.value).replace(/,/g, "");
    amountStr = stripToDigits(amountStr); // فقط رقم‌ها
    let amount = parseInt(persianToEnglish(amountStr));
    

    if (isNaN(amount) || amount <= 0) {
        errorMsg.textContent = 'لطفاً مبلغ معتبر و بزرگتر از صفر وارد کنید';
        errorMsg.style.display = 'block';
        return;
    }
    
    errorMsg.style.display = 'none';

    // 1. ذخیره مبلغ قابل پرداخت (برای dargah.js)
    localStorage.setItem('payableAmount', amount);

    // 2. تنظیم نوع پرداخت به "wallet"
    localStorage.setItem('payType', 'wallet'); 

    // 3. ریدایرکت به صفحه درگاه (با mode=wallet)
    window.location.href = 'dargah.php?mode=wallet'; 

});