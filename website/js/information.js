// information.js

// نمایش جزئیات سفارش
const orderTotalText = document.getElementById("order-total-text");
const shippingCostText = document.getElementById("shipping-cost-text");
const payableAmountText = document.getElementById("payable-amount-text");
const form = document.getElementById("customer-form");
const shippingCost = localStorage.getItem("dc_shipping_cost") || 0;

//const SHIPPING_COST = 25000;
const persianRegex = /^[\u0600-\u06FF\s]+$/;      // فقط حروف فارسی و فاصله
const addressRegex = /^[\u0600-\u06FF0-9\s,]+$/;  // فارسی، عدد، فاصله، ویرگول

const formatPrice = num => Number(num).toLocaleString("fa-IR") + " تومان";

// ------------------------------------
// نمایش جزئیات سفارش در بارگذاری اولیه
// ------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    // 1. مبلغ سفارش از localStorage
    let orderTotal = Number(localStorage.getItem("finalAmount")) || 0;

    // 2. هزینه ارسال از data-shipping روی div
    const shippingDiv = document.getElementById("shipping-cost-text");
    let shippingCost = 0;

    if (shippingDiv && shippingDiv.dataset.shipping) {
        shippingCost = Number(shippingDiv.dataset.shipping) || 0;
    }

    // 3. محاسبه مبلغ قابل پرداخت
    let payableAmount = orderTotal + shippingCost;

    // 4. ذخیره مبلغ قابل پرداخت نهایی در localStorage (برای درگاه / کیف پول)
    localStorage.setItem("payableAmount", payableAmount);

    // 5. نمایش مقادیر
    orderTotalText.textContent     = formatPrice(orderTotal);
    shippingCostText.textContent   = formatPrice(shippingCost);
    payableAmountText.textContent  = formatPrice(payableAmount);

    // بقیه کد: لیسنرهای pay-online / pay-wallet / cancel
    const payOnlineBtn = document.getElementById("pay-online");
    const payWalletBtn = document.getElementById("pay-wallet");
    const cancelBtn    = document.getElementById("cancel");

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


async function checkStockBeforePay() {

    const cart = localStorage.getItem("cartItems");
    if (!cart) return false;

    const formData = new FormData();
    formData.append("cart", cart);

    let response = await fetch("check_stock.php", {
        method: "POST",
        body: formData
    });

    let result = await response.json();

    if (result.status === "ok") {
        return true; // همه موجودی‌ها کافی است
    }

    if (result.status === "fail") {
        let msg = "موجودی بعضی غذاها کافی نیست:\n\n";
        result.items.forEach(it => {
            msg += `❌ ${it.name} — موجودی: ${it.available} مورد، نیاز: ${it.needed} مورد\n`;
        });
        alert(msg);
        return false;
    }

    alert("خطا در بررسی موجودی!");
    return false;
}


// ------------------------------
// منطق مشترک ثبت سفارش
// ------------------------------
async function processOrder(method) {

    try {

        // 1) اعتبارسنجی فرم
        if (!validateForm()) return;

        // 2) چک موجودی غذا
        let stockOk = await checkStockBeforePay();
        if (!stockOk) return;

        // 3) چک سبد خرید
        const cartItems = localStorage.getItem("cartItems");
        if (!cartItems || JSON.parse(cartItems).length === 0) {
            alert("سبد خرید شما خالی است.");
            return;
        }

        // 4) ذخیره اطلاعات سفارش
        const formData = new FormData(form);
        formData.append("cart_data", cartItems);

        let res = await fetch("save_info.php", {
            method: "POST",
            body: formData
        });

        if (!res.ok) {
            alert("خطا در ذخیره اطلاعات سفارش!");
            return;
        }

        // ------------------------------
        // 🔵 پرداخت آنلاین
        // ------------------------------
        if (method === "online") {
            localStorage.setItem("payType", "order");
            window.location.href = "dargah.php";
            return;
        }

        // ------------------------------
        // 🟢 پرداخت از کیف پول
        // ------------------------------
        if (method === "wallet") {

            let finalAmount = Number(localStorage.getItem("payableAmount") || 0);

            let walletRes = await fetch("get_wallet_balance.php");
            let walletData = await walletRes.json();

            if (walletData.status !== "ok") {
                alert("خطا در بررسی موجودی کیف پول.");
                return;
            }

            let walletBalance = Number(walletData.wallet || 0);

            if (walletBalance < finalAmount) {
                alert("❌ موجودی کیف پول کافی نیست.");
                return;
            }

            // اگر موجودی کافی بود → ارسال به process_payment.php
            const tempForm = document.createElement("form");
            tempForm.method = "POST";
            tempForm.action = "process_payment.php";

            tempForm.innerHTML = `
                <input type="hidden" name="pay_type" value="wallet_order">
                <input type="hidden" name="final_amount" value="${finalAmount}">
            `;

            document.body.appendChild(tempForm);
            tempForm.submit();
        }

    } catch (err) {
        console.error("خطای کلی در پردازش سفارش:", err);
        alert("خطا در برقراری ارتباط با سرور!");
    }
}
