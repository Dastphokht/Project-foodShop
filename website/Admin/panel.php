<?php 
require_once "db.php";

 include 'FoodManager.php';
if (isset($_GET['delete_id']))
        {
    $ok=Delete_food($_GET['delete_id']);
    // برای جلوگیری از تکرار حذف هنگام رفرش، ریدایرکت می‌کنیم:
    if($ok)
    {
        header("Location: panel.php?tab=product");
    }
    exit;
        }
if (isset($_GET['Avalible']))
        {
    
     $ok=Avalible_food($_GET['Avalible'],$_GET['set']);
    // برای جلوگیری از تکرار حذف هنگام رفرش، ریدایرکت می‌کنیم:
    if($ok)
    {
        header("Location: panel.php?tab=product");
    }
    exit;
        }



?>


<!DOCTYPE html>
<html lang="en">
    <?php  
        require_once 'SessionCheck.php';?>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/AddFood.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://kit.fontawesome.com/9d805968ce.js" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>panel</title>
</head>

<body>
    <section class="dashboard">
        <aside>
            <ul class="menu_dashboard">
                <li class="logo-dashboard li-menu-dashboard"><a href="#">
                        <img src="../asset/icon/logo.png" alt="">
                    </a></li>
                <li data-name="dashboard" class="active-dashboard li-menu-dashboard"><a href="#">داشبورد<i
                            class="fas fa-home"></i></a></li>
                <li data-name="product" class="li-menu-dashboard"><a href="#">مدیریت غذاها<i class="fa fa-cutlery" aria-hidden="true"></i>
                </a></li>
                <li data-name="addProduct" class="li-menu-dashboard"><a href="#">افزودن غذا<i class="fa fa-plus" aria-hidden="true"></i></a></li>

                <li data-name="order" class="li-menu-dashboard"><a href="#">مدیریت سفارشات<i
                            class="fa-brands fa-shopify"></i></a></li>
                <li data-name="user" class="li-menu-dashboard"><a href="#">مدیریت کاربران<i
                            class="fa-solid fa-user"></i></a></li>
                <li class="li-menu-dashboard" data-name="cost"><a href="#">مدیریت هزینه ها<i class="fa-solid fa-sack-dollar"></i></a></li>
                <li class="li-menu-dashboard"><a href="../index.html">ورود به سایت<i class="fa-solid fa-globe"></i></a></li>
            <!-- ++++++++++ logout   -->
                <li class="li-menu-dashboard"><a href="../Admin/LogOut.php">خروج <i class="fa-solid fa-arrow-right-from-bracket"></i></a></li>
          
            </ul>
        </aside>
        <main data-id="dashboard" id="dashboard" class="index-main" dir="rtl">
            <div class="top-dashboard">
                <div class="action">
                    <div class="profile" onclick="toggleMenu()">
                        <img src="../asset/icon/user-profile.png" alt="profile img">
                    </div>
                    <div class="menu-profile-dashboard">
                        <h3>محمدرضا احمدپور
                            <br> <span>طراح و برنامه نویس وب</span>
                        </h3>
                        <ul>
                            <li><img src="../asset/icon/user-profile.png" alt=""><a href="#">پروفایل من</a></li>
                            <li><img src="../asset/icon/edit.png" alt=""><a href="#">تغییر اطلاعات</a></li>
                            <li><img src="../asset/icon/inbox.png" alt=""><a href="#">پیام ها</a></li>
                            <li><img src="../asset/icon/setting.png" alt=""><a href="#">تنظیمات</a></li>
                            <li><img src="../asset/icon/help.jpg" alt=""><a href="#">پشتیبانی</a></li>
                            <li><img src="../asset/icon/logout.png" alt=""><a href="#"> خروچ</a></li>
                        </ul>
                    </div>
                </div>
                <div class="search">
                    <input type="text" placeholder="جستجو..." class="inputSearch">
                    <button class="btnSearch">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="box-dashboard">
                <div class="item-box-dashboard">
                    <div class="icon_box_dashboard order_dash"><i class="fa-solid fa-cart-shopping"></i></div>
                    <div class="text_box_dashboard">
                        <h3>تعداد سفارش</h3>
                        <p><span> 18 </span>سفارش جدید</p>
                    </div>
                </div>
                <div class="item-box-dashboard">
                    <div class="icon_box_dashboard sale_dash"><i class="fa-regular fa-credit-card"></i></div>
                    <div class="text_box_dashboard">
                        <h3>تعداد فروش</h3>
                        <p>
                            <span>12</span>
                            فروش
                        </p>
                    </div>
                </div>
                <div class="item-box-dashboard">
                    <div class="icon_box_dashboard user_dash"><i class="fa-solid fa-user"></i></div>
                    <div class="text_box_dashboard">
                        <h3>تعداد کاربر</h3>
                        <p><span>255 </span>کاربر</p>
                    </div>
                </div>

            </div>
            <div class="chart-dashboard">
                <canvas id="myChart" width="300" height="150"></canvas>

            </div>
        </main>
        <main class="prodcut_main" data-id="product">
            <ul class="product-menu">

                <li data-name="iranianFood-submenu" class="active-submenu">مدیریت غذای ایران</li>
                <li data-name="fastFood-submenu">مدیریت فست فود</li>
                <li data-name="kebab-submenu">مدیریت کباب</li>
                <li data-name="sokhari-submenu">مدیریت سوخاری</li>
                <li data-name="drink-submenu">مدیریت نوشیدنی</li>
            </ul>
                    <div id="foods-container">
                    <!-- محتوای غذاها اینجا با AJAX بارگذاری میشه -->
                    </div>
        </main>
        <main class="order_main" data-id="order">

        
        <div class="orders-header">
            <div>شماره سفارش</div>
            <div>کاربر</div>
            <div>تاریخ</div>
            <div>قیمت</div>
            <div>وضعیت</div>
            <div></div>
        </div>
                <?php include "orders.php"; ?>

        </main>

        <main data-id="addProduct" id="addfood">
            <!-- <form class="formAddProduct" action=""> -->
                <div class="food_section">
                    
                    <?php



                                        $food = null;

                                            if (isset($_GET['editId'])) {
                                                $result = Select_food($_GET['editId']);
                                                if ($result && mysqli_num_rows($result) > 0) {
                                                    $food = mysqli_fetch_assoc($result);
                                                }
                                            }
                    ?>


                    <form class="form_food" action="AddFood.php" method="POST" enctype="multipart/form-data">
                    
                        <h2 style="text-align:center;margin-bottom:15px;">افزودن غذای جدید</h2>
                    
                            <?php if ($food): ?>
                            <input type="hidden" name="food_id" value="<?= $food['food_ID'] ?>">
                             <?php endif; ?>

                        <h3 class="label_food">نام غذا:</h3>
                        <input type="text" name="food_info[name]" class="input_food" placeholder="مثلاً پیتزا مخصوص" id="name" value="<?= $food ? $food['food_Name'] : '' ?>">
                        <span class="error-message" id="nameError"></span>
                    
                        <h3 class="label_food">توضیحات غذا:</h3>
                        <textarea class="input_area" name="food_info[description]" placeholder="توضیحات..." id="description"  ><?= $food ? $food['description'] : '' ?></textarea>
                        <span class="error-message" id="descriptionError"></span>
                    
                        <h3 class="label_food">قیمت:</h3>
                        <input type="text" class="input_food"  name="food_info[price]"  placeholder="مثلا 50.000 تومان" id="price"  value="<?= $food ? $food['price'] : '' ?>">
                        <span class="error-message" id="priceError"></span>
                         <div class="counter-qty">
                         <div class="food-select-box">
                            <label for="food-type" class="food-label">انتخاب نوع غذا</label>
                            <select id="food-type" name="food_info[type]" class="food-dropdown">
                                    <option value="" disabled <?= !$food ? 'selected' : '' ?>>انتخاب کنید...</option>

                                    <option value="irani"     <?= ($food && $food['Type'] == 'irani') ? 'selected' : '' ?>>غذای ایرانی</option>
                                    <option value="fastfood"  <?= ($food && $food['Type'] == 'fastfood') ? 'selected' : '' ?>>فست فود</option>
                                    <option value="kebab"     <?= ($food && $food['Type'] == 'kebab') ? 'selected' : '' ?>>کباب</option>
                                    <option value="sokhari"   <?= ($food && $food['Type'] == 'sokhari') ? 'selected' : '' ?>>سوخاری</option>
                                    <option value="drink"     <?= ($food && $food['Type'] == 'drink') ? 'selected' : '' ?>>نوشیدنی</option>

                                </select>

                          </div>
                    
                        <div class="admin-qty-box">
                            <label for="qty-input">تعداد :</label>
                            <input type="number" id="qty-input" name="food_info[qty]" placeholder="مثلاً 200" value="<?= $food ? $food['Quantity'] : '' ?>">
                        </div>


                         </div>
                         
                        <h3 class="label_food">تصویر غذا:</h3>
                        <input type="file"  name="food_image" class="input_food" id="image" accept="image/*" value="<?= $food ? $food['img_url'] : '' ?>">
                        <span class="error-message" id="imageError"></span>
                        <div style="margin-top:5px;">
                            <?php 
                            // تعیین آدرس تصویر
                            $imageSrc = '';
                            if ($food && !empty($food['img_url'])) {
                                $imagePath = "../asset/img/FoodsImage/" . $food['img_url'];
                                $imageSrc = file_exists($imagePath) ? $imagePath : '';
                            }
                            ?>
                            
                            <img id="imagePreview" 
                                src="<?= $imageSrc ?>" 
                                alt="پیش‌نمایش تصویر" 
                                style="max-width:150px; <?= $imageSrc ? 'display:block;' : 'display:none;' ?> border:1px solid #ccc; padding:3px;">
                        </div>
                
                        <input type="submit" class="btn_submit_food" value="ثبت غذا"></input>
                    
                    </form>
                    </div>
                   
            </div>
            <!-- </form> -->
        </main>


        <main class="user" data-id="user">
                <?php include "users.php"; ?>
        </main>

        <main class="" data-id="cost">
        <div class="dc-main">
    <div class="dc-container">
        <h2 class="dc-title">مدیریت کد تخفیف</h2>

        <div class="dc-section-title">افزودن کد تخفیف جدید</div>

        <label class="dc-label">کد تخفیف:</label>
        <input type="text" id="dc-code" class="dc-input" placeholder="مثال: OFF50">

        <label class="dc-label">درصد تخفیف:</label>
        <input type="number" id="dc-percent" class="dc-input" placeholder="مثال: 20">

        <label class="dc-label">وضعیت:</label>
        <select id="dc-is-active" class="dc-input">
            <option value="1">فعال</option>
            <option value="0">غیرفعال</option>
        </select>

        <button class="dc-btn" onclick="dc_saveDiscountCode()">ذخیره کد تخفیف</button>
        <div id="dc-msg1" class="dc-success">کد تخفیف با موفقیت ذخیره شد.</div>

        <br><br>

        <div class="dc-section-title">تنظیم هزینه ارسال</div>

        <label class="dc-label">هزینه ارسال (تومان):</label>
        <input type="number" id="dc-shipping" class="dc-input" placeholder="مثال: 30000">

        <button class="dc-btn" onclick="dc_saveShipping()">ذخیره هزینه ارسال</button>
        <div id="dc-msg2" class="dc-success">هزینه ارسال ذخیره شد.</div>
    </div>
