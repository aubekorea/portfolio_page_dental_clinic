const body = document.body;
const siteHeader = document.getElementById("siteHeader");
const loginBox = document.getElementById("loginBox");
const loginTitle = document.getElementById("loginTitle");
const mobileMenuBtn = document.getElementById("mobileMenuBtn");
const mobileMenuClose = document.getElementById("mobileMenuClose");
const globalNav = document.getElementById("globalNav");
const quick = document.getElementById("quick");
const mobileQuery = window.matchMedia("(max-width: 960px)");
const authTitles = {
  login: "로그인",
  join: "회원가입",
  find: "아이디 · 비밀번호 찾기",
};

function setHeaderState() {
  if (!siteHeader) return;
  siteHeader.classList.toggle("is_scrolled", window.scrollY > 20);
}

function setAuthPanel(panelName = "login") {
  if (!loginBox) return;
  const panel = authTitles[panelName] ? panelName : "login";

  loginBox.querySelectorAll("[data-auth-panel]").forEach((item) => {
    item.classList.toggle("is_active", item.dataset.authPanel === panel);
  });
  if (loginTitle) loginTitle.textContent = authTitles[panel];
}

function openLoginModal(event) {
  if (!loginBox) return;
  const targetPanel = event?.currentTarget?.dataset?.authTarget || "login";
  setAuthPanel(targetPanel);
  loginBox.classList.add("active");
  loginBox.setAttribute("aria-hidden", "false");
  body.classList.add("modal_open");
}

function closeLoginModal() {
  if (!loginBox) return;
  loginBox.classList.remove("active");
  loginBox.setAttribute("aria-hidden", "true");
  body.classList.remove("modal_open");
}

function closeMobileMenu() {
  if (!globalNav || !mobileMenuBtn) return;
  globalNav.classList.remove("on");
  mobileMenuBtn.classList.remove("is_active");
  mobileMenuBtn.setAttribute("aria-expanded", "false");
  globalNav.querySelectorAll(".depth1 > li").forEach((item) => {
    if (!item.classList.contains("current")) item.classList.remove("active");
  });
  globalNav.querySelectorAll(".depth1 > li > a").forEach((link) => {
    if (!link.closest("li")?.classList.contains("current")) link.classList.remove("on");
  });
}

function initHeaderCurrent() {
  if (!globalNav) return;

  const currentFile = (window.location.pathname.split("/").pop() || "index.html").toLowerCase();
  globalNav.querySelectorAll(".depth1 > li").forEach((item) => {
    const links = Array.from(item.querySelectorAll("a[href]"));
    const isCurrent = links.some((link) => {
      const href = link.getAttribute("href") || "";
      const file = href.split("#")[0].split("?")[0].split("/").pop().toLowerCase();
      return file === currentFile;
    });

    item.classList.toggle("current", isCurrent);
    if (isCurrent) {
      item.classList.add("active");
      item.querySelector(":scope > a")?.classList.add("on");
    }
  });
}

window.addEventListener("scroll", setHeaderState, { passive: true });
setHeaderState();

document.querySelectorAll("[data-open-login]").forEach((button) => {
  button.addEventListener("click", openLoginModal);
});

document.querySelectorAll("[data-close-login]").forEach((button) => {
  button.addEventListener("click", closeLoginModal);
});

document.querySelectorAll("[data-auth-switch]").forEach((button) => {
  button.addEventListener("click", (event) => {
    event.preventDefault();
    setAuthPanel(button.dataset.authSwitch);
  });
});

if (loginBox) {
  loginBox.addEventListener("click", (event) => {
    if (event.target === loginBox) closeLoginModal();
  });
}

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeLoginModal();
    closeMobileMenu();
  }
});

const loginForm = document.getElementById("loginForm");
if (loginForm) {
  loginForm.addEventListener("submit", (event) => {
    event.preventDefault();
    alert("로그인 연결 전 시안 화면입니다.");
  });
}

