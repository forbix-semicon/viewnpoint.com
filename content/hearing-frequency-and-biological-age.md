<div id="hearing-test-app" class="hearing-tool">
    <div class="hearing-tool-head">
        <h3>Hearing frequency lab</h3>
        <p>Find the highest tone you can still hear — then see what that says about your biological / ear age.</p>
    </div>
    <div class="hearing-tool-body">
        <div class="hearing-wave-wrap" aria-hidden="true">
            <canvas width="640" height="160"></canvas>
        </div>
        <div class="hearing-readouts">
            <div class="hearing-stat">
                <span class="label">Frequency</span>
                <span class="value"><span data-freq-value>1.00k</span><span class="unit">Hz</span></span>
            </div>
            <div class="hearing-stat">
                <span class="label">Amplitude</span>
                <span class="value"><span data-amp-value>03</span><span class="unit">/10</span></span>
            </div>
            <div class="hearing-stat">
                <span class="label">Wave</span>
                <span class="value" style="font-size:1rem;">sine</span>
            </div>
        </div>
        <div class="hearing-controls">
            <div class="hearing-control">
                <label for="hearing-freq">Frequency <span data-freq-value>1.00k</span> Hz</label>
                <input id="hearing-freq" data-freq type="range" min="0" max="1000" step="1" value="400" aria-valuemin="20" aria-valuemax="30000">
            </div>
            <div class="hearing-control">
                <label for="hearing-amp">Volume / amplitude <span data-amp-value>03</span></label>
                <input id="hearing-amp" data-amp type="range" min="0" max="10" step="1" value="3">
            </div>
        </div>
        <div class="hearing-actions">
            <button type="button" class="hearing-btn hearing-btn-primary" data-play>Play tone</button>
            <button type="button" class="hearing-btn" data-stop disabled>Stop</button>
            <button type="button" class="hearing-btn" data-mark disabled>I can still hear this</button>
            <button type="button" class="hearing-btn is-hidden" data-share disabled>Share result</button>
        </div>
        <ul class="hearing-checklist">
            <li>Start quiet (volume 2–3). Use headphones if you can.</li>
            <li>Slider is stretched for hearing age: ~25% ≈ 500 Hz, ~50% ≈ 2 kHz, <strong>~75% ≈ 12 kHz</strong>, 100% ≈ 30 kHz.</li>
            <li>Slide up until the tone vanishes, nudge down until you hear it, then press <strong>I can still hear this</strong>.</li>
        </ul>
        <div class="hearing-age-box is-hidden" data-age-box>
            <h4>Result — your rough biological / ear age</h4>
            <div class="hearing-result-grid">
                <div class="hearing-stat">
                    <span class="label">Highest tone marked</span>
                    <span class="value" style="font-size:1.05rem;" data-result-hz>—</span>
                </div>
                <div class="hearing-stat">
                    <span class="label">Est. ear age</span>
                    <span class="value hearing-age-number" data-age-number style="font-size:1.4rem;">—</span>
                </div>
            </div>
            <p data-age-detail></p>
            <p class="hearing-warn">Marked frequency: <strong data-highest>—</strong></p>
        </div>
        <p class="hearing-warn">Browser demo for curiosity — not a clinical audiogram. Keep volume low, especially above 8 kHz.</p>
    </div>
</div>

<div id="hearing-share-modal" class="hearing-share-modal" aria-hidden="true" role="dialog" aria-label="Share hearing result">
    <div class="hearing-share-card">
        <h3>Share your result</h3>
        <p class="hearing-warn" style="margin-bottom:.5rem;">Add your name (optional), copy the message, and send it to a friend — the link brings them back here to check their own ear age.</p>
        <label for="hearing-share-name">Your name</label>
        <input id="hearing-share-name" data-share-name type="text" maxlength="60" placeholder="e.g. Priya" autocomplete="name">
        <label for="hearing-share-preview">Message</label>
        <textarea id="hearing-share-preview" data-share-preview readonly></textarea>
        <div class="hearing-share-actions">
            <button type="button" class="hearing-btn hearing-btn-primary" data-share-copy>Copy result &amp; link</button>
            <button type="button" class="hearing-btn" data-share-close>Close</button>
        </div>
    </div>
