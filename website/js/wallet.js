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
    let clean = amountInput.value.replace(/[^۰-۹]/g, ''); // فقط اعداد فارسی

    // تبدیل اعداد انگلیسی هم اگر وارد شد، به فارسی
    clean = clean.replace(/\d/g, d => "۰۱۲۳۴۵۶۷۸۹"[d]);

    amountInput.value = clean;
    errorMsg.style.display = 'none'; // مخفی کردن پیام خطا هنگام تایپ
});


quickButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        let val = btn.getAttribute('data-amount');
        amountInput.value = formatFarsi(val); 
        errorMsg.style.display = 'none';
    });
});

// ---------------------------------------------------
// منطق دکمه پرداخت
// ---------------------------------------------------
confirmBtn.addEventListener('click', () => {

    // تبدیل مبلغ وارد شده (فارسی و بدون کاما) به عدد انگلیسی برای استفاده در localStorage
    let amountStr = amountInput.value.replace(/,/g, "");
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