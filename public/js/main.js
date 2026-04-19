/* ============================================================
   MAIN.JS — Gampong Muenye Pirak
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
  initPreloader();
  initNavbar();
  initScrollProgress();
  initReveal();
  initCounters();
  initHeroCarousel();
  initHeroParallax();
  initTiltCards();
  initMagneticButtons();
  initGalleryLightbox();
  initBackToTop();
  initFloatWa();
  initShareCopy();
  initLazyLoad();
  initSmoothAnchor();
  initArticleProgress();
  initGalleryFilter();
  initPageTransition();
});

/* ── Preloader ───────────────────────────── */
function initPreloader() {
  const el = document.getElementById('page-preloader');
  if (!el) return;
  const hide = () => el.classList.add('is-hidden');
  if (document.readyState === 'complete') {
    setTimeout(hide, 350);
  } else {
    window.addEventListener('load', () => setTimeout(hide, 350));
  }
}

/* ── Navbar ──────────────────────────────── */
function initNavbar() {
  const nav       = document.getElementById('siteNav');
  const hamburger = document.getElementById('navHamburger');
  const menuWrap  = document.getElementById('siteNavMenu');
  const overlay   = document.getElementById('navOverlay');
  const closeBtn  = document.getElementById('navDrawerClose');
  const dropdowns = document.querySelectorAll('.has-dropdown');
  if (!nav) return;

  // Scroll behaviour
  const onScroll = () => nav.classList.toggle('is-scrolled', window.scrollY > 60);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Mobile drawer
  const openDrawer = () => {
    menuWrap?.classList.add('is-open');
    overlay?.classList.add('is-open');
    overlay?.removeAttribute('hidden');
    hamburger?.setAttribute('aria-expanded', 'true');
    document.body.classList.add('nav-open');
  };
  const closeDrawer = () => {
    menuWrap?.classList.remove('is-open');
    overlay?.classList.remove('is-open');
    hamburger?.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('nav-open');
    setTimeout(() => overlay?.setAttribute('hidden', ''), 450);
  };

  hamburger?.addEventListener('click', () => {
    if (menuWrap?.classList.contains('is-open')) {
      closeDrawer();
      return;
    }
    openDrawer();
  });
  closeBtn?.addEventListener('click', closeDrawer);
  overlay?.addEventListener('click', closeDrawer);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

  // Dropdowns
  dropdowns.forEach(dd => {
    const btn = dd.querySelector('.nav-link-dropdown');
    btn?.addEventListener('click', e => {
      e.stopPropagation();
      const isOpen = dd.classList.contains('open');
      dropdowns.forEach(d => d.classList.remove('open'));
      if (!isOpen) dd.classList.add('open');
      btn.setAttribute('aria-expanded', String(!isOpen));
    });
  });
  document.addEventListener('click', () => dropdowns.forEach(d => d.classList.remove('open')));
}

/* ── Scroll Progress ─────────────────────── */
function initScrollProgress() {
  window.addEventListener('scroll', () => {
    const pct = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    document.documentElement.style.setProperty('--progress', pct + '%');
  }, { passive: true });
}

/* ── Scroll Reveal ───────────────────────── */
function initReveal() {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const selectors = '[data-reveal], [data-stagger], [data-stagger-diag], [data-stagger-flip]';
  const allEls = document.querySelectorAll(selectors);

  if (prefersReduced) {
    allEls.forEach(el => el.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });

  allEls.forEach(el => {
    // Langsung tampilkan elemen yang sudah ada di viewport saat halaman load
    const rect = el.getBoundingClientRect();
    if (rect.top < window.innerHeight && rect.bottom > 0) {
      el.classList.add('is-visible');
    } else {
      observer.observe(el);
    }
  });
}

/* ── Counter Animations ──────────────────── */
function initCounters() {
  const counters = document.querySelectorAll('[data-counter]');
  if (!counters.length) return;
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const animateCounter = (el) => {
    const target = parseInt(el.dataset.counter.replace(/\D/g, '')) || 0;
    if (target === 0 || prefersReduced) return;
    const duration = 1800;
    const start = performance.now();
    const tick = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased    = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(target * eased).toLocaleString('id-ID');
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };

  const obs = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) { animateCounter(entry.target); obs.unobserve(entry.target); }
    });
  }, { threshold: 0.5 });
  counters.forEach(el => obs.observe(el));
}