</div>

<p>I teach acoustics labs at Portland State. Every semester someone walks in sure they have perfect hearing. We put on headphones, start near 1 kHz, then climb. Around 14–15 kHz a few faces go blank. By 17–18 kHz, almost everyone over 25 is guessing.</p>

<p>That gap is not just “music taste.” High-frequency hearing is one of the earliest clocks of sensory aging. Calendar age is one number. The highest tone you can still hear is another — a rough <strong>biological / ear age</strong> shaped by traffic, headphones, stress, and sleep.</p>

<p>Use the lab above. Mark the highest tone you can hear. Read the result. Then come back in a few days with the same headphones and see if the number moved.</p>

<hr class="section-break">

<h2>Hearing frequency ↔ biological age (data-backed guide)</h2>

<p>Clinics measure thresholds across many frequencies. At home, the useful shortcut is your <strong>high-frequency ceiling</strong> — the highest pitch you can still detect at a comfortable volume. Teaching charts and aging/noise studies line up like this:</p>

<div class="table-card">
    <h3 class="table-title">Typical highest audible tone vs approximate ear age</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Approx. age band</th>
                    <th>Highest still heard (approx.)</th>
                    <th>Lowest practical (approx.)</th>
                    <th>Notes from research / teaching use</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>16–20</td><td>~17–20 kHz</td><td>~20 Hz</td><td>Teen / early adult; high end still sharp</td></tr>
                <tr><td>20–25</td><td>~15–17 kHz</td><td>~20 Hz</td><td>Young adult; city noise already bites some people</td></tr>
                <tr><td>25–35</td><td>~12–15 kHz</td><td>~20–25 Hz</td><td>Headphones + commute often show here</td></tr>
                <tr><td>35–45</td><td>~10–13 kHz</td><td>~25–30 Hz</td><td>Early middle-age high-tone pattern</td></tr>
                <tr><td>45–55</td><td>~8–11 kHz</td><td>~30–40 Hz</td><td>Common after years of urban / leisure noise</td></tr>
                <tr><td>55–65</td><td>~6–9 kHz</td><td>~40–60 Hz</td><td>“Air” and birds fade; speech still mostly OK</td></tr>
                <tr><td>65–75+</td><td>~4–7 kHz or lower</td><td>~50–80 Hz</td><td>Age + cumulative noise; get a clinic test if sudden</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="hearing-chart-wrap">
    <canvas id="hearing-age-chart" width="480" height="300" aria-label="Chart: highest still-heard frequency in kHz versus age in years"></canvas>
    <p class="hearing-warn" style="margin-top:.55rem;">Each point shows the typical highest tone still heard (kHz) at that age (yrs). Not a personal diagnosis.</p>
</div>

<p class="table-note"><em>Sources informing these bands: <a href="https://www.nidcd.nih.gov/health/age-related-hearing-loss" rel="noopener noreferrer">NIDCD on presbycusis</a>, <a href="https://www.nidcd.nih.gov/health/noise-induced-hearing-loss" rel="noopener noreferrer">NIDCD on noise-induced loss</a>, and <a href="https://www.nidcd.nih.gov/health/statistics/quick-statistics-hearing" rel="noopener noreferrer">NIDCD hearing statistics</a>. Exact cutoffs vary by person, headphones, and room noise.</em></p>

<hr class="section-break">

<h2>What is the audible range, really?</h2>

<p>Textbooks say humans hear about <strong>20 Hz to 20,000 Hz</strong>. That is a classroom average, not a promise for your ears today.</p>

<ul>
    <li><strong>Below ~20 Hz</strong> — mostly felt as vibration.</li>
    <li><strong>20–250 Hz</strong> — bass, traffic rumble, HVAC.</li>
    <li><strong>250–4,000 Hz</strong> — most speech clarity.</li>
    <li><strong>4,000–8,000 Hz</strong> — consonants, birds, alarms; early noise damage often shows near 4 kHz.</li>
    <li><strong>8,000–20,000 Hz</strong> — the “sparkle.” This ceiling falls first with age and loud living — and it is what our ear-age estimate uses.</li>
