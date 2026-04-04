import './bootstrap';

import Alpine from 'alpinejs';

import collapse from '@alpinejs/collapse'; 

Alpine.plugin(collapse)

window.Alpine = Alpine;

Alpine.data('charCounter', (max = 28) => ({
    max,
    count: 0,

    update(el) {
        this.count = el.value.length
    },

    get remaining() {
        return this.max - this.count
    },

    get color() {
        if (this.count > this.max) return 'text-red-500'
        if (this.count > this.max * 0.8) return 'text-yellow-500'
        return 'text-gray-400'
    }
}))


Alpine.start();

import './catalog.js';