const joinForm = document.getElementById("joinForm");
if (joinForm) {
  joinForm.addEventListener("submit", (event) => {
    event.preventDefault();
    alert("회원가입 연결 전 시안 화면입니다.");
  });
}

document.querySelectorAll("#findIdForm, #findPwForm").forEach((form) => {
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    alert("아이디/비밀번호 찾기 연결 전 시안 화면입니다.");
  });
});

const naverLoginBtn = document.getElementById("naverLoginBtn");
if (naverLoginBtn) {
  naverLoginBtn.addEventListener("click", (event) => {
    event.preventDefault();
    alert("네이버 간편 로그인 연결 영역입니다.");
  });
}

const kakaoLoginBtn = document.getElementById("kakaoLoginBtn");
if (kakaoLoginBtn) {
  kakaoLoginBtn.addEventListener("click", (event) => {
    event.preventDefault();
    alert("카카오톡 간편 로그인 연결 영역입니다.");
  });
}

if (globalNav && siteHeader) {
  globalNav.addEventListener("mouseenter", () => {
    if (!mobileQuery.matches) siteHeader.classList.add("on");
  });

  globalNav.addEventListener("mouseleave", () => {
    if (!mobileQuery.matches) siteHeader.classList.remove("on");
  });
}

if (mobileMenuBtn && globalNav) {
  mobileMenuBtn.addEventListener("click", () => {
    const isOpen = globalNav.classList.toggle("on");
    mobileMenuBtn.classList.toggle("is_active", isOpen);
    mobileMenuBtn.setAttribute("aria-expanded", String(isOpen));
  });

  mobileMenuClose?.addEventListener("click", closeMobileMenu);

  globalNav.querySelectorAll(".depth1 > li > a").forEach((link) => {
    link.addEventListener("click", (event) => {
      const item = link.closest("li");
      const hasSubmenu = item?.querySelector(".depth2 li");
      if (!mobileQuery.matches || !hasSubmenu || item.classList.contains("dir_menu")) return;

      event.preventDefault();
      const isOpen = item.classList.contains("active");
      globalNav.querySelectorAll(".depth1 > li").forEach((sibling) => {
        if (!sibling.classList.contains("current")) sibling.classList.remove("active");
      });
      globalNav.querySelectorAll(".depth1 > li > a").forEach((siblingLink) => {
        if (!siblingLink.closest("li")?.classList.contains("current")) siblingLink.classList.remove("on");
      });
      item.classList.toggle("active", !isOpen);
      link.classList.toggle("on", !isOpen);
    });
  });

  globalNav.querySelectorAll(".depth2 a, .dir_menu > a").forEach((link) => {
    link.addEventListener("click", () => {
      if (mobileQuery.matches) closeMobileMenu();
    });
  });
}

mobileQuery.addEventListener("change", () => {
  closeMobileMenu();
  siteHeader?.classList.remove("on");
});

const quickButton = document.getElementById("q_btn");
if (quickButton && quick) {
  quickButton.addEventListener("click", () => {
    quick.classList.toggle("open");
  });
}

document.querySelectorAll(".top_btn").forEach((button) => {
  button.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
});

