(function () {
    "use strict";

    var root = document.getElementById("fourier-lab-app");
    if (!root) {
        return;
    }

    var shapeEl = root.querySelector("[data-shape]");
    var termsEl = root.querySelector("[data-terms]");
    var termsVal = root.querySelector("[data-terms-val]");
    var freqEl = root.querySelector("[data-freq]");
    var ampEl = root.querySelector("[data-amp]");
    var eqEl = root.querySelector("[data-equation]");
    var applyBtn = root.querySelector("[data-apply]");
    var resetBtn = root.querySelector("[data-reset]");
    var timeCanvas = root.querySelector("[data-time-canvas]");
    var specCanvas = root.querySelector("[data-spec-canvas]");
    var eqOut = root.querySelector("[data-eq-out]");
    var termsBody = root.querySelector("[data-terms-body]");

    var TWO_PI = Math.PI * 2;
    var SAMPLES = 512;
    var state = {
        shape: "square",
        terms: 8,
        f0: 1,
        amp: 1,
        custom: [],
    };

    function clamp(n, a, b) {
        return Math.min(b, Math.max(a, n));
    }

    function cssVar(name, fallback) {
        var v = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        return v || fallback;
    }

    function defaultCustom(nTerms) {
        var list = [];
        for (var n = 1; n <= nTerms; n++) {
            list.push({ n: n, a: 0, b: n % 2 === 1 ? 1 / n : 0 });
        }
        return list;
    }

    function parseEquation(text) {
        var list = [];
        if (!text || !String(text).trim()) {
            return list;
        }
        var parts = String(text).split(/[,;]+/);
        parts.forEach(function (part) {
            part = part.trim().toLowerCase().replace(/\s+/g, "");
            if (!part) {
                return;
            }
            var mA = part.match(/^a(\d+)=([-+]?[0-9]*\.?[0-9]+(?:\/[0-9]+)?)$/);
            var mB = part.match(/^b(\d+)=([-+]?[0-9]*\.?[0-9]+(?:\/[0-9]+)?)$/);
            var mSin = part.match(/^([-+]?[0-9]*\.?[0-9]+(?:\/[0-9]+)?)\*?sin\((\d+)\)$/);
            var mCos = part.match(/^([-+]?[0-9]*\.?[0-9]+(?:\/[0-9]+)?)\*?cos\((\d+)\)$/);
            function num(s) {
                if (s.indexOf("/") >= 0) {
                    var bits = s.split("/");
                    return Number(bits[0]) / Number(bits[1]);
                }
                return Number(s);
            }
            if (mA) {
                list.push({ n: Number(mA[1]), a: num(mA[2]), b: 0, kind: "a" });
            } else if (mB) {
                list.push({ n: Number(mB[1]), a: 0, b: num(mB[2]), kind: "b" });
            } else if (mSin) {
                list.push({ n: Number(mSin[2]), a: 0, b: num(mSin[1]), kind: "b" });
            } else if (mCos) {
                list.push({ n: Number(mCos[2]), a: num(mCos[1]), b: 0, kind: "a" });
            }
        });
        return list;
    }

    function mergeCustom(parsed, nTerms) {
        var map = {};
        for (var n = 1; n <= nTerms; n++) {
            map[n] = { n: n, a: 0, b: 0 };
        }
        parsed.forEach(function (p) {
            if (p.n < 1 || p.n > 40) {
                return;
            }
            if (!map[p.n]) {
                map[p.n] = { n: p.n, a: 0, b: 0 };
            }
            if (p.kind === "a") {
                map[p.n].a = p.a;
            } else if (p.kind === "b") {
                map[p.n].b = p.b;
            } else {
                map[p.n].a = p.a;
                map[p.n].b = p.b;
            }
        });
        return Object.keys(map)
            .map(Number)
            .sort(function (x, y) {
                return x - y;
            })
            .map(function (k) {
                return map[k];
            });
    }

    function coeffsForShape(shape, terms, amp) {
        var list = [];
        var n;
        if (shape === "sine") {
            list.push({ n: 1, a: 0, b: amp });
            for (n = 2; n <= terms; n++) {
                list.push({ n: n, a: 0, b: 0 });
            }
        } else if (shape === "square") {
            for (n = 1; n <= terms; n++) {
                list.push({
                    n: n,
                    a: 0,
                    b: n % 2 === 1 ? (4 * amp) / (Math.PI * n) : 0,
                });
            }
        } else if (shape === "saw") {
            for (n = 1; n <= terms; n++) {
                list.push({
                    n: n,
                    a: 0,
                    b: ((2 * amp) / (Math.PI * n)) * (n % 2 === 0 ? -1 : 1),
                });
            }
        } else if (shape === "triangle") {
            for (n = 1; n <= terms; n++) {
                var odd = n % 2 === 1;
                list.push({
                    n: n,
                    a: 0,
                    b: odd
                        ? ((8 * amp) / (Math.PI * Math.PI * n * n)) * (((n - 1) / 2) % 2 === 0 ? 1 : -1)
                        : 0,
                });
            }
        } else if (shape === "pulse") {
            var duty = 0.25;
            list.push({ n: 0, a: amp * duty, b: 0 });
            for (n = 1; n <= terms; n++) {
                var x = Math.PI * n * duty;
                var sinc = Math.abs(x) < 1e-9 ? 1 : Math.sin(x) / x;
                list.push({ n: n, a: 2 * amp * duty * sinc, b: 0 });
            }
        } else {
            list = state.custom.length ? state.custom.slice(0, terms) : defaultCustom(terms);
            list = list.map(function (c) {
                return { n: c.n, a: c.a * amp, b: c.b * amp };
            });
        }
        return list.filter(function (c) {
            return c.n === 0 || c.n <= terms;
        });
    }

    function seriesValue(coeffs, t, f0) {
        var y = 0;
        var w = TWO_PI * f0;
        coeffs.forEach(function (c) {
            if (c.n === 0) {
                y += c.a;
                return;
            }
            var wt = c.n * w * t;
            y += c.a * Math.cos(wt) + c.b * Math.sin(wt);
        });
        return y;
    }

    function targetValue(shape, t, f0, amp) {
        var phase = ((t * f0) % 1 + 1) % 1;
        if (shape === "sine") {
            return amp * Math.sin(TWO_PI * f0 * t);
        }
        if (shape === "square") {
            return phase < 0.5 ? amp : -amp;
        }
        if (shape === "saw") {
            return amp * (2 * phase - 1);
        }
        if (shape === "triangle") {
            return amp * (phase < 0.5 ? 4 * phase - 1 : 3 - 4 * phase);
        }
        if (shape === "pulse") {
            var duty = 0.25;
            var start = 0.5 - duty / 2;
            var end = 0.5 + duty / 2;
            return phase >= start && phase < end ? amp : 0;
        }
        return null;
    }

    function equationText(coeffs) {
        var bits = [];
        coeffs.forEach(function (c) {
            if (c.n === 0) {
                if (Math.abs(c.a) > 1e-6) {
                    bits.push(round3(c.a) + " (DC)");
                }
                return;
            }
            if (Math.abs(c.a) > 1e-6) {
                bits.push((c.a >= 0 && bits.length ? "+" : "") + round3(c.a) + "·cos(" + c.n + "ωt)");
            }
            if (Math.abs(c.b) > 1e-6) {
                bits.push((c.b >= 0 && bits.length ? "+" : "") + round3(c.b) + "·sin(" + c.n + "ωt)");
            }
        });
        return bits.length ? "f(t) ≈ " + bits.join(" ") : "f(t) ≈ 0";
    }

    function round3(n) {
        return Math.round(n * 1000) / 1000;
    }

    function readCustomFromTable() {
        if (!termsBody) {
            return state.custom;
        }
        var rows = termsBody.querySelectorAll("tr");
        var list = [];
        rows.forEach(function (row) {
            var n = Number(row.getAttribute("data-n"));
            var aIn = row.querySelector("[data-a]");
            var bIn = row.querySelector("[data-b]");
            if (!aIn || !bIn) {
                return;
            }
            list.push({
                n: n,
                a: Number(aIn.value) || 0,
                b: Number(bIn.value) || 0,
            });
        });
        return list;
    }

    function renderTermsTable(coeffs) {
        if (!termsBody) {
            return;
        }
        var html = "";
        coeffs.forEach(function (c) {
            html +=
                '<tr data-n="' +
                c.n +
                '"><td>' +
                c.n +
                '</td><td><input data-a type="number" step="0.01" value="' +
                round3(c.a) +
                '"></td><td><input data-b type="number" step="0.01" value="' +
                round3(c.b) +
                '"></td><td>' +
                round3(Math.hypot(c.a, c.b)) +
                "</td></tr>";
        });
        termsBody.innerHTML = html;
        termsBody.querySelectorAll("input").forEach(function (inp) {
            inp.addEventListener("change", function () {
                state.shape = "custom";
                if (shapeEl) {
                    shapeEl.value = "custom";
                }
                state.custom = readCustomFromTable();
                redraw();
            });
        });
    }

    function drawAxes(ctx, w, h, pad, xLabel, yLabel, colors) {
        var plotW = w - pad.l - pad.r;
        var plotH = h - pad.t - pad.b;
        ctx.strokeStyle = colors.border;
        ctx.lineWidth = 1;
        ctx.strokeRect(pad.l, pad.t, plotW, plotH);

        ctx.fillStyle = colors.muted;
        ctx.font = "11px sans-serif";
        ctx.textAlign = "center";
        ctx.textBaseline = "top";
        ctx.fillText(xLabel, pad.l + plotW / 2, h - 14);

        ctx.save();
        ctx.translate(12, pad.t + plotH / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.textBaseline = "middle";
        ctx.fillText(yLabel, 0, 0);
        ctx.restore();

        return { plotW: plotW, plotH: plotH };
    }

    function drawTimePlot(coeffs, canvasEl, opts) {
        canvasEl = canvasEl || timeCanvas;
        opts = opts || {};
        if (!canvasEl) {
            return;
        }
        var shape = opts.shape != null ? opts.shape : state.shape;
        var f0 = opts.f0 != null ? opts.f0 : state.f0;
        var amp = opts.amp != null ? opts.amp : state.amp;
        var h = opts.height || 220;
        var dpr = window.devicePixelRatio || 1;
        var w = canvasEl.clientWidth || 520;
        canvasEl.width = Math.floor(w * dpr);
        canvasEl.height = Math.floor(h * dpr);
        var ctx = canvasEl.getContext("2d");
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, w, h);

        var colors = {
            border: cssVar("--border", "#445"),
            muted: cssVar("--article-byline", "#889"),
            text: cssVar("--article-text", "#ccc"),
            accent: cssVar("--accent-2", "#30d5c8"),
            target: "#f6ad55",
        };
        var pad = { l: 44, r: 12, t: 16, b: 36 };
        var box = drawAxes(ctx, w, h, pad, "time t (s)", "amp", colors);
        var period = 1 / Math.max(f0, 0.01);
        var tMax = period * 2;
        var ys = [];
        var yTarget = [];
        var i;
        var yMin = Infinity;
        var yMax = -Infinity;
        for (i = 0; i <= SAMPLES; i++) {
            var t = (i / SAMPLES) * tMax;
            var y = seriesValue(coeffs, t, f0);
            ys.push(y);
            var yt = targetValue(shape, t, f0, amp);
            yTarget.push(yt);
            yMin = Math.min(yMin, y, yt == null ? y : yt);
            yMax = Math.max(yMax, y, yt == null ? y : yt);
        }
        if (yMax === yMin) {
            yMax = yMin + 1;
        }
        var padY = (yMax - yMin) * 0.12;
        yMin -= padY;
        yMax += padY;

        function xOf(t) {
            return pad.l + (t / tMax) * box.plotW;
        }
        function yOf(y) {
            return pad.t + (1 - (y - yMin) / (yMax - yMin)) * box.plotH;
        }

        // zero line
        if (yMin < 0 && yMax > 0) {
            ctx.strokeStyle = colors.border;
            ctx.globalAlpha = 0.5;
            ctx.setLineDash([3, 3]);
            ctx.beginPath();
            ctx.moveTo(pad.l, yOf(0));
            ctx.lineTo(pad.l + box.plotW, yOf(0));
            ctx.stroke();
            ctx.setLineDash([]);
            ctx.globalAlpha = 1;
        }

        // X ticks
        ctx.fillStyle = colors.muted;
        ctx.font = "10px sans-serif";
        ctx.textAlign = "center";
        ctx.textBaseline = "top";
        [0, period, 2 * period].forEach(function (t) {
            var x = xOf(t);
            ctx.beginPath();
            ctx.moveTo(x, pad.t + box.plotH);
            ctx.lineTo(x, pad.t + box.plotH + 4);
            ctx.strokeStyle = colors.border;
            ctx.stroke();
            ctx.fillText(round3(t) + "s", x, pad.t + box.plotH + 6);
        });

        // Y ticks
        ctx.textAlign = "right";
        ctx.textBaseline = "middle";
        [yMin, 0, yMax].forEach(function (yv) {
            if (yv < yMin || yv > yMax) {
                return;
            }
            var y = yOf(yv);
            ctx.fillText(String(round3(yv)), pad.l - 6, y);
        });

        // target (dashed) for presets
        if (shape !== "custom" && yTarget[0] != null) {
            ctx.strokeStyle = colors.target;
            ctx.lineWidth = 1.5;
            ctx.setLineDash([5, 4]);
            ctx.beginPath();
            for (i = 0; i <= SAMPLES; i++) {
                var tx = xOf((i / SAMPLES) * tMax);
                var ty = yOf(yTarget[i]);
                if (i === 0) {
                    ctx.moveTo(tx, ty);
                } else {
                    ctx.lineTo(tx, ty);
                }
            }
            ctx.stroke();
            ctx.setLineDash([]);
        }

        // series
        ctx.strokeStyle = colors.accent;
        ctx.lineWidth = 2;
        ctx.beginPath();
        for (i = 0; i <= SAMPLES; i++) {
            var sx = xOf((i / SAMPLES) * tMax);
            var sy = yOf(ys[i]);
            if (i === 0) {
                ctx.moveTo(sx, sy);
            } else {
                ctx.lineTo(sx, sy);
            }
        }
        ctx.stroke();

        ctx.fillStyle = colors.accent;
        ctx.font = "11px sans-serif";
        ctx.textAlign = "left";
        ctx.textBaseline = "alphabetic";
        ctx.fillText("Fourier series", pad.l + 6, pad.t + 12);
        if (shape !== "custom") {
            ctx.fillStyle = colors.target;
            ctx.fillText("Target shape", pad.l + 110, pad.t + 12);
        }
    }

    function drawSpectrum(coeffs, canvasEl, opts) {
        canvasEl = canvasEl || specCanvas;
        opts = opts || {};
        if (!canvasEl) {
            return;
        }
        var f0 = opts.f0 != null ? opts.f0 : state.f0;
        var terms = opts.terms != null ? opts.terms : state.terms;
        var h = opts.height || 220;
        var dpr = window.devicePixelRatio || 1;
        var w = canvasEl.clientWidth || 360;
        canvasEl.width = Math.floor(w * dpr);
        canvasEl.height = Math.floor(h * dpr);
        var ctx = canvasEl.getContext("2d");
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, w, h);

        var colors = {
            border: cssVar("--border", "#445"),
            muted: cssVar("--article-byline", "#889"),
            accent: cssVar("--accent-2", "#30d5c8"),
        };
        var pad = { l: 44, r: 12, t: 16, b: 40 };
        var box = drawAxes(ctx, w, h, pad, "freq f (Hz)", "|c|", colors);

        var mags = coeffs.map(function (c) {
            return { n: c.n, f: c.n * f0, mag: Math.hypot(c.a, c.b) };
        });
        var maxMag = 0;
        mags.forEach(function (m) {
            maxMag = Math.max(maxMag, m.mag);
        });
        if (maxMag < 1e-9) {
            maxMag = 1;
        }

        var maxN = Math.max(terms, 1);

        ctx.fillStyle = colors.muted;
        ctx.font = "10px sans-serif";
        ctx.textAlign = "right";
        ctx.textBaseline = "middle";
        [0, maxMag / 2, maxMag].forEach(function (v) {
            var y = pad.t + (1 - v / maxMag) * box.plotH;
            ctx.fillText(String(round3(v)), pad.l - 6, y);
            ctx.globalAlpha = 0.3;
            ctx.strokeStyle = colors.border;
            ctx.setLineDash([3, 3]);
            ctx.beginPath();
            ctx.moveTo(pad.l, y);
            ctx.lineTo(pad.l + box.plotW, y);
            ctx.stroke();
            ctx.setLineDash([]);
            ctx.globalAlpha = 1;
        });

        mags.forEach(function (m) {
            if (m.mag < 1e-6) {
                return;
            }
            var x = pad.l + (m.n / (maxN + 1)) * box.plotW;
            var barH = (m.mag / maxMag) * box.plotH;
            var y = pad.t + box.plotH - barH;
            ctx.strokeStyle = colors.accent;
            ctx.lineWidth = 1.25;
            ctx.lineCap = "butt";
            ctx.beginPath();
            ctx.moveTo(x, pad.t + box.plotH);
            ctx.lineTo(x, y);
            ctx.stroke();

            ctx.fillStyle = colors.muted;
            ctx.textAlign = "center";
            ctx.textBaseline = "top";
            ctx.fillText(m.n === 0 ? "DC" : String(round3(m.f)), x, pad.t + box.plotH + 6);
        });
    }

    function redraw() {
        state.terms = clamp(Number(termsEl && termsEl.value) || 8, 1, 40);
        state.f0 = clamp(Number(freqEl && freqEl.value) || 1, 0.2, 20);
        state.amp = clamp(Number(ampEl && ampEl.value) || 1, 0.1, 5);
        state.shape = (shapeEl && shapeEl.value) || "square";

        if (termsVal) {
            termsVal.textContent = String(state.terms);
        }

        if (state.shape === "custom") {
            if (!state.custom.length) {
                state.custom = defaultCustom(state.terms);
            } else {
                // extend/trim
                var map = {};
                state.custom.forEach(function (c) {
                    map[c.n] = c;
                });
                var next = [];
                for (var n = 1; n <= state.terms; n++) {
                    next.push(map[n] || { n: n, a: 0, b: 0 });
                }
                state.custom = next;
            }
        }

        var coeffs = coeffsForShape(state.shape, state.terms, state.amp);
        if (eqOut) {
            eqOut.textContent = equationText(coeffs);
        }
        renderTermsTable(
            state.shape === "custom"
                ? state.custom
                : coeffs.map(function (c) {
                      return { n: c.n, a: round3(c.a), b: round3(c.b) };
                  })
        );
        drawTimePlot(coeffs);
        drawSpectrum(coeffs);
        drawStaticDemos();
    }

    function drawStaticDemos() {
        var nodes = document.querySelectorAll("[data-fourier-demo]");
        nodes.forEach(function (canvas) {
            var shape = canvas.getAttribute("data-demo-shape") || "square";
            var view = canvas.getAttribute("data-demo-view") || "time";
            var terms = clamp(Number(canvas.getAttribute("data-demo-terms") || 9), 1, 40);
            var f0 = 1;
            var amp = 1;
            var coeffs = coeffsForShape(shape, terms, amp);
            if (view === "freq") {
                drawSpectrum(coeffs, canvas, { f0: f0, terms: terms, height: 180 });
            } else {
                drawTimePlot(coeffs, canvas, { shape: shape, f0: f0, amp: amp, height: 180 });
            }
        });
    }

    function applyEquation() {
        var parsed = parseEquation(eqEl ? eqEl.value : "");
        if (!parsed.length) {
            alert("Could not parse. Try: b1=1, b3=1/3, b5=1/5  or  1*sin(1), 0.5*sin(3)");
            return;
        }
        state.shape = "custom";
        if (shapeEl) {
            shapeEl.value = "custom";
        }
        var maxN = 1;
        parsed.forEach(function (p) {
            maxN = Math.max(maxN, p.n);
        });
        state.terms = clamp(Math.max(state.terms, maxN), 1, 40);
        if (termsEl) {
            termsEl.value = String(state.terms);
        }
        state.custom = mergeCustom(parsed, state.terms);
        redraw();
    }

    function resetLab() {
        state.shape = "square";
        state.terms = 8;
        state.f0 = 1;
        state.amp = 1;
        state.custom = defaultCustom(8);
        if (shapeEl) {
            shapeEl.value = "square";
        }
        if (termsEl) {
            termsEl.value = "8";
        }
        if (freqEl) {
            freqEl.value = "1";
        }
        if (ampEl) {
            ampEl.value = "1";
        }
        if (eqEl) {
            eqEl.value = "b1=1, b3=1/3, b5=1/5, b7=1/7";
        }
        redraw();
    }

    if (shapeEl) {
        shapeEl.addEventListener("change", redraw);
    }
    if (termsEl) {
        termsEl.addEventListener("input", redraw);
    }
    if (freqEl) {
        freqEl.addEventListener("change", redraw);
    }
    if (ampEl) {
        ampEl.addEventListener("change", redraw);
    }
    if (applyBtn) {
        applyBtn.addEventListener("click", applyEquation);
    }
    if (resetBtn) {
        resetBtn.addEventListener("click", resetLab);
    }
    window.addEventListener("resize", function () {
        redraw();
        drawStaticDemos();
    });

    resetLab();
})();
