let slideIndex = 0;
showSlides();

// Tự động chuyển slide sau mỗi 3 giây
let autoSlide = setInterval(function() {
  plusSlides(1);
}, 3000);

function showSlides() {
  const slides = document.querySelectorAll('.slide');
  slides.forEach((slide, index) => {
    slide.style.display = index === slideIndex ? 'block' : 'none';
  });
}

function plusSlides(n) {
  const slides = document.querySelectorAll('.slide');
  slideIndex = (slideIndex + n + slides.length) % slides.length;
  showSlides();
}

// Khi người dùng click vào các nút điều hướng, tự động đặt lại thời gian tự chuyển slide
document.querySelector('.prev').addEventListener('click', function() {
  clearInterval(autoSlide);
  plusSlides(-1);
  autoSlide = setInterval(function() {
    plusSlides(1);
  }, 3000);
});

document.querySelector('.next').addEventListener('click', function() {
  clearInterval(autoSlide);
  plusSlides(1);
  autoSlide = setInterval(function() {
    plusSlides(1);
  }, 3000);
});
