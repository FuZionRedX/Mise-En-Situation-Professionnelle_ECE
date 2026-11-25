{{--
    Thème visuel partagé — inspiré de l'univers des serveurs Minecraft PvP français :
    fond quasi noir, cartes anthracite, accent orange en dégradé avec halo,
    typographie Montserrat très grasse, angles peu arrondis.

    À inclure juste APRÈS <script src="https://cdn.tailwindcss.com"></script> :
    les couleurs Tailwind existantes (gray, green, purple, blue) sont remappées ici,
    donc les classes déjà présentes dans les vues et dans le JS prennent la nouvelle charte.
--}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                },
                colors: {
                    // Neutres : noir profond → anthracite
                    gray: {
                        50:  '#f5f5f5',
                        100: '#ededed',
                        200: '#d6d6d6',
                        300: '#b8b8b8',
                        400: '#8c8c8c',
                        500: '#666666',
                        600: '#383838',
                        700: '#2a2a2a',
                        750: '#232323',
                        800: '#1c1c1c',
                        850: '#181818',
                        900: '#131313',
                        950: '#0c0c0c',
                    },
                    // Accent principal (anciennement vert) : orange
                    green: {
                        50:  '#fff4eb',
                        100: '#ffe4cc',
                        200: '#ffc799',
                        300: '#ffa866',
                        400: '#ff8c33',
                        500: '#ff7a1a',
                        600: '#ff6a00',
                        700: '#d95a00',
                        800: '#a34400',
                        900: '#4d2000',
                        950: '#2b1200',
                    },
                    // Accent secondaire (mobs, admin — anciennement violet) : or
                    purple: {
                        50:  '#fff9e6',
                        100: '#fff0bf',
                        200: '#ffe28a',
                        300: '#ffd25c',
                        400: '#ffc233',
                        500: '#f5ab0a',
                        600: '#e09400',
                        700: '#b37600',
                        800: '#7a5000',
                        900: '#3d2800',
                        950: '#241800',
                    },
                    // Actions secondaires (anciennement bleu) : boutons sombres à contour
                    blue: {
                        50:  '#f5f5f5',
                        100: '#e5e5e5',
                        200: '#cfcfcf',
                        300: '#d0d0d0',
                        400: '#a8a8a8',
                        500: '#424242',
                        600: '#303030',
                        700: '#2a2a2a',
                        800: '#222222',
                        900: '#1f1f1f',
                        950: '#141414',
                    },
                },
                borderRadius: {
                    sm: '2px',
                    DEFAULT: '4px',
                    md: '4px',
                    lg: '6px',
                    xl: '8px',
                    '2xl': '10px',
                },
            },
        },
    };
