(function () {
    "use strict";

    var root = document.getElementById("gears-calc-app");
    if (!root) {
        return;
    }

    var typeEl = root.querySelector("[data-gear-type]");
    var resetBtn = root.querySelector("[data-reset]");
    var ratioCanvas = root.querySelector("[data-ratio-canvas]");
    var eqOut = root.querySelector("[data-eq-out]");
    var typeHint = root.querySelector("[data-type-hint]");

    var fields = [
        { key: "z1", min: 8, max: 120, step: 1 },
        { key: "z2", min: 8, max: 200, step: 1 },
        { key: "module", min: 0.5, max: 20, step: 0.1 },
        { key: "phi", min: 14, max: 30, step: 0.5 },
        { key: "nin", min: 1, max: 20000, step: 1 },
        { key: "tin", min: 0.1, max: 5000, step: 0.1 },
        { key: "shaft", min: 10, max: 170, step: 1 },
        { key: "worm-starts", min: 1, max: 4, step: 1 },
    ];

    var DEG = Math.PI / 180;

    function clamp(n, a, b) {
        return Math.min(b, Math.max(a, n));
    }

    function cssVar(name, fallback) {
        var v = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        return v || fallback;
    }

    function round(n, d) {
        d = d == null ? 3 : d;
        var f = Math.pow(10, d);
        return Math.round(n * f) / f;
    }

    function syncPair(key, from) {
        var num = root.querySelector('[data-' + key + '][type="number"]');
        var range = root.querySelector('[data-' + key + '][type="range"]');
        if (!num || !range) {
            return;
        }
        if (from === "number") {
            range.value = num.value;
        } else {
            num.value = range.value;
        }
    }

    function readState() {
        function val(key, fallback) {
            var el = root.querySelector('[data-' + key + '][type="number"]');
            return el ? Number(el.value) : fallback;
        }
        return {
            type: (typeEl && typeEl.value) || "spur",
            z1: clamp(val("z1", 18), 8, 120),
            z2: clamp(val("z2", 36), 8, 200),
            m: clamp(val("module", 2), 0.5, 20),
            phi: clamp(val("phi", 20), 14, 30),
            nIn: clamp(val("nin", 1500), 1, 20000),
            tIn: clamp(val("tin", 10), 0.1, 5000),
            shaft: clamp(val("shaft", 90), 10, 170),
            wormStarts: clamp(val("worm-starts", 1), 1, 4),
        };
    }

    function compute(s) {
        var z1 = s.z1;
        var z2 = s.z2;
        var m = s.m;
        var phi = s.phi * DEG;
        var d1 = m * z1;
        var d2 = m * z2;
        var p = Math.PI * m;
        var Pd = 25.4 / m;
        var a = (d1 + d2) / 2;
        var i = z2 / z1;
        var nOut = s.nIn / i;
        var tOut = s.tIn * i;
        var gamma1 = 0;
        var gamma2 = 0;
        var note = "";

        if (s.type === "spur" || s.type === "helical" || s.type === "herringbone" || s.type === "screw") {
            note =
                s.type === "helical"
                    ? "Helical: same basic ratio as spur; helix adds axial thrust."
                    : s.type === "herringbone"
                      ? "Herringbone: double helical; opposing helixes cancel axial thrust."
                      : s.type === "screw"
                        ? "Screw / crossed helical: skew shafts; ratio still tracks tooth counts when they mesh."
                        : "Spur: parallel shafts, straight teeth.";
        } else if (s.type === "bevel" || s.type === "miter" || s.type === "spiral-bevel" || s.type === "hypoid") {
            var Sigma = s.type === "miter" ? 90 * DEG : s.shaft * DEG;
            var zA = s.type === "miter" ? z1 : z1;
            var zB = s.type === "miter" ? z1 : z2;
            if (s.type === "miter") {
                z2 = z1;
                d2 = d1;
                i = 1;
                nOut = s.nIn;
                tOut = s.tIn;
            }
            gamma1 = Math.atan((zA / zB) * Math.sin(Sigma) / (1 + (zA / zB) * Math.cos(Sigma)));
            gamma2 = Sigma - gamma1;
            a = (d1 / 2) / Math.sin(Math.max(gamma1, 1e-6));
            note =
                s.type === "miter"
                    ? "Miter: equal bevel gears, usually Σ = 90°, i = 1."
                    : s.type === "spiral-bevel"
                      ? "Spiral bevel: cone geometry like straight bevel; curved teeth for smoother mesh."
                      : s.type === "hypoid"
                        ? "Hypoid: like spiral bevel with pinion axis offset — shafts do not intersect."
                        : "Bevel: shafts meet at angle Σ. Pitch cones define γ₁ and γ₂.";
        } else if (s.type === "planetary") {
            i = 1 + z2 / z1;
            nOut = s.nIn / i;
            tOut = s.tIn * i;
            a = (d2 - d1) / 2;
            note = "Planetary (ring fixed): i ≈ 1 + Z_ring/Z_sun. Z₁=sun, Z₂=ring in this tool.";
        } else if (s.type === "worm") {
            i = z2 / s.wormStarts;
            nOut = s.nIn / i;
            tOut = s.tIn * i;
            note = "Worm drive: i ≈ Z_wheel / starts. Friction losses are real — ideal τ only.";
        } else if (s.type === "rack") {
            i = Infinity;
            nOut = 0;
            tOut = s.tIn / Math.max(d1 / 2000, 1e-6);
            a = d1 / 2;
            note = "Rack & pinion: rotation → linear. Ideal F ≈ τ / (d/2).";
        } else if (s.type === "internal") {
            a = Math.abs(d2 - d1) / 2;
            note = "Internal: same rotation sense; a = |d₂ − d₁|/2.";
        } else if (s.type === "cam" || s.type === "pawl" || s.type === "coupling") {
            i = 1;
            nOut = s.nIn;
            tOut = s.tIn;
            if (s.type === "cam") {
                note = "Cam: motion follows profile s(θ). No fixed tooth ratio.";
            } else if (s.type === "pawl") {
                note = "Pawl & ratchet: one-way step drive. Ratio is not a continuous gear i.";
            } else {
                note = "Gear coupling: torque across misaligned shafts. Not a speed reducer.";
            }
        }

        return {
            s: s,
            d1: d1,
            d2: d2,
            p: p,
            Pd: Pd,
            a: a,
            i: i,
            nOut: nOut,
            tOut: tOut,
            gamma1: (gamma1 * 180) / Math.PI,
            gamma2: (gamma2 * 180) / Math.PI,
            note: note,
        };
    }

    function setText(sel, text) {
        var el = root.querySelector(sel);
        if (el) {
            el.textContent = text;
        }
    }

    function updateReadouts(r) {
        setText("[data-out-i]", isFinite(r.i) ? round(r.i, 3) + " : 1" : "linear");
        setText("[data-out-d1]", round(r.d1, 2) + " mm");
        setText(
            "[data-out-d2]",
            r.s.type === "rack" || r.s.type === "cam" || r.s.type === "pawl" || r.s.type === "coupling"
                ? "—"
                : round(r.d2, 2) + " mm"
        );
        setText(
            "[data-out-a]",
            r.s.type === "cam" || r.s.type === "pawl" || r.s.type === "coupling" ? "—" : round(r.a, 2) + " mm"
        );
        setText("[data-out-nout]", r.s.type === "rack" ? "—" : round(r.nOut, 1) + " rpm");
        setText("[data-out-tout]", round(r.tOut, 2) + (r.s.type === "rack" ? " N" : " N·m"));
        setText("[data-out-p]", round(r.p, 3) + " mm");
        setText("[data-out-pd]", round(r.Pd, 2) + " 1/in");

        if (eqOut) {
            if (r.s.type === "bevel" || r.s.type === "spiral-bevel" || r.s.type === "hypoid") {
                eqOut.textContent =
                    "i = Z₂/Z₁ = " +
                    round(r.i, 3) +
                    " · Σ = " +
                    r.s.shaft +
                    "° · γ₁ ≈ " +
                    round(r.gamma1, 1) +
                    "° · γ₂ ≈ " +
                    round(r.gamma2, 1) +
                    "° · d = mZ" +
                    (r.s.type === "hypoid" ? " · offset axis" : "");
            } else if (r.s.type === "miter") {
                eqOut.textContent = "Miter: Z₁ = Z₂ · i = 1 · Σ = 90° · d = mZ";
            } else if (r.s.type === "planetary") {
                eqOut.textContent =
                    "i ≈ 1 + Z₂/Z₁ = " + round(r.i, 3) + " (ring fixed) · n₂ = n₁/i · τ₂ ≈ τ₁·i";
            } else if (r.s.type === "worm") {
                eqOut.textContent =
                    "i ≈ Z₂/starts = " + round(r.i, 3) + " · n₂ = n₁/i · τ₂ ≈ τ₁·i (ideal)";
            } else if (r.s.type === "rack") {
                eqOut.textContent = "v = ω·(d₁/2) · d₁ = m·Z₁ · F ≈ τ₁/(d₁/2)";
            } else if (r.s.type === "cam" || r.s.type === "pawl" || r.s.type === "coupling") {
                eqOut.textContent = r.note;
            } else {
                eqOut.textContent =
                    "i = Z₂/Z₁ = n₁/n₂ = " +
                    round(r.i, 3) +
                    " · d = mZ · a = (d₁+d₂)/2 · p = πm";
            }
        }
        if (typeHint) {
            typeHint.textContent = r.note;
        }
    }

    function prepCanvas(canvas, h) {
        var dpr = window.devicePixelRatio || 1;
        var w = canvas.clientWidth || 480;
        h = h || 240;
        canvas.width = Math.floor(w * dpr);
        canvas.height = Math.floor(h * dpr);
        var ctx = canvas.getContext("2d");
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.clearRect(0, 0, w, h);
        return { ctx: ctx, w: w, h: h };
    }

    function drawRatioChart(r) {
        if (!ratioCanvas) {
            return;
        }
        var box = prepCanvas(ratioCanvas, 240);
        var ctx = box.ctx;
        var w = box.w;
        var h = box.h;
        var border = cssVar("--border", "#445");
        var muted = cssVar("--article-byline", "#889");
        var text = cssVar("--article-text", "#ccc");
        var accent = cssVar("--accent-2", "#30d5c8");
        var pad = { l: 48, r: 14, t: 28, b: 40 };
        var plotW = w - pad.l - pad.r;
        var plotH = h - pad.t - pad.b;
        var iMax = 8;

        ctx.strokeStyle = border;
        ctx.strokeRect(pad.l, pad.t, plotW, plotH);
        ctx.fillStyle = muted;
        ctx.font = "12px sans-serif";
        ctx.textAlign = "center";
        ctx.fillText("Ideal speed vs torque trade", pad.l + plotW / 2, 16);
        ctx.fillText("ratio i = Z₂/Z₁", pad.l + plotW / 2, h - 10);
        ctx.save();
        ctx.translate(14, pad.t + plotH / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.fillText("relative magnitude", 0, 0);
        ctx.restore();

        function xOf(ii) {
            return pad.l + ((ii - 1) / (iMax - 1)) * plotW;
        }
        function yOf(v) {
            return pad.t + (1 - v / iMax) * plotH;
        }

        ctx.globalAlpha = 0.35;
        ctx.setLineDash([3, 3]);
        [1, 2, 4, 8].forEach(function (v) {
            var y = yOf(v);
            ctx.beginPath();
            ctx.moveTo(pad.l, y);
            ctx.lineTo(pad.l + plotW, y);
            ctx.strokeStyle = border;
            ctx.stroke();
            ctx.globalAlpha = 1;
            ctx.fillStyle = muted;
            ctx.textAlign = "right";
            ctx.font = "10px sans-serif";
            ctx.fillText(String(v), pad.l - 6, y + 3);
            ctx.globalAlpha = 0.35;
        });
        ctx.setLineDash([]);
        ctx.globalAlpha = 1;

        var k;
        ctx.strokeStyle = accent;
        ctx.lineWidth = 2;
        ctx.beginPath();
        for (k = 1; k <= 40; k++) {
            var ii = 1 + ((iMax - 1) * (k - 1)) / 39;
            var x = xOf(ii);
            var y = yOf((1 / ii) * iMax);
            if (k === 1) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        }
        ctx.stroke();

        ctx.strokeStyle = "#f6ad55";
        ctx.beginPath();
        for (k = 1; k <= 40; k++) {
            ii = 1 + ((iMax - 1) * (k - 1)) / 39;
            x = xOf(ii);
            y = yOf(ii);
            if (k === 1) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        }
        ctx.stroke();

        var curI = isFinite(r.i) ? clamp(r.i, 1, iMax) : 1;
        var mx = xOf(curI);
        ctx.strokeStyle = text;
        ctx.setLineDash([2, 2]);
        ctx.beginPath();
        ctx.moveTo(mx, pad.t);
        ctx.lineTo(mx, pad.t + plotH);
        ctx.stroke();
        ctx.setLineDash([]);
        ctx.fillStyle = accent;
        ctx.beginPath();
        ctx.arc(mx, yOf((1 / curI) * iMax), 4, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = "#f6ad55";
        ctx.beginPath();
        ctx.arc(mx, yOf(curI), 4, 0, Math.PI * 2);
        ctx.fill();

        ctx.font = "11px sans-serif";
        ctx.textAlign = "left";
        ctx.fillStyle = accent;
        ctx.fillText("n₂ / n₁", pad.l + 8, pad.t + 14);
        ctx.fillStyle = "#f6ad55";
        ctx.fillText("τ₂ / τ₁", pad.l + 90, pad.t + 14);
    }

    function toggleTypeFields() {
        var t = (typeEl && typeEl.value) || "spur";
        root.querySelectorAll("[data-show-for]").forEach(function (el) {
            var list = (el.getAttribute("data-show-for") || "").split(",");
            el.style.display = list.indexOf(t) >= 0 ? "" : "none";
        });
    }

    function redraw() {
        toggleTypeFields();
        var r = compute(readState());
        updateReadouts(r);
        drawRatioChart(r);
    }

    function resetCalc() {
        if (typeEl) {
            typeEl.value = "spur";
        }
        var defaults = {
            z1: "18",
            z2: "36",
            module: "2",
            phi: "20",
            nin: "1500",
            tin: "10",
            shaft: "90",
            "worm-starts": "1",
        };
        Object.keys(defaults).forEach(function (key) {
            root.querySelectorAll("[data-" + key + "]").forEach(function (el) {
                el.value = defaults[key];
            });
        });
        redraw();
    }

    fields.forEach(function (f) {
        var num = root.querySelector('[data-' + f.key + '][type="number"]');
        var range = root.querySelector('[data-' + f.key + '][type="range"]');
        if (num) {
            num.addEventListener("input", function () {
                syncPair(f.key, "number");
                redraw();
            });
        }
        if (range) {
            range.addEventListener("input", function () {
                syncPair(f.key, "range");
                redraw();
            });
        }
    });
    if (typeEl) {
        typeEl.addEventListener("change", redraw);
    }
    if (resetBtn) {
        resetBtn.addEventListener("click", resetCalc);
    }
    window.addEventListener("resize", redraw);

    resetCalc();
})();
