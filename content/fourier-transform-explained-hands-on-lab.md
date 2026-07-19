<nav id="fourier-toc" class="fourier-toc" aria-label="Table of contents">
<strong class="fourier-toc-title">On this page</strong>
<ol class="fourier-toc-list">
<li><a href="#fourier-lab">Fourier series calculator</a></li>
<li><a href="#what-is-ft">What is a Fourier transform?</a></li>
<li><a href="#series-vs-transform">Series vs transform</a></li>
<li><a href="#read-graphs">How to read the graphs</a></li>
<li><a href="#time-vs-freq">Time ↔ frequency</a></li>
<li><a href="#built-series">Built series line</a></li>
<li><a href="#custom-coefficients">Custom coefficients</a></li>
<li><a href="#general-series">General Fourier series</a></li>
<li><a href="#sine-wave">Sine wave</a></li>
<li><a href="#square-wave">Square wave</a></li>
<li><a href="#sawtooth-wave">Sawtooth wave</a></li>
<li><a href="#triangle-wave">Triangle wave</a></li>
<li><a href="#pulse-train">Pulse train</a></li>
<li><a href="#cheat-sheet">Cheat sheet</a></li>
<li><a href="#try-inputs">Try these inputs</a></li>
<li><a href="#real-life">Real life</a></li>
<li><a href="#honesty">Limits of finite harmonics</a></li>
<li><a href="#sources">Sources</a></li>
</ol>
</nav>

<div id="fourier-lab" class="fourier-lab">
<div class="fourier-lab-head">
<h3>Fourier series online calculator</h3>
<p>Pick a shape, set harmonics, or type coefficients. Watch time and frequency plots update.</p>
</div>
<div class="fourier-lab-body" id="fourier-lab-app">
<div class="fourier-controls">
<div class="fourier-row">
<div class="fourier-field">
<label for="fourier-shape">Wave shape</label>
<select id="fourier-shape" data-shape>
<option value="square">Square wave</option>
<option value="saw">Sawtooth wave</option>
<option value="triangle">Triangle wave</option>
<option value="custom">Custom harmonics</option>
</select>
</div>
<div class="fourier-field">
<label for="fourier-terms">Harmonics N = <span data-terms-val>8</span></label>
<input id="fourier-terms" data-terms type="range" min="1" max="40" step="1" value="8">
</div>
<div class="fourier-field">
<label for="fourier-freq">Fundamental f₀ (Hz)</label>
<input id="fourier-freq" data-freq type="number" min="0.2" max="20" step="0.1" value="1">
</div>
<div class="fourier-field">
<label for="fourier-amp">Amplitude</label>
<input id="fourier-amp" data-amp type="number" min="0.1" max="5" step="0.1" value="1">
</div>
</div>
<div class="fourier-field">
<label for="fourier-eq">Custom equation / coefficients</label>
<textarea id="fourier-eq" data-equation placeholder="b1=1, b3=1/3, b5=1/5  or  1*sin(1), 0.5*sin(3)">b1=1, b3=1/3, b5=1/5, b7=1/7</textarea>
<p class="fourier-hint">b1=1 means 1·sin(1ωt). b3=1/3 means (1/3)·sin(3ωt). aₙ = cos terms. Apply switches to Custom.</p>
</div>
<div class="fourier-actions">
<button type="button" class="fourier-btn fourier-btn-primary" data-apply>Apply equation</button>
<button type="button" class="fourier-btn" data-reset>Reset calculator</button>
</div>
<div class="fourier-eq-box">
<span class="label">Built series</span>
<code data-eq-out>f(t) ≈ …</code>
</div>
</div>
<div class="fourier-help" id="lab-controls">
<h4>What each calculator control means</h4>
<p><strong>Wave shape</strong> picks the target recipe. Square, saw, triangle, or your mix.</p>
<p>Shape decides which harmonics are allowed, and the starting aₙ / bₙ values.</p>
<p><strong>Harmonics N</strong> is how many terms you keep in the sum.</p>
<p>Low N = rough sketch. High N = closer to the ideal time shape.</p>
<p>In frequency view, N is also “how many bar slots” you are willing to fill.</p>
<p><strong>Fundamental f₀</strong> sets the base pitch and the period T = 1/f₀.</p>
<p>Every bar sits at n·f₀. Raise f₀ and the whole spectrum stretches right.</p>
<p>Time plot: one period gets shorter. The wiggles pack into less time.</p>
<p><strong>Amplitude</strong> scales the whole wave up or down. Like a volume knob.</p>
<p>All |cₙ| bars grow together. Ratios between harmonics stay the same.</p>
</div>
<div class="fourier-plots">
<div class="fourier-plot">
<h4>Time plot — Fourier waveform</h4>
<canvas data-time-canvas width="520" height="220" aria-label="Time domain Fourier series waveform with labeled axes"></canvas>
<p>X = time in seconds. Y = amplitude. Teal = series sum. Orange dashed = target.</p>
</div>
<div class="fourier-plot">
<h4>Frequency plot — Fourier spectrum</h4>
<canvas data-spec-canvas width="360" height="220" aria-label="Frequency spectrum bar chart with labeled axes"></canvas>
<p>X = frequency in Hz (n·f₀). Y = coefficient size |c| = √(a²+b²).</p>
</div>
</div>
<div class="table-wrap">
<table class="fourier-terms">
<thead>
<tr><th>n</th><th>aₙ (cos)</th><th>bₙ (sin)</th><th>|cₙ|</th></tr>
</thead>
<tbody data-terms-body></tbody>
</table>
</div>
<p class="fourier-warn">Demo for learning. Real FFT tools use sampled data and windowing. Keep N modest on slow phones.</p>
</div>
</div>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>