</script>
<style>
    :root {
        --pal-orange: #ff6a00;
        --pal-orange-light: #ff8c33;
        --pal-gold: #f5ab0a;
        --pal-bg: #131313;
        --pal-card: #1c1c1c;
        --pal-line: #2a2a2a;
    }

    html { color-scheme: dark; }

    body {
        font-family: 'Montserrat', ui-sans-serif, system-ui, sans-serif;
        background-color: var(--pal-bg);
        background-image:
            radial-gradient(ellipse 60% 40% at 50% -10%, rgba(255, 106, 0, 0.10), transparent 70%);
        background-attachment: fixed;
        -webkit-font-smoothing: antialiased;
    }

    ::selection { background: var(--pal-orange); color: #fff; }

    /* Titres : Montserrat extra-gras au lieu de la police monospace */
    .minecraft-font {
        font-family: 'Montserrat', ui-sans-serif, system-ui, sans-serif !important;
        font-weight: 800 !important;
        letter-spacing: -0.01em;
    }
    h1, h2, h3 { letter-spacing: -0.01em; }
    /* Titres de section en blanc ; l'orange est réservé aux sur-titres et aux actions */
    h2.minecraft-font { color: #fff !important; }
    h1 { font-weight: 800; }

    /* Petit sur-titre orange en majuscules */
    .pal-eyebrow {
        color: var(--pal-orange);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    /* En-tête */
    header.sticky {
        background: rgba(19, 19, 19, 0.85) !important;
        border-bottom-color: #222 !important;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    /* Logo : cube orange en dégradé */
    .pal-mark {
        width: 44px; height: 44px;
        flex-shrink: 0;
        filter: drop-shadow(0 0 14px rgba(255, 106, 0, 0.45));
    }

    /* Boutons principaux : dégradé orange + halo */
    [class*="from-green-6"],
    a.bg-green-600, button.bg-green-600 {
        background-image: linear-gradient(180deg, #ff8a1f 0%, #ff6a00 100%) !important;
        box-shadow: 0 0 22px rgba(255, 106, 0, 0.40), inset 0 1px 0 rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 160, 80, 0.55);
        color: #fff !important;
    }
    [class*="from-green-6"]:hover,
    a.bg-green-600:hover, button.bg-green-600:hover {
        box-shadow: 0 0 34px rgba(255, 106, 0, 0.65), inset 0 1px 0 rgba(255, 255, 255, 0.22);
        filter: brightness(1.06);
    }

    /* Boutons dorés (mobs / admin) */
    [class*="from-purple-6"],
    a.bg-purple-700, button.bg-purple-600 {
        background-image: linear-gradient(180deg, #ffbf33 0%, #e09400 100%) !important;
        box-shadow: 0 0 20px rgba(245, 171, 10, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 210, 110, 0.5);
        color: #1a1200 !important;
    }
    [class*="from-purple-6"]:hover,
    a.bg-purple-700:hover, button.bg-purple-600:hover {
        box-shadow: 0 0 32px rgba(245, 171, 10, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.25);
    }

    /* Boutons secondaires : fond sombre + contour fin, comme « En savoir plus » */
    a.bg-gray-700, button.bg-gray-700,
    a.bg-blue-600, button.bg-blue-600 {
        background-color: #1a1a1a !important;
        border: 1px solid #3d3d3d;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
        color: #f2f2f2 !important;
    }
    a.bg-gray-700:hover, button.bg-gray-700:hover,
    a.bg-blue-600:hover, button.bg-blue-600:hover {
        background-color: #232323 !important;
        border-color: #5a5a5a;
    }

    /* Bouton danger plus sobre */
    a.bg-red-600, button.bg-red-600 {
        box-shadow: 0 0 18px rgba(220, 38, 38, 0.25);
    }

    /* Supprime les effets de zoom au survol, peu présents dans la charte */
    [class*="hover:scale-105"]:hover { transform: translateY(-1px) !important; }

    /* Cartes : bordure qui passe à l'orange avec un halo, comme l'étape mise en avant */
    .card-hover { transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease; }
    .card-hover:hover {
        transform: translateY(-3px);
        border-color: rgba(255, 106, 0, 0.7) !important;
        box-shadow: 0 0 0 1px rgba(255, 106, 0, 0.35), 0 0 40px rgba(255, 106, 0, 0.18), 0 14px 30px rgba(0, 0, 0, 0.5);
    }

    /* Champs de formulaire */
    input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):not([type="file"]),
    select, textarea {
        background-color: #161616 !important;
        border-color: #2f2f2f !important;
        color: #f2f2f2;
        transition: border-color .2s ease, box-shadow .2s ease;
    }
    input:not([type="checkbox"]):not([type="radio"]):not([type="range"]):not([type="color"]):not([type="file"]):focus,
    select:focus, textarea:focus {
        outline: none !important;
        border-color: var(--pal-orange) !important;
        box-shadow: 0 0 0 1px var(--pal-orange), 0 0 16px rgba(255, 106, 0, 0.25) !important;
        transform: none !important;
    }
    input::placeholder, textarea::placeholder { color: #5c5c5c; }
    input[type="checkbox"], input[type="radio"], input[type="range"], progress { accent-color: var(--pal-orange); }

    /* Barre de défilement */
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: #101010; }
    ::-webkit-scrollbar-thumb { background: #2e2e2e; border-radius: 6px; border: 2px solid #101010; }
    ::-webkit-scrollbar-thumb:hover { background: var(--pal-orange); }
    * { scrollbar-color: #2e2e2e #101010; }

    /* Pied de page */
    .pal-footer { border-top: 1px solid #1f1f1f; }
</style>
