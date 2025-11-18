document.addEventListener("DOMContentLoaded", function () {
  // Swiper для галереи
  const gallerySwiper = new Swiper(".gallerySwiper", {
    loop: true,
    centeredSlides: true,
    slidesPerView: "auto",
    spaceBetween: 30,
    effect: "coverflow",
    coverflowEffect: {
      rotate: 0,
      stretch: 0,
      depth: 150,
      modifier: 1,
      slideShadows: false,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".gallery-next",
      prevEl: ".gallery-prev",
    },
    breakpoints: {
      320: {
        spaceBetween: 15,
      },
      768: {
        spaceBetween: 25,
      },
      992: {
        spaceBetween: 30,
      },
    },
  });
  // Swiper для отзывов
  const reviewsSwiper = new Swiper(".reviewsSwiper", {
    loop: true,
    centeredSlides: true,
    slidesPerView: "auto",
    spaceBetween: 30,
    effect: "coverflow",
    coverflowEffect: {
      rotate: 0,
      stretch: 0,
      depth: 150,
      modifier: 1,
      slideShadows: false,
    },
    pagination: {
      el: ".swiper-fraction",
      type: "fraction",
      renderFraction: (current, total) =>
        `<span class="${current}"></span> / <span class="${total}"></span>`,
    },
    navigation: {
      nextEl: ".reviews-next",
      prevEl: ".reviews-prev",
    },
    breakpoints: {
      320: {
        spaceBetween: 15,
      },
      768: {
        spaceBetween: 25,
      },
      992: {
        spaceBetween: 30,
      },
    },
  });
   // Хранилище для активных внутренних слайдеров
    const activeImgSliders = new Map();
    // Основной Swiper
    const mainSwiper = new Swiper(".rooms-swiper", {
      slidesPerView: 1,
      spaceBetween: 20,
      speed: 400,
      centeredSlides: false,
      watchOverflow: true,
      lazy: true,
      preloadImages: false,
      updateOnWindowResize: true,
      navigation: {
        nextEl: ".swiper-next",
        prevEl: ".swiper-prev",
      },
      breakpoints: {
        576: {
          slidesPerView: 1,
          spaceBetween: 20,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 25,
        },
        992: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        1400: {
          slidesPerView: 3,
          spaceBetween: 20,
        },
      },
      on: {
        init: function() {
          // Инициализируем внутренние слайдеры для видимых карточек
          const visibleSlides = this.slides.filter(slide => {
            const rect = slide.getBoundingClientRect();
            return rect.right > 0 && rect.left < (window.innerWidth || document.documentElement.clientWidth);
          });
          visibleSlides.forEach(slide => {
            initImgSlider(slide);
          });
        },
        slideChange: function() {
          // Очищаем внутренние слайдеры для невидимых карточек
          activeImgSliders.forEach((_, slideEl) => {
            const rect = slideEl.getBoundingClientRect();
            const isVisible = rect.right > 0 && rect.left < (window.innerWidth || document.documentElement.clientWidth);
            if (!isVisible) {
              destroyImgSlider(slideEl);
            }
          });
          // Инициализируем внутренние слайдеры для новых видимых карточек
          this.slides.forEach(slide => {
            const rect = slide.getBoundingClientRect();
            const isVisible = rect.right > 0 && rect.left < (window.innerWidth || document.documentElement.clientWidth);
            if (isVisible && !activeImgSliders.has(slide)) {
              initImgSlider(slide);
            }
          });
        }
      }
    });
    // Инициализация внутреннего слайдера изображений
    function initImgSlider(slideEl) {
      const slider = slideEl.querySelector(".img-slider");
      if (!slider) return;
      const slidesContainer = slider.querySelector(".img-slides");
      const dots = slider.querySelectorAll(".img-dot");
      const prevArrow = slider.querySelector(".img-left");
      const nextArrow = slider.querySelector(".img-right");
      // Если только одно изображение - не нужен слайдер
      if (dots.length <= 1) return;
      let currentSlide = 0;
      const totalSlides = dots.length;
      function updateSlider() {
        slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
        dots.forEach((dot, index) => {
          dot.classList.toggle("active", index === currentSlide);
        });
      }
      function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlider();
      }
      function prevSlide() {
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        updateSlider();
      }
      function goToSlide(index) {
        currentSlide = index;
        updateSlider();
      }
      // Навешиваем обработчики
      if (nextArrow) nextArrow.addEventListener("click", nextSlide);
      if (prevArrow) prevArrow.addEventListener("click", prevSlide);
      dots.forEach((dot, index) => {
        dot.addEventListener("click", () => goToSlide(index));
      });
      // Сохраняем для последующей очистки
      activeImgSliders.set(slideEl, {
        nextSlide,
        prevSlide,
        goToSlide,
        dots,
        nextArrow,
        prevArrow
      });
      updateSlider();
    }
    // Очистка внутреннего слайдера
    function destroyImgSlider(slideEl) {
      const sliderData = activeImgSliders.get(slideEl);
      if (!sliderData) return;
      const { nextSlide, prevSlide, goToSlide, dots, nextArrow, prevArrow } = sliderData;
      if (nextArrow) nextArrow.removeEventListener("click", nextSlide);
      if (prevArrow) prevArrow.removeEventListener("click", prevSlide);
      dots.forEach((dot, index) => {
        dot.removeEventListener("click", () => goToSlide(index));
      });
      activeImgSliders.delete(slideEl);
    }
    // Обработчик для отслеживания видимости слайдов при ресайзе
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        mainSwiper.slides.forEach(slide => {
          const rect = slide.getBoundingClientRect();
          const isVisible = rect.right > 0 && rect.left < (window.innerWidth || document.documentElement.clientWidth);
          if (isVisible && !activeImgSliders.has(slide)) {
            initImgSlider(slide);
          } else if (!isVisible && activeImgSliders.has(slide)) {
            destroyImgSlider(slide);
          }
        });
      }, 250);
    });
});