function animateCounter(counter) {
  const target = Number(counter.dataset.count || "0");
  const duration = 1200;
  const start = performance.now();
  counter.textContent = "0";

  function tick(now) {
    const progress = Math.min((now - start) / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    counter.textContent = Math.round(target * eased).toString();
    if (progress < 1) requestAnimationFrame(tick);
  }

  requestAnimationFrame(tick);
}

function initMainSlider() {
  const slider = document.getElementById("main_top_slider");
  if (!slider) return;

  const slides = Array.from(slider.querySelectorAll(".swiper-slide"));
  const pagination = slider.querySelector(".main_slider_pagination");
  const toggleButton = slider.querySelector("#mainSliderToggle");
  const cursor = document.getElementById("circleCursor");
  if (!slides.length || !pagination) return;

  pagination.innerHTML = "";
  const buttons = slides.map((_, index) => {
    const button = document.createElement("button");
    button.type = "button";
    button.setAttribute("aria-label", `${index + 1}번째 비주얼`);
    button.addEventListener("click", (event) => {
      event.stopPropagation();
      showSlide(index, true);
    });
    pagination.appendChild(button);
    return button;
  });

  let current = slides.findIndex((slide) => slide.classList.contains("active"));
  if (current < 0) current = 0;
  let timer;
  let isPaused = false;

  function showSlide(index, manual = false) {
    slides[current]?.classList.remove("active");
    buttons[current]?.classList.remove("active");
    current = (index + slides.length) % slides.length;
    slides[current].classList.add("active");
    buttons[current].classList.add("active");
    slides[current].querySelectorAll(".counter").forEach(animateCounter);
    if (manual) restart();
  }

  function updateToggleButton() {
    if (!toggleButton) return;
    toggleButton.classList.toggle("is_paused", isPaused);
    toggleButton.setAttribute("aria-pressed", String(isPaused));
    toggleButton.setAttribute("aria-label", isPaused ? "비주얼 배너 재생" : "비주얼 배너 일시정지");
  }

  function restart() {
    window.clearInterval(timer);
    if (isPaused) return;
    timer = window.setInterval(() => showSlide(current + 1), 5200);
  }

  function toggleAutoplay() {
    isPaused = !isPaused;
    updateToggleButton();
    restart();
  }

  function updateCursor(event) {
    if (!cursor) return;
    const rect = slider.getBoundingClientRect();
    const isPrev = event.clientX < rect.left + rect.width / 2;
    cursor.style.left = `${event.clientX}px`;
    cursor.style.top = `${event.clientY}px`;
    cursor.classList.add("is_visible");
    cursor.classList.toggle("is_prev", isPrev);
  }

  let pointerStartX = 0;
  let pointerStartY = 0;
  let pointerMoved = false;

  slider.addEventListener("pointerenter", updateCursor);
  slider.addEventListener("pointermove", (event) => {
    updateCursor(event);
    if (event.buttons) {
      pointerMoved = pointerMoved || Math.abs(event.clientX - pointerStartX) > 8 || Math.abs(event.clientY - pointerStartY) > 8;
    }
  });
  slider.addEventListener("pointerleave", () => {
    cursor?.classList.remove("is_visible", "is_dragging");
  });
  slider.addEventListener("pointerdown", (event) => {
    if (event.target.closest(".main_slider_pagination, .main_slider_toggle")) return;
    pointerStartX = event.clientX;
    pointerStartY = event.clientY;
    pointerMoved = false;
    cursor?.classList.add("is_dragging");
    slider.setPointerCapture?.(event.pointerId);
  });
  slider.addEventListener("pointerup", (event) => {
    if (event.target.closest(".main_slider_pagination, .main_slider_toggle")) return;
    cursor?.classList.remove("is_dragging");
    slider.releasePointerCapture?.(event.pointerId);

    const deltaX = event.clientX - pointerStartX;
    if (Math.abs(deltaX) > 42) {
      showSlide(deltaX < 0 ? current + 1 : current - 1, true);
      return;
    }

    const rect = slider.getBoundingClientRect();
    const nextIndex = event.clientX < rect.left + rect.width / 2 ? current - 1 : current + 1;
    if (!pointerMoved) showSlide(nextIndex, true);
  });
  slider.addEventListener("pointercancel", () => {
    cursor?.classList.remove("is_dragging");
  });

  toggleButton?.addEventListener("click", (event) => {
    event.stopPropagation();
    toggleAutoplay();
  });

  showSlide(current);
  updateToggleButton();
  restart();
}

function initReveal() {
  const targets = document.querySelectorAll(".reveal");
  if (!targets.length) return;

  if (!("IntersectionObserver" in window)) {
    targets.forEach((target) => target.classList.add("active"));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("active");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.16 }
  );

  targets.forEach((target) => observer.observe(target));
}

