// order.js
function changeStatus(orderId, newStatus) {
    const selectElement = event.target;
    const orderCard = selectElement.closest('.order-card');
    const statusWrapper = selectElement.closest('.status-wrapper');
    
    // ذخیره مقدار قبلی در صورت نیاز به بازگشت
    const oldStatus = selectElement.value;
    
    const formData = new FormData();
    formData.append("orderId", orderId);
    formData.append("status", newStatus);

    fetch("updateStatus.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data.trim() === "success") {
            console.log("Order status updated");
            
            // بروزرسانی UI
            if (newStatus === 'canceled') {
                // نمایش پیام موفقیت
                showNotification("سفارش لغو شد و مبلغ به کیف پول کاربر بازگردانده شد", "success");
                
                // جایگزینی select با متن ثابت
                statusWrapper.innerHTML = `
                    <div class="canceled-status" style="color: #dc3545; font-weight: bold;">
                        لغو شده (غیرقابل تغییر)
                    </div>
                `;
                
                // اضافه کردن کلاس به کارت سفارش برای تغییر ظاهر
                orderCard.classList.add('order-canceled');
                
            } else {
                showNotification("وضعیت سفارش به‌روزرسانی شد", "success");
                
                // فقط آپدیت مقدار select
                selectElement.value = newStatus;
            }
            
        } else if (data.trim() === "canceled_no_change") {
            showNotification("سفارش لغو شده قابل تغییر نیست", "error");
            // بازگرداندن به مقدار قبلی
            selectElement.value = oldStatus;
        } else {
            showNotification("خطا در بروزرسانی وضعیت", "error");
            // بازگرداندن به مقدار قبلی
            selectElement.value = oldStatus;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification("خطا در ارتباط با سرور", "error");
        // بازگرداندن به مقدار قبلی
        selectElement.value = oldStatus;
    });
}

// تابع کمکی برای نمایش نوتیفیکیشن
function showNotification(message, type) {
    // اگر قبلاً نوتیفیکیشن وجود دارد، حذفش کن
    const oldNotif = document.querySelector('.status-notification');
    if (oldNotif) oldNotif.remove();
    
    // ایجاد نوتیفیکیشن جدید
    const notif = document.createElement('div');
    notif.className = `status-notification ${type}`;
    notif.textContent = message;
    notif.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        ${type === 'success' ? 'background-color: #4CAF50;' : 'background-color: #f44336;'}
    `;
    
    document.body.appendChild(notif);
    
    // حذف خودکار بعد از 3 ثانیه
    setTimeout(() => {
        if (notif.parentNode) {
            notif.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notif.remove(), 300);
        }
    }, 3000);
}

// // اضافه کردن استایل‌های انیمیشن (فقط یک بار)
// if (!document.querySelector('#notification-styles')) {
//     const style = document.createElement('style');
//     style.id = 'notification-styles';
//     style.textContent = `
//         @keyframes slideIn {
//             from { transform: translateX(100%); opacity: 0; }
//             to { transform: translateX(0); opacity: 1; }
//         }
//         @keyframes slideOut {
//             from { transform: translateX(0); opacity: 1; }
//             to { transform: translateX(100%); opacity: 0; }
//         }
//         .order-canceled {
//             opacity: 0.7;
//             background-color: #f8f9fa;
//         }
       
//     `;
//     document.head.appendChild(style);
// }