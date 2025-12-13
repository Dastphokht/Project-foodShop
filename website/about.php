<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رستوران - درباره ما</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Menu -->
    <?php include('Menu.php');  ?>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-left animate-slide-up">
                    <h1>غذای خوشمزه با <span class="orange">عشق</span></h1>
                    <p>ما با اعتقاد به کیفیت و تازگی مواد اولیه، هر روز برای شما بهترین غذاها را تهیه می‌کنیم. تجربه طعم‌های اصیل و سنتی در هر لقمه.</p>
                    <div class="buttons">
                        <a href="food.php" class="btn btn-primary">سفارش دهید</a>
                    </div>
                </div>
                <div class="hero-right">
                    <div class="emoji-large animate-float-emoji">🍕</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="story">
        <div class="container">
            <div class="story-header animate-slide-up">
                <h2>داستان ما</h2>
                <div class="divider"></div>
            </div>

            <div class="story-grid">
                <div class="story-card animate-slide-up">
                    <div class="story-icon">👨‍🍳</div>
                    <h3>سرآشپز متخصص</h3>
                    <p>تیم سرآشپزهای ما با بیش از 20 سال تجربه، هر غذا را با دقت و علاقه تهیه می‌کنند.</p>
                </div>
                <div class="story-card animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="story-icon">🥗</div>
                    <h3>مواد تازه</h3>
                    <p>تمام مواد اولیه هر روز از بازارهای محلی تهیه می‌شوند تا تازگی تضمین شود.</p>
                </div>
                <div class="story-card animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="story-icon">❤️</div>
                    <h3>خدمات عالی</h3>
                    <p>رضایت مشتری برای ما اولویت اول است و ما همیشه برای بهتری تلاش می‌کنیم.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values">
        <div class="container">
            <h2 class="values-title animate-slide-up">ارزش‌های ما</h2>

            <div class="values-grid">
                <div class="value-item animate-slide-up">
                    <div class="value-icon">✓</div>
                    <h3>کیفیت بی‌نظیر</h3>
                    <p>هر غذایی که از آشپزخانه ما بیرون می‌رود، استاندارد کیفیت بالایی را دارد.</p>
                </div>

                <div class="value-item animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="value-icon green">✓</div>
                    <h3>سرعت تحویل</h3>
                    <p>سفارش‌های شما در سریع‌ترین زمان ممکن آماده و تحویل داده می‌شود.</p>
                </div>

                <div class="value-item animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="value-icon yellow">✓</div>
                    <h3>قیمت منصفانه</h3>
                    <p>ما معتقد هستیم غذای خوب نباید گران باشد. قیمت‌های ما رقابتی و منصفانه است.</p>
                </div>

                <div class="value-item animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="value-icon orange">✓</div>
                    <h3>مشتری‌محور</h3>
                    <p>نظرات و پیشنهادات شما برای ما بسیار مهم است و ما همیشه گوش می‌دهیم.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team">
        <div class="container">
            <h2 class="team-title animate-slide-up">تیم ما</h2>

            <div class="team-grid">
                <div class="team-member animate-slide-up">
                    <div class="member-avatar">👩‍💻</div>
                    <h3>راحیل احمدی</h3>
                    <p class="member-role">مدیر پروژه و فرانت اند دولوپر</p>
                </div>

                <div class="team-member animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="member-avatar">👩‍💻</div>
                    <h3>فاطمه دادوند</h3>
                    <p class="member-role">تحلیل گر و فرانت دولوپر</p>
                </div>

                <div class="team-member animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="member-avatar">👩‍💼</div>
                    <h3>زهرا حبیب الهی</h3>
                    <p class="member-role"> مدیر عملیات و بک اند دولوپر</p>
                </div>

                <div class="team-member animate-slide-up" style="animation-delay: 0.3s;">
                    <div class="member-avatar">👩‍💼</div>
                    <h3> فائره احسان فر</h3>
                    <p class="member-role">دیتابیس کار و بک اند دولوپر</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
