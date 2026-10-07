/* ============================================
   نکست‌شاپ — اسکریپت اصلی فروشگاه
   ============================================ */
(function () {
    'use strict';

    var CSRF = (window.NS && window.NS.csrf) || '';

    /* ---------- توست ---------- */
    window.NS_toast = function (message, type) {
        type = type || 'success';
        var box = document.getElementById('toasts');
        if (!box) return;
        var el = document.createElement('div');
        el.className = 'toast ' + type;
        el.innerHTML = '<span class="toast-dot"></span><span>' + message + '</span>';
        box.appendChild(el);
        setTimeout(function () {
            el.classList.add('hide');
            setTimeout(function () { el.remove(); }, 350);
        }, 3800);
    };

    // نمایش پیام‌های سرور (flash)
    if (window.NS && Array.isArray(window.NS.flashes)) {
        window.NS.flashes.forEach(function (f) {
            var div = document.createElement('div');
            div.innerHTML = f.message;
            NS_toast(div.textContent, f.type);
        });
    }

    /* ---------- ریویل هنگام اسکرول ---------- */
    (function () {
        var els = document.querySelectorAll('.reveal, .section-head, .service-strip, .category-card, .product-card, .promo-tile, .promo-banner, .brand-chip-lg, .why-card, .testi-card, .deals-box');
        if (!('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('in'); });
            return;
        }
        els.forEach(function (el, i) {
            el.classList.add('reveal');
            el.style.transitionDelay = Math.min((i % 8) * 60, 420) + 'ms';
        });
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
            });
        }, { threshold: 0.08 });
        els.forEach(function (el) { io.observe(el); });
    })();

    /* ---------- بنر کوکی ---------- */
    (function () {
        var banner = document.getElementById('cookieBanner');
        if (!banner) return;
        var choice = null;
        try { choice = localStorage.getItem('ns_consent'); } catch (e) {}
        if (!choice) banner.classList.add('open');
        function set(v) {
            try { localStorage.setItem('ns_consent', v); } catch (e) {}
            banner.classList.remove('open');
            if (v === 'all') window.dispatchEvent(new Event('ns:consent'));
        }
        var a = document.getElementById('cookieAccept');
        var r = document.getElementById('cookieReject');
        if (a) a.addEventListener('click', function () { set('all'); });
        if (r) r.addEventListener('click', function () { set('essential'); });
    })();

    /* ---------- لودینگ دکمه‌های فرم ---------- */
    document.querySelectorAll('form[method="post"]').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.classList.contains('no-loading')) {
                btn.classList.add('loading');
                btn.disabled = true;
                setTimeout(function () { btn.disabled = false; btn.classList.remove('loading'); }, 8000);
            }
        });
    });

    /* ---------- CTA چسبان موبایل ---------- */
    (function () {
        var cta = document.getElementById('stickyCta');
        if (!cta) return;
        document.body.classList.add('has-sticky-cta');
        var buyBox = document.getElementById('buyForm');
        function onScroll() {
            var show = window.scrollY > 420;
            if (buyBox) {
                var r = buyBox.getBoundingClientRect();
                show = r.bottom < 0 || r.top > window.innerHeight;
            }
            cta.classList.toggle('show', show);
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    })();

    /* ---------- کپی کد سفارش ---------- */
    var copyBtn = document.getElementById('copyOrderCode');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var code = copyBtn.dataset.code || '';
            function done() { NS_toast('کد سفارش کپی شد', 'success'); }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(done, done);
            } else { done(); }
        });
    }

    /* ---------- فرم تماس (اعتبارسنجی سمت کاربر) ---------- */
    var contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var hp = contactForm.querySelector('input[name="website_url"]');
            if (hp && hp.value) return;
            var ok = true;
            contactForm.querySelectorAll('[required]').forEach(function (inp) {
                var bad = !inp.value.trim() || (inp.type === 'email' && !/^\S+@\S+\.\S+$/.test(inp.value));
                inp.classList.toggle('field-error', bad);
                if (bad) ok = false;
            });
            if (!ok) { NS_toast('لطفاً همه فیلدها را کامل و درست پر کنید', 'error'); return; }
            NS_toast('پیام شما با موفقیت ارسال شد. به‌زودی پاسخ می‌دهیم.', 'success');
            contactForm.reset();
        });
    }

    /* ---------- اعداد فارسی/انگلیسی ---------- */
    function toEn(str) {
        var fa = '۰۱۲۳۴۵۶۷۸۹', ar = '٠١٢٣٤٥٦٧٨٩';
        return String(str).replace(/[۰-۹٠-٩]/g, function (d) {
            var i = fa.indexOf(d);
            return i > -1 ? i : ar.indexOf(d);
        });
    }
    function toFa(str) {
        return String(str).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; });
    }

    /* ---------- درخواست AJAX ---------- */
    function post(url, data) {
        var body = new FormData();
        body.append('_token', CSRF);
        Object.keys(data || {}).forEach(function (k) { body.append(k, data[k]); });
        return fetch(url, {
            method: 'POST',
            body: body,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(function (r) { return r.json().then(function (j) { j._status = r.status; return j; }); });
    }

    function updateCartBadge(count) {
        var badge = document.getElementById('cartBadge');
        if (!badge) return;
        badge.textContent = toFa(count);
        badge.hidden = !count;
        badge.style.animation = 'none';
        void badge.offsetWidth;
        badge.style.animation = 'pop .4s cubic-bezier(.2,1.6,.4,1)';
    }

    /* ---------- تم تاریک/روشن ---------- */
    var themeBtn = document.getElementById('themeToggle');
    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            var dark = document.documentElement.getAttribute('data-theme') === 'dark';
            document.documentElement.setAttribute('data-theme', dark ? '' : 'dark');
            if (dark) document.documentElement.removeAttribute('data-theme');
            try { localStorage.setItem('theme', dark ? 'light' : 'dark'); } catch (e) {}
        });
    }

    /* ---------- منوی موبایل ---------- */
    var overlay = document.getElementById('drawerOverlay');
    function openDrawer(el) { if (el) { el.classList.add('open'); overlay && overlay.classList.add('open'); document.body.style.overflow = 'hidden'; } }
    function closeDrawers() {
        document.querySelectorAll('.drawer.open, .admin-sidebar.open, .filters.open').forEach(function (d) { d.classList.remove('open'); });
        overlay && overlay.classList.remove('open');
        document.body.style.overflow = '';
    }
    var menuToggle = document.getElementById('menuToggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            openDrawer(document.getElementById('mobileDrawer') || document.getElementById('adminSidebar'));
        });
    }
    var drawerClose = document.getElementById('drawerClose');
    if (drawerClose) drawerClose.addEventListener('click', closeDrawers);
    if (overlay) overlay.addEventListener('click', closeDrawers);

    /* ---------- فیلترها در موبایل ---------- */
    var filtersOpen = document.getElementById('filtersOpen');
    var filtersPanel = document.getElementById('filtersPanel');
    var filtersClose = document.getElementById('filtersClose');
    if (filtersOpen && filtersPanel) filtersOpen.addEventListener('click', function () { filtersPanel.classList.add('open'); });
    if (filtersClose && filtersPanel) filtersClose.addEventListener('click', function () { filtersPanel.classList.remove('open'); });

    /* ---------- جستجوی زنده ---------- */
    var searchInput = document.getElementById('searchInput');
    var searchResults = document.getElementById('searchResults');
    var searchTimer = null;
    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            var q = this.value.trim();
            if (q.length < 2) { searchResults.classList.remove('open'); return; }
            searchResults.innerHTML = '<div class="search-loading"><i></i></div>';
            searchResults.classList.add('open');
            searchTimer = setTimeout(function () {
                fetch('/api/search?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (!res.items || !res.items.length) {
                            searchResults.innerHTML = '<div class="search-empty">چیزی پیدا نشد برای «' + q.replace(/</g, '&lt;') + '»</div>';
                        } else {
                            searchResults.innerHTML = res.items.map(function (it) {
                                return '<a href="' + it.url + '" class="search-result-item">' +
                                    '<img src="' + it.image + '" alt="' + it.name.replace(/"/g, '') + '" loading="lazy">' +
                                    '<div><div class="sr-name">' + it.name + '</div><div class="sr-cat">' + it.category + '</div></div>' +
                                    '<span class="sr-price">' + it.price + '</span></a>';
                            }).join('');
                        }
                        searchResults.classList.add('open');
                    });
            }, 300);
        });
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-form')) searchResults.classList.remove('open');
        });
    }

    /* ---------- افزودن به سبد (کارت محصول) ---------- */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.add-to-cart');
        if (!btn) return;
        e.preventDefault();
        var pid = btn.dataset.product;
        btn.disabled = true;
        post('/cart/add', { product_id: pid, qty: 1 }).then(function (res) {
            NS_toast(res.message, res.ok ? 'success' : 'error');
            if (res.ok && res.cart) updateCartBadge(res.cart.count);
            if (!res.ok && res.redirect) setTimeout(function () { location.href = res.redirect; }, 1200);
        }).finally(function () { btn.disabled = false; });
    });

    /* ---------- فرم خرید صفحه محصول ---------- */
    var buyForm = document.getElementById('buyForm');
    if (buyForm) {
        var qtyInput = document.getElementById('qtyInput');
        buyForm.querySelectorAll('.qty-btn').forEach(function (b) {
            b.addEventListener('click', function () {
                var v = parseInt(toEn(qtyInput.value)) || 1;
                var max = parseInt(qtyInput.getAttribute('max')) || 99;
                v = Math.min(max, Math.max(1, v + parseInt(b.dataset.step)));
                qtyInput.value = toFa(v);
            });
        });
        qtyInput.addEventListener('change', function () {
            var v = parseInt(toEn(this.value)) || 1;
            var max = parseInt(this.getAttribute('max')) || 99;
            this.value = toFa(Math.min(max, Math.max(1, v)));
        });
        buyForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = buyForm.querySelector('.add-to-cart-detail');
            btn.disabled = true;
            post('/cart/add', {
                product_id: buyForm.querySelector('[name=product_id]').value,
                qty: toEn(qtyInput.value)
            }).then(function (res) {
                NS_toast(res.message, res.ok ? 'success' : 'error');
                if (res.ok && res.cart) updateCartBadge(res.cart.count);
            }).finally(function () { btn.disabled = false; });
        });
    }

    /* ---------- علاقه‌مندی ---------- */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.wishlist-btn');
        if (!btn) return;
        e.preventDefault();
        post('/wishlist/toggle', { product_id: btn.dataset.product }).then(function (res) {
            if (res._status === 401) {
                NS_toast(res.message, 'warning');
                setTimeout(function () { location.href = res.redirect || '/login'; }, 1200);
                return;
            }
            NS_toast(res.message, res.ok ? 'success' : 'error');
            if (res.ok) btn.classList.toggle('active', !!res.active);
        });
    });

    /* ---------- صفحه سبد خرید ---------- */
    function refreshSummary(cart) {
        if (!cart) return;
        updateCartBadge(cart.count);
        var el;
        if ((el = document.getElementById('sumSubtotal'))) el.textContent = cart.subtotal_f;
        if ((el = document.getElementById('sumTotal'))) el.textContent = cart.total_f;
        if ((el = document.getElementById('sumShipping'))) el.textContent = cart.shipping_f;
        var rowCoupon = document.getElementById('rowCoupon');
        if (rowCoupon) {
            rowCoupon.hidden = !cart.discount;
            var d = document.getElementById('sumDiscount');
            if (d) d.textContent = '-' + cart.discount_f;
        }
    }

    var cartDebounce = {};
    function cartUpdate(pid, qty, row) {
        clearTimeout(cartDebounce[pid]);
        cartDebounce[pid] = setTimeout(function () {
            post('/cart/update', { product_id: pid, qty: qty }).then(function (res) {
                if (!res.ok) { NS_toast(res.message, 'error'); return; }
                if (res.line && row) {
                    var sub = row.querySelector('.cart-item-subtotal');
                    if (sub) sub.textContent = res.line.subtotal_f;
                    var inp = row.querySelector('.cart-qty-input');
                    if (inp) inp.value = toFa(res.line.qty);
                }
                refreshSummary(res.cart);
                if (qty <= 0 && row) { row.remove(); checkEmptyCart(); }
            });
        }, 350);
    }

    function checkEmptyCart() {
        if (!document.querySelector('.cart-item')) location.reload();
    }

    document.querySelectorAll('.cart-item').forEach(function (row) {
        var pid = row.dataset.product;
        var input = row.querySelector('.cart-qty-input');
        var max = parseInt(input.dataset.max) || 99;
        row.querySelectorAll('.cart-qty').forEach(function (b) {
            b.addEventListener('click', function () {
                var v = parseInt(toEn(input.value)) || 1;
                v = Math.min(max, Math.max(0, v + parseInt(b.dataset.step)));
                if (v === 0) { cartRemove(pid, row); return; }
                input.value = toFa(v);
                cartUpdate(pid, v, row);
            });
        });
        input.addEventListener('change', function () {
            var v = Math.min(max, Math.max(1, parseInt(toEn(this.value)) || 1));
            this.value = toFa(v);
            cartUpdate(pid, v, row);
        });
        var rm = row.querySelector('.cart-remove');
        if (rm) rm.addEventListener('click', function () { cartRemove(pid, row); });
    });

    function cartRemove(pid, row) {
        post('/cart/remove', { product_id: pid }).then(function (res) {
            NS_toast(res.message, 'success');
            refreshSummary(res.cart);
            if (row) {
                row.style.transition = 'all .3s';
                row.style.opacity = '0';
                row.style.transform = 'translateX(30px)';
                setTimeout(function () { row.remove(); checkEmptyCart(); }, 300);
            }
        });
    }

    /* ---------- کد تخفیف ---------- */
    var applyBtn = document.getElementById('applyCoupon');
    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            var code = document.getElementById('couponInput').value.trim();
            if (!code) return;
            post('/cart/coupon', { code: code }).then(function (res) {
                NS_toast(res.message, res.ok ? 'success' : 'error');
                if (res.ok) setTimeout(function () { location.reload(); }, 700);
            });
        });
    }
    var removeCouponBtn = document.getElementById('removeCoupon');
    if (removeCouponBtn) {
        removeCouponBtn.addEventListener('click', function () {
            post('/cart/coupon/remove', {}).then(function (res) {
                NS_toast(res.message, 'success');
                setTimeout(function () { location.reload(); }, 500);
            });
        });
    }

    /* ---------- تب‌ها ---------- */
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var wrap = btn.closest('.tabs');
            wrap.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
            wrap.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
            btn.classList.add('active');
            var panel = document.getElementById('tab-' + btn.dataset.tab);
            if (panel) panel.classList.add('active');
        });
    });
    // باز کردن تب دیدگاه‌ها با لینک #reviews
    if (location.hash === '#reviews') {
        var revBtn = document.querySelector('.tab-btn[data-tab="reviews"]');
        if (revBtn) revBtn.click();
    }

    /* ---------- نمایش/مخفی رمز عبور ---------- */
    document.querySelectorAll('.toggle-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.target);
            if (input) input.type = input.type === 'password' ? 'text' : 'password';
        });
    });

    /* ---------- پرکردن خودکار حساب دمو ---------- */
    document.querySelectorAll('.demo-fill').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var form = btn.closest('form') || document.querySelector('.auth-form');
            var email = document.querySelector('input[name="email"]');
            var pass = document.querySelector('input[name="password"]');
            if (email) email.value = btn.dataset.email;
            if (pass) pass.value = btn.dataset.pass;
            NS_toast('اطلاعات دمو پر شد — روی ورود بزنید', 'info');
        });
    });

    /* ---------- خبرنامه ---------- */
    var nlForm = document.getElementById('newsletterForm');
    if (nlForm) {
        nlForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = nlForm.querySelector('button[type="submit"]');
            var hp = nlForm.querySelector('input[name="website_url"]');
            if (btn) { btn.classList.add('loading'); btn.disabled = true; }
            post('/newsletter', { email: nlForm.email.value, website_url: hp ? hp.value : '' }).then(function (res) {
                NS_toast(res.message, res.ok ? 'success' : 'error');
                if (res.ok) nlForm.reset();
            }).finally(function () {
                if (btn) { btn.classList.remove('loading'); btn.disabled = false; }
            });
        });
    }

    /* ---------- تغییر وضعیت محصول (AJAX در ادمین) ---------- */
    document.querySelectorAll('.ajax-toggle').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(function (r) { return r.json(); }).then(function (res) {
                NS_toast(res.message || 'انجام شد', res.ok ? 'success' : 'error');
            });
        });
    });

    /* ---------- جداکننده هزارگان برای ورودی‌های قیمت ---------- */
    document.querySelectorAll('input[inputmode="numeric"]').forEach(function (inp) {
        if (inp.closest('.qty-stepper')) return;
        inp.addEventListener('input', function () {
            var raw = toEn(this.value).replace(/[^\d]/g, '');
            if (raw === '') { this.value = ''; return; }
            this.value = toFa(Number(raw).toLocaleString('en-US').replace(/,/g, '،'));
        });
    });

    /* ---------- گالری تصاویر محصول ---------- */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.gallery-thumb-btn');
        if (!btn) return;
        var newSrc = btn.getAttribute('data-src');
        var mainImg = document.getElementById('mainImage');
        if (mainImg && newSrc) {
            mainImg.style.opacity = '0.25';
            setTimeout(function () {
                mainImg.src = newSrc;
                mainImg.style.opacity = '1';
            }, 120);
        }
        document.querySelectorAll('.gallery-thumb-btn').forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
    });

    /* ---------- اسلایدر هیرو پویا ---------- */
    (function () {
        var sliderWrap = document.querySelector('.hero-slider-wrap');
        if (!sliderWrap) return;
        var slides = sliderWrap.querySelectorAll('.hero-slide');
        var dots = sliderWrap.querySelectorAll('.hero-slider-dot');
        var prevBtn = sliderWrap.querySelector('.hero-slider-nav.prev');
        var nextBtn = sliderWrap.querySelector('.hero-slider-nav.next');
        if (slides.length <= 1) return;

        var currentIndex = 0;
        var timer = null;

        function showSlide(index) {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;
            currentIndex = index;

            slides.forEach(function (s, i) {
                if (i === currentIndex) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });

            dots.forEach(function (d, i) {
                if (i === currentIndex) {
                    d.classList.add('active');
                } else {
                    d.classList.remove('active');
                }
            });
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }

        function startAuto() {
            stopAuto();
            timer = setInterval(nextSlide, 5500);
        }

        function stopAuto() {
            if (timer) clearInterval(timer);
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                nextSlide();
                startAuto();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                prevSlide();
                startAuto();
            });
        }

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () {
                showSlide(i);
                startAuto();
            });
        });

        sliderWrap.addEventListener('mouseenter', stopAuto);
        sliderWrap.addEventListener('mouseleave', startAuto);

        startAuto();
    })();
})();

