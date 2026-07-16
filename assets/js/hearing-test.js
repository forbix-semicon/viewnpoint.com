(function () {
    "use strict";

    var root = document.getElementById("hearing-test-app");
    if (!root) {
        return;
    }

    // Anchored map: more of the slider lives in the high-frequency band used for ear-age.
    // 0%→20Hz, 25%→500Hz, 50%→2kHz, 75%→12kHz, 100%→30kHz
    var ANCHORS = [
        { t: 0, hz: 20 },
        { t: 0.25, hz: 500 },
        { t: 0.5, hz: 2000 },
        { t: 0.75, hz: 12000 },
        { t: 1, hz: 30000 },
    ];
    var DEFAULT_HZ = 1000;
    var DEFAULT_AMP = 3;
    var PAGE_URL = (function () {
        var link = document.querySelector('link[rel="canonical"]');
        return link && link.href ? link.href : window.location.href.split("#")[0];
    })();

    var freqSlider = root.querySelector("[data-freq]");
    var ampSlider = root.querySelector("[data-amp]");
    var playBtn = root.querySelector("[data-play]");
    var markBtn = root.querySelector("[data-mark]");
    var stopBtn = root.querySelector("[data-stop]");
    var shareBtn = root.querySelector("[data-share]");
    var canvas = root.querySelector("canvas");
    var ageBox = root.querySelector("[data-age-box]");
    var ageNumber = root.querySelector("[data-age-number]");
    var ageDetail = root.querySelector("[data-age-detail]");
    var highestEl = root.querySelector("[data-highest]");
    var resultHzEl = root.querySelector("[data-result-hz]");
    var modal = document.getElementById("hearing-share-modal");
    var shareName = modal ? modal.querySelector("[data-share-name]") : null;
    var sharePreview = modal ? modal.querySelector("[data-share-preview]") : null;
    var shareCopyBtn = modal ? modal.querySelector("[data-share-copy]") : null;
    var shareCloseBtns = modal ? modal.querySelectorAll("[data-share-close]") : [];

    var audioCtx = null;
    var oscillator = null;
    var gainNode = null;
    var playing = false;
    var animId = 0;
    var phase = 0;
    var lastResult = null;

    function clamp(n, min, max) {
        return Math.min(max, Math.max(min, n));
    }

    function sliderToHz(pos) {
        var t = clamp(Number(pos) / 1000, 0, 1);
        var i = 0;
        while (i < ANCHORS.length - 2 && t > ANCHORS[i + 1].t) {
            i += 1;
        }
        var a = ANCHORS[i];
        var b = ANCHORS[i + 1];
        var local = (t - a.t) / (b.t - a.t);
        var logA = Math.log10(a.hz);
        var logB = Math.log10(b.hz);
        return Math.pow(10, logA + local * (logB - logA));
    }

    function hzToSlider(hz) {
        hz = clamp(hz, ANCHORS[0].hz, ANCHORS[ANCHORS.length - 1].hz);
        var i = 0;
        while (i < ANCHORS.length - 2 && hz > ANCHORS[i + 1].hz) {
            i += 1;
        }
        var a = ANCHORS[i];
        var b = ANCHORS[i + 1];
        var logHz = Math.log10(hz);
        var logA = Math.log10(a.hz);
        var logB = Math.log10(b.hz);
        var local = (logHz - logA) / (logB - logA);
        return Math.round((a.t + local * (b.t - a.t)) * 1000);
    }

    function formatHz(hz) {
        if (hz < 100) {
            return hz.toFixed(1);
        }
        if (hz < 1000) {
            return String(Math.round(hz));
        }
        if (hz < 10000) {
            return (hz / 1000).toFixed(2) + "k";
        }
        return (hz / 1000).toFixed(1) + "k";
    }

    function formatHzExact(hz) {
        if (hz < 1000) {
            return Math.round(hz) + " Hz";
        }
        return Math.round(hz) + " Hz (" + (hz / 1000).toFixed(2) + " kHz)";
    }

    function ampToGain(amp) {
        return (clamp(Number(amp), 0, 10) / 10) * 0.35;
    }

    function biologicalAgeFromHz(hz) {
        if (hz >= 18000) {
            return { age: 18, range: "16–20", note: "Teen / early adult range. High-frequency hearing is still sharp." };
        }
        if (hz >= 16000) {
            return { age: 23, range: "20–25", note: "Young adult. Many city workers already lose this band." };
        }
        if (hz >= 14000) {
            return { age: 29, range: "25–32", note: "Late twenties pattern. Headphones and traffic start to show." };
        }
        if (hz >= 12000) {
            return { age: 36, range: "32–40", note: "Early middle-age pattern for high tones." };
        }
        if (hz >= 10000) {
            return { age: 44, range: "40–48", note: "Common after years of urban noise or loud music." };
        }
        if (hz >= 8000) {
            return { age: 52, range: "48–55", note: "High-frequency ceiling is dropping." };
        }
        if (hz >= 6000) {
            return { age: 60, range: "55–65", note: "Clear high-frequency loss pattern. Protect what remains." };
        }
        if (hz >= 4000) {
            return { age: 70, range: "65–75", note: "Typical age/noise pattern. See an audiologist if this is new for you." };
        }
        return { age: 78, range: "75+", note: "Very limited high-frequency range on this home test." };
    }

    function currentHz() {
        return sliderToHz(freqSlider.value);
    }

    function currentAmp() {
        return Number(ampSlider.value);
    }

    function audibleHz(hz) {
        // Web Audio cannot produce above Nyquist (sampleRate/2). Clamp so the
        // oscillator request matches what the device can actually synthesize.
        if (!audioCtx) {
            return hz;
        }
        var nyquist = audioCtx.sampleRate / 2;
        return Math.min(hz, nyquist - 1);
    }

    function updateReadouts() {
        var hz = currentHz();
        var amp = currentAmp();
        var hzText = formatHz(hz);
        var ampText = String(amp).padStart(2, "0");
        root.querySelectorAll("[data-freq-value]").forEach(function (el) {
            el.textContent = hzText;
        });
        root.querySelectorAll("[data-amp-value]").forEach(function (el) {
            el.textContent = ampText;
        });
        if (oscillator && audioCtx) {
            // Instant set keeps pitch locked to the slider (no lag/smoothing miss).
            oscillator.frequency.setValueAtTime(audibleHz(hz), audioCtx.currentTime);
        }
        if (gainNode && audioCtx) {
            gainNode.gain.setTargetAtTime(playing ? ampToGain(amp) : 0, audioCtx.currentTime, 0.02);
        }
    }

    function ensureAudio() {
        if (!audioCtx) {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            audioCtx = new Ctx();
        }
        if (audioCtx.state === "suspended") {
            return audioCtx.resume();
        }
        return Promise.resolve();
    }

    function startTone() {
        ensureAudio().then(function () {
            if (playing) {
                updateReadouts();
                return;
            }
            oscillator = audioCtx.createOscillator();
            gainNode = audioCtx.createGain();
            oscillator.type = "sine";
            oscillator.frequency.value = audibleHz(currentHz());
            gainNode.gain.value = 0;
            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            oscillator.start();
            playing = true;
            playBtn.textContent = "Playing…";
            playBtn.disabled = true;
            stopBtn.disabled = false;
            markBtn.disabled = false;
            gainNode.gain.setTargetAtTime(ampToGain(currentAmp()), audioCtx.currentTime, 0.03);
            drawLoop();
        });
    }

    function stopTone() {
        if (oscillator) {
            try {
                oscillator.stop();
            } catch (e) {
                /* ignore */
            }
            oscillator.disconnect();
            oscillator = null;
        }
        if (gainNode) {
            gainNode.disconnect();
            gainNode = null;
        }
        playing = false;
        playBtn.textContent = "Play tone";
        playBtn.disabled = false;
        stopBtn.disabled = true;
        // Keep mark enabled after the first play so results can be refreshed
        cancelAnimationFrame(animId);
        drawWave(false);
    }

    function showResult(hz) {
        var result = biologicalAgeFromHz(hz);
        lastResult = {
            hz: hz,
            age: result.age,
            range: result.range,
            note: result.note,
        };
        if (highestEl) {
            highestEl.textContent = formatHzExact(hz);
        }
        if (resultHzEl) {
            resultHzEl.textContent = formatHzExact(hz);
        }
        if (ageNumber) {
            ageNumber.innerHTML = result.age + ' <span>yrs (est. biological / ear age)</span>';
        }
        if (ageDetail) {
            ageDetail.textContent =
                "You marked " +
                formatHzExact(hz) +
                " as still audible. That maps to a rough ear-age band of " +
                result.range +
                ". " +
                result.note +
                " Retest in a few days with the same headphones.";
        }
        if (ageBox) {
            ageBox.classList.remove("is-hidden");
            ageBox.hidden = false;
            ageBox.style.display = "block";
        }
        if (shareBtn) {
            shareBtn.disabled = false;
            shareBtn.classList.remove("is-hidden");
        }
        updateSharePreview();
    }

    function markHeard() {
        showResult(currentHz());
    }

    function buildShareText(name) {
        if (!lastResult) {
            return "";
        }
        var who = (name || "").trim() || "I";
        var line1 =
            who === "I"
                ? "I just checked my hearing frequency on ViewNPoint."
                : who + " just checked their hearing frequency on ViewNPoint.";
        return (
            line1 +
            "\n\nHighest tone still heard: " +
            formatHzExact(lastResult.hz) +
            "\nEstimated biological / ear age: ~" +
            lastResult.age +
            " years (" +
            lastResult.range +
            ")" +
            "\n\nDo you also want to check your biological age from your hearing?\n" +
            PAGE_URL
        );
    }

    function updateSharePreview() {
        if (!sharePreview || !lastResult) {
            return;
        }
        sharePreview.value = buildShareText(shareName ? shareName.value : "");
    }

    function openShareModal() {
        if (!modal || !lastResult) {
            return;
        }
        updateSharePreview();
        modal.classList.add("is-open");
        modal.setAttribute("aria-hidden", "false");
        if (shareName) {
            shareName.focus();
        }
    }

    function closeShareModal() {
        if (!modal) {
            return;
        }
        modal.classList.remove("is-open");
        modal.setAttribute("aria-hidden", "true");
    }

    function copyShare() {
        var text = buildShareText(shareName ? shareName.value : "");
        if (!text) {
            return;
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                if (shareCopyBtn) {
                    shareCopyBtn.textContent = "Copied!";
                    setTimeout(function () {
                        shareCopyBtn.textContent = "Copy result & link";
                    }, 1600);
                }
            });
        } else if (sharePreview) {
            sharePreview.select();
            document.execCommand("copy");
            if (shareCopyBtn) {
                shareCopyBtn.textContent = "Copied!";
            }
        }
    }

    function drawWave(active) {
        if (!canvas) {
            return;
        }
        var dpr = window.devicePixelRatio || 1;
        var cssW = canvas.clientWidth || 640;
        var cssH = canvas.clientHeight || 160;
        if (canvas.width !== Math.floor(cssW * dpr) || canvas.height !== Math.floor(cssH * dpr)) {
            canvas.width = Math.floor(cssW * dpr);
            canvas.height = Math.floor(cssH * dpr);
        }
        var ctx = canvas.getContext("2d");
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, cssW, cssH);

        ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue("--border").trim() || "#334";
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(0, cssH / 2);
        ctx.lineTo(cssW, cssH / 2);
        ctx.stroke();

        var hz = currentHz();
        var amp = currentAmp() / 10;
        // Log density so the drawn wave keeps changing past ~3 kHz.
        // (Old formula clamp(hz/80, …, 40) froze the picture at ~3.2 kHz.)
        var logMin = Math.log10(20);
        var logMax = Math.log10(30000);
        var logHz = Math.log10(clamp(hz, 20, 30000));
        var cycles = 0.8 + ((logHz - logMin) / (logMax - logMin)) * 72;
        var mid = cssH / 2;
        var peak = cssH * 0.38 * Math.max(amp, 0.05);
        var ampScale = active ? 1 : 0.55;

        ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue("--accent-2").trim() || "#30d5c8";
        // Thinner stroke when dense so high-kHz cycles stay readable
        ctx.lineWidth = cycles > 36 ? 1.25 : 2;
        ctx.beginPath();
        for (var x = 0; x <= cssW; x++) {
            var t = x / cssW;
            var y = mid - Math.sin(t * cycles * Math.PI * 2 + phase) * peak * ampScale;
            if (x === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        }
        ctx.stroke();

        if (active) {
            // Phase advance scales with frequency so motion stays visible at high Hz
            phase += 0.08 + (logHz - logMin) * 0.18;
        }
    }

    function drawLoop() {
        drawWave(playing);
        if (playing) {
            animId = requestAnimationFrame(drawLoop);
        }
    }

    function drawAgeChart() {
        var chart = document.getElementById("hearing-age-chart");
        if (!chart) {
            return;
        }
        // Highest still-heard frequency by age (teaching ranges)
        var points = [
            { age: 18, khz: 18 },
            { age: 25, khz: 16 },
            { age: 35, khz: 14 },
            { age: 45, khz: 12 },
            { age: 55, khz: 10 },
            { age: 65, khz: 8 },
            { age: 75, khz: 6 },
        ];
        var dpr = window.devicePixelRatio || 1;
        var parentW = chart.parentElement ? chart.parentElement.clientWidth - 24 : 480;
        var cssW = Math.min(Math.max(parentW, 280), 480);
        var cssH = 300;
        chart.style.width = cssW + "px";
        chart.style.height = cssH + "px";
        chart.width = Math.floor(cssW * dpr);
        chart.height = Math.floor(cssH * dpr);
        var ctx = chart.getContext("2d");
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, cssW, cssH);

        var pad = { l: 52, r: 16, t: 28, b: 52 };
        var plotW = cssW - pad.l - pad.r;
        var plotH = cssH - pad.t - pad.b;
        var ageMin = 12;
        var ageMax = 80;
        var khzMin = 0;
        var khzMax = 20;
        var borderColor = getComputedStyle(document.documentElement).getPropertyValue("--border").trim() || "#445";
        var bylineColor = getComputedStyle(document.documentElement).getPropertyValue("--article-byline").trim() || "#889";
        var textColor = getComputedStyle(document.documentElement).getPropertyValue("--article-text").trim() || "#ccc";
        var highColor = getComputedStyle(document.documentElement).getPropertyValue("--accent-2").trim() || "#30d5c8";
        var ageTicks = [18, 25, 35, 45, 55, 65, 75];
        var khzTicks = [0, 5, 10, 15, 20];

        function xOf(age) {
            return pad.l + ((age - ageMin) / (ageMax - ageMin)) * plotW;
        }
        function yOf(khz) {
            return pad.t + (1 - (khz - khzMin) / (khzMax - khzMin)) * plotH;
        }

        ctx.strokeStyle = borderColor;
        ctx.lineWidth = 1;
        ctx.strokeRect(pad.l, pad.t, plotW, plotH);

        ctx.font = "11px sans-serif";
        ctx.fillStyle = bylineColor;
        ctx.textAlign = "right";
        ctx.textBaseline = "middle";
        khzTicks.forEach(function (khz) {
            var y = yOf(khz);
            ctx.globalAlpha = 0.35;
            ctx.setLineDash([3, 4]);
            ctx.strokeStyle = borderColor;
            ctx.beginPath();
            ctx.moveTo(pad.l, y);
            ctx.lineTo(pad.l + plotW, y);
            ctx.stroke();
            ctx.setLineDash([]);
            ctx.globalAlpha = 1;
            ctx.beginPath();
            ctx.moveTo(pad.l - 5, y);
            ctx.lineTo(pad.l, y);
            ctx.stroke();
            ctx.fillText(String(khz), pad.l - 8, y);
        });

        ctx.textAlign = "center";
        ctx.textBaseline = "top";
        ageTicks.forEach(function (age) {
            var x = xOf(age);
            ctx.beginPath();
            ctx.moveTo(x, pad.t + plotH);
            ctx.lineTo(x, pad.t + plotH + 5);
            ctx.stroke();
            ctx.fillText(String(age), x, pad.t + plotH + 8);
        });

        ctx.font = "12px sans-serif";
        ctx.fillStyle = bylineColor;
        ctx.fillText("Age (yrs)", pad.l + plotW / 2, cssH - 10);
        ctx.save();
        ctx.translate(14, pad.t + plotH / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.textBaseline = "middle";
        ctx.fillText("Frequency (kHz)", 0, 0);
        ctx.restore();

        ctx.strokeStyle = highColor;
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        points.forEach(function (p, idx) {
            var x = xOf(p.age);
            var y = yOf(p.khz);
            if (idx === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });
        ctx.stroke();

        points.forEach(function (p) {
            var x = xOf(p.age);
            var y = yOf(p.khz);
            ctx.beginPath();
            ctx.arc(x, y, 5, 0, Math.PI * 2);
            ctx.fillStyle = highColor;
            ctx.fill();
            ctx.strokeStyle = borderColor;
            ctx.lineWidth = 1;
            ctx.stroke();

            ctx.fillStyle = textColor;
            ctx.font = "bold 11px sans-serif";
            ctx.textAlign = "center";
            ctx.textBaseline = "bottom";
            ctx.fillText(p.khz + " kHz", x, y - 8);
        });

        ctx.fillStyle = highColor;
        ctx.font = "12px sans-serif";
        ctx.textAlign = "left";
        ctx.textBaseline = "alphabetic";
        ctx.fillText("Highest tone still heard vs age", pad.l, pad.t - 10);
    }

    freqSlider.value = String(hzToSlider(DEFAULT_HZ));
    ampSlider.value = String(DEFAULT_AMP);
    updateReadouts();
    drawWave(false);
    drawAgeChart();
    window.addEventListener("resize", function () {
        drawWave(playing);
        drawAgeChart();
    });

    freqSlider.addEventListener("input", function () {
        updateReadouts();
        if (!playing) {
            drawWave(false);
        }
    });
    ampSlider.addEventListener("input", function () {
        updateReadouts();
        if (!playing) {
            drawWave(false);
        }
    });
    playBtn.addEventListener("click", startTone);
    stopBtn.addEventListener("click", stopTone);
    markBtn.addEventListener("click", markHeard);
    if (shareBtn) {
        shareBtn.addEventListener("click", openShareModal);
    }
    if (shareName) {
        shareName.addEventListener("input", updateSharePreview);
    }
    if (shareCopyBtn) {
        shareCopyBtn.addEventListener("click", copyShare);
    }
    shareCloseBtns.forEach(function (btn) {
        btn.addEventListener("click", closeShareModal);
    });
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                closeShareModal();
            }
        });
    }

    window.addEventListener("beforeunload", stopTone);
    document.addEventListener("visibilitychange", function () {
        if (document.hidden && playing) {
            stopTone();
        }
    });
})();
