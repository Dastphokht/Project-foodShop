// js/chart.js - نسخه با لاگ کامل
let lastChartData = null;
let lastStatsData = null;

// بارگذاری چارت
function loadChartSimple() {
    console.log("🔄 درخواست داده‌های چارت...");
    
    fetch('../Admin/getChartData.php')
        .then(res => {
            console.log("📊 وضعیت پاسخ چارت:", res.status);
            return res.json();
        })
        .then(data => {
            console.log("📈 داده‌های چارت دریافت شد:", data);
            
            // بررسی تغییر داده‌ها
            if (JSON.stringify(data) === JSON.stringify(lastChartData)) {
                console.log("📊 داده‌های چارت تغییر نکرده");
                return;
            }
            
            lastChartData = data;
            
            const canvas = document.getElementById("myChart");
            if (!canvas) {
                console.error("❌ Canvas پیدا نشد");
                return;
            }
            
            // حذف چارت قبلی
            if (window.currentChart) {
                window.currentChart.destroy();
            }
            
            const ctx = canvas.getContext("2d");
            window.currentChart = new Chart(ctx, {
                type: 'polarArea',
                data: {
                    labels: ["غذای ایرانی", "فست فود", "کباب", "سوخاری", "نوشیدنی"],
                    datasets: [{
                        label: "تعداد سفارشات",
                        data: [
                            data.irani || 0,
                            data.fastfood || 0,
                            data.kebab || 0,
                            data.sokhari || 0,
                            data.drink || 0
                        ],
                        backgroundColor: [
                            "rgba(255, 99, 132, 1)",
                            "rgba(54, 162, 235, 1)",
                            "rgba(255, 206, 86, 1)",
                            "rgba(75, 192, 192, 1)",
                            "rgba(153, 102, 255, 1)",
                        ],
                        borderWidth: 1
                    }]
                },
                options: { 
                    scales: {},
                    title: {
                        display: true,
                        text: 'آمار سفارشات - بروزرسانی: ' + new Date().toLocaleTimeString('fa-IR')
                    }
                }
            });
            
            console.log("✅ چارت بروزرسانی شد");
            
        })
        .catch(err => {
            console.error("❌ خطا در دریافت چارت:", err);
            // نمایش چارت با داده‌های پیش‌فرض
            showFallbackChart();
        });
}

// بارگذاری آمار
function loadStatsSimple() {
    console.log("🔄 درخواست آمار داشبورد...");
    
    fetch('../Admin/getDashboardStats.php')
        .then(res => {
            console.log("📊 وضعیت پاسخ آمار:", res.status);
            return res.json();
        })
        .then(stats => {
            console.log("📈 آمار داشبورد دریافت شد:", stats);
            
            // بررسی تغییر داده‌ها
            if (JSON.stringify(stats) === JSON.stringify(lastStatsData)) {
                console.log("📊 آمار تغییر نکرده");
                return;
            }
            
            lastStatsData = stats;
            
            // آپدیت باکس‌ها
            updateStatsBoxes(stats);
            console.log("✅ آمار بروزرسانی شد");
            
        })
        .catch(err => {
            console.error("❌ خطا در دریافت آمار:", err);
            // نمایش مقادیر پیش‌فرض
            updateStatsBoxes({
                new_orders: '0',
                total_sales: '0',
                total_users: '0'
            });
        });
}

// آپدیت باکس‌های آمار
function updateStatsBoxes(stats) {
    const boxes = document.querySelectorAll('.item-box-dashboard');
    
    // باکس ۱: سفارشات جدید
    if (boxes[0]) {
        const span = boxes[0].querySelector('.text_box_dashboard span');
        if (span) {
            span.textContent = stats.new_orders !== undefined ? stats.new_orders : '0';
            console.log("📦 سفارشات جدید:", span.textContent);
        }
    }
    
    // باکس ۲: مجموع فروش
    if (boxes[1]) {
        const span = boxes[1].querySelector('.text_box_dashboard span');
        if (span) {
            span.textContent = stats.total_sales !== undefined ? stats.total_sales : '0';
            console.log("💰 مجموع فروش:", span.textContent);
        }
    }
    
    // باکس ۳: تعداد کاربران
    if (boxes[2]) {
        const span = boxes[2].querySelector('.text_box_dashboard span');
        if (span) {
            span.textContent = stats.total_users !== undefined ? stats.total_users : '0';
            console.log("👥 تعداد کاربران:", span.textContent);
        }
    }
}

// چارت پیش‌فرض در صورت خطا
function showFallbackChart() {
    const canvas = document.getElementById("myChart");
    if (!canvas) return;
    
    const ctx = canvas.getContext("2d");
    
    if (window.currentChart) {
        window.currentChart.destroy();
    }
    
    window.currentChart = new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: ["غذای ایرانی", "فست فود", "کباب", "سوخاری", "نوشیدنی"],
            datasets: [{
                label: "داده‌های نمونه",
                data: [10, 5, 8, 3, 15],
                backgroundColor: [
                    "rgba(255, 99, 132, 1)",
                    "rgba(54, 162, 235, 1)",
                    "rgba(255, 206, 86, 1)",
                    "rgba(75, 192, 192, 1)",
                    "rgba(153, 102, 255, 1)",
                ],
                borderWidth: 1
            }]
        },
        options: { 
            scales: {},
            title: {
                display: true,
                text: 'داده‌های آزمایشی (خطا در اتصال)'
            }
        }
    });
}

// تابع اصلی برای بارگذاری همه
function refreshDashboard() {
    console.log("🔄 بروزرسانی داشبورد در:", new Date().toLocaleTimeString('fa-IR'));
    loadChartSimple();
    loadStatsSimple();
}

// هنگام لود صفحه
document.addEventListener('DOMContentLoaded', function() {
    console.log("🚀 صفحه لود شد");
    
    if (document.getElementById("myChart")) {
        console.log("✅ صفحه داشبورد شناسایی شد");
        
        // بارگذاری اولیه با تأخیر
        setTimeout(refreshDashboard, 1000);
        
        // رفرش خودکار هر 30 ثانیه
        setInterval(refreshDashboard, 30000);
        
        // اضافه کردن دکمه رفرش دستی
        addRefreshButton();
    }
});

// اضافه کردن دکمه رفرش
function addRefreshButton() {
    const header = document.querySelector('.header h2');
    if (!header) return;
    
    const btn = document.createElement('button');
    btn.innerHTML = '🔄 بروزرسانی';
    btn.style.cssText = `
        background: #4CAF50;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 3px;
        cursor: pointer;
        margin-right: 10px;
        font-size: 12px;
    `;
    
    btn.onclick = function() {
        this.disabled = true;
        this.innerHTML = '⏳ ...';
        
        refreshDashboard();
        
        setTimeout(() => {
            this.disabled = false;
            this.innerHTML = '🔄 بروزرسانی';
        }, 2000);
    };
    
    header.parentNode.insertBefore(btn, header);
}

// هنگام تغییر تب
document.querySelectorAll('.li-menu-dashboard').forEach(item => {
    item.addEventListener('click', function() {
        if (this.getAttribute('data-name') === 'dashboard') {
            setTimeout(() => {
                if (document.getElementById("myChart")) {
                    console.log("🔍 تب داشبورد انتخاب شد");
                    refreshDashboard();
                }
            }, 500);
        }
    });
});

// تابع برای تست دستی
window.testDashboard = function() {
    console.log("🧪 تست دستی داشبورد");
    refreshDashboard();
};