/* ── Hero Carousel ───────────────────────── */
function initHeroCarousel() {
  const slides = document.querySelectorAll('.hero-slide');
  const dots   = document.querySelectorAll('.hero-nav-dot');
  if (slides.length < 2) return;

  let current = 0;
  let timer = null;

  const goTo = (idx) => {
    slides[current].classList.remove('active');
    dots[current]?.classList.remove('active');
    dots[current]?.setAttribute('aria-selected', 'false');
    current = (idx + slides.length) % slides.length;
    slides[current].classList.add('active');
    dots[current]?.classList.add('active');
    dots[current]?.setAttribute('aria-selected', 'true');
  };

  const startAuto = () => { timer = setInterval(() => goTo(current + 1), 5500); };
  const stopAuto  = () => clearInterval(timer);

  dots.forEach((dot, i) => dot.addEventListener('click', () => { stopAuto(); goTo(i); startAuto(); }));
  startAuto();

  // Touch swipe
  let touchStartX = 0;
  const heroSection = document.querySelector('.hero-section');
  heroSection?.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
  heroSection?.addEventListener('touchend', e => {
    const dx = e.changedTouches[0].clientX - touchStartX;
    if (Math.abs(dx) > 50) { stopAuto(); goTo(dx < 0 ? current + 1 : current - 1); startAuto(); }
  }, { passive: true });
}

/* ── Hero Parallax ───────────────────────── */
function initHeroParallax() {
  const layers = document.querySelectorAll('[data-parallax-speed]');
  if (!layers.length) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  let ticking = false;
  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(() => {
        const sy = window.scrollY;
        layers.forEach(l => { l.style.transform = `translateY(${sy * parseFloat(l.dataset.parallaxSpeed || '0.1')}px)`; });
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });
}

/* ── 3D Tilt Cards ───────────────────────── */
function initTiltCards() {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if ('ontouchstart' in window) return;
  document.querySelectorAll('.tilt-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const rect = card.getBoundingClientRect();
      const dx = (e.clientX - rect.left - rect.width  / 2) / (rect.width  / 2);
      const dy = (e.clientY - rect.top  - rect.height / 2) / (rect.height / 2);
      card.style.transform = `perspective(600px) rotateY(${dx * 7}deg) rotateX(${-dy * 7}deg) scale(1.02)`;
    });
    card.addEventListener('mouseleave', () => { card.style.transform = ''; });
  });
}

/* ── Magnetic Buttons ────────────────────── */
function initMagneticButtons() {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if ('ontouchstart' in window) return;
  document.querySelectorAll('.btn-magnetic').forEach(btn => {
    btn.addEventListener('mousemove', e => {
      const rect = btn.getBoundingClientRect();
      const dx = (e.clientX - rect.left - rect.width  / 2) * 0.3;
      const dy = (e.clientY - rect.top  - rect.height / 2) * 0.3;
      btn.style.transform = `translate(${dx}px, ${dy}px)`;
    });
    btn.addEventListener('mouseleave', () => { btn.style.transform = ''; });
  });
}

/* ── Gallery Lightbox ────────────────────── */
function initGalleryLightbox() {
  const dialog   = document.getElementById('imageLightbox');
  const img      = document.getElementById('imageLightboxImg');
  const caption  = document.getElementById('imageLightboxCaption');
  const closeBtn = document.getElementById('imageLightboxClose');
  if (!dialog || !img) return;

  const openImg = (src, alt) => {
    img.src = src;
    img.alt = alt || '';
    if (caption) {
      const text = (alt || '').trim();
      caption.textContent = text;
      caption.hidden = text === '';
    }
    if (!dialog.open) dialog.showModal();
  };
  const closeIt = () => {
    if (dialog.open) dialog.close();
    img.src = '';
    img.alt = '';
    if (caption) {
      caption.textContent = '';
      caption.hidden = true;
    }
  };

  document.addEventListener('click', e => {
    const item = e.target.closest('[data-lightbox-src]');
    if (item) { e.preventDefault(); openImg(item.dataset.lightboxSrc, item.dataset.lightboxAlt || ''); }
  });
  document.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      const f = document.activeElement;
      if (f?.dataset?.lightboxSrc) openImg(f.dataset.lightboxSrc, f.dataset.lightboxAlt || '');
    }
  });
  closeBtn?.addEventListener('click', closeIt);
  dialog.addEventListener('click', e => { if (e.target === dialog) closeIt(); });
  dialog.addEventListener('cancel', e => { e.preventDefault(); closeIt(); });
}

