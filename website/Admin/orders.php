<?php
require_once "db.php";

$sql = "SELECT orders.*, users.user_Name 
        FROM orders 
        JOIN users ON users.user_ID = orders.user_ID
        ORDER BY orders.order_ID DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "<p>خطا در دریافت سفارش‌ها</p>";
    return;
}

while ($row = mysqli_fetch_assoc($result)):

    // آیتم‌های هر سفارش
    $detailSQL = "SELECT order_items.*, foods.food_Name, foods.img_url
                  FROM order_items
                  JOIN foods ON foods.food_ID = order_items.food_ID
                  WHERE order_items.order_ID = ".$row['order_ID'];
    $details = mysqli_query($conn, $detailSQL);
?>

<div class="order-card">

    <div class="order-summary">
        <div><?= $row['order_ID'] ?></div>
        <div><?= $row['user_Name'] ?></div>
        <div><?= $row['created_At'] ?></div>
        <div><?= number_format($row['total_Price']) ?></div>

        <div class="status-wrapper">
        
             <?php if ($row['status'] != 'canceled'): ?>
        <select class="status-select"
                onchange="changeStatus(<?= $row['order_ID'] ?>, this.value)">
            
            <option value="registered"  <?= $row['status']=='registered'?'selected':'' ?>>ثبت سفارش</option>
            <option value="preparing"   <?= $row['status']=='preparing'?'selected':'' ?>>در حال آماده‌سازی</option>
            <option value="Sending"     <?= $row['status']=='Sending'?'selected':'' ?>>در حال ارسال</option>
            <option value="delivered"   <?= $row['status']=='delivered'?'selected':'' ?>>تحویل داده شده</option>
            <option value="canceled"    <?= $row['status']=='canceled'?'selected':'' ?>>لغو سفارش</option>
        </select>
    <?php else: ?>
        <div class="canceled-status" style="color: #dc3545; font-weight: bold;">
            لغو شده (غیرقابل تغییر)
        </div>
    <?php endif; ?>
        
        
        </div>

        <div class="arrow" onclick="toggleOrder(this)">▾</div>
    </div>

    <div class="order-details">
        <?php while ($item = mysqli_fetch_assoc($details)): ?>
            <div class="product-item">
                <img src="../asset/img/FoodsImage/<?= $item['img_url'] ?>">

                <div class="info">
                    <h4><?= $item['food_Name'] ?></h4>
                    <h5>قیمت واحد: <?= number_format($item['price']) ?></h5>
                    <h5>تعداد: <?= $item['quantity'] ?></h5>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</div>

<?php endwhile; ?>
