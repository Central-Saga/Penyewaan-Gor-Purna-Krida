import 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

function initGsapAnimations() {
    // Bersihkan ScrollTrigger lama jika ada pergantian halaman via wire:navigate
    ScrollTrigger.getAll().forEach(trigger => trigger.kill());

    // 1. Hero Section Entrance Animation
    if (document.querySelector('.landing-hero')) {
        const heroTl = gsap.timeline({ defaults: { ease: 'power3.out' } });

        heroTl
            .fromTo('.landing-hero .badge, .landing-hero .stat-chip',
                { opacity: 0, y: -20 },
                { opacity: 1, y: 0, duration: 0.6, stagger: 0.08 }
            )
            .fromTo('.landing-hero h1',
                { opacity: 0, y: 35 },
                { opacity: 1, y: 0, duration: 0.8 },
                '-=0.4'
            )
            .fromTo('.landing-hero .lead',
                { opacity: 0, y: 25 },
                { opacity: 1, y: 0, duration: 0.7 },
                '-=0.5'
            )
            .fromTo('.landing-hero .btn',
                { opacity: 0, y: 20, scale: 0.95 },
                { opacity: 1, y: 0, scale: 1, duration: 0.5, stagger: 0.1 },
                '-=0.4'
            )
            .fromTo('.hero-preview-card',
                { opacity: 0, x: 50, scale: 0.92 },
                { opacity: 1, x: 0, scale: 1, duration: 0.9, ease: 'back.out(1.4)' },
                '-=0.6'
            );

        // Efek floating halus pada kartu preview hero
        gsap.to('.hero-preview-card', {
            y: -10,
            duration: 2.8,
            repeat: -1,
            yoyo: true,
            ease: 'sine.inOut'
        });
    }

    // 2. Animasi ScrollTrigger Kartu Fasilitas
    const facilityCards = document.querySelectorAll('.facility-card');
    if (facilityCards.length > 0) {
        gsap.fromTo(facilityCards,
            { opacity: 0, y: 35 },
            {
                opacity: 1,
                y: 0,
                duration: 0.65,
                stagger: 0.1,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: facilityCards[0].closest('.row') || facilityCards[0],
                    start: 'top 85%',
                    once: true
                }
            }
        );
    }

    // 3. Animasi ScrollTrigger Langkah Pemesanan
    const stepCards = document.querySelectorAll('.step-card');
    if (stepCards.length > 0) {
        gsap.fromTo(stepCards,
            { opacity: 0, y: 35 },
            {
                opacity: 1,
                y: 0,
                duration: 0.65,
                stagger: 0.12,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: stepCards[0].closest('.row') || stepCards[0],
                    start: 'top 85%',
                    once: true
                }
            }
        );
    }

    // 4. Animasi ScrollTrigger Keunggulan
    const featureBoxes = document.querySelectorAll('.feature-box');
    if (featureBoxes.length > 0) {
        gsap.fromTo(featureBoxes,
            { opacity: 0, y: 30, scale: 0.96 },
            {
                opacity: 1,
                y: 0,
                scale: 1,
                duration: 0.55,
                stagger: 0.08,
                ease: 'back.out(1.2)',
                scrollTrigger: {
                    trigger: featureBoxes[0].closest('.row') || featureBoxes[0],
                    start: 'top 85%',
                    once: true
                }
            }
        );
    }

    // 5. Animasi Entrance Form Login & Register
    const authForm = document.querySelector('.auth-form-anim');
    if (authForm) {
        gsap.fromTo(authForm,
            { opacity: 0, y: 25 },
            { opacity: 1, y: 0, duration: 0.6, ease: 'power2.out' }
        );
    }
    const authVisualCard = document.querySelector('.auth-visual-card');
    if (authVisualCard) {
        gsap.fromTo(authVisualCard,
            { opacity: 0, scale: 0.94, x: 25 },
            { opacity: 1, scale: 1, x: 0, duration: 0.8, ease: 'power3.out', delay: 0.1 }
        );
    }
}

document.addEventListener('DOMContentLoaded', initGsapAnimations);
document.addEventListener('livewire:navigated', initGsapAnimations);