<p>I still remember the day a square wave finally clicked for me.</p>
<p>It looked angry on the scope. All corners. No curve in sight.</p>
<p>Then my professor slid the harmonic count from 1 to 15.</p>
<p>The corners grew out of soft sines. Like bricks stacked into a wall.</p>
<p>That stack is the Fourier idea. Ugly waves are just polite sines in a pile.</p>
<hr class="section-break">

<h2 id="what-is-ft">What is a Fourier transform, in plain words?</h2>
<p>A Fourier transform asks one simple question about a signal.</p>
<p>Which pure tones are hiding inside it, and how loud is each?</p>
<p>Think of a smoothie. Fruit is mixed. You want the recipe back.</p>
<p>Fourier math is the blender run in reverse. It lists the recipe.</p>
<p>Time view shows “what happens when.” Frequency view shows “what tones.”</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="series-vs-transform">Fourier series vs Fourier transform</h2>
<p>People mix these names. They are cousins, not twins.</p>
<div class="table-card"><h3 class="table-title">Quick map</h3><div class="table-wrap"><table><thead><tr><th>Idea</th><th>Best for</th><th>What you get</th></tr></thead><tbody><tr><td>Fourier series</td><td>Repeating waves</td><td>Sum of sines and cosines</td></tr><tr><td>Fourier transform</td><td>One-off or long signals</td><td>A continuous spectrum</td></tr><tr><td>DFT / FFT</td><td>Computer samples</td><td>Discrete frequency bins</td></tr></tbody></table></div></div>
<p>This calculator draws a Fourier series. That is the hands-on cousin.</p>
<p>If a wave repeats forever, a series can rebuild it from harmonics.</p>
<p>The transform generalizes the same idea beyond neat periods.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="read-graphs">How to read the graphs</h2>
<p><strong>Left plot (time):</strong> X is seconds. Y is amplitude.</p>
<p>Teal line is your series sum. Orange dashed is the ideal shape.</p>
<p>Add terms and the teal line hugs the orange corners more tightly.</p>
<p><strong>Right plot (frequency):</strong> X is Hz. Y is coefficient size.</p>
<p>Tall bars mean strong tones. Empty spots mean that harmonic is off.</p>
<p>For a square wave, only odd harmonics show. That is not random.</p>
<p>Even pieces cancel. Odd pieces build the flat tops and sharp edges.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="time-vs-freq">Time domain ↔ frequency domain</h2>
<p>These two plots are the same signal, told two ways.</p>
<p><strong>Time domain:</strong> height versus time. You see shape, edges, and period.</p>
<p><strong>Frequency domain:</strong> strength versus frequency. You see the tone recipe.</p>
<p>A slow smooth bump in time needs mostly low-frequency bars.</p>
<p>A sharp corner or jump in time needs tall high-frequency bars too.</p>
<p>That is the core link: sharp in time ↔ wide in frequency.</p>
<p>Square: flat tops + jumps → odd bars that die slowly (~1/n).</p>
<p>Sawtooth: one big snap each period → bars at every n·f₀ (~1/n).</p>
<p>Sine: already one tone → one bar. Time and spectrum both look “simple.”</p>
<p>Change shape → bar pattern changes. Change f₀ → bar positions move.</p>
<p>Change amplitude → bar heights scale. Change N → more (or fewer) bars used.</p>
<p>Use the figures under each wave below. Time on the left. Spectrum on the right.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="built-series">What the “Built series” line means</h2>
<p>The calculator prints a live sum. It might look like this:</p>
<p><code>f(t) ≈ 1.273·sin(1ωt) +0.424·sin(3ωt) +0.255·sin(5ωt) +…</code></p>
<p>Read it left to right. f(t) is the wave height at time t.</p>
<p>The ≈ sign means “close enough with N terms,” not perfect forever.</p>
<p>Each chunk is one pure tone: amp · sin(nωt) or amp · cos(nωt).</p>
<p>n = 1 is the fundamental. n = 3 is three times as fast. And so on.</p>
<p>ω means 2πf₀. If f₀ = 1 Hz, then ω = 2π radians per second.</p>
<p>So sin(1ωt) wiggles once per period. sin(3ωt) wiggles three times.</p>
<p>Bigger coefficients mean louder that harmonic in the mix.</p>
<p>If amplitude is set to 4, those numbers scale up by about four.</p>
<p>Example: square, amp = 4, N = 7 often starts near 5.093·sin(1ωt).</p>
<p>That is just 4 × 4/π. The recipe stayed the same. Only the volume changed.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="custom-coefficients">Custom equation / coefficients (aₙ and bₙ)</h2>
<p>Think of aₙ and bₙ as knobs on a mixer for each harmonic n.</p>
<p><strong>bₙ</strong> is the weight of sin(nωt). Odd symmetry leans on these.</p>
<p><strong>aₙ</strong> is the weight of cos(nωt). Even / shifted shapes use these.</p>
<p>Typing <code>b1=1</code> means: include 1 · sin(1ωt).</p>
<p>Typing <code>b3=1/3</code> means: include (1/3) · sin(3ωt).</p>
<p>Typing <code>a2=0.2</code> means: include 0.2 · cos(2ωt).</p>
<p>You can also write <code>1*sin(1), 0.5*sin(3)</code>. Same idea, shorter.</p>
<p>Hit <strong>Apply equation</strong>. The shape switches to Custom. Plots redraw.</p>
<p>Or edit the table cells. Change aₙ or bₙ. Watch the spectrum move.</p>
<p>|cₙ| in the table is √(aₙ² + bₙ²). It is the total strength of harmonic n.</p>
<p>Zero aₙ and zero bₙ for some n? That bar disappears. Simple as that.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="general-series">The general Fourier series</h2>
<p>For a period T = 1/f₀, the real form is:</p>
<p><code>f(t) ≈ a₀/2 + Σₙ [aₙ cos(nωt) + bₙ sin(nωt)]</code></p>
<p>with ω = 2π/T = 2πf₀, and n = 1, 2, 3, …</p>
<p>a₀ covers a DC offset (average height). Many odd waves set a₀ = 0.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="sine-wave">Sine wave</h2>
<p>A pure sine is already one Fourier term. Nothing to “discover.”</p>
<p><code>f(t) = A · sin(ωt)</code></p>
<p>So b₁ = A, and every other aₙ, bₙ is zero.</p>
<p>Spectrum: one bar at f₀. Time plot: one smooth curve. Done.</p>
<p>In the calculator, use Custom: <code>b1=1</code> and set N = 1.</p>
<div class="fourier-demo-grid">
<div class="fourier-plot">
<h4>Sine — time domain</h4>
<canvas data-fourier-demo data-demo-shape="sine" data-demo-view="time" data-demo-terms="1" width="400" height="180" aria-label="Sine wave time domain plot"></canvas>
<p>X = time t (s). Y = amplitude. One smooth cycle per period. Teal matches the target.</p>
</div>
<div class="fourier-plot">
<h4>Sine — frequency domain</h4>
<canvas data-fourier-demo data-demo-shape="sine" data-demo-view="freq" data-demo-terms="1" width="400" height="180" aria-label="Sine wave frequency spectrum"></canvas>
<p>X = frequency f (Hz). Y = |cₙ|. A single bar at f₀. No other harmonics.</p>
</div>
</div>
<p>If you add extras by mistake, new bars appear. The time plot stops looking pure.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="square-wave">Square wave</h2>
<p>Period T. Height +A for half a period, then −A for the other half.</p>
<p>Only odd sine terms survive. Even harmonics cancel out.</p>
<p><code>f(t) ≈ (4A/π) · [sin(ωt) + (1/3)sin(3ωt) + (1/5)sin(5ωt) + …]</code></p>
<p>In coefficient language: bₙ = 4A/(πn) for odd n. Else bₙ = 0.</p>
<p>aₙ = 0 for all n in this odd square setup.</p>
<p>More odd terms → flatter tops, sharper jumps, tiny Gibbs ripples.</p>
<p>Calculator: Shape = Square. Raise N. Watch odd bars grow on the right plot.</p>
<div class="fourier-demo-grid">
<div class="fourier-plot">
<h4>Square — time domain</h4>
<canvas data-fourier-demo data-demo-shape="square" data-demo-view="time" data-demo-terms="9" width="400" height="180" aria-label="Square wave time domain plot"></canvas>
<p>X = time t (s). Y = amplitude. Orange dashed = ideal square. Teal = Fourier sum (N = 9).</p>
</div>
<div class="fourier-plot">
<h4>Square — frequency domain</h4>
<canvas data-fourier-demo data-demo-shape="square" data-demo-view="freq" data-demo-terms="9" width="400" height="180" aria-label="Square wave frequency spectrum"></canvas>
<p>X = frequency f (Hz). Y = |cₙ|. Only odd bars: f₀, 3f₀, 5f₀… Heights fall ~1/n.</p>
</div>
</div>
<p>Time jumps need many high frequencies. That is why the spectrum is “odd and long.”</p>
<p>Gaps at 2f₀, 4f₀, … match the missing even terms in the equation.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="sawtooth-wave">Sawtooth wave</h2>
<p>A ramp that climbs, then snaps back. Classic synth and scope shape.</p>
<p>Both odd and even sine harmonics appear. Amplitudes fall like 1/n.</p>
<p><code>f(t) ≈ (2A/π) · [sin(ωt) − (1/2)sin(2ωt) + (1/3)sin(3ωt) − …]</code></p>
<p>Or: bₙ = (2A/(πn)) · (−1)<sup>n+1</sup>.</p>
<p>Spectrum is denser than a square. Every integer multiple of f₀ shows up.</p>
<p>Calculator: Shape = Sawtooth. Compare the bar chart to Square’s odd-only pattern.</p>
<div class="fourier-demo-grid">
<div class="fourier-plot">
<h4>Sawtooth — time domain</h4>
<canvas data-fourier-demo data-demo-shape="saw" data-demo-view="time" data-demo-terms="10" width="400" height="180" aria-label="Sawtooth wave time domain plot"></canvas>
<p>X = time t (s). Y = amplitude. Orange dashed = ideal ramp. Teal = Fourier sum (N = 10).</p>
</div>
<div class="fourier-plot">
<h4>Sawtooth — frequency domain</h4>
<canvas data-fourier-demo data-demo-shape="saw" data-demo-view="freq" data-demo-terms="10" width="400" height="180" aria-label="Sawtooth wave frequency spectrum"></canvas>
<p>X = frequency f (Hz). Y = |cₙ|. Bars at every n·f₀. Heights fall ~1/n.</p>
</div>
</div>
<p>The snap-back edge is sharp in time. So energy spreads across many harmonics.</p>
<p>Unlike the square, even bars are present. The time shape is not half-wave odd the same way.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="triangle-wave">Triangle wave</h2>
<p>Linear up, linear down. Softer than a square. Less harsh to the ear.</p>
<p>Odd harmonics again, but strengths fall like 1/n². Much faster decay.</p>
<p><code>f(t) ≈ (8A/π²) · [sin(ωt) − (1/9)sin(3ωt) + (1/25)sin(5ωt) − …]</code></p>
<p>For odd n: bₙ = ±8A/(π² n²). Signs alternate in a fixed pattern.</p>
<p>You need fewer terms to look “good.” Corners are gentler by design.</p>
<p>Calculator: Shape = Triangle. Notice how high-n bars stay tiny.</p>
<div class="fourier-demo-grid">
<div class="fourier-plot">
<h4>Triangle — time domain</h4>
<canvas data-fourier-demo data-demo-shape="triangle" data-demo-view="time" data-demo-terms="7" width="400" height="180" aria-label="Triangle wave time domain plot"></canvas>
<p>X = time t (s). Y = amplitude. Orange dashed = ideal triangle. Teal = Fourier sum (N = 7).</p>
</div>
<div class="fourier-plot">
<h4>Triangle — frequency domain</h4>
<canvas data-fourier-demo data-demo-shape="triangle" data-demo-view="freq" data-demo-terms="7" width="400" height="180" aria-label="Triangle wave frequency spectrum"></canvas>
<p>X = frequency f (Hz). Y = |cₙ|. Odd bars only. Heights fall fast ~1/n².</p>
</div>
</div>
<p>Compare to square: same odd slots, but much weaker high-n energy.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="pulse-train">Pulse train (rectangular pulse)</h2>
<p>A pulse is “on” for a short duty cycle D, then zero until the next period.</p>
<p>If the pulse is even (centered), cosine terms aₙ carry the story.</p>
<p>With period T and pulse width τ (so D = τ/T):</p>
<p><code>f(t) ≈ A D + Σ 2 A D · sinc(n D) · cos(nωt)</code></p>
<p>Here sinc(x) = sin(πx)/(πx). Narrow D spreads the spectrum wider.</p>
<p>Figures below use D = 0.25 (on for a quarter of each period).</p>
<p>Calculator tip: build a similar mix with Custom aₙ values and compare.</p>
<div class="fourier-demo-grid">
<div class="fourier-plot">
<h4>Pulse — time domain</h4>
<canvas data-fourier-demo data-demo-shape="pulse" data-demo-view="time" data-demo-terms="12" width="400" height="180" aria-label="Pulse train time domain plot"></canvas>
<p>X = time t (s). Y = amplitude. Orange dashed = ideal pulses. Teal = Fourier sum (N = 12).</p>
</div>
<div class="fourier-plot">
<h4>Pulse — frequency domain</h4>
<canvas data-fourier-demo data-demo-shape="pulse" data-demo-view="freq" data-demo-terms="12" width="400" height="180" aria-label="Pulse train frequency spectrum"></canvas>
<p>X = frequency f (Hz). Y = |cₙ|. Cosine bars under a sinc envelope. DC is the average height.</p>
</div>
</div>
<p>Narrower pulses → slower sinc decay → more high-frequency bars matter.</p>
<p>Rule of thumb: sharp time edges ↔ wide frequency content.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="cheat-sheet">Side-by-side cheat sheet</h2>
<div class="table-card"><h3 class="table-title">Common waves and their harmonics</h3><div class="table-wrap"><table><thead><tr><th>Wave</th><th>Which terms?</th><th>How fast |cₙ| falls</th></tr></thead><tbody><tr><td>Sine</td><td>Only n = 1</td><td>—</td></tr><tr><td>Square</td><td>Odd bₙ</td><td>~ 1/n</td></tr><tr><td>Sawtooth</td><td>All bₙ</td><td>~ 1/n</td></tr><tr><td>Triangle</td><td>Odd bₙ</td><td>~ 1/n²</td></tr><tr><td>Pulse</td><td>Mostly aₙ (+ DC)</td><td>sinc envelope</td></tr></tbody></table></div></div>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="try-inputs">Try these inputs in the calculator</h2>
<ul>
<li>Shape: Square. N: 1 → 3 → 15. Spot the ringing near jumps.</li>
<li>Shape: Sawtooth. Even harmonics appear. The spectrum fills in.</li>
<li>Shape: Triangle. Amplitudes fall like 1/n². Softer corners.</li>
<li>Custom sine: <code>b1=1</code> then Apply. One bar. One wiggle.</li>
<li>Custom square-ish: <code>b1=1, b3=1/3, b5=1/5</code> then Apply.</li>
<li>Edit aₙ / bₙ in the table. The plots redraw after each change.</li>
</ul>
<p>If the parser shrugs, check commas and equals signs. Keep it simple.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="real-life">Where this shows up in real life</h2>
<p>EQ knobs on a mixer are frequency thinking, not time thinking.</p>
<p>Phone codecs drop weak bands. Your ear rarely notices the missing bits.</p>
<p>MRI, radio, and vibration tests all lean on the same decomposition.</p>
<p>You do not need the full proof to use the picture with care.</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="honesty">Limits of finite harmonics</h2>
<p>Infinite terms can match many periodic targets exactly.</p>
<p>Finite N is always an approximation. That is fine for learning.</p>
<p>Jump discontinuities keep tiny ripples. That is the Gibbs effect.</p>
<p>Real FFT work also fights noise, windows, and sample rate limits.</p>
<p>Use this page to build intuition. Use a textbook for proofs.</p>
<p>— <strong>Leila Okonkwo</strong><br>signals notes, rewritten for humans</p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
<hr class="section-break">

<h2 id="sources">Sources</h2>
<ul>
<li><a href="https://ocw.mit.edu/courses/6-003-signals-and-systems-fall-2011/" rel="noopener noreferrer">MIT OpenCourseWare — Signals and Systems (6.003)</a></li>
<li><a href="https://math.mit.edu/~gs/" rel="noopener noreferrer">Gilbert Strang materials — MIT Mathematics</a></li>
<li><a href="https://www.nist.gov/pml/time-and-frequency-division" rel="noopener noreferrer">NIST — Time and Frequency Division</a></li>
</ul>
<p class="table-note"><em>Educational demo only. Not a substitute for a full DSP course.</em></p>
<p class="fourier-back"><a href="#fourier-toc">↑ Top / Index</a></p>
