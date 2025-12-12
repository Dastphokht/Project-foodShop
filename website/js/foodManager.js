// جلوگیری از XSS ساده
function escapeHtml(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/[&<>"']/g, function(m) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m];
    });
}

// فرمت کردن عدد (مثلاً 530000 -> 530,000) — اختیاری ولی ظاهر بهتر
function numberWithCommas(x) {
    if (x === null || x === undefined) return '';
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}




document.addEventListener('DOMContentLoaded', () => {
    const apiUrl = 'api_foods.php'; // <- اگر فایلت api_foods.php هست، اینجا عوض کن
    const menuItems = document.querySelectorAll('.product-menu li');
    const foodsContainer = document.getElementById('foods-container');

    // map از data-name به type که تو DB استفاده می‌کنی
    const typeMap = {
      'iranianFood-submenu': 'irani',
      'fastFood-submenu': 'fastfood',
      'kebab-submenu': 'kebab',
      'sokhari-submenu': 'sokhari',
      'drink-submenu': 'drink'
    };

    let currentType = 'irani'; // مقدار جاری تب

    async function loadFoods(type) {
        currentType = type;
        try {
            const res = await fetch(`${apiUrl}?action=list&type=${encodeURIComponent(type)}`);
            console.log('list response status:', res.status);
            const text = await res.text();              // ابتدا متن را بگیر
            console.log('list raw response:', text);    // لاگ خام پاسخ
            let data;
            try {
                data = JSON.parse(text);
            } catch (err) {
                foodsContainer.innerHTML = 'پاسخ سرور معتبر نیست';
                console.error('JSON parse error', err);
                return;
            }

            if (data.status === 'success') {
                renderFoods(data.foods || []);
            } else {
                foodsContainer.innerHTML = 'خطا در دریافت اطلاعات: ' + (data.message || '');
            }
        } catch (e) {
            console.error('fetch list error', e);
            foodsContainer.innerHTML = 'خطا در ارتباط با سرور';
        }
    }

    function renderFoods(foods) {
    if (!Array.isArray(foods) || foods.length === 0) {
        foodsContainer.innerHTML = '<p>هیچ غذایی برای این دسته وجود ندارد.</p>';
        return;
    }

    let html = '';
    foods.forEach(food => {
        html += `
            <div class="prodcut_items_box" data-id="${food.food_ID}">
                <div class="img_text_product_item">
                    <img src="../asset/img/FoodsImage/${food.img_url}" alt="${escapeHtml(food.food_Name)}">
                    <div class="text_prodcut_item_box">
                        <h3>${escapeHtml(food.food_Name)}</h3>
                        <div class="price_prodcut_box">
                            <h4> قیمت: </h4>
                            <h5>${numberWithCommas(food.price)} تومان</h5>
                        </div>
                        ${food.Quantity ? `
                        <div class="count_prodcut_box">
                            <h4> تعداد: </h4>
                            <h5>${escapeHtml(food.Quantity)}</h5>
                        </div>` : ''}
                    </div>
                </div>

                <div class="buttons_product_item">
                    <div class="edite_delete_buttons_product_item">
                        <a class="edit_product_item" href="panel.php?tab=addProduct&editId=${food.food_ID}">ویرایش</a>
                        <a href="#" class="delete_product_item" data-id="${food.food_ID}">حذف محصول</a>
                    </div>

                    <a href="#"
                    class="disable_product_item ${food.available == 0 ? 'inactive-status' : 'active-status'}"
                    data-id="${food.food_ID}"
                    data-status="${food.available}">
                        ${food.available == 0 ? 'غیر فعال' : 'فعال'}
                    </a>


                </div>
            </div>
        `;
    });

    foodsContainer.innerHTML = html;

    // helpers for safety formatting (define once in file)
    function attachListeners() {
        // حذف
        document.querySelectorAll('.delete_product_item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.getAttribute('data-id');
                if (!id) return alert('شناسه غذا پیدا نشد');
                if (confirm('آیا مطمئن هستید که می‌خواهید این غذا را حذف کنید؟')) {
                    deleteFood(id);
                }
            });
        });

        // فعال/غیرفعال (نمایش به سبک anchor همان css)
        document.querySelectorAll('.disable_product_item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.getAttribute('data-id');
                const status = parseInt(btn.getAttribute('data-status')) || 0;
                const newStatus = status === 1 ? 0 : 1;
                toggleAvailable(id, newStatus);
            });
        });
    }

    attachListeners();
}




    async function deleteFood(id) {
        try {
            const res = await fetch(`${apiUrl}?action=delete`, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id=${encodeURIComponent(id)}`
            });
            console.log('delete status:', res.status);
            const text = await res.text();
            console.log('delete raw response:', text);
            const data = JSON.parse(text);
            if (data.status === 'success') {
                alert('غذا با موفقیت حذف شد.');
                loadFoods(currentType);  // فقط همان تب را دوباره بارگذاری کن
            } else {
                alert('خطا در حذف غذا: ' + (data.message || ''));
            }
        } catch (e) {
            console.error('delete fetch error', e);
            alert('خطا در ارتباط با سرور.');
        }
    }

    async function toggleAvailable(id, status) {
        try {
            const res = await fetch(`${apiUrl}?action=toggleAvailable`, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`
            });
            console.log('toggle status:', res.status);
            const text = await res.text();
            console.log('toggle raw response:', text);
            const data = JSON.parse(text);
            if (data.status === 'success') {
                // بدون alert فقط محتوا رو آپدیت کن (تجربه بهتر)
                loadFoods(currentType);
            } else {
                alert('خطا در بروزرسانی وضعیت: ' + (data.message || ''));
            }
        } catch (e) {
            console.error('toggle fetch error', e);
            alert('خطا در ارتباط با سرور.');
        }
    }

    // تنظیم listener های منو و بارگذاری اولیه
    menuItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            menuItems.forEach(i => i.classList.remove('active-submenu'));
            item.classList.add('active-submenu');
            const dataName = item.getAttribute('data-name');
            const mapped = typeMap[dataName];
            if (!mapped) {
                console.warn('no mapping for', dataName);
                return;
            }
            loadFoods(mapped);
        });
    });

    // بارگذاری اولیه
    loadFoods(currentType);
});
