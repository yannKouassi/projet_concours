

//c'est ici j'ai tué lié chaque section au button radio là
function openSection(radioId, sectionSelector) {
    // 1. Cocher le bouton radio
    document.getElementById(radioId).checked = true;

    // 2. Masquer toutes les sections
    document.querySelectorAll('.main').forEach(section => {
        section.style.display = 'none';
        section.classList.remove('active');
    });

    // 3. Afficher la bonne section
    const targetSection = document.querySelector(sectionSelector);
    if (targetSection) {
        targetSection.style.display = 'block';
        targetSection.classList.add('active');
        targetSection.scrollIntoView({behavior: 'smooth'});
    }
}
document.addEventListener('DOMContentLoaded', function () {
//bon ici là, j'ai fais xa pour la navigation pour transformer mes button en lien
    document.getElementById('login').addEventListener('click', () => {
        window.location.href = 'login.php';
    });
    document.getElementById('login1').addEventListener('click', () => {
        window.location.href = 'login.php';
    });
    document.getElementById('login2').addEventListener('click', () => {
        window.location.href = 'login.php';
    });
    document.getElementById('tab6').addEventListener('click', () => {
        window.location.href = 'login.php';
    });

    document.getElementById('register').addEventListener('click', () => {
        window.location.href = 'register.php';
    });
    document.getElementById('tab7').addEventListener('click', () => {
        window.location.href = 'register.php';
    });
    document.getElementById('register1').addEventListener('click', () => {
        window.location.href = 'register.php';
    });


//bon coté ça là c'est pour rendre menu responsive genre quand on clic le menu apparait buurger façon là
    var button = document.querySelector('.button');
    var pageMenu = document.querySelector('.pageMenu');
    var register_login = document.querySelector('.register_login');

    button.onclick = function toggleMenu() {
        button.classList.toggle('active')
        pageMenu.classList.toggle('active')
        register_login.classList.toggle('hide');
    }


    document.querySelectorAll('.card').forEach(svg => {
        svg.classList.add('reveal');
    });

    document.querySelectorAll('p').forEach(svg => {
        svg.classList.add('reveal');
    });


// fou c'est facile j'ai fais de tel sorte qu'on ai un effet de chargement au scroll

    const reveals = document.querySelectorAll('.reveal');

    function handleReveal() {
        reveals.forEach((el) => {
            const windowHeight = window.innerHeight;
            const elementTop = el.getBoundingClientRect().top;
            const revealPoint = 100;

            if (elementTop < windowHeight - revealPoint) {
                el.classList.add('active');
            } else {
                el.classList.remove('active');
            }
        });
    }

    window.addEventListener('scroll', handleReveal);
    window.addEventListener('load', handleReveal);




//c'est ici là meme que j'ai tué c'est une seule page donc je mets tout dans le main et je les cahe comme ça quand je clic sur a propos ou accueil ça apparait
    document.querySelectorAll('.tab_label').forEach(label => {
        label.addEventListener('click', () => {
            const targetSelector = label.getAttribute('data-target');
            if (!targetSelector) return;

            // Cacher toutes les sections
            document.querySelectorAll('.main').forEach(section => {
                section.classList.remove('active');
                section.style.display = 'none';
            });

            // Afficher la section ciblée
            const targetSection = document.querySelector(targetSelector);
            if (targetSection) {
                targetSection.classList.add('active');
                targetSection.style.display = 'block';

                // Scroll en douceur vers la section affichée
                targetSection.scrollIntoView({behavior: 'smooth'});
            }
        });
    });

    // Au chargement, afficher la section par défaut
    window.addEventListener('DOMContentLoaded', () => {
        const defaultSection = document.querySelector('.tab_label[for="tab1"]').getAttribute('data-target');
        const section = document.querySelector(defaultSection);
        if (section) {
            section.classList.add('active');
            section.style.display = 'block';
        }
    });

})

