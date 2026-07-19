<nav id="gears-toc" class="gears-toc" aria-label="Table of contents">
<strong class="gears-toc-title">On this page</strong>
<ol class="gears-toc-list">
<li><a href="#gear-types-snapshot">Gear types at a glance</a></li>
<li><a href="#gears-calculator">Online gear calculator</a></li>
<li><a href="#what-gears-do">What gears do</a></li>
<li><a href="#core-equations">Core equations</a></li>
<li><a href="#transfer-ratio">Transfer ratio</a></li>
<li><a href="#spur-gear">Spur (straight)</a></li>
<li><a href="#helical-gear">Helical</a></li>
<li><a href="#bevel-gear">Straight bevel</a></li>
<li><a href="#miter-gear">Miter</a></li>
<li><a href="#spiral-bevel">Spiral bevel</a></li>
<li><a href="#hypoid-gear">Hypoid</a></li>
<li><a href="#screw-gear">Screw gear</a></li>
<li><a href="#worm-gear">Worm</a></li>
<li><a href="#worm-wheel">Worm wheel</a></li>
<li><a href="#rack-pinion">Rack &amp; pinion</a></li>
<li><a href="#internal-gear">Internal gear</a></li>
<li><a href="#planetary-gear">Planetary</a></li>
<li><a href="#gear-coupling">Gear coupling</a></li>
<li><a href="#pawl-ratchet">Pawl &amp; ratchet</a></li>
<li><a href="#cam-drive">Cam drive</a></li>
<li><a href="#herringbone-gear">Herringbone</a></li>
<li><a href="#angles">Transmission angles</a></li>
<li><a href="#cheat-sheet">Cheat sheet</a></li>
<li><a href="#example-setups">Example setups</a></li>
<li><a href="#sources">Sources</a></li>
</ol>
</nav>

<div id="gear-types-snapshot" class="gears-snapshot">
<strong class="gears-snapshot-title">Gear types at a glance</strong>
<p class="gears-snapshot-lead">Quick visual index. Click a card to jump to that type.</p>
<div class="gears-snapshot-grid">
<a class="gears-snapshot-card" href="#spur-gear"><img src="/blog/gears/gear-spur.jpg" alt="Spur gear" width="640" height="640" loading="lazy" decoding="async"><span>Spur</span></a>
<a class="gears-snapshot-card" href="#helical-gear"><img src="/blog/gears/gear-helical.jpg" alt="Helical gear" width="640" height="426" loading="lazy" decoding="async"><span>Helical</span></a>
<a class="gears-snapshot-card" href="#bevel-gear"><img src="/blog/gears/gear-bevel.jpg" alt="Straight bevel gear" width="640" height="640" loading="lazy" decoding="async"><span>Straight bevel</span></a>
<a class="gears-snapshot-card" href="#miter-gear"><img src="/blog/gears/gear-miter.jpg" alt="Miter gear" width="640" height="640" loading="lazy" decoding="async"><span>Miter</span></a>
<a class="gears-snapshot-card" href="#spiral-bevel"><img src="/blog/gears/gear-spiral-bevel.jpg" alt="Spiral bevel gear" width="640" height="640" loading="lazy" decoding="async"><span>Spiral bevel</span></a>
<a class="gears-snapshot-card" href="#hypoid-gear"><img src="/blog/gears/gear-hypoid.jpg" alt="Hypoid gear" width="640" height="426" loading="lazy" decoding="async"><span>Hypoid</span></a>
<a class="gears-snapshot-card" href="#screw-gear"><img src="/blog/gears/gear-screw.jpg" alt="Screw gear" width="640" height="426" loading="lazy" decoding="async"><span>Screw gear</span></a>
<a class="gears-snapshot-card" href="#worm-gear"><img src="/blog/gears/gear-worm.jpg" alt="Worm gear" width="640" height="640" loading="lazy" decoding="async"><span>Worm</span></a>
<a class="gears-snapshot-card" href="#worm-wheel"><img src="/blog/gears/gear-worm-wheel.jpg" alt="Worm wheel" width="640" height="640" loading="lazy" decoding="async"><span>Worm wheel</span></a>
<a class="gears-snapshot-card" href="#rack-pinion"><img src="/blog/gears/gear-rack.jpg" alt="Rack and pinion" width="640" height="640" loading="lazy" decoding="async"><span>Rack &amp; pinion</span></a>
<a class="gears-snapshot-card" href="#internal-gear"><img src="/blog/gears/gear-internal.jpg" alt="Internal gear" width="640" height="640" loading="lazy" decoding="async"><span>Internal</span></a>
<a class="gears-snapshot-card" href="#planetary-gear"><img src="/blog/gears/gear-planetary.jpg" alt="Planetary gear" width="640" height="426" loading="lazy" decoding="async"><span>Planetary</span></a>
<a class="gears-snapshot-card" href="#gear-coupling"><img src="/blog/gears/gear-coupling.jpg" alt="Gear coupling" width="640" height="426" loading="lazy" decoding="async"><span>Gear coupling</span></a>
<a class="gears-snapshot-card" href="#pawl-ratchet"><img src="/blog/gears/gear-pawl-ratchet.jpg" alt="Pawl and ratchet" width="640" height="640" loading="lazy" decoding="async"><span>Pawl &amp; ratchet</span></a>
<a class="gears-snapshot-card" href="#cam-drive"><img src="/blog/gears/gear-cam.jpg" alt="Cam drive" width="640" height="426" loading="lazy" decoding="async"><span>Cam</span></a>
<a class="gears-snapshot-card" href="#herringbone-gear"><img src="/blog/gears/gear-herringbone.jpg" alt="Herringbone gear" width="640" height="426" loading="lazy" decoding="async"><span>Herringbone</span></a>
</div>
</div>

