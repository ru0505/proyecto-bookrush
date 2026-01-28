// Script para menú hamburguesa móvil - Book Rush
// Autor: Sistema responsive
// Fecha: 27 de diciembre de 2025

document.addEventListener('DOMContentLoaded', function() {
    // Crear overlay si no existe
    if (!document.querySelector('.menu-overlay')) {
        const overlay = document.createElement('div');
        overlay.className = 'menu-overlay';
        document.body.appendChild(overlay);
        
        // Cerrar menú al click en overlay
        overlay.addEventListener('click', toggleMenu);
    }
    
    // Prevenir zoom en double-tap (iOS)
    let lastTap = 0;
    document.addEventListener('touchend', function(e) {
        const now = Date.now();
        if (now - lastTap < 300) {
            e.preventDefault();
        }
        lastTap = now;
    }, { passive: false });
    
    // Mejorar feedback táctil en botones
    document.querySelectorAll('a, button, .libro').forEach(el => {
        el.addEventListener('touchstart', function() {
            this.style.opacity = '0.8';
        }, { passive: true });
        
        el.addEventListener('touchend', function() {
            this.style.opacity = '1';
        }, { passive: true });
    });
    
    // Cerrar menú al hacer clic en un enlace
    document.querySelectorAll('nav a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                toggleMenu();
            }
        });
    });
    
    // Cerrar menú al redimensionar ventana (si cambia a desktop)
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                const header = document.querySelector('header');
                const menuToggle = document.querySelector('.menu-toggle');
                const overlay = document.querySelector('.menu-overlay');
                
                if (header) header.classList.remove('show');
                if (menuToggle) menuToggle.classList.remove('active');
                if (overlay) overlay.classList.remove('show');
                
                // Rehabilitar scroll
                document.body.style.overflow = '';
            }
        }, 250);
    });
});

// Función para toggle del menú
function toggleMenu() {
    const header = document.querySelector('header');
    const menuToggle = document.querySelector('.menu-toggle');
    const overlay = document.querySelector('.menu-overlay');
    
    if (header && menuToggle) {
        const isOpen = header.classList.contains('show');
        
        if (isOpen) {
            // Cerrar menú
            header.classList.remove('show');
            menuToggle.classList.remove('active');
            if (overlay) overlay.classList.remove('show');
            document.body.style.overflow = ''; // Rehabilitar scroll
        } else {
            // Abrir menú
            header.classList.add('show');
            menuToggle.classList.add('active');
            if (overlay) overlay.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevenir scroll de fondo
        }
    }
}

// Hacer la función global para que pueda ser llamada desde onclick
window.toggleMenu = toggleMenu;