/* ── Back To Top ─────────────────────────── */
function initBackToTop() {
  const btn = document.getElementById('back-to-top');
  if (!btn) return;
  window.addEventListener('scroll', () => btn.classList.toggle('visible', window.scrollY > 400), { passive: true });
  btn.addEventListener('click', e => { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
}

/* ── WhatsApp Float ──────────────────────── */
function initFloatWa() {
  const wa = document.getElementById('floatWa');
  if (!wa) return;
  window.addEventListener('scroll', () => wa.classList.toggle('visible', window.scrollY > 300), { passive: true });
}

/* ── Share Copy ──────────────────────────── */
function initShareCopy() {
  document.querySelectorAll('[data-copy-url]').forEach(btn => {
    btn.addEventListener('click', () => {
      navigator.clipboard?.writeText(window.location.href).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check2"></i> Tersalin!';
        setTimeout(() => { btn.innerHTML = orig; }, 2000);
      });
    });
  });
}

/* ── Lazy Load Images ────────────────────── */
function initLazyLoad() {
  if ('loading' in HTMLImageElement.prototype) return;
  const imgs = document.querySelectorAll('img[loading="lazy"]');
  const obs  = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        if (img.dataset.src) img.src = img.dataset.src;
        obs.unobserve(img);
      }
    });
  });
  imgs.forEach(img => obs.observe(img));
}

/* ── Smooth Anchor ───────────────────────── */
function initSmoothAnchor() {
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
  });
}

/* ── Article Reading Progress ────────────── */
function initArticleProgress() {
  const bar     = document.querySelector('.article-progress-bar');
  const article = document.querySelector('.article-content');
  if (!bar || !article) return;
  window.addEventListener('scroll', () => {
    const rect = article.getBoundingClientRect();
    const pct  = Math.min(100, Math.max(0, (-rect.top / article.offsetHeight) * 100));
    bar.style.width = pct + '%';
  }, { passive: true });
}

/* ── Gallery Category Filter ─────────────── */
function initGalleryFilter() {
  const filterBtns = document.querySelectorAll('.filter-btn');
  if (!filterBtns.length) return;

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filter;
      const items  = document.querySelectorAll('.gallery-full-item');
      items.forEach((item, i) => {
        const show = filter === 'Semua' || item.dataset.category === filter;
        item.style.transition = `opacity 0.3s ${i * 0.03}s, transform 0.35s ${i * 0.03}s`;
        if (show) {
          item.style.display = '';
          requestAnimationFrame(() => { item.style.opacity = '1'; item.style.transform = ''; });
        } else {
          item.style.opacity = '0';
          item.style.transform = 'scale(0.92)';
          setTimeout(() => { if (item.style.opacity === '0') item.style.display = 'none'; }, 320);
        }
      });
    });
  });
}

/* ── Page Transition ─────────────────────── */
function initPageTransition() {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  document.querySelectorAll('a[href]').forEach(a => {
    const href = a.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('http') || href.startsWith('mailto') || href.startsWith('tel')) return;
    if (a.target === '_blank') return;
    a.addEventListener('click', e => {
      const url = a.href;
      if (url === window.location.href) return;
      e.preventDefault();
      document.body.style.transition = 'opacity 0.22s ease-out';
      document.body.style.opacity = '0';
      setTimeout(() => { window.location.href = url; }, 200);
    });
  });
  window.addEventListener('pageshow', () => {
    document.body.style.opacity = '1';
  });
}
