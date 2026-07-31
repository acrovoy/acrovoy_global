import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';

export function initSwipers() {

    document.querySelectorAll('.certificates-swiper').forEach((element) => {

        if (element.swiper) {
            element.swiper.destroy(true, true);
        }

        const container = element.parentElement;

        new Swiper(element, {
            modules: [Navigation],

            slidesPerView: 'auto',
            spaceBetween: 16,

            watchOverflow: true,
            slidesOffsetBefore: 0,
            slidesOffsetAfter: 16,

            navigation: {
                nextEl: container.querySelector('.cert-next'),
                prevEl: container.querySelector('.cert-prev'),
            },

            breakpoints: {
                0: {
                    slidesPerView: 1.2,
                },
                640: {
                    slidesPerView: 2.2,
                },
                1024: {
                    slidesPerView: 3,
                },
                1440: {
                    slidesPerView: 5,
                },
            },
        });

    });

}