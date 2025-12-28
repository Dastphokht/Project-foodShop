// تابع برای بارگذاری هزینه ارسال از پایگاه داده
function loadShippingCost() {
    fetch('panel.php?action=get_shipping_cost')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cost = data.cost;
                const formattedCost = new Intl.NumberFormat('fa-IR').format(cost);
                document.querySelector("#current-shipping-cost").innerText = formattedCost;
                document.getElementById("dc-shipping").value = cost;
            }
        })
        .catch(error => {
            console.error('Error loading shipping cost:', error);
        });
}

// تابع برای ذخیره هزینه ارسال
function saveShippingCost() {
    const costInput = document.getElementById("dc-shipping");
    const cost = costInput.value;
    
    if (!cost || cost <= 0) {
        alert("لطفاً هزینه ارسال معتبر وارد کنید.");
        return;
    }
    
    // ایجاد FormData برای ارسال داده
    const formData = new FormData();
    formData.append('action', 'save_shipping');
    formData.append('shipping_cost', cost);
    
    // نمایش لودینگ
    const saveButton = document.querySelector('.dc-btn');
    const originalText = saveButton.textContent;
    saveButton.textContent = 'در حال ذخیره...';
    saveButton.disabled = true;
    
    // ارسال درخواست به سرور
    fetch('panel.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // به‌روزرسانی نمایش هزینه
            const formattedCost = new Intl.NumberFormat('fa-IR').format(cost);
            document.querySelector("#current-shipping-cost").innerText = formattedCost;
            
            // نمایش پیام موفقیت
            showMessage(data.message, 'success');
        } else {
            // نمایش پیام خطا
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving shipping cost:', error);
        showMessage('خطا در ارتباط با سرور', 'error');
    })
    .finally(() => {
        // بازگرداندن دکمه به حالت اول
        saveButton.textContent = originalText;
        saveButton.disabled = false;
    });
}

// تابع برای نمایش پیام
function showMessage(message, type) {
    const msgElement = document.getElementById("dc-msg2");
    msgElement.textContent = message;
    msgElement.style.display = "block";
    msgElement.style.color = type === 'success' ? 'green' : 'red';
    msgElement.style.backgroundColor = type === 'success' ? '#d4edda' : '#f8d7da';
    
    setTimeout(() => {
        msgElement.style.display = "none";
    }, type === 'success' ? 2000 : 3000);
}

// بارگذاری هزینه ارسال هنگام لود صفحه
document.addEventListener("DOMContentLoaded", function() {
    loadShippingCost();
});

// اضافه کردن event listener برای کلید Enter
document.getElementById("dc-shipping").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        saveShippingCost();
    }
});