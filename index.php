<?php
declare(strict_types=1);

$cssCandidates = [__DIR__ . '/valimo.css', __DIR__ . '/valimo(4).css'];
$cssFile = null;
foreach ($cssCandidates as $candidate) {
  if (is_file($candidate)) {
    $cssFile = $candidate;
    break;
  }
}

$css = $cssFile ? (string) file_get_contents($cssFile) : '';
$cssVersion = $cssFile ? (string) filemtime($cssFile) : (string) time();
$cssHref = $cssFile ? rawurlencode(basename($cssFile)) . '?v=' . rawurlencode($cssVersion) : 'valimo.css?v=' . rawurlencode($cssVersion);

function e(string $value): string
{
  return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function ruleVars(string $css, string $selector): array
{
  $vars = [];
  $pattern = '/' . preg_quote($selector, '/') . '\\s*\\{([^}]*)\\}/s';
  if (preg_match($pattern, $css, $rule)) {
    preg_match_all('/(--[a-zA-Z0-9-]+)\\s*:\\s*([^;]+);/', $rule[1], $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
      $vars[$match[1]] = trim($match[2]);
    }
  }
  return $vars;
}

function allClassNames(string $css): array
{
  preg_match_all('/\\.([a-zA-Z_][a-zA-Z0-9_-]*)/', preg_replace('/\\/\\*.*?\\*\\//s', '', $css) ?? $css, $matches);
  $classes = array_values(array_unique($matches[1] ?? []));
  sort($classes, SORT_NATURAL | SORT_FLAG_CASE);
  return $classes;
}

function tokenValue(array $vars, string $name): string
{
  return $vars[$name] ?? '—';
}

$rootVars = ruleVars($css, ':root');
$darkVars = ruleVars($css, '[data-theme="dark"]');
$lightVars = ruleVars($css, '[data-theme="light"]');
$accentNames = ['green', 'blue', 'red', 'gold', 'orange', 'purple', 'green-vivid', 'lime-purple'];
$accentVars = [];
foreach ($accentNames as $accentName) {
  $accentVars[$accentName] = ruleVars($css, '[data-accent="' . $accentName . '"]');
}
$allClasses = allClassNames($css);
$spacingSteps = [0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16];
$fontSamples = [
  ['Body', 'font-normal', '--font-body', 'The quick brown fox · Aa Bb Cc 0123'],
  ['Heading', 'font-heading', '--font-heading', 'The quick brown fox · Aa Bb Cc 0123'],
  ['Mono', 'font-mono', '--font-mono', 'The quick brown fox · Aa Bb Cc 0123'],
  ['Special', 'font-special', '--font-special', 'The quick brown fox · Aa Bb Cc 0123'],
  ['Code', 'font-code', '--font-code', 'The quick brown fox · Aa Bb Cc 0123'],
];
?>
<!doctype html>
<html lang="nl" data-theme="dark" data-accent="green" data-valimo-theme="valimo-default">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="dark light">
  <title>Valimo v4 · complete CSS showcase</title>
  <link rel="stylesheet" href="<?= e($cssHref) ?>">
</head>

<body>
  <main class="max-w-content p-6 flex flex-col gap-10">

    <header class="panel p-8 flex flex-col gap-6">
      <div class="flex flex-col gap-3">
        <span class="badge accent">Valimo v4 · complete showcase</span>
        <h1 class="text-2xl">De hele stylesheet in beeld</h1>
        <p class="text-lg text-muted m-0">Componenten, thema's, accenttokens, fonts, surfaces, tekststijlen, states en
          utilities — rechtstreeks uit de CSS.</p>
      </div>
      <?php if (!$cssFile): ?>
        <div class="alert danger"><strong>×</strong><span>Zet <span class="font-code">valimo.css</span> of <span
              class="font-code">valimo(4).css</span> naast dit PHP-bestand.</span></div>
      <?php else: ?>
        <div class="alert success"><strong>✓</strong><span>Geladen: <span
              class="font-code"><?= e(basename($cssFile)) ?></span> · cacheversie
            <?= e(date('Y-m-d H:i:s', (int) $cssVersion)) ?></span></div>
      <?php endif; ?>
      <div class="flex flex-wrap gap-2 items-center">
        <button class="btn primary" type="button" data-theme-button="dark">Dark</button>
        <button class="btn secondary" type="button" data-theme-button="light">Light</button>
        <span class="stat-pill"><span class="stat-label">Classes gevonden</span><span
            class="stat-value font-mono"><?= count($allClasses) ?></span></span>
        <span class="stat-pill"><span class="stat-label">Accentsets</span><span
            class="stat-value font-mono"><?= count($accentVars) ?></span></span>
      </div>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($accentNames as $accentName): ?>
          <button class="btn ghost" type="button"
            data-accent-button="<?= e($accentName) ?>"><?= e($accentName) ?></button>
        <?php endforeach; ?>
      </div>
    </header>

    <section class="flex flex-col gap-4">
      <div class="panel-header">
        <div><span class="text-xs text-soft font-mono">01 / THEME</span>
          <h2 class="text-xl">Theme tokens</h2>
        </div>
        <span class="signal accent">dark + light</span>
      </div>
      <div class="card p-5">
        <table>
          <thead>
            <tr>
              <th>Token</th>
              <th>Dark</th>
              <th>Light</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (['--bg', '--bg-alt', '--text', '--text-muted', '--text-soft', '--border', '--border-strong', '--surface-opacity-1', '--surface-opacity-2', '--surface-opacity-3', '--surface-opacity-4'] as $token): ?>
              <tr>
                <td class="font-code accent"><?= e($token) ?></td>
                <td class="font-mono"><?= e(tokenValue($darkVars, $token)) ?></td>
                <td class="font-mono"><?= e(tokenValue($lightVars, $token)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div class="panel-header">
        <div><span class="text-xs text-soft font-mono">02 / ACCENTS</span>
          <h2 class="text-xl">Alle accentkleuren &amp; accent-2</h2>
        </div>
        <span class="badge accent">8 sets</span>
      </div>
      <div class="alert info"><strong>i</strong><span><span class="font-code">--accent-2</span> wordt door Valimo
          gebruikt voor de zichtbare focus-outline. Klik een accent en tab vervolgens door de controls om hem in context
          te zien.</span></div>
      <div class="card p-5">
        <table>
          <thead>
            <tr>
              <th>Accent</th>
              <th>--accent</th>
              <th>--accent-2</th>
              <th>--accent-rgb</th>
              <th>--accent-ink</th>
              <th>--accent-line</th>
              <th>--accent-contrast</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($accentVars as $accentName => $vars): ?>
              <tr>
                <td><button class="btn ghost compact" type="button"
                    data-accent-button="<?= e($accentName) ?>"><?= e($accentName) ?></button></td>
                <?php foreach (['--accent', '--accent-2', '--accent-rgb', '--accent-ink', '--accent-line', '--accent-contrast'] as $token): ?>
                  <td class="font-mono text-xs"><?= e(tokenValue($vars, $token)) ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="card p-6 flex flex-wrap gap-3 items-center">
        <span class="accent font-bold">.accent</span>
        <span class="accent-bg p-3 radius-1">.accent-bg</span>
        <span class="badge accent">.badge.accent</span>
        <span class="signal accent">.signal.accent</span>
        <input class="w-auto" type="text" value="Focus mij: --accent-2" aria-label="Accent 2 focus demo">
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">03 / FONTS</span>
        <h2 class="text-xl">Alle vijf fontfamilies</h2>
      </div>
      <div class="card p-6 flex flex-col gap-5">
        <?php foreach ($fontSamples as [$label, $class, $token, $sample]): ?>
          <div class="flex flex-col gap-1">
            <div class="flex flex-wrap justify-between gap-2"><span class="text-xs text-soft font-mono"><?= e($label) ?> ·
                <?= e($token) ?></span><span
                class="text-xs text-muted font-code"><?= e(tokenValue($rootVars, $token)) ?></span></div>
            <div class="text-xl <?= e($class) ?>"><?= e($sample) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">04 / TYPE SCALE</span>
        <h2 class="text-xl">Tekstgroottes, gewicht &amp; alignment</h2>
      </div>
      <div class="card p-6 flex flex-col gap-3">
        <div class="text-2xl">.text-2xl · <?= e(tokenValue($rootVars, '--text-2xl')) ?></div>
        <div class="text-xl">.text-xl · <?= e(tokenValue($rootVars, '--text-xl')) ?></div>
        <div class="text-lg">.text-lg · <?= e(tokenValue($rootVars, '--text-lg')) ?></div>
        <div class="text-base">.text-base · <?= e(tokenValue($rootVars, '--text-base')) ?></div>
        <div class="text-sm">.text-sm · <?= e(tokenValue($rootVars, '--text-sm')) ?></div>
        <div class="text-xs">.text-xs · <?= e(tokenValue($rootVars, '--text-xs')) ?></div>
      </div>
      <div class="u-grid grid-cols-2 gap-3">
        <div class="card p-5 flex flex-col gap-2"><span class="font-normal">.font-normal · 400</span><span
            class="font-medium">.font-medium · 500</span><span class="font-semibold">.font-semibold · 600</span><span
            class="font-bold">.font-bold · 700</span></div>
        <div class="card p-5 flex flex-col gap-2"><span class="text-left">.text-left</span><span
            class="text-center">.text-center</span><span class="text-right">.text-right</span><span
            class="text-muted">.text-muted</span><span class="text-soft">.text-soft</span></div>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">05 / BUTTONS</span>
        <h2 class="text-xl">Knoppen, aliases &amp; states</h2>
      </div>
      <div class="card p-6 flex flex-col gap-4">
        <div class="flex flex-wrap gap-3"><button class="btn primary">.btn.primary</button><button
            class="btn secondary">.btn.secondary</button><button class="btn danger">.btn.danger</button><button
            class="btn ghost">.btn.ghost</button><button class="btn" disabled>button:disabled</button><button
            class="icon-btn" aria-label="Ster">★</button></div>
        <div class="flex flex-wrap gap-3"><button class="btn btn-primary">.btn-primary</button><button
            class="btn btn-secondary">.btn-secondary</button><button class="btn btn-danger">.btn-danger</button><button
            class="btn btn-ghost">.btn-ghost</button></div>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">06 / PANELS</span>
        <h2 class="text-xl">Panel, block, card &amp; stats</h2>
      </div>
      <div class="u-grid grid-cols-3 gap-3">
        <div class="panel p-5"><strong>.panel</strong>
          <p class="text-sm text-muted mb-0">Panel surface</p>
        </div>
        <div class="block p-5"><strong>.block</strong>
          <p class="text-sm text-muted mb-0">Block surface</p>
        </div>
        <div class="card p-5"><strong>.card</strong>
          <p class="text-sm text-muted mb-0">Card surface</p>
        </div>
      </div>
      <div class="stats-grid">
        <div class="stat-card"><span class="text-xs text-soft">Bezoekers</span>
          <div class="val">24.8K</div><span class="status-ok text-xs">+12.4%</span>
        </div>
        <div class="stat-card"><span class="text-xs text-soft">Conversie</span>
          <div class="val">8.42%</div><span class="status-success text-xs">+1.8%</span>
        </div>
        <div class="stat-card"><span class="text-xs text-soft">Bounces</span>
          <div class="val">31.2%</div><span class="status-bad text-xs">+2.1%</span>
        </div>
        <div class="stat-card"><span class="text-xs text-soft">Fouten</span>
          <div class="val">4</div><span class="status-danger text-xs">aandacht</span>
        </div>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">07 / FEEDBACK</span>
        <h2 class="text-xl">Alerts, badges, signals &amp; pills</h2>
      </div>
      <div class="flex flex-col gap-2">
        <div class="alert success"><strong>✓</strong><span>.alert.success</span></div>
        <div class="alert info"><strong>i</strong><span>.alert.info</span></div>
        <div class="alert warning"><strong>!</strong><span>.alert.warning</span></div>
        <div class="alert danger"><strong>×</strong><span>.alert.danger</span></div>
      </div>
      <div class="card p-5 flex flex-wrap gap-3 items-center"><span class="badge">.badge</span><span
          class="badge accent">.badge.accent</span><span class="signal">.signal</span><span
          class="signal accent">.signal.accent</span><span class="stat-pill"><span
            class="stat-label">.stat-label</span><span class="stat-value">.stat-value</span></span></div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">08 / FORMS</span>
        <h2 class="text-xl">Alle form controls &amp; focus</h2>
      </div>
      <form class="card p-6 flex flex-col gap-5" onsubmit="return false">
        <div class="field-group"><label for="demo-name">Input</label><input id="demo-name" type="text"
            value="Normale input">
          <p class="field-hint">.field-group · label · .field-hint</p>
        </div>
        <div class="field-group"><span class="field-label">.input op element</span>
          <div class="input" tabindex="0">Niet-input element met .input</div>
        </div>
        <div class="field-group"><label for="demo-select">Select</label><select id="demo-select">
            <option>Eerste optie</option>
            <option>Tweede optie</option>
          </select></div>
        <div class="field-group"><label for="demo-textarea">Textarea</label><textarea id="demo-textarea"
            rows="3">Textarea voorbeeld</textarea></div>
        <div><button class="btn primary">Submit button</button></div>
      </form>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">09 / NAVIGATION</span>
        <h2 class="text-xl">Alle nav-item varianten</h2>
      </div>
      <div class="card p-5 flex flex-wrap gap-2"><span class="nav-item inline">default inline</span><span
          class="nav-item inline active">active</span><span class="nav-item inline outline">outline</span><span
          class="nav-item inline outline active">outline active</span><span
          class="nav-item inline compact">compact</span><span class="nav-item inline compact active">compact
          active</span></div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">10 / TABLE</span>
        <h2 class="text-xl">Tabellen</h2>
      </div>
      <div class="card p-5">
        <table>
          <thead>
            <tr>
              <th>Project</th>
              <th>Status</th>
              <th>Eigenaar</th>
              <th>Score</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Northstar</td>
              <td class="status-success">Gezond</td>
              <td>Mira</td>
              <td class="font-mono">94</td>
            </tr>
            <tr>
              <td>Atlas</td>
              <td class="accent">Actief</td>
              <td>Noah</td>
              <td class="font-mono">87</td>
            </tr>
            <tr>
              <td>Echo</td>
              <td class="status-danger">Aandacht</td>
              <td>Sam</td>
              <td class="font-mono">61</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">11 / SURFACES</span>
        <h2 class="text-xl">Surface, border &amp; radius utilities</h2>
      </div>
      <div class="u-grid grid-cols-4 gap-3">
        <div class="surface-1 border p-5 radius-0">surface-1<br><span class="text-xs text-muted">radius-0</span></div>
        <div class="surface-2 border p-5 radius-1">surface-2<br><span class="text-xs text-muted">radius-1</span></div>
        <div class="surface-3 border-strong p-5 radius-2">surface-3<br><span class="text-xs text-muted">radius-2</span>
        </div>
        <div class="surface-4 border-strong p-5">surface-4<br><span class="text-xs text-muted">border-strong</span>
        </div>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">12 / CORE TOKENS</span>
        <h2 class="text-xl">Alle :root tokens</h2>
      </div>
      <div class="card p-5">
        <table>
          <thead>
            <tr>
              <th>Token</th>
              <th>Waarde uit CSS</th>
            </tr>
          </thead>
          <tbody><?php foreach ($rootVars as $token => $value): ?>
              <tr>
                <td class="font-code accent"><?= e($token) ?></td>
                <td class="font-mono text-sm"><?= e($value) ?></td>
              </tr><?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">13 / SPACING</span>
        <h2 class="text-xl">Spacing-schaal &amp; utilityfamilies</h2>
      </div>
      <div class="card p-5">
        <table>
          <thead>
            <tr>
              <th>Stap</th>
              <th>Token</th>
              <th>Waarde</th>
              <th>Utilityfamilies</th>
            </tr>
          </thead>
          <tbody><?php foreach ($spacingSteps as $step):
            $token = '--space-' . $step; ?>
              <tr>
                <td class="font-mono"><?= $step ?></td>
                <td class="font-code accent"><?= e($token) ?></td>
                <td class="font-mono"><?= e(tokenValue($rootVars, $token)) ?></td>
                <td class="text-xs font-code">m/mt/mr/mb/ml/mx/my · p/pt/pr/pb/pl/px/py · gap/gap-x/gap-y</td>
              </tr><?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="flex flex-col gap-3">
        <?php foreach ([1, 2, 3, 4, 6, 8, 12, 16] as $step): ?>
          <div class="surface-1 border p-2 flex items-center gap-3"><span
              class="font-code text-xs">p-<?= $step ?></span><span class="surface-3 border p-<?= $step ?>">padding
              <?= e(tokenValue($rootVars, '--space-' . $step)) ?></span></div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">14 / LAYOUT</span>
        <h2 class="text-xl">Flex, grid, sizing &amp; alignment</h2>
      </div>
      <div class="card p-5 flex flex-col gap-4">
        <div class="flex flex-wrap gap-2"><span class="badge">.flex</span><span class="badge">.inline-flex</span><span
            class="badge">.flex-col</span><span class="badge">.flex-row</span><span class="badge">.flex-wrap</span><span
            class="badge">.flex-nowrap</span><span class="badge">.flex-1</span><span
            class="badge">.flex-auto</span><span class="badge">.flex-none</span></div>
        <div class="flex flex-wrap gap-2"><span class="badge">items-center/start/end/stretch</span><span
            class="badge">justify-between/center/start/end</span><span class="badge">content-center</span><span
            class="badge">self-start/end/center</span></div>
        <div class="flex flex-wrap gap-2"><span class="badge">w-full / w-auto</span><span class="badge">h-full /
            h-auto</span><span class="badge">min-h-screen</span><span class="badge">max-w-content</span></div>
      </div>
      <div class="u-grid grid-cols-4 gap-2">
        <div class="surface-1 border p-4 text-center">grid 1</div>
        <div class="surface-2 border p-4 text-center">grid 2</div>
        <div class="surface-3 border p-4 text-center">grid 3</div>
        <div class="surface-4 border p-4 text-center">grid 4</div>
      </div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">15 / STATES</span>
        <h2 class="text-xl">Visibility &amp; state utilities</h2>
      </div>
      <div class="card p-5 flex flex-wrap gap-3"><span class="badge">.hidden → display:none</span><button
          class="btn is-disabled" type="button">.is-disabled</button><span class="status-ok">.status-ok</span><span
          class="status-success">.status-success</span><span class="status-bad">.status-bad</span><span
          class="status-danger">.status-danger</span></div>
    </section>

    <section class="flex flex-col gap-4">
      <div><span class="text-xs text-soft font-mono">16 / OVERLAYS</span>
        <h2 class="text-xl">Modal &amp; toast</h2>
      </div>
      <div class="card p-6 flex flex-wrap gap-3"><button class="btn primary" type="button" id="open-modal">Open
          modal</button><button class="btn secondary" type="button" id="show-toast">Toon toast</button></div>
    </section>

    <section class="flex flex-col gap-4">
      <div class="panel-header">
        <div><span class="text-xs text-soft font-mono">17 / INVENTORY</span>
          <h2 class="text-xl">Alle gevonden CSS-classes</h2>
        </div><span class="stat-pill"><span class="stat-label">Totaal</span><span
            class="stat-value"><?= count($allClasses) ?></span></span>
      </div>
      <div class="card p-5 flex flex-wrap gap-2"><?php foreach ($allClasses as $className): ?><span
            class="badge font-code">.<?= e($className) ?></span><?php endforeach; ?></div>
      <p class="field-hint">Deze lijst wordt bij iedere page load opnieuw uit de stylesheet gehaald. Nieuwe class in de
        CSS? Dan verschijnt hij hier automatisch.</p>
    </section>

    <footer class="border-strong py-8 flex flex-col gap-2">
      <span class="font-mono text-xs text-soft">VALIMO V4 · COMPLETE SHOWCASE</span>
      <span class="text-muted">Geen inline styles en geen extra stylesheet. PHP leest alleen de bestaande CSS uit en de
        browser gebruikt diezelfde CSS voor de volledige presentatie.</span>
    </footer>
  </main>

  <div class="modal-overlay hidden" id="demo-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal flex flex-col gap-5">
      <div class="panel-header">
        <h2 id="modal-title">.modal</h2><button class="icon-btn" type="button" id="close-modal"
          aria-label="Sluiten">×</button>
      </div>
      <p class="text-muted m-0">Deze overlay demonstreert <span class="font-code">.modal-overlay</span> en <span
          class="font-code">.modal</span>.</p>
      <div class="alert info">Backdrop, z-index, padding, border en radius komen uit Valimo.</div>
      <div class="flex justify-end gap-2"><button class="btn secondary" type="button"
          id="cancel-modal">Annuleren</button><button class="btn primary" type="button"
          id="confirm-modal">Doorgaan</button></div>
    </div>
  </div>
  <div id="toast-container" aria-live="polite"></div>

  <script>
    const root = document.documentElement;
    document.querySelectorAll('[data-theme-button]').forEach(button => button.addEventListener('click', () => {
      root.dataset.theme = button.dataset.themeButton;
    }));
    document.querySelectorAll('[data-accent-button]').forEach(button => button.addEventListener('click', () => {
      root.dataset.accent = button.dataset.accentButton;
    }));

    const modal = document.getElementById('demo-modal');
    const closeModal = () => modal.classList.add('hidden');
    document.getElementById('open-modal').addEventListener('click', () => modal.classList.remove('hidden'));
    ['close-modal', 'cancel-modal', 'confirm-modal'].forEach(id => document.getElementById(id).addEventListener('click', closeModal));
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });

    document.getElementById('show-toast').addEventListener('click', () => {
      const toast = document.createElement('div');
      toast.className = 'toast';
      toast.innerHTML = '<span class="status-success">●</span><span>.toast · Dit is de Valimo toast-component.</span>';
      document.getElementById('toast-container').appendChild(toast);
      window.setTimeout(() => toast.remove(), 3200);
    });
  </script>
</body>

</html>