function initAccordion() {
  const groups = document.querySelectorAll(".main_cont06_accordion_wrap");
  if (!groups.length) return;

  const closeGroup = (group) => {
    group.querySelectorAll(".main_cont06_qna_wrap").forEach((item) => item.classList.remove("active"));
    group.querySelectorAll(".main_cont06_que").forEach((question) => question.classList.remove("on"));
    group.querySelectorAll(".main_cont06_anw").forEach((answer) => {
      answer.classList.remove("on");
      answer.style.display = "none";
    });
  };

  groups.forEach((group) => {
    group.querySelectorAll(".main_cont06_que").forEach((question) => {
      question.addEventListener("click", (event) => {
        event.preventDefault();
        const item = question.closest(".main_cont06_qna_wrap");
        const answer = question.nextElementSibling;
        if (!item || !answer?.classList.contains("main_cont06_anw")) return;

        const willOpen = !question.classList.contains("on");
        closeGroup(group);
        if (willOpen) {
          item.classList.add("active");
          question.classList.add("on");
          answer.classList.add("on");
          answer.style.display = "block";
        }
      });
    });
  });

  document.querySelectorAll(".main_cont06_bg, .main_cont06_bg2, .main_cont06_section_bg, .main_cont06_title").forEach((closer) => {
    closer.addEventListener("click", (event) => {
      event.preventDefault();
      groups.forEach(closeGroup);
    });
  });
}

function initMainCont06BackgroundFlash() {
  const sections = document.querySelectorAll(".main_cont_wrap06, .section5_notice_wrap");
  if (!sections.length) return;

  let ticking = false;

  const update = () => {
    sections.forEach((section) => {
      const rect = section.getBoundingClientRect();
      const shouldTurnBlack = rect.top <= window.innerHeight * 0.65 && rect.bottom > 0;
      section.classList.toggle("bg_flash_on", shouldTurnBlack);
      section.querySelectorAll(".main_cont06_bg, .main_cont06_bg2").forEach((bg) => {
        bg.classList.toggle("on", shouldTurnBlack);
      });
    });
    ticking = false;
  };

  const requestUpdate = () => {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(update);
  };

  window.addEventListener("scroll", requestUpdate, { passive: true });
  window.addEventListener("resize", requestUpdate);
  update();
}

function initMobileMainCont02Scroll() {
  const section = document.querySelector("#section1");
  const items = Array.from(section?.querySelectorAll(".main_cont02_list_item") || []);
  if (!section || !items.length) return;

  const mobileQuery = window.matchMedia("(max-width: 1024px)");
  let ticking = false;

  const clearActive = () => {
    items.forEach((item) => item.classList.remove("is_scroll_active"));
  };

  const update = () => {
    if (!mobileQuery.matches) {
      clearActive();
      ticking = false;
      return;
    }

    const triggerY = window.innerHeight * 0.42;
    const activeBand = Math.min(240, window.innerHeight * 0.3);
    let activeItem = null;
    let closestDistance = Infinity;

    items.forEach((item) => {
      const rect = item.getBoundingClientRect();
      const centerY = rect.top + rect.height / 2;
      const distance = Math.abs(centerY - triggerY);
      const isNearTrigger = distance <= activeBand && rect.bottom > 0 && rect.top < window.innerHeight;
      if (isNearTrigger && distance < closestDistance) {
        activeItem = item;
        closestDistance = distance;
      }
    });

    items.forEach((item) => {
      item.classList.toggle("is_scroll_active", item === activeItem);
    });
    ticking = false;
  };

  const requestUpdate = () => {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(update);
  };

  window.addEventListener("scroll", requestUpdate, { passive: true });
  window.addEventListener("resize", requestUpdate);
  mobileQuery.addEventListener?.("change", requestUpdate);
  update();
}

