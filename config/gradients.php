<?php

declare(strict_types=1);

/**
 * Curated app gradient themes.
 *
 * 'css' is the full background value applied to the app background.
 * Every entry includes the base #0D1117 fallback so the value is self-contained.
 * Low-stimulation mode ignores all gradients and renders #0D1117 only.
 *
 * Supporter-only gradients are marked with 'supporter_only' => true.
 * The canonical list of supporter keys lives in config/supporter.php gradient_packs.
 *
 * Environmental lighting approach — each gradient models a real light source:
 *   1. Primary source (0.42–0.52) — the dominant light: lamp, fire, screen, snow
 *   2. Atmospheric fill (0.18–0.28) — how that light diffuses through the space
 *   3. Secondary source or reflection (0.20–0.32) — bounce light, second window, etc.
 *   4. Depth zone / edge shadow (0.18–0.32) — where the light does NOT reach
 *   5. Environmental accent (0.12–0.20) — fog, mist, scatter, ceiling
 *   6. #0D1117 base — dominant dark always shows through
 *
 * Free gradients use slightly lower peak opacity (~0.40–0.44) to maintain
 * hierarchy with supporter gradients (0.44–0.52).
 */
return [

    // ── Free gradients (available to all users) ──────────────────────────────

    'forest_night' => [
        'label' => 'Forest Night',
        // Light source: ground-level bioluminescence from forest floor (moss, roots)
        // Canopy: dense leaves block all sky — absolute dark overhead
        // Mist: diffuse soft haze drifts between trees at mid-height
        // Sides: tree trunks form dark vertical shadows at both edges
        'css'   => 'radial-gradient(ellipse 110% 52% at 50% 108%, rgba(10,55,22,0.44) 0%, rgba(6,38,15,0.22) 55%, transparent 72%), radial-gradient(ellipse 90% 45% at 50% 52%, rgba(10,35,16,0.18) 0%, transparent 68%), radial-gradient(ellipse 95% 52% at 50% 0%, rgba(4,10,18,0.34) 0%, transparent 62%), radial-gradient(ellipse 25% 80% at 0% 55%, rgba(5,22,8,0.24) 0%, transparent 52%), radial-gradient(ellipse 25% 80% at 100% 55%, rgba(5,22,8,0.20) 0%, transparent 52%), #0D1117',
    ],

    'rainy_blue' => [
        'label' => 'Rainy Blue',
        // Light source: rain-diffused daylight through a window, upper-left
        // The window creates a directional cool-blue wash across the room
        // Base warmth: indoor air at floor level, slightly warmer than outside cold
        // Right wall: away from the window, darker and less saturated
        'css'   => 'radial-gradient(ellipse 80% 62% at 6% 3%, rgba(10,35,88,0.44) 0%, rgba(6,22,65,0.22) 55%, transparent 74%), radial-gradient(ellipse 90% 55% at 55% 50%, rgba(8,22,55,0.18) 0%, transparent 70%), radial-gradient(ellipse 65% 38% at 50% 108%, rgba(16,12,10,0.22) 0%, transparent 55%), radial-gradient(ellipse 35% 65% at 100% 52%, rgba(4,10,28,0.24) 0%, transparent 52%), #0D1117',
    ],

    'warm_slate' => [
        'label' => 'Warm Slate',
        // Light source: hearth fire on the floor — amber rising from below-center
        // Stone walls absorb and softly scatter the warmth throughout the room
        // Ceiling: cold slate stone — no warmth reaches up there
        // Shadow corner: where the warm light fades into cool stone shadow
        'css'   => 'radial-gradient(ellipse 80% 55% at 50% 108%, rgba(85,62,24,0.44) 0%, rgba(62,44,15,0.22) 55%, transparent 72%), radial-gradient(ellipse 90% 62% at 50% 75%, rgba(55,38,16,0.22) 0%, transparent 68%), radial-gradient(ellipse 80% 48% at 50% 0%, rgba(22,22,20,0.30) 0%, transparent 60%), radial-gradient(ellipse 28% 55% at 2% 45%, rgba(18,14,10,0.20) 0%, transparent 50%), #0D1117',
    ],

    'soft_green' => [
        'label' => 'Soft Green',
        // Light source: overcast sky filtering through a canopy — diffuse, directionless
        // Ground: moss and undergrowth hold and reflect soft green ambient
        // Leaf filter: light enters from upper-right, slightly brighter there
        // Shadow edge: deeper shade on the left where sun never reaches
        'css'   => 'radial-gradient(ellipse 90% 55% at 50% 108%, rgba(14,58,26,0.42) 0%, rgba(10,42,18,0.20) 55%, transparent 72%), radial-gradient(ellipse 75% 55% at 50% 50%, rgba(10,35,16,0.16) 0%, transparent 68%), radial-gradient(ellipse 65% 52% at 72% 5%, rgba(8,42,18,0.26) 0%, transparent 60%), radial-gradient(ellipse 30% 72% at 0% 52%, rgba(6,22,10,0.20) 0%, transparent 52%), #0D1117',
    ],

    'midnight' => [
        'label' => 'Midnight',
        // Light source: moon partially behind clouds — diffuse cold indigo from upper-right
        // Sky mass: deep indigo pressing down, dominating the upper two-thirds
        // Ground: cold and dark, reflecting nothing
        // Far corner: absolute dark where no moonlight reaches at all
        'css'   => 'radial-gradient(ellipse 90% 68% at 50% 0%, rgba(7,15,58,0.50) 0%, rgba(5,10,42,0.26) 58%, transparent 82%), radial-gradient(ellipse 42% 40% at 65% 16%, rgba(12,20,68,0.26) 0%, transparent 55%), radial-gradient(ellipse 85% 42% at 50% 108%, rgba(5,8,18,0.28) 0%, transparent 58%), radial-gradient(ellipse 28% 62% at 2% 32%, rgba(4,6,20,0.22) 0%, transparent 50%), #0D1117',
    ],

    // ── Supporter gradients ───────────────────────────────────────────────────
    // Each models a specific environment through its lighting behaviour.

    'night_window' => [
        'label'          => 'Night Window',
        'supporter_only' => true,
        // Looking out a window at night. Streetlamp glows amber through the glass from below.
        // Night sky is deep blue above. Window frame creates dark side pillars.
        // Warm scatter in the middle pane where lamplight diffuses through glass.
        'css'   => 'radial-gradient(ellipse 60% 45% at 50% 112%, rgba(118,72,15,0.48) 0%, rgba(88,50,8,0.22) 58%, transparent 78%), radial-gradient(ellipse 100% 62% at 50% 0%, rgba(5,12,58,0.50) 0%, rgba(4,9,44,0.24) 62%, transparent 82%), radial-gradient(ellipse 16% 88% at 0% 50%, rgba(3,6,20,0.34) 0%, transparent 52%), radial-gradient(ellipse 16% 88% at 100% 50%, rgba(3,6,20,0.34) 0%, transparent 52%), radial-gradient(ellipse 48% 42% at 50% 58%, rgba(82,48,8,0.14) 0%, transparent 62%), #0D1117',
    ],

    'rainy_crt' => [
        'label'          => 'Rainy CRT',
        'supporter_only' => true,
        // A CRT monitor glowing phosphor-green in a dark room. Rain falls outside.
        // Screen face is the primary source — concentrated oval at center-upper.
        // Screen corona: softer green halo around the face.
        // Desk reflection: green bounces off the surface below the monitor.
        // Rain window: cold blue seeps in from far lower-left corner.
        'css'   => 'radial-gradient(ellipse 48% 42% at 50% 38%, rgba(0,92,40,0.52) 0%, rgba(0,65,28,0.28) 48%, transparent 72%), radial-gradient(ellipse 78% 62% at 50% 38%, rgba(0,52,22,0.20) 0%, transparent 72%), radial-gradient(ellipse 55% 28% at 50% 84%, rgba(0,45,18,0.24) 0%, transparent 58%), radial-gradient(ellipse 32% 52% at 2% 78%, rgba(5,16,42,0.28) 0%, transparent 52%), #0D1117',
    ],

    'lantern_glow' => [
        'label'          => 'Lantern Glow',
        'supporter_only' => true,
        // A single lantern hung at center-upper. Warm amber sphere of light in deep darkness.
        // Core: tight concentrated source — this is the flame itself.
        // Atmospheric spread: the warm air immediately around the lantern.
        // Floor warmth: light falls and pools gently below.
        // Velvety edges: beyond the lantern's reach — absolute dark.
        'css'   => 'radial-gradient(ellipse 36% 42% at 50% 28%, rgba(188,108,18,0.56) 0%, rgba(148,75,10,0.30) 45%, transparent 70%), radial-gradient(ellipse 68% 60% at 50% 42%, rgba(105,55,8,0.30) 0%, transparent 70%), radial-gradient(ellipse 85% 48% at 50% 102%, rgba(78,35,5,0.26) 0%, transparent 58%), radial-gradient(ellipse 25% 82% at 0% 50%, rgba(2,1,3,0.20) 0%, transparent 52%), radial-gradient(ellipse 25% 82% at 100% 50%, rgba(2,1,3,0.20) 0%, transparent 52%), #0D1117',
    ],

    'pixel_night' => [
        'label'          => 'Pixel Night',
        'supporter_only' => true,
        // Gaming late at night. Screen glow rises from below like light off a keyboard.
        // Primary: violet-indigo rising from the floor — the monitor's ambient spill.
        // Room atmosphere: the screen lights the whole dark room a deep purple.
        // Ceiling: dark — no light source reaches up there.
        // Navy corners: where the room wall meets the far distance from the screen.
        'css'   => 'radial-gradient(ellipse 72% 58% at 50% 104%, rgba(74,22,122,0.52) 0%, rgba(52,15,95,0.28) 52%, transparent 72%), radial-gradient(ellipse 90% 62% at 50% 78%, rgba(42,12,80,0.28) 0%, transparent 70%), radial-gradient(ellipse 85% 45% at 50% 0%, rgba(8,10,42,0.32) 0%, transparent 58%), radial-gradient(ellipse 35% 42% at 4% 12%, rgba(8,12,50,0.24) 0%, transparent 50%), radial-gradient(ellipse 35% 42% at 96% 12%, rgba(8,12,50,0.24) 0%, transparent 50%), #0D1117',
    ],

    'foggy_forest' => [
        'label'          => 'Foggy Forest',
        'supporter_only' => true,
        // Dense morning fog in a forest. Light is completely diffuse — no direct source.
        // Ground: deep forest floor absorbs everything, darker and more saturated.
        // Primary fog: wide gray-green diffuse layer at mid-height — this is the fog itself.
        // Sky above treeline: cooler, slightly lighter blue-gray where fog thins.
        // Forest edge: dark vertical mass of trees at both sides.
        // Depth fog: a second fainter layer adds visual layering.
        'css'   => 'radial-gradient(ellipse 110% 52% at 50% 108%, rgba(18,42,22,0.40) 0%, transparent 62%), radial-gradient(ellipse 120% 55% at 50% 52%, rgba(28,52,32,0.34) 0%, rgba(22,44,28,0.14) 65%, transparent 82%), radial-gradient(ellipse 85% 48% at 50% 0%, rgba(24,38,50,0.38) 0%, rgba(18,30,42,0.18) 58%, transparent 78%), radial-gradient(ellipse 28% 78% at 0% 52%, rgba(8,18,10,0.30) 0%, transparent 52%), radial-gradient(ellipse 28% 78% at 100% 52%, rgba(8,18,10,0.26) 0%, transparent 52%), radial-gradient(ellipse 75% 42% at 50% 38%, rgba(30,56,34,0.16) 0%, transparent 60%), #0D1117',
    ],

    'coffee_shop' => [
        'label'          => 'Coffee Shop',
        'supporter_only' => true,
        // Small table near a window. Warm lamp hangs above-left — your table is lit.
        // Lamp source: concentrated amber pool, upper-left — this is where the light hangs.
        // Table surface: the lamp's light falls and pools below it.
        // Room ambient: general coffee shop warmth diffuses through the whole space.
        // Cold window: night outside, upper-right — blue seeps through the glass.
        // Far dark corner: lower-right, furthest from both lamp and window.
        'css'   => 'radial-gradient(ellipse 52% 58% at 18% 18%, rgba(128,65,8,0.52) 0%, rgba(95,45,5,0.26) 52%, transparent 74%), radial-gradient(ellipse 60% 40% at 22% 80%, rgba(92,42,5,0.32) 0%, transparent 60%), radial-gradient(ellipse 85% 65% at 35% 55%, rgba(68,28,4,0.22) 0%, transparent 72%), radial-gradient(ellipse 48% 52% at 90% 6%, rgba(5,10,35,0.42) 0%, rgba(3,7,26,0.20) 58%, transparent 78%), radial-gradient(ellipse 38% 38% at 90% 88%, rgba(2,2,5,0.20) 0%, transparent 52%), #0D1117',
    ],

    'aquarium_glow' => [
        'label'          => 'Aquarium Glow',
        'supporter_only' => true,
        // Standing next to a large lit aquarium. Tank LEDs glow blue-green from below.
        // Tank floor: main light source — teal rising up from the bottom.
        // Left wall: tank edge spills light sideways onto you.
        // Right wall: matching spill, slightly softer.
        // Water surface shimmer: a cooler brighter horizontal band at water level.
        // Room ceiling: far from the tank light — very dark.
        'css'   => 'radial-gradient(ellipse 75% 55% at 50% 112%, rgba(0,120,115,0.50) 0%, rgba(0,85,92,0.26) 55%, transparent 72%), radial-gradient(ellipse 38% 68% at 6% 55%, rgba(0,95,105,0.34) 0%, transparent 58%), radial-gradient(ellipse 38% 68% at 94% 55%, rgba(0,95,105,0.26) 0%, transparent 55%), radial-gradient(ellipse 80% 20% at 50% 52%, rgba(0,108,118,0.18) 0%, transparent 55%), radial-gradient(ellipse 90% 42% at 50% 0%, rgba(2,5,12,0.32) 0%, transparent 55%), #0D1117',
    ],

    'cassette_evening' => [
        'label'          => 'Cassette Evening',
        'supporter_only' => true,
        // 1990s bedroom. Warm lamp and tape deck glow rose-mauve from the lower-left.
        // Deck source: concentrated warm-pink glow — the tape deck LEDs and lamp.
        // Room diffuse: mauve warmth spreading through the bedroom space.
        // Night window: cold blue-gray from upper-right — outside it's dark and late.
        // Floor: darker below and to the right, away from the lamp.
        // Upper-left shadow: ceiling above the lamp is in deep shadow.
        'css'   => 'radial-gradient(ellipse 55% 55% at 12% 90%, rgba(128,28,75,0.52) 0%, rgba(98,18,58,0.26) 52%, transparent 74%), radial-gradient(ellipse 75% 62% at 28% 68%, rgba(82,18,52,0.28) 0%, transparent 70%), radial-gradient(ellipse 50% 52% at 92% 5%, rgba(5,8,40,0.44) 0%, rgba(3,6,30,0.20) 58%, transparent 78%), radial-gradient(ellipse 60% 35% at 52% 102%, rgba(45,8,28,0.24) 0%, transparent 55%), radial-gradient(ellipse 32% 38% at 4% 4%, rgba(3,2,8,0.20) 0%, transparent 50%), #0D1117',
    ],

    'snow_quiet' => [
        'label'          => 'Snow Quiet',
        'supporter_only' => true,
        // Snow falling outside. Reflected moonlight on snow creates a wide cool glow through the window.
        // Snow glow: wide diffuse blue-white from the top — the window facing the snowfall.
        // Window center: slightly brighter where the glass pane is.
        // Room cold: the cold seeps in — blue ambient fills the whole space below.
        // Indoor warmth: very faint amber at bottom corners — a lamp or heat source far away.
        'css'   => 'radial-gradient(ellipse 85% 58% at 50% 0%, rgba(58,88,148,0.50) 0%, rgba(42,65,118,0.24) 58%, transparent 78%), radial-gradient(ellipse 52% 42% at 50% 0%, rgba(72,100,158,0.24) 0%, transparent 55%), radial-gradient(ellipse 100% 62% at 50% 45%, rgba(30,46,92,0.18) 0%, transparent 72%), radial-gradient(ellipse 42% 38% at 4% 100%, rgba(36,26,16,0.24) 0%, transparent 52%), radial-gradient(ellipse 42% 38% at 96% 100%, rgba(36,26,16,0.24) 0%, transparent 52%), #0D1117',
    ],

    'observatory' => [
        'label'          => 'Observatory',
        'supporter_only' => true,
        // Inside a dome observatory at night. The dome is open to deep space above.
        // Deep space: absolute indigo pressing down — the primary character of the space.
        // Star field: a faint slightly lighter zone where stars cluster in the field of view.
        // Telescope focal point: very small, subtle bloom — what the scope is aimed at.
        // Cold stone floor: dark below, the stone base of the observatory.
        // Dome rim: faint dark ring where the dome structure meets the opening.
        'css'   => 'radial-gradient(ellipse 88% 75% at 50% 5%, rgba(4,7,40,0.56) 0%, rgba(3,5,30,0.30) 62%, transparent 82%), radial-gradient(ellipse 35% 35% at 50% 22%, rgba(15,22,82,0.30) 0%, transparent 52%), radial-gradient(ellipse 16% 16% at 50% 20%, rgba(20,28,95,0.22) 0%, transparent 40%), radial-gradient(ellipse 100% 45% at 50% 108%, rgba(8,12,25,0.30) 0%, transparent 60%), radial-gradient(ellipse 100% 18% at 50% 35%, rgba(2,3,14,0.16) 0%, transparent 50%), #0D1117',
    ],

    'forest_cabin' => [
        'label'          => 'Forest Cabin',
        'supporter_only' => true,
        // Inside a cabin deep in the forest. Hearth fire glows from the floor below-center.
        // Fire source: intense concentrated orange-amber from floor level — hot and bright.
        // Fire floor: warmth spreading sideways across the cabin floor.
        // Cabin ambient: general warm glow from fire diffusing through the interior.
        // Forest left: cold forest visible through left window — dark and green outside.
        // Forest right: matching cold forest through right window.
        // Forest ceiling: dark treetops through a skylight — cold and very dark.
        'css'   => 'radial-gradient(ellipse 58% 55% at 50% 92%, rgba(162,78,10,0.56) 0%, rgba(122,52,6,0.28) 52%, transparent 72%), radial-gradient(ellipse 82% 38% at 50% 102%, rgba(95,40,4,0.34) 0%, transparent 60%), radial-gradient(ellipse 72% 58% at 50% 72%, rgba(68,28,4,0.22) 0%, transparent 68%), radial-gradient(ellipse 46% 78% at 2% 38%, rgba(5,25,8,0.42) 0%, transparent 58%), radial-gradient(ellipse 46% 78% at 98% 38%, rgba(5,25,8,0.42) 0%, transparent 58%), radial-gradient(ellipse 80% 42% at 50% 0%, rgba(3,14,5,0.26) 0%, transparent 55%), #0D1117',
    ],

    'deep_ocean' => [
        'label'          => 'Deep Ocean',
        'supporter_only' => true,
        // Deep underwater. Bioluminescent organisms glow from the seabed.
        // Primary source: teal-cyan rising from below-right — main seabed creature cluster.
        // Secondary source: another bioluminescent cluster, lower-left — gives asymmetry.
        // Water column: dark cold blue in the middle — the water itself absorbs light.
        // Abyss above: absolute black-blue — no sunlight at this depth.
        // Far side glow: a third faint source off to the left — eerie depth.
        'css'   => 'radial-gradient(ellipse 68% 55% at 78% 112%, rgba(0,118,108,0.50) 0%, rgba(0,82,88,0.24) 55%, transparent 72%), radial-gradient(ellipse 48% 45% at 16% 88%, rgba(0,80,90,0.36) 0%, transparent 58%), radial-gradient(ellipse 85% 58% at 50% 50%, rgba(0,10,26,0.22) 0%, transparent 70%), radial-gradient(ellipse 100% 48% at 50% 0%, rgba(0,5,15,0.42) 0%, transparent 62%), radial-gradient(ellipse 20% 32% at 2% 52%, rgba(0,68,82,0.24) 0%, transparent 50%), #0D1117',
    ],

    'quiet_library' => [
        'label'          => 'Quiet Library',
        'supporter_only' => true,
        // A private reading room. One lamp in the upper-left casts warm sepia-gold light.
        // Lamp source: concentrated warm gold — the lamp itself, tight and bright.
        // Desk pool: the lamp's light falls on the reading surface, pools below and left.
        // Room warm ambient: sepia spreads through the whole space, fades toward corners.
        // Bookshelf right: rows of books in shadow — the far side of the room.
        // Dark floor corner: lower-right, furthest from the lamp, almost black.
        'css'   => 'radial-gradient(ellipse 52% 60% at 14% 8%, rgba(135,70,12,0.54) 0%, rgba(100,50,6,0.28) 52%, transparent 74%), radial-gradient(ellipse 55% 42% at 28% 50%, rgba(92,44,6,0.30) 0%, transparent 62%), radial-gradient(ellipse 85% 68% at 30% 38%, rgba(65,28,5,0.22) 0%, transparent 72%), radial-gradient(ellipse 46% 75% at 94% 58%, rgba(3,3,5,0.22) 0%, transparent 55%), radial-gradient(ellipse 58% 40% at 80% 100%, rgba(2,2,3,0.20) 0%, transparent 52%), #0D1117',
    ],

    'campfire_dusk' => [
        'label'          => 'Campfire Dusk',
        'supporter_only' => true,
        // Around a campfire as the sky turns from dusk to night. Fire is below-center.
        // Fire core: intense orange-red from the ground — the flame itself, hot and saturated.
        // Fire ground: warmth spreading sideways across the earth around the fire.
        // Fire air: the orange ambient that warms the air immediately above the flames.
        // Dusk sky: blue-purple pressing down — the sky transitioning to full dark.
        // Horizon band: where fire glow meets the cooling dusk sky.
        'css'   => 'radial-gradient(ellipse 60% 52% at 50% 104%, rgba(188,68,6,0.56) 0%, rgba(148,44,4,0.28) 52%, transparent 72%), radial-gradient(ellipse 88% 38% at 50% 102%, rgba(118,30,3,0.34) 0%, transparent 60%), radial-gradient(ellipse 62% 45% at 50% 70%, rgba(88,22,2,0.22) 0%, transparent 62%), radial-gradient(ellipse 90% 65% at 50% 0%, rgba(20,10,58,0.48) 0%, rgba(14,7,44,0.22) 58%, transparent 80%), radial-gradient(ellipse 85% 18% at 50% 44%, rgba(58,18,3,0.16) 0%, transparent 52%), #0D1117',
    ],

    'stormwatch' => [
        'label'          => 'Stormwatch',
        'supporter_only' => true,
        // Watching a storm from a sheltered position. Heavy cloud mass dominates.
        // Primary cloud: main storm mass bearing down from upper-left — the heaviest clouds.
        // Secondary cloud: overlapping layer from upper-right — cloud depth and volume.
        // Cloud core: deep dark zone at the storm's center, where it's most intense.
        // Rain sheets: falling rain darkens the left and right edges.
        // Shelter warmth: the faintest hint of dry warmth at the base — where you stand.
        'css'   => 'radial-gradient(ellipse 88% 60% at 22% 3%, rgba(15,32,75,0.52) 0%, rgba(10,22,58,0.26) 62%, transparent 82%), radial-gradient(ellipse 70% 52% at 80% 5%, rgba(12,28,68,0.42) 0%, transparent 65%), radial-gradient(ellipse 55% 42% at 50% 12%, rgba(7,14,46,0.30) 0%, transparent 58%), radial-gradient(ellipse 25% 72% at 2% 65%, rgba(5,10,30,0.32) 0%, transparent 55%), radial-gradient(ellipse 25% 72% at 98% 65%, rgba(5,10,30,0.32) 0%, transparent 55%), radial-gradient(ellipse 75% 35% at 50% 108%, rgba(22,18,14,0.24) 0%, transparent 55%), #0D1117',
    ],

];
