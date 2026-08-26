# Valimo

![Valimo-logo](valimo-logo.png)

Valimo is een open-source CSS-framework voor moderne webapplicaties. Het combineert designtokens, thema's, herbruikbare componenten en utilityklassen in één stylesheet, zonder verplichte JavaScript-runtime.

De huidige stylesheet is **Valimo v4.1.1**. Valimo bevat een dark en light theme, acht accentsets en aparte CSS-lagen voor core, themes, components, utilities en projects.

## Snel starten

Plaats `valimo.css` in je project en laad het in de `<head>`:

```html
<!doctype html>
<html lang="nl" data-theme="dark" data-accent="green">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    <link rel="stylesheet" href="valimo.css">
    <title>Mijn Valimo-project</title>
  </head>
  <body>
    <main class="max-w-content p-6">
      <section class="panel p-6 flex flex-col gap-4">
        <span class="badge accent">Valimo</span>
        <h1 class="text-2xl">Welkom</h1>
        <p class="text-muted">Een pagina gebouwd met Valimo.</p>
        <div><button class="btn primary">Aan de slag</button></div>
      </section>
    </main>
  </body>
</html>
```

`valimo.css` importeert Google Fonts en Material Symbols via `fonts.googleapis.com`. Voor die webfonts is tijdens het laden een internetverbinding nodig.

## Themes en accenten

Stel beide in met data-attributen op het `<html>`-element:

```html
<html data-theme="light" data-accent="blue">
```

Beschikbare themes zijn `dark` en `light`. De accentsets zijn:

- `green`
- `blue`
- `red`
- `gold`
- `orange`
- `purple`
- `green-vivid`
- `lime-purple`

De waarden kunnen ook tijdens runtime worden aangepast:

```js
document.documentElement.dataset.theme = 'light';
document.documentElement.dataset.accent = 'lime-purple';
```

## Wat zit erin?

- Tokens voor kleur, typografie, spacing, borders, radius en surfaces
- Knoppen, panels, cards, formulieren, navigatie, badges en statussen
- Alerts, tabellen, statistiekblokken, modals en toasts
- Layoututilities voor flex, grid, sizing, alignment en visibility
- Spacingutilities voor margin, padding en gap
- Zichtbare focusstijlen die de actieve accentset volgen

Een compact componentvoorbeeld:

```html
<div class="card p-5 flex flex-col gap-3">
  <div class="panel-header">
    <h2 class="text-xl">Projectstatus</h2>
    <span class="signal accent">Actief</span>
  </div>
  <div class="alert success">
    <strong>OK</strong>
    <span>Alle controles zijn geslaagd.</span>
  </div>
</div>
```

## CSS-lagen en uitbreiden

Valimo legt deze cascadevolgorde vast:

```css
@layer valimo.core,
       valimo.theme,
       valimo.components,
       valimo.utilities,
       valimo.project,
       valimo.project.components,
       valimo.project.utilities;
```

Plaats projectspecifieke regels in een aparte stylesheet en laad die **na** `valimo.css`. Gebruik bij voorkeur de beschikbare projectlagen, zodat de core herbruikbaar blijft.

```css
@layer valimo.project {
  .project-header {
    border-bottom: 1px solid var(--border);
  }
}
```

`valimo-project-confetti.css` is een lokaal voorbeeld van zo'n projectlaag en is geen vereiste voor Valimo zelf.

## Showcase lokaal openen

`index.php` is de interactieve showcase. Het bestand leest `valimo.css`, toont tokens en gevonden klassen en demonstreert themes, accenten, componenten, states en utilities.

```bash
php -S localhost:8000
```

Open daarna [http://localhost:8000](http://localhost:8000). PHP moet lokaal beschikbaar zijn. `valimo-preview.html` en `valimo-showcase.html` zijn statische voorbeelden die ook rechtstreeks in een browser geopend kunnen worden.

## Belangrijkste bestanden

| Bestand | Rol |
| --- | --- |
| `valimo.css` | Volledige stylesheet en primaire distributie |
| `index.php` | Dynamische showcase en CSS-inspectie |
| `handleiding.html` | Uitgebreide lokale handleiding |
| `valimo-preview.html` | Compacte statische preview |
| `valimo-showcase.html` | Statische componentenshowcase |
| `valimo-project-confetti.css` | Voorbeeld van een projectlaag |
| `valimo-logo.png` | Logo in deze README |

## Bronnen voor deze README

Deze README is uitsluitend samengesteld op basis van bestanden in de hoofdmap:

- **Hoofdbronnen:** `valimo.css` en `index.php`
- **Ondersteunende bronnen:** `handleiding.html`, `valimo-preview.html`, `valimo-showcase.html` en `valimo-project-confetti.css`
- **Visuele bron:** `valimo-logo.png`

`Valimo Engine v4 Handleiding.pdf` is niet gebruikt als inhoudelijke bron.