function initBeforeAfterSection() {
  document.querySelectorAll("[data-beforeafter-slider]").forEach((slider) => {
    const allSlides = Array.from(slider.querySelectorAll("[data-ba-slide]"));
    const allThumbs = Array.from(slider.querySelectorAll("[data-ba-thumb]"));
    const singleCase = slider.classList.contains("beforeafter_slider_teeth");
    const slides = singleCase ? allSlides.slice(0, 1) : allSlides;
    const thumbs = singleCase ? allThumbs.slice(0, 1) : allThumbs;
    const prevButton = slider.querySelector("[data-ba-prev]");
    const nextButton = slider.querySelector("[data-ba-next]");
    const currentText = slider.querySelector("[data-ba-current]");
    const totalText = slider.querySelector("[data-ba-total]");
    let current = slides.findIndex((slide) => slide.classList.contains("is_active"));
    if (current < 0) current = 0;

    const setComparePosition = (range) => {
      const media = range.closest(".beforeafter_media");
      if (!media) return;
      const value = Math.min(100, Math.max(0, Number(range.value || 50)));
      media.style.setProperty("--ba-pos", `${value}%`);
    };

    const setMediaPosition = (media, clientX) => {
      const rect = media.getBoundingClientRect();
      const value = Math.min(100, Math.max(0, ((clientX - rect.left) / rect.width) * 100));
      media.style.setProperty("--ba-pos", `${value}%`);
      const range = media.querySelector(".beforeafter_range");
      if (range) range.value = String(Math.round(value));
    };

    const showSlide = (index) => {
      if (!slides.length) return;
      current = (index + slides.length) % slides.length;
      slides.forEach((slide, slideIndex) => {
        slide.classList.toggle("is_active", slideIndex === current);
      });
      thumbs.forEach((thumb, thumbIndex) => {
        thumb.classList.toggle("is_active", thumbIndex === current);
      });
      if (currentText) currentText.textContent = String(current + 1).padStart(2, "0");
      slides[current].querySelectorAll(".beforeafter_range").forEach(setComparePosition);
    };

    slides.forEach((slide) => {
      slide.querySelectorAll(".beforeafter_media").forEach((media) => {
        let dragging = false;

        media.addEventListener("pointerdown", (event) => {
          dragging = true;
          media.setPointerCapture?.(event.pointerId);
          setMediaPosition(media, event.clientX);
        });

        media.addEventListener("pointermove", (event) => {
          if (!dragging) return;
          setMediaPosition(media, event.clientX);
        });

        media.addEventListener("pointerup", (event) => {
          dragging = false;
          media.releasePointerCapture?.(event.pointerId);
        });

        media.addEventListener("pointercancel", () => {
          dragging = false;
        });
      });

      slide.querySelectorAll(".beforeafter_range").forEach((range) => {
        setComparePosition(range);
        range.addEventListener("input", () => setComparePosition(range));
      });
    });

    thumbs.forEach((thumb) => {
      thumb.addEventListener("click", () => {
        showSlide(Number(thumb.dataset.baThumb || 0));
      });
    });

    prevButton?.addEventListener("click", () => showSlide(current - 1));
    nextButton?.addEventListener("click", () => showSlide(current + 1));
    if (totalText) totalText.textContent = String(slides.length).padStart(2, "0");
    showSlide(current);
  });
}

function initOveRection05() {
  const slider = document.querySelector(".main_cont08_section .re05_container");
  if (!slider || typeof window.Swiper !== "function") return;

  new window.Swiper(slider, {
    slidesPerView: "auto",
    speed: 2000,
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
    observer: true,
    observeParents: true,
  });
}

function initTypingText() {
  const target = document.getElementById("typingText");
  if (!target) return;

  const phrases = ["전문성을 더한 구강 건강", "입술의 위치까지 고려한 솔루션"];
  const colorFillClass = "is_color_filled";
  const resetColorFill = () => target.classList.remove(colorFillClass);
  const playColorFill = () => {
    target.classList.remove(colorFillClass);
    void target.offsetWidth;
    target.classList.add(colorFillClass);
  };
  let phraseIndex = 0;
  let charIndex = phrases[0].length;
  let deleting = false;
  let pauseUntil = performance.now() + 1800;

  target.textContent = phrases[phraseIndex];
  window.requestAnimationFrame(playColorFill);

  window.setInterval(() => {
    const now = performance.now();
    if (now < pauseUntil) return;

    const phrase = phrases[phraseIndex];
    if (deleting) {
      resetColorFill();
      charIndex -= 1;
      if (charIndex <= 0) {
        deleting = false;
        phraseIndex = (phraseIndex + 1) % phrases.length;
        charIndex = 0;
        pauseUntil = now + 250;
      }
    } else {
      charIndex += 1;
      if (charIndex >= phrases[phraseIndex].length) {
        charIndex = phrases[phraseIndex].length;
        target.textContent = phrases[phraseIndex];
        playColorFill();
        deleting = true;
        pauseUntil = now + 1800;
        return;
      }
    }

    target.textContent = phrases[phraseIndex].slice(0, charIndex);
  }, 90);
}