<div id="gears-calculator" class="gears-lab">
<div class="gears-lab-head">
<h3>Online gear calculator</h3>
<p>Move the sliders or type numbers. Diameters, ratio, speed, and torque update at once.</p>
</div>
<div class="gears-lab-body" id="gears-calc-app">
<div class="gears-row">
<div class="gears-field">
<label for="gear-type">Gear type</label>
<select id="gear-type" data-gear-type>
<option value="spur">Spur (straight)</option>
<option value="helical">Helical</option>
<option value="bevel">Straight bevel</option>
<option value="miter">Miter</option>
<option value="spiral-bevel">Spiral bevel</option>
<option value="hypoid">Hypoid</option>
<option value="screw">Screw (crossed helical)</option>
<option value="worm">Worm &amp; wheel</option>
<option value="rack">Rack &amp; pinion</option>
<option value="internal">Internal</option>
<option value="planetary">Planetary</option>
<option value="coupling">Gear coupling</option>
<option value="pawl">Pawl &amp; ratchet</option>
<option value="cam">Cam</option>
<option value="herringbone">Herringbone</option>
</select>
</div>
</div>
<div class="gears-row">
<div class="gears-field">
<label for="gear-z1">Pinion teeth Z₁</label>
<div class="gears-field-inputs">
<input data-z1 type="range" min="8" max="120" step="1" value="18">
<input id="gear-z1" data-z1 type="number" min="8" max="120" step="1" value="18">
</div>
</div>
<div class="gears-field">
<label for="gear-z2">Gear / wheel teeth Z₂</label>
<div class="gears-field-inputs">
<input data-z2 type="range" min="8" max="200" step="1" value="36">
<input id="gear-z2" data-z2 type="number" min="8" max="200" step="1" value="36">
</div>
</div>
<div class="gears-field">
<label for="gear-module">Module m (mm)</label>
<div class="gears-field-inputs">
<input data-module type="range" min="0.5" max="20" step="0.1" value="2">
<input id="gear-module" data-module type="number" min="0.5" max="20" step="0.1" value="2">
</div>
</div>
<div class="gears-field">
<label for="gear-phi">Pressure angle φ (°)</label>
<div class="gears-field-inputs">
<input data-phi type="range" min="14" max="30" step="0.5" value="20">
<input id="gear-phi" data-phi type="number" min="14" max="30" step="0.5" value="20">
</div>
</div>
</div>
<div class="gears-row">
<div class="gears-field">
<label for="gear-nin">Input speed n₁ (rpm)</label>
<div class="gears-field-inputs">
<input data-nin type="range" min="1" max="20000" step="1" value="1500">
<input id="gear-nin" data-nin type="number" min="1" max="20000" step="1" value="1500">
</div>
</div>
<div class="gears-field">
<label for="gear-tin">Input torque τ₁ (N·m)</label>
<div class="gears-field-inputs">
<input data-tin type="range" min="0.1" max="5000" step="0.1" value="10">
<input id="gear-tin" data-tin type="number" min="0.1" max="5000" step="0.1" value="10">
</div>
</div>
<div class="gears-field" data-show-for="bevel,spiral-bevel,hypoid">
<label for="gear-shaft">Shaft angle Σ (°)</label>
<div class="gears-field-inputs">
<input data-shaft type="range" min="10" max="170" step="1" value="90">
<input id="gear-shaft" data-shaft type="number" min="10" max="170" step="1" value="90">
</div>
</div>
<div class="gears-field" data-show-for="worm">
<label for="gear-starts">Worm starts</label>
<div class="gears-field-inputs">
<input data-worm-starts type="range" min="1" max="4" step="1" value="1">
<input id="gear-starts" data-worm-starts type="number" min="1" max="4" step="1" value="1">
</div>
</div>
</div>
<div class="gears-actions">
<button type="button" class="gears-btn gears-btn-primary" data-reset>Reset calculator</button>
</div>
<div class="gears-help">
<h4>What each control means</h4>
<p><strong>Gear type</strong> picks the mesh family and which angle rules apply.</p>
<p><strong>Z₁ / Z₂</strong> set tooth counts. Ratio usually follows Z₂/Z₁.</p>
<p><strong>Module m</strong> sets tooth size in mm. Bigger m → bigger diameters.</p>
<p><strong>Pressure angle φ</strong> tilts the line of action (often 20°).</p>
<p><strong>n₁ / τ₁</strong> are input speed and torque for the ideal output trade.</p>
<p><strong>Σ / starts</strong> appear for bevel shaft angle and worm thread starts.</p>
</div>
<div class="gears-results">
<div class="gears-stat"><span class="label">Ratio i</span><span class="value" data-out-i>—</span></div>
<div class="gears-stat"><span class="label">d₁</span><span class="value" data-out-d1>—</span></div>
<div class="gears-stat"><span class="label">d₂</span><span class="value" data-out-d2>—</span></div>
<div class="gears-stat"><span class="label">Center a</span><span class="value" data-out-a>—</span></div>
<div class="gears-stat"><span class="label">n₂</span><span class="value" data-out-nout>—</span></div>
<div class="gears-stat"><span class="label">τ₂ / F</span><span class="value" data-out-tout>—</span></div>
<div class="gears-stat"><span class="label">Circular p</span><span class="value" data-out-p>—</span></div>
<div class="gears-stat"><span class="label">Diam. pitch</span><span class="value" data-out-pd>—</span></div>
</div>
<div class="gears-eq-box">
<span class="label">Active relations</span>
<code data-eq-out>…</code>
<p class="gears-warn" style="margin-top:.45rem;" data-type-hint></p>
</div>
<div class="gears-plot">
<h4>Ideal speed vs torque trade</h4>
<canvas data-ratio-canvas width="520" height="240" aria-label="Graph of speed ratio versus torque ratio"></canvas>
<p>X = ratio i. Teal = n₂/n₁. Orange = τ₂/τ₁. Marker = your current i.</p>
</div>
<p class="gears-warn">Educational calculator. Ignores friction, deflection, AGMA strength checks, and lubrication.</p>
</div>
</div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>