</ul>

<hr class="section-break">

<h2>City noise, stress, pollution — why ear age runs ahead of the calendar</h2>

<p>NIOSH and NIDCD both describe noise-induced hearing loss as gradual and cumulative — not only a single loud night. Traffic, transit platforms, construction, clubs, and earbuds turned up to mask the city all add exposure minutes that stack over years.</p>

<p>Researchers at the <a href="https://medicine.musc.edu/departments/pathology-laboratory-medicine/divisions/experimental-pathology/labs/sha" rel="noopener noreferrer">Medical University of South Carolina</a> note that aging, loud noise, and certain ototoxic exposures share similar inner-ear damage patterns, with high frequencies often affected first. CDC occupational data show age is the strongest predictor of hearing loss in adults — but people with years of hazardous noise exposure start from a worse baseline.</p>

<p>Stress and poor sleep do not damage hair cells the way a jackhammer does, but they keep you in loud environments longer and nudge you toward higher volume. NIDCD guidance on <a href="https://www.nidcd.nih.gov/health/noisy-world-damages-hearing" rel="noopener noreferrer">everyday noise</a> is blunt: if you need to shout over background sound, that environment is already risky for long-term hearing.</p>

<hr class="section-break">

<h2>What slows the decline (and what speeds it up)</h2>

<p><strong>Slows it:</strong> keep average listening nearer 80 dB when you can; earplugs on metro platforms and at shows; quiet hours so hair cells recover; sleep away from street windows; retest this page weekly with the same headphones.</p>

<p><strong>Speeds it up:</strong> sleeping with music on; standing next to speakers “for one song”; ignoring ringing after a night out; raising volume to drown the city instead of blocking the city.</p>

<p>You cannot undo every past year with a green smoothie. You can stop adding damage this month — and watch whether your marked frequency climbs back after quiet weeks.</p>

<hr class="section-break">

<h2>Share it, then come back</h2>

<p>After you mark a result, use <strong>Share result</strong>. Copy the message with your name (optional) and the link. Ask a friend: <em>do you also want to check your biological age from your hearing?</em></p>

<p>One test is a snapshot. Three tests are a story. If conversation in restaurants is getting harder, skip the browser toy and book a real audiogram.</p>

<p>— <strong>Jerry Thomas</strong><br>
Portland State University</p>

<hr class="section-break">

<h2>Sources</h2>

<ul>
    <li><a href="https://www.nidcd.nih.gov/health/age-related-hearing-loss" rel="noopener noreferrer">NIH / NIDCD — age-related hearing loss (presbycusis)</a></li>
    <li><a href="https://www.nidcd.nih.gov/health/noise-induced-hearing-loss" rel="noopener noreferrer">NIH / NIDCD — noise-induced hearing loss</a></li>
    <li><a href="https://www.nidcd.nih.gov/health/noisy-world-damages-hearing" rel="noopener noreferrer">NIH / NIDCD — how everyday noise damages hearing</a></li>
    <li><a href="https://www.nidcd.nih.gov/health/statistics/quick-statistics-hearing" rel="noopener noreferrer">NIH / NIDCD — quick statistics on hearing</a></li>
    <li><a href="https://www.cdc.gov/niosh/noise/about/noise.html" rel="noopener noreferrer">CDC / NIOSH — noise-induced hearing loss overview</a></li>
    <li><a href="https://www.cdc.gov/niosh/noise/about/index.html" rel="noopener noreferrer">CDC / NIOSH — occupational hearing loss</a></li>
    <li><a href="https://medicine.musc.edu/departments/pathology-laboratory-medicine/divisions/experimental-pathology/labs/sha" rel="noopener noreferrer">Medical University of South Carolina — Sha Lab (acquired hearing loss research)</a></li>
    <li><a href="https://www.pdx.edu/" rel="noopener noreferrer">Portland State University</a></li>
</ul>

<p class="table-note"><em>Educational tool only. Not medical advice.</em></p>
