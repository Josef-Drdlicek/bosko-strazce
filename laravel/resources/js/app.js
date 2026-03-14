import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('counter', () => ({
    current: 0,
    target: 0,
    duration: 1500,
    init() {
        this.target = parseInt(this.$el.dataset.target) || 0;
        if (this.target === 0) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animate();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        observer.observe(this.$el);
    },
    animate() {
        const start = performance.now();
        const step = (now) => {
            const progress = Math.min((now - start) / this.duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            this.current = Math.floor(eased * this.target);
            if (progress < 1) requestAnimationFrame(step);
            else this.current = this.target;
        };
        requestAnimationFrame(step);
    },
    get formatted() {
        return this.current.toLocaleString('cs-CZ');
    }
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const reveals = document.querySelectorAll('.reveal');
    if (reveals.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('revealed');
                }, index * 80);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    reveals.forEach(el => observer.observe(el));
});