<p>Shop floors still argue about gears the same way textbooks do.</p>
<p>How many teeth? How big is the module? What angle do the shafts make?</p>
<p>Change one number and the diameters jump. The ratio moves with them.</p>
<p>This page pairs clear 3D stills with a live online calculator.</p>
<hr class="section-break">

<h2 id="what-gears-do">What gears do</h2>
<p>Gears pass motion and torque through meshing teeth.</p>
<p>They keep a nearly constant speed ratio when tooth profiles are conjugate.</p>
<p>That constant ratio is why involute spur teeth show up everywhere.</p>
<p>Pick the type first: parallel shafts, intersecting shafts, or crossed shafts.</p>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="core-equations">Core equations (metric module system)</h2>
<p>Pitch diameter: <code>d = m · Z</code></p>
<p>Circular pitch: <code>p = π · m</code></p>
<p>Diametral pitch (inch habit): <code>P_d ≈ 25.4 / m</code></p>
<p>Center distance (external spur pair): <code>a = (d₁ + d₂) / 2</code></p>
<p>Base diameter: <code>d_b = d · cos φ</code></p>
<p>Outside diameter (full-depth approx.): <code>d_a ≈ d + 2m</code></p>
<p>Module must match on mating gears. Teeth will not mesh otherwise.</p>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="transfer-ratio">Transfer ratio (gear ratio)</h2>
<p>For an external gear pair:</p>
<p><code>i = Z₂ / Z₁ = n₁ / n₂ = d₂ / d₁</code></p>
<p>Ideal power balance (no loss): <code>τ₂ / τ₁ ≈ i</code> and <code>n₂ = n₁ / i</code>.</p>
<p>Raise i to slow the output and grow torque. Lower i for speed.</p>
<p>You cannot win both speed and torque from the same power budget.</p>
<p>Use the calculator graph: teal falls as orange rises when i increases.</p>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="spur-gear">Spur gear (straight teeth)</h2>
<p>Parallel shafts. Teeth cut straight across the face.</p>
<p>Simple, cheap, and noisier at high speed than helical.</p>
<p>Ratio and center distance follow the spur formulas above.</p>
<div class="gears-photo"><img src="/blog/gears/gear-spur.jpg" alt="Silver spur gear pair meshing" width="800" height="800" loading="lazy" decoding="async"><p>Spur pair — straight teeth, parallel shafts</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="helical-gear">Helical gear</h2>
<p>Teeth are slanted. Contact starts gradually. Quieter mesh.</p>
<p>Ratio still tracks Z₂/Z₁ when modules match.</p>
<p>Helix angle adds axial thrust — bearings must take that load.</p>
<div class="gears-photo"><img src="/blog/gears/gear-helical.jpg" alt="Silver helical gears meshing" width="640" height="426" loading="lazy" decoding="async"><p>Helical pair — slanted teeth for smoother contact</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="bevel-gear">Straight bevel gear</h2>
<p>Shafts intersect. Pitch surfaces are cones, not cylinders.</p>
<p>Common case: Σ = 90° (right-angle drive).</p>
<p>Pitch cone angles (approx.):</p>
<p><code>tan γ₁ = (Z₁/Z₂)·sinΣ / (1 + (Z₁/Z₂)·cosΣ)</code></p>
<p><code>γ₂ = Σ − γ₁</code></p>
<div class="gears-photo"><img src="/blog/gears/gear-bevel.jpg" alt="Silver straight bevel gear pair at 90 degrees" width="800" height="800" loading="lazy" decoding="async"><p>Straight bevel — intersecting shafts, conical pitch surfaces</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="miter-gear">Miter gear</h2>
<p>A special bevel pair with equal teeth. Usually Σ = 90°.</p>
<p>Transfer ratio i = 1. Same speed, direction change only.</p>
<p>Use the calculator type “Miter” to lock that 1:1 case.</p>
<div class="gears-photo"><img src="/blog/gears/gear-miter.jpg" alt="Equal silver miter bevel gears" width="800" height="800" loading="lazy" decoding="async"><p>Miter gears — equal size, 90° turn, i = 1</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="spiral-bevel">Spiral bevel gear</h2>
<p>Same cone layout as straight bevel, but teeth curve on the face.</p>
<p>Smoother, quieter mesh. Used in many vehicle differentials.</p>
<p>Cone angle math still starts from Σ, Z₁, and Z₂.</p>
<div class="gears-photo"><img src="/blog/gears/gear-spiral-bevel.jpg" alt="Spiral bevel gears with curved teeth" width="640" height="640" loading="lazy" decoding="async"><p>Spiral bevel — curved teeth on pitch cones</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="hypoid-gear">Hypoid gear</h2>
<p>Like spiral bevel, but pinion axis is offset from the ring center.</p>
<p>Shafts do not intersect. Common in rear axles and differentials.</p>
<p>Offset lets the pinion sit lower and run quieter under load.</p>
<div class="gears-photo"><img src="/blog/gears/gear-hypoid.jpg" alt="Hypoid ring gear and offset pinion" width="640" height="426" loading="lazy" decoding="async"><p>Hypoid — offset pinion, non-intersecting shafts</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="screw-gear">Screw gear (crossed helical)</h2>
<p>Two helical gears on skew (non-intersecting, non-parallel) shafts.</p>
<p>Also called screw gears. Point contact; lower load than spur pairs.</p>
<p>Useful for light drives and odd shaft layouts.</p>
<div class="gears-photo"><img src="/blog/gears/gear-screw.jpg" alt="Crossed helical screw gears without long axles" width="640" height="426" loading="lazy" decoding="async"><p>Screw gears — crossed helicals on skew axes</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="worm-gear">Worm</h2>
<p>A screw-like driver on a shaft. Usually crossed at 90° to the wheel.</p>
<p>Approx. ratio: <code>i ≈ Z_wheel / number of worm starts</code></p>
<p>Single-start worms self-lock more easily. Multi-start worms are faster.</p>
<div class="gears-photo"><img src="/blog/gears/gear-worm.jpg" alt="Silver worm meshing with bronze worm wheel" width="800" height="800" loading="lazy" decoding="async"><p>Worm drive — high ratio in one compact stage</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="worm-wheel">Worm wheel</h2>
<p>The mating gear for a worm. Often bronze for sliding wear.</p>
<p>Throat is concave so more teeth hug the worm thread.</p>
<p>Efficiency can be low. Heat and oil matter in real boxes.</p>
<div class="gears-photo"><img src="/blog/gears/gear-worm-wheel.jpg" alt="Bronze-gold worm wheel gear" width="800" height="800" loading="lazy" decoding="async"><p>Worm wheel — throated gear for worm mesh</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="rack-pinion">Rack &amp; pinion</h2>
<p>A rack is a gear with infinite radius — a straight tooth row.</p>
<p>Rotation becomes linear travel along the pitch line.</p>
<p><code>v = ω · (d/2)</code> with <code>d = m·Z</code> for the pinion.</p>
<div class="gears-photo"><img src="/blog/gears/gear-rack.jpg" alt="Silver rack and pinion" width="800" height="800" loading="lazy" decoding="async"><p>Rack &amp; pinion — rotary to linear motion</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="internal-gear">Internal (annular) gear</h2>
<p>Teeth cut inside a ring. Pinion sits inside.</p>
<p>Both rotate the same way. Center distance shrinks:</p>
<p><code>a = |d₂ − d₁| / 2</code></p>
<div class="gears-photo"><img src="/blog/gears/gear-internal.jpg" alt="Internal ring gear with pinion" width="640" height="640" loading="lazy" decoding="async"><p>Internal gear — compact annular mesh</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="planetary-gear">Planetary gear (epicyclic)</h2>
<p>Sun in the center, planets on a carrier, ring gear outside.</p>
<p>Ratio depends on which member is fixed, input, or output.</p>
<p>Compact high ratio. Used in automatics, hubs, and reducers.</p>
<p>Simple case (ring fixed, sun in, carrier out): <code>i ≈ 1 + Z_ring/Z_sun</code></p>
<div class="gears-photo"><img src="/blog/gears/gear-planetary.jpg" alt="Planetary gear set with sun planets and ring" width="640" height="426" loading="lazy" decoding="async"><p>Planetary — sun, planets, and annulus ring</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="gear-coupling">Gear coupling</h2>
<p>Two hubs with external teeth inside a floating sleeve.</p>
<p>Passes torque while allowing small misalignment.</p>
<p>Not a speed reducer — treat i ≈ 1 in the calculator.</p>
<div class="gears-photo"><img src="/blog/gears/gear-coupling.jpg" alt="Gear coupling hubs and sleeve" width="800" height="800" loading="lazy" decoding="async"><p>Gear coupling — torque across a flexible tooth sleeve</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="pawl-ratchet">Pawl &amp; ratchet</h2>
<p>A toothed wheel plus a pawl that catches one way.</p>
<p>Used for winches, jacks, and one-way step feeds.</p>
<p>Motion is stepwise, not a continuous gear ratio.</p>
<div class="gears-photo"><img src="/blog/gears/gear-pawl-ratchet.jpg" alt="Pawl and ratchet wheel mechanism" width="800" height="800" loading="lazy" decoding="async"><p>Pawl &amp; ratchet — one-way incremental drive</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="cam-drive">Cam drive</h2>
<p>A shaped disk or plate. A follower tracks the profile.</p>
<p>There is no fixed tooth ratio i. Stroke follows s(θ).</p>
<p>Use cams when you need a custom motion program.</p>
<div class="gears-photo"><img src="/blog/gears/gear-cam.jpg" alt="Cam disk with roller follower" width="800" height="800" loading="lazy" decoding="async"><p>Cam &amp; follower — programmed displacement vs angle</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="herringbone-gear">Herringbone gear (double helical)</h2>
<p>Two opposite helixes meet in a V or chevron pattern on one blank.</p>
<p>Axial thrusts cancel. Quiet mesh without net end thrust.</p>
<p>Harder to cut than single helical. Ratio still follows Z₂/Z₁.</p>
<div class="gears-photo"><img src="/blog/gears/gear-herringbone.jpg" alt="Silver herringbone gears with V-shaped chevron teeth" width="640" height="426" loading="lazy" decoding="async"><p>Herringbone — V-shaped teeth, opposing helixes</p></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="angles">Transmission angles that matter</h2>
<div class="table-card"><h3 class="table-title">Angles by gear family</h3><div class="table-wrap"><table><thead><tr><th>Type</th><th>Key angle</th><th>What it sets</th></tr></thead><tbody><tr><td>Spur / helical / herringbone</td><td>Pressure angle φ</td><td>Line of action tilt</td></tr><tr><td>Helical / screw</td><td>Helix angle β</td><td>Thrust &amp; shaft skew</td></tr><tr><td>Bevel / miter / spiral / hypoid</td><td>Shaft Σ, cone γ, offset</td><td>How shafts meet</td></tr><tr><td>Planetary</td><td>Which member fixed</td><td>Which ratio path</td></tr><tr><td>Worm</td><td>Lead / lead angle</td><td>Advance per turn</td></tr><tr><td>Cam</td><td>Cam angle θ</td><td>Follower schedule</td></tr><tr><td>Ratchet</td><td>Tooth / pawl angle</td><td>Lock direction</td></tr></tbody></table></div></div>
<p>Parallel shafts → spur or helical. Intersecting → bevel family. Crossed → worm or screw.</p>
<p>Wrong family for the shaft layout is a layout error, not a module tweak.</p>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="cheat-sheet">Side-by-side cheat sheet</h2>
<div class="table-card"><h3 class="table-title">Quick compare</h3><div class="table-wrap"><table><thead><tr><th>Type</th><th>Shafts</th><th>Typical i</th><th>Watch-outs</th></tr></thead><tbody><tr><td>Spur</td><td>Parallel</td><td>Low–medium</td><td>Noise at speed</td></tr><tr><td>Helical</td><td>Parallel</td><td>Low–medium</td><td>Axial thrust</td></tr><tr><td>Bevel / spiral</td><td>Intersecting</td><td>Low–medium</td><td>Cone setup</td></tr><tr><td>Hypoid</td><td>Offset / skew</td><td>Low–medium</td><td>Sliding, lube</td></tr><tr><td>Miter</td><td>Intersecting</td><td>1:1</td><td>Equal teeth</td></tr><tr><td>Screw</td><td>Skew</td><td>Low</td><td>Point contact</td></tr><tr><td>Worm</td><td>Crossed</td><td>High</td><td>Heat, friction</td></tr><tr><td>Rack</td><td>Rotary→linear</td><td>—</td><td>Backlash</td></tr><tr><td>Internal</td><td>Parallel</td><td>Medium</td><td>Same-way spin</td></tr><tr><td>Planetary</td><td>Coaxial train</td><td>Medium–high</td><td>Which member fixed</td></tr><tr><td>Coupling</td><td>Near-coaxial</td><td>~1</td><td>Misalignment only</td></tr><tr><td>Pawl / ratchet</td><td>One-way</td><td>Step</td><td>Impact loads</td></tr><tr><td>Cam</td><td>Custom motion</td><td>n/a</td><td>Profile wear</td></tr><tr><td>Herringbone</td><td>Parallel</td><td>Low–medium</td><td>Harder to cut</td></tr></tbody></table></div></div>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="example-setups">Example setups</h2>
<ul>
<li>Spur: Z₁=18, Z₂=36, m=2. Then d₁=36 mm, d₂=72 mm, a=54 mm, i=2.</li>
<li>Slide Z₂ upward. Watch n₂ fall and τ₂ rise on the readouts.</li>
<li>Switch to Bevel. Set Σ=90°, then 60°. Cone angles move.</li>
<li>Miter: confirm i stays 1 with equal teeth.</li>
<li>Worm: starts=1 vs 4 with same Z₂. Ratio changes a lot.</li>
<li>Rack: ignore n₂. Read force-style output from torque.</li>
<li>Hypoid: set Σ near 90°. Offset is the layout difference from spiral bevel.</li>
<li>Planetary: treat Z₁ as sun, Z₂ as ring for a rough ring-fixed ratio.</li>
<li>Herringbone: same ratio math as helical; thrust cancels.</li>
<li>Coupling / pawl / cam: note that i is not a normal reducer.</li>
</ul>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="sources">Sources (.edu / .gov)</h2>
<ul>
<li><a href="https://mae3.eng.ucsd.edu/machine-design/gear-ratios" rel="noopener noreferrer">UC San Diego MAE 3 — Gear ratios</a></li>
<li><a href="https://isl.charlotte.edu/gear-nomenclature/" rel="noopener noreferrer">UNC Charlotte — Gear nomenclature</a></li>
<li><a href="https://home.engineering.iastate.edu/~gkstarns/me325/gears_1.pdf" rel="noopener noreferrer">Iowa State — Gear geometry notes (PDF)</a></li>
<li><a href="https://community.wvu.edu/~bpbettig/MAE342/Lecture_3_spur_gears_b.pdf" rel="noopener noreferrer">West Virginia University — Idealized spur gears (PDF)</a></li>
<li><a href="https://www.nist.gov/pml" rel="noopener noreferrer">NIST Physical Measurement Laboratory</a></li>
</ul>
<p class="table-note"><em>Teaching demo only. Not a substitute for AGMA design or a full machine-design course.</em></p>
<p>— <strong>Rajiv Nair</strong><br>mechanism notes, rewritten for the bench</p>
<p class="gears-back"><a href="#gears-toc">↑ Top / Index</a></p>
