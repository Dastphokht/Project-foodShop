<?php
// اتصال به دیتابیس
require_once 'db.php';

// پردازش درخواست‌های Ajax
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $response = ['success' => false, 'message' => ''];
    
    switch ($action) {
        case 'save_discount':
            $code = $_POST['code'] ?? '';
            $percent = $_POST['percent'] ?? '';
            $is_active = $_POST['is_active'] ?? '1';
            
            if (!empty($code) && !empty($percent)) {
                // بررسی وجود کد تکراری
                $checkQuery = "SELECT code_ID FROM discount_codes WHERE code = ?";
                $checkStmt = mysqli_prepare($conn, $checkQuery);
                mysqli_stmt_bind_param($checkStmt, "s", $code);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_store_result($checkStmt);
                
                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                    $response['message'] = 'این کد تخفیف قبلاً ثبت شده است';
                } else {
                    $query = "INSERT INTO discount_codes (code, percent_off, is_active) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "sii", $code, $percent, $is_active);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $response['success'] = true;
                        $response['id'] = mysqli_insert_id($conn);
                    } else {
                        $response['message'] = 'خطا در ذخیره سازی';
                    }
                    mysqli_stmt_close($stmt);
                }
                mysqli_stmt_close($checkStmt);
            } else {
                $response['message'] = 'فیلدهای ضروری را پر کنید';
            }
            break;
            
        case 'save_shipping':
            $shipping_cost = $_POST['shipping_cost'] ?? 0;
            
            if (is_numeric($shipping_cost) && $shipping_cost >= 0) {
                // شما باید shop_ID را مشخص کنید. اگر همیشه 1 است:
                $shop_ID = 1;
                
                // بررسی وجود رکورد
                $checkQuery = "SELECT shop_ID FROM shipping_settings WHERE shop_ID = ?";
                $checkStmt = mysqli_prepare($conn, $checkQuery);
                mysqli_stmt_bind_param($checkStmt, "i", $shop_ID);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_store_result($checkStmt);
                
                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                    // به‌روزرسانی
                    $query = "UPDATE shipping_settings SET cost = ? WHERE shop_ID = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "ii", $shipping_cost, $shop_ID);
                } else {
                    // درج جدید
                    $query = "INSERT INTO shipping_settings (shop_ID, cost) VALUES (?, ?)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "ii", $shop_ID, $shipping_cost);
                }
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'خطا در ذخیره سازی';
                }
                
                mysqli_stmt_close($checkStmt);
                mysqli_stmt_close($stmt);
            } else {
                $response['message'] = 'مقدار هزینه ارسال نامعتبر است';
            }
            break;
            
        case 'delete_discount':
            $id = $_POST['id'] ?? 0;
            
            if ($id > 0) {
                $query = "DELETE FROM discount_codes WHERE code_ID = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "i", $id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'خطا در حذف';
                }
                mysqli_stmt_close($stmt);
            }
            break;
            
        case 'toggle_status':
            $id = $_POST['id'] ?? 0;
            $status = $_POST['status'] ?? 0;
            
            if ($id > 0) {
                $query = "UPDATE discount_codes SET is_active = ? WHERE code_ID = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ii", $status, $id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $response['success'] = true;
                } else {
                    $response['message'] = 'خطا در تغییر وضعیت';
                }
                mysqli_stmt_close($stmt);
            }
            break;
            
        default:
            $response['message'] = 'Action نامعتبر';
    }
    
    echo json_encode($response);
    exit;
}

// گرفتن داده‌ها برای نمایش
// گرفتن هزینه ارسال فعلی
$shop_ID = 1; // اگر shop_ID متفاوت است، باید تنظیم کنید
$shippingQuery = "SELECT cost FROM shipping_settings WHERE shop_ID = ?";
$shippingStmt = mysqli_prepare($conn, $shippingQuery);
mysqli_stmt_bind_param($shippingStmt, "i", $shop_ID);
mysqli_stmt_execute($shippingStmt);
$shippingResult = mysqli_stmt_get_result($shippingStmt);
$shippingData = mysqli_fetch_assoc($shippingResult);
$currentShipping = $shippingData ? $shippingData['cost'] : 0;
mysqli_stmt_close($shippingStmt);

