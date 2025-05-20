const reviews = new Array(
    { author: "Sarah M.", text: "Une expérience incroyable à Bizerte ! Les plages étaient magnifiques et les habitants très accueillants.", image: "https://th.bing.com/th/id/OIP.MISOwNmS4AC-U1RR__BjiQHaDt?rs=1&pid=ImgDetMain", rating: 5 },
    { author: "Ahmed K.", text: "Kairouan est un joyau caché. La grande mosquée est à couper le souffle, un vrai voyage dans l'histoire.", image: "https://voyage-tunisie.info/wp-content/uploads/2018/03/Grande-Mosqu%C3%A9e-de-Kairouan-1024x683.jpg", rating: 4 },
    { author: "Laura P.", text: "Tozeur m'a transporté dans un autre monde avec ses décors de Star Wars et ses oasis.", image: "https://voyage-tunisie.info/wp-content/uploads/2018/03/D%C3%A9cor-Star-Wars-%C3%A0-Tozeur-1024x410.jpg", rating: 5 }
);

function loadReviews() {
    try {
        const reviewContainer = document.querySelector("#review-container");
        if (!reviewContainer) return;

        // Les avis sont maintenant chargés via PHP dans index.php
        // Cette fonction peut être utilisée pour des mises à jour dynamiques futures si nécessaire
    } catch (error) {
        console.error("Erreur dans loadReviews:", error);
    }
}

function integrateMathElements() {
    try {
        const vector = new Array(1, 0, 1);
        const multiplicationResult = 12 * 3;
        const equationResult = (1/2) * 3;

        const mathBox = document.createElement('div');
        mathBox.style.cssText = 'position:fixed; bottom:10px; left:10px; background:#f8f9fa; padding:10px; border-radius:5px; z-index:1000; box-shadow:0 0 10px rgba(0,0,0,0.1);';
        mathBox.innerHTML = `
            <h4 style="margin-top:0;">Contenu Mathématique</h4>
            <p>Vecteur: [${vector.join(', ')}]</p>
            <p>12 × 3 = ${multiplicationResult}</p>
            <p>x = ½ × 3 → x = ${equationResult}</p>
            <button onclick="this.parentElement.style.display='none'" style="cursor:pointer;">× Fermer</button>
        `;
        document.body.appendChild(mathBox);

        console.log("Éléments mathématiques intégrés:");
        console.table({
            "Vecteur": vector,
            "Multiplication": multiplicationResult,
            "Équation": equationResult
        });
    } catch (error) {
        console.error("Erreur dans integrateMathElements:", error);
    }
}

function setupMultimedia() {
    try {
        const video = document.querySelector('video');
        if (video) {
            video.controls = true;
            video.loop = true;
            video.muted = true;
            video.playsInline = true;

            const videoControl = document.createElement('button');
            videoControl.textContent = 'Pause';
            videoControl.className = 'video-control';
            videoControl.addEventListener('click', () => {
                if (video.paused) {
                    video.play();
                    videoControl.textContent = 'Pause';
                } else {
                    video.pause();
                    videoControl.textContent = 'Lecture';
                }
            });

            const videoContainer = document.querySelector('.video');
            if (videoContainer) {
                videoContainer.style.position = 'relative';
                videoControl.style.cssText = 'position:absolute; bottom:20px; right:20px; padding:5px 10px; background:rgba(0,0,0,0.7); color:white; border:none; border-radius:3px;';
                videoContainer.appendChild(videoControl);
            }
        }

        const audio = document.createElement('audio');
        audio.src = 'media/audio-guide.mp3';
        audio.controls = true;
        audio.style.display = 'none';
        document.body.appendChild(audio);
    } catch (error) {
        console.error("Erreur dans setupMultimedia:", error);
    }
}

function enhanceUI() {
    try {
        const hamburger = document.querySelector('.hamburger');
        const nav = document.querySelector('.main-nav');

        if (hamburger && nav) {
            hamburger.addEventListener('click', () => {
                nav.classList.toggle('active');
                hamburger.setAttribute('aria-expanded', nav.classList.contains('active'));
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    nav.classList.remove('active');
                }
            });
        }

        const scrollTopBtn = document.querySelector('.scroll-top');
        if (scrollTopBtn) {
            window.addEventListener('scroll', () => {
                scrollTopBtn.classList.toggle('show', window.scrollY > 300);
            });

            scrollTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        const yearElement = document.querySelector('.site-footer p:first-of-type');
        if (yearElement) {
            yearElement.textContent = `© ${new Date().getFullYear()} Your Guide. All rights reserved.`;
        }
    } catch (error) {
        console.error("Erreur dans enhanceUI:", error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadReviews();
    integrateMathElements();
    setupMultimedia();
    enhanceUI();

    console.log('Application initialisée avec succès');
});