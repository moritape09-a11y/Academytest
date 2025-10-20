# 🚀 پلاگین AJAX حرفه‌ای - راهنمای کامل

## ✨ ویژگی‌های جدید

### 🔥 1. جستجوی زنده (Live Search)
```
کاربر تایپ می‌کند: "طراحی س..."
  ↓ (500ms delay)
نتایج خودکار نمایش داده می‌شود!
بدون نیاز به کلیک دکمه! ✨
```

### ⚡ 2. AJAX بدون رفرش صفحه
```
قبل: کلیک → رفرش صفحه → صبر → نتایج
بعد: کلیک → نتایج فوری! (بدون رفرش) 🎉
```

### 🎬 3. Loading Animations
```
در حال جستجو...
├── Spinner چرخان
├── Skeleton screens (کارت‌های خاکستری)
└── Smooth fade-in animations
```

### 📊 4. URL State Management
```
URL تغییر می‌کند همراه با جستجو:
example.com/search?q=زبان&city=تهران

مزایا:
✅ لینک قابل اشتراک‌گذاری
✅ دکمه Back/Forward مرورگر کار می‌کند
✅ بوک‌مارک کردن جستجو
```

### 🎨 5. UX پیشرفته
```
✅ Hover effects زیبا
✅ Smooth scrolling
✅ Error handling حرفه‌ای
✅ Empty states خلاقانه
✅ Responsive animations
```

---

## 🏗️ ساختار فایل‌ها

### 1️⃣ Backend (PHP)
```
includes/ajax-handler.php
├── REST API Endpoints
│   ├── /edu/v1/search (جستجو)
│   └── /edu/v1/filters (فیلترها)
├── Security (nonce)
└── JSON Response
```

### 2️⃣ Frontend (JavaScript)
```
assets/ajax-search.js
├── AJAX Request Handler
├── Debounce Function (500ms)
├── Loading States
├── Results Rendering
├── URL Management
├── Error Handling
└── Browser History API
```

### 3️⃣ Styling (CSS)
```
assets/style.css
├── Loading Spinner
├── Skeleton Screens
├── Animations (fade-in, pulse, swing)
├── Enhanced Cards
├── Responsive Design
└── Accessibility
```

---

## 🔧 چطور کار می‌کند؟

### مرحله 1: کاربر تایپ می‌کند
```javascript
User Input: "طراحی سایت"
   ↓
Debounce (500ms wait)
   ↓
Check: آیا مقدار تغییر کرده؟
   ↓ YES
AJAX Request
```

### مرحله 2: ارسال Request
```javascript
jQuery.ajax({
    url: /wp-json/edu/v1/search
    data: {
        query: "طراحی سایت",
        type: "all",
        city: "",
        subject: "",
        rating: ""
    }
})
```

### مرحله 3: دریافت Response
```json
{
    "success": true,
    "total": 15,
    "results": [
        {
            "type": "academy",
            "label": "آموزشگاه‌ها",
            "count": 8,
            "posts": [...]
        },
        {
            "type": "teacher",
            "label": "معلمین",
            "count": 7,
            "posts": [...]
        }
    ]
}
```

### مرحله 4: نمایش نتایج
```javascript
1. پاک کردن نتایج قبلی
2. ساخت HTML جدید
3. Fade-in animation
4. Smooth scroll به نتایج
5. Update URL
```

---

## 📱 ویژگی‌های تکنیکال

### 🔐 Security
```php
✅ REST API Nonce
✅ Sanitize Input (sanitize_text_field)
✅ Escape Output (esc_html, esc_attr)
✅ Permission Callbacks
```

### ⚡ Performance
```javascript
✅ Debounce (کاهش تعداد request ها)
✅ Abort قبلی requests
✅ Lazy loading
✅ CSS Animations (GPU accelerated)
```

### ♿ Accessibility
```css
✅ Focus states
✅ Keyboard navigation
✅ ARIA labels
✅ High contrast
```

### 📱 Responsive
```css
✅ Mobile-first design
✅ Touch-friendly buttons
✅ Flexible grids
✅ Optimized for all screens
```

---

## 🎯 User Flow

### سناریو 1: جستجوی ساده
```
1. کاربر صفحه جستجو را باز می‌کند
2. تایپ می‌کند: "زبان"
3. (بعد از 500ms) نتایج خودکار نمایش داده می‌شوند
4. کارت‌ها با fade-in animation ظاهر می‌شوند
5. URL تغییر می‌کند: ?q=زبان
```

### سناریو 2: جستجو + فیلتر
```
1. کاربر تایپ می‌کند: "زبان"
2. شهر را انتخاب می‌کند: "تهران"
3. نتایج فوری آپدیت می‌شوند
4. URL: ?q=زبان&city=تهران
5. لینک قابل اشتراک است!
```

### سناریو 3: تغییر فیلتر
```
1. کاربر رشته را تغییر می‌دهد
2. Loading spinner نمایش داده می‌شود
3. Skeleton cards ظاهر می‌شوند
4. نتایج جدید با animation نمایش داده می‌شوند
```

### سناریو 4: خطا
```
1. اینترنت قطع می‌شود
2. پیام خطای زیبا: "خطا در ارتباط با سرور"
3. آیکون ⚠️
4. راهنمایی برای کاربر
```

---

## 🧪 تست سناریوها

### تست 1: Live Search
```
1. صفحه جستجو را باز کنید
2. شروع به تایپ کنید: "طراحی"
3. صبر کنید 500ms
4. نتایج باید خودکار نمایش داده شوند ✅
```

### تست 2: فیلتر
```
1. از dropdown رشته را انتخاب کنید
2. نتایج باید فوری تغییر کنند ✅
3. Loading spinner باید نمایش داده شود ✅
```

### تست 3: URL State
```
1. جستجو کنید: "زبان"
2. URL باید تغییر کند: ?q=زبان ✅
3. دکمه Back مرورگر را بزنید
4. باید به حالت قبل برگردد ✅
```

### تست 4: موبایل
```
1. صفحه را در موبایل باز کنید
2. تمام دکمه‌ها باید لمس‌پذیر باشند ✅
3. Animations باید smooth باشند ✅
```

### تست 5: Performance
```
1. تایپ سریع کنید
2. فقط یک request باید ارسال شود (debounce) ✅
3. Page freeze نباید رخ دهد ✅
```

---

## 🎨 Animations در دسترس

### 1. Fade In
```css
.edu-fade-in {
    animation: edu-fade-in 0.5s ease-out;
}
```

### 2. Pulse
```css
.edu-results-count {
    animation: edu-pulse 2s infinite;
}
```

### 3. Swing
```css
.edu-no-results-icon {
    animation: edu-swing 2s infinite;
}
```

### 4. Skeleton Loading
```css
.edu-skeleton-image {
    animation: edu-skeleton-loading 1.5s infinite;
}
```

### 5. Spin
```css
.edu-loading-spinner {
    animation: edu-spin 0.8s linear infinite;
}
```

---

## 🔥 Advanced Features

### Debounce Function
```javascript
// جلوگیری از ارسال request های زیاد
let timeout;
input.on('input', function() {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        performSearch();
    }, 500);
});
```

### Abort Previous Requests
```javascript
// لغو request قبلی
if (currentRequest) {
    currentRequest.abort();
}
currentRequest = $.ajax({...});
```

### History API
```javascript
// مدیریت URL بدون reload
history.pushState({}, '', newUrl);

// گوش دادن به back/forward
window.addEventListener('popstate', () => {
    loadFromURL();
});
```

### Smart Scrolling
```javascript
// اسکرول smooth به نتایج
$('html, body').animate({
    scrollTop: $results.offset().top - 100
}, 500);
```

---

## 📊 API Endpoints

### GET /wp-json/edu/v1/search
```
پارامترها:
- query: عبارت جستجو
- type: نوع (all/academy/school/teacher)
- city: شهر (slug)
- subject: رشته (slug)
- rating: امتیاز
- page: صفحه

Response:
{
    "success": true,
    "total": 15,
    "results": [...],
    "query": "...",
    "filters": {...}
}
```

### GET /wp-json/edu/v1/filters
```
پارامترها: -

Response:
{
    "success": true,
    "cities": [...],
    "subjects": [...]
}
```

---

## 🚀 دستورات نصب

### قدم 1: آپلود فایل‌ها
```
✅ includes/ajax-handler.php (جدید)
✅ assets/ajax-search.js (جدید)
✅ assets/style.css (به‌روز شده)
✅ educational-directory.php (به‌روز شده)
```

### قدم 2: کش را پاک کنید
```
1. Ctrl + Shift + R (مرورگر)
2. پلاگین Cache را پاک کنید
3. CDN Cache را clear کنید
```

### قدم 3: تست کنید
```
1. صفحه جستجو را باز کنید
2. شروع به تایپ کنید
3. نتایج باید خودکار بیایند!
```

---

## 💡 نکات مهم

### ✅ DO (انجام دهید)
```
✅ همیشه کش را پاک کنید
✅ در Console برای خطاها چک کنید
✅ در موبایل تست کنید
✅ در مرورگرهای مختلف تست کنید
```

### ❌ DON'T (انجام ندهید)
```
❌ فایل‌های قدیمی را نگه ندارید
❌ کش را فراموش نکنید
❌ بدون تست deploy نکنید
❌ jQuery را remove نکنید (dependency است)
```

---

## 🐛 Troubleshooting

### مشکل 1: نتایج نمی‌آیند
```
1. Console را باز کنید (F12)
2. خطا پیدا کنید
3. چک کنید: REST API کار می‌کند؟
   → بروید: /wp-json/edu/v1/search
4. چک کنید: JavaScript لود شده؟
5. کش را پاک کنید
```

### مشکل 2: Loading بی‌نهایت
```
1. Console → Network tab
2. Request را پیدا کنید
3. Response را چک کنید
4. 404 Error? → Permalink را flush کنید
5. 500 Error? → PHP Error log را چک کنید
```

### مشکل 3: Animation ها کار نمی‌کنند
```
1. CSS لود شده؟
2. Ctrl + F5 (hard refresh)
3. Browser compatibility?
4. CDN cache?
```

---

## 📈 بهبودهای آینده

### Phase 2 (اختیاری):
```
🔜 Infinite Scroll
🔜 Filter count badges
🔜 Auto-complete suggestions
🔜 Recent searches
🔜 Favorites system
🔜 Compare feature
🔜 Map view
🔜 Voice search
```

---

## 🎉 خلاصه

### چه چیزهایی اضافه شد:
```
✅ REST API برای AJAX
✅ JavaScript با live search
✅ Debounce (500ms)
✅ Loading states (spinner + skeleton)
✅ Smooth animations
✅ URL state management
✅ Error handling
✅ Responsive design
✅ Browser history support
✅ Security (nonce)
```

### قبل و بعد:
```
❌ قبل:
   - رفرش صفحه
   - بدون animation
   - کند
   - تجربه کاربری ضعیف

✅ بعد:
   - بدون رفرش (AJAX)
   - Animations زیبا
   - فوری
   - تجربه کاربری حرفه‌ای!
```

---

**یک پلاگین AJAX حرفه‌ای آماده است! 🚀**
