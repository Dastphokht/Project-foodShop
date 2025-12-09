// ----------------------------
//  تعریف‌های اصلی (بیرون از DOMContentLoaded)
// ----------------------------

// سبد خرید از localStorage
// سبد خرید از localStorage (با یکسان‌سازی نوع داده‌ها)
let cart = [];
const storedCart = localStorage.getItem("cartItems");

if (storedCart) {
    try {
        const parsed = JSON.parse(storedCart);
        if (Array.isArray(parsed)) {
            cart = parsed.map(item => ({
                id: String(item.id),                           // همیشه رشته
                name: item.name,
                price: Number(item.price),                     // عدد
                qty: Number(item.qty ?? item.quantity ?? 1)    // تعداد
            }));
        }
    } catch (e) {
        console.error("خطا در خواندن سبد از localStorage:", e);
        cart = [];
    }
}


// بررسی لاگین کاربر
const isUserLoggedIn = () => {
    const meta = document.querySelector('meta[name="user-logged-in"]');
    return meta && meta.getAttribute('content') === 'true';
};

function areCartsEqual(localCart, serverCart) {
    if (!Array.isArray(localCart) || !Array.isArray(serverCart)) return false;

    const localMap = new Map();
    localCart.forEach(it => {
        localMap.set(String(it.id), Number(it.qty));
    });

    if (localMap.size !== serverCart.length) return false;

    for (const it of serverCart) {
        const id = String(it.id);
        const q = Number(it.qty);
        if (!localMap.has(id) || localMap.get(id) !== q) {
            return false;
        }
    }
    return true;
}


// لود سبد از دیتابیس
async function loadCartFromServer() {
    if (!isUserLoggedIn()) return;

    try {
        const res = await fetch("get_cart.php");
        const data = await res.json();

        if (data.status !== "ok" || !Array.isArray(data.cart)) {
            return;
        }

        const serverCart = data.cart.map(item => ({
            id: String(item.id),
            name: item.name,
            price: Number(item.price),
            qty: Number(item.qty)
        }));

        if (cart.length === 0) {
            // فقط دیتابیس داریم
            cart = serverCart;
        } else {
            // اگر local و سرور کاملاً یکسان هستند → هیچ کاری نکن
            if (areCartsEqual(cart, serverCart)) {
                // برای اطمینان فقط نرمال‌سازی نوع‌ها
                cart = cart.map(it => ({
                    id: String(it.id),
                    name: it.name,
                    price: Number(it.price),
                    qty: Number(it.qty)
                }));
            } else {
                // اختلاف دارند → merge (سرور + مهمان)
                const mergedMap = new Map();

                serverCart.forEach(it => {
                    mergedMap.set(it.id, { ...it });
                });

                cart.forEach(it => {
                    const id = String(it.id);
                    const existing = mergedMap.get(id);
                    if (existing) {
                        existing.qty += Number(it.qty);
                    } else {
                        mergedMap.set(id, {
                            id,
                            name: it.name,
                            price: Number(it.price),
                            qty: Number(it.qty)
                        });
                    }
                });

                cart = Array.from(mergedMap.values()).map(it => ({
                    ...it,
                    qty: Math.min(it.qty, 10)
                }));
            }
        }

        localStorage.setItem("cartItems", JSON.stringify(cart));

    } catch (err) {
        console.log("خطا در دریافت/ادغام سبد خرید:", err);
    }
}




// ذخیره در localStorage + دیتابیس
function saveCart() {
    localStorage.setItem("cartItems", JSON.stringify(cart));
    if (isUserLoggedIn()) syncCartToServer();
}

// ارسال به سبد خرید دیتابیس
function syncCartToServer() {
    fetch("sync_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cart })
    }).catch(err => console.error("Sync error:", err));
}

// فرمت قیمت
function formatPrice(num) {
    return Number(num).toLocaleString("fa-IR") + " تومان";
}

// ذخیره مبلغ نهایی
function saveFinalAmount(amount) {
    let cleanAmount = String(amount).replace(/[^0-9\u06F0-\u06F9]/g, '');
    cleanAmount = cleanAmount.replace(/[\u06F0-\u06F9]/g, d => d.charCodeAt(0) - 1776);
    localStorage.setItem("finalAmount", Math.round(Number(cleanAmount || 0)));
}

// رندر سبد خرید
function renderCart() {
    const cartContainer = document.querySelector(".cart-items");
    const totalEl = document.querySelector(".jamkol_txt strong");

    cartContainer.innerHTML = "";
    let total = 0;

    if (cart.length === 0) {
        cartContainer.innerHTML = "<p class='empty-cart-msg'>سبد خرید شما خالی است.</p>";
        saveFinalAmount(0);
        totalEl.textContent = formatPrice(0);
        return;
    }

    cart.forEach((item, index) => {
        total += item.price * item.qty;

        const div = document.createElement("div");
        div.classList.add("cart-item");
        div.innerHTML = `
            <span class="item-name">${item.name}</span>
            <div class="item-controls">
                <button class="quantity-btn decrease" data-index="${index}">-</button>
                <span class="quantity">${item.qty}</span>
                <button class="quantity-btn increase" data-index="${index}">+</button>
            </div>
            <span class="item-price">${formatPrice(item.price * item.qty)}</span>
            <button class="remove-item" data-index="${index}">حذف</button>
        `;

        div.querySelector(".decrease").addEventListener("click", e => {
            const i = e.target.dataset.index;
            if (cart[i].qty > 1) cart[i].qty--;
            else cart.splice(i, 1);
            saveCart();
            renderCart();
        });

        div.querySelector(".increase").addEventListener("click", e => {
            const i = e.target.dataset.index;
            if (cart[i].qty < 10) cart[i].qty++;
            saveCart();
            renderCart();
        });

        div.querySelector(".remove-item").addEventListener("click", e => {
            const i = e.target.dataset.index;
            cart.splice(i, 1);
            saveCart();
            renderCart();
        });

        cartContainer.appendChild(div);
    });

    saveFinalAmount(total);
    totalEl.textContent = formatPrice(total);
}



// ----------------------------
//     DOMContentLoaded
// ----------------------------

document.addEventListener("DOMContentLoaded", async () => {

    const discountInput = document.querySelector(".discount-input");
    const applyBtn = document.querySelector(".apply-discount");
    const payBtn = document.querySelector(".pay-btn");

    // 1 — لود سبد خرید از دیتابیس
    await loadCartFromServer();

    // 2 — نمایش سبد روی صفحه
    renderCart();

    if (isUserLoggedIn() && cart.length) {
        syncCartToServer();
    }
    
    // ------------------------------
    // تخفیف
    // ------------------------------
    applyBtn.addEventListener("click", () => {
        let total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        let code = discountInput.value.trim().toUpperCase();
        const totalEl = document.querySelector(".jamkol_txt strong");

        if (code === "SAVE10") {
            total *= 0.9;
        }

        totalEl.textContent = formatPrice(total);
        saveFinalAmount(total);
    });

    // ------------------------------
    // پرداخت
    // ------------------------------
    payBtn.parentElement.addEventListener("click", (e) => {
        if (cart.length === 0) {
            e.preventDefault();
            alert("سبد خرید خالی است.");
            return;
        }

        const totalEl = document.querySelector(".jamkol_txt strong");
        const finalAmountString = totalEl.textContent;
        const clean = finalAmountString.replace(/[^0-9\u06F0-\u06F9]/g, '');

        saveFinalAmount(clean);
        localStorage.setItem("payType", "order");
        localStorage.setItem("payableAmount", clean);
    });

});