// گرفتن لیست کدهای تخفیف
$discountQuery = "SELECT * FROM discount_codes ORDER BY code_ID DESC";
$discountResult = mysqli_query($conn, $discountQuery);
$discounts = [];
if ($discountResult) {
    while ($row = mysqli_fetch_assoc($discountResult)) {
        $discounts[] = $row;
    }
}
?>

    <div class="dc-main">
        <div class="dc-container">
            <div class="code-takfif">
                <h2 class="dc-title">مدیریت کد تخفیف</h2>

                <div class="dc-section-title">افزودن کد تخفیف جدید</div>

                <label class="dc-label">کد تخفیف:</label>
                <input type="text" id="dc-code" class="dc-input" placeholder="مثال: OFF50">

                <label class="dc-label">درصد تخفیف:</label>
                <input type="number" id="dc-percent" class="dc-input" placeholder="مثال: 20" min="1" max="100">

                <label class="dc-label">وضعیت:</label>
                <select id="dc-is-active" class="dc-input">
                    <option value="1">فعال</option>
                    <option value="0">غیرفعال</option>
                </select>

                <button class="dc-btn" onclick="dc_saveDiscountCode()">ذخیره کد تخفیف</button>
                <div id="dc-msg1" class="dc-success">کد تخفیف با موفقیت ذخیره شد.</div>
                
                <div class="dc-section-title">لیست کدهای تخفیف</div>
                <div id="dc-discount-list" class="dc-list">
                    <?php if (!empty($discounts)): ?>
                        <?php foreach ($discounts as $discount): ?>

                            <div class="dc-item" id="discount-<?= $discount['code_ID'] ?>">
                                    <div class="dc-item-info" >    
                                        <span class="code-display">کد: <?= htmlspecialchars($discount['code']) ?></span>
                                        <span class="percent-display">درصد: <?= $discount['percent_off'] ?>%</span>
                                    </div> 
                                    <div  class="dc-item-actions">   
                                            وضعیت: 
                                            <button class="dc-toggle status-btn <?= $discount['is_active'] ? 'active' : 'inactive' ?>" 
                                                    onclick="dc_toggleStatus(<?= $discount['code_ID'] ?>, <?= $discount['is_active'] ?>)">
                                                <?= $discount['is_active'] ? 'فعال' : 'غیرفعال' ?>
                                            </button>

                                     </div>  
                                        <button class="dc-delete" onclick="dc_deleteDiscount(<?= $discount['code_ID'] ?>)">حذف</button>
                                    
                            
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                <div class="no-data">هیچ کد تخفیفی یافت نشد.</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="hazine-ersal">
                <h2 class="dc-title">مدیریت هزینه ارسال</h2>
                <div class="dc-section-title">تنظیم هزینه ارسال</div>

                <label class="dc-label">هزینه ارسال (تومان):</label>
                <input type="number" id="dc-shipping" class="dc-input" placeholder="مثال: 30000" min="0">

                <div id="dc-current-shipping" class="dc-current">
                    هزینه ارسال فعلی: <strong><?= number_format($currentShipping) ?></strong> تومان
                </div>

                <button class="dc-btn" onclick="dc_saveShipping()">ذخیره هزینه ارسال</button>
                <div id="dc-msg2" class="dc-success">هزینه ارسال ذخیره شد.</div>
            </div>
        </div>
    </div>


<script>
// تابع کمکی برای نمایش پیام موقت
function showTempMessage(message, type) {
    const msgDiv = document.createElement('div');
    msgDiv.className = `temp-message ${type}`;
    msgDiv.textContent = message;
    document.body.appendChild(msgDiv);
    
    setTimeout(() => {
        msgDiv.remove();
    }, 3000);
}

