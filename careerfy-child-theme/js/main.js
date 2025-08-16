document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('.cfy-site-header .menu-toggle');
    const menu = document.querySelector('.cfy-site-header nav');
    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            menu.classList.toggle('open');
        });
    }
});
