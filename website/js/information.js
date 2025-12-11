// information.js

// نمایش جزئیات سفارش
const orderTotalText = document.getElementById("order-total-text");
const shippingCostText = document.getElementById("shipping-cost-text");
const payableAmountText = document.getElementById("payable-amount-text");
const form = document.getElementById("customer-form");

const SHIPPING_COST = 25000;
const persianRegex = /^[\u0600-\u06FF\s]+$/;      // فقط حروف فارسی و فاصله
const addressRegex = /^[\u0600-\u06FF0-9\s,]+$/;  // فارسی، عدد، فاصله، ویرگول

const formatPrice = num => Number(num).toLocaleString("fa-IR") + " تومان";

// ------------------------------------
// نمایش جزئیات سفارش در بارگذاری اولیه
// ------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    // 1. خواندن مبلغ سفارش از localStorage
    let orderTotal = Number(localStorage.getItem("finalAmount")) || 0;

    let payableAmount = orderTotal + SHIPPING_COST;

    // 2. ذخیره مبلغ قابل پرداخت نهایی در localStorage
    localStorage.setItem("payableAmount", payableAmount);

    // 3. نمایش مقادیر
    orderTotalText.textContent = formatPrice(orderTotal);
    shippingCostText.textContent = formatPrice(SHIPPING_COST);
    payableAmountText.textContent = formatPrice(payableAmount);

    // بعد از اینکه DOM کامل شد، دکمه‌ها را وصل کنیم
    const payOnlineBtn = document.getElementById("pay-online");
    const payWalletBtn = document.getElementById("pay-wallet");
    const cancelBtn = document.getElementById("cancel");

    if (payOnlineBtn) {
        payOnlineBtn.addEventListener("click", () => {
            processOrder("online");
        });
    }

    if (payWalletBtn) {
        payWalletBtn.addEventListener("click", () => {
            processOrder("wallet");
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener("click", () => {
            window.location.href = "shoppingCart.php";
        });
    }
});

// ------------------------------
// تابع اعتبارسنجی فرم
// ------------------------------
function validateForm() {
    let isValid = true;

    const firstName = document.getElementById("first-name");
    const lastName  = document.getElementById("last-name");
    const city      = document.getElementById("city");
    const address   = document.getElementById("address");

    // پاک کردن خطاهای قبلی
    [firstName, lastName, city, address].forEach(input => {
        input.classList.remove("error");
        if (input.nextElementSibling) input.nextElementSibling.textContent = "";
    });

    // نام
    if (!firstName.value.trim() || !persianRegex.test(firstName.value)) {
        firstName.classList.add("error");
        if (firstName.nextElementSibling)
            firstName.nextElementSibling.textContent = "لطفاً نام خود را به فارسی وارد کنید";
        isValid = false;
    }

    // نام خانوادگی
    if (!lastName.value.trim() || !persianRegex.test(lastName.value)) {
        lastName.classList.add("error");
        if (lastName.nextElementSibling)
            lastName.nextElementSibling.textContent = "لطفاً نام خانوادگی خود را به فارسی وارد کنید";
        isValid = false;
    }

    // شهر
    if (!city.value.trim() || !persianRegex.test(city.value)) {
        city.classList.add("error");
        if (city.nextElementSibling)
            city.nextElementSibling.textContent = "لطفاً نام شهر را به فارسی وارد کنید";
        isValid = false;
    }

    // آدرس
    if (!address.value.trim() || !addressRegex.test(address.value)) {
        address.classList.add("error");
        if (address.nextElementSibling)
            address.nextElementSibling.textContent = "لطفاً آدرس را به درستی وارد کنید";
        isValid = false;
    }

    return isValid;
}

// ------------------------------
// منطق مشترک ثبت سفارش
// ------------------------------
function processOrder(method) {

    // 1) اعتبارسنجی
    if (!validateForm()) return;

    const formData = new FormData(form);
    const cartItems = localStorage.getItem("cartItems");

    if (!cartItems || JSON.parse(cartItems).length === 0) {
        alert("سبد خرید شما خالی است.");
        return;
    }

    formData.append('cart_data', cartItems);

    fetch('save_info.php', {
        method: 'POST',
        body: formData
    })
    .then(res => {

        if (!res.ok) {
            alert("خطا در ذخیره اطلاعات سفارش!");
            return;
        }

        // 🔵 پرداخت آنلاین → رفتن به درگاه
        if (method === "online") {
            localStorage.setItem("payType", "order");
            window.location.href = "dargah.php";
        }

        // 🟢 پرداخت با کیف پول → ارسال مستقیم به process_payment.php
       // 🟢 پرداخت با کیف پول
else if (method === "wallet") {

    let finalAmount = Number(localStorage.getItem("payableAmount") || 0);

    // ۱) از سرور بپرس موجودی کیف پول چقدر است
    fetch('get_wallet_balance.php')
        .then(res => res.json())
        .then(data => {

            if (data.status !== 'ok') {
                alert("خطا در بررسی موجودی کیف پول.");
                return;
            }

            const walletBalance = Number(data.wallet || 0);

            // اگر موجودی کمتر بود → فقط پیام، در همین صفحه بمان
            if (walletBalance < finalAmount) {
                alert("موجودی کیف پول برای این پرداخت کافی نیست.");
                return;
            }

            // اگر موجودی کافی است → ارسال فرم مخفی به process_payment.php
            const tempForm = document.createElement("form");
            tempForm.method = "POST";
            tempForm.action = "process_payment.php";

            tempForm.innerHTML = `
                <input type="hidden" name="pay_type" value="wallet_order">
                <input type="hidden" name="final_amount" value="${finalAmount}">
            `;

            document.body.appendChild(tempForm);
            tempForm.submit();
        })
        .catch(err => {
            console.error("wallet check error:", err);
            alert("خطا در بررسی موجودی کیف پول.");
        });
}

    })
    .catch(err => {
        console.error("خطا در ارتباط با سرور:", err);
        alert("خطا در برقراری ارتباط با سرور!");
    });
}