// ذخیره کد تخفیف با Ajax
function dc_saveDiscountCode() {
    const code = document.getElementById('dc-code').value.trim();
    const percent = document.getElementById('dc-percent').value;
    const isActive = document.getElementById('dc-is-active').value;
    
    if (!code) {
        showTempMessage('لطفا کد تخفیف را وارد کنید', 'error');
        return;
    }
    
    if (!percent || percent < 1 || percent > 100) {
        showTempMessage('لطفا درصد تخفیف معتبر وارد کنید (1-100)', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'save_discount');
    formData.append('code', code);
    formData.append('percent', percent);
    formData.append('is_active', isActive);
    
    fetch('shipping.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // نمایش پیام موفقیت
            showTempMessage('کد تخفیف با موفقیت ذخیره شد', 'success');
            
            // // اضافه کردن کد جدید به لیست
            const discountList = document.getElementById('dc-discount-list');
            // const newItem = document.createElement('div');
            // newItem.className = 'discount-item';
            // newItem.id = 'discount-' + data.id;
            // newItem.innerHTML = `
            //     <span class="code-display">کد: ${code}</span>
            //     <span class="percent-display">درصد: ${percent}%</span>
            //     <span class="status-container">
            //         وضعیت: 
            //         <button class="status-btn ${isActive == '1' ? 'active' : 'inactive'}" 
            //                 onclick="dc_toggleStatus(${data.id}, ${isActive})">
            //             ${isActive == '1' ? 'فعال' : 'غیرفعال'}
            //         </button>
            //     </span>
            //     <button class="delete-btn" onclick="dc_deleteDiscount(${data.id})">حذف</button>
            // `;


            const newItem = document.createElement('div');
                newItem.className = 'dc-item';
                newItem.id = 'discount-' + data.id;

                newItem.innerHTML = `
                    <div class="dc-item-info">
                        <span class="code-display">کد: ${code}</span>
                        <span class="percent-display">درصد: ${percent}%</span>
                    </div>
                    <div class="dc-item-actions">
                        وضعیت:
                        <button class="dc-toggle status-btn ${isActive == '1' ? 'active' : 'inactive'}"
                            onclick="dc_toggleStatus(${data.id}, ${isActive})">
                            ${isActive == '1' ? 'فعال' : 'غیرفعال'}
                        </button>
                    </div>
                    <button class="dc-delete dc-item" onclick="dc_deleteDiscount(${data.id})">حذف</button>
                `;

            
            // حذف پیام "هیچ کدی یافت نشد" اگر وجود دارد
            const noDataMsg = discountList.querySelector('.no-data');
            if (noDataMsg) {
                discountList.removeChild(noDataMsg);
            }
            
            discountList.prepend(newItem);
            
            // خالی کردن فیلدها
            document.getElementById('dc-code').value = '';
            document.getElementById('dc-percent').value = '';
        } else {
            showTempMessage(data.message || 'خطا در ذخیره کد تخفیف', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showTempMessage('خطا در ارتباط با سرور', 'error');
    });
}

// ذخیره هزینه ارسال با Ajax
function dc_saveShipping() {
    const shippingCost = document.getElementById('dc-shipping').value;
    
    if (!shippingCost || shippingCost < 0) {
        showTempMessage('لطفا هزینه ارسال معتبر وارد کنید', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'save_shipping');
    formData.append('shipping_cost', shippingCost);
    
    fetch('shipping.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // نمایش پیام موفقیت
            showTempMessage('هزینه ارسال با موفقیت ذخیره شد', 'success');
            
            // به‌روزرسانی هزینه فعلی
            const currentShipping = document.querySelector('#dc-current-shipping strong');
            currentShipping.textContent = new Intl.NumberFormat().format(shippingCost);
            
            // خالی کردن فیلد
            document.getElementById('dc-shipping').value = '';
        } else {
            showTempMessage(data.message || 'خطا در ذخیره هزینه ارسال', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showTempMessage('خطا در ارتباط با سرور', 'error');
    });
}

// تغییر وضعیت فعال/غیرفعال
function dc_toggleStatus(id, currentStatus) {
    const newStatus = currentStatus ? 0 : 1;
    
    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('id', id);
    formData.append('status', newStatus);
    
    fetch('shipping.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // به‌روزرسانی دکمه در صفحه
            const statusBtn = document.querySelector(`#discount-${id} .status-btn`);
            if (statusBtn) {
                const isActive = newStatus == 1;
                statusBtn.className = `status-btn ${isActive ? 'active' : 'inactive'}`;
                statusBtn.textContent = isActive ? 'فعال' : 'غیرفعال';
                statusBtn.setAttribute('onclick', `dc_toggleStatus(${id}, ${isActive ? 1 : 0})`);
                
                // نمایش پیام موقت
                const message = isActive ? 'کد تخفیف فعال شد' : 'کد تخفیف غیرفعال شد';
                showTempMessage(message, 'success');
            }
        } else {
            showTempMessage(data.message || 'خطا در تغییر وضعیت', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showTempMessage('خطا در ارتباط با سرور', 'error');
    });
}

// حذف کد تخفیف
function dc_deleteDiscount(id) {
    if (!confirm('آیا از حذف این کد تخفیف مطمئن هستید؟')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_discount');
    formData.append('id', id);
    
    fetch('shipping.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // حذف از لیست
            const item = document.getElementById('discount-' + id);
            if (item) {
                item.remove();
            }
            
            // اگر لیست خالی شد
            const discountList = document.getElementById('dc-discount-list');
            if (discountList.children.length === 0) {
                discountList.innerHTML = '<div class="no-data">هیچ کد تخفیفی یافت نشد.</div>';
            }
            
            showTempMessage('کد تخفیف با موفقیت حذف شد', 'success');
        } else {
            showTempMessage(data.message || 'خطا در حذف کد تخفیف', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showTempMessage('خطا در ارتباط با سرور', 'error');
    });
}
</script>