</div>
        </main>

    </section>
    <script src="../js/AddFood.js"></script>
    <script src="../js/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0/dist/Chart.min.js"></script>
   <!-- <script src="js/main.js"></script> -->
   <script>
    let inputs = document.querySelectorAll("input[type=file]");
    
    inputs.forEach((input, index) => {
        let textInput = document.querySelectorAll('.textInput')[index];
        let imgPrev = document.querySelectorAll('.image-prev')[index];
        let iconPrev = document.querySelectorAll('.icon_prev')[index];
    
        input.addEventListener("change", () => {
            let inputImage = input.files[0];
            if (inputImage) {
                const reader = new FileReader();
                iconPrev.style.display = 'none';
                imgPrev.style.display = 'block';
                textInput.style.display = 'none';
    
                reader.addEventListener('load', function () {
                    imgPrev.setAttribute('src', this.result);
                });
                reader.readAsDataURL(inputImage);
            } else {
                iconPrev.style.display = 'block';
                imgPrev.style.display = 'none';
                textInput.style.display = 'block';
                imgPrev.setAttribute('src', '');
            }
        });
    });
    
            // var ctx = document.getElementById("myChart").getContext("2d");
            // var myChart = new Chart(ctx, {
            //     type: 'polarArea',
            //     data: {
            //         labels: ["غذای ایرانی", "فست فود", "کباب ", "سوخاری", "نوشیدنی "],
            //         datasets: [
            //             {
            //                 label: "",
            //                 data: [15, 2, 13, 5, 10],
            //                 backgroundColor: [
            //                     "rgba(255, 99, 132, 1)",
            //                     "rgba(54, 162, 235, 1)",
            //                     "rgba(255, 206, 86, 1)",
            //                     "rgba(75, 192, 192, 1)",
            //                     "rgba(153, 102, 255, 1)",
            //                 ],
    
            //                 borderWidth: 1
            //             }
            //         ]
            //     },
            //     options: {
            //         scales: {
    
            //         }
            //     }
            // });
    

    
            function toggleMenu() {
                const togglemenu = document.querySelector('.menu-profile-dashboard');
                togglemenu.classList.toggle("active");
            }
            // search
            let input = document.querySelector('.inputSearch');
            let btnSearch = document.querySelector('.btnSearch');
            let search = document.querySelector('.search');
            console.log(btnSearch)
            btnSearch.addEventListener('click', function () {
                console.log('si')
                search.classList.toggle('active');
            })
    
    
            ///page context
            const limenu = document.querySelectorAll('.li-menu-dashboard')
            const mains = document.querySelectorAll('main');
    
            for (let i = 0; i < limenu.length; i++) {
                limenu[i].addEventListener('click', () => {
                    for (let a = 0; a < limenu.length; a++) {
                        limenu[a].classList.remove('active-dashboard');
                    }
                    limenu[i].classList.add('active-dashboard');
    
    
                    const target = limenu[i].getAttribute('data-name');
    
                    for (let k = 0; k < mains.length; k++) {
                        mains[k].style.display = 'none';
                        if (target == mains[k].getAttribute('data-id')) {
                            mains[k].style.display = 'block'
                        }
                    }
                })
            }
    
    
            ///submenu tab
            const lisubmenu = document.querySelectorAll('.product-menu li')
            const subdivs = document.querySelectorAll('.subdivs');
    
            for (let i = 0; i < lisubmenu.length; i++) {
                lisubmenu[i].addEventListener('click', () => {
                    for (let a = 0; a < lisubmenu.length; a++) {
                        lisubmenu[a].classList.remove('active-submenu');
                    }
                    lisubmenu[i].classList.add('active-submenu');
    
    
                    const target = lisubmenu[i].getAttribute('data-name');
    
                    for (let k = 0; k < subdivs.length; k++) {
                        subdivs[k].style.display = 'none';
                        if (target == subdivs[k].getAttribute('data-id')) {
                            subdivs[k].style.display = 'block'
                        }
                    }
                })
            }
    
    
    

