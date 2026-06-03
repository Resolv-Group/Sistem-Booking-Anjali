import "../css/app.css";

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
window.Swal = Swal;

// Wait until the DOM is completely ready before running Lucide
document.addEventListener("DOMContentLoaded", () => {

    // 1. Convert any class-based icons if they exist
    document.querySelectorAll('[class*="lucide-"]').forEach(el => {
        const classList = Array.from(el.classList);
        const lucideClass = classList.find(c => c.startsWith('lucide-'));
        if (lucideClass) {
            const iconName = lucideClass.replace('lucide-', '');
            el.setAttribute('data-lucide', iconName);
        }
    });

    // 2. Initialize the icons globally
    createIcons({ icons });
});

Alpine.start();
