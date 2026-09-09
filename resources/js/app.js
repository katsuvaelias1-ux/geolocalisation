import './bootstrap';

const siteIntro = document.getElementById('site-intro');
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const introDuration = 3500;

const closeIntro = () => {
	document.body.classList.remove('site-loading');
	document.body.classList.add('site-ready');
	if (!siteIntro) return;
	siteIntro.classList.add('is-leaving');
	window.setTimeout(() => siteIntro.remove(), prefersReducedMotion ? 30 : 850);
};

window.setTimeout(closeIntro, prefersReducedMotion ? 50 : introDuration);

const menuToggle = document.querySelector('.menu-toggle');
const mobileMenu = document.querySelector('.mobile-menu');

menuToggle?.addEventListener('click', () => {
	const isOpen = mobileMenu.classList.toggle('is-open');
	menuToggle.setAttribute('aria-expanded', String(isOpen));
	menuToggle.textContent = isOpen ? '×' : '☰';
});

const closeMobileMenu = () => {
	if (!mobileMenu?.classList.contains('is-open')) return;
	mobileMenu.classList.remove('is-open');
	menuToggle?.setAttribute('aria-expanded', 'false');
	if (menuToggle) menuToggle.textContent = '☰';
};

window.addEventListener('resize', () => {
	if (window.innerWidth > 850) closeMobileMenu();
});

window.addEventListener('keydown', (event) => {
	if (event.key === 'Escape') closeMobileMenu();
});

const revealItems = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver((entries, observer) => {
	entries.forEach((entry) => {
		if (entry.isIntersecting) {
			entry.target.classList.add('is-visible');
			observer.unobserve(entry.target);
		}
	});
}, { threshold: 0.12 });

revealItems.forEach((item) => revealObserver.observe(item));

const stickyHeader = document.querySelector('.site-header');
const parallaxItems = [...document.querySelectorAll('[data-parallax]')];
const reduceMotion = prefersReducedMotion;
let animationFrame;

const updateScrollEffects = () => {
	const scrollY = window.scrollY;
	stickyHeader?.classList.toggle('is-scrolled', scrollY > 12);

	if (!reduceMotion) {
		parallaxItems.forEach((item) => {
			const speed = Number(item.dataset.parallax || 0);
			const position = item.getBoundingClientRect();
			const viewportCenter = window.innerHeight / 2;
			const offset = (position.top + position.height / 2 - viewportCenter) * speed;
			item.style.setProperty('--scroll-shift', `${Math.round(offset)}px`);
		});
	}

	animationFrame = undefined;
};

const requestScrollUpdate = () => {
	if (!animationFrame) animationFrame = window.requestAnimationFrame(updateScrollEffects);
};

window.addEventListener('scroll', requestScrollUpdate, { passive: true });
window.addEventListener('resize', requestScrollUpdate);
updateScrollEffects();

if (!prefersReducedMotion && window.matchMedia('(pointer: fine)').matches) {
	document.querySelectorAll('[data-magnetic]').forEach((element) => {
		element.addEventListener('pointermove', (event) => {
			const bounds = element.getBoundingClientRect();
			const moveX = (event.clientX - bounds.left - bounds.width / 2) * 0.12;
			const moveY = (event.clientY - bounds.top - bounds.height / 2) * 0.12;
			element.style.setProperty('--magnetic-x', `${moveX.toFixed(1)}px`);
			element.style.setProperty('--magnetic-y', `${moveY.toFixed(1)}px`);
		});
		element.addEventListener('pointerleave', () => {
			element.style.setProperty('--magnetic-x', '0px');
			element.style.setProperty('--magnetic-y', '0px');
		});
	});
}
