let currentSlide = 0;
        const slides = document.querySelectorAll('.studio-slide');
        const indicators = document.querySelectorAll('.indicator');
        const totalSlides = slides.length;
        const container = document.querySelector('.studio-container');

        function updateContainerHeight() {
            const activeSlide = slides[currentSlide];
            activeSlide.style.height = 'auto';
            
            setTimeout(() => {
                const slideHeight = activeSlide.scrollHeight;
                container.style.height = Math.max(600, slideHeight) + 'px';
            }, 50);
        }

        function updateSlide(newIndex) {
            slides[currentSlide].classList.remove('active');
            slides[currentSlide].classList.add('prev');
            indicators[currentSlide].classList.remove('active');

            setTimeout(() => {
                slides[currentSlide].classList.remove('prev');
                slides[newIndex].classList.add('active');
                indicators[newIndex].classList.add('active');
                currentSlide = newIndex;
                
                updateContainerHeight();
            }, 100);
        }

        function nextStudio() {
            const nextIndex = (currentSlide + 1) % totalSlides;
            updateSlide(nextIndex);
        }

        function prevStudio() {
            const prevIndex = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlide(prevIndex);
        }

        function goToSlide(index) {
            if (index !== currentSlide) {
                updateSlide(index);
            }
        }

        let autoSlideInterval;

        function startAutoSlide() {
            autoSlideInterval = setInterval(nextStudio, 5000);
        }

        function stopAutoSlide() {
            clearInterval(autoSlideInterval);
        }

        window.addEventListener('load', () => {
            updateContainerHeight();
        });

        startAutoSlide();

        const slider = document.querySelector('.studio-slider');
        slider.addEventListener('mouseenter', stopAutoSlide);
        slider.addEventListener('mouseleave', startAutoSlide);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                prevStudio();
            } else if (e.key === 'ArrowRight') {
                nextStudio();
            }
        });

        let startX = 0;
        let endX = 0;

        slider.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        });

        slider.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            handleSwipe();
        });

        function handleSwipe() {
            const threshold = 50;
            const diff = startX - endX;

            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    nextStudio();
                } else {
                    prevStudio();
                }
            }
        }