//    ++++++++++++++++++++++++ بخش باز کردن تب از صفحه php+++++++++++++
     // فعال‌سازی تب از طریق پارامتر URL
  function activateTabByName(tabName) {
    const menuItem = document.querySelector(`.li-menu-dashboard[data-name="${tabName}"]`);
    if (menuItem) {
      menuItem.click(); // استفاده از سیستم موجود نمایش تب
    }
  }

  // بررسی ?tab=addProduct
  const params = new URLSearchParams(window.location.search);
  if (params.has('tab')) {
    const tab = params.get('tab');
    activateTabByName(tab);

    // اگر خواستی پیام موفقیت بده
    if (params.get('status') === 'success') {
      // ساده‌ترین حالت:
      alert("غذا با موفقیت ذخیره شد");
    }
  }

// order management
function toggleOrder(el) {
    const summary = el.parentElement;
    summary.classList.toggle("open");
}

function changeStatus(select, event) {
    event.stopPropagation();

    const summary = select.closest(".order-summary");

    summary.classList.remove("status-registered", "status-preparing", "status-delivering");

    if (select.value === "registered") summary.classList.add("status-registered");
    if (select.value === "preparing") summary.classList.add("status-preparing");
    if (select.value === "delivering") summary.classList.add("status-delivering");
}


// مدیریت هزینه ها
function dc_saveDiscountCode() {
        const code = document.getElementById("dc-code").value;
        const percent = document.getElementById("dc-percent").value;
        const is_active = document.getElementById("dc-is-active").value;

        if (!code || !percent) {
            alert("تمام فیلدها باید پر شوند.");
            return;
        }

        const data = {
            code,
            percent_off: percent,
            is_active
        };

        localStorage.setItem("discount_code", JSON.stringify(data));

        document.getElementById("dc-msg1").style.display = "block";
        setTimeout(() => document.getElementById("dc-msg1").style.display = "none", 2500);
    }

    function dc_saveShipping() {
        const cost = document.getElementById("dc-shipping").value;

        if (!cost) {
            alert("لطفاً هزینه ارسال را وارد کنید.");
            return;
        }

        localStorage.setItem("shipping_cost", cost);

        document.getElementById("dc-msg2").style.display = "block";
        setTimeout(() => document.getElementById("dc-msg2").style.display = "none", 2500);
    }

        </script>
        <script src="../js/order.js"></script>
        <script src="../js/foodManager.js"></script>


</body>

</html>