function escapeHTML(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

async function loadCafe24Items(url) {
  const response = await fetch(url, { headers: { Accept: "application/json" } });
  if (!response.ok) throw new Error("api unavailable");
  const data = await response.json();
  if (!data.ok || !Array.isArray(data.items)) throw new Error("invalid api response");
  return data.items;
}

function renderNoticeItems(items) {
  const list = document.querySelector(".notice_list");
  if (!list || !items.length) return;

  list.innerHTML = items.map((item, index) => {
    const pinned = Number(item.is_pinned) === 1;
    const badge = pinned
      ? '<span class="notice_badge">공지</span>'
      : `<span class="notice_num">${String(items.length - index).padStart(2, "0")}</span>`;
    return `
      <a class="notice_item${pinned ? " is_notice" : ""}" href="#href_id">
        ${badge}
        <strong>${escapeHTML(item.title)}</strong>
        <em>${escapeHTML(item.published_at)}</em>
      </a>
    `;
  }).join("");

  const countText = document.querySelector(".notice_board .board_toolbar strong");
  if (countText) countText.textContent = `전체 ${items.length}건`;
}

function renderCounselItems(items) {
  const list = document.querySelector(".counsel_list");
  if (!list || !items.length) return;

  list.innerHTML = items.map((item) => {
    const done = item.status === "done";
    const secret = item.is_secret ? " is_secret" : "";
    return `
      <a class="counsel_item${secret}" href="#href_id">
        <span class="counsel_state ${done ? "done" : "wait"}">${done ? "답변완료" : "답변대기"}</span>
        <strong>${escapeHTML(item.title)}</strong>
        <em>${escapeHTML(item.name)} · ${escapeHTML(item.created_at)}</em>
      </a>
    `;
  }).join("");
}

function initCafe24Content() {
  if (document.querySelector(".notice_list")) {
    loadCafe24Items("./api/notices.php").then(renderNoticeItems).catch(() => {});
  }

  if (document.querySelector(".counsel_list")) {
    loadCafe24Items("./api/counsels.php").then(renderCounselItems).catch(() => {});
  }

  document.querySelectorAll("[data-counsel-form]").forEach((form) => {
    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      if (!form.reportValidity()) return;

      const submitButton = form.querySelector('button[type="submit"]');
      submitButton?.setAttribute("disabled", "disabled");
      try {
        const response = await fetch("./api/counsel_create.php", {
          method: "POST",
          body: new FormData(form),
          headers: { Accept: "application/json" },
        });
        const data = await response.json();
        if (!response.ok || !data.ok) throw new Error(data.message || "submit failed");
        alert(data.message || "?곷떞???묒닔?섏뿀?듬땲??");
        form.reset();
        loadCafe24Items("./api/counsels.php").then(renderCounselItems).catch(() => {});
      } catch (error) {
        alert("?꾩옱???섑뵆 ?붾㈃?낅땲?? Cafe24 PHP DB ?곌껐 ???곷떞 ?묒닔媛 ?쒖꽦?붾맗?덈떎.");
      } finally {
        submitButton?.removeAttribute("disabled");
      }
    });
  });
}

initMainSlider();
initHeaderCurrent();
initReveal();
initAccordion();
initMainCont06BackgroundFlash();
initMobileMainCont02Scroll();
initBeforeAfterSection();
initOveRection05();
initTypingText();
initCafe24Content();
