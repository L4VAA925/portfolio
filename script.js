/* ========================================
   Edin Jašarspahić — Portfolio Script
   Theme toggle, scroll animations, nav,
   skill bars, counters, mobile menu, form
   ======================================== */

document.addEventListener('DOMContentLoaded', () => {

  /* --- Theme Toggle --- */
  const themeToggle = document.getElementById('themeToggle');
  const themeIcon = themeToggle.querySelector('i');
  const html = document.documentElement;

  // Load saved theme
  const savedTheme = localStorage.getItem('theme') || 'dark';
  html.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);

  themeToggle.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
  });

  function updateThemeIcon(theme) {
    themeIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
  }

  /* --- Mobile Menu --- */
  const menuToggle = document.getElementById('menuToggle');
  const navLinks = document.getElementById('navLinks');

  menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('open');
    navLinks.classList.toggle('open');
  });

  // Close menu on link click
  navLinks.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      menuToggle.classList.remove('open');
      navLinks.classList.remove('open');
    });
  });

  /* --- Nav Scroll State --- */
  const nav = document.getElementById('nav');
  const scrollProgress = document.getElementById('scrollProgress');

  window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const progress = (scrollY / docHeight) * 100;

    nav.classList.toggle('scrolled', scrollY > 50);
    scrollProgress.style.width = progress + '%';
  });

  /* --- Active Nav Link on Scroll --- */
  const sections = document.querySelectorAll('section[id]');
  const navLinkEls = document.querySelectorAll('.nav-link');

  const navObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const id = entry.target.id;
        navLinkEls.forEach(link => {
          link.classList.toggle('active', link.getAttribute('href') === '#' + id);
        });
      }
    });
  }, { rootMargin: '-40% 0px -55% 0px' });

  sections.forEach(s => navObserver.observe(s));

  /* --- Reveal on Scroll --- */
  const revealEls = document.querySelectorAll('.reveal');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

  revealEls.forEach(el => revealObserver.observe(el));

  /* --- Skill Bars --- */
  const skills = document.querySelectorAll('.skill');
  const skillObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const skill = entry.target;
        const percent = skill.dataset.percent;
        const fill = skill.querySelector('.skill-fill');
        const pctText = skill.querySelector('.skill-pct');

        fill.style.width = percent + '%';

        // Animate percentage text
        let current = 0;
        const target = parseInt(percent);
        const duration = 1200;
        const startTime = performance.now();

        function animatePct(now) {
          const elapsed = now - startTime;
          const progress = Math.min(elapsed / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          current = Math.round(eased * target);
          pctText.textContent = current + '%';
          if (progress < 1) requestAnimationFrame(animatePct);
        }
        requestAnimationFrame(animatePct);

        skillObserver.unobserve(skill);
      }
    });
  }, { threshold: 0.3 });

  skills.forEach(skill => skillObserver.observe(skill));

  /* --- Counter Animation --- */
  const counters = document.querySelectorAll('.stat-num');
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseInt(el.dataset.count);
        const duration = 1500;
        const startTime = performance.now();

        function animate(now) {
          const elapsed = now - startTime;
          const progress = Math.min(elapsed / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          el.textContent = Math.round(eased * target) + '+';
          if (progress < 1) requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(c => counterObserver.observe(c));

  /* --- Contact Form --- */
  const form = document.getElementById('contactForm');
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const btn = form.querySelector('button');
      const originalText = btn.innerHTML;

      btn.innerHTML = '<i class="fa-solid fa-check"></i> Message Sent!';
      btn.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';

      // Open email client
      const name = form.querySelector('input[type="text"]').value;
      const email = form.querySelector('input[type="email"]').value;
      const subject = form.querySelector('input[type="text"]:nth-of-type(2)').value;
      const message = form.querySelector('textarea').value;

      setTimeout(() => {
        window.location.href = `mailto:jasarspahic.edin@hotmail.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(`Name: ${name}\nEmail: ${email}\n\n${message}`)}`;
      }, 500);

      setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = '';
        form.reset();
      }, 3000);
    });
  }

  /* --- Smooth Scroll for anchor links --- */
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      const target = document.querySelector(link.getAttribute('href'));
      if (target) {
        e.preventDefault();
        const offset = 70;
        const top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    });
  });

});
