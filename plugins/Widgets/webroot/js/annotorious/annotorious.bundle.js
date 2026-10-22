// ../../../node_modules/@annotorious/annotorious/dist/annotorious.es.js
var To = Object.defineProperty;
var Po = (e, t, n) => t in e ? To(e, t, { enumerable: true, configurable: true, writable: true, value: n }) : e[t] = n;
var Ot = (e, t, n) => Po(e, typeof t != "symbol" ? t + "" : t, n);
function $() {
}
function Kt(e, t) {
  for (const n in t)
    e[n] = t[n];
  return (
    /** @type {T & S} */
    e
  );
}
function xn(e) {
  return e();
}
function sn() {
  return /* @__PURE__ */ Object.create(null);
}
function Se(e) {
  e.forEach(xn);
}
function se(e) {
  return typeof e == "function";
}
function ae(e, t) {
  return e != e ? t == t : e !== t || e && typeof e == "object" || typeof e == "function";
}
function Lo(e) {
  return Object.keys(e).length === 0;
}
function $n(e, ...t) {
  if (e == null) {
    for (const o of t)
      o(void 0);
    return $;
  }
  const n = e.subscribe(...t);
  return n.unsubscribe ? () => n.unsubscribe() : n;
}
function Nt(e, t, n) {
  e.$$.on_destroy.push($n(t, n));
}
function Co(e, t, n, o) {
  if (e) {
    const s = eo(e, t, n, o);
    return e[0](s);
  }
}
function eo(e, t, n, o) {
  return e[1] && o ? Kt(n.ctx.slice(), e[1](o(t))) : n.ctx;
}
function Io(e, t, n, o) {
  if (e[2] && o) {
    const s = e[2](o(n));
    if (t.dirty === void 0)
      return s;
    if (typeof s == "object") {
      const i = [], l = Math.max(t.dirty.length, s.length);
      for (let r = 0; r < l; r += 1)
        i[r] = t.dirty[r] | s[r];
      return i;
    }
    return t.dirty | s;
  }
  return t.dirty;
}
function Oo(e, t, n, o, s, i) {
  if (s) {
    const l = eo(t, n, o, i);
    e.p(l, s);
  }
}
function No(e) {
  if (e.ctx.length > 32) {
    const t = [], n = e.ctx.length / 32;
    for (let o = 0; o < n; o++)
      t[o] = -1;
    return t;
  }
  return -1;
}
function rn(e) {
  const t = {};
  for (const n in e)
    n[0] !== "$" && (t[n] = e[n]);
  return t;
}
function Ze(e) {
  return e ?? "";
}
function G(e, t) {
  e.appendChild(t);
}
function Y(e, t, n) {
  e.insertBefore(t, n || null);
}
function R(e) {
  e.parentNode && e.parentNode.removeChild(e);
}
function Ge(e, t) {
  for (let n = 0; n < e.length; n += 1)
    e[n] && e[n].d(t);
}
function C(e) {
  return document.createElementNS("http://www.w3.org/2000/svg", e);
}
function to(e) {
  return document.createTextNode(e);
}
function we() {
  return to(" ");
}
function Me() {
  return to("");
}
function Q(e, t, n, o) {
  return e.addEventListener(t, n, o), () => e.removeEventListener(t, n, o);
}
function c(e, t, n) {
  n == null ? e.removeAttribute(t) : e.getAttribute(t) !== n && e.setAttribute(t, n);
}
function Do(e) {
  return Array.from(e.childNodes);
}
function Te(e, t, n) {
  e.classList.toggle(t, !!n);
}
function Ro(e, t, { bubbles: n = false, cancelable: o = false } = {}) {
  return new CustomEvent(e, { detail: t, bubbles: n, cancelable: o });
}
var nt;
function tt(e) {
  nt = e;
}
function no() {
  if (!nt)
    throw new Error("Function called outside component initialization");
  return nt;
}
function Be(e) {
  no().$$.on_mount.push(e);
}
function Ve() {
  const e = no();
  return (t, n, { cancelable: o = false } = {}) => {
    const s = e.$$.callbacks[t];
    if (s) {
      const i = Ro(
        /** @type {string} */
        t,
        n,
        { cancelable: o }
      );
      return s.slice().forEach((l) => {
        l.call(e, i);
      }), !i.defaultPrevented;
    }
    return true;
  };
}
function de(e, t) {
  const n = e.$$.callbacks[t.type];
  n && n.slice().forEach((o) => o.call(this, t));
}
var Ke = [];
var vt = [];
var Je = [];
var ln = [];
var oo = /* @__PURE__ */ Promise.resolve();
var Wt = false;
function so() {
  Wt || (Wt = true, oo.then(ro));
}
function io() {
  return so(), oo;
}
function Zt(e) {
  Je.push(e);
}
var Dt = /* @__PURE__ */ new Set();
var Fe = 0;
function ro() {
  if (Fe !== 0)
    return;
  const e = nt;
  do {
    try {
      for (; Fe < Ke.length; ) {
        const t = Ke[Fe];
        Fe++, tt(t), Yo(t.$$);
      }
    } catch (t) {
      throw Ke.length = 0, Fe = 0, t;
    }
    for (tt(null), Ke.length = 0, Fe = 0; vt.length; )
      vt.pop()();
    for (let t = 0; t < Je.length; t += 1) {
      const n = Je[t];
      Dt.has(n) || (Dt.add(n), n());
    }
    Je.length = 0;
  } while (Ke.length);
  for (; ln.length; )
    ln.pop()();
  Wt = false, Dt.clear(), tt(e);
}
function Yo(e) {
  if (e.fragment !== null) {
    e.update(), Se(e.before_update);
    const t = e.dirty;
    e.dirty = [-1], e.fragment && e.fragment.p(e.ctx, t), e.after_update.forEach(Zt);
  }
}
function Bo(e) {
  const t = [], n = [];
  Je.forEach((o) => e.indexOf(o) === -1 ? t.push(o) : n.push(o)), n.forEach((o) => o()), Je = t;
}
var yt = /* @__PURE__ */ new Set();
var He;
function be() {
  He = {
    r: 0,
    c: [],
    p: He
    // parent group
  };
}
function Ee() {
  He.r || Se(He.c), He = He.p;
}
function V(e, t) {
  e && e.i && (yt.delete(e), e.i(t));
}
function H(e, t, n, o) {
  if (e && e.o) {
    if (yt.has(e))
      return;
    yt.add(e), He.c.push(() => {
      yt.delete(e), o && (n && e.d(1), o());
    }), e.o(t);
  } else
    o && o();
}
function ve(e) {
  return (e == null ? void 0 : e.length) !== void 0 ? e : Array.from(e);
}
function ce(e) {
  e && e.c();
}
function re(e, t, n) {
  const { fragment: o, after_update: s } = e.$$;
  o && o.m(t, n), Zt(() => {
    const i = e.$$.on_mount.map(xn).filter(se);
    e.$$.on_destroy ? e.$$.on_destroy.push(...i) : Se(i), e.$$.on_mount = [];
  }), s.forEach(Zt);
}
function le(e, t) {
  const n = e.$$;
  n.fragment !== null && (Bo(n.after_update), Se(n.on_destroy), n.fragment && n.fragment.d(t), n.on_destroy = n.fragment = null, n.ctx = []);
}
function Vo(e, t) {
  e.$$.dirty[0] === -1 && (Ke.push(e), so(), e.$$.dirty.fill(0)), e.$$.dirty[t / 31 | 0] |= 1 << t % 31;
}
function _e(e, t, n, o, s, i, l = null, r = [-1]) {
  const a2 = nt;
  tt(e);
  const f = e.$$ = {
    fragment: null,
    ctx: [],
    // state
    props: i,
    update: $,
    not_equal: s,
    bound: sn(),
    // lifecycle
    on_mount: [],
    on_destroy: [],
    on_disconnect: [],
    before_update: [],
    after_update: [],
    context: new Map(t.context || (a2 ? a2.$$.context : [])),
    // everything else
    callbacks: sn(),
    dirty: r,
    skip_bound: false,
    root: t.target || a2.$$.root
  };
  l && l(f.root);
  let d = false;
  if (f.ctx = n ? n(e, t.props || {}, (u, h, ...g) => {
    const p = g.length ? g[0] : h;
    return f.ctx && s(f.ctx[u], f.ctx[u] = p) && (!f.skip_bound && f.bound[u] && f.bound[u](p), d && Vo(e, u)), h;
  }) : [], f.update(), d = true, Se(f.before_update), f.fragment = o ? o(f.ctx) : false, t.target) {
    if (t.hydrate) {
      const u = Do(t.target);
      f.fragment && f.fragment.l(u), u.forEach(R);
    } else
      f.fragment && f.fragment.c();
    t.intro && V(e.$$.fragment), re(e, t.target, t.anchor), ro();
  }
  tt(a2);
}
var ye = class {
  constructor() {
    Ot(this, "$$");
    Ot(this, "$$set");
  }
  /** @returns {void} */
  $destroy() {
    le(this, 1), this.$destroy = $;
  }
  /**
   * @template {Extract<keyof Events, string>} K
   * @param {K} type
   * @param {((e: Events[K]) => void) | null | undefined} callback
   * @returns {() => void}
   */
  $on(t, n) {
    if (!se(n))
      return $;
    const o = this.$$.callbacks[t] || (this.$$.callbacks[t] = []);
    return o.push(n), () => {
      const s = o.indexOf(n);
      s !== -1 && o.splice(s, 1);
    };
  }
  /**
   * @param {Partial<Props>} props
   * @returns {void}
   */
  $set(t) {
    this.$$set && !Lo(t) && (this.$$.skip_bound = true, this.$$set(t), this.$$.skip_bound = false);
  }
};
var Uo = "4";
typeof window < "u" && (window.__svelte || (window.__svelte = { v: /* @__PURE__ */ new Set() })).v.add(Uo);
var x = /* @__PURE__ */ ((e) => (e.ELLIPSE = "ELLIPSE", e.MULTIPOLYGON = "MULTIPOLYGON", e.POLYGON = "POLYGON", e.POLYLINE = "POLYLINE", e.RECTANGLE = "RECTANGLE", e.LINE = "LINE", e))(x || {});
function Xo(e) {
  return e && e.__esModule && Object.prototype.hasOwnProperty.call(e, "default") ? e.default : e;
}
var lo = { exports: {} };
(function(e) {
  (function() {
    function t(r, a2) {
      var f = r.x - a2.x, d = r.y - a2.y;
      return f * f + d * d;
    }
    function n(r, a2, f) {
      var d = a2.x, u = a2.y, h = f.x - d, g = f.y - u;
      if (h !== 0 || g !== 0) {
        var p = ((r.x - d) * h + (r.y - u) * g) / (h * h + g * g);
        p > 1 ? (d = f.x, u = f.y) : p > 0 && (d += h * p, u += g * p);
      }
      return h = r.x - d, g = r.y - u, h * h + g * g;
    }
    function o(r, a2) {
      for (var f = r[0], d = [f], u, h = 1, g = r.length; h < g; h++)
        u = r[h], t(u, f) > a2 && (d.push(u), f = u);
      return f !== u && d.push(u), d;
    }
    function s(r, a2, f, d, u) {
      for (var h = d, g, p = a2 + 1; p < f; p++) {
        var y = n(r[p], r[a2], r[f]);
        y > h && (g = p, h = y);
      }
      h > d && (g - a2 > 1 && s(r, a2, g, d, u), u.push(r[g]), f - g > 1 && s(r, g, f, d, u));
    }
    function i(r, a2) {
      var f = r.length - 1, d = [r[0]];
      return s(r, 0, f, a2, d), d.push(r[f]), d;
    }
    function l(r, a2, f) {
      if (r.length <= 2)
        return r;
      var d = a2 !== void 0 ? a2 * a2 : 1;
      return r = f ? r : o(r, d), r = i(r, d), r;
    }
    e.exports = l, e.exports.default = l;
  })();
})(lo);
var Ho = lo.exports;
var Go = /* @__PURE__ */ Xo(Ho);
var $t = {};
var Qe = (e, t) => $t[e] = t;
var Jt = (e) => $t[e.type].area(e);
var jo = (e, t, n, o) => $t[e.type].intersects(e, t, n, o);
var ue = (e) => {
  let t = 1 / 0, n = 1 / 0, o = -1 / 0, s = -1 / 0;
  return e.forEach(([i, l]) => {
    t = Math.min(t, i), n = Math.min(n, l), o = Math.max(o, i), s = Math.max(s, l);
  }), { minX: t, minY: n, maxX: o, maxY: s };
};
var At = (e) => {
  let t = 0, n = e.length - 1;
  for (let o = 0; o < e.length; o++)
    t += (e[n][0] + e[o][0]) * (e[n][1] - e[o][1]), n = o;
  return Math.abs(0.5 * t);
};
var St = (e, t, n) => {
  let o = false;
  for (let s = 0, i = e.length - 1; s < e.length; i = s++) {
    const l = e[s][0], r = e[s][1], a2 = e[i][0], f = e[i][1];
    r > n != f > n && t < (a2 - l) * (n - r) / (f - r) + l && (o = !o);
  }
  return o;
};
var Fo = (e, t = true) => {
  let n = "M ";
  return e.forEach(([o, s], i) => {
    i === 0 ? n += `${o},${s}` : n += ` L ${o},${s}`;
  }), t && (n += " Z"), n;
};
var ao = (e, t = 1) => {
  const n = e.map(([o, s]) => ({ x: o, y: s }));
  return Go(n, t, true).map((o) => [o.x, o.y]);
};
var wt = (e, t) => {
  const n = Math.abs(t[0] - e[0]), o = Math.abs(t[1] - e[1]);
  return Math.sqrt(Math.pow(n, 2) + Math.pow(o, 2));
};
var zo = {
  area: (e) => Math.PI * e.geometry.rx * e.geometry.ry,
  intersects: (e, t, n) => {
    const { cx: o, cy: s, rx: i, ry: l } = e.geometry, r = 0, a2 = Math.cos(r), f = Math.sin(r), d = t - o, u = n - s, h = a2 * d + f * u, g = f * d - a2 * u;
    return h * h / (i * i) + g * g / (l * l) <= 1;
  }
};
Qe(x.ELLIPSE, zo);
var qo = {
  area: (e) => 0,
  intersects: (e, t, n, o = 2) => {
    const [[s, i], [l, r]] = e.geometry.points, a2 = Math.abs((r - i) * t - (l - s) * n + l * i - r * s), f = wt([s, i], [l, r]);
    return a2 / f <= o;
  }
};
Qe(x.LINE, qo);
var Ko = {
  area: (e) => {
    const { polygons: t } = e.geometry;
    return t.reduce((n, o) => {
      const [s, ...i] = o.rings, l = At(s.points), r = i.reduce((a2, f) => a2 + At(f.points), 0);
      return n + l - r;
    }, 0);
  },
  intersects: (e, t, n) => {
    const { polygons: o } = e.geometry;
    for (const s of o) {
      const [i, ...l] = s.rings;
      if (St(i.points, t, n)) {
        let r = false;
        for (const a2 of l)
          if (St(a2.points, t, n)) {
            r = true;
            break;
          }
        if (!r)
          return true;
      }
    }
    return false;
  }
};
var bt = (e) => {
  const t = e.reduce((n, o) => [...n, ...o.rings[0].points], []);
  return ue(t);
};
var Pe = (e) => e.rings.map((n) => Fo(n.points)).join(" ");
var Wo = (e) => e.polygons.reduce((t, n) => [
  ...t,
  ...n.rings.reduce((o, s) => [...o, ...s.points], [])
], []);
var Zr = (e, t = 1) => {
  const n = e.geometry.polygons.map((s) => {
    const i = s.rings.map((r) => {
      const a2 = ao(r.points, t);
      return {
        ...r,
        points: a2
      };
    }), l = ue(i[0].points);
    return {
      ...s,
      rings: i,
      bounds: l
    };
  }), o = bt(n);
  return {
    ...e,
    geometry: {
      ...e.geometry,
      polygons: n,
      bounds: o
    }
  };
};
Qe(x.MULTIPOLYGON, Ko);
var Zo = {
  area: (e) => {
    const t = e.geometry.points;
    return At(t);
  },
  intersects: (e, t, n) => {
    const o = e.geometry.points;
    return St(o, t, n);
  }
};
var Jr = (e, t = 1) => {
  const n = ao(e.geometry.points, t), o = ue(n);
  return {
    ...e,
    geometry: {
      ...e.geometry,
      bounds: o,
      points: n
    }
  };
};
Qe(x.POLYGON, Zo);
var Jo = {
  area: (e) => {
    const t = e.geometry;
    if (!t.closed || t.points.length < 3)
      return 0;
    const n = Qt(t.points, t.closed);
    return At(n);
  },
  intersects: (e, t, n, o = 2) => {
    const s = e.geometry;
    if (s.closed) {
      const i = Qt(s.points, s.closed);
      return St(i, t, n);
    } else
      return Qo(s, [t, n], o);
  }
};
var Qt = (e, t = false) => {
  const n = [];
  for (let o = 0; o < e.length; o++) {
    const s = e[o], i = e[(o + 1) % e.length];
    if (n.push(s.point), (o < e.length - 1 || t) && (s.type === "CURVE" || i.type == "CURVE")) {
      const r = co(
        s.point,
        s.type === "CURVE" && s.outHandle || s.point,
        i.type === "CURVE" && i.inHandle || i.point,
        i.point,
        10
        // number of approximation segments
      );
      n.push(...r.slice(1));
    }
  }
  return n;
};
var co = (e, t, n, o, s = 10) => {
  const i = [];
  for (let l = 0; l <= s; l++) {
    const r = l / s, a2 = Math.pow(1 - r, 3) * e[0] + 3 * Math.pow(1 - r, 2) * r * t[0] + 3 * (1 - r) * Math.pow(r, 2) * n[0] + Math.pow(r, 3) * o[0], f = Math.pow(1 - r, 3) * e[1] + 3 * Math.pow(1 - r, 2) * r * t[1] + 3 * (1 - r) * Math.pow(r, 2) * n[1] + Math.pow(r, 3) * o[1];
    i.push([a2, f]);
  }
  return i;
};
var Qo = (e, t, n) => {
  for (let o = 0; o < e.points.length - 1; o++) {
    const s = e.points[o], i = e.points[o + 1];
    if (s.type === "CURVE" || i.type === "CURVE") {
      const r = co(
        s.point,
        s.type === "CURVE" && s.outHandle || s.point,
        i.type === "CURVE" && i.inHandle || i.point,
        i.point,
        20
        // TODO make configurable? Based on scale factor? Length?
      );
      for (let a2 = 0; a2 < r.length - 1; a2++)
        if (an(t, r[a2], r[a2 + 1]) <= n)
          return true;
    } else if (an(t, s.point, i.point) <= n)
      return true;
  }
  return false;
};
var an = (e, t, n) => {
  const [o, s] = e, [i, l] = t, [r, a2] = n, f = r - i, d = a2 - l, u = Math.sqrt(f * f + d * d);
  if (u === 0)
    return Math.sqrt((o - i) * (o - i) + (s - l) * (s - l));
  const h = ((o - i) * f + (s - l) * d) / (u * u);
  return h <= 0 ? Math.sqrt((o - i) * (o - i) + (s - l) * (s - l)) : h >= 1 ? Math.sqrt((o - r) * (o - r) + (s - a2) * (s - a2)) : Math.abs((a2 - l) * o - (r - i) * s + r * l - a2 * i) / u;
};
var fo = (e) => {
  if (!e.points || e.points.length === 0)
    return "";
  const t = [], n = e.points[0];
  t.push(`M ${n.point[0]} ${n.point[1]}`);
  for (let o = 1; o < e.points.length; o++) {
    const s = e.points[o], i = e.points[o - 1];
    if (s.type === "CURVE" || i.type === "CURVE") {
      const l = i.type === "CURVE" && i.outHandle || i.point, r = s.type === "CURVE" && s.inHandle || s.point, a2 = s.point;
      t.push(`C ${l[0]} ${l[1]} ${r[0]} ${r[1]} ${a2[0]} ${a2[1]}`);
    } else
      t.push(`L ${s.point[0]} ${s.point[1]}`);
  }
  if (e.closed) {
    const o = e.points[e.points.length - 1], s = e.points[0];
    if (o.type === "CURVE" || s.type === "CURVE") {
      const l = o.outHandle || o.point, r = s.inHandle || s.point, a2 = s.point;
      t.push(`C ${l[0]} ${l[1]} ${r[0]} ${r[1]} ${a2[0]} ${a2[1]}`);
    }
    t.push("Z");
  }
  return t.join(" ");
};
Qe(x.POLYLINE, Jo);
var xo = {
  area: (e) => e.geometry.w * e.geometry.h,
  intersects: (e, t, n) => {
    const o = e.geometry;
    if (o.rot) {
      const s = o.x + o.w / 2, i = o.y + o.h / 2, l = t - s, r = n - i, a2 = Math.cos(o.rot), f = Math.sin(o.rot), d = l * a2 + r * f, u = -l * f + r * a2;
      return d >= -o.w / 2 && d <= o.w / 2 && u >= -o.h / 2 && u <= o.h / 2;
    } else
      return t >= o.x && t <= o.x + o.w && n >= o.y && n <= o.y + o.h;
  }
};
Qe(x.RECTANGLE, xo);
var Mt = (e) => Et(e.target);
var Et = (e) => {
  var t, n;
  return (e == null ? void 0 : e.annotation) !== void 0 && ((n = (t = e == null ? void 0 : e.selector) == null ? void 0 : t.geometry) == null ? void 0 : n.bounds) !== void 0;
};
var Re = "-?\\d+(?:\\.\\d+)?(?:[eE][+-]?\\d+)?";
var uo = 512;
var $o = (e) => (e == null ? void 0 : e.type) === "FragmentSelector" ? true : typeof e == "string" ? e.length > uo || e.indexOf("#") < 0 ? false : new RegExp(
  `#xywh=((?:pixel|percent):)?(${Re}),(${Re}),(${Re}),(${Re})$`,
  "i"
).test(e) : false;
var es = (e, t = false) => {
  const n = typeof e == "string" ? e : e.value;
  if (n.length > uo)
    throw new Error("Fragment too long: " + n);
  const s = new RegExp(
    `(xywh)=((?:pixel|percent))?:?(${Re}),(${Re}),(${Re}),(${Re})$`
  ).exec(n);
  if (!s)
    throw new Error("Not a MediaFragment: " + n);
  const [i, l, r, a2, f, d, u] = s;
  if (l !== "xywh")
    throw new Error("Unsupported MediaFragment: " + n);
  if (r && r !== "pixel")
    throw new Error(`Unsupported MediaFragment unit: ${r}`);
  const [h, g, p, y] = [a2, f, d, u].map(parseFloat);
  return {
    type: x.RECTANGLE,
    geometry: {
      x: h,
      y: g,
      w: p,
      h: y,
      rot: 0,
      bounds: {
        minX: h,
        minY: t ? g - y : g,
        maxX: h + p,
        maxY: t ? g : g + y
      }
    }
  };
};
var ts = (e) => {
  const { x: t, y: n, w: o, h: s } = e;
  return {
    type: "FragmentSelector",
    conformsTo: "http://www.w3.org/TR/media-frags/",
    value: `xywh=pixel:${t},${n},${o},${s}`
  };
};
var ho = "http://www.w3.org/2000/svg";
var cn = (e) => {
  const t = (o) => {
    Array.from(o.attributes).forEach((s) => {
      s.name.startsWith("on") && o.removeAttribute(s.name);
    });
  }, n = e.getElementsByTagName("script");
  return Array.from(n).reverse().forEach((o) => o.parentNode.removeChild(o)), Array.from(e.querySelectorAll("*")).forEach(t), e;
};
var ns = (e) => {
  const o = new XMLSerializer().serializeToString(e.documentElement).replace("<svg>", `<svg xmlns="${ho}">`);
  return new DOMParser().parseFromString(o, "image/svg+xml").documentElement;
};
var it = (e) => {
  const n = new DOMParser().parseFromString(e, "image/svg+xml"), o = n.lookupPrefix(ho), s = n.lookupNamespaceURI(null);
  return o || s ? cn(n).firstChild : cn(ns(n)).firstChild;
};
var os = (e) => {
  const t = go(e), n = [];
  let o = [], s = [0, 0];
  for (const i of t)
    switch (i.type.toUpperCase()) {
      case "M":
        o.length > 0 && (n.push({ points: o }), o = []), s = [i.args[0], i.args[1]], o.push([...s]);
        break;
      case "L":
        s = [i.args[0], i.args[1]], o.push([...s]);
        break;
      case "H":
        s = [i.args[0], s[1]], o.push([...s]);
        break;
      case "V":
        s = [s[0], i.args[0]], o.push([...s]);
        break;
      case "C":
        s = [i.args[4], i.args[5]], o.push([...s]);
        break;
      case "Z":
        break;
      default:
        console.warn(`Unsupported SVG path command: ${i.type}`);
        break;
    }
  if (o.length > 2 && n.push({ points: o }), n.length > 0) {
    const i = ue(n[0].points);
    return { rings: n, bounds: i };
  }
};
var ss = (e) => {
  const { point: t, inHandle: n, outHandle: o } = e;
  if (!n || !o)
    return false;
  const s = n[0] - t[0], i = n[1] - t[1], l = o[0] - t[0], r = o[1] - t[1], a2 = s * r - i * l;
  return Math.abs(a2) < 0.01;
};
var is = (e) => {
  const t = go(e);
  let n = [], o = [0, 0], s = false;
  for (let l = 0; l < t.length; l++) {
    const r = t[l];
    switch (r.type.toUpperCase()) {
      case "M":
        o = [r.args[0], r.args[1]], n.push({
          type: "CORNER",
          point: [...o]
        });
        break;
      case "L":
        o = [r.args[0], r.args[1]], n.push({
          type: "CORNER",
          point: [...o]
        });
        break;
      case "C":
        const a2 = [r.args[0], r.args[1]], f = [r.args[2], r.args[3]], d = [r.args[4], r.args[5]];
        if (n.length > 0) {
          const h = n[n.length - 1];
          (a2[0] !== h.point[0] || a2[1] !== h.point[1]) && (h.type = "CURVE", h.outHandle = a2);
        }
        const u = {
          type: f[0] !== d[0] || f[1] !== d[1] ? "CURVE" : "CORNER",
          point: d
        };
        u.type === "CURVE" && (u.inHandle = f), n.push(u), o = d;
        break;
      case "Z":
        s = true;
        break;
      default:
        console.warn(`Unsupported SVG path command: ${r.type}`);
        break;
    }
  }
  n = n.map((l) => ss(l) ? { ...l, locked: true } : l);
  const i = ue(Qt(n, s));
  return {
    points: n,
    closed: s,
    bounds: i
  };
};
var go = (e) => {
  const t = [], n = e.replace(/,/g, " ").trim(), o = /([MmLlHhVvCcZz])\s*([^MmLlHhVvCcZz]*)/g;
  let s;
  for (; (s = o.exec(n)) !== null; ) {
    const [, i, l] = s, r = l.trim() === "" ? [] : l.trim().split(/\s+/).map(Number).filter((a2) => !isNaN(a2));
    t.push({
      type: i,
      args: r
    });
  }
  return t;
};
var rs = (e) => {
  const [t, n, o] = e.match(/(<polygon points=["|'])([^("|')]*)/) || [], s = o.split(" ").map((i) => i.split(",").map(parseFloat));
  return {
    type: x.POLYGON,
    geometry: {
      points: s,
      bounds: ue(s)
    }
  };
};
var ls = (e) => {
  const t = it(e), n = parseFloat(t.getAttribute("cx")), o = parseFloat(t.getAttribute("cy")), s = parseFloat(t.getAttribute("rx")), i = parseFloat(t.getAttribute("ry")), l = {
    minX: n - s,
    minY: o - i,
    maxX: n + s,
    maxY: o + i
  };
  return {
    type: x.ELLIPSE,
    geometry: {
      cx: n,
      cy: o,
      rx: s,
      ry: i,
      bounds: l
    }
  };
};
var as = (e) => {
  const t = it(e), n = parseFloat(t.getAttribute("x1")), o = parseFloat(t.getAttribute("x2")), s = parseFloat(t.getAttribute("y1")), i = parseFloat(t.getAttribute("y2")), l = {
    minX: Math.min(n, o),
    minY: Math.min(s, i),
    maxX: Math.max(n, o),
    maxY: Math.max(s, i)
  };
  return {
    type: x.LINE,
    geometry: {
      points: [[n, s], [o, i]],
      bounds: l
    }
  };
};
var cs = (e) => {
  const t = it(e), n = t.nodeName === "path" ? t : Array.from(t.querySelectorAll("path"))[0], o = n == null ? void 0 : n.getAttribute("d");
  if (!o)
    throw new Error("Could not parse SVG path");
  const s = is(o);
  if (!s)
    throw new Error("Could not parse SVG path");
  return {
    type: x.POLYLINE,
    geometry: s
  };
};
var fs = (e) => {
  const t = it(e), n = t.nodeName === "rect" ? t : Array.from(t.querySelectorAll("rect"))[0];
  if (!n)
    throw new Error("Could not parse SVG rect");
  const o = parseFloat(n.getAttribute("x")), s = parseFloat(n.getAttribute("y")), i = parseFloat(n.getAttribute("width")), l = parseFloat(n.getAttribute("height")), r = n.getAttribute("transform");
  let a2 = 0;
  if (r && r.startsWith("rotate(")) {
    const p = r.match(/rotate\(([^)]+)\)/);
    p && (a2 = p[1].split(/\s+/).map(parseFloat)[0] * Math.PI / 180);
  }
  const f = o + i / 2, d = s + l / 2, h = [
    [o, s],
    [o + i, s],
    [o + i, s + l],
    [o, s + l]
  ].map(([p, y]) => {
    const S = p - f, w = y - d, A = Math.cos(a2), _ = Math.sin(a2);
    return [
      f + S * A - w * _,
      d + S * _ + w * A
    ];
  }), g = ue(h);
  return {
    type: x.RECTANGLE,
    geometry: {
      x: o,
      y: s,
      w: i,
      h: l,
      rot: a2,
      bounds: g
    }
  };
};
var us = (e) => {
  const t = it(e), s = (t.nodeName === "path" ? [t] : Array.from(t.querySelectorAll("path"))).map((a2) => a2.getAttribute("d") || "").map((a2) => os(a2)).filter(Boolean), i = s.reduce((a2, f) => [...a2, ...f.rings[0].points], []), l = ue(i);
  return s.length === 1 && s[0].rings.length === 1 ? {
    type: x.POLYGON,
    geometry: {
      points: i,
      bounds: l
    }
  } : {
    type: x.MULTIPOLYGON,
    geometry: {
      polygons: s,
      bounds: l
    }
  };
};
var ds = (e) => {
  const t = typeof e == "string" ? e : e.value;
  if (t.includes("<polygon points="))
    return rs(t);
  if (t.includes("<path ") && (t.includes(" C ") || !t.includes("Z")))
    return cs(t);
  if (t.includes("<path "))
    return us(t);
  if (t.includes("<ellipse "))
    return ls(t);
  if (t.includes("<line "))
    return as(t);
  if (t.includes("<rect "))
    return fs(t);
  throw "Unsupported SVG shape: " + t;
};
var hs = (e) => `<g>${e.polygons.map((n) => `<path fill-rule="evenodd" d="${Pe(n)}" />`).join("")}</g>`;
var gs = (e) => {
  let t;
  switch (e.type) {
    case x.RECTANGLE: {
      const n = e.geometry, { x: o, y: s, w: i, h: l, rot: r } = n;
      if (!r)
        t = `<svg><rect x="${o}" y="${s}" width="${i}" height="${l}" /></svg>`;
      else {
        const a2 = o + i / 2, f = s + l / 2, d = (r ?? 0) * 180 / Math.PI;
        t = `<svg><rect x="${o}" y="${s}" width="${i}" height="${l}" transform="rotate(${d} ${a2} ${f})" /></svg>`;
      }
      break;
    }
    case x.POLYGON: {
      const n = e.geometry, { points: o } = n;
      t = `<svg><polygon points="${o.map((s) => s.join(",")).join(" ")}" /></svg>`;
      break;
    }
    case x.ELLIPSE: {
      const n = e.geometry;
      t = `<svg><ellipse cx="${n.cx}" cy="${n.cy}" rx="${n.rx}" ry="${n.ry}" /></svg>`;
      break;
    }
    case x.MULTIPOLYGON: {
      const n = e.geometry;
      t = `<svg>${hs(n)}</svg>`;
      break;
    }
    case x.LINE: {
      const n = e.geometry, [[o, s], [i, l]] = n.points;
      t = `<svg><line x1="${o}" y1="${s}" x2="${i}" y2="${l}" /></svg>`;
      break;
    }
    case x.POLYLINE:
      t = `<svg><path d="${fo(e.geometry)}" /></svg>`;
  }
  if (t)
    return { type: "SvgSelector", value: t };
  throw `Unsupported shape type: ${e.type}`;
};
var me = [];
for (let e = 0; e < 256; ++e)
  me.push((e + 256).toString(16).slice(1));
function ms(e, t = 0) {
  return (me[e[t + 0]] + me[e[t + 1]] + me[e[t + 2]] + me[e[t + 3]] + "-" + me[e[t + 4]] + me[e[t + 5]] + "-" + me[e[t + 6]] + me[e[t + 7]] + "-" + me[e[t + 8]] + me[e[t + 9]] + "-" + me[e[t + 10]] + me[e[t + 11]] + me[e[t + 12]] + me[e[t + 13]] + me[e[t + 14]] + me[e[t + 15]]).toLowerCase();
}
var ps = new Uint8Array(16);
function _s() {
  return crypto.getRandomValues(ps);
}
function mo(e, t, n) {
  return crypto.randomUUID ? crypto.randomUUID() : ys(e);
}
function ys(e, t, n) {
  var s;
  e = e || {};
  const o = e.random ?? ((s = e.rng) == null ? void 0 : s.call(e)) ?? _s();
  if (o.length < 16)
    throw new Error("Random bytes length must be >= 16");
  return o[6] = o[6] & 15 | 64, o[8] = o[8] & 63 | 128, ms(o);
}
var fn = Object.prototype.hasOwnProperty;
function Ye(e, t) {
  var n, o;
  if (e === t)
    return true;
  if (e && t && (n = e.constructor) === t.constructor) {
    if (n === Date)
      return e.getTime() === t.getTime();
    if (n === RegExp)
      return e.toString() === t.toString();
    if (n === Array) {
      if ((o = e.length) === t.length)
        for (; o-- && Ye(e[o], t[o]); )
          ;
      return o === -1;
    }
    if (!n || typeof e == "object") {
      o = 0;
      for (n in e)
        if (fn.call(e, n) && ++o && !fn.call(t, n) || !(n in t) || !Ye(e[n], t[n]))
          return false;
      return Object.keys(t).length === o;
    }
  }
  return e !== e && t !== t;
}
var ws = Symbol("clean");
var ke = [];
var De = 0;
var gt = 4;
var bs = globalThis.nanostoresGlobal || (globalThis.nanostoresGlobal = { epoch: 0 });
var en = /* @__NO_SIDE_EFFECTS__ */ (e) => {
  let t = [], n = {
    get() {
      return n.lc || n.listen(() => {
      })(), n.value;
    },
    init: e,
    lc: 0,
    listen(o) {
      return n.lc = t.push(o), () => {
        for (let i = De + gt; i < ke.length; )
          ke[i] === o ? ke.splice(i, gt) : i += gt;
        let s = t.indexOf(o);
        ~s && (t.splice(s, 1), --n.lc || n.off());
      };
    },
    notify(o, s) {
      bs.epoch++;
      let i = !ke.length;
      for (let l of t)
        ke.push(l, n.value, o, s);
      if (i) {
        for (De = 0; De < ke.length; De += gt)
          ke[De](
            ke[De + 1],
            ke[De + 2],
            ke[De + 3]
          );
        ke.length = 0;
      }
    },
    /* It will be called on last listener unsubscribing.
       We will redefine it in onMount and onStop. */
    off() {
    },
    set(o) {
      let s = n.value;
      s !== o && (n.value = o, n.notify(s));
    },
    subscribe(o) {
      let s = n.listen(o);
      return o(n.value), s;
    },
    value: e
  };
  return n[ws] = () => {
    t = [], n.lc = 0, n.off();
  }, n;
};
var Es = (e) => {
  const t = /* @__PURE__ */ en(null);
  return e.observe(({ changes: n }) => {
    const o = t.get();
    if (o) {
      (n.deleted || []).some((i) => i.id === o) && t.set(null);
      const s = (n.updated || []).find(({ oldValue: i }) => i.id === o);
      s && t.set(s.newValue.id);
    }
  }), {
    get current() {
      return t.get();
    },
    subscribe: t.subscribe.bind(t),
    set: t.set.bind(t)
  };
};
var po = /* @__PURE__ */ ((e) => (e.EDIT = "EDIT", e.SELECT = "SELECT", e.NONE = "NONE", e))(po || {});
var Rt = { selected: [] };
var vs = (e, t, n) => {
  const o = /* @__PURE__ */ en(Rt);
  let s = t;
  const i = () => {
    Ye(o.get(), Rt) || o.set(Rt);
  }, l = () => {
    var g;
    return ((g = o.get().selected) == null ? void 0 : g.length) === 0;
  }, r = (g) => {
    if (l())
      return false;
    const p = typeof g == "string" ? g : g.id;
    return o.get().selected.some((y) => y.id === p);
  }, a2 = (g, p) => {
    let y;
    if (Array.isArray(g)) {
      if (y = g.map((w) => e.getAnnotation(w)).filter(Boolean), y.length < g.length) {
        console.warn("Invalid selection: " + g.filter((w) => !y.some((A) => A.id === w)));
        return;
      }
    } else {
      const w = e.getAnnotation(g);
      if (!w) {
        console.warn("Invalid selection: " + g);
        return;
      }
      y = [w];
    }
    const S = y.reduce((w, A) => {
      const _ = h(A);
      return _ === "EDIT" ? [...w, { id: A.id, editable: true }] : _ === "SELECT" ? [...w, { id: A.id }] : w;
    }, []);
    o.set({ selected: S, event: p });
  }, f = (g, p) => {
    const y = Array.isArray(g) ? g : [g], S = y.map((w) => e.getAnnotation(w)).filter((w) => !!w);
    o.set({
      selected: S.map((w) => {
        const A = p === void 0 ? h(w) === "EDIT" : p;
        return { id: w.id, editable: A };
      })
    }), S.length !== y.length && console.warn("Invalid selection", g);
  }, d = (g) => {
    if (l())
      return false;
    const { selected: p } = o.get();
    p.some(({ id: y }) => g.includes(y)) && o.set({ selected: p.filter(({ id: y }) => !g.includes(y)) });
  }, u = (g) => {
    s = g, f(o.get().selected.map(({ id: p }) => p));
  }, h = (g) => As(g, s, n);
  return e.observe(
    ({ changes: g }) => d((g.deleted || []).map((p) => p.id))
  ), {
    get event() {
      const g = o.get();
      return g ? g.event : null;
    },
    get selected() {
      const g = o.get();
      return g ? [...g.selected] : null;
    },
    get userSelectAction() {
      return s;
    },
    clear: i,
    evalSelectAction: h,
    isEmpty: l,
    isSelected: r,
    setSelected: f,
    setUserSelectAction: u,
    subscribe: o.subscribe.bind(o),
    userSelect: a2
  };
};
var As = (e, t, n) => {
  const o = n ? n.serialize(e) : e;
  return typeof t == "function" ? t(o) : t || "EDIT";
};
var pe = [];
for (let e = 0; e < 256; ++e)
  pe.push((e + 256).toString(16).slice(1));
function Ss(e, t = 0) {
  return (pe[e[t + 0]] + pe[e[t + 1]] + pe[e[t + 2]] + pe[e[t + 3]] + "-" + pe[e[t + 4]] + pe[e[t + 5]] + "-" + pe[e[t + 6]] + pe[e[t + 7]] + "-" + pe[e[t + 8]] + pe[e[t + 9]] + "-" + pe[e[t + 10]] + pe[e[t + 11]] + pe[e[t + 12]] + pe[e[t + 13]] + pe[e[t + 14]] + pe[e[t + 15]]).toLowerCase();
}
var Ms = new Uint8Array(16);
function ks() {
  return crypto.getRandomValues(Ms);
}
function _o(e, t, n) {
  return crypto.randomUUID ? crypto.randomUUID() : Ts(e);
}
function Ts(e, t, n) {
  var o;
  e = e || {};
  const s = e.random ?? ((o = e.rng) == null ? void 0 : o.call(e)) ?? ks();
  if (s.length < 16)
    throw new Error("Random bytes length must be >= 16");
  return s[6] = s[6] & 15 | 64, s[8] = s[8] & 63 | 128, Ss(s);
}
var Yt = (e) => {
  const t = (n) => {
    const o = { ...n };
    return n.created && typeof n.created == "string" && (o.created = new Date(n.created)), n.updated && typeof n.updated == "string" && (o.updated = new Date(n.updated)), o;
  };
  return {
    ...e,
    bodies: (e.bodies || []).map(t),
    target: t(e.target)
  };
};
var Qr = (e, t, n, o) => ({
  id: _o(),
  annotation: typeof e == "string" ? e : e.id,
  created: n || /* @__PURE__ */ new Date(),
  creator: o,
  ...t
});
var Ps = (e, t) => {
  const n = new Set(e.bodies.map((o) => o.id));
  return t.bodies.filter((o) => !n.has(o.id));
};
var Ls = (e, t) => {
  const n = new Set(t.bodies.map((o) => o.id));
  return e.bodies.filter((o) => !n.has(o.id));
};
var Cs = (e, t) => t.bodies.map((n) => {
  const o = e.bodies.find((s) => s.id === n.id);
  return { newBody: n, oldBody: o && !Ye(o, n) ? o : void 0 };
}).filter(({ oldBody: n }) => n).map(({ oldBody: n, newBody: o }) => ({ oldBody: n, newBody: o }));
var Is = (e, t) => !Ye(e.target, t.target);
var yo = (e, t) => {
  const n = Ps(e, t), o = Ls(e, t), s = Cs(e, t);
  return {
    oldValue: e,
    newValue: t,
    bodiesCreated: n.length > 0 ? n : void 0,
    bodiesDeleted: o.length > 0 ? o : void 0,
    bodiesUpdated: s.length > 0 ? s : void 0,
    targetUpdated: Is(e, t) ? { oldTarget: e.target, newTarget: t.target } : void 0
  };
};
var oe = /* @__PURE__ */ ((e) => (e.LOCAL = "LOCAL", e.REMOTE = "REMOTE", e.SILENT = "SILENT", e))(oe || {});
var Os = (e, t) => {
  var n, o;
  const { changes: s, origin: i } = t;
  if (!(e.options.origin ? e.options.origin === i : i !== "SILENT"))
    return false;
  if (e.options.ignore) {
    const { ignore: l } = e.options, r = (a2) => a2 && a2.length > 0;
    if (!(r(s.created) || r(s.deleted))) {
      const a2 = (n = s.updated) == null ? void 0 : n.some((d) => r(d.bodiesCreated) || r(d.bodiesDeleted) || r(d.bodiesUpdated)), f = (o = s.updated) == null ? void 0 : o.some((d) => d.targetUpdated);
      if (l === "BODY_ONLY" && a2 && !f || l === "TARGET_ONLY" && f && !a2)
        return false;
    }
  }
  if (e.options.annotations) {
    const l = /* @__PURE__ */ new Set([
      ...(s.created || []).map((r) => r.id),
      ...(s.deleted || []).map((r) => r.id),
      ...(s.updated || []).map(({ oldValue: r }) => r.id)
    ]);
    return !!(Array.isArray(e.options.annotations) ? e.options.annotations : [e.options.annotations]).find((r) => l.has(r));
  } else
    return true;
};
var Ns = (e, t) => {
  const n = new Set((e.created || []).map((u) => u.id)), o = new Set((e.updated || []).map(({ newValue: u }) => u.id)), s = new Set((t.created || []).map((u) => u.id)), i = new Set((t.deleted || []).map((u) => u.id)), l = new Set((t.updated || []).map(({ oldValue: u }) => u.id)), r = new Set((t.updated || []).filter(({ oldValue: u }) => n.has(u.id) || o.has(u.id)).map(({ oldValue: u }) => u.id)), a2 = [
    ...(e.created || []).filter((u) => !i.has(u.id)).map((u) => l.has(u.id) ? t.updated.find(({ oldValue: h }) => h.id === u.id).newValue : u),
    ...t.created || []
  ], f = [
    ...(e.deleted || []).filter((u) => !s.has(u.id)),
    ...(t.deleted || []).filter((u) => !n.has(u.id))
  ], d = [
    ...(e.updated || []).filter(({ newValue: u }) => !i.has(u.id)).map((u) => {
      const { oldValue: h, newValue: g } = u;
      if (l.has(g.id)) {
        const p = t.updated.find((y) => y.oldValue.id === g.id).newValue;
        return yo(h, p);
      } else
        return u;
    }),
    ...(t.updated || []).filter(({ oldValue: u }) => !r.has(u.id))
  ];
  return { created: a2, deleted: f, updated: d };
};
var mt = (e) => {
  const t = e.id === void 0 ? _o() : e.id;
  return {
    ...e,
    id: t,
    bodies: e.bodies === void 0 ? [] : e.bodies.map((n) => ({
      ...n,
      annotation: t
    })),
    target: {
      ...e.target,
      annotation: t
    }
  };
};
var Ds = (e) => e.id !== void 0;
var Rs = () => {
  const e = /* @__PURE__ */ new Map(), t = /* @__PURE__ */ new Map(), n = [], o = (v, E = {}) => {
    n.push({ onChange: v, options: E });
  }, s = (v) => {
    const E = n.findIndex((m) => m.onChange == v);
    E > -1 && n.splice(E, 1);
  }, i = (v, E) => {
    const m = {
      origin: v,
      changes: {
        created: E.created || [],
        updated: E.updated || [],
        deleted: E.deleted || []
      },
      state: [...e.values()]
    };
    n.forEach((b) => {
      Os(b, m) && b.onChange(m);
    });
  }, l = (v, E = oe.LOCAL) => {
    if (v.id && e.get(v.id))
      throw Error(`Cannot add annotation ${v.id} - exists already`);
    {
      const m = mt(v);
      e.set(m.id, m), m.bodies.forEach((b) => t.set(b.id, m.id)), i(E, { created: [m] });
    }
  }, r = (v, E) => {
    const m = mt(typeof v == "string" ? E : v), b = typeof v == "string" ? v : v.id, L = b && e.get(b);
    if (L) {
      const k = yo(L, m);
      return b === m.id ? e.set(b, m) : (e.delete(b), e.set(m.id, m)), L.bodies.forEach((O) => t.delete(O.id)), m.bodies.forEach((O) => t.set(O.id, m.id)), k;
    } else
      console.warn(`Cannot update annotation ${b} - does not exist`);
  }, a2 = (v, E = oe.LOCAL, m = oe.LOCAL) => {
    const b = Ds(E) ? m : E, L = r(v, E);
    L && i(b, { updated: [L] });
  }, f = (v, E = oe.LOCAL) => {
    e.get(v.id) ? a2(v, E) : l(v, E);
  }, d = (v, E = oe.LOCAL) => {
    const m = v.reduce((b, L) => {
      const k = r(L);
      return k ? [...b, k] : b;
    }, []);
    m.length > 0 && i(E, { updated: m });
  }, u = (v, E = oe.LOCAL) => {
    const m = v.map(mt), { toAdd: b, toUpdate: L } = m.reduce((O, F2) => e.get(F2.id) ? { ...O, toUpdate: [...O.toUpdate, F2] } : { ...O, toAdd: [...O.toAdd, F2] }, { toAdd: [], toUpdate: [] }), k = L.map((O) => r(O, E)).filter(Boolean);
    b.forEach((O) => {
      e.set(O.id, O), O.bodies.forEach((F2) => t.set(F2.id, O.id));
    }), i(E, { created: b, updated: k });
  }, h = (v, E = oe.LOCAL) => {
    const m = e.get(v.annotation);
    if (m) {
      const b = {
        ...m,
        bodies: [...m.bodies, v]
      };
      e.set(m.id, b), t.set(v.id, b.id), i(E, { updated: [{
        oldValue: m,
        newValue: b,
        bodiesCreated: [v]
      }] });
    } else
      console.warn(`Attempt to add body to missing annotation: ${v.annotation}`);
  }, g = () => [...e.values()], p = (v = oe.LOCAL) => {
    const E = [...e.values()];
    e.clear(), t.clear(), i(v, { deleted: E });
  }, y = (v, E = true, m = oe.LOCAL) => {
    const b = v.map(mt);
    if (E) {
      const L = [...e.values()];
      e.clear(), t.clear(), b.forEach((k) => {
        e.set(k.id, k), k.bodies.forEach((O) => t.set(O.id, k.id));
      }), i(m, { created: b, deleted: L });
    } else {
      const L = v.reduce((k, O) => {
        const F2 = O.id && e.get(O.id);
        return F2 ? [...k, F2] : k;
      }, []);
      if (L.length > 0)
        throw Error(`Bulk insert would overwrite the following annotations: ${L.map((k) => k.id).join(", ")}`);
      b.forEach((k) => {
        e.set(k.id, k), k.bodies.forEach((O) => t.set(O.id, k.id));
      }), i(m, { created: b });
    }
  }, S = (v) => {
    const E = typeof v == "string" ? v : v.id, m = e.get(E);
    if (m)
      return e.delete(E), m.bodies.forEach((b) => t.delete(b.id)), m;
    console.warn(`Attempt to delete missing annotation: ${E}`);
  }, w = (v, E = oe.LOCAL) => {
    const m = S(v);
    m && i(E, { deleted: [m] });
  }, A = (v, E = oe.LOCAL) => {
    const m = v.reduce((b, L) => {
      const k = S(L);
      return k ? [...b, k] : b;
    }, []);
    m.length > 0 && i(E, { deleted: m });
  }, _ = (v) => {
    const E = e.get(v.annotation);
    if (E) {
      const m = E.bodies.find((b) => b.id === v.id);
      if (m) {
        t.delete(m.id);
        const b = {
          ...E,
          bodies: E.bodies.filter((L) => L.id !== v.id)
        };
        return e.set(E.id, b), {
          oldValue: E,
          newValue: b,
          bodiesDeleted: [m]
        };
      } else
        console.warn(`Attempt to delete missing body ${v.id} from annotation ${v.annotation}`);
    } else
      console.warn(`Attempt to delete body from missing annotation ${v.annotation}`);
  }, M = (v, E = oe.LOCAL) => {
    const m = _(v);
    m && i(E, { updated: [m] });
  }, P = (v, E = oe.LOCAL) => {
    const m = v.map((b) => _(b)).filter(Boolean);
    m.length > 0 && i(E, { updated: m });
  }, I = (v) => {
    const E = e.get(v);
    return E ? { ...E } : void 0;
  }, T = (v) => {
    const E = t.get(v);
    if (E) {
      const m = I(E).bodies.find((b) => b.id === v);
      if (m)
        return m;
      console.error(`Store integrity error: body ${v} in index, but not in annotation`);
    } else
      console.warn(`Attempt to retrieve missing body: ${v}`);
  }, B2 = (v, E) => {
    if (v.annotation !== E.annotation)
      throw "Annotation integrity violation: annotation ID must be the same when updating bodies";
    const m = e.get(v.annotation);
    if (m) {
      const b = m.bodies.find((k) => k.id === v.id), L = {
        ...m,
        bodies: m.bodies.map((k) => k.id === b.id ? E : k)
      };
      return e.set(m.id, L), b.id !== E.id && (t.delete(b.id), t.set(E.id, L.id)), {
        oldValue: m,
        newValue: L,
        bodiesUpdated: [{ oldBody: b, newBody: E }]
      };
    } else
      console.warn(`Attempt to add body to missing annotation ${v.annotation}`);
  }, N = (v, E, m = oe.LOCAL) => {
    const b = B2(v, E);
    b && i(m, { updated: [b] });
  }, Z = (v, E = oe.LOCAL) => {
    const m = v.map((b) => B2({ id: b.id, annotation: b.annotation }, b)).filter(Boolean);
    i(E, { updated: m });
  }, j = (v) => {
    const E = e.get(v.annotation);
    if (E) {
      const m = {
        ...E,
        target: {
          ...E.target,
          ...v
        }
      };
      return e.set(E.id, m), {
        oldValue: E,
        newValue: m,
        targetUpdated: {
          oldTarget: E.target,
          newTarget: v
        }
      };
    } else
      console.warn(`Attempt to update target on missing annotation: ${v.annotation}`);
  };
  return {
    addAnnotation: l,
    addBody: h,
    all: g,
    bulkAddAnnotations: y,
    bulkDeleteAnnotations: A,
    bulkDeleteBodies: P,
    bulkUpdateAnnotations: d,
    bulkUpdateBodies: Z,
    bulkUpdateTargets: (v, E = oe.LOCAL) => {
      const m = v.map((b) => j(b)).filter(Boolean);
      m.length > 0 && i(E, { updated: m });
    },
    bulkUpsertAnnotations: u,
    clear: p,
    deleteAnnotation: w,
    deleteBody: M,
    getAnnotation: I,
    getBody: T,
    observe: o,
    unobserve: s,
    updateAnnotation: a2,
    updateBody: N,
    updateTarget: (v, E = oe.LOCAL) => {
      const m = j(v);
      m && i(E, { updated: [m] });
    },
    upsertAnnotation: f
  };
};
var Ys = () => ({
  emit(e, ...t) {
    for (let n = this.events[e] || [], o = 0, s = n.length; o < s; o++)
      n[o](...t);
  },
  events: {},
  on(e, t) {
    var n;
    return ((n = this.events)[e] || (n[e] = [])).push(t), () => {
      var o;
      this.events[e] = (o = this.events[e]) == null ? void 0 : o.filter((s) => t !== s);
    };
  }
});
var Bs = 250;
var Vs = (e, t) => {
  const n = Ys(), o = (t == null ? void 0 : t.changes) || [];
  let s = t ? t.pointer : -1, i = false, l = 0;
  const r = (p) => {
    if (!i) {
      const { changes: y } = p, S = performance.now();
      if (S - l > Bs)
        o.splice(s + 1), o.push(y), s = o.length - 1;
      else {
        const w = o.length - 1;
        o[w] = Ns(o[w], y);
      }
      l = S;
    }
    i = false;
  };
  e.observe(r, { origin: oe.LOCAL });
  const a2 = (p) => p && p.length > 0 && e.bulkDeleteAnnotations(p), f = (p) => p && p.length > 0 && e.bulkAddAnnotations(p, false), d = (p) => p && p.length > 0 && e.bulkUpdateAnnotations(p.map(({ oldValue: y }) => y)), u = (p) => p && p.length > 0 && e.bulkUpdateAnnotations(p.map(({ newValue: y }) => y)), h = (p) => p && p.length > 0 && e.bulkAddAnnotations(p, false), g = (p) => p && p.length > 0 && e.bulkDeleteAnnotations(p);
  return {
    canRedo: () => o.length - 1 > s,
    canUndo: () => s > -1,
    destroy: () => e.unobserve(r),
    getHistory: () => ({ changes: [...o], pointer: s }),
    on: (p, y) => n.on(p, y),
    redo: () => {
      if (o.length - 1 > s) {
        i = true;
        const { created: p, updated: y, deleted: S } = o[s + 1];
        f(p), u(y), g(S), n.emit("redo", o[s + 1]), s += 1;
      }
    },
    undo: () => {
      if (s > -1) {
        i = true;
        const { created: p, updated: y, deleted: S } = o[s];
        a2(p), d(y), h(S), n.emit("undo", o[s]), s -= 1;
      }
    }
  };
};
var Us = () => {
  const e = /* @__PURE__ */ en([]);
  return {
    subscribe: e.subscribe.bind(e),
    set: e.set.bind(e)
  };
};
var Xs = (e, t, n, o) => {
  const { hover: s, selection: i, store: l, viewport: r } = e, a2 = /* @__PURE__ */ new Map();
  let f = [], d, u;
  const h = (w, A) => {
    a2.has(w) ? a2.get(w).push(A) : a2.set(w, [A]);
  }, g = (w, A) => {
    const _ = a2.get(w);
    if (_) {
      const M = _.indexOf(A);
      M !== -1 && _.splice(M, 1);
    }
  }, p = (w, A, _) => {
    a2.has(w) && setTimeout(() => {
      a2.get(w).forEach((M) => {
        if (n) {
          const P = Array.isArray(A) ? A.map((T) => n.serialize(T)) : n.serialize(A), I = _ ? _ instanceof PointerEvent ? _ : n.serialize(_) : void 0;
          M(P, I);
        } else
          M(A, _);
      });
    }, 1);
  }, y = () => {
    const { selected: w } = i, A = (w || []).map(({ id: _ }) => l.getAnnotation(_));
    A.forEach((_) => {
      const M = f.find((P) => P.id === _.id);
      (!M || !Ye(M, _)) && p("updateAnnotation", _, M);
    }), f = f.map((_) => A.find(({ id: P }) => P === _.id) || _);
  };
  i.subscribe(({ selected: w }) => {
    if (!(f.length === 0 && w.length === 0)) {
      if (f.length === 0 && w.length > 0)
        f = w.map(({ id: A }) => l.getAnnotation(A));
      else if (f.length > 0 && w.length === 0)
        f.forEach((A) => {
          const _ = l.getAnnotation(A.id);
          _ && !Ye(_, A) && p("updateAnnotation", _, A);
        }), f = [];
      else {
        const A = new Set(f.map((M) => M.id)), _ = new Set(w.map(({ id: M }) => M));
        f.filter((M) => !_.has(M.id)).forEach((M) => {
          const P = l.getAnnotation(M.id);
          P && !Ye(P, M) && p("updateAnnotation", P, M);
        }), f = [
          // Remove annotations that were deselected
          ...f.filter((M) => _.has(M.id)),
          // Add editable annotations that were selected
          ...w.filter(({ id: M }) => !A.has(M)).map(({ id: M }) => l.getAnnotation(M))
        ];
      }
      p("selectionChanged", f);
    }
  }), s.subscribe((w) => {
    !d && w ? p("mouseEnterAnnotation", l.getAnnotation(w)) : d && !w ? p("mouseLeaveAnnotation", l.getAnnotation(d)) : d && w && (p("mouseLeaveAnnotation", l.getAnnotation(d)), p("mouseEnterAnnotation", l.getAnnotation(w))), d = w;
  }), r == null || r.subscribe((w) => p("viewportIntersect", w.map((A) => l.getAnnotation(A)))), l.observe((w) => {
    o && (u && clearTimeout(u), u = setTimeout(y, 1e3));
    const { created: A, deleted: _ } = w.changes;
    (A || []).forEach((M) => p("createAnnotation", M)), (_ || []).forEach((M) => p("deleteAnnotation", M)), (w.changes.updated || []).filter((M) => [
      ...M.bodiesCreated || [],
      ...M.bodiesDeleted || [],
      ...M.bodiesUpdated || []
    ].length > 0).forEach(({ oldValue: M, newValue: P }) => {
      const I = f.find((T) => T.id === M.id) || M;
      f = f.map((T) => T.id === M.id ? P : T), p("updateAnnotation", P, I);
    });
  }, { origin: oe.LOCAL }), l.observe((w) => {
    if (f) {
      const A = new Set(f.map((M) => M.id)), _ = (w.changes.updated || []).filter(({ newValue: M }) => A.has(M.id)).map(({ newValue: M }) => M);
      _.length > 0 && (f = f.map((M) => _.find((I) => I.id === M.id) || M));
    }
  }, { origin: oe.REMOTE });
  const S = (w) => (A) => {
    const { updated: _ } = A;
    w ? (_ || []).forEach((M) => p("updateAnnotation", M.oldValue, M.newValue)) : (_ || []).forEach((M) => p("updateAnnotation", M.newValue, M.oldValue));
  };
  return t.on("undo", S(true)), t.on("redo", S(false)), { on: h, off: g, emit: p };
};
var Hs = (e) => (t) => t.reduce((n, o) => {
  const { parsed: s, error: i } = e.parse(o);
  return i ? {
    parsed: n.parsed,
    failed: [...n.failed, o]
  } : s ? {
    parsed: [...n.parsed, s],
    failed: n.failed
  } : {
    ...n
  };
}, { parsed: [], failed: [] });
var Gs = (e, t, n) => {
  const { store: o, selection: s } = e, i = (w) => {
    if (n) {
      const { parsed: A, error: _ } = n.parse(w);
      A ? o.addAnnotation(A, oe.REMOTE) : console.error(_);
    } else
      o.addAnnotation(Yt(w), oe.REMOTE);
  }, l = () => s.clear(), r = () => o.clear(), a2 = (w) => {
    const A = o.getAnnotation(w);
    return n && A ? n.serialize(A) : A;
  }, f = () => n ? o.all().map(n.serialize) : o.all(), d = () => {
    var w;
    const A = (((w = s.selected) == null ? void 0 : w.map((_) => _.id)) || []).map((_) => o.getAnnotation(_)).filter(Boolean);
    return n ? A.map(n.serialize) : A;
  }, u = (w, A = true) => fetch(w).then((_) => _.json()).then((_) => (g(_, A), _)), h = (w) => {
    if (typeof w == "string") {
      const A = o.getAnnotation(w);
      if (o.deleteAnnotation(w), A)
        return n ? n.serialize(A) : A;
    } else {
      const A = n ? n.parse(w).parsed : w;
      if (A)
        return o.deleteAnnotation(A), w;
    }
  }, g = (w, A = true) => {
    if (n) {
      const _ = n.parseAll || Hs(n), { parsed: M, failed: P } = _(w);
      P.length > 0 && console.warn(`Discarded ${P.length} invalid annotations`, P), o.bulkAddAnnotations(M, A, oe.REMOTE);
    } else
      o.bulkAddAnnotations(w.map(Yt), A, oe.REMOTE);
  }, p = (w, A) => {
    w ? s.setSelected(w, A) : s.clear();
  }, y = (w) => {
    s.setUserSelectAction(w);
  }, S = (w) => {
    if (n) {
      const A = n.parse(w).parsed, _ = n.serialize(o.getAnnotation(A.id));
      return o.updateAnnotation(A), _;
    } else {
      const A = o.getAnnotation(w.id);
      return o.updateAnnotation(Yt(w)), A;
    }
  };
  return {
    addAnnotation: i,
    cancelSelected: l,
    canRedo: t.canRedo,
    canUndo: t.canUndo,
    clearAnnotations: r,
    getAnnotationById: a2,
    getAnnotations: f,
    getHistory: t.getHistory,
    getSelected: d,
    loadAnnotations: u,
    redo: t.redo,
    removeAnnotation: h,
    setAnnotations: g,
    setSelected: p,
    setUserSelectAction: y,
    undo: t.undo,
    updateAnnotation: S
  };
};
var xr = (e, t, n) => typeof t == "function" ? t(e, n) : t;
var $r = (e, t) => typeof e != "function" && typeof t != "function" ? {
  ...e || {},
  ...t || {}
} : (n, o) => {
  const s = typeof e == "function" ? e(n, o) : e, i = typeof t == "function" ? t(n, o) : t;
  return {
    ...s || {},
    ...i || {}
  };
};
var js = "useandom-26T198340PX75pxJACKVERYMINDBUSHWOLF_GQZbfghjklqvwyzrict";
var Fs = (e) => crypto.getRandomValues(new Uint8Array(e));
var zs = (e, t, n) => {
  let o = 256 - 256 % e.length;
  if (o === 256) {
    let i = e.length - 1;
    return (l = t) => {
      if (!l)
        return "";
      let r = "";
      for (; ; ) {
        let a2 = n(l), f = l;
        for (; f--; )
          if (r += e[a2[f] & i], r.length >= l)
            return r;
      }
    };
  }
  let s = Math.ceil(1.6 * 256 * t / o);
  return (i = t) => {
    if (!i)
      return "";
    let l = "";
    for (; ; ) {
      let r = n(s), a2 = s;
      for (; a2--; )
        if (r[a2] < o && (l += e[r[a2] % e.length], l.length >= i))
          return l;
    }
  };
};
var qs = (e, t = 21) => zs(e, t | 0, Fs);
var Ks = (e = 21) => {
  let t = "", n = crypto.getRandomValues(new Uint8Array(e |= 0));
  for (; e--; )
    t += js[n[e] & 63];
  return t;
};
var Ws = () => ({ isGuest: true, id: qs("1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_", 20)() });
var Zs = (e) => {
  const t = JSON.stringify(e);
  let n = 0;
  for (let o = 0, s = t.length; o < s; o++) {
    let i = t.charCodeAt(o);
    n = (n << 5) - n + i, n |= 0;
  }
  return `${n}`;
};
var wo = (e) => e ? typeof e == "object" ? { ...e } : e : void 0;
var Js = (e, t) => (Array.isArray(e) ? e : [e]).map((n) => {
  const { id: o, type: s, purpose: i, value: l, created: r, modified: a2, creator: f, ...d } = n;
  return {
    id: o || `temp-${Zs(n)}`,
    annotation: t,
    type: s,
    purpose: i,
    value: l,
    creator: wo(f),
    created: r ? new Date(r) : void 0,
    updated: a2 ? new Date(a2) : void 0,
    ...d
  };
});
var Qs = (e) => e.map((t) => {
  var n;
  const { annotation: o, created: s, updated: i, ...l } = t, r = {
    ...l,
    created: s == null ? void 0 : s.toISOString(),
    modified: i == null ? void 0 : i.toISOString()
  };
  return (n = r.id) != null && n.startsWith("temp-") && delete r.id, r;
});
var xs = [
  "#ff7c00",
  // orange
  "#1ac938",
  // green
  "#e8000b",
  // red
  "#8b2be2",
  // purple
  "#9f4800",
  // brown
  "#f14cc1",
  // pink
  "#ffc400",
  // khaki
  "#00d7ff",
  // cyan
  "#023eff"
  // blue
];
var el = () => {
  const e = [...xs];
  return { assignRandomColor: () => {
    const t = Math.floor(Math.random() * e.length), n = e[t];
    return e.splice(t, 1), n;
  }, releaseColor: (t) => e.push(t) };
};
globalThis.ANNOTORIOUS_PRESENCE_KEY || (globalThis.ANNOTORIOUS_PRESENCE_KEY = Ks());
var tl = (e, t = { strict: true, invertY: false }) => ({ parse: (s) => $s(s, t), serialize: (s) => ei(s, e, t) });
var $s = (e, t = { strict: true, invertY: false }) => {
  const n = e.id || mo(), {
    creator: o,
    created: s,
    modified: i,
    body: l,
    ...r
  } = e, a2 = Js(l || [], n), f = Array.isArray(e.target) ? e.target[0] : e.target, d = typeof f == "string" ? f : Array.isArray(f.selector) ? f.selector[0] : f.selector, u = $o(d) ? es(d, t.invertY) : (d == null ? void 0 : d.type) === "SvgSelector" ? ds(d) : void 0, h = Array.isArray(r.target) ? r.target[0] : r.targret;
  return u || !t.strict ? {
    parsed: {
      ...r,
      id: n,
      bodies: a2,
      target: {
        created: s ? new Date(s) : void 0,
        creator: wo(o),
        updated: i ? new Date(i) : void 0,
        // Note the target can be a string and we don't want to spread the characters...
        ...typeof h == "string" ? {} : h,
        annotation: n,
        selector: u || d
      }
    }
  } : {
    error: Error(`Invalid selector: ${JSON.stringify(d)}`)
  };
};
var ei = (e, t, n = { strict: true, invertY: false }) => {
  const {
    selector: o,
    creator: s,
    created: i,
    updated: l,
    updatedBy: r,
    // Excluded from serialization
    ...a2
  } = e.target;
  let f;
  try {
    o.type === x.RECTANGLE && !o.geometry.rot ? f = ts(o.geometry) : f = gs(o);
  } catch (u) {
    if (n.strict)
      throw u;
    f = o;
  }
  const d = {
    ...e,
    "@context": "http://www.w3.org/ns/anno.jsonld",
    id: e.id,
    type: "Annotation",
    body: Qs(e.bodies),
    created: i == null ? void 0 : i.toISOString(),
    creator: s,
    modified: l == null ? void 0 : l.toISOString(),
    target: {
      ...a2,
      source: t,
      type: "SpecificResource",
      selector: f
    }
  };
  return delete d.bodies, typeof d.target != "string" && "annotation" in d.target && delete d.target.annotation, d;
};
var ze = [];
function ti(e, t = $) {
  let n;
  const o = /* @__PURE__ */ new Set();
  function s(r) {
    if (ae(e, r) && (e = r, n)) {
      const a2 = !ze.length;
      for (const f of o)
        f[1](), ze.push(f, e);
      if (a2) {
        for (let f = 0; f < ze.length; f += 2)
          ze[f][0](ze[f + 1]);
        ze.length = 0;
      }
    }
  }
  function i(r) {
    s(r(e));
  }
  function l(r, a2 = $) {
    const f = [r, a2];
    return o.add(f), o.size === 1 && (n = t(s, i) || $), r(e), () => {
      o.delete(f), o.size === 0 && n && (n(), n = null);
    };
  }
  return { set: s, update: i, subscribe: l };
}
var ni = (e, t) => {
  const { naturalWidth: n, naturalHeight: o } = e;
  if (!n && !o) {
    const { width: s, height: i } = e;
    t.setAttribute("viewBox", `0 0 ${s} ${i}`), e.addEventListener("load", (l) => {
      const r = l.target;
      t.setAttribute("viewBox", `0 0 ${r.naturalWidth} ${r.naturalHeight}`);
    });
  } else
    t.setAttribute("viewBox", `0 0 ${n} ${o}`);
};
var oi = (e, t) => {
  ni(e, t);
  const { subscribe: n, set: o } = ti(1);
  let s;
  return window.ResizeObserver && (s = new ResizeObserver(() => {
    const l = t.getBoundingClientRect(), { width: r, height: a2 } = t.viewBox.baseVal, f = Math.max(
      l.width / r,
      l.height / a2
    );
    o(f);
  }), s.observe(t.parentElement)), { destroy: () => {
    s && s.disconnect();
  }, subscribe: n };
};
var je = (e, t, n) => {
  const o = typeof t == "function" ? t(e, n) : t;
  if (o) {
    const { fill: s, fillOpacity: i, stroke: l, strokeWidth: r, strokeOpacity: a2 } = o;
    let f = "";
    return s && (f += `fill:${s};`), i || i === 0 ? f += `fill-opacity:${i};` : s && (f += "fill-opacity:0.25;"), l && (f += `stroke:${l};`, f += `stroke-width:${r || "1"};`, f += `stroke-opacity:${a2 || "1"};`), f;
  }
};
var kt = (e, t = 0) => {
  const { minX: n, minY: o, maxX: s, maxY: i } = e;
  return {
    x: n - t,
    y: o - t,
    w: s - n + 2 * t,
    h: i - o + 2 * t
  };
};
var Le = typeof window > "u" || typeof navigator > "u" ? false : "ontouchstart" in window || navigator.maxTouchPoints > 0 || // @ts-ignore
navigator.msMaxTouchPoints > 0;
var si = (e) => ({});
var un = (e) => ({ grab: (
  /*onGrab*/
  e[0]
) });
function ii(e) {
  let t, n, o, s;
  const i = (
    /*#slots*/
    e[8].default
  ), l = Co(
    i,
    e,
    /*$$scope*/
    e[7],
    un
  );
  return {
    c() {
      t = C("g"), l && l.c(), c(t, "class", "a9s-annotation selected");
    },
    m(r, a2) {
      Y(r, t, a2), l && l.m(t, null), n = true, o || (s = [
        Q(
          t,
          "pointerup",
          /*onRelease*/
          e[2]
        ),
        Q(
          t,
          "pointermove",
          /*onPointerMove*/
          e[1]
        )
      ], o = true);
    },
    p(r, [a2]) {
      l && l.p && (!n || a2 & /*$$scope*/
      128) && Oo(
        l,
        i,
        r,
        /*$$scope*/
        r[7],
        n ? Io(
          i,
          /*$$scope*/
          r[7],
          a2,
          si
        ) : No(
          /*$$scope*/
          r[7]
        ),
        un
      );
    },
    i(r) {
      n || (V(l, r), n = true);
    },
    o(r) {
      H(l, r), n = false;
    },
    d(r) {
      r && R(t), l && l.d(r), o = false, Se(s);
    }
  };
}
function ri(e, t, n) {
  let { $$slots: o = {}, $$scope: s } = t;
  const i = Ve();
  let { shape: l } = t, { editor: r } = t, { transform: a2 } = t, { svgEl: f } = t, d, u, h;
  const g = (S) => (w) => {
    if (d = S, f) {
      const { left: _, top: M } = f.getBoundingClientRect(), P = w.clientX - _, I = w.clientY - M;
      u = a2.elementToImage(P, I);
    } else {
      const { offsetX: _, offsetY: M } = w;
      u = a2.elementToImage(_, M);
    }
    h = l, w.target.setPointerCapture(w.pointerId), i("grab", w);
  }, p = (S) => {
    if (d) {
      const [w, A] = a2.elementToImage(S.offsetX, S.offsetY), _ = [w - u[0], A - u[1]];
      n(3, l = r(h, d, _)), i("change", l);
    }
  }, y = (S) => {
    S.target.releasePointerCapture(S.pointerId), d = void 0, h = l, i("release", S);
  };
  return e.$$set = (S) => {
    "shape" in S && n(3, l = S.shape), "editor" in S && n(4, r = S.editor), "transform" in S && n(5, a2 = S.transform), "svgEl" in S && n(6, f = S.svgEl), "$$scope" in S && n(7, s = S.$$scope);
  }, [
    g,
    p,
    y,
    l,
    r,
    a2,
    f,
    s,
    o
  ];
}
var tn = class extends ye {
  constructor(t) {
    super(), _e(this, t, ri, ii, ae, {
      shape: 3,
      editor: 4,
      transform: 5,
      svgEl: 6
    });
  }
};
function li(e) {
  let t, n, o, s, i, l, r, a2, f = (
    /*selected*/
    e[3] && dn(e)
  );
  return {
    c() {
      t = C("g"), n = C("circle"), f && f.c(), s = C("circle"), c(n, "class", "a9s-handle-buffer svelte-qtyc7s"), c(
        n,
        "cx",
        /*x*/
        e[0]
      ), c(
        n,
        "cy",
        /*y*/
        e[1]
      ), c(n, "r", o = /*handleRadius*/
      e[5] + 6 / /*scale*/
      e[2]), c(n, "role", "button"), c(n, "tabindex", "0"), c(s, "class", i = Ze(`a9s-handle-dot${/*selected*/
      e[3] ? " selected" : ""}`) + " svelte-qtyc7s"), c(
        s,
        "cx",
        /*x*/
        e[0]
      ), c(
        s,
        "cy",
        /*y*/
        e[1]
      ), c(
        s,
        "r",
        /*handleRadius*/
        e[5]
      ), c(t, "class", l = `a9s-handle ${/*$$props*/
      e[8].class || ""}`.trim());
    },
    m(d, u) {
      Y(d, t, u), G(t, n), f && f.m(t, null), G(t, s), r || (a2 = [
        Q(
          n,
          "dblclick",
          /*dblclick_handler_1*/
          e[12]
        ),
        Q(
          n,
          "pointerenter",
          /*pointerenter_handler*/
          e[13]
        ),
        Q(
          n,
          "pointerleave",
          /*pointerleave_handler*/
          e[14]
        ),
        Q(
          n,
          "pointerdown",
          /*pointerdown_handler_1*/
          e[15]
        ),
        Q(
          n,
          "pointerdown",
          /*onPointerDown*/
          e[6]
        ),
        Q(
          n,
          "pointerup",
          /*pointerup_handler_1*/
          e[16]
        ),
        Q(
          n,
          "pointerup",
          /*onPointerUp*/
          e[7]
        )
      ], r = true);
    },
    p(d, u) {
      u & /*x*/
      1 && c(
        n,
        "cx",
        /*x*/
        d[0]
      ), u & /*y*/
      2 && c(
        n,
        "cy",
        /*y*/
        d[1]
      ), u & /*handleRadius, scale*/
      36 && o !== (o = /*handleRadius*/
      d[5] + 6 / /*scale*/
      d[2]) && c(n, "r", o), /*selected*/
      d[3] ? f ? f.p(d, u) : (f = dn(d), f.c(), f.m(t, s)) : f && (f.d(1), f = null), u & /*selected*/
      8 && i !== (i = Ze(`a9s-handle-dot${/*selected*/
      d[3] ? " selected" : ""}`) + " svelte-qtyc7s") && c(s, "class", i), u & /*x*/
      1 && c(
        s,
        "cx",
        /*x*/
        d[0]
      ), u & /*y*/
      2 && c(
        s,
        "cy",
        /*y*/
        d[1]
      ), u & /*handleRadius*/
      32 && c(
        s,
        "r",
        /*handleRadius*/
        d[5]
      ), u & /*$$props*/
      256 && l !== (l = `a9s-handle ${/*$$props*/
      d[8].class || ""}`.trim()) && c(t, "class", l);
    },
    d(d) {
      d && R(t), f && f.d(), r = false, Se(a2);
    }
  };
}
function ai(e) {
  let t, n, o, s, i, l, r, a2, f;
  return {
    c() {
      t = C("g"), n = C("circle"), s = C("circle"), l = C("circle"), c(
        n,
        "cx",
        /*x*/
        e[0]
      ), c(
        n,
        "cy",
        /*y*/
        e[1]
      ), c(n, "r", o = /*handleRadius*/
      e[5] * 10), c(n, "class", "a9s-touch-halo"), Te(
        n,
        "touched",
        /*touched*/
        e[4]
      ), c(
        s,
        "cx",
        /*x*/
        e[0]
      ), c(
        s,
        "cy",
        /*y*/
        e[1]
      ), c(s, "r", i = /*handleRadius*/
      e[5] + 10 / /*scale*/
      e[2]), c(s, "class", "a9s-handle-buffer svelte-qtyc7s"), c(s, "role", "button"), c(s, "tabindex", "0"), c(l, "class", "a9s-handle-dot"), c(
        l,
        "cx",
        /*x*/
        e[0]
      ), c(
        l,
        "cy",
        /*y*/
        e[1]
      ), c(l, "r", r = /*handleRadius*/
      e[5] + 2 / /*scale*/
      e[2]), c(t, "class", "a9s-touch-handle");
    },
    m(d, u) {
      Y(d, t, u), G(t, n), G(t, s), G(t, l), a2 || (f = [
        Q(
          s,
          "dblclick",
          /*dblclick_handler*/
          e[9]
        ),
        Q(
          s,
          "pointerdown",
          /*pointerdown_handler*/
          e[10]
        ),
        Q(
          s,
          "pointerdown",
          /*onPointerDown*/
          e[6]
        ),
        Q(
          s,
          "pointerup",
          /*pointerup_handler*/
          e[11]
        ),
        Q(
          s,
          "pointerup",
          /*onPointerUp*/
          e[7]
        )
      ], a2 = true);
    },
    p(d, u) {
      u & /*x*/
      1 && c(
        n,
        "cx",
        /*x*/
        d[0]
      ), u & /*y*/
      2 && c(
        n,
        "cy",
        /*y*/
        d[1]
      ), u & /*handleRadius*/
      32 && o !== (o = /*handleRadius*/
      d[5] * 10) && c(n, "r", o), u & /*touched*/
      16 && Te(
        n,
        "touched",
        /*touched*/
        d[4]
      ), u & /*x*/
      1 && c(
        s,
        "cx",
        /*x*/
        d[0]
      ), u & /*y*/
      2 && c(
        s,
        "cy",
        /*y*/
        d[1]
      ), u & /*handleRadius, scale*/
      36 && i !== (i = /*handleRadius*/
      d[5] + 10 / /*scale*/
      d[2]) && c(s, "r", i), u & /*x*/
      1 && c(
        l,
        "cx",
        /*x*/
        d[0]
      ), u & /*y*/
      2 && c(
        l,
        "cy",
        /*y*/
        d[1]
      ), u & /*handleRadius, scale*/
      36 && r !== (r = /*handleRadius*/
      d[5] + 2 / /*scale*/
      d[2]) && c(l, "r", r);
    },
    d(d) {
      d && R(t), a2 = false, Se(f);
    }
  };
}
function dn(e) {
  let t, n;
  return {
    c() {
      t = C("circle"), c(t, "class", "a9s-handle-selected"), c(
        t,
        "cx",
        /*x*/
        e[0]
      ), c(
        t,
        "cy",
        /*y*/
        e[1]
      ), c(t, "r", n = /*handleRadius*/
      e[5] + 8 / /*scale*/
      e[2]);
    },
    m(o, s) {
      Y(o, t, s);
    },
    p(o, s) {
      s & /*x*/
      1 && c(
        t,
        "cx",
        /*x*/
        o[0]
      ), s & /*y*/
      2 && c(
        t,
        "cy",
        /*y*/
        o[1]
      ), s & /*handleRadius, scale*/
      36 && n !== (n = /*handleRadius*/
      o[5] + 8 / /*scale*/
      o[2]) && c(t, "r", n);
    },
    d(o) {
      o && R(t);
    }
  };
}
function ci(e) {
  let t;
  function n(i, l) {
    return Le ? ai : li;
  }
  let s = n()(e);
  return {
    c() {
      s.c(), t = Me();
    },
    m(i, l) {
      s.m(i, l), Y(i, t, l);
    },
    p(i, [l]) {
      s.p(i, l);
    },
    i: $,
    o: $,
    d(i) {
      i && R(t), s.d(i);
    }
  };
}
function fi(e, t, n) {
  let o, { x: s } = t, { y: i } = t, { scale: l } = t, { selected: r = void 0 } = t, a2 = false;
  const f = (_) => {
    _.pointerType === "touch" && n(4, a2 = true);
  }, d = () => n(4, a2 = false);
  function u(_) {
    de.call(this, e, _);
  }
  function h(_) {
    de.call(this, e, _);
  }
  function g(_) {
    de.call(this, e, _);
  }
  function p(_) {
    de.call(this, e, _);
  }
  function y(_) {
    de.call(this, e, _);
  }
  function S(_) {
    de.call(this, e, _);
  }
  function w(_) {
    de.call(this, e, _);
  }
  function A(_) {
    de.call(this, e, _);
  }
  return e.$$set = (_) => {
    n(8, t = Kt(Kt({}, t), rn(_))), "x" in _ && n(0, s = _.x), "y" in _ && n(1, i = _.y), "scale" in _ && n(2, l = _.scale), "selected" in _ && n(3, r = _.selected);
  }, e.$$.update = () => {
    e.$$.dirty & /*scale*/
    4 && n(5, o = 4 / l);
  }, t = rn(t), [
    s,
    i,
    l,
    r,
    a2,
    o,
    f,
    d,
    t,
    u,
    h,
    g,
    p,
    y,
    S,
    w,
    A
  ];
}
var Xe = class extends ye {
  constructor(t) {
    super(), _e(this, t, fi, ci, ae, { x: 0, y: 1, scale: 2, selected: 3 });
  }
};
function ui(e) {
  let t, n, o, s, i, l, r;
  return {
    c() {
      t = C("g"), n = C("circle"), s = C("circle"), i = C("circle"), c(n, "class", "a9s-polygon-midpoint-buffer svelte-12ykj76"), c(
        n,
        "cx",
        /*x*/
        e[0]
      ), c(
        n,
        "cy",
        /*y*/
        e[1]
      ), c(n, "r", o = 1.75 * /*handleRadius*/
      e[2]), c(s, "class", "a9s-polygon-midpoint-outer svelte-12ykj76"), c(
        s,
        "cx",
        /*x*/
        e[0]
      ), c(
        s,
        "cy",
        /*y*/
        e[1]
      ), c(
        s,
        "r",
        /*handleRadius*/
        e[2]
      ), c(i, "class", "a9s-polygon-midpoint-inner svelte-12ykj76"), c(
        i,
        "cx",
        /*x*/
        e[0]
      ), c(
        i,
        "cy",
        /*y*/
        e[1]
      ), c(
        i,
        "r",
        /*handleRadius*/
        e[2]
      ), c(t, "class", "a9s-polygon-midpoint svelte-12ykj76");
    },
    m(a2, f) {
      Y(a2, t, f), G(t, n), G(t, s), G(t, i), l || (r = [
        Q(
          n,
          "pointerdown",
          /*pointerdown_handler*/
          e[5]
        ),
        Q(
          n,
          "pointerdown",
          /*onPointerDown*/
          e[3]
        )
      ], l = true);
    },
    p(a2, f) {
      f & /*x*/
      1 && c(
        n,
        "cx",
        /*x*/
        a2[0]
      ), f & /*y*/
      2 && c(
        n,
        "cy",
        /*y*/
        a2[1]
      ), f & /*handleRadius*/
      4 && o !== (o = 1.75 * /*handleRadius*/
      a2[2]) && c(n, "r", o), f & /*x*/
      1 && c(
        s,
        "cx",
        /*x*/
        a2[0]
      ), f & /*y*/
      2 && c(
        s,
        "cy",
        /*y*/
        a2[1]
      ), f & /*handleRadius*/
      4 && c(
        s,
        "r",
        /*handleRadius*/
        a2[2]
      ), f & /*x*/
      1 && c(
        i,
        "cx",
        /*x*/
        a2[0]
      ), f & /*y*/
      2 && c(
        i,
        "cy",
        /*y*/
        a2[1]
      ), f & /*handleRadius*/
      4 && c(
        i,
        "r",
        /*handleRadius*/
        a2[2]
      );
    },
    d(a2) {
      a2 && R(t), l = false, Se(r);
    }
  };
}
function di(e) {
  let t;
  return {
    c() {
      t = C("circle"), c(
        t,
        "cx",
        /*x*/
        e[0]
      ), c(
        t,
        "cy",
        /*y*/
        e[1]
      ), c(
        t,
        "r",
        /*handleRadius*/
        e[2]
      );
    },
    m(n, o) {
      Y(n, t, o);
    },
    p(n, o) {
      o & /*x*/
      1 && c(
        t,
        "cx",
        /*x*/
        n[0]
      ), o & /*y*/
      2 && c(
        t,
        "cy",
        /*y*/
        n[1]
      ), o & /*handleRadius*/
      4 && c(
        t,
        "r",
        /*handleRadius*/
        n[2]
      );
    },
    d(n) {
      n && R(t);
    }
  };
}
function hi(e) {
  let t;
  function n(i, l) {
    return Le ? di : ui;
  }
  let s = n()(e);
  return {
    c() {
      s.c(), t = Me();
    },
    m(i, l) {
      s.m(i, l), Y(i, t, l);
    },
    p(i, [l]) {
      s.p(i, l);
    },
    i: $,
    o: $,
    d(i) {
      i && R(t), s.d(i);
    }
  };
}
function gi(e, t, n) {
  let o, { x: s } = t, { y: i } = t, { scale: l } = t;
  const r = (f) => {
    f.pointerType;
  };
  function a2(f) {
    de.call(this, e, f);
  }
  return e.$$set = (f) => {
    "x" in f && n(0, s = f.x), "y" in f && n(1, i = f.y), "scale" in f && n(4, l = f.scale);
  }, e.$$.update = () => {
    e.$$.dirty & /*scale*/
    16 && n(2, o = 4 / l);
  }, [s, i, o, r, l, a2];
}
var bo = class extends ye {
  constructor(t) {
    super(), _e(this, t, gi, hi, ae, { x: 0, y: 1, scale: 4 });
  }
};
function Bt(e) {
  const t = e.slice(), n = (
    /*midpoints*/
    t[10][
      /*visibleMidpoint*/
      t[6]
    ]
  );
  return t[28] = n.point, t;
}
function hn(e, t, n) {
  const o = e.slice();
  return o[28] = t[n], o[30] = n, o;
}
function Vt(e) {
  const t = e.slice(), n = (
    /*midpoints*/
    t[10][
      /*visibleMidpoint*/
      t[6]
    ]
  );
  return t[28] = n.point, t;
}
function Ut(e) {
  const t = e.slice(), n = (
    /*midpoints*/
    t[10][
      /*visibleMidpoint*/
      t[6]
    ]
  );
  return t[28] = n.point, t;
}
function gn(e) {
  let t, n, o, s;
  return {
    c() {
      t = C("circle"), c(t, "cx", n = /*point*/
      e[28][0]), c(t, "cy", o = /*point*/
      e[28][1]), c(t, "r", s = ot / /*viewportScale*/
      e[3]), c(t, "class", "svelte-1h2slbm");
    },
    m(i, l) {
      Y(i, t, l);
    },
    p(i, l) {
      l[0] & /*midpoints, visibleMidpoint*/
      1088 && n !== (n = /*point*/
      i[28][0]) && c(t, "cx", n), l[0] & /*midpoints, visibleMidpoint*/
      1088 && o !== (o = /*point*/
      i[28][1]) && c(t, "cy", o), l[0] & /*viewportScale*/
      8 && s !== (s = ot / /*viewportScale*/
      i[3]) && c(t, "r", s);
    },
    d(i) {
      i && R(t);
    }
  };
}
function mn(e) {
  let t, n, o, s, i, l, r, a2, f, d;
  return {
    c() {
      t = C("mask"), n = C("rect"), r = C("circle"), c(n, "x", o = /*mask*/
      e[9].x), c(n, "y", s = /*mask*/
      e[9].y), c(n, "width", i = /*mask*/
      e[9].w), c(n, "height", l = /*mask*/
      e[9].h), c(n, "class", "svelte-1h2slbm"), c(r, "cx", a2 = /*point*/
      e[28][0]), c(r, "cy", f = /*point*/
      e[28][1]), c(r, "r", d = ot / /*viewportScale*/
      e[3]), c(r, "class", "svelte-1h2slbm"), c(t, "id", `${/*maskId*/
      e[19]}-inner`), c(t, "class", "a9s-polygon-editor-mask svelte-1h2slbm");
    },
    m(u, h) {
      Y(u, t, h), G(t, n), G(t, r);
    },
    p(u, h) {
      h[0] & /*mask*/
      512 && o !== (o = /*mask*/
      u[9].x) && c(n, "x", o), h[0] & /*mask*/
      512 && s !== (s = /*mask*/
      u[9].y) && c(n, "y", s), h[0] & /*mask*/
      512 && i !== (i = /*mask*/
      u[9].w) && c(n, "width", i), h[0] & /*mask*/
      512 && l !== (l = /*mask*/
      u[9].h) && c(n, "height", l), h[0] & /*midpoints, visibleMidpoint*/
      1088 && a2 !== (a2 = /*point*/
      u[28][0]) && c(r, "cx", a2), h[0] & /*midpoints, visibleMidpoint*/
      1088 && f !== (f = /*point*/
      u[28][1]) && c(r, "cy", f), h[0] & /*viewportScale*/
      8 && d !== (d = ot / /*viewportScale*/
      u[3]) && c(r, "r", d);
    },
    d(u) {
      u && R(t);
    }
  };
}
function pn(e) {
  let t, n;
  return t = new Xe({
    props: {
      class: "a9s-corner-handle",
      x: (
        /*point*/
        e[28][0]
      ),
      y: (
        /*point*/
        e[28][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      ),
      selected: (
        /*selectedCorners*/
        e[8].includes(
          /*idx*/
          e[30]
        )
      )
    }
  }), t.$on(
    "pointerenter",
    /*onEnterHandle*/
    e[11]
  ), t.$on(
    "pointerleave",
    /*onLeaveHandle*/
    e[12]
  ), t.$on(
    "pointerdown",
    /*onHandlePointerDown*/
    e[15]
  ), t.$on("pointerdown", function() {
    se(
      /*grab*/
      e[27](`HANDLE-${/*idx*/
      e[30]}`)
    ) && e[27](`HANDLE-${/*idx*/
    e[30]}`).apply(this, arguments);
  }), t.$on(
    "pointerup",
    /*onHandlePointerUp*/
    e[16](
      /*idx*/
      e[30]
    )
  ), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      e = o;
      const i = {};
      s[0] & /*geom*/
      32 && (i.x = /*point*/
      e[28][0]), s[0] & /*geom*/
      32 && (i.y = /*point*/
      e[28][1]), s[0] & /*viewportScale*/
      8 && (i.scale = /*viewportScale*/
      e[3]), s[0] & /*selectedCorners*/
      256 && (i.selected = /*selectedCorners*/
      e[8].includes(
        /*idx*/
        e[30]
      )), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function _n(e) {
  let t, n;
  return t = new bo({
    props: {
      x: (
        /*point*/
        e[28][0]
      ),
      y: (
        /*point*/
        e[28][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), t.$on("pointerdown", function() {
    se(
      /*onAddPoint*/
      e[18](
        /*visibleMidpoint*/
        e[6]
      )
    ) && e[18](
      /*visibleMidpoint*/
      e[6]
    ).apply(this, arguments);
  }), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      e = o;
      const i = {};
      s[0] & /*midpoints, visibleMidpoint*/
      1088 && (i.x = /*point*/
      e[28][0]), s[0] & /*midpoints, visibleMidpoint*/
      1088 && (i.y = /*point*/
      e[28][1]), s[0] & /*viewportScale*/
      8 && (i.scale = /*viewportScale*/
      e[3]), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function mi(e) {
  let t, n, o, s, i, l, r, a2, f, d, u, h, g, p, y, S, w, A, _, M, P, I = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && gn(Ut(e))
  ), T = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && mn(Vt(e))
  ), B2 = ve(
    /*geom*/
    e[5].points
  ), N = [];
  for (let v = 0; v < B2.length; v += 1)
    N[v] = pn(hn(e, B2, v));
  const Z = (v) => H(N[v], 1, 1, () => {
    N[v] = null;
  });
  let j = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && _n(Bt(e))
  );
  return {
    c() {
      t = C("defs"), n = C("mask"), o = C("rect"), a2 = C("polygon"), I && I.c(), T && T.c(), d = we(), u = C("polygon"), g = we(), p = C("polygon"), S = we();
      for (let v = 0; v < N.length; v += 1)
        N[v].c();
      w = we(), j && j.c(), A = Me(), c(o, "x", s = /*mask*/
      e[9].x), c(o, "y", i = /*mask*/
      e[9].y), c(o, "width", l = /*mask*/
      e[9].w), c(o, "height", r = /*mask*/
      e[9].h), c(o, "class", "svelte-1h2slbm"), c(a2, "points", f = /*geom*/
      e[5].points.map(yn).join(" ")), c(a2, "class", "svelte-1h2slbm"), c(n, "id", `${/*maskId*/
      e[19]}-outer`), c(n, "class", "a9s-polygon-editor-mask svelte-1h2slbm"), c(u, "class", "a9s-outer"), c(u, "mask", `url(#${/*maskId*/
      e[19]}-outer)`), c(u, "points", h = /*geom*/
      e[5].points.map(wn).join(" ")), c(p, "class", "a9s-inner a9s-shape-handle"), c(p, "mask", `url(#${/*maskId*/
      e[19]}-inner)`), c(
        p,
        "style",
        /*computedStyle*/
        e[1]
      ), c(p, "points", y = /*geom*/
      e[5].points.map(bn).join(" "));
    },
    m(v, E) {
      Y(v, t, E), G(t, n), G(n, o), G(n, a2), I && I.m(n, null), T && T.m(t, null), Y(v, d, E), Y(v, u, E), Y(v, g, E), Y(v, p, E), Y(v, S, E);
      for (let m = 0; m < N.length; m += 1)
        N[m] && N[m].m(v, E);
      Y(v, w, E), j && j.m(v, E), Y(v, A, E), _ = true, M || (P = [
        Q(
          u,
          "pointerup",
          /*onShapePointerUp*/
          e[14]
        ),
        Q(u, "pointerdown", function() {
          se(
            /*grab*/
            e[27]("SHAPE")
          ) && e[27]("SHAPE").apply(this, arguments);
        }),
        Q(
          p,
          "pointermove",
          /*onPointerMove*/
          e[13]
        ),
        Q(
          p,
          "pointerup",
          /*onShapePointerUp*/
          e[14]
        ),
        Q(p, "pointerdown", function() {
          se(
            /*grab*/
            e[27]("SHAPE")
          ) && e[27]("SHAPE").apply(this, arguments);
        })
      ], M = true);
    },
    p(v, E) {
      if (e = v, (!_ || E[0] & /*mask*/
      512 && s !== (s = /*mask*/
      e[9].x)) && c(o, "x", s), (!_ || E[0] & /*mask*/
      512 && i !== (i = /*mask*/
      e[9].y)) && c(o, "y", i), (!_ || E[0] & /*mask*/
      512 && l !== (l = /*mask*/
      e[9].w)) && c(o, "width", l), (!_ || E[0] & /*mask*/
      512 && r !== (r = /*mask*/
      e[9].h)) && c(o, "height", r), (!_ || E[0] & /*geom*/
      32 && f !== (f = /*geom*/
      e[5].points.map(yn).join(" "))) && c(a2, "points", f), /*visibleMidpoint*/
      e[6] !== void 0 && !/*isHandleHovered*/
      e[7] ? I ? I.p(Ut(e), E) : (I = gn(Ut(e)), I.c(), I.m(n, null)) : I && (I.d(1), I = null), /*visibleMidpoint*/
      e[6] !== void 0 && !/*isHandleHovered*/
      e[7] ? T ? T.p(Vt(e), E) : (T = mn(Vt(e)), T.c(), T.m(t, null)) : T && (T.d(1), T = null), (!_ || E[0] & /*geom*/
      32 && h !== (h = /*geom*/
      e[5].points.map(wn).join(" "))) && c(u, "points", h), (!_ || E[0] & /*computedStyle*/
      2) && c(
        p,
        "style",
        /*computedStyle*/
        e[1]
      ), (!_ || E[0] & /*geom*/
      32 && y !== (y = /*geom*/
      e[5].points.map(bn).join(" "))) && c(p, "points", y), E[0] & /*geom, viewportScale, selectedCorners, onEnterHandle, onLeaveHandle, onHandlePointerDown, grab, onHandlePointerUp*/
      134322472) {
        B2 = ve(
          /*geom*/
          e[5].points
        );
        let m;
        for (m = 0; m < B2.length; m += 1) {
          const b = hn(e, B2, m);
          N[m] ? (N[m].p(b, E), V(N[m], 1)) : (N[m] = pn(b), N[m].c(), V(N[m], 1), N[m].m(w.parentNode, w));
        }
        for (be(), m = B2.length; m < N.length; m += 1)
          Z(m);
        Ee();
      }
      e[6] !== void 0 && !/*isHandleHovered*/
      e[7] ? j ? (j.p(Bt(e), E), E[0] & /*visibleMidpoint, isHandleHovered*/
      192 && V(j, 1)) : (j = _n(Bt(e)), j.c(), V(j, 1), j.m(A.parentNode, A)) : j && (be(), H(j, 1, 1, () => {
        j = null;
      }), Ee());
    },
    i(v) {
      if (!_) {
        for (let E = 0; E < B2.length; E += 1)
          V(N[E]);
        V(j), _ = true;
      }
    },
    o(v) {
      N = N.filter(Boolean);
      for (let E = 0; E < N.length; E += 1)
        H(N[E]);
      H(j), _ = false;
    },
    d(v) {
      v && (R(t), R(d), R(u), R(g), R(p), R(S), R(w), R(A)), I && I.d(), T && T.d(), Ge(N, v), j && j.d(v), M = false, Se(P);
    }
  };
}
function pi(e) {
  let t, n;
  return t = new tn({
    props: {
      shape: (
        /*shape*/
        e[0]
      ),
      transform: (
        /*transform*/
        e[2]
      ),
      editor: (
        /*editor*/
        e[17]
      ),
      svgEl: (
        /*svgEl*/
        e[4]
      ),
      $$slots: {
        default: [
          mi,
          ({ grab: o }) => ({ 27: o }),
          ({ grab: o }) => [o ? 134217728 : 0]
        ]
      },
      $$scope: { ctx: e }
    }
  }), t.$on(
    "change",
    /*change_handler*/
    e[20]
  ), t.$on(
    "grab",
    /*grab_handler*/
    e[21]
  ), t.$on(
    "release",
    /*release_handler*/
    e[22]
  ), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      const i = {};
      s[0] & /*shape*/
      1 && (i.shape = /*shape*/
      o[0]), s[0] & /*transform*/
      4 && (i.transform = /*transform*/
      o[2]), s[0] & /*svgEl*/
      16 && (i.svgEl = /*svgEl*/
      o[4]), s[0] & /*midpoints, visibleMidpoint, viewportScale, isHandleHovered, geom, selectedCorners, grab, computedStyle, mask*/
      134219754 | s[1] & /*$$scope*/
      1 && (i.$$scope = { dirty: s, ctx: o }), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
var _i = 250;
var yi = 1e3;
var wi = 12;
var ot = 4.5;
var yn = (e) => e.join(",");
var wn = (e) => e.join(",");
var bn = (e) => e.join(",");
function bi(e, t, n) {
  let o, s, i;
  const l = Ve();
  let { shape: r } = t, { computedStyle: a2 } = t, { transform: f } = t, { viewportScale: d = 1 } = t, { svgEl: u } = t, h, g = false, p, y = [];
  const S = () => n(7, g = true), w = () => n(7, g = false), A = (m) => {
    if (y.length > 0 || !s.some((q) => q.visible)) {
      n(6, h = void 0);
      return;
    }
    const [b, L] = f.elementToImage(m.offsetX, m.offsetY), k = (q) => Math.pow(q[0] - b, 2) + Math.pow(q[1] - L, 2), O = o.points.reduce((q, K) => k(K) < k(q) ? K : q), F2 = s.filter((q) => q.visible).reduce((q, K) => k(K.point) < k(q.point) ? K : q), U = Math.pow(yi / d, 2);
    k(O) < U || k(F2.point) < U ? n(6, h = s.indexOf(F2)) : n(6, h = void 0);
  }, _ = () => {
    document.activeElement !== u && u.focus();
  }, M = () => {
    n(8, y = []), _();
  }, P = (m) => {
    n(7, g = true), m.preventDefault(), m.stopPropagation(), p = performance.now();
  }, I = (m) => (b) => {
    if (!p || Le || performance.now() - p > _i)
      return;
    const L = y.includes(m);
    b.metaKey || b.ctrlKey || b.shiftKey ? L ? n(8, y = y.filter((k) => k !== m)) : n(8, y = [...y, m]) : L && y.length > 1 ? n(8, y = [m]) : L ? n(8, y = []) : n(8, y = [m]), _();
  }, T = (m, b, L) => {
    _();
    let k;
    const O = m.geometry;
    y.length > 1 ? k = O.points.map(([U, J2], q) => y.includes(q) ? [U + L[0], J2 + L[1]] : [U, J2]) : b === "SHAPE" ? k = O.points.map(([U, J2]) => [U + L[0], J2 + L[1]]) : k = O.points.map(([U, J2], q) => b === `HANDLE-${q}` ? [U + L[0], J2 + L[1]] : [U, J2]);
    const F2 = ue(k);
    return { ...m, geometry: { points: k, bounds: F2 } };
  }, B2 = (m) => async (b) => {
    b.stopPropagation();
    const L = [
      ...o.points.slice(0, m + 1),
      s[m].point,
      ...o.points.slice(m + 1)
    ], k = ue(L);
    l("change", { ...r, geometry: { points: L, bounds: k } }), await io();
    const O = [...document.querySelectorAll(".a9s-handle")][m + 1];
    if (O != null && O.firstChild) {
      const F2 = new PointerEvent(
        "pointerdown",
        {
          bubbles: true,
          cancelable: true,
          clientX: b.clientX,
          clientY: b.clientY,
          pointerId: b.pointerId,
          pointerType: b.pointerType,
          isPrimary: b.isPrimary,
          buttons: b.buttons
        }
      );
      O.firstChild.dispatchEvent(F2);
    }
  }, N = () => {
    if (o.points.length - y.length < 3)
      return;
    const m = o.points.filter((L, k) => !y.includes(k)), b = ue(m);
    l("change", { ...r, geometry: { points: m, bounds: b } }), n(8, y = []);
  };
  Be(() => {
    if (Le)
      return;
    const m = (b) => {
      (b.key === "Delete" || b.key === "Backspace") && (b.preventDefault(), N());
    };
    return u.addEventListener("pointermove", A), u.addEventListener("keydown", m), () => {
      u.removeEventListener("pointermove", A), u.removeEventListener("keydown", m);
    };
  });
  const Z = `polygon-mask-${Math.random().toString(36).substring(2, 12)}`;
  function j(m) {
    de.call(this, e, m);
  }
  function v(m) {
    de.call(this, e, m);
  }
  function E(m) {
    de.call(this, e, m);
  }
  return e.$$set = (m) => {
    "shape" in m && n(0, r = m.shape), "computedStyle" in m && n(1, a2 = m.computedStyle), "transform" in m && n(2, f = m.transform), "viewportScale" in m && n(3, d = m.viewportScale), "svgEl" in m && n(4, u = m.svgEl);
  }, e.$$.update = () => {
    e.$$.dirty[0] & /*shape*/
    1 && n(5, o = r.geometry), e.$$.dirty[0] & /*geom, viewportScale*/
    40 && n(10, s = Le ? [] : o.points.map((m, b) => {
      const L = b === o.points.length - 1 ? o.points[0] : o.points[b + 1], k = (m[0] + L[0]) / 2, O = (m[1] + L[1]) / 2, U = Math.sqrt(Math.pow(L[0] - k, 2) + Math.pow(L[1] - O, 2)) > wi / d;
      return { point: [k, O], visible: U };
    })), e.$$.dirty[0] & /*geom, viewportScale*/
    40 && n(9, i = kt(o.bounds, ot / d));
  }, [
    r,
    a2,
    f,
    d,
    u,
    o,
    h,
    g,
    y,
    i,
    s,
    S,
    w,
    A,
    M,
    P,
    I,
    T,
    B2,
    Z,
    j,
    v,
    E
  ];
}
var Ei = class extends ye {
  constructor(t) {
    super(), _e(
      this,
      t,
      bi,
      pi,
      ae,
      {
        shape: 0,
        computedStyle: 1,
        transform: 2,
        viewportScale: 3,
        svgEl: 4
      },
      null,
      [-1, -1]
    );
  }
};
var Eo = (e, t, n) => {
  const [o, s] = e, [i, l] = t, r = Math.cos(n), a2 = Math.sin(n), f = o - i, d = s - l;
  return [
    i + f * r - d * a2,
    l + f * a2 + d * r
  ];
};
var vi = (e, t, n, o, s = 0) => {
  const i = [
    [e, t],
    [e + n, t],
    [e + n, t + o],
    [e, t + o]
  ], l = [e + n / 2, t + o / 2];
  return i.map((r) => Eo(r, l, s));
};
var En = (e, t) => {
  const { x: n, y: o, w: s, h: i, rot: l = 0 } = e, r = [n + s / 2, o + i / 2];
  let a2 = [n + s / 2, o - t];
  return Eo(a2, r, l);
};
var Ai = (e, t, n) => {
  const o = Math.cos(n), s = Math.sin(n);
  return [
    e * o + t * s,
    -e * s + t * o
  ];
};
var Si = (e, t, n) => {
  const o = e[0] - n[0], s = e[1] - n[1], i = Math.atan2(s, o), l = t[0] - n[0], r = t[1] - n[1];
  return Math.atan2(r, l) - i;
};
var Mi = (e, t = 10) => {
  const n = t * Math.PI / 180;
  return Math.round(e / n) * n;
};
function ki(e) {
  let t, n, o, s, i, l, r, a2, f, d, u, h, g, p, y, S, w, A, _, M, P, I, T, B2, N, Z, j, v, E, m, b, L, k, O, F2, U, J2, q, K, ee, he2, ne2, Ae, X, fe, te, ge, ie, Ue2, rt, lt, at, Tt, Ce2, Pt, Ie2, Lt, Oe, Ct2, Ne2, W2, It, on2;
  return I = new Xe({
    props: {
      class: "a9s-rotation-handle",
      x: (
        /*rotationHandlePos*/
        e[7][0]
      ),
      y: (
        /*rotationHandlePos*/
        e[7][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), I.$on("pointerdown", function() {
    se(
      /*grab*/
      e[16]("ROTATION")
    ) && e[16]("ROTATION").apply(this, arguments);
  }), Ce2 = new Xe({
    props: {
      class: "a9s-corner-handle-topleft",
      x: (
        /*rotatedCorners*/
        e[6][0][0]
      ),
      y: (
        /*rotatedCorners*/
        e[6][0][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), Ce2.$on("pointerdown", function() {
    se(
      /*grab*/
      e[16]("TOP_LEFT")
    ) && e[16]("TOP_LEFT").apply(this, arguments);
  }), Ie2 = new Xe({
    props: {
      class: "a9s-corner-handle-topright",
      x: (
        /*rotatedCorners*/
        e[6][1][0]
      ),
      y: (
        /*rotatedCorners*/
        e[6][1][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), Ie2.$on("pointerdown", function() {
    se(
      /*grab*/
      e[16]("TOP_RIGHT")
    ) && e[16]("TOP_RIGHT").apply(this, arguments);
  }), Oe = new Xe({
    props: {
      class: "a9s-corner-handle-bottomright",
      x: (
        /*rotatedCorners*/
        e[6][2][0]
      ),
      y: (
        /*rotatedCorners*/
        e[6][2][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), Oe.$on("pointerdown", function() {
    se(
      /*grab*/
      e[16]("BOTTOM_RIGHT")
    ) && e[16]("BOTTOM_RIGHT").apply(this, arguments);
  }), Ne2 = new Xe({
    props: {
      class: "a9s-corner-handle-bottomleft",
      x: (
        /*rotatedCorners*/
        e[6][3][0]
      ),
      y: (
        /*rotatedCorners*/
        e[6][3][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), Ne2.$on("pointerdown", function() {
    se(
      /*grab*/
      e[16]("BOTTOM_LEFT")
    ) && e[16]("BOTTOM_LEFT").apply(this, arguments);
  }), {
    c() {
      t = C("defs"), n = C("mask"), o = C("rect"), a2 = C("polygon"), d = we(), u = C("g"), h = C("line"), w = C("line"), ce(I.$$.fragment), T = we(), B2 = C("g"), N = C("polygon"), j = C("polygon"), E = we(), m = C("line"), F2 = we(), U = C("line"), he2 = we(), ne2 = C("line"), ge = we(), ie = C("line"), Tt = we(), ce(Ce2.$$.fragment), Pt = we(), ce(Ie2.$$.fragment), Lt = we(), ce(Oe.$$.fragment), Ct2 = we(), ce(Ne2.$$.fragment), c(o, "class", "rect-mask-bg svelte-1bwhzbc"), c(o, "x", s = /*mask*/
      e[5].x), c(o, "y", i = /*mask*/
      e[5].y), c(o, "width", l = /*mask*/
      e[5].w), c(o, "height", r = /*mask*/
      e[5].h), c(a2, "class", "rect-mask-fg svelte-1bwhzbc"), c(a2, "points", f = /*rotatedCorners*/
      e[6].map(vn).join(" ")), c(
        n,
        "id",
        /*maskId*/
        e[9]
      ), c(n, "class", "a9s-rectangle-editor-mask svelte-1bwhzbc"), c(h, "class", "a9s-rotation-handle-line-bg"), c(h, "x1", g = /*rotatedCorners*/
      e[6][0][0] + /*rotatedCorners*/
      (e[6][1][0] - /*rotatedCorners*/
      e[6][0][0]) / 2), c(h, "y1", p = /*rotatedCorners*/
      e[6][0][1] + /*rotatedCorners*/
      (e[6][1][1] - /*rotatedCorners*/
      e[6][0][1]) / 2), c(h, "x2", y = /*rotationHandlePos*/
      e[7][0]), c(h, "y2", S = /*rotationHandlePos*/
      e[7][1]), c(h, "pointer-events", "none"), c(w, "class", "a9s-rotation-handle-line-fg"), c(w, "x1", A = /*rotatedCorners*/
      e[6][0][0] + /*rotatedCorners*/
      (e[6][1][0] - /*rotatedCorners*/
      e[6][0][0]) / 2), c(w, "y1", _ = /*rotatedCorners*/
      e[6][0][1] + /*rotatedCorners*/
      (e[6][1][1] - /*rotatedCorners*/
      e[6][0][1]) / 2), c(w, "x2", M = /*rotationHandlePos*/
      e[7][0]), c(w, "y2", P = /*rotationHandlePos*/
      e[7][1]), c(w, "pointer-events", "none"), c(u, "class", "a9s-rotation-handle-group"), c(N, "class", "a9s-outer"), c(N, "mask", `url(#${/*maskId*/
      e[9]})`), c(N, "points", Z = /*rotatedCorners*/
      e[6].map(An).join(" ")), c(j, "class", "a9s-inner a9s-shape-handle"), c(
        j,
        "style",
        /*computedStyle*/
        e[1]
      ), c(j, "points", v = /*rotatedCorners*/
      e[6].map(Sn).join(" ")), c(m, "class", "a9s-edge-handle a9s-edge-handle-top"), c(m, "x1", b = /*rotatedCorners*/
      e[6][0][0]), c(m, "y1", L = /*rotatedCorners*/
      e[6][0][1]), c(m, "x2", k = /*rotatedCorners*/
      e[6][1][0]), c(m, "y2", O = /*rotatedCorners*/
      e[6][1][1]), c(U, "class", "a9s-edge-handle a9s-edge-handle-right"), c(U, "x1", J2 = /*rotatedCorners*/
      e[6][1][0]), c(U, "y1", q = /*rotatedCorners*/
      e[6][1][1]), c(U, "x2", K = /*rotatedCorners*/
      e[6][2][0]), c(U, "y2", ee = /*rotatedCorners*/
      e[6][2][1]), c(ne2, "class", "a9s-edge-handle a9s-edge-handle-bottom"), c(ne2, "x1", Ae = /*rotatedCorners*/
      e[6][2][0]), c(ne2, "y1", X = /*rotatedCorners*/
      e[6][2][1]), c(ne2, "x2", fe = /*rotatedCorners*/
      e[6][3][0]), c(ne2, "y2", te = /*rotatedCorners*/
      e[6][3][1]), c(ie, "class", "a9s-edge-handle a9s-edge-handle-left"), c(ie, "x1", Ue2 = /*rotatedCorners*/
      e[6][3][0]), c(ie, "y1", rt = /*rotatedCorners*/
      e[6][3][1]), c(ie, "x2", lt = /*rotatedCorners*/
      e[6][0][0]), c(ie, "y2", at = /*rotatedCorners*/
      e[6][0][1]);
    },
    m(z, D) {
      Y(z, t, D), G(t, n), G(n, o), G(n, a2), Y(z, d, D), Y(z, u, D), G(u, h), G(u, w), re(I, u, null), Y(z, T, D), Y(z, B2, D), G(B2, N), G(B2, j), Y(z, E, D), Y(z, m, D), Y(z, F2, D), Y(z, U, D), Y(z, he2, D), Y(z, ne2, D), Y(z, ge, D), Y(z, ie, D), Y(z, Tt, D), re(Ce2, z, D), Y(z, Pt, D), re(Ie2, z, D), Y(z, Lt, D), re(Oe, z, D), Y(z, Ct2, D), re(Ne2, z, D), W2 = true, It || (on2 = [
        Q(N, "pointerdown", function() {
          se(
            /*grab*/
            e[16]("SHAPE")
          ) && e[16]("SHAPE").apply(this, arguments);
        }),
        Q(j, "pointerdown", function() {
          se(
            /*grab*/
            e[16]("SHAPE")
          ) && e[16]("SHAPE").apply(this, arguments);
        }),
        Q(m, "pointerdown", function() {
          se(
            /*grab*/
            e[16]("TOP")
          ) && e[16]("TOP").apply(this, arguments);
        }),
        Q(U, "pointerdown", function() {
          se(
            /*grab*/
            e[16]("RIGHT")
          ) && e[16]("RIGHT").apply(this, arguments);
        }),
        Q(ne2, "pointerdown", function() {
          se(
            /*grab*/
            e[16]("BOTTOM")
          ) && e[16]("BOTTOM").apply(this, arguments);
        }),
        Q(ie, "pointerdown", function() {
          se(
            /*grab*/
            e[16]("LEFT")
          ) && e[16]("LEFT").apply(this, arguments);
        })
      ], It = true);
    },
    p(z, D) {
      e = z, (!W2 || D & /*mask*/
      32 && s !== (s = /*mask*/
      e[5].x)) && c(o, "x", s), (!W2 || D & /*mask*/
      32 && i !== (i = /*mask*/
      e[5].y)) && c(o, "y", i), (!W2 || D & /*mask*/
      32 && l !== (l = /*mask*/
      e[5].w)) && c(o, "width", l), (!W2 || D & /*mask*/
      32 && r !== (r = /*mask*/
      e[5].h)) && c(o, "height", r), (!W2 || D & /*rotatedCorners*/
      64 && f !== (f = /*rotatedCorners*/
      e[6].map(vn).join(" "))) && c(a2, "points", f), (!W2 || D & /*rotatedCorners*/
      64 && g !== (g = /*rotatedCorners*/
      e[6][0][0] + /*rotatedCorners*/
      (e[6][1][0] - /*rotatedCorners*/
      e[6][0][0]) / 2)) && c(h, "x1", g), (!W2 || D & /*rotatedCorners*/
      64 && p !== (p = /*rotatedCorners*/
      e[6][0][1] + /*rotatedCorners*/
      (e[6][1][1] - /*rotatedCorners*/
      e[6][0][1]) / 2)) && c(h, "y1", p), (!W2 || D & /*rotationHandlePos*/
      128 && y !== (y = /*rotationHandlePos*/
      e[7][0])) && c(h, "x2", y), (!W2 || D & /*rotationHandlePos*/
      128 && S !== (S = /*rotationHandlePos*/
      e[7][1])) && c(h, "y2", S), (!W2 || D & /*rotatedCorners*/
      64 && A !== (A = /*rotatedCorners*/
      e[6][0][0] + /*rotatedCorners*/
      (e[6][1][0] - /*rotatedCorners*/
      e[6][0][0]) / 2)) && c(w, "x1", A), (!W2 || D & /*rotatedCorners*/
      64 && _ !== (_ = /*rotatedCorners*/
      e[6][0][1] + /*rotatedCorners*/
      (e[6][1][1] - /*rotatedCorners*/
      e[6][0][1]) / 2)) && c(w, "y1", _), (!W2 || D & /*rotationHandlePos*/
      128 && M !== (M = /*rotationHandlePos*/
      e[7][0])) && c(w, "x2", M), (!W2 || D & /*rotationHandlePos*/
      128 && P !== (P = /*rotationHandlePos*/
      e[7][1])) && c(w, "y2", P);
      const ct2 = {};
      D & /*rotationHandlePos*/
      128 && (ct2.x = /*rotationHandlePos*/
      e[7][0]), D & /*rotationHandlePos*/
      128 && (ct2.y = /*rotationHandlePos*/
      e[7][1]), D & /*viewportScale*/
      8 && (ct2.scale = /*viewportScale*/
      e[3]), I.$set(ct2), (!W2 || D & /*rotatedCorners*/
      64 && Z !== (Z = /*rotatedCorners*/
      e[6].map(An).join(" "))) && c(N, "points", Z), (!W2 || D & /*computedStyle*/
      2) && c(
        j,
        "style",
        /*computedStyle*/
        e[1]
      ), (!W2 || D & /*rotatedCorners*/
      64 && v !== (v = /*rotatedCorners*/
      e[6].map(Sn).join(" "))) && c(j, "points", v), (!W2 || D & /*rotatedCorners*/
      64 && b !== (b = /*rotatedCorners*/
      e[6][0][0])) && c(m, "x1", b), (!W2 || D & /*rotatedCorners*/
      64 && L !== (L = /*rotatedCorners*/
      e[6][0][1])) && c(m, "y1", L), (!W2 || D & /*rotatedCorners*/
      64 && k !== (k = /*rotatedCorners*/
      e[6][1][0])) && c(m, "x2", k), (!W2 || D & /*rotatedCorners*/
      64 && O !== (O = /*rotatedCorners*/
      e[6][1][1])) && c(m, "y2", O), (!W2 || D & /*rotatedCorners*/
      64 && J2 !== (J2 = /*rotatedCorners*/
      e[6][1][0])) && c(U, "x1", J2), (!W2 || D & /*rotatedCorners*/
      64 && q !== (q = /*rotatedCorners*/
      e[6][1][1])) && c(U, "y1", q), (!W2 || D & /*rotatedCorners*/
      64 && K !== (K = /*rotatedCorners*/
      e[6][2][0])) && c(U, "x2", K), (!W2 || D & /*rotatedCorners*/
      64 && ee !== (ee = /*rotatedCorners*/
      e[6][2][1])) && c(U, "y2", ee), (!W2 || D & /*rotatedCorners*/
      64 && Ae !== (Ae = /*rotatedCorners*/
      e[6][2][0])) && c(ne2, "x1", Ae), (!W2 || D & /*rotatedCorners*/
      64 && X !== (X = /*rotatedCorners*/
      e[6][2][1])) && c(ne2, "y1", X), (!W2 || D & /*rotatedCorners*/
      64 && fe !== (fe = /*rotatedCorners*/
      e[6][3][0])) && c(ne2, "x2", fe), (!W2 || D & /*rotatedCorners*/
      64 && te !== (te = /*rotatedCorners*/
      e[6][3][1])) && c(ne2, "y2", te), (!W2 || D & /*rotatedCorners*/
      64 && Ue2 !== (Ue2 = /*rotatedCorners*/
      e[6][3][0])) && c(ie, "x1", Ue2), (!W2 || D & /*rotatedCorners*/
      64 && rt !== (rt = /*rotatedCorners*/
      e[6][3][1])) && c(ie, "y1", rt), (!W2 || D & /*rotatedCorners*/
      64 && lt !== (lt = /*rotatedCorners*/
      e[6][0][0])) && c(ie, "x2", lt), (!W2 || D & /*rotatedCorners*/
      64 && at !== (at = /*rotatedCorners*/
      e[6][0][1])) && c(ie, "y2", at);
      const ft2 = {};
      D & /*rotatedCorners*/
      64 && (ft2.x = /*rotatedCorners*/
      e[6][0][0]), D & /*rotatedCorners*/
      64 && (ft2.y = /*rotatedCorners*/
      e[6][0][1]), D & /*viewportScale*/
      8 && (ft2.scale = /*viewportScale*/
      e[3]), Ce2.$set(ft2);
      const ut2 = {};
      D & /*rotatedCorners*/
      64 && (ut2.x = /*rotatedCorners*/
      e[6][1][0]), D & /*rotatedCorners*/
      64 && (ut2.y = /*rotatedCorners*/
      e[6][1][1]), D & /*viewportScale*/
      8 && (ut2.scale = /*viewportScale*/
      e[3]), Ie2.$set(ut2);
      const dt = {};
      D & /*rotatedCorners*/
      64 && (dt.x = /*rotatedCorners*/
      e[6][2][0]), D & /*rotatedCorners*/
      64 && (dt.y = /*rotatedCorners*/
      e[6][2][1]), D & /*viewportScale*/
      8 && (dt.scale = /*viewportScale*/
      e[3]), Oe.$set(dt);
      const ht = {};
      D & /*rotatedCorners*/
      64 && (ht.x = /*rotatedCorners*/
      e[6][3][0]), D & /*rotatedCorners*/
      64 && (ht.y = /*rotatedCorners*/
      e[6][3][1]), D & /*viewportScale*/
      8 && (ht.scale = /*viewportScale*/
      e[3]), Ne2.$set(ht);
    },
    i(z) {
      W2 || (V(I.$$.fragment, z), V(Ce2.$$.fragment, z), V(Ie2.$$.fragment, z), V(Oe.$$.fragment, z), V(Ne2.$$.fragment, z), W2 = true);
    },
    o(z) {
      H(I.$$.fragment, z), H(Ce2.$$.fragment, z), H(Ie2.$$.fragment, z), H(Oe.$$.fragment, z), H(Ne2.$$.fragment, z), W2 = false;
    },
    d(z) {
      z && (R(t), R(d), R(u), R(T), R(B2), R(E), R(m), R(F2), R(U), R(he2), R(ne2), R(ge), R(ie), R(Tt), R(Pt), R(Lt), R(Ct2)), le(I), le(Ce2, z), le(Ie2, z), le(Oe, z), le(Ne2, z), It = false, Se(on2);
    }
  };
}
function Ti(e) {
  let t, n;
  return t = new tn({
    props: {
      shape: (
        /*shape*/
        e[0]
      ),
      transform: (
        /*transform*/
        e[2]
      ),
      editor: (
        /*editor*/
        e[8]
      ),
      svgEl: (
        /*svgEl*/
        e[4]
      ),
      $$slots: {
        default: [
          ki,
          ({ grab: o }) => ({ 16: o }),
          ({ grab: o }) => o ? 65536 : 0
        ]
      },
      $$scope: { ctx: e }
    }
  }), t.$on(
    "grab",
    /*grab_handler*/
    e[12]
  ), t.$on(
    "change",
    /*change_handler*/
    e[13]
  ), t.$on(
    "release",
    /*release_handler*/
    e[14]
  ), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, [s]) {
      const i = {};
      s & /*shape*/
      1 && (i.shape = /*shape*/
      o[0]), s & /*transform*/
      4 && (i.transform = /*transform*/
      o[2]), s & /*svgEl*/
      16 && (i.svgEl = /*svgEl*/
      o[4]), s & /*$$scope, rotatedCorners, viewportScale, grab, computedStyle, rotationHandlePos, mask*/
      196842 && (i.$$scope = { dirty: s, ctx: o }), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
var vn = (e) => `${e[0]},${e[1]}`;
var An = (e) => `${e[0]},${e[1]}`;
var Sn = (e) => `${e[0]},${e[1]}`;
function Pi(e, t, n) {
  let o, s, i, l, r, { shape: a2 } = t, { computedStyle: f } = t, { transform: d } = t, { viewportScale: u = 1 } = t, { svgEl: h } = t, g = false;
  const p = (_, M, P) => {
    let { x: I, y: T, w: B2, h: N, rot: Z = 0 } = _.geometry;
    const [j, v] = P;
    if (M === "ROTATION") {
      const m = En(_.geometry, o), b = m[0] + j, L = m[1] + v, k = [I + B2 / 2, T + N / 2];
      Z += Si([m[0], m[1]], [b, L], k), g && (Z = Mi(Z));
      const O = 2 * Math.PI;
      Z = (Z % O + O) % O;
    } else if (M === "SHAPE")
      I += j, T += v;
    else {
      let m = 0, b = 0, L = B2, k = N;
      const [O, F2] = Z !== 0 ? Ai(j, v, Z) : [j, v];
      switch (M) {
        case "TOP":
        case "TOP_LEFT":
        case "TOP_RIGHT":
          b += F2;
          break;
        case "BOTTOM":
        case "BOTTOM_LEFT":
        case "BOTTOM_RIGHT":
          k += F2;
          break;
      }
      switch (M) {
        case "LEFT":
        case "TOP_LEFT":
        case "BOTTOM_LEFT":
          m += O;
          break;
        case "RIGHT":
        case "TOP_RIGHT":
        case "BOTTOM_RIGHT":
          L += O;
          break;
      }
      const U = (m + L) / 2, J2 = (b + k) / 2;
      B2 = Math.abs(L - m), N = Math.abs(k - b);
      const q = [I + _.geometry.w / 2, T + _.geometry.h / 2], K = [
        U - _.geometry.w / 2,
        J2 - _.geometry.h / 2
      ], ee = Math.cos(Z), he2 = Math.sin(Z), ne2 = q[0] + K[0] * ee - K[1] * he2, Ae = q[1] + K[0] * he2 + K[1] * ee;
      I = ne2 - B2 / 2, T = Ae - N / 2;
    }
    const E = ue(i);
    return {
      ..._,
      geometry: { x: I, y: T, w: B2, h: N, rot: Z, bounds: E }
    };
  };
  Be(() => {
    const _ = (P) => {
      P.key === "Shift" && (g = true);
    }, M = (P) => {
      P.key === "Shift" && (g = false);
    };
    return window.addEventListener("keydown", _), window.addEventListener("keyup", M), () => {
      window.removeEventListener("keydown", _), window.removeEventListener("keyup", M);
    };
  });
  const y = `rect-mask-${Math.random().toString(36).substring(2, 12)}`;
  function S(_) {
    de.call(this, e, _);
  }
  function w(_) {
    de.call(this, e, _);
  }
  function A(_) {
    de.call(this, e, _);
  }
  return e.$$set = (_) => {
    "shape" in _ && n(0, a2 = _.shape), "computedStyle" in _ && n(1, f = _.computedStyle), "transform" in _ && n(2, d = _.transform), "viewportScale" in _ && n(3, u = _.viewportScale), "svgEl" in _ && n(4, h = _.svgEl);
  }, e.$$.update = () => {
    e.$$.dirty & /*viewportScale*/
    8 && n(11, o = 20 / u), e.$$.dirty & /*shape*/
    1 && n(10, s = a2.geometry), e.$$.dirty & /*geom*/
    1024 && n(6, i = vi(s.x, s.y, s.w, s.h, s.rot)), e.$$.dirty & /*geom, ROTATION_HANDLE_OFFSET*/
    3072 && n(7, l = En(s, o)), e.$$.dirty & /*geom, viewportScale*/
    1032 && n(5, r = kt(s.bounds, 5 / u));
  }, [
    a2,
    f,
    d,
    u,
    h,
    r,
    i,
    l,
    p,
    y,
    s,
    o,
    S,
    w,
    A
  ];
}
var Li = class extends ye {
  constructor(t) {
    super(), _e(this, t, Pi, Ti, ae, {
      shape: 0,
      computedStyle: 1,
      transform: 2,
      viewportScale: 3,
      svgEl: 4
    });
  }
};
var Mn = Object.prototype.hasOwnProperty;
function xt(e, t) {
  var n, o;
  if (e === t)
    return true;
  if (e && t && (n = e.constructor) === t.constructor) {
    if (n === Date)
      return e.getTime() === t.getTime();
    if (n === RegExp)
      return e.toString() === t.toString();
    if (n === Array) {
      if ((o = e.length) === t.length)
        for (; o-- && xt(e[o], t[o]); )
          ;
      return o === -1;
    }
    if (!n || typeof e == "object") {
      o = 0;
      for (n in e)
        if (Mn.call(e, n) && ++o && !Mn.call(t, n) || !(n in t) || !xt(e[n], t[n]))
          return false;
      return Object.keys(t).length === o;
    }
  }
  return e !== e && t !== t;
}
var Ci = 12;
var Ii = (e, t) => e.polygons.reduce((n, o, s) => {
  const i = o.rings.reduce((l, r, a2) => {
    const f = r.points.map((d, u) => {
      const h = u === r.points.length - 1 ? r.points[0] : r.points[u + 1], g = (d[0] + h[0]) / 2, p = (d[1] + h[1]) / 2, S = Math.sqrt(
        Math.pow(h[0] - g, 2) + Math.pow(h[1] - p, 2)
      ) > Ci / t;
      return { point: [g, p], visible: S, elementIdx: s, ringIdx: a2, pointIdx: u };
    });
    return [...l, ...f];
  }, []);
  return [...n, ...i];
}, []);
function Xt(e) {
  const t = e.slice(), n = (
    /*midpoints*/
    t[10][
      /*visibleMidpoint*/
      t[6]
    ]
  );
  return t[29] = n.point, t;
}
function kn(e, t, n) {
  const o = e.slice();
  return o[30] = t[n], o[32] = n, o;
}
function Tn(e, t, n) {
  const o = e.slice();
  return o[33] = t[n], o[35] = n, o;
}
function Pn(e, t, n) {
  const o = e.slice();
  return o[29] = t[n], o[37] = n, o;
}
function Ht(e) {
  const t = e.slice(), n = (
    /*midpoints*/
    t[10][
      /*visibleMidpoint*/
      t[6]
    ]
  );
  return t[29] = n.point, t;
}
function Gt(e) {
  const t = e.slice(), n = (
    /*midpoints*/
    t[10][
      /*visibleMidpoint*/
      t[6]
    ]
  );
  return t[29] = n.point, t;
}
function Ln(e) {
  let t, n, o, s;
  return {
    c() {
      t = C("circle"), c(t, "cx", n = /*point*/
      e[29][0]), c(t, "cy", o = /*point*/
      e[29][1]), c(t, "r", s = st / /*viewportScale*/
      e[3]), c(t, "class", "svelte-1vxo6dc");
    },
    m(i, l) {
      Y(i, t, l);
    },
    p(i, l) {
      l[0] & /*midpoints, visibleMidpoint*/
      1088 && n !== (n = /*point*/
      i[29][0]) && c(t, "cx", n), l[0] & /*midpoints, visibleMidpoint*/
      1088 && o !== (o = /*point*/
      i[29][1]) && c(t, "cy", o), l[0] & /*viewportScale*/
      8 && s !== (s = st / /*viewportScale*/
      i[3]) && c(t, "r", s);
    },
    d(i) {
      i && R(t);
    }
  };
}
function Cn(e) {
  let t, n, o, s, i, l, r, a2, f, d;
  return {
    c() {
      t = C("mask"), n = C("rect"), r = C("circle"), c(n, "x", o = /*mask*/
      e[9].x), c(n, "y", s = /*mask*/
      e[9].y), c(n, "width", i = /*mask*/
      e[9].w), c(n, "height", l = /*mask*/
      e[9].h), c(n, "class", "svelte-1vxo6dc"), c(r, "cx", a2 = /*point*/
      e[29][0]), c(r, "cy", f = /*point*/
      e[29][1]), c(r, "r", d = st / /*viewportScale*/
      e[3]), c(r, "class", "svelte-1vxo6dc"), c(t, "id", `${/*maskId*/
      e[18]}-${/*elementIdx*/
      e[32]}-inner`), c(t, "class", "a9s-multipolygon-editor-mask svelte-1vxo6dc");
    },
    m(u, h) {
      Y(u, t, h), G(t, n), G(t, r);
    },
    p(u, h) {
      h[0] & /*mask*/
      512 && o !== (o = /*mask*/
      u[9].x) && c(n, "x", o), h[0] & /*mask*/
      512 && s !== (s = /*mask*/
      u[9].y) && c(n, "y", s), h[0] & /*mask*/
      512 && i !== (i = /*mask*/
      u[9].w) && c(n, "width", i), h[0] & /*mask*/
      512 && l !== (l = /*mask*/
      u[9].h) && c(n, "height", l), h[0] & /*midpoints, visibleMidpoint*/
      1088 && a2 !== (a2 = /*point*/
      u[29][0]) && c(r, "cx", a2), h[0] & /*midpoints, visibleMidpoint*/
      1088 && f !== (f = /*point*/
      u[29][1]) && c(r, "cy", f), h[0] & /*viewportScale*/
      8 && d !== (d = st / /*viewportScale*/
      u[3]) && c(r, "r", d);
    },
    d(u) {
      u && R(t);
    }
  };
}
function In(e) {
  let t, n;
  function o(...s) {
    return (
      /*func*/
      e[19](
        /*elementIdx*/
        e[32],
        /*ringIdx*/
        e[35],
        /*pointIdx*/
        e[37],
        ...s
      )
    );
  }
  return t = new Xe({
    props: {
      class: "a9s-corner-handle",
      x: (
        /*point*/
        e[29][0]
      ),
      y: (
        /*point*/
        e[29][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      ),
      selected: (
        /*selectedCorners*/
        e[8].some(o)
      )
    }
  }), t.$on(
    "pointerenter",
    /*onEnterHandle*/
    e[11]
  ), t.$on(
    "pointerleave",
    /*onLeaveHandle*/
    e[12]
  ), t.$on(
    "pointerdown",
    /*onHandlePointerDown*/
    e[14]
  ), t.$on("pointerdown", function() {
    se(
      /*grab*/
      e[28](`HANDLE-${/*elementIdx*/
      e[32]}-${/*ringIdx*/
      e[35]}-${/*pointIdx*/
      e[37]}`)
    ) && e[28](`HANDLE-${/*elementIdx*/
    e[32]}-${/*ringIdx*/
    e[35]}-${/*pointIdx*/
    e[37]}`).apply(this, arguments);
  }), t.$on(
    "pointerup",
    /*onHandlePointerUp*/
    e[15](
      /*elementIdx*/
      e[32],
      /*ringIdx*/
      e[35],
      /*pointIdx*/
      e[37]
    )
  ), {
    c() {
      ce(t.$$.fragment);
    },
    m(s, i) {
      re(t, s, i), n = true;
    },
    p(s, i) {
      e = s;
      const l = {};
      i[0] & /*geom*/
      32 && (l.x = /*point*/
      e[29][0]), i[0] & /*geom*/
      32 && (l.y = /*point*/
      e[29][1]), i[0] & /*viewportScale*/
      8 && (l.scale = /*viewportScale*/
      e[3]), i[0] & /*selectedCorners*/
      256 && (l.selected = /*selectedCorners*/
      e[8].some(o)), t.$set(l);
    },
    i(s) {
      n || (V(t.$$.fragment, s), n = true);
    },
    o(s) {
      H(t.$$.fragment, s), n = false;
    },
    d(s) {
      le(t, s);
    }
  };
}
function On(e) {
  let t, n, o = ve(
    /*ring*/
    e[33].points
  ), s = [];
  for (let l = 0; l < o.length; l += 1)
    s[l] = In(Pn(e, o, l));
  const i = (l) => H(s[l], 1, 1, () => {
    s[l] = null;
  });
  return {
    c() {
      for (let l = 0; l < s.length; l += 1)
        s[l].c();
      t = Me();
    },
    m(l, r) {
      for (let a2 = 0; a2 < s.length; a2 += 1)
        s[a2] && s[a2].m(l, r);
      Y(l, t, r), n = true;
    },
    p(l, r) {
      if (r[0] & /*geom, viewportScale, selectedCorners, onEnterHandle, onLeaveHandle, onHandlePointerDown, grab, onHandlePointerUp*/
      268491048) {
        o = ve(
          /*ring*/
          l[33].points
        );
        let a2;
        for (a2 = 0; a2 < o.length; a2 += 1) {
          const f = Pn(l, o, a2);
          s[a2] ? (s[a2].p(f, r), V(s[a2], 1)) : (s[a2] = In(f), s[a2].c(), V(s[a2], 1), s[a2].m(t.parentNode, t));
        }
        for (be(), a2 = o.length; a2 < s.length; a2 += 1)
          i(a2);
        Ee();
      }
    },
    i(l) {
      if (!n) {
        for (let r = 0; r < o.length; r += 1)
          V(s[r]);
        n = true;
      }
    },
    o(l) {
      s = s.filter(Boolean);
      for (let r = 0; r < s.length; r += 1)
        H(s[r]);
      n = false;
    },
    d(l) {
      l && R(t), Ge(s, l);
    }
  };
}
function Nn(e) {
  let t, n, o, s, i, l, r, a2, f, d, u, h, g, p, y, S, w, A = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && Ln(Gt(e))
  ), _ = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && Cn(Ht(e))
  ), M = ve(
    /*element*/
    e[30].rings
  ), P = [];
  for (let T = 0; T < M.length; T += 1)
    P[T] = On(Tn(e, M, T));
  const I = (T) => H(P[T], 1, 1, () => {
    P[T] = null;
  });
  return {
    c() {
      t = C("g"), n = C("defs"), o = C("mask"), s = C("rect"), f = C("path"), A && A.c(), _ && _.c(), u = C("path"), g = C("path");
      for (let T = 0; T < P.length; T += 1)
        P[T].c();
      c(s, "x", i = /*mask*/
      e[9].x), c(s, "y", l = /*mask*/
      e[9].y), c(s, "width", r = /*mask*/
      e[9].w), c(s, "height", a2 = /*mask*/
      e[9].h), c(s, "class", "svelte-1vxo6dc"), c(f, "d", d = Pe(
        /*element*/
        e[30]
      )), c(f, "class", "svelte-1vxo6dc"), c(o, "id", `${/*maskId*/
      e[18]}-${/*elementIdx*/
      e[32]}-outer`), c(o, "class", "a9s-multipolygon-editor-mask svelte-1vxo6dc"), c(u, "class", "a9s-outer"), c(u, "mask", `url(#${/*maskId*/
      e[18]}-${/*elementIdx*/
      e[32]}-outer)`), c(u, "fill-rule", "evenodd"), c(u, "d", h = Pe(
        /*element*/
        e[30]
      )), c(g, "class", "a9s-inner"), c(g, "mask", `url(#${/*maskId*/
      e[18]}-${/*elementIdx*/
      e[32]}-inner)`), c(
        g,
        "style",
        /*computedStyle*/
        e[1]
      ), c(g, "fill-rule", "evenodd"), c(g, "d", p = Pe(
        /*element*/
        e[30]
      ));
    },
    m(T, B2) {
      Y(T, t, B2), G(t, n), G(n, o), G(o, s), G(o, f), A && A.m(o, null), _ && _.m(n, null), G(t, u), G(t, g);
      for (let N = 0; N < P.length; N += 1)
        P[N] && P[N].m(t, null);
      y = true, S || (w = [
        Q(
          u,
          "pointerup",
          /*onShapePointerUp*/
          e[13]
        ),
        Q(u, "pointerdown", function() {
          se(
            /*grab*/
            e[28]("SHAPE")
          ) && e[28]("SHAPE").apply(this, arguments);
        }),
        Q(
          g,
          "pointerup",
          /*onShapePointerUp*/
          e[13]
        ),
        Q(g, "pointerdown", function() {
          se(
            /*grab*/
            e[28]("SHAPE")
          ) && e[28]("SHAPE").apply(this, arguments);
        })
      ], S = true);
    },
    p(T, B2) {
      if (e = T, (!y || B2[0] & /*mask*/
      512 && i !== (i = /*mask*/
      e[9].x)) && c(s, "x", i), (!y || B2[0] & /*mask*/
      512 && l !== (l = /*mask*/
      e[9].y)) && c(s, "y", l), (!y || B2[0] & /*mask*/
      512 && r !== (r = /*mask*/
      e[9].w)) && c(s, "width", r), (!y || B2[0] & /*mask*/
      512 && a2 !== (a2 = /*mask*/
      e[9].h)) && c(s, "height", a2), (!y || B2[0] & /*geom*/
      32 && d !== (d = Pe(
        /*element*/
        e[30]
      ))) && c(f, "d", d), /*visibleMidpoint*/
      e[6] !== void 0 && !/*isHandleHovered*/
      e[7] ? A ? A.p(Gt(e), B2) : (A = Ln(Gt(e)), A.c(), A.m(o, null)) : A && (A.d(1), A = null), /*visibleMidpoint*/
      e[6] !== void 0 && !/*isHandleHovered*/
      e[7] ? _ ? _.p(Ht(e), B2) : (_ = Cn(Ht(e)), _.c(), _.m(n, null)) : _ && (_.d(1), _ = null), (!y || B2[0] & /*geom*/
      32 && h !== (h = Pe(
        /*element*/
        e[30]
      ))) && c(u, "d", h), (!y || B2[0] & /*computedStyle*/
      2) && c(
        g,
        "style",
        /*computedStyle*/
        e[1]
      ), (!y || B2[0] & /*geom*/
      32 && p !== (p = Pe(
        /*element*/
        e[30]
      ))) && c(g, "d", p), B2[0] & /*geom, viewportScale, selectedCorners, onEnterHandle, onLeaveHandle, onHandlePointerDown, grab, onHandlePointerUp*/
      268491048) {
        M = ve(
          /*element*/
          e[30].rings
        );
        let N;
        for (N = 0; N < M.length; N += 1) {
          const Z = Tn(e, M, N);
          P[N] ? (P[N].p(Z, B2), V(P[N], 1)) : (P[N] = On(Z), P[N].c(), V(P[N], 1), P[N].m(t, null));
        }
        for (be(), N = M.length; N < P.length; N += 1)
          I(N);
        Ee();
      }
    },
    i(T) {
      if (!y) {
        for (let B2 = 0; B2 < M.length; B2 += 1)
          V(P[B2]);
        y = true;
      }
    },
    o(T) {
      P = P.filter(Boolean);
      for (let B2 = 0; B2 < P.length; B2 += 1)
        H(P[B2]);
      y = false;
    },
    d(T) {
      T && R(t), A && A.d(), _ && _.d(), Ge(P, T), S = false, Se(w);
    }
  };
}
function Dn(e) {
  let t, n;
  return t = new bo({
    props: {
      x: (
        /*point*/
        e[29][0]
      ),
      y: (
        /*point*/
        e[29][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), t.$on("pointerdown", function() {
    se(
      /*onAddPoint*/
      e[17](
        /*visibleMidpoint*/
        e[6]
      )
    ) && e[17](
      /*visibleMidpoint*/
      e[6]
    ).apply(this, arguments);
  }), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      e = o;
      const i = {};
      s[0] & /*midpoints, visibleMidpoint*/
      1088 && (i.x = /*point*/
      e[29][0]), s[0] & /*midpoints, visibleMidpoint*/
      1088 && (i.y = /*point*/
      e[29][1]), s[0] & /*viewportScale*/
      8 && (i.scale = /*viewportScale*/
      e[3]), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function Oi(e) {
  let t, n, o, s = ve(
    /*geom*/
    e[5].polygons
  ), i = [];
  for (let a2 = 0; a2 < s.length; a2 += 1)
    i[a2] = Nn(kn(e, s, a2));
  const l = (a2) => H(i[a2], 1, 1, () => {
    i[a2] = null;
  });
  let r = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && Dn(Xt(e))
  );
  return {
    c() {
      for (let a2 = 0; a2 < i.length; a2 += 1)
        i[a2].c();
      t = we(), r && r.c(), n = Me();
    },
    m(a2, f) {
      for (let d = 0; d < i.length; d += 1)
        i[d] && i[d].m(a2, f);
      Y(a2, t, f), r && r.m(a2, f), Y(a2, n, f), o = true;
    },
    p(a2, f) {
      if (f[0] & /*geom, viewportScale, selectedCorners, onEnterHandle, onLeaveHandle, onHandlePointerDown, grab, onHandlePointerUp, maskId, computedStyle, onShapePointerUp, midpoints, visibleMidpoint, mask, isHandleHovered*/
      268763114) {
        s = ve(
          /*geom*/
          a2[5].polygons
        );
        let d;
        for (d = 0; d < s.length; d += 1) {
          const u = kn(a2, s, d);
          i[d] ? (i[d].p(u, f), V(i[d], 1)) : (i[d] = Nn(u), i[d].c(), V(i[d], 1), i[d].m(t.parentNode, t));
        }
        for (be(), d = s.length; d < i.length; d += 1)
          l(d);
        Ee();
      }
      a2[6] !== void 0 && !/*isHandleHovered*/
      a2[7] ? r ? (r.p(Xt(a2), f), f[0] & /*visibleMidpoint, isHandleHovered*/
      192 && V(r, 1)) : (r = Dn(Xt(a2)), r.c(), V(r, 1), r.m(n.parentNode, n)) : r && (be(), H(r, 1, 1, () => {
        r = null;
      }), Ee());
    },
    i(a2) {
      if (!o) {
        for (let f = 0; f < s.length; f += 1)
          V(i[f]);
        V(r), o = true;
      }
    },
    o(a2) {
      i = i.filter(Boolean);
      for (let f = 0; f < i.length; f += 1)
        H(i[f]);
      H(r), o = false;
    },
    d(a2) {
      a2 && (R(t), R(n)), Ge(i, a2), r && r.d(a2);
    }
  };
}
function Ni(e) {
  let t, n;
  return t = new tn({
    props: {
      shape: (
        /*shape*/
        e[0]
      ),
      transform: (
        /*transform*/
        e[2]
      ),
      editor: (
        /*editor*/
        e[16]
      ),
      svgEl: (
        /*svgEl*/
        e[4]
      ),
      $$slots: {
        default: [
          Oi,
          ({ grab: o }) => ({ 28: o }),
          ({ grab: o }) => [o ? 268435456 : 0]
        ]
      },
      $$scope: { ctx: e }
    }
  }), t.$on(
    "change",
    /*change_handler*/
    e[20]
  ), t.$on(
    "grab",
    /*grab_handler*/
    e[21]
  ), t.$on(
    "release",
    /*release_handler*/
    e[22]
  ), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      const i = {};
      s[0] & /*shape*/
      1 && (i.shape = /*shape*/
      o[0]), s[0] & /*transform*/
      4 && (i.transform = /*transform*/
      o[2]), s[0] & /*svgEl*/
      16 && (i.svgEl = /*svgEl*/
      o[4]), s[0] & /*midpoints, visibleMidpoint, viewportScale, isHandleHovered, geom, selectedCorners, grab, computedStyle, mask*/
      268437482 | s[1] & /*$$scope*/
      128 && (i.$$scope = { dirty: s, ctx: o }), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
var Di = 250;
var Ri = 1e3;
var st = 4.5;
function Yi(e, t, n) {
  let o, s, i;
  const l = Ve();
  let { shape: r } = t, { computedStyle: a2 } = t, { transform: f } = t, { viewportScale: d = 1 } = t, { svgEl: u } = t, h, g = false, p, y = [];
  const S = () => n(7, g = true), w = () => n(7, g = false), A = (b) => {
    if (y.length > 0 || !s.some((K) => K.visible)) {
      n(6, h = void 0);
      return;
    }
    const [L, k] = f.elementToImage(b.offsetX, b.offsetY), O = (K) => Math.pow(K[0] - L, 2) + Math.pow(K[1] - k, 2), F2 = Wo(o).reduce((K, ee) => O(ee) < O(K) ? ee : K), U = s.filter((K) => K.visible).reduce((K, ee) => O(ee.point) < O(K.point) ? ee : K), J2 = Math.pow(Ri / d, 2);
    O(F2) < J2 || O(U.point) < J2 ? n(6, h = s.indexOf(U)) : n(6, h = void 0);
  }, _ = () => {
    document.activeElement !== u && u.focus();
  }, M = () => {
    n(8, y = []), _();
  }, P = (b) => {
    n(7, g = true), b.preventDefault(), b.stopPropagation(), p = performance.now();
  }, I = (b, L, k) => (O) => {
    if (!p || Le || performance.now() - p > Di)
      return;
    const F2 = (J2) => J2.polygon === b && J2.ring === L && J2.point === k, U = y.some(F2);
    O.metaKey || O.ctrlKey || O.shiftKey ? U ? n(8, y = y.filter((J2) => !F2(J2))) : n(8, y = [...y, { polygon: b, ring: L, point: k }]) : U && y.length > 1 ? n(8, y = [{ polygon: b, ring: L, point: k }]) : U ? n(8, y = []) : n(8, y = [{ polygon: b, ring: L, point: k }]), _();
  }, T = (b, L, k) => {
    _();
    const O = b.geometry.polygons;
    let F2;
    if (L === "SHAPE")
      F2 = O.map((U) => {
        const J2 = U.rings.map((K, ee) => ({ points: K.points.map((ne2, Ae) => [ne2[0] + k[0], ne2[1] + k[1]]) })), q = ue(J2[0].points);
        return { rings: J2, bounds: q };
      });
    else {
      const [U, J2, q, K] = L.split("-").map((ee) => parseInt(ee));
      F2 = O.map((ee, he2) => {
        if (he2 === J2) {
          const ne2 = ee.rings.map((X, fe) => fe === q ? { points: X.points.map((ge, ie) => ie === K ? [ge[0] + k[0], ge[1] + k[1]] : ge) } : X), Ae = ue(ne2[0].points);
          return { rings: ne2, bounds: Ae };
        } else
          return ee;
      });
    }
    return {
      ...b,
      geometry: {
        polygons: F2,
        bounds: bt(F2)
      }
    };
  }, B2 = (b) => async (L) => {
    L.stopPropagation();
    const k = s[b], O = o.polygons.map((U, J2) => {
      if (J2 === k.elementIdx) {
        const q = U.rings.map((ee, he2) => he2 === k.ringIdx ? { points: [
          ...ee.points.slice(0, k.pointIdx + 1),
          k.point,
          ...ee.points.slice(k.pointIdx + 1)
        ] } : ee), K = ue(q[0].points);
        return { rings: q, bounds: K };
      } else
        return U;
    });
    l("change", {
      ...r,
      geometry: {
        polygons: O,
        bounds: bt(O)
      }
    }), await io();
    const F2 = [...document.querySelectorAll(".a9s-handle")][b + 1];
    if (F2 != null && F2.firstChild) {
      const U = new PointerEvent(
        "pointerdown",
        {
          bubbles: true,
          cancelable: true,
          clientX: L.clientX,
          clientY: L.clientY,
          pointerId: L.pointerId,
          pointerType: L.pointerType,
          isPrimary: L.isPrimary,
          buttons: L.buttons
        }
      );
      F2.firstChild.dispatchEvent(U);
    }
  }, N = () => {
    const b = o.polygons.map((k, O) => {
      if (y.some((U) => U.polygon === O)) {
        const U = k.rings.map((q, K) => {
          const ee = y.filter((he2) => he2.polygon === O && he2.ring === K);
          return ee.length && q.points.length - ee.length >= 3 ? { points: q.points.filter((ne2, Ae) => !ee.some((X) => X.point === Ae)) } : q;
        }), J2 = ue(U[0].points);
        return { rings: U, bounds: J2 };
      } else
        return k;
    });
    !xt(o.polygons, b) && (l("change", {
      ...r,
      geometry: {
        polygons: b,
        bounds: bt(b)
      }
    }), n(8, y = []));
  };
  Be(() => {
    if (Le)
      return;
    const b = (L) => {
      (L.key === "Delete" || L.key === "Backspace") && (L.preventDefault(), N());
    };
    return u.addEventListener("pointermove", A), u.addEventListener("keydown", b), () => {
      u.removeEventListener("pointermove", A), u.removeEventListener("keydown", b);
    };
  });
  const Z = `polygon-mask-${Math.random().toString(36).substring(2, 12)}`, j = (b, L, k, { polygon: O, ring: F2, point: U }) => O === b && F2 === L && U === k;
  function v(b) {
    de.call(this, e, b);
  }
  function E(b) {
    de.call(this, e, b);
  }
  function m(b) {
    de.call(this, e, b);
  }
  return e.$$set = (b) => {
    "shape" in b && n(0, r = b.shape), "computedStyle" in b && n(1, a2 = b.computedStyle), "transform" in b && n(2, f = b.transform), "viewportScale" in b && n(3, d = b.viewportScale), "svgEl" in b && n(4, u = b.svgEl);
  }, e.$$.update = () => {
    e.$$.dirty[0] & /*shape*/
    1 && n(5, o = r.geometry), e.$$.dirty[0] & /*geom, viewportScale*/
    40 && n(10, s = Le ? [] : Ii(o, d)), e.$$.dirty[0] & /*geom, viewportScale*/
    40 && n(9, i = kt(o.bounds, st / d));
  }, [
    r,
    a2,
    f,
    d,
    u,
    o,
    h,
    g,
    y,
    i,
    s,
    S,
    w,
    M,
    P,
    I,
    T,
    B2,
    Z,
    j,
    v,
    E,
    m
  ];
}
var Bi = class extends ye {
  constructor(t) {
    super(), _e(
      this,
      t,
      Yi,
      Ni,
      ae,
      {
        shape: 0,
        computedStyle: 1,
        transform: 2,
        viewportScale: 3,
        svgEl: 4
      },
      null,
      [-1, -1]
    );
  }
};
var vo = /* @__PURE__ */ new Map([
  [x.RECTANGLE, Li],
  [x.POLYGON, Ei],
  [x.MULTIPOLYGON, Bi]
]);
var Vi = (e) => vo.get(e.type);
var Ui = (e, t) => vo.set(e, t);
function Xi(e, t, n) {
  let o;
  const s = Ve();
  let { annotation: i } = t, { editor: l } = t, { style: r } = t, { target: a2 } = t, { transform: f } = t, { viewportScale: d } = t, u;
  return Be(() => (n(6, u = new l({
    target: a2,
    props: {
      shape: i.target.selector,
      computedStyle: o,
      transform: f,
      viewportScale: d,
      svgEl: a2.closest("svg")
    }
  })), u.$on("change", (h) => {
    u.$$set({ shape: h.detail }), s("change", h.detail);
  }), u.$on("grab", (h) => s("grab", h.detail)), u.$on("release", (h) => s("release", h.detail)), () => {
    u.$destroy();
  })), e.$$set = (h) => {
    "annotation" in h && n(0, i = h.annotation), "editor" in h && n(1, l = h.editor), "style" in h && n(2, r = h.style), "target" in h && n(3, a2 = h.target), "transform" in h && n(4, f = h.transform), "viewportScale" in h && n(5, d = h.viewportScale);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    5 && n(7, o = je(i, r, { selected: true, hovered: true })), e.$$.dirty & /*annotation, editorComponent*/
    65 && i && (u == null || u.$set({ shape: i.target.selector })), e.$$.dirty & /*editorComponent, transform*/
    80 && u && u.$set({ transform: f }), e.$$.dirty & /*editorComponent, viewportScale*/
    96 && u && u.$set({ viewportScale: d }), e.$$.dirty & /*editorComponent, computedStyle*/
    192 && u && o && u.$set({ computedStyle: o });
  }, [
    i,
    l,
    r,
    a2,
    f,
    d,
    u,
    o
  ];
}
var Hi = class extends ye {
  constructor(t) {
    super(), _e(this, t, Xi, null, ae, {
      annotation: 0,
      editor: 1,
      style: 2,
      target: 3,
      transform: 4,
      viewportScale: 5
    });
  }
};
function Gi(e, t, n) {
  const o = Ve();
  let { drawingMode: s } = t, { target: i } = t, { tool: l } = t, { transform: r } = t, { viewportScale: a2 } = t, f;
  return Be(() => {
    const d = i.closest("svg"), u = [], h = (g, p, y) => {
      d == null || d.addEventListener(g, p, y), u.push(() => d == null ? void 0 : d.removeEventListener(g, p, y));
    };
    return n(5, f = new l({
      target: i,
      props: {
        addEventListener: h,
        drawingMode: s,
        transform: r,
        viewportScale: a2
      }
    })), f.$on("create", (g) => o("create", g.detail)), () => {
      u.forEach((g) => g()), f.$destroy();
    };
  }), e.$$set = (d) => {
    "drawingMode" in d && n(0, s = d.drawingMode), "target" in d && n(1, i = d.target), "tool" in d && n(2, l = d.tool), "transform" in d && n(3, r = d.transform), "viewportScale" in d && n(4, a2 = d.viewportScale);
  }, e.$$.update = () => {
    e.$$.dirty & /*toolComponent, transform*/
    40 && f && f.$set({ transform: r }), e.$$.dirty & /*toolComponent, viewportScale*/
    48 && f && f.$set({ viewportScale: a2 });
  }, [s, i, l, r, a2, f];
}
var ji = class extends ye {
  constructor(t) {
    super(), _e(this, t, Gi, null, ae, {
      drawingMode: 0,
      target: 1,
      tool: 2,
      transform: 3,
      viewportScale: 4
    });
  }
};
function Rn(e) {
  let t, n, o, s, i, l, r, a2, f, d;
  return {
    c() {
      t = C("defs"), n = C("mask"), o = C("rect"), a2 = C("rect"), f = C("rect"), d = C("rect"), c(o, "class", "rect-mask-bg svelte-1a76qe7"), c(o, "x", s = /*x*/
      e[1] - /*buffer*/
      e[5]), c(o, "y", i = /*y*/
      e[2] - /*buffer*/
      e[5]), c(o, "width", l = /*w*/
      e[3] + 2 * /*buffer*/
      e[5]), c(o, "height", r = /*h*/
      e[4] + 2 * /*buffer*/
      e[5]), c(a2, "class", "rect-mask-fg svelte-1a76qe7"), c(
        a2,
        "x",
        /*x*/
        e[1]
      ), c(
        a2,
        "y",
        /*y*/
        e[2]
      ), c(
        a2,
        "width",
        /*w*/
        e[3]
      ), c(
        a2,
        "height",
        /*h*/
        e[4]
      ), c(
        n,
        "id",
        /*maskId*/
        e[6]
      ), c(n, "class", "a9s-rubberband-rectangle-mask svelte-1a76qe7"), c(f, "class", "a9s-outer"), c(f, "mask", `url(#${/*maskId*/
      e[6]})`), c(
        f,
        "x",
        /*x*/
        e[1]
      ), c(
        f,
        "y",
        /*y*/
        e[2]
      ), c(
        f,
        "width",
        /*w*/
        e[3]
      ), c(
        f,
        "height",
        /*h*/
        e[4]
      ), c(d, "class", "a9s-inner"), c(
        d,
        "x",
        /*x*/
        e[1]
      ), c(
        d,
        "y",
        /*y*/
        e[2]
      ), c(
        d,
        "width",
        /*w*/
        e[3]
      ), c(
        d,
        "height",
        /*h*/
        e[4]
      );
    },
    m(u, h) {
      Y(u, t, h), G(t, n), G(n, o), G(n, a2), Y(u, f, h), Y(u, d, h);
    },
    p(u, h) {
      h & /*x, buffer*/
      34 && s !== (s = /*x*/
      u[1] - /*buffer*/
      u[5]) && c(o, "x", s), h & /*y, buffer*/
      36 && i !== (i = /*y*/
      u[2] - /*buffer*/
      u[5]) && c(o, "y", i), h & /*w, buffer*/
      40 && l !== (l = /*w*/
      u[3] + 2 * /*buffer*/
      u[5]) && c(o, "width", l), h & /*h, buffer*/
      48 && r !== (r = /*h*/
      u[4] + 2 * /*buffer*/
      u[5]) && c(o, "height", r), h & /*x*/
      2 && c(
        a2,
        "x",
        /*x*/
        u[1]
      ), h & /*y*/
      4 && c(
        a2,
        "y",
        /*y*/
        u[2]
      ), h & /*w*/
      8 && c(
        a2,
        "width",
        /*w*/
        u[3]
      ), h & /*h*/
      16 && c(
        a2,
        "height",
        /*h*/
        u[4]
      ), h & /*x*/
      2 && c(
        f,
        "x",
        /*x*/
        u[1]
      ), h & /*y*/
      4 && c(
        f,
        "y",
        /*y*/
        u[2]
      ), h & /*w*/
      8 && c(
        f,
        "width",
        /*w*/
        u[3]
      ), h & /*h*/
      16 && c(
        f,
        "height",
        /*h*/
        u[4]
      ), h & /*x*/
      2 && c(
        d,
        "x",
        /*x*/
        u[1]
      ), h & /*y*/
      4 && c(
        d,
        "y",
        /*y*/
        u[2]
      ), h & /*w*/
      8 && c(
        d,
        "width",
        /*w*/
        u[3]
      ), h & /*h*/
      16 && c(
        d,
        "height",
        /*h*/
        u[4]
      );
    },
    d(u) {
      u && (R(t), R(f), R(d));
    }
  };
}
function Fi(e) {
  let t, n = (
    /*origin*/
    e[0] && Rn(e)
  );
  return {
    c() {
      t = C("g"), n && n.c(), c(t, "class", "a9s-annotation a9s-rubberband");
    },
    m(o, s) {
      Y(o, t, s), n && n.m(t, null);
    },
    p(o, [s]) {
      o[0] ? n ? n.p(o, s) : (n = Rn(o), n.c(), n.m(t, null)) : n && (n.d(1), n = null);
    },
    i: $,
    o: $,
    d(o) {
      o && R(t), n && n.d();
    }
  };
}
function zi(e, t, n) {
  let o;
  const s = Ve();
  let { addEventListener: i } = t, { drawingMode: l } = t, { transform: r } = t, { viewportScale: a2 = 1 } = t, f, d, u, h, g, p, y;
  const S = (P) => {
    const I = P;
    f = performance.now(), l === "drag" && (n(0, d = r.elementToImage(I.offsetX, I.offsetY)), u = d, n(1, h = d[0]), n(2, g = d[1]), n(3, p = 1), n(4, y = 1));
  }, w = (P) => {
    const I = P;
    d && (u = r.elementToImage(I.offsetX, I.offsetY), n(1, h = Math.min(u[0], d[0])), n(2, g = Math.min(u[1], d[1])), n(3, p = Math.abs(u[0] - d[0])), n(4, y = Math.abs(u[1] - d[1])));
  }, A = (P) => {
    const I = P, T = performance.now() - f;
    if (l === "click") {
      if (T > 300)
        return;
      d ? _() : (n(0, d = r.elementToImage(I.offsetX, I.offsetY)), u = d, n(1, h = d[0]), n(2, g = d[1]), n(3, p = 1), n(4, y = 1));
    } else
      d && (T > 300 || p * y > 100 ? (I.stopPropagation(), _()) : (n(0, d = void 0), u = void 0));
  }, _ = () => {
    if (p * y > 15) {
      const P = {
        type: x.RECTANGLE,
        geometry: {
          bounds: {
            minX: h,
            minY: g,
            maxX: h + p,
            maxY: g + y
          },
          rot: 0,
          x: h,
          y: g,
          w: p,
          h: y
        }
      };
      s("create", P);
    }
    n(0, d = void 0), u = void 0;
  };
  Be(() => {
    i("pointerdown", S), i("pointermove", w), i("pointerup", A, true);
  });
  const M = `rect-mask-${Math.random().toString(36).substring(2, 12)}`;
  return e.$$set = (P) => {
    "addEventListener" in P && n(7, i = P.addEventListener), "drawingMode" in P && n(8, l = P.drawingMode), "transform" in P && n(9, r = P.transform), "viewportScale" in P && n(10, a2 = P.viewportScale);
  }, e.$$.update = () => {
    e.$$.dirty & /*viewportScale*/
    1024 && n(5, o = 2 / a2);
  }, [
    d,
    h,
    g,
    p,
    y,
    o,
    M,
    i,
    l,
    r,
    a2
  ];
}
var qi = class extends ye {
  constructor(t) {
    super(), _e(this, t, zi, Fi, ae, {
      addEventListener: 7,
      drawingMode: 8,
      transform: 9,
      viewportScale: 10
    });
  }
};
function jt(e) {
  const t = e.slice(), n = (
    /*coords*/
    t[2].map((o) => o.join(",")).join(" ")
  );
  return t[19] = n, t;
}
function Yn(e) {
  let t, n, o, s, i, l, r, a2, f, d, u, h, g, p, y = (
    /*isClosable*/
    e[1] && Bn(e)
  );
  return {
    c() {
      t = C("defs"), n = C("mask"), o = C("rect"), a2 = C("polygon"), d = C("polygon"), h = C("polygon"), y && y.c(), p = Me(), c(o, "x", s = /*mask*/
      e[3].x), c(o, "y", i = /*mask*/
      e[3].y), c(o, "width", l = /*mask*/
      e[3].w), c(o, "height", r = /*mask*/
      e[3].h), c(o, "class", "svelte-18wrg3t"), c(a2, "points", f = /*str*/
      e[19]), c(a2, "class", "svelte-18wrg3t"), c(
        n,
        "id",
        /*maskId*/
        e[5]
      ), c(n, "class", "a9s-rubberband-polygon-mask svelte-18wrg3t"), c(d, "class", "a9s-outer"), c(d, "mask", `url(#${/*maskId*/
      e[5]})`), c(d, "points", u = /*str*/
      e[19]), c(h, "class", "a9s-inner"), c(h, "points", g = /*str*/
      e[19]);
    },
    m(S, w) {
      Y(S, t, w), G(t, n), G(n, o), G(n, a2), Y(S, d, w), Y(S, h, w), y && y.m(S, w), Y(S, p, w);
    },
    p(S, w) {
      w & /*mask*/
      8 && s !== (s = /*mask*/
      S[3].x) && c(o, "x", s), w & /*mask*/
      8 && i !== (i = /*mask*/
      S[3].y) && c(o, "y", i), w & /*mask*/
      8 && l !== (l = /*mask*/
      S[3].w) && c(o, "width", l), w & /*mask*/
      8 && r !== (r = /*mask*/
      S[3].h) && c(o, "height", r), w & /*coords*/
      4 && f !== (f = /*str*/
      S[19]) && c(a2, "points", f), w & /*coords*/
      4 && u !== (u = /*str*/
      S[19]) && c(d, "points", u), w & /*coords*/
      4 && g !== (g = /*str*/
      S[19]) && c(h, "points", g), /*isClosable*/
      S[1] ? y ? y.p(S, w) : (y = Bn(S), y.c(), y.m(p.parentNode, p)) : y && (y.d(1), y = null);
    },
    d(S) {
      S && (R(t), R(d), R(h), R(p)), y && y.d(S);
    }
  };
}
function Bn(e) {
  let t, n, o;
  return {
    c() {
      t = C("circle"), c(t, "class", "a9s-handle svelte-18wrg3t"), c(t, "cx", n = /*points*/
      e[0][0][0]), c(t, "cy", o = /*points*/
      e[0][0][1]), c(
        t,
        "r",
        /*handleRadius*/
        e[4]
      );
    },
    m(s, i) {
      Y(s, t, i);
    },
    p(s, i) {
      i & /*points*/
      1 && n !== (n = /*points*/
      s[0][0][0]) && c(t, "cx", n), i & /*points*/
      1 && o !== (o = /*points*/
      s[0][0][1]) && c(t, "cy", o), i & /*handleRadius*/
      16 && c(
        t,
        "r",
        /*handleRadius*/
        s[4]
      );
    },
    d(s) {
      s && R(t);
    }
  };
}
function Ki(e) {
  let t, n = (
    /*mask*/
    e[3] && Yn(jt(e))
  );
  return {
    c() {
      t = C("g"), n && n.c(), c(t, "class", "a9s-annotation a9s-rubberband");
    },
    m(o, s) {
      Y(o, t, s), n && n.m(t, null);
    },
    p(o, [s]) {
      o[3] ? n ? n.p(jt(o), s) : (n = Yn(jt(o)), n.c(), n.m(t, null)) : n && (n.d(1), n = null);
    },
    i: $,
    o: $,
    d(o) {
      o && R(t), n && n.d();
    }
  };
}
var Wi = 20;
var Zi = 1500;
function Ji(e, t, n) {
  let o, s, i;
  const l = Ve();
  let { addEventListener: r } = t, { drawingMode: a2 } = t, { transform: f } = t, { viewportScale: d = 1 } = t, u, h = [], g, p, y = false;
  const S = (I) => {
    const T = I, { timeStamp: B2, offsetX: N, offsetY: Z } = T;
    if (u = { timeStamp: B2, offsetX: N, offsetY: Z }, a2 === "drag" && h.length === 0) {
      const j = f.elementToImage(T.offsetX, T.offsetY);
      h.push(j), n(10, g = j);
    }
  }, w = (I) => {
    const T = I;
    if (p && clearTimeout(p), h.length > 0) {
      if (n(10, g = f.elementToImage(T.offsetX, T.offsetY)), h.length > 2) {
        const B2 = wt(g, h[0]) * d;
        n(1, y = B2 < Wi);
      }
      T.pointerType === "touch" && (p = setTimeout(
        () => {
          _();
        },
        Zi
      ));
    }
  }, A = (I) => {
    const T = I;
    if (p && clearTimeout(p), a2 === "click") {
      const B2 = T.timeStamp - u.timeStamp, N = wt([u.offsetX, u.offsetY], [T.offsetX, T.offsetY]);
      if (B2 > 300 || N > 15)
        return;
      if (y)
        M();
      else if (h.length === 0) {
        const Z = f.elementToImage(T.offsetX, T.offsetY);
        h.push(Z), n(10, g = Z);
      } else
        g && h.push(g);
    } else {
      if (!g)
        return;
      if (h.length === 1 && wt(h[0], g) <= 4) {
        n(0, h = []), n(10, g = void 0);
        return;
      }
      T.stopImmediatePropagation(), y ? M() : h.push(g);
    }
  }, _ = () => {
    if (!g)
      return;
    const I = h.slice(0, -1);
    if (I.length < 3)
      return;
    const T = {
      type: x.POLYGON,
      geometry: {
        bounds: ue(h),
        points: I
      }
    };
    Jt(T) > 4 && (n(0, h = []), n(10, g = void 0), l("create", T));
  }, M = () => {
    const I = {
      type: x.POLYGON,
      geometry: {
        bounds: ue(h),
        points: [...h]
      }
    };
    n(0, h = []), n(10, g = void 0), l("create", I);
  };
  Be(() => {
    r("pointerdown", S, true), r("pointermove", w), r("pointerup", A, true), r("dblclick", _, true);
  });
  const P = `polygon-mask-${Math.random().toString(36).substring(2, 12)}`;
  return e.$$set = (I) => {
    "addEventListener" in I && n(6, r = I.addEventListener), "drawingMode" in I && n(7, a2 = I.drawingMode), "transform" in I && n(8, f = I.transform), "viewportScale" in I && n(9, d = I.viewportScale);
  }, e.$$.update = () => {
    e.$$.dirty & /*viewportScale*/
    512 && n(4, o = 4 / d), e.$$.dirty & /*cursor, isClosable, points*/
    1027 && n(2, s = g ? y ? h : [...h, g] : []), e.$$.dirty & /*coords, viewportScale*/
    516 && n(3, i = s.length > 0 ? kt(ue(s), 2 / d) : void 0);
  }, [
    h,
    y,
    s,
    i,
    o,
    P,
    r,
    a2,
    f,
    d,
    g
  ];
}
var Qi = class extends ye {
  constructor(t) {
    super(), _e(this, t, Ji, Ki, ae, {
      addEventListener: 6,
      drawingMode: 7,
      transform: 8,
      viewportScale: 9
    });
  }
};
var nn = /* @__PURE__ */ new Map([
  ["rectangle", { tool: qi }],
  ["polygon", { tool: Qi }]
]);
var Ao = () => [...nn.keys()];
var So = (e) => nn.get(e);
var xi = (e, t, n = {}) => nn.set(e, { tool: t, opts: n });
function $i(e) {
  let t, n, o, s, i;
  return {
    c() {
      t = C("g"), n = C("ellipse"), s = C("ellipse"), c(n, "class", "a9s-outer"), c(n, "style", o = /*computedStyle*/
      e[1] ? "display:none;" : void 0), c(
        n,
        "cx",
        /*cx*/
        e[2]
      ), c(
        n,
        "cy",
        /*cy*/
        e[3]
      ), c(
        n,
        "rx",
        /*rx*/
        e[4]
      ), c(
        n,
        "ry",
        /*ry*/
        e[5]
      ), c(s, "class", "a9s-inner"), c(
        s,
        "style",
        /*computedStyle*/
        e[1]
      ), c(
        s,
        "cx",
        /*cx*/
        e[2]
      ), c(
        s,
        "cy",
        /*cy*/
        e[3]
      ), c(
        s,
        "rx",
        /*rx*/
        e[4]
      ), c(
        s,
        "ry",
        /*ry*/
        e[5]
      ), c(t, "class", "a9s-annotation"), c(t, "data-id", i = /*annotation*/
      e[0].id);
    },
    m(l, r) {
      Y(l, t, r), G(t, n), G(t, s);
    },
    p(l, [r]) {
      r & /*computedStyle*/
      2 && o !== (o = /*computedStyle*/
      l[1] ? "display:none;" : void 0) && c(n, "style", o), r & /*computedStyle*/
      2 && c(
        s,
        "style",
        /*computedStyle*/
        l[1]
      ), r & /*annotation*/
      1 && i !== (i = /*annotation*/
      l[0].id) && c(t, "data-id", i);
    },
    i: $,
    o: $,
    d(l) {
      l && R(t);
    }
  };
}
function er(e, t, n) {
  let o, { annotation: s } = t, { geom: i } = t, { style: l } = t;
  const { cx: r, cy: a2, rx: f, ry: d } = i;
  return e.$$set = (u) => {
    "annotation" in u && n(0, s = u.annotation), "geom" in u && n(6, i = u.geom), "style" in u && n(7, l = u.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    129 && n(1, o = je(s, l));
  }, [s, o, r, a2, f, d, i, l];
}
var tr = class extends ye {
  constructor(t) {
    super(), _e(this, t, er, $i, ae, { annotation: 0, geom: 6, style: 7 });
  }
};
function nr(e) {
  let t, n, o, s, i;
  return {
    c() {
      t = C("g"), n = C("line"), s = C("line"), c(n, "class", "a9s-outer"), c(n, "style", o = /*computedStyle*/
      e[1] ? "display:none;" : void 0), c(
        n,
        "x1",
        /*x1*/
        e[2]
      ), c(
        n,
        "y1",
        /*y1*/
        e[3]
      ), c(
        n,
        "x2",
        /*x2*/
        e[4]
      ), c(
        n,
        "y2",
        /*y2*/
        e[5]
      ), c(s, "class", "a9s-inner"), c(
        s,
        "style",
        /*computedStyle*/
        e[1]
      ), c(
        s,
        "x1",
        /*x1*/
        e[2]
      ), c(
        s,
        "y1",
        /*y1*/
        e[3]
      ), c(
        s,
        "x2",
        /*x2*/
        e[4]
      ), c(
        s,
        "y2",
        /*y2*/
        e[5]
      ), c(t, "class", "a9s-annotation"), c(t, "data-id", i = /*annotation*/
      e[0].id);
    },
    m(l, r) {
      Y(l, t, r), G(t, n), G(t, s);
    },
    p(l, [r]) {
      r & /*computedStyle*/
      2 && o !== (o = /*computedStyle*/
      l[1] ? "display:none;" : void 0) && c(n, "style", o), r & /*computedStyle*/
      2 && c(
        s,
        "style",
        /*computedStyle*/
        l[1]
      ), r & /*annotation*/
      1 && i !== (i = /*annotation*/
      l[0].id) && c(t, "data-id", i);
    },
    i: $,
    o: $,
    d(l) {
      l && R(t);
    }
  };
}
function or(e, t, n) {
  let o, { annotation: s } = t, { geom: i } = t, { style: l } = t;
  const { points: r } = i, [[a2, f], [d, u]] = r;
  return e.$$set = (h) => {
    "annotation" in h && n(0, s = h.annotation), "geom" in h && n(6, i = h.geom), "style" in h && n(7, l = h.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    129 && n(1, o = je(s, l));
  }, [s, o, a2, f, d, u, i, l];
}
var sr = class extends ye {
  constructor(t) {
    super(), _e(this, t, or, nr, ae, { annotation: 0, geom: 6, style: 7 });
  }
};
function Vn(e, t, n) {
  const o = e.slice();
  return o[5] = t[n], o;
}
function Un(e) {
  let t, n, o;
  return {
    c() {
      t = C("path"), o = C("path"), c(t, "class", "a9s-outer"), c(t, "style", n = /*computedStyle*/
      e[1] ? "display:none;" : void 0), c(t, "fill-rule", "evenodd"), c(t, "d", Pe(
        /*polygonElement*/
        e[5]
      )), c(o, "class", "a9s-inner"), c(
        o,
        "style",
        /*computedStyle*/
        e[1]
      ), c(o, "fill-rule", "evenodd"), c(o, "d", Pe(
        /*polygonElement*/
        e[5]
      ));
    },
    m(s, i) {
      Y(s, t, i), Y(s, o, i);
    },
    p(s, i) {
      i & /*computedStyle*/
      2 && n !== (n = /*computedStyle*/
      s[1] ? "display:none;" : void 0) && c(t, "style", n), i & /*computedStyle*/
      2 && c(
        o,
        "style",
        /*computedStyle*/
        s[1]
      );
    },
    d(s) {
      s && (R(t), R(o));
    }
  };
}
function ir(e) {
  let t, n, o = ve(
    /*polygons*/
    e[2]
  ), s = [];
  for (let i = 0; i < o.length; i += 1)
    s[i] = Un(Vn(e, o, i));
  return {
    c() {
      t = C("g");
      for (let i = 0; i < s.length; i += 1)
        s[i].c();
      c(t, "class", "a9s-annotation"), c(t, "data-id", n = /*annotation*/
      e[0].id);
    },
    m(i, l) {
      Y(i, t, l);
      for (let r = 0; r < s.length; r += 1)
        s[r] && s[r].m(t, null);
    },
    p(i, [l]) {
      if (l & /*computedStyle, polygons, undefined*/
      6) {
        o = ve(
          /*polygons*/
          i[2]
        );
        let r;
        for (r = 0; r < o.length; r += 1) {
          const a2 = Vn(i, o, r);
          s[r] ? s[r].p(a2, l) : (s[r] = Un(a2), s[r].c(), s[r].m(t, null));
        }
        for (; r < s.length; r += 1)
          s[r].d(1);
        s.length = o.length;
      }
      l & /*annotation*/
      1 && n !== (n = /*annotation*/
      i[0].id) && c(t, "data-id", n);
    },
    i: $,
    o: $,
    d(i) {
      i && R(t), Ge(s, i);
    }
  };
}
function rr(e, t, n) {
  let o, { annotation: s } = t, { geom: i } = t, { style: l } = t;
  const { polygons: r } = i;
  return e.$$set = (a2) => {
    "annotation" in a2 && n(0, s = a2.annotation), "geom" in a2 && n(3, i = a2.geom), "style" in a2 && n(4, l = a2.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    17 && n(1, o = je(s, l));
  }, [s, o, r, i, l];
}
var lr = class extends ye {
  constructor(t) {
    super(), _e(this, t, rr, ir, ae, { annotation: 0, geom: 3, style: 4 });
  }
};
function ar(e) {
  let t, n, o, s, i;
  return {
    c() {
      t = C("g"), n = C("polygon"), s = C("polygon"), c(n, "class", "a9s-outer"), c(n, "style", o = /*computedStyle*/
      e[1] ? "display:none;" : void 0), c(
        n,
        "points",
        /*points*/
        e[2].map(cr).join(" ")
      ), c(s, "class", "a9s-inner"), c(
        s,
        "style",
        /*computedStyle*/
        e[1]
      ), c(
        s,
        "points",
        /*points*/
        e[2].map(fr).join(" ")
      ), c(t, "class", "a9s-annotation"), c(t, "data-id", i = /*annotation*/
      e[0].id);
    },
    m(l, r) {
      Y(l, t, r), G(t, n), G(t, s);
    },
    p(l, [r]) {
      r & /*computedStyle*/
      2 && o !== (o = /*computedStyle*/
      l[1] ? "display:none;" : void 0) && c(n, "style", o), r & /*computedStyle*/
      2 && c(
        s,
        "style",
        /*computedStyle*/
        l[1]
      ), r & /*annotation*/
      1 && i !== (i = /*annotation*/
      l[0].id) && c(t, "data-id", i);
    },
    i: $,
    o: $,
    d(l) {
      l && R(t);
    }
  };
}
var cr = (e) => e.join(",");
var fr = (e) => e.join(",");
function ur(e, t, n) {
  let o, { annotation: s } = t, { geom: i } = t, { style: l } = t;
  const { points: r } = i;
  return e.$$set = (a2) => {
    "annotation" in a2 && n(0, s = a2.annotation), "geom" in a2 && n(3, i = a2.geom), "style" in a2 && n(4, l = a2.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    17 && n(1, o = je(s, l));
  }, [s, o, r, i, l];
}
var dr = class extends ye {
  constructor(t) {
    super(), _e(this, t, ur, ar, ae, { annotation: 0, geom: 3, style: 4 });
  }
};
function hr(e) {
  let t, n, o, s, i, l, r;
  return {
    c() {
      t = C("g"), n = C("path"), i = C("path"), c(n, "class", o = Ze(`a9s-outer ${/*cssClass*/
      e[1]}`) + " svelte-1w0132l"), c(n, "style", s = /*computedStyle*/
      e[3] ? "display:none;" : void 0), c(
        n,
        "d",
        /*d*/
        e[2]
      ), c(i, "class", l = Ze(`a9s-inner ${/*cssClass*/
      e[1]}`) + " svelte-1w0132l"), c(
        i,
        "style",
        /*computedStyle*/
        e[3]
      ), c(
        i,
        "d",
        /*d*/
        e[2]
      ), c(t, "class", "a9s-annotation"), c(t, "data-id", r = /*annotation*/
      e[0].id);
    },
    m(a2, f) {
      Y(a2, t, f), G(t, n), G(t, i);
    },
    p(a2, [f]) {
      f & /*cssClass*/
      2 && o !== (o = Ze(`a9s-outer ${/*cssClass*/
      a2[1]}`) + " svelte-1w0132l") && c(n, "class", o), f & /*computedStyle*/
      8 && s !== (s = /*computedStyle*/
      a2[3] ? "display:none;" : void 0) && c(n, "style", s), f & /*d*/
      4 && c(
        n,
        "d",
        /*d*/
        a2[2]
      ), f & /*cssClass*/
      2 && l !== (l = Ze(`a9s-inner ${/*cssClass*/
      a2[1]}`) + " svelte-1w0132l") && c(i, "class", l), f & /*computedStyle*/
      8 && c(
        i,
        "style",
        /*computedStyle*/
        a2[3]
      ), f & /*d*/
      4 && c(
        i,
        "d",
        /*d*/
        a2[2]
      ), f & /*annotation*/
      1 && r !== (r = /*annotation*/
      a2[0].id) && c(t, "data-id", r);
    },
    i: $,
    o: $,
    d(a2) {
      a2 && R(t);
    }
  };
}
function gr(e, t, n) {
  let o, s, i, { annotation: l } = t, { geom: r } = t, { style: a2 } = t;
  return e.$$set = (f) => {
    "annotation" in f && n(0, l = f.annotation), "geom" in f && n(4, r = f.geom), "style" in f && n(5, a2 = f.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    33 && n(3, o = je(l, a2)), e.$$.dirty & /*geom*/
    16 && n(2, s = fo(r)), e.$$.dirty & /*geom*/
    16 && n(1, i = r.closed ? "closed" : "open");
  }, [l, i, s, o, r, a2];
}
var mr = class extends ye {
  constructor(t) {
    super(), _e(this, t, gr, hr, ae, { annotation: 0, geom: 4, style: 5 });
  }
};
function pr(e) {
  let t, n, o, s, i, l;
  return {
    c() {
      t = C("g"), n = C("g"), o = C("rect"), i = C("rect"), c(o, "class", "a9s-outer"), c(o, "style", s = /*computedStyle*/
      e[6] ? "display:none;" : void 0), c(
        o,
        "x",
        /*x*/
        e[4]
      ), c(
        o,
        "y",
        /*y*/
        e[2]
      ), c(
        o,
        "width",
        /*w*/
        e[3]
      ), c(
        o,
        "height",
        /*h*/
        e[1]
      ), c(i, "class", "a9s-inner"), c(
        i,
        "style",
        /*computedStyle*/
        e[6]
      ), c(
        i,
        "x",
        /*x*/
        e[4]
      ), c(
        i,
        "y",
        /*y*/
        e[2]
      ), c(
        i,
        "width",
        /*w*/
        e[3]
      ), c(
        i,
        "height",
        /*h*/
        e[1]
      ), c(
        n,
        "transform",
        /*rectTransform*/
        e[5]
      ), c(t, "class", "a9s-annotation"), c(t, "data-id", l = /*annotation*/
      e[0].id);
    },
    m(r, a2) {
      Y(r, t, a2), G(t, n), G(n, o), G(n, i);
    },
    p(r, [a2]) {
      a2 & /*computedStyle*/
      64 && s !== (s = /*computedStyle*/
      r[6] ? "display:none;" : void 0) && c(o, "style", s), a2 & /*x*/
      16 && c(
        o,
        "x",
        /*x*/
        r[4]
      ), a2 & /*y*/
      4 && c(
        o,
        "y",
        /*y*/
        r[2]
      ), a2 & /*w*/
      8 && c(
        o,
        "width",
        /*w*/
        r[3]
      ), a2 & /*h*/
      2 && c(
        o,
        "height",
        /*h*/
        r[1]
      ), a2 & /*computedStyle*/
      64 && c(
        i,
        "style",
        /*computedStyle*/
        r[6]
      ), a2 & /*x*/
      16 && c(
        i,
        "x",
        /*x*/
        r[4]
      ), a2 & /*y*/
      4 && c(
        i,
        "y",
        /*y*/
        r[2]
      ), a2 & /*w*/
      8 && c(
        i,
        "width",
        /*w*/
        r[3]
      ), a2 & /*h*/
      2 && c(
        i,
        "height",
        /*h*/
        r[1]
      ), a2 & /*rectTransform*/
      32 && c(
        n,
        "transform",
        /*rectTransform*/
        r[5]
      ), a2 & /*annotation*/
      1 && l !== (l = /*annotation*/
      r[0].id) && c(t, "data-id", l);
    },
    i: $,
    o: $,
    d(r) {
      r && R(t);
    }
  };
}
function _r(e, t, n) {
  let o, s, i, l, r, a2, f, { annotation: d } = t, { geom: u } = t, { style: h } = t;
  return e.$$set = (g) => {
    "annotation" in g && n(0, d = g.annotation), "geom" in g && n(7, u = g.geom), "style" in g && n(8, h = g.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    257 && n(6, o = je(d, h)), e.$$.dirty & /*geom*/
    128 && n(4, { x: s, y: i, w: l, h: r, rot: a2 } = u, s, (n(2, i), n(7, u)), (n(3, l), n(7, u)), (n(1, r), n(7, u)), (n(9, a2), n(7, u))), e.$$.dirty & /*rot, x, w, y, h*/
    542 && n(5, f = (a2 ?? 0) !== 0 ? `translate(${s + l / 2}, ${i + r / 2}) rotate(${(a2 ?? 0) * 180 / Math.PI}) translate(${-(s + l / 2)}, ${-(i + r / 2)})` : void 0);
  }, [d, r, i, l, s, f, o, u, h, a2];
}
var yr = class extends ye {
  constructor(t) {
    super(), _e(this, t, _r, pr, ae, { annotation: 0, geom: 7, style: 8 });
  }
};
var nl = {
  elementToImage: (e, t) => [e, t]
};
var wr = (e) => ({
  elementToImage: (t, n) => {
    const o = e.getBoundingClientRect(), s = e.createSVGPoint();
    s.x = t + o.x, s.y = n + o.y;
    const { x: i, y: l } = s.matrixTransform(e.getScreenCTM().inverse());
    return [i, l];
  }
});
var br = 250;
var Er = (e, t) => {
  const n = Ve();
  let o;
  return { onPointerDown: () => o = performance.now(), onPointerUp: (l) => {
    if (performance.now() - o < br) {
      const { x: a2, y: f } = Mo(l, e), d = Le ? 10 : 2, u = t.getAt(a2, f, void 0, d);
      u ? n("click", { originalEvent: l, annotation: u }) : n("click", { originalEvent: l });
    }
  } };
};
var Mo = (e, t) => {
  const n = t.createSVGPoint(), o = t.getBoundingClientRect(), s = e.clientX - o.x, i = e.clientY - o.y, { left: l, top: r } = t.getBoundingClientRect();
  return n.x = s + l, n.y = i + r, n.matrixTransform(t.getScreenCTM().inverse());
};
function Xn(e, t, n) {
  const o = e.slice();
  return o[39] = t[n], o;
}
function Hn(e, t, n) {
  const o = e.slice();
  return o[42] = t[n], o;
}
function Ft(e) {
  const t = e.slice(), n = (
    /*annotation*/
    t[42].target.selector
  );
  return t[45] = n, t;
}
function Gn(e) {
  let t = (
    /*annotation*/
    e[42]
  ), n, o, s = jn(e);
  return {
    c() {
      s.c(), n = Me();
    },
    m(i, l) {
      s.m(i, l), Y(i, n, l), o = true;
    },
    p(i, l) {
      l[0] & /*$store*/
      131072 && ae(t, t = /*annotation*/
      i[42]) ? (be(), H(s, 1, 1, $), Ee(), s = jn(i), s.c(), V(s, 1), s.m(n.parentNode, n)) : s.p(i, l);
    },
    i(i) {
      o || (V(s), o = true);
    },
    o(i) {
      H(s), o = false;
    },
    d(i) {
      i && R(n), s.d(i);
    }
  };
}
function vr(e) {
  let t, n;
  return t = new sr({
    props: {
      annotation: (
        /*annotation*/
        e[42]
      ),
      geom: (
        /*selector*/
        e[45].geometry
      ),
      style: (
        /*style*/
        e[1]
      )
    }
  }), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      const i = {};
      s[0] & /*$store*/
      131072 && (i.annotation = /*annotation*/
      o[42]), s[0] & /*$store*/
      131072 && (i.geom = /*selector*/
      o[45].geometry), s[0] & /*style*/
      2 && (i.style = /*style*/
      o[1]), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function Ar(e) {
  let t, n;
  return t = new mr({
    props: {
      annotation: (
        /*annotation*/
        e[42]
      ),
      geom: (
        /*selector*/
        e[45].geometry
      ),
      style: (
        /*style*/
        e[1]
      )
    }
  }), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      const i = {};
      s[0] & /*$store*/
      131072 && (i.annotation = /*annotation*/
      o[42]), s[0] & /*$store*/
      131072 && (i.geom = /*selector*/
      o[45].geometry), s[0] & /*style*/
      2 && (i.style = /*style*/
      o[1]), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function Sr(e) {
  let t, n;
  return t = new lr({
    props: {
      annotation: (
        /*annotation*/
        e[42]
      ),
      geom: (
        /*selector*/
        e[45].geometry
      ),
      style: (
        /*style*/
        e[1]
      )
    }
  }), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      const i = {};
      s[0] & /*$store*/
      131072 && (i.annotation = /*annotation*/
      o[42]), s[0] & /*$store*/
      131072 && (i.geom = /*selector*/
      o[45].geometry), s[0] & /*style*/
      2 && (i.style = /*style*/
      o[1]), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function Mr(e) {
  let t, n;
  return t = new dr({
    props: {
      annotation: (
        /*annotation*/
        e[42]
      ),
      geom: (
        /*selector*/
        e[45].geometry
      ),
      style: (
        /*style*/
        e[1]
      )
    }
  }), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      const i = {};
      s[0] & /*$store*/
      131072 && (i.annotation = /*annotation*/
      o[42]), s[0] & /*$store*/
      131072 && (i.geom = /*selector*/
      o[45].geometry), s[0] & /*style*/
      2 && (i.style = /*style*/
      o[1]), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function kr(e) {
  let t, n;
  return t = new yr({
    props: {
      annotation: (
        /*annotation*/
        e[42]
      ),
      geom: (
        /*selector*/
        e[45].geometry
      ),
      style: (
        /*style*/
        e[1]
      )
    }
  }), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      const i = {};
      s[0] & /*$store*/
      131072 && (i.annotation = /*annotation*/
      o[42]), s[0] & /*$store*/
      131072 && (i.geom = /*selector*/
      o[45].geometry), s[0] & /*style*/
      2 && (i.style = /*style*/
      o[1]), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function Tr(e) {
  var o;
  let t, n;
  return t = new tr({
    props: {
      annotation: (
        /*annotation*/
        e[42]
      ),
      geom: (
        /*selector*/
        (o = e[45]) == null ? void 0 : o.geometry
      ),
      style: (
        /*style*/
        e[1]
      )
    }
  }), {
    c() {
      ce(t.$$.fragment);
    },
    m(s, i) {
      re(t, s, i), n = true;
    },
    p(s, i) {
      var r;
      const l = {};
      i[0] & /*$store*/
      131072 && (l.annotation = /*annotation*/
      s[42]), i[0] & /*$store*/
      131072 && (l.geom = /*selector*/
      (r = s[45]) == null ? void 0 : r.geometry), i[0] & /*style*/
      2 && (l.style = /*style*/
      s[1]), t.$set(l);
    },
    i(s) {
      n || (V(t.$$.fragment, s), n = true);
    },
    o(s) {
      H(t.$$.fragment, s), n = false;
    },
    d(s) {
      le(t, s);
    }
  };
}
function jn(e) {
  let t, n, o, s;
  const i = [
    Tr,
    kr,
    Mr,
    Sr,
    Ar,
    vr
  ], l = [];
  function r(a2, f) {
    var d, u, h, g, p, y;
    return (
      /*selector*/
      ((d = a2[45]) == null ? void 0 : d.type) === x.ELLIPSE ? 0 : (
        /*selector*/
        ((u = a2[45]) == null ? void 0 : u.type) === x.RECTANGLE ? 1 : (
          /*selector*/
          ((h = a2[45]) == null ? void 0 : h.type) === x.POLYGON ? 2 : (
            /*selector*/
            ((g = a2[45]) == null ? void 0 : g.type) === x.MULTIPOLYGON ? 3 : (
              /*selector*/
              ((p = a2[45]) == null ? void 0 : p.type) === x.POLYLINE ? 4 : (
                /*selector*/
                ((y = a2[45]) == null ? void 0 : y.type) === x.LINE ? 5 : -1
              )
            )
          )
        )
      )
    );
  }
  return ~(t = r(e)) && (n = l[t] = i[t](e)), {
    c() {
      n && n.c(), o = Me();
    },
    m(a2, f) {
      ~t && l[t].m(a2, f), Y(a2, o, f), s = true;
    },
    p(a2, f) {
      let d = t;
      t = r(a2), t === d ? ~t && l[t].p(a2, f) : (n && (be(), H(l[d], 1, 1, () => {
        l[d] = null;
      }), Ee()), ~t ? (n = l[t], n ? n.p(a2, f) : (n = l[t] = i[t](a2), n.c()), V(n, 1), n.m(o.parentNode, o)) : n = null);
    },
    i(a2) {
      s || (V(n), s = true);
    },
    o(a2) {
      H(n), s = false;
    },
    d(a2) {
      a2 && R(o), ~t && l[t].d(a2);
    }
  };
}
function Fn(e) {
  let t = Mt(
    /*annotation*/
    e[42]
  ) && !/*isEditable*/
  e[10](
    /*annotation*/
    e[42]
  ), n, o, s = t && Gn(Ft(e));
  return {
    c() {
      s && s.c(), n = Me();
    },
    m(i, l) {
      s && s.m(i, l), Y(i, n, l), o = true;
    },
    p(i, l) {
      l[0] & /*$store, isEditable*/
      132096 && (t = Mt(
        /*annotation*/
        i[42]
      ) && !/*isEditable*/
      i[10](
        /*annotation*/
        i[42]
      )), t ? s ? (s.p(Ft(i), l), l[0] & /*$store, isEditable*/
      132096 && V(s, 1)) : (s = Gn(Ft(i)), s.c(), V(s, 1), s.m(n.parentNode, n)) : s && (be(), H(s, 1, 1, () => {
        s = null;
      }), Ee());
    },
    i(i) {
      o || (V(s), o = true);
    },
    o(i) {
      H(s), o = false;
    },
    d(i) {
      i && R(n), s && s.d(i);
    }
  };
}
function zn(e) {
  let t, n, o, s;
  const i = [Lr, Pr], l = [];
  function r(a2, f) {
    return (
      /*editors*/
      a2[6] ? 0 : (
        /*tool*/
        a2[15] && /*drawingEnabled*/
        a2[0] ? 1 : -1
      )
    );
  }
  return ~(t = r(e)) && (n = l[t] = i[t](e)), {
    c() {
      n && n.c(), o = Me();
    },
    m(a2, f) {
      ~t && l[t].m(a2, f), Y(a2, o, f), s = true;
    },
    p(a2, f) {
      let d = t;
      t = r(a2), t === d ? ~t && l[t].p(a2, f) : (n && (be(), H(l[d], 1, 1, () => {
        l[d] = null;
      }), Ee()), ~t ? (n = l[t], n ? n.p(a2, f) : (n = l[t] = i[t](a2), n.c()), V(n, 1), n.m(o.parentNode, o)) : n = null);
    },
    i(a2) {
      s || (V(n), s = true);
    },
    o(a2) {
      H(n), s = false;
    },
    d(a2) {
      a2 && R(o), ~t && l[t].d(a2);
    }
  };
}
function Pr(e) {
  let t = `${/*toolName*/
  e[2]}-${/*toolMountKey*/
  e[7]}`, n, o, s = qn(e);
  return {
    c() {
      s.c(), n = Me();
    },
    m(i, l) {
      s.m(i, l), Y(i, n, l), o = true;
    },
    p(i, l) {
      l[0] & /*toolName, toolMountKey*/
      132 && ae(t, t = `${/*toolName*/
      i[2]}-${/*toolMountKey*/
      i[7]}`) ? (be(), H(s, 1, 1, $), Ee(), s = qn(i), s.c(), V(s, 1), s.m(n.parentNode, n)) : s.p(i, l);
    },
    i(i) {
      o || (V(s), o = true);
    },
    o(i) {
      H(s), o = false;
    },
    d(i) {
      i && R(n), s.d(i);
    }
  };
}
function Lr(e) {
  let t, n, o = ve(
    /*editors*/
    e[6]
  ), s = [];
  for (let l = 0; l < o.length; l += 1)
    s[l] = Wn(Xn(e, o, l));
  const i = (l) => H(s[l], 1, 1, () => {
    s[l] = null;
  });
  return {
    c() {
      for (let l = 0; l < s.length; l += 1)
        s[l].c();
      t = Me();
    },
    m(l, r) {
      for (let a2 = 0; a2 < s.length; a2 += 1)
        s[a2] && s[a2].m(l, r);
      Y(l, t, r), n = true;
    },
    p(l, r) {
      if (r[0] & /*editors, drawingEl, style, transform, $scale, onChangeSelected*/
      8659266) {
        o = ve(
          /*editors*/
          l[6]
        );
        let a2;
        for (a2 = 0; a2 < o.length; a2 += 1) {
          const f = Xn(l, o, a2);
          s[a2] ? (s[a2].p(f, r), V(s[a2], 1)) : (s[a2] = Wn(f), s[a2].c(), V(s[a2], 1), s[a2].m(t.parentNode, t));
        }
        for (be(), a2 = o.length; a2 < s.length; a2 += 1)
          i(a2);
        Ee();
      }
    },
    i(l) {
      if (!n) {
        for (let r = 0; r < o.length; r += 1)
          V(s[r]);
        n = true;
      }
    },
    o(l) {
      s = s.filter(Boolean);
      for (let r = 0; r < s.length; r += 1)
        H(s[r]);
      n = false;
    },
    d(l) {
      l && R(t), Ge(s, l);
    }
  };
}
function qn(e) {
  let t, n;
  return t = new ji({
    props: {
      target: (
        /*drawingEl*/
        e[8]
      ),
      tool: (
        /*tool*/
        e[15]
      ),
      drawingMode: (
        /*drawingMode*/
        e[14]
      ),
      transform: (
        /*transform*/
        e[13]
      ),
      viewportScale: (
        /*$scale*/
        e[18]
      )
    }
  }), t.$on(
    "create",
    /*onSelectionCreated*/
    e[22]
  ), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      const i = {};
      s[0] & /*drawingEl*/
      256 && (i.target = /*drawingEl*/
      o[8]), s[0] & /*tool*/
      32768 && (i.tool = /*tool*/
      o[15]), s[0] & /*drawingMode*/
      16384 && (i.drawingMode = /*drawingMode*/
      o[14]), s[0] & /*transform*/
      8192 && (i.transform = /*transform*/
      o[13]), s[0] & /*$scale*/
      262144 && (i.viewportScale = /*$scale*/
      o[18]), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function Kn(e) {
  let t, n;
  return t = new Hi({
    props: {
      target: (
        /*drawingEl*/
        e[8]
      ),
      editor: (
        /*editable*/
        e[39].editor
      ),
      annotation: (
        /*editable*/
        e[39].annotation
      ),
      style: (
        /*style*/
        e[1]
      ),
      transform: (
        /*transform*/
        e[13]
      ),
      viewportScale: (
        /*$scale*/
        e[18]
      )
    }
  }), t.$on("change", function() {
    se(
      /*onChangeSelected*/
      e[23](
        /*editable*/
        e[39].annotation
      )
    ) && e[23](
      /*editable*/
      e[39].annotation
    ).apply(this, arguments);
  }), {
    c() {
      ce(t.$$.fragment);
    },
    m(o, s) {
      re(t, o, s), n = true;
    },
    p(o, s) {
      e = o;
      const i = {};
      s[0] & /*drawingEl*/
      256 && (i.target = /*drawingEl*/
      e[8]), s[0] & /*editors*/
      64 && (i.editor = /*editable*/
      e[39].editor), s[0] & /*editors*/
      64 && (i.annotation = /*editable*/
      e[39].annotation), s[0] & /*style*/
      2 && (i.style = /*style*/
      e[1]), s[0] & /*transform*/
      8192 && (i.transform = /*transform*/
      e[13]), s[0] & /*$scale*/
      262144 && (i.viewportScale = /*$scale*/
      e[18]), t.$set(i);
    },
    i(o) {
      n || (V(t.$$.fragment, o), n = true);
    },
    o(o) {
      H(t.$$.fragment, o), n = false;
    },
    d(o) {
      le(t, o);
    }
  };
}
function Wn(e) {
  let t = (
    /*editable*/
    e[39].annotation.id
  ), n, o, s = Kn(e);
  return {
    c() {
      s.c(), n = Me();
    },
    m(i, l) {
      s.m(i, l), Y(i, n, l), o = true;
    },
    p(i, l) {
      l[0] & /*editors*/
      64 && ae(t, t = /*editable*/
      i[39].annotation.id) ? (be(), H(s, 1, 1, $), Ee(), s = Kn(i), s.c(), V(s, 1), s.m(n.parentNode, n)) : s.p(i, l);
    },
    i(i) {
      o || (V(s), o = true);
    },
    o(i) {
      H(s), o = false;
    },
    d(i) {
      i && R(n), s.d(i);
    }
  };
}
function Cr(e) {
  let t, n, o, s, i, l, r = ve(
    /*$store*/
    e[17].filter(
      /*func*/
      e[34]
    )
  ), a2 = [];
  for (let u = 0; u < r.length; u += 1)
    a2[u] = Fn(Hn(e, r, u));
  const f = (u) => H(a2[u], 1, 1, () => {
    a2[u] = null;
  });
  let d = (
    /*drawingEl*/
    e[8] && zn(e)
  );
  return {
    c() {
      t = C("svg"), n = C("g");
      for (let u = 0; u < a2.length; u += 1)
        a2[u].c();
      o = C("g"), d && d.c(), c(o, "class", "drawing"), c(t, "role", "application"), c(t, "tabindex", 0), c(t, "class", "a9s-annotationlayer"), Te(
        t,
        "drawing",
        /*tool*/
        e[15]
      ), Te(
        t,
        "editing",
        /*editableAnnotations*/
        e[5]
      ), Te(t, "hidden", !/*visible*/
      e[3]), Te(
        t,
        "hover",
        /*$hover*/
        e[16]
      );
    },
    m(u, h) {
      Y(u, t, h), G(t, n);
      for (let g = 0; g < a2.length; g += 1)
        a2[g] && a2[g].m(n, null);
      G(t, o), d && d.m(o, null), e[35](o), e[36](t), s = true, i || (l = [
        Q(t, "pointerup", function() {
          se(
            /*onPointerUp*/
            e[11]
          ) && e[11].apply(this, arguments);
        }),
        Q(t, "pointerdown", function() {
          se(
            /*onPointerDown*/
            e[12]
          ) && e[12].apply(this, arguments);
        }),
        Q(
          t,
          "pointermove",
          /*onPointerMove*/
          e[24]
        )
      ], i = true);
    },
    p(u, h) {
      if (e = u, h[0] & /*$store, style, isEditable*/
      132098) {
        r = ve(
          /*$store*/
          e[17].filter(
            /*func*/
            e[34]
          )
        );
        let g;
        for (g = 0; g < r.length; g += 1) {
          const p = Hn(e, r, g);
          a2[g] ? (a2[g].p(p, h), V(a2[g], 1)) : (a2[g] = Fn(p), a2[g].c(), V(a2[g], 1), a2[g].m(n, null));
        }
        for (be(), g = r.length; g < a2.length; g += 1)
          f(g);
        Ee();
      }
      e[8] ? d ? (d.p(e, h), h[0] & /*drawingEl*/
      256 && V(d, 1)) : (d = zn(e), d.c(), V(d, 1), d.m(o, null)) : d && (be(), H(d, 1, 1, () => {
        d = null;
      }), Ee()), (!s || h[0] & /*tool*/
      32768) && Te(
        t,
        "drawing",
        /*tool*/
        e[15]
      ), (!s || h[0] & /*editableAnnotations*/
      32) && Te(
        t,
        "editing",
        /*editableAnnotations*/
        e[5]
      ), (!s || h[0] & /*visible*/
      8) && Te(t, "hidden", !/*visible*/
      e[3]), (!s || h[0] & /*$hover*/
      65536) && Te(
        t,
        "hover",
        /*$hover*/
        e[16]
      );
    },
    i(u) {
      if (!s) {
        for (let h = 0; h < r.length; h += 1)
          V(a2[h]);
        V(d), s = true;
      }
    },
    o(u) {
      a2 = a2.filter(Boolean);
      for (let h = 0; h < a2.length; h += 1)
        H(a2[h]);
      H(d), s = false;
    },
    d(u) {
      u && R(t), Ge(a2, u), d && d.d(), e[35](null), e[36](null), i = false, Se(l);
    }
  };
}
function Ir(e, t, n) {
  let o, s, i, l, r, a2, f, d, u, h, g, p, y = $, S = () => (y(), y = $n(b, (X) => n(18, p = X)), b);
  e.$$.on_destroy.push(() => y());
  let { drawingEnabled: w } = t, { image: A } = t, { preferredDrawingMode: _ } = t, { state: M } = t, { style: P = void 0 } = t, { toolName: I = Ao()[0] } = t, { user: T } = t, { visible: B2 = true } = t, N = 0;
  const Z = () => n(7, N += 1), j = () => I, v = () => w;
  let E, m, b;
  Be(() => S(n(9, b = oi(A, m))));
  const { hover: L, selection: k, store: O } = M;
  Nt(e, L, (X) => n(16, u = X)), Nt(e, k, (X) => n(33, h = X)), Nt(e, O, (X) => n(17, g = X));
  let F2, U;
  const J2 = (X) => {
    F2 && O.unobserve(F2);
    const fe = X.filter(({ editable: te }) => te).map(({ id: te }) => te);
    fe.length > 0 ? (n(5, U = fe.map((te) => O.getAnnotation(te)).filter((te) => te && Mt(te))), F2 = (te) => {
      const { updated: ge } = te.changes;
      n(5, U = ge == null ? void 0 : ge.map((ie) => ie.newValue));
    }, O.observe(F2, { annotations: fe })) : n(5, U = void 0);
  }, q = (X) => {
    const fe = mo(), te = {
      id: fe,
      bodies: [],
      target: {
        annotation: fe,
        selector: X.detail,
        creator: T,
        created: /* @__PURE__ */ new Date()
      }
    };
    O.addAnnotation(te), k.setSelected(te.id);
  }, K = (X) => (fe) => {
    var Ue2;
    const { target: te } = X, ge = 10 * 60 * 1e3, ie = ((Ue2 = te.creator) == null ? void 0 : Ue2.id) !== T.id || !te.created || (/* @__PURE__ */ new Date()).getTime() - te.created.getTime() > ge;
    O.updateTarget({
      ...te,
      selector: fe.detail,
      created: ie ? te.created : /* @__PURE__ */ new Date(),
      updated: ie ? /* @__PURE__ */ new Date() : void 0,
      updatedBy: ie ? T : void 0
    });
  }, ee = (X) => {
    const { x: fe, y: te } = Mo(X, m), ge = O.getAt(fe, te, void 0, 2);
    ge ? u !== ge.id && L.set(ge.id) : L.set(void 0);
  }, he2 = (X) => Mt(X);
  function ne2(X) {
    vt[X ? "unshift" : "push"](() => {
      E = X, n(8, E);
    });
  }
  function Ae(X) {
    vt[X ? "unshift" : "push"](() => {
      m = X, n(4, m);
    });
  }
  return e.$$set = (X) => {
    "drawingEnabled" in X && n(0, w = X.drawingEnabled), "image" in X && n(25, A = X.image), "preferredDrawingMode" in X && n(26, _ = X.preferredDrawingMode), "state" in X && n(27, M = X.state), "style" in X && n(1, P = X.style), "toolName" in X && n(2, I = X.toolName), "user" in X && n(28, T = X.user), "visible" in X && n(3, B2 = X.visible);
  }, e.$$.update = () => {
    e.$$.dirty[0] & /*toolName*/
    4 && n(15, { tool: o, opts: s } = So(I) || { tool: void 0, opts: void 0 }, o, (n(32, s), n(2, I))), e.$$.dirty[0] & /*preferredDrawingMode*/
    67108864 | e.$$.dirty[1] & /*opts*/
    2 && n(14, i = (s == null ? void 0 : s.drawingMode) || _), e.$$.dirty[0] & /*svgEl*/
    16 && n(13, l = wr(m)), e.$$.dirty[0] & /*svgEl*/
    16 && n(12, { onPointerDown: r, onPointerUp: a2 } = Er(m, O), r, (n(11, a2), n(4, m))), e.$$.dirty[1] & /*$selection*/
    4 && J2(h.selected), e.$$.dirty[0] & /*editableAnnotations*/
    32 && n(6, f = U ? U.map((X) => ({
      annotation: X,
      editor: Vi(X.target.selector)
    })).filter((X) => X.editor) : void 0), e.$$.dirty[0] & /*editors*/
    64 && n(10, d = (X) => f && f.some((fe) => fe.annotation.id === X.id));
  }, [
    w,
    P,
    I,
    B2,
    m,
    U,
    f,
    N,
    E,
    b,
    d,
    a2,
    r,
    l,
    i,
    o,
    u,
    g,
    p,
    L,
    k,
    O,
    q,
    K,
    ee,
    A,
    _,
    M,
    T,
    Z,
    j,
    v,
    s,
    h,
    he2,
    ne2,
    Ae
  ];
}
var Or = class extends ye {
  constructor(t) {
    super(), _e(
      this,
      t,
      Ir,
      Cr,
      ae,
      {
        drawingEnabled: 0,
        image: 25,
        preferredDrawingMode: 26,
        state: 27,
        style: 1,
        toolName: 2,
        user: 28,
        visible: 3,
        cancelDrawing: 29,
        getDrawingTool: 30,
        isDrawingEnabled: 31
      },
      null,
      [-1, -1]
    );
  }
  get cancelDrawing() {
    return this.$$.ctx[29];
  }
  get getDrawingTool() {
    return this.$$.ctx[30];
  }
  get isDrawingEnabled() {
    return this.$$.ctx[31];
  }
};
function ko(e, t, n = 0, o = e.length - 1, s = Nr) {
  for (; o > n; ) {
    if (o - n > 600) {
      const a2 = o - n + 1, f = t - n + 1, d = Math.log(a2), u = 0.5 * Math.exp(2 * d / 3), h = 0.5 * Math.sqrt(d * u * (a2 - u) / a2) * (f - a2 / 2 < 0 ? -1 : 1), g = Math.max(n, Math.floor(t - f * u / a2 + h)), p = Math.min(o, Math.floor(t + (a2 - f) * u / a2 + h));
      ko(e, t, g, p, s);
    }
    const i = e[t];
    let l = n, r = o;
    for (xe(e, n, t), s(e[o], i) > 0 && xe(e, n, o); l < r; ) {
      for (xe(e, l, r), l++, r--; s(e[l], i) < 0; )
        l++;
      for (; s(e[r], i) > 0; )
        r--;
    }
    s(e[n], i) === 0 ? xe(e, n, r) : (r++, xe(e, r, o)), r <= t && (n = r + 1), t <= r && (o = r - 1);
  }
}
function xe(e, t, n) {
  const o = e[t];
  e[t] = e[n], e[n] = o;
}
function Nr(e, t) {
  return e < t ? -1 : e > t ? 1 : 0;
}
var Dr = class {
  constructor(t = 9) {
    this._maxEntries = Math.max(4, t), this._minEntries = Math.max(2, Math.ceil(this._maxEntries * 0.4)), this.clear();
  }
  all() {
    return this._all(this.data, []);
  }
  search(t) {
    let n = this.data;
    const o = [];
    if (!_t(t, n))
      return o;
    const s = this.toBBox, i = [];
    for (; n; ) {
      for (let l = 0; l < n.children.length; l++) {
        const r = n.children[l], a2 = n.leaf ? s(r) : r;
        _t(t, a2) && (n.leaf ? o.push(r) : qt(t, a2) ? this._all(r, o) : i.push(r));
      }
      n = i.pop();
    }
    return o;
  }
  collides(t) {
    let n = this.data;
    if (!_t(t, n))
      return false;
    const o = [];
    for (; n; ) {
      for (let s = 0; s < n.children.length; s++) {
        const i = n.children[s], l = n.leaf ? this.toBBox(i) : i;
        if (_t(t, l)) {
          if (n.leaf || qt(t, l))
            return true;
          o.push(i);
        }
      }
      n = o.pop();
    }
    return false;
  }
  load(t) {
    if (!(t && t.length))
      return this;
    if (t.length < this._minEntries) {
      for (let o = 0; o < t.length; o++)
        this.insert(t[o]);
      return this;
    }
    let n = this._build(t.slice(), 0, t.length - 1, 0);
    if (!this.data.children.length)
      this.data = n;
    else if (this.data.height === n.height)
      this._splitRoot(this.data, n);
    else {
      if (this.data.height < n.height) {
        const o = this.data;
        this.data = n, n = o;
      }
      this._insert(n, this.data.height - n.height - 1, true);
    }
    return this;
  }
  insert(t) {
    return t && this._insert(t, this.data.height - 1), this;
  }
  clear() {
    return this.data = We([]), this;
  }
  remove(t, n) {
    if (!t)
      return this;
    let o = this.data;
    const s = this.toBBox(t), i = [], l = [];
    let r, a2, f;
    for (; o || i.length; ) {
      if (o || (o = i.pop(), a2 = i[i.length - 1], r = l.pop(), f = true), o.leaf) {
        const d = Rr(t, o.children, n);
        if (d !== -1)
          return o.children.splice(d, 1), i.push(o), this._condense(i), this;
      }
      !f && !o.leaf && qt(o, s) ? (i.push(o), l.push(r), r = 0, a2 = o, o = o.children[0]) : a2 ? (r++, o = a2.children[r], f = false) : o = null;
    }
    return this;
  }
  toBBox(t) {
    return t;
  }
  compareMinX(t, n) {
    return t.minX - n.minX;
  }
  compareMinY(t, n) {
    return t.minY - n.minY;
  }
  toJSON() {
    return this.data;
  }
  fromJSON(t) {
    return this.data = t, this;
  }
  _all(t, n) {
    const o = [];
    for (; t; )
      t.leaf ? n.push(...t.children) : o.push(...t.children), t = o.pop();
    return n;
  }
  _build(t, n, o, s) {
    const i = o - n + 1;
    let l = this._maxEntries, r;
    if (i <= l)
      return r = We(t.slice(n, o + 1)), qe(r, this.toBBox), r;
    s || (s = Math.ceil(Math.log(i) / Math.log(l)), l = Math.ceil(i / Math.pow(l, s - 1))), r = We([]), r.leaf = false, r.height = s;
    const a2 = Math.ceil(i / l), f = a2 * Math.ceil(Math.sqrt(l));
    Zn(t, n, o, f, this.compareMinX);
    for (let d = n; d <= o; d += f) {
      const u = Math.min(d + f - 1, o);
      Zn(t, d, u, a2, this.compareMinY);
      for (let h = d; h <= u; h += a2) {
        const g = Math.min(h + a2 - 1, u);
        r.children.push(this._build(t, h, g, s - 1));
      }
    }
    return qe(r, this.toBBox), r;
  }
  _chooseSubtree(t, n, o, s) {
    for (; s.push(n), !(n.leaf || s.length - 1 === o); ) {
      let i = 1 / 0, l = 1 / 0, r;
      for (let a2 = 0; a2 < n.children.length; a2++) {
        const f = n.children[a2], d = zt(f), u = Vr(t, f) - d;
        u < l ? (l = u, i = d < i ? d : i, r = f) : u === l && d < i && (i = d, r = f);
      }
      n = r || n.children[0];
    }
    return n;
  }
  _insert(t, n, o) {
    const s = o ? t : this.toBBox(t), i = [], l = this._chooseSubtree(s, this.data, n, i);
    for (l.children.push(t), et(l, s); n >= 0 && i[n].children.length > this._maxEntries; )
      this._split(i, n), n--;
    this._adjustParentBBoxes(s, i, n);
  }
  // split overflowed node into two
  _split(t, n) {
    const o = t[n], s = o.children.length, i = this._minEntries;
    this._chooseSplitAxis(o, i, s);
    const l = this._chooseSplitIndex(o, i, s), r = We(o.children.splice(l, o.children.length - l));
    r.height = o.height, r.leaf = o.leaf, qe(o, this.toBBox), qe(r, this.toBBox), n ? t[n - 1].children.push(r) : this._splitRoot(o, r);
  }
  _splitRoot(t, n) {
    this.data = We([t, n]), this.data.height = t.height + 1, this.data.leaf = false, qe(this.data, this.toBBox);
  }
  _chooseSplitIndex(t, n, o) {
    let s, i = 1 / 0, l = 1 / 0;
    for (let r = n; r <= o - n; r++) {
      const a2 = $e(t, 0, r, this.toBBox), f = $e(t, r, o, this.toBBox), d = Ur(a2, f), u = zt(a2) + zt(f);
      d < i ? (i = d, s = r, l = u < l ? u : l) : d === i && u < l && (l = u, s = r);
    }
    return s || o - n;
  }
  // sorts node children by the best axis for split
  _chooseSplitAxis(t, n, o) {
    const s = t.leaf ? this.compareMinX : Yr, i = t.leaf ? this.compareMinY : Br, l = this._allDistMargin(t, n, o, s), r = this._allDistMargin(t, n, o, i);
    l < r && t.children.sort(s);
  }
  // total margin of all possible split distributions where each node is at least m full
  _allDistMargin(t, n, o, s) {
    t.children.sort(s);
    const i = this.toBBox, l = $e(t, 0, n, i), r = $e(t, o - n, o, i);
    let a2 = pt(l) + pt(r);
    for (let f = n; f < o - n; f++) {
      const d = t.children[f];
      et(l, t.leaf ? i(d) : d), a2 += pt(l);
    }
    for (let f = o - n - 1; f >= n; f--) {
      const d = t.children[f];
      et(r, t.leaf ? i(d) : d), a2 += pt(r);
    }
    return a2;
  }
  _adjustParentBBoxes(t, n, o) {
    for (let s = o; s >= 0; s--)
      et(n[s], t);
  }
  _condense(t) {
    for (let n = t.length - 1, o; n >= 0; n--)
      t[n].children.length === 0 ? n > 0 ? (o = t[n - 1].children, o.splice(o.indexOf(t[n]), 1)) : this.clear() : qe(t[n], this.toBBox);
  }
};
function Rr(e, t, n) {
  if (!n)
    return t.indexOf(e);
  for (let o = 0; o < t.length; o++)
    if (n(e, t[o]))
      return o;
  return -1;
}
function qe(e, t) {
  $e(e, 0, e.children.length, t, e);
}
function $e(e, t, n, o, s) {
  s || (s = We(null)), s.minX = 1 / 0, s.minY = 1 / 0, s.maxX = -1 / 0, s.maxY = -1 / 0;
  for (let i = t; i < n; i++) {
    const l = e.children[i];
    et(s, e.leaf ? o(l) : l);
  }
  return s;
}
function et(e, t) {
  return e.minX = Math.min(e.minX, t.minX), e.minY = Math.min(e.minY, t.minY), e.maxX = Math.max(e.maxX, t.maxX), e.maxY = Math.max(e.maxY, t.maxY), e;
}
function Yr(e, t) {
  return e.minX - t.minX;
}
function Br(e, t) {
  return e.minY - t.minY;
}
function zt(e) {
  return (e.maxX - e.minX) * (e.maxY - e.minY);
}
function pt(e) {
  return e.maxX - e.minX + (e.maxY - e.minY);
}
function Vr(e, t) {
  return (Math.max(t.maxX, e.maxX) - Math.min(t.minX, e.minX)) * (Math.max(t.maxY, e.maxY) - Math.min(t.minY, e.minY));
}
function Ur(e, t) {
  const n = Math.max(e.minX, t.minX), o = Math.max(e.minY, t.minY), s = Math.min(e.maxX, t.maxX), i = Math.min(e.maxY, t.maxY);
  return Math.max(0, s - n) * Math.max(0, i - o);
}
function qt(e, t) {
  return e.minX <= t.minX && e.minY <= t.minY && t.maxX <= e.maxX && t.maxY <= e.maxY;
}
function _t(e, t) {
  return t.minX <= e.maxX && t.minY <= e.maxY && t.maxX >= e.minX && t.maxY >= e.minY;
}
function We(e) {
  return {
    children: e,
    height: 1,
    leaf: true,
    minX: 1 / 0,
    minY: 1 / 0,
    maxX: -1 / 0,
    maxY: -1 / 0
  };
}
function Zn(e, t, n, o, s) {
  const i = [t, n];
  for (; i.length; ) {
    if (n = i.pop(), t = i.pop(), n - t <= o)
      continue;
    const l = t + Math.ceil((n - t) / o / 2) * o;
    ko(e, l, t, n, s), i.push(t, l, l, n);
  }
}
var Xr = () => {
  const e = new Dr(), t = /* @__PURE__ */ new Map(), n = () => [...t.values()], o = () => {
    e.clear(), t.clear();
  }, s = (u) => {
    if (!Et(u))
      return;
    const { minX: h, minY: g, maxX: p, maxY: y } = u.selector.geometry.bounds, S = { minX: h, minY: g, maxX: p, maxY: y, target: u };
    e.insert(S), t.set(u.annotation, S);
  }, i = (u) => {
    if (!Et(u))
      return;
    const h = t.get(u.annotation);
    h && e.remove(h), t.delete(u.annotation);
  };
  return {
    all: n,
    clear: o,
    getAt: (u, h, g = 0) => {
      const y = e.search({
        minX: u - g,
        minY: h - g,
        maxX: u + g,
        maxY: h + g
      }).map((S) => S.target).filter(({ selector: S }) => jo(S, u, h, g));
      return y.length > 0 ? (y.sort((S, w) => Jt(S.selector) - Jt(w.selector)), y) : [];
    },
    getIntersecting: (u, h, g, p) => e.search({
      minX: u,
      minY: h,
      maxX: u + g,
      maxY: h + p
    }).map((y) => y.target),
    insert: s,
    remove: i,
    set: (u, h = true) => {
      h && o();
      const g = u.reduce((p, y) => {
        if (Et(y)) {
          const { minX: S, minY: w, maxX: A, maxY: _ } = y.selector.geometry.bounds;
          return [...p, { minX: S, minY: w, maxX: A, maxY: _, target: y }];
        } else
          return p;
      }, []);
      g.forEach((p) => t.set(p.target.annotation, p)), e.load(g);
    },
    size: () => e.all().length,
    update: (u, h) => {
      i(u), s(h);
    }
  };
};
var Hr = (e) => ({
  ...e,
  subscribe: (n) => {
    const o = (s) => n(s.state);
    return e.observe(o), n(e.all()), () => e.unobserve(o);
  }
});
var Gr = (e) => {
  const t = Rs(), n = Xr(), o = vs(t, e.userSelectAction, e.adapter), s = Es(t), i = Us();
  return t.observe(({ changes: a2 }) => {
    n.set((a2.created || []).map((f) => f.target), false), (a2.deleted || []).forEach((f) => n.remove(f.target)), (a2.updated || []).forEach(({ oldValue: f, newValue: d }) => n.update(f.target, d.target));
  }), {
    store: {
      ...t,
      getAt: (a2, f, d, u) => {
        const h = n.getAt(a2, f, u);
        if (d)
          return h.map((p) => t.getAnnotation(p.annotation)).filter(Boolean).filter(d)[0];
        {
          const g = h[0];
          return g ? t.getAnnotation(g.annotation) : void 0;
        }
      },
      getIntersecting: (a2, f, d, u) => n.getIntersecting(a2, f, d, u).map((h) => t.getAnnotation(h.annotation)).filter(Boolean)
    },
    selection: o,
    hover: s,
    viewport: i
  };
};
var jr = (e) => {
  const t = Gr(e);
  return {
    ...t,
    store: Hr(t.store)
  };
};
var Fr = (e) => {
  let t, n;
  if (e.nodeName === "CANVAS")
    t = e, n = t.getContext("2d", { willReadFrequently: true });
  else {
    const s = e;
    t = document.createElement("canvas"), t.width = s.width, t.height = s.height, n = t.getContext("2d", { willReadFrequently: true }), n.drawImage(s, 0, 0, s.width, s.height);
  }
  let o = 0;
  for (let s = 1; s < 10; s++)
    for (let i = 1; i < 10; i++) {
      const l = Math.round(i * t.width / 10), r = Math.round(s * t.height / 10), a2 = n.getImageData(l, r, 1, 1).data, f = (0.299 * a2[0] + 0.587 * a2[1] + 0.114 * a2[2]) / 255;
      o += f;
    }
  return o / 81;
};
var zr = (e) => {
  const t = Fr(e), n = t > 0.6 ? "dark" : "light";
  return console.log(`[Annotorious] Image brightness: ${t.toFixed(1)}. Setting ${n} theme.`), n;
};
var Jn = (e, t, n) => t.setAttribute("data-theme", n === "auto" ? zr(e) : n);
var qr = (e, t) => ({
  ...e,
  drawingEnabled: e.drawingEnabled === void 0 ? t.drawingEnabled : e.drawingEnabled,
  drawingMode: e.drawingMode || t.drawingMode,
  userSelectAction: e.userSelectAction || t.userSelectAction,
  theme: e.theme || t.theme
});
var Qn = typeof navigator > "u" ? false : navigator.userAgent.indexOf("Mac OS X") !== -1;
var Kr = (e, t) => {
  const n = t || document, o = (l) => {
    const r = l;
    r.key === "z" && r.ctrlKey ? e.undo() : r.key === "y" && r.ctrlKey && e.redo();
  }, s = (l) => {
    const r = l;
    r.key === "z" && r.metaKey && (r.shiftKey ? e.redo() : e.undo());
  }, i = () => {
    Qn ? n.removeEventListener("keydown", s) : n.removeEventListener("keydown", o);
  };
  return Qn ? n.addEventListener("keydown", s) : n.addEventListener("keydown", o), {
    destroy: i
  };
};
var ol = (e, t = {}) => {
  if (!e)
    throw "Missing argument: image";
  const n = typeof e == "string" ? document.getElementById(e) : e, o = qr(t, {
    drawingEnabled: true,
    drawingMode: "drag",
    userSelectAction: po.EDIT,
    theme: "light"
  }), s = jr(o), { selection: i, store: l } = s, r = Vs(l, o.initialHistory), a2 = Xs(
    s,
    r,
    o.adapter,
    o.autoSave
  ), f = document.createElement("DIV");
  f.style.position = "relative", f.style.display = "inline-block", n.style.display = "block", n.parentNode.insertBefore(f, n), f.appendChild(n);
  const d = Kr(r);
  let u = Ws();
  Jn(n, f, o.theme);
  const h = new Or({
    target: f,
    props: {
      drawingEnabled: !!o.drawingEnabled,
      image: n,
      preferredDrawingMode: o.drawingMode,
      state: s,
      style: o.style,
      user: u
    }
  });
  h.$on("click", (E) => {
    const { originalEvent: m, annotation: b } = E.detail;
    b ? i.userSelect(b.id, m) : i.isEmpty() || i.clear();
  });
  const g = Gs(s, r, o.adapter), p = () => h.cancelDrawing(), y = () => {
    h.$destroy(), f.parentNode.insertBefore(n, f), f.parentNode.removeChild(f), d.destroy(), r.destroy();
  }, S = () => h.getDrawingTool(), w = () => u, A = () => h.isDrawingEnabled(), _ = (E, m, b) => xi(E, m, b), M = (E, m) => Ui(E, m), P = (E) => {
    if (!So(E))
      throw `No drawing tool named ${E}`;
    h.$set({ toolName: E });
  }, I = (E) => h.$set({ drawingEnabled: E }), T = (E) => h.$set({ preferredDrawingMode: E }), B2 = (E) => {
    console.warn("Filter not implemented yet");
  }, N = (E) => h.$set({ style: E }), Z = (E) => Jn(n, f, E), j = (E) => {
    u = E, h.$set({ user: E });
  }, v = (E) => (
    // @ts-ignore
    h.$set({ visible: E })
  );
  return {
    ...g,
    cancelDrawing: p,
    destroy: y,
    getDrawingTool: S,
    getUser: w,
    isDrawingEnabled: A,
    listDrawingTools: Ao,
    on: a2.on,
    off: a2.off,
    registerDrawingTool: _,
    registerShapeEditor: M,
    setDrawingEnabled: I,
    setDrawingMode: T,
    setDrawingTool: P,
    setFilter: B2,
    setStyle: N,
    setTheme: Z,
    setUser: j,
    setVisible: v,
    element: f,
    state: s
  };
};

// ../../../../../node_modules/@annotorious/plugin-tools/dist/annotorious-plugin-tools.es.js
var ss2 = Object.defineProperty;
var ls2 = (e, n, t) => n in e ? ss2(e, n, { enumerable: true, configurable: true, writable: true, value: t }) : e[n] = t;
var Kn2 = (e, n, t) => ls2(e, typeof n != "symbol" ? n + "" : n, t);
var ys2 = "4";
typeof window < "u" && (window.__svelte || (window.__svelte = { v: /* @__PURE__ */ new Set() })).v.add(ys2);
var ke2 = /* @__PURE__ */ ((e) => (e.ELLIPSE = "ELLIPSE", e.MULTIPOLYGON = "MULTIPOLYGON", e.POLYGON = "POLYGON", e.POLYLINE = "POLYLINE", e.RECTANGLE = "RECTANGLE", e.LINE = "LINE", e))(ke2 || {});
var ws2 = { exports: {} };
(function(e) {
  (function() {
    function n(i, c2) {
      var f = i.x - c2.x, p = i.y - c2.y;
      return f * f + p * p;
    }
    function t(i, c2, f) {
      var p = c2.x, u = c2.y, d = f.x - p, g = f.y - u;
      if (d !== 0 || g !== 0) {
        var y = ((i.x - p) * d + (i.y - u) * g) / (d * d + g * g);
        y > 1 ? (p = f.x, u = f.y) : y > 0 && (p += d * y, u += g * y);
      }
      return d = i.x - p, g = i.y - u, d * d + g * g;
    }
    function o(i, c2) {
      for (var f = i[0], p = [f], u, d = 1, g = i.length; d < g; d++)
        u = i[d], n(u, f) > c2 && (p.push(u), f = u);
      return f !== u && p.push(u), p;
    }
    function s(i, c2, f, p, u) {
      for (var d = p, g, y = c2 + 1; y < f; y++) {
        var h = t(i[y], i[c2], i[f]);
        h > d && (g = y, d = h);
      }
      d > p && (g - c2 > 1 && s(i, c2, g, p, u), u.push(i[g]), f - g > 1 && s(i, g, f, p, u));
    }
    function l(i, c2) {
      var f = i.length - 1, p = [i[0]];
      return s(i, 0, f, c2, p), p.push(i[f]), p;
    }
    function r(i, c2, f) {
      if (i.length <= 2)
        return i;
      var p = c2 !== void 0 ? c2 * c2 : 1;
      return i = f ? i : o(i, p), i = l(i, p), i;
    }
    e.exports = r, e.exports.default = r;
  })();
})(ws2);
var En2 = (e, n) => n;
var Se2 = (e) => {
  let n = 1 / 0, t = 1 / 0, o = -1 / 0, s = -1 / 0;
  return e.forEach(([l, r]) => {
    n = Math.min(n, l), t = Math.min(t, r), o = Math.max(o, l), s = Math.max(s, r);
  }), { minX: n, minY: t, maxX: o, maxY: s };
};
var jn2 = (e) => {
  let n = 0, t = e.length - 1;
  for (let o = 0; o < e.length; o++)
    n += (e[t][0] + e[o][0]) * (e[t][1] - e[o][1]), t = o;
  return Math.abs(0.5 * n);
};
var qn2 = (e, n, t) => {
  let o = false;
  for (let s = 0, l = e.length - 1; s < e.length; l = s++) {
    const r = e[s][0], i = e[s][1], c2 = e[l][0], f = e[l][1];
    i > t != f > t && n < (c2 - r) * (t - i) / (f - i) + r && (o = !o);
  }
  return o;
};
var dn2 = (e, n) => {
  const t = Math.abs(n[0] - e[0]), o = Math.abs(n[1] - e[1]);
  return Math.sqrt(Math.pow(t, 2) + Math.pow(o, 2));
};
var Es2 = {
  area: (e) => Math.PI * e.geometry.rx * e.geometry.ry,
  intersects: (e, n, t) => {
    const { cx: o, cy: s, rx: l, ry: r } = e.geometry, i = 0, c2 = Math.cos(i), f = Math.sin(i), p = n - o, u = t - s, d = c2 * p + f * u, g = f * p - c2 * u;
    return d * d / (l * l) + g * g / (r * r) <= 1;
  }
};
En2(ke2.ELLIPSE, Es2);
var bs2 = {
  area: (e) => 0,
  intersects: (e, n, t, o = 2) => {
    const [[s, l], [r, i]] = e.geometry.points, c2 = Math.abs((i - l) * n - (r - s) * t + r * l - i * s), f = dn2([s, l], [r, i]);
    return c2 / f <= o;
  }
};
En2(ke2.LINE, bs2);
var ks2 = {
  area: (e) => {
    const { polygons: n } = e.geometry;
    return n.reduce((t, o) => {
      const [s, ...l] = o.rings, r = jn2(s.points), i = l.reduce((c2, f) => c2 + jn2(f.points), 0);
      return t + r - i;
    }, 0);
  },
  intersects: (e, n, t) => {
    const { polygons: o } = e.geometry;
    for (const s of o) {
      const [l, ...r] = s.rings;
      if (qn2(l.points, n, t)) {
        let i = false;
        for (const c2 of r)
          if (qn2(c2.points, n, t)) {
            i = true;
            break;
          }
        if (!i)
          return true;
      }
    }
    return false;
  }
};
En2(ke2.MULTIPOLYGON, ks2);
var Ss2 = {
  area: (e) => {
    const n = e.geometry.points;
    return jn2(n);
  },
  intersects: (e, n, t) => {
    const o = e.geometry.points;
    return qn2(o, n, t);
  }
};
En2(ke2.POLYGON, Ss2);
var Ts2 = {
  area: (e) => {
    const n = e.geometry;
    if (!n.closed || n.points.length < 3)
      return 0;
    const t = un2(n.points, n.closed);
    return jn2(t);
  },
  intersects: (e, n, t, o = 2) => {
    const s = e.geometry;
    if (s.closed) {
      const l = un2(s.points, s.closed);
      return qn2(l, n, t);
    } else
      return Ms2(s, [n, t], o);
  }
};
var un2 = (e, n = false) => {
  const t = [];
  for (let o = 0; o < e.length; o++) {
    const s = e[o], l = e[(o + 1) % e.length];
    if (t.push(s.point), (o < e.length - 1 || n) && (s.type === "CURVE" || l.type == "CURVE")) {
      const r = Yo2(
        s.point,
        s.type === "CURVE" && s.outHandle || s.point,
        l.type === "CURVE" && l.inHandle || l.point,
        l.point,
        10
        // number of approximation segments
      );
      t.push(...r.slice(1));
    }
  }
  return t;
};
var Yo2 = (e, n, t, o, s = 10) => {
  const l = [];
  for (let r = 0; r <= s; r++) {
    const i = r / s, c2 = Math.pow(1 - i, 3) * e[0] + 3 * Math.pow(1 - i, 2) * i * n[0] + 3 * (1 - i) * Math.pow(i, 2) * t[0] + Math.pow(i, 3) * o[0], f = Math.pow(1 - i, 3) * e[1] + 3 * Math.pow(1 - i, 2) * i * n[1] + 3 * (1 - i) * Math.pow(i, 2) * t[1] + Math.pow(i, 3) * o[1];
    l.push([c2, f]);
  }
  return l;
};
var Ms2 = (e, n, t) => {
  for (let o = 0; o < e.points.length - 1; o++) {
    const s = e.points[o], l = e.points[o + 1];
    if (s.type === "CURVE" || l.type === "CURVE") {
      const r = Yo2(
        s.point,
        s.type === "CURVE" && s.outHandle || s.point,
        l.type === "CURVE" && l.inHandle || l.point,
        l.point,
        20
        // TODO make configurable? Based on scale factor? Length?
      );
      for (let i = 0; i < r.length - 1; i++)
        if (Ct(n, r[i], r[i + 1]) <= t)
          return true;
    } else if (Ct(n, s.point, l.point) <= t)
      return true;
  }
  return false;
};
var Ct = (e, n, t) => {
  const [o, s] = e, [l, r] = n, [i, c2] = t, f = i - l, p = c2 - r, u = Math.sqrt(f * f + p * p);
  if (u === 0)
    return Math.sqrt((o - l) * (o - l) + (s - r) * (s - r));
  const d = ((o - l) * f + (s - r) * p) / (u * u);
  return d <= 0 ? Math.sqrt((o - l) * (o - l) + (s - r) * (s - r)) : d >= 1 ? Math.sqrt((o - i) * (o - i) + (s - c2) * (s - c2)) : Math.abs((c2 - r) * o - (i - l) * s + i * r - c2 * l) / u;
};
var Ls2 = (e) => {
  if (!e.points || e.points.length === 0)
    return "";
  const n = [], t = e.points[0];
  n.push(`M ${t.point[0]} ${t.point[1]}`);
  for (let o = 1; o < e.points.length; o++) {
    const s = e.points[o], l = e.points[o - 1];
    if (s.type === "CURVE" || l.type === "CURVE") {
      const r = l.type === "CURVE" && l.outHandle || l.point, i = s.type === "CURVE" && s.inHandle || s.point, c2 = s.point;
      n.push(`C ${r[0]} ${r[1]} ${i[0]} ${i[1]} ${c2[0]} ${c2[1]}`);
    } else
      n.push(`L ${s.point[0]} ${s.point[1]}`);
  }
  if (e.closed) {
    const o = e.points[e.points.length - 1], s = e.points[0];
    if (o.type === "CURVE" || s.type === "CURVE") {
      const l = o.outHandle || o.point, r = s.inHandle || s.point, i = s.point;
      n.push(`C ${l[0]} ${l[1]} ${r[0]} ${r[1]} ${i[0]} ${i[1]}`);
    }
    n.push("Z");
  }
  return n.join(" ");
};
En2(ke2.POLYLINE, Ts2);
var Hs2 = {
  area: (e) => e.geometry.w * e.geometry.h,
  intersects: (e, n, t) => n >= e.geometry.x && n <= e.geometry.x + e.geometry.w && t >= e.geometry.y && t <= e.geometry.y + e.geometry.h
};
En2(ke2.RECTANGLE, Hs2);
var Os2 = [];
for (let e = 0; e < 256; ++e)
  Os2.push((e + 256).toString(16).slice(1));
typeof crypto < "u" && crypto.randomUUID && crypto.randomUUID.bind(crypto);
var Is2 = [];
for (let e = 0; e < 256; ++e)
  Is2.push((e + 256).toString(16).slice(1));
typeof crypto < "u" && crypto.randomUUID && crypto.randomUUID.bind(crypto);
var Ns2 = "useandom-26T198340PX75pxJACKVERYMINDBUSHWOLF_GQZbfghjklqvwyzrict";
var Cs2 = (e = 21) => {
  let n = "", t = crypto.getRandomValues(new Uint8Array(e |= 0));
  for (; e--; )
    n += Ns2[t[e] & 63];
  return n;
};
Cs2();
var An2 = (e, n = 0) => {
  const { minX: t, minY: o, maxX: s, maxY: l } = e;
  return {
    x: t - n,
    y: o - n,
    w: s - t + 2 * n,
    h: l - o + 2 * n
  };
};
var Be2 = typeof window > "u" || typeof navigator > "u" ? false : "ontouchstart" in window || navigator.maxTouchPoints > 0 || // @ts-ignore
navigator.msMaxTouchPoints > 0;
ke2.RECTANGLE, ke2.POLYGON, ke2.MULTIPOLYGON;
typeof navigator > "u" || navigator.userAgent.indexOf("Mac OS X");
function Le2() {
}
function gt2(e, n) {
  for (const t in n)
    e[t] = n[t];
  return (
    /** @type {T & S} */
    e
  );
}
function Wo2(e) {
  return e();
}
function eo2() {
  return /* @__PURE__ */ Object.create(null);
}
function Te2(e) {
  e.forEach(Wo2);
}
function le2(e) {
  return typeof e == "function";
}
function Ne(e, n) {
  return e != e ? n == n : e !== n || e && typeof e == "object" || typeof e == "function";
}
function fl(e) {
  return Object.keys(e).length === 0;
}
function ul(e, n, t, o) {
  if (e) {
    const s = Qo2(e, n, t, o);
    return e[0](s);
  }
}
function Qo2(e, n, t, o) {
  return e[1] && o ? gt2(t.ctx.slice(), e[1](o(n))) : t.ctx;
}
function pl(e, n, t, o) {
  if (e[2] && o) {
    const s = e[2](o(t));
    if (n.dirty === void 0)
      return s;
    if (typeof s == "object") {
      const l = [], r = Math.max(n.dirty.length, s.length);
      for (let i = 0; i < r; i += 1)
        l[i] = n.dirty[i] | s[i];
      return l;
    }
    return n.dirty | s;
  }
  return n.dirty;
}
function hl(e, n, t, o, s, l) {
  if (s) {
    const r = Qo2(n, t, o, l);
    e.p(r, s);
  }
}
function dl(e) {
  if (e.ctx.length > 32) {
    const n = [], t = e.ctx.length / 32;
    for (let o = 0; o < t; o++)
      n[o] = -1;
    return n;
  }
  return -1;
}
function no2(e) {
  const n = {};
  for (const t in e)
    t[0] !== "$" && (n[t] = e[t]);
  return n;
}
function Ge2(e) {
  return e ?? "";
}
function se2(e, n) {
  e.appendChild(n);
}
function F(e, n, t) {
  e.insertBefore(n, t || null);
}
function B(e) {
  e.parentNode && e.parentNode.removeChild(e);
}
function Dn2(e, n) {
  for (let t = 0; t < e.length; t += 1)
    e[t] && e[t].d(n);
}
function V2(e) {
  return document.createElementNS("http://www.w3.org/2000/svg", e);
}
function Jo2(e) {
  return document.createTextNode(e);
}
function ae2() {
  return Jo2(" ");
}
function We2() {
  return Jo2("");
}
function ne(e, n, t, o) {
  return e.addEventListener(n, t, o), () => e.removeEventListener(n, t, o);
}
function a(e, n, t) {
  t == null ? e.removeAttribute(n) : e.getAttribute(n) !== t && e.setAttribute(n, t);
}
function ml(e) {
  return Array.from(e.childNodes);
}
function Gn2(e, n, t) {
  e.classList.toggle(n, !!t);
}
function gl(e, n, { bubbles: t = false, cancelable: o = false } = {}) {
  return new CustomEvent(e, { detail: n, bubbles: t, cancelable: o });
}
var In2;
function Mn2(e) {
  In2 = e;
}
function zo2() {
  if (!In2)
    throw new Error("Function called outside component initialization");
  return In2;
}
function bn2(e) {
  zo2().$$.on_mount.push(e);
}
function on() {
  const e = zo2();
  return (n, t, { cancelable: o = false } = {}) => {
    const s = e.$$.callbacks[n];
    if (s) {
      const l = gl(
        /** @type {string} */
        n,
        t,
        { cancelable: o }
      );
      return s.slice().forEach((r) => {
        r.call(e, l);
      }), !l.defaultPrevented;
    }
    return true;
  };
}
function ce2(e, n) {
  const t = e.$$.callbacks[n.type];
  t && t.slice().forEach((o) => o.call(this, n));
}
var fn2 = [];
var Bn2 = [];
var mn2 = [];
var to2 = [];
var xo2 = /* @__PURE__ */ Promise.resolve();
var _t2 = false;
function es2() {
  _t2 || (_t2 = true, xo2.then(ns2));
}
function kt2() {
  return es2(), xo2;
}
function yt2(e) {
  mn2.push(e);
}
var nt2 = /* @__PURE__ */ new Set();
var rn2 = 0;
function ns2() {
  if (rn2 !== 0)
    return;
  const e = In2;
  do {
    try {
      for (; rn2 < fn2.length; ) {
        const n = fn2[rn2];
        rn2++, Mn2(n), _l(n.$$);
      }
    } catch (n) {
      throw fn2.length = 0, rn2 = 0, n;
    }
    for (Mn2(null), fn2.length = 0, rn2 = 0; Bn2.length; )
      Bn2.pop()();
    for (let n = 0; n < mn2.length; n += 1) {
      const t = mn2[n];
      nt2.has(t) || (nt2.add(t), t());
    }
    mn2.length = 0;
  } while (fn2.length);
  for (; to2.length; )
    to2.pop()();
  _t2 = false, nt2.clear(), Mn2(e);
}
function _l(e) {
  if (e.fragment !== null) {
    e.update(), Te2(e.before_update);
    const n = e.dirty;
    e.dirty = [-1], e.fragment && e.fragment.p(e.ctx, n), e.after_update.forEach(yt2);
  }
}
function yl(e) {
  const n = [], t = [];
  mn2.forEach((o) => e.indexOf(o) === -1 ? n.push(o) : t.push(o)), t.forEach((o) => o()), mn2 = n;
}
var Xn2 = /* @__PURE__ */ new Set();
var nn2;
function Ue() {
  nn2 = {
    r: 0,
    c: [],
    p: nn2
    // parent group
  };
}
function Xe2() {
  nn2.r || Te2(nn2.c), nn2 = nn2.p;
}
function W(e, n) {
  e && e.i && (Xn2.delete(e), e.i(n));
}
function J(e, n, t, o) {
  if (e && e.o) {
    if (Xn2.has(e))
      return;
    Xn2.add(e), nn2.c.push(() => {
      Xn2.delete(e), o && (t && e.d(1), o());
    }), e.o(n);
  } else
    o && o();
}
function Ve2(e) {
  return (e == null ? void 0 : e.length) !== void 0 ? e : Array.from(e);
}
function _e2(e) {
  e && e.c();
}
function he(e, n, t) {
  const { fragment: o, after_update: s } = e.$$;
  o && o.m(n, t), yt2(() => {
    const l = e.$$.on_mount.map(Wo2).filter(le2);
    e.$$.on_destroy ? e.$$.on_destroy.push(...l) : Te2(l), e.$$.on_mount = [];
  }), s.forEach(yt2);
}
function de2(e, n) {
  const t = e.$$;
  t.fragment !== null && (yl(t.after_update), Te2(t.on_destroy), t.fragment && t.fragment.d(n), t.on_destroy = t.fragment = null, t.ctx = []);
}
function wl(e, n) {
  e.$$.dirty[0] === -1 && (fn2.push(e), es2(), e.$$.dirty.fill(0)), e.$$.dirty[n / 31 | 0] |= 1 << n % 31;
}
function Ce(e, n, t, o, s, l, r = null, i = [-1]) {
  const c2 = In2;
  Mn2(e);
  const f = e.$$ = {
    fragment: null,
    ctx: [],
    // state
    props: l,
    update: Le2,
    not_equal: s,
    bound: eo2(),
    // lifecycle
    on_mount: [],
    on_destroy: [],
    on_disconnect: [],
    before_update: [],
    after_update: [],
    context: new Map(n.context || (c2 ? c2.$$.context : [])),
    // everything else
    callbacks: eo2(),
    dirty: i,
    skip_bound: false,
    root: n.target || c2.$$.root
  };
  r && r(f.root);
  let p = false;
  if (f.ctx = t ? t(e, n.props || {}, (u, d, ...g) => {
    const y = g.length ? g[0] : d;
    return f.ctx && s(f.ctx[u], f.ctx[u] = y) && (!f.skip_bound && f.bound[u] && f.bound[u](y), p && wl(e, u)), d;
  }) : [], f.update(), p = true, Te2(f.before_update), f.fragment = o ? o(f.ctx) : false, n.target) {
    if (n.hydrate) {
      const u = ml(n.target);
      f.fragment && f.fragment.l(u), u.forEach(B);
    } else
      f.fragment && f.fragment.c();
    n.intro && W(e.$$.fragment), he(e, n.target, n.anchor), ns2();
  }
  Mn2(c2);
}
var Re2 = class {
  constructor() {
    Kn2(this, "$$");
    Kn2(this, "$$set");
  }
  /** @returns {void} */
  $destroy() {
    de2(this, 1), this.$destroy = Le2;
  }
  /**
   * @template {Extract<keyof Events, string>} K
   * @param {K} type
   * @param {((e: Events[K]) => void) | null | undefined} callback
   * @returns {() => void}
   */
  $on(n, t) {
    if (!le2(t))
      return Le2;
    const o = this.$$.callbacks[n] || (this.$$.callbacks[n] = []);
    return o.push(t), () => {
      const s = o.indexOf(t);
      s !== -1 && o.splice(s, 1);
    };
  }
  /**
   * @param {Partial<Props>} props
   * @returns {void}
   */
  $set(n) {
    this.$$set && !fl(n) && (this.$$.skip_bound = true, this.$$set(n), this.$$.skip_bound = false);
  }
};
var vl = "4";
typeof window < "u" && (window.__svelte || (window.__svelte = { v: /* @__PURE__ */ new Set() })).v.add(vl);
var Fe2 = /* @__PURE__ */ ((e) => (e.ELLIPSE = "ELLIPSE", e.MULTIPOLYGON = "MULTIPOLYGON", e.POLYGON = "POLYGON", e.POLYLINE = "POLYLINE", e.RECTANGLE = "RECTANGLE", e.LINE = "LINE", e))(Fe2 || {});
var El = { exports: {} };
(function(e) {
  (function() {
    function n(i, c2) {
      var f = i.x - c2.x, p = i.y - c2.y;
      return f * f + p * p;
    }
    function t(i, c2, f) {
      var p = c2.x, u = c2.y, d = f.x - p, g = f.y - u;
      if (d !== 0 || g !== 0) {
        var y = ((i.x - p) * d + (i.y - u) * g) / (d * d + g * g);
        y > 1 ? (p = f.x, u = f.y) : y > 0 && (p += d * y, u += g * y);
      }
      return d = i.x - p, g = i.y - u, d * d + g * g;
    }
    function o(i, c2) {
      for (var f = i[0], p = [f], u, d = 1, g = i.length; d < g; d++)
        u = i[d], n(u, f) > c2 && (p.push(u), f = u);
      return f !== u && p.push(u), p;
    }
    function s(i, c2, f, p, u) {
      for (var d = p, g, y = c2 + 1; y < f; y++) {
        var h = t(i[y], i[c2], i[f]);
        h > d && (g = y, d = h);
      }
      d > p && (g - c2 > 1 && s(i, c2, g, p, u), u.push(i[g]), f - g > 1 && s(i, g, f, p, u));
    }
    function l(i, c2) {
      var f = i.length - 1, p = [i[0]];
      return s(i, 0, f, c2, p), p.push(i[f]), p;
    }
    function r(i, c2, f) {
      if (i.length <= 2)
        return i;
      var p = c2 !== void 0 ? c2 * c2 : 1;
      return i = f ? i : o(i, p), i = l(i, p), i;
    }
    e.exports = r, e.exports.default = r;
  })();
})(El);
var kn2 = (e, n) => n;
var Fn2 = (e) => {
  let n = 0, t = e.length - 1;
  for (let o = 0; o < e.length; o++)
    n += (e[t][0] + e[o][0]) * (e[t][1] - e[o][1]), t = o;
  return Math.abs(0.5 * n);
};
var Vn2 = (e, n, t) => {
  let o = false;
  for (let s = 0, l = e.length - 1; s < e.length; l = s++) {
    const r = e[s][0], i = e[s][1], c2 = e[l][0], f = e[l][1];
    i > t != f > t && n < (c2 - r) * (t - i) / (f - i) + r && (o = !o);
  }
  return o;
};
var kl = (e, n) => {
  const t = Math.abs(n[0] - e[0]), o = Math.abs(n[1] - e[1]);
  return Math.sqrt(Math.pow(t, 2) + Math.pow(o, 2));
};
var Pl = {
  area: (e) => Math.PI * e.geometry.rx * e.geometry.ry,
  intersects: (e, n, t) => {
    const { cx: o, cy: s, rx: l, ry: r } = e.geometry, i = 0, c2 = Math.cos(i), f = Math.sin(i), p = n - o, u = t - s, d = c2 * p + f * u, g = f * p - c2 * u;
    return d * d / (l * l) + g * g / (r * r) <= 1;
  }
};
kn2(Fe2.ELLIPSE, Pl);
var Sl = {
  area: (e) => 0,
  intersects: (e, n, t, o = 2) => {
    const [[s, l], [r, i]] = e.geometry.points, c2 = Math.abs((i - l) * n - (r - s) * t + r * l - i * s), f = kl([s, l], [r, i]);
    return c2 / f <= o;
  }
};
kn2(Fe2.LINE, Sl);
var Tl = {
  area: (e) => {
    const { polygons: n } = e.geometry;
    return n.reduce((t, o) => {
      const [s, ...l] = o.rings, r = Fn2(s.points), i = l.reduce((c2, f) => c2 + Fn2(f.points), 0);
      return t + r - i;
    }, 0);
  },
  intersects: (e, n, t) => {
    const { polygons: o } = e.geometry;
    for (const s of o) {
      const [l, ...r] = s.rings;
      if (Vn2(l.points, n, t)) {
        let i = false;
        for (const c2 of r)
          if (Vn2(c2.points, n, t)) {
            i = true;
            break;
          }
        if (!i)
          return true;
      }
    }
    return false;
  }
};
kn2(Fe2.MULTIPOLYGON, Tl);
var Ll = {
  area: (e) => {
    const n = e.geometry.points;
    return Fn2(n);
  },
  intersects: (e, n, t) => {
    const o = e.geometry.points;
    return Vn2(o, n, t);
  }
};
kn2(Fe2.POLYGON, Ll);
var Hl = {
  area: (e) => {
    const n = e.geometry;
    if (!n.closed || n.points.length < 3)
      return 0;
    const t = oo2(n.points, n.closed);
    return Fn2(t);
  },
  intersects: (e, n, t, o = 2) => {
    const s = e.geometry;
    if (s.closed) {
      const l = oo2(s.points, s.closed);
      return Vn2(l, n, t);
    } else
      return Ol(s, [n, t], o);
  }
};
var oo2 = (e, n = false) => {
  const t = [];
  for (let o = 0; o < e.length; o++) {
    const s = e[o], l = e[(o + 1) % e.length];
    if (t.push(s.point), (o < e.length - 1 || n) && (s.type === "CURVE" || l.type == "CURVE")) {
      const i = ts2(
        s.point,
        s.type === "CURVE" && s.outHandle || s.point,
        l.type === "CURVE" && l.inHandle || l.point,
        l.point,
        10
        // number of approximation segments
      );
      t.push(...i.slice(1));
    }
  }
  return t;
};
var ts2 = (e, n, t, o, s = 10) => {
  const l = [];
  for (let r = 0; r <= s; r++) {
    const i = r / s, c2 = Math.pow(1 - i, 3) * e[0] + 3 * Math.pow(1 - i, 2) * i * n[0] + 3 * (1 - i) * Math.pow(i, 2) * t[0] + Math.pow(i, 3) * o[0], f = Math.pow(1 - i, 3) * e[1] + 3 * Math.pow(1 - i, 2) * i * n[1] + 3 * (1 - i) * Math.pow(i, 2) * t[1] + Math.pow(i, 3) * o[1];
    l.push([c2, f]);
  }
  return l;
};
var Ol = (e, n, t) => {
  for (let o = 0; o < e.points.length - 1; o++) {
    const s = e.points[o], l = e.points[o + 1];
    if (s.type === "CURVE" || l.type === "CURVE") {
      const i = ts2(
        s.point,
        s.type === "CURVE" && s.outHandle || s.point,
        l.type === "CURVE" && l.inHandle || l.point,
        l.point,
        20
        // TODO make configurable? Based on scale factor? Length?
      );
      for (let c2 = 0; c2 < i.length - 1; c2++)
        if (so2(n, i[c2], i[c2 + 1]) <= t)
          return true;
    } else if (so2(n, s.point, l.point) <= t)
      return true;
  }
  return false;
};
var so2 = (e, n, t) => {
  const [o, s] = e, [l, r] = n, [i, c2] = t, f = i - l, p = c2 - r, u = Math.sqrt(f * f + p * p);
  if (u === 0)
    return Math.sqrt((o - l) * (o - l) + (s - r) * (s - r));
  const d = ((o - l) * f + (s - r) * p) / (u * u);
  return d <= 0 ? Math.sqrt((o - l) * (o - l) + (s - r) * (s - r)) : d >= 1 ? Math.sqrt((o - i) * (o - i) + (s - c2) * (s - c2)) : Math.abs((c2 - r) * o - (i - l) * s + i * r - c2 * l) / u;
};
kn2(Fe2.POLYLINE, Hl);
var Il = {
  area: (e) => e.geometry.w * e.geometry.h,
  intersects: (e, n, t) => n >= e.geometry.x && n <= e.geometry.x + e.geometry.w && t >= e.geometry.y && t <= e.geometry.y + e.geometry.h
};
kn2(Fe2.RECTANGLE, Il);
var Nl = [];
for (let e = 0; e < 256; ++e)
  Nl.push((e + 256).toString(16).slice(1));
typeof crypto < "u" && crypto.randomUUID && crypto.randomUUID.bind(crypto);
var Cl = "useandom-26T198340PX75pxJACKVERYMINDBUSHWOLF_GQZbfghjklqvwyzrict";
var Rl = (e = 21) => {
  let n = "", t = crypto.getRandomValues(new Uint8Array(e |= 0));
  for (; e--; )
    n += Cl[t[e] & 63];
  return n;
};
Rl();
var Je2 = typeof window > "u" || typeof navigator > "u" ? false : "ontouchstart" in window || navigator.maxTouchPoints > 0 || // @ts-ignore
navigator.msMaxTouchPoints > 0;
var Al = (e) => ({});
var lo2 = (e) => ({ grab: (
  /*onGrab*/
  e[0]
) });
function Dl(e) {
  let n, t, o, s;
  const l = (
    /*#slots*/
    e[8].default
  ), r = ul(
    l,
    e,
    /*$$scope*/
    e[7],
    lo2
  );
  return {
    c() {
      n = V2("g"), r && r.c(), a(n, "class", "a9s-annotation selected");
    },
    m(i, c2) {
      F(i, n, c2), r && r.m(n, null), t = true, o || (s = [
        ne(
          n,
          "pointerup",
          /*onRelease*/
          e[2]
        ),
        ne(
          n,
          "pointermove",
          /*onPointerMove*/
          e[1]
        )
      ], o = true);
    },
    p(i, [c2]) {
      r && r.p && (!t || c2 & /*$$scope*/
      128) && hl(
        r,
        l,
        i,
        /*$$scope*/
        i[7],
        t ? pl(
          l,
          /*$$scope*/
          i[7],
          c2,
          Al
        ) : dl(
          /*$$scope*/
          i[7]
        ),
        lo2
      );
    },
    i(i) {
      t || (W(r, i), t = true);
    },
    o(i) {
      J(r, i), t = false;
    },
    d(i) {
      i && B(n), r && r.d(i), o = false, Te2(s);
    }
  };
}
function Ul(e, n, t) {
  let { $$slots: o = {}, $$scope: s } = n;
  const l = on();
  let { shape: r } = n, { editor: i } = n, { transform: c2 } = n, { svgEl: f } = n, p, u, d;
  const g = (k) => (D) => {
    if (p = k, f) {
      const { left: _, top: P } = f.getBoundingClientRect(), S = D.clientX - _, H2 = D.clientY - P;
      u = c2.elementToImage(S, H2);
    } else {
      const { offsetX: _, offsetY: P } = D;
      u = c2.elementToImage(_, P);
    }
    d = r, D.target.setPointerCapture(D.pointerId), l("grab", D);
  }, y = (k) => {
    if (p) {
      const [D, N] = c2.elementToImage(k.offsetX, k.offsetY), _ = [D - u[0], N - u[1]];
      t(3, r = i(d, p, _)), l("change", r);
    }
  }, h = (k) => {
    k.target.releasePointerCapture(k.pointerId), p = void 0, d = r, l("release", k);
  };
  return e.$$set = (k) => {
    "shape" in k && t(3, r = k.shape), "editor" in k && t(4, i = k.editor), "transform" in k && t(5, c2 = k.transform), "svgEl" in k && t(6, f = k.svgEl), "$$scope" in k && t(7, s = k.$$scope);
  }, [
    g,
    y,
    h,
    r,
    i,
    c2,
    f,
    s,
    o
  ];
}
var Pn2 = class extends Re2 {
  constructor(n) {
    super(), Ce(this, n, Ul, Dl, Ne, {
      shape: 3,
      editor: 4,
      transform: 5,
      svgEl: 6
    });
  }
};
function Xl(e) {
  let n, t, o, s, l, r, i, c2, f = (
    /*selected*/
    e[3] && io2(e)
  );
  return {
    c() {
      n = V2("g"), t = V2("circle"), f && f.c(), s = V2("circle"), a(t, "class", "a9s-handle-buffer svelte-qtyc7s"), a(
        t,
        "cx",
        /*x*/
        e[0]
      ), a(
        t,
        "cy",
        /*y*/
        e[1]
      ), a(t, "r", o = /*handleRadius*/
      e[5] + 6 / /*scale*/
      e[2]), a(t, "role", "button"), a(t, "tabindex", "0"), a(s, "class", l = Ge2(`a9s-handle-dot${/*selected*/
      e[3] ? " selected" : ""}`) + " svelte-qtyc7s"), a(
        s,
        "cx",
        /*x*/
        e[0]
      ), a(
        s,
        "cy",
        /*y*/
        e[1]
      ), a(
        s,
        "r",
        /*handleRadius*/
        e[5]
      ), a(n, "class", r = `a9s-handle ${/*$$props*/
      e[8].class || ""}`.trim());
    },
    m(p, u) {
      F(p, n, u), se2(n, t), f && f.m(n, null), se2(n, s), i || (c2 = [
        ne(
          t,
          "dblclick",
          /*dblclick_handler_1*/
          e[12]
        ),
        ne(
          t,
          "pointerenter",
          /*pointerenter_handler*/
          e[13]
        ),
        ne(
          t,
          "pointerleave",
          /*pointerleave_handler*/
          e[14]
        ),
        ne(
          t,
          "pointerdown",
          /*pointerdown_handler_1*/
          e[15]
        ),
        ne(
          t,
          "pointerdown",
          /*onPointerDown*/
          e[6]
        ),
        ne(
          t,
          "pointerup",
          /*pointerup_handler_1*/
          e[16]
        ),
        ne(
          t,
          "pointerup",
          /*onPointerUp*/
          e[7]
        )
      ], i = true);
    },
    p(p, u) {
      u & /*x*/
      1 && a(
        t,
        "cx",
        /*x*/
        p[0]
      ), u & /*y*/
      2 && a(
        t,
        "cy",
        /*y*/
        p[1]
      ), u & /*handleRadius, scale*/
      36 && o !== (o = /*handleRadius*/
      p[5] + 6 / /*scale*/
      p[2]) && a(t, "r", o), /*selected*/
      p[3] ? f ? f.p(p, u) : (f = io2(p), f.c(), f.m(n, s)) : f && (f.d(1), f = null), u & /*selected*/
      8 && l !== (l = Ge2(`a9s-handle-dot${/*selected*/
      p[3] ? " selected" : ""}`) + " svelte-qtyc7s") && a(s, "class", l), u & /*x*/
      1 && a(
        s,
        "cx",
        /*x*/
        p[0]
      ), u & /*y*/
      2 && a(
        s,
        "cy",
        /*y*/
        p[1]
      ), u & /*handleRadius*/
      32 && a(
        s,
        "r",
        /*handleRadius*/
        p[5]
      ), u & /*$$props*/
      256 && r !== (r = `a9s-handle ${/*$$props*/
      p[8].class || ""}`.trim()) && a(n, "class", r);
    },
    d(p) {
      p && B(n), f && f.d(), i = false, Te2(c2);
    }
  };
}
function jl(e) {
  let n, t, o, s, l, r, i, c2, f;
  return {
    c() {
      n = V2("g"), t = V2("circle"), s = V2("circle"), r = V2("circle"), a(
        t,
        "cx",
        /*x*/
        e[0]
      ), a(
        t,
        "cy",
        /*y*/
        e[1]
      ), a(t, "r", o = /*handleRadius*/
      e[5] * 10), a(t, "class", "a9s-touch-halo"), Gn2(
        t,
        "touched",
        /*touched*/
        e[4]
      ), a(
        s,
        "cx",
        /*x*/
        e[0]
      ), a(
        s,
        "cy",
        /*y*/
        e[1]
      ), a(s, "r", l = /*handleRadius*/
      e[5] + 10 / /*scale*/
      e[2]), a(s, "class", "a9s-handle-buffer svelte-qtyc7s"), a(s, "role", "button"), a(s, "tabindex", "0"), a(r, "class", "a9s-handle-dot"), a(
        r,
        "cx",
        /*x*/
        e[0]
      ), a(
        r,
        "cy",
        /*y*/
        e[1]
      ), a(r, "r", i = /*handleRadius*/
      e[5] + 2 / /*scale*/
      e[2]), a(n, "class", "a9s-touch-handle");
    },
    m(p, u) {
      F(p, n, u), se2(n, t), se2(n, s), se2(n, r), c2 || (f = [
        ne(
          s,
          "dblclick",
          /*dblclick_handler*/
          e[9]
        ),
        ne(
          s,
          "pointerdown",
          /*pointerdown_handler*/
          e[10]
        ),
        ne(
          s,
          "pointerdown",
          /*onPointerDown*/
          e[6]
        ),
        ne(
          s,
          "pointerup",
          /*pointerup_handler*/
          e[11]
        ),
        ne(
          s,
          "pointerup",
          /*onPointerUp*/
          e[7]
        )
      ], c2 = true);
    },
    p(p, u) {
      u & /*x*/
      1 && a(
        t,
        "cx",
        /*x*/
        p[0]
      ), u & /*y*/
      2 && a(
        t,
        "cy",
        /*y*/
        p[1]
      ), u & /*handleRadius*/
      32 && o !== (o = /*handleRadius*/
      p[5] * 10) && a(t, "r", o), u & /*touched*/
      16 && Gn2(
        t,
        "touched",
        /*touched*/
        p[4]
      ), u & /*x*/
      1 && a(
        s,
        "cx",
        /*x*/
        p[0]
      ), u & /*y*/
      2 && a(
        s,
        "cy",
        /*y*/
        p[1]
      ), u & /*handleRadius, scale*/
      36 && l !== (l = /*handleRadius*/
      p[5] + 10 / /*scale*/
      p[2]) && a(s, "r", l), u & /*x*/
      1 && a(
        r,
        "cx",
        /*x*/
        p[0]
      ), u & /*y*/
      2 && a(
        r,
        "cy",
        /*y*/
        p[1]
      ), u & /*handleRadius, scale*/
      36 && i !== (i = /*handleRadius*/
      p[5] + 2 / /*scale*/
      p[2]) && a(r, "r", i);
    },
    d(p) {
      p && B(n), c2 = false, Te2(f);
    }
  };
}
function io2(e) {
  let n, t;
  return {
    c() {
      n = V2("circle"), a(n, "class", "a9s-handle-selected"), a(
        n,
        "cx",
        /*x*/
        e[0]
      ), a(
        n,
        "cy",
        /*y*/
        e[1]
      ), a(n, "r", t = /*handleRadius*/
      e[5] + 8 / /*scale*/
      e[2]);
    },
    m(o, s) {
      F(o, n, s);
    },
    p(o, s) {
      s & /*x*/
      1 && a(
        n,
        "cx",
        /*x*/
        o[0]
      ), s & /*y*/
      2 && a(
        n,
        "cy",
        /*y*/
        o[1]
      ), s & /*handleRadius, scale*/
      36 && t !== (t = /*handleRadius*/
      o[5] + 8 / /*scale*/
      o[2]) && a(n, "r", t);
    },
    d(o) {
      o && B(n);
    }
  };
}
function ql(e) {
  let n;
  function t(l, r) {
    return Je2 ? jl : Xl;
  }
  let s = t()(e);
  return {
    c() {
      s.c(), n = We2();
    },
    m(l, r) {
      s.m(l, r), F(l, n, r);
    },
    p(l, [r]) {
      s.p(l, r);
    },
    i: Le2,
    o: Le2,
    d(l) {
      l && B(n), s.d(l);
    }
  };
}
function Gl(e, n, t) {
  let o, { x: s } = n, { y: l } = n, { scale: r } = n, { selected: i = void 0 } = n, c2 = false;
  const f = (_) => {
    _.pointerType === "touch" && t(4, c2 = true);
  }, p = () => t(4, c2 = false);
  function u(_) {
    ce2.call(this, e, _);
  }
  function d(_) {
    ce2.call(this, e, _);
  }
  function g(_) {
    ce2.call(this, e, _);
  }
  function y(_) {
    ce2.call(this, e, _);
  }
  function h(_) {
    ce2.call(this, e, _);
  }
  function k(_) {
    ce2.call(this, e, _);
  }
  function D(_) {
    ce2.call(this, e, _);
  }
  function N(_) {
    ce2.call(this, e, _);
  }
  return e.$$set = (_) => {
    t(8, n = gt2(gt2({}, n), no2(_))), "x" in _ && t(0, s = _.x), "y" in _ && t(1, l = _.y), "scale" in _ && t(2, r = _.scale), "selected" in _ && t(3, i = _.selected);
  }, e.$$.update = () => {
    e.$$.dirty & /*scale*/
    4 && t(5, o = 4 / r);
  }, n = no2(n), [
    s,
    l,
    r,
    i,
    c2,
    o,
    f,
    p,
    n,
    u,
    d,
    g,
    y,
    h,
    k,
    D,
    N
  ];
}
var Ie = class extends Re2 {
  constructor(n) {
    super(), Ce(this, n, Gl, ql, Ne, { x: 0, y: 1, scale: 2, selected: 3 });
  }
};
function Bl(e) {
  let n, t, o, s, l, r, i;
  return {
    c() {
      n = V2("g"), t = V2("circle"), s = V2("circle"), l = V2("circle"), a(t, "class", "a9s-polygon-midpoint-buffer svelte-12ykj76"), a(
        t,
        "cx",
        /*x*/
        e[0]
      ), a(
        t,
        "cy",
        /*y*/
        e[1]
      ), a(t, "r", o = 1.75 * /*handleRadius*/
      e[2]), a(s, "class", "a9s-polygon-midpoint-outer svelte-12ykj76"), a(
        s,
        "cx",
        /*x*/
        e[0]
      ), a(
        s,
        "cy",
        /*y*/
        e[1]
      ), a(
        s,
        "r",
        /*handleRadius*/
        e[2]
      ), a(l, "class", "a9s-polygon-midpoint-inner svelte-12ykj76"), a(
        l,
        "cx",
        /*x*/
        e[0]
      ), a(
        l,
        "cy",
        /*y*/
        e[1]
      ), a(
        l,
        "r",
        /*handleRadius*/
        e[2]
      ), a(n, "class", "a9s-polygon-midpoint svelte-12ykj76");
    },
    m(c2, f) {
      F(c2, n, f), se2(n, t), se2(n, s), se2(n, l), r || (i = [
        ne(
          t,
          "pointerdown",
          /*pointerdown_handler*/
          e[5]
        ),
        ne(
          t,
          "pointerdown",
          /*onPointerDown*/
          e[3]
        )
      ], r = true);
    },
    p(c2, f) {
      f & /*x*/
      1 && a(
        t,
        "cx",
        /*x*/
        c2[0]
      ), f & /*y*/
      2 && a(
        t,
        "cy",
        /*y*/
        c2[1]
      ), f & /*handleRadius*/
      4 && o !== (o = 1.75 * /*handleRadius*/
      c2[2]) && a(t, "r", o), f & /*x*/
      1 && a(
        s,
        "cx",
        /*x*/
        c2[0]
      ), f & /*y*/
      2 && a(
        s,
        "cy",
        /*y*/
        c2[1]
      ), f & /*handleRadius*/
      4 && a(
        s,
        "r",
        /*handleRadius*/
        c2[2]
      ), f & /*x*/
      1 && a(
        l,
        "cx",
        /*x*/
        c2[0]
      ), f & /*y*/
      2 && a(
        l,
        "cy",
        /*y*/
        c2[1]
      ), f & /*handleRadius*/
      4 && a(
        l,
        "r",
        /*handleRadius*/
        c2[2]
      );
    },
    d(c2) {
      c2 && B(n), r = false, Te2(i);
    }
  };
}
function Fl(e) {
  let n;
  return {
    c() {
      n = V2("circle"), a(
        n,
        "cx",
        /*x*/
        e[0]
      ), a(
        n,
        "cy",
        /*y*/
        e[1]
      ), a(
        n,
        "r",
        /*handleRadius*/
        e[2]
      );
    },
    m(t, o) {
      F(t, n, o);
    },
    p(t, o) {
      o & /*x*/
      1 && a(
        n,
        "cx",
        /*x*/
        t[0]
      ), o & /*y*/
      2 && a(
        n,
        "cy",
        /*y*/
        t[1]
      ), o & /*handleRadius*/
      4 && a(
        n,
        "r",
        /*handleRadius*/
        t[2]
      );
    },
    d(t) {
      t && B(n);
    }
  };
}
function Vl(e) {
  let n;
  function t(l, r) {
    return Je2 ? Fl : Bl;
  }
  let s = t()(e);
  return {
    c() {
      s.c(), n = We2();
    },
    m(l, r) {
      s.m(l, r), F(l, n, r);
    },
    p(l, [r]) {
      s.p(l, r);
    },
    i: Le2,
    o: Le2,
    d(l) {
      l && B(n), s.d(l);
    }
  };
}
function $l(e, n, t) {
  let o, { x: s } = n, { y: l } = n, { scale: r } = n;
  const i = (f) => {
    f.pointerType;
  };
  function c2(f) {
    ce2.call(this, e, f);
  }
  return e.$$set = (f) => {
    "x" in f && t(0, s = f.x), "y" in f && t(1, l = f.y), "scale" in f && t(4, r = f.scale);
  }, e.$$.update = () => {
    e.$$.dirty & /*scale*/
    16 && t(2, o = 4 / r);
  }, [s, l, o, i, r, c2];
}
var St2 = class extends Re2 {
  constructor(n) {
    super(), Ce(this, n, $l, Vl, Ne, { x: 0, y: 1, scale: 4 });
  }
};
Fe2.RECTANGLE, Fe2.POLYGON, Fe2.MULTIPOLYGON;
typeof navigator > "u" ? false : navigator.userAgent.indexOf("Mac OS X") !== -1;
function ui2(e) {
  let n, t, o, s, l, r, i, c2, f, p, u, d, g, y, h, k, D, N, _, P, S, H2, w, I, M, j, U, L, X, b, v, A, E, C2, G2;
  return j = new Ie({
    props: {
      class: "a9s-corner-top",
      x: (
        /*geom*/
        e[5].cx
      ),
      y: (
        /*geom*/
        e[5].cy - /*geom*/
        e[5].ry
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), j.$on("pointerdown", function() {
    le2(
      /*grab*/
      e[12]("TOP")
    ) && e[12]("TOP").apply(this, arguments);
  }), L = new Ie({
    props: {
      class: "a9s-corner-handle-right",
      x: (
        /*geom*/
        e[5].cx + /*geom*/
        e[5].rx
      ),
      y: (
        /*geom*/
        e[5].cy
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), L.$on("pointerdown", function() {
    le2(
      /*grab*/
      e[12]("RIGHT")
    ) && e[12]("RIGHT").apply(this, arguments);
  }), b = new Ie({
    props: {
      class: "a9s-corner-handle-bottom",
      x: (
        /*geom*/
        e[5].cx
      ),
      y: (
        /*geom*/
        e[5].cy + /*geom*/
        e[5].ry
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), b.$on("pointerdown", function() {
    le2(
      /*grab*/
      e[12]("BOTTOM")
    ) && e[12]("BOTTOM").apply(this, arguments);
  }), A = new Ie({
    props: {
      class: "a9s-corner-handle-left",
      x: (
        /*geom*/
        e[5].cx - /*geom*/
        e[5].rx
      ),
      y: (
        /*geom*/
        e[5].cy
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), A.$on("pointerdown", function() {
    le2(
      /*grab*/
      e[12]("LEFT")
    ) && e[12]("LEFT").apply(this, arguments);
  }), {
    c() {
      n = V2("defs"), t = V2("mask"), o = V2("rect"), c2 = V2("ellipse"), g = ae2(), y = V2("ellipse"), _ = ae2(), P = V2("ellipse"), M = ae2(), _e2(j.$$.fragment), U = ae2(), _e2(L.$$.fragment), X = ae2(), _e2(b.$$.fragment), v = ae2(), _e2(A.$$.fragment), a(o, "x", s = /*mask*/
      e[6].x), a(o, "y", l = /*mask*/
      e[6].y), a(o, "width", r = /*mask*/
      e[6].w), a(o, "height", i = /*mask*/
      e[6].h), a(o, "class", "svelte-wdm5en"), a(c2, "cx", f = /*geom*/
      e[5].cx), a(c2, "cy", p = /*geom*/
      e[5].cy), a(c2, "rx", u = /*geom*/
      e[5].rx), a(c2, "ry", d = /*geom*/
      e[5].ry), a(c2, "class", "svelte-wdm5en"), a(
        t,
        "id",
        /*maskId*/
        e[8]
      ), a(t, "class", "a9s-ellipse-editor-mask svelte-wdm5en"), a(y, "class", "a9s-outer"), a(y, "mask", `url(#${/*maskId*/
      e[8]})`), a(y, "cx", h = /*geom*/
      e[5].cx), a(y, "cy", k = /*geom*/
      e[5].cy), a(y, "rx", D = /*geom*/
      e[5].rx), a(y, "ry", N = /*geom*/
      e[5].ry), a(P, "class", "a9s-inner a9s-shape-handle"), a(
        P,
        "style",
        /*computedStyle*/
        e[1]
      ), a(P, "cx", S = /*geom*/
      e[5].cx), a(P, "cy", H2 = /*geom*/
      e[5].cy), a(P, "rx", w = /*geom*/
      e[5].rx), a(P, "ry", I = /*geom*/
      e[5].ry);
    },
    m(T, O) {
      F(T, n, O), se2(n, t), se2(t, o), se2(t, c2), F(T, g, O), F(T, y, O), F(T, _, O), F(T, P, O), F(T, M, O), he(j, T, O), F(T, U, O), he(L, T, O), F(T, X, O), he(b, T, O), F(T, v, O), he(A, T, O), E = true, C2 || (G2 = [
        ne(y, "pointerdown", function() {
          le2(
            /*grab*/
            e[12]("SHAPE")
          ) && e[12]("SHAPE").apply(this, arguments);
        }),
        ne(P, "pointerdown", function() {
          le2(
            /*grab*/
            e[12]("SHAPE")
          ) && e[12]("SHAPE").apply(this, arguments);
        })
      ], C2 = true);
    },
    p(T, O) {
      e = T, (!E || O & /*mask*/
      64 && s !== (s = /*mask*/
      e[6].x)) && a(o, "x", s), (!E || O & /*mask*/
      64 && l !== (l = /*mask*/
      e[6].y)) && a(o, "y", l), (!E || O & /*mask*/
      64 && r !== (r = /*mask*/
      e[6].w)) && a(o, "width", r), (!E || O & /*mask*/
      64 && i !== (i = /*mask*/
      e[6].h)) && a(o, "height", i), (!E || O & /*geom*/
      32 && f !== (f = /*geom*/
      e[5].cx)) && a(c2, "cx", f), (!E || O & /*geom*/
      32 && p !== (p = /*geom*/
      e[5].cy)) && a(c2, "cy", p), (!E || O & /*geom*/
      32 && u !== (u = /*geom*/
      e[5].rx)) && a(c2, "rx", u), (!E || O & /*geom*/
      32 && d !== (d = /*geom*/
      e[5].ry)) && a(c2, "ry", d), (!E || O & /*geom*/
      32 && h !== (h = /*geom*/
      e[5].cx)) && a(y, "cx", h), (!E || O & /*geom*/
      32 && k !== (k = /*geom*/
      e[5].cy)) && a(y, "cy", k), (!E || O & /*geom*/
      32 && D !== (D = /*geom*/
      e[5].rx)) && a(y, "rx", D), (!E || O & /*geom*/
      32 && N !== (N = /*geom*/
      e[5].ry)) && a(y, "ry", N), (!E || O & /*computedStyle*/
      2) && a(
        P,
        "style",
        /*computedStyle*/
        e[1]
      ), (!E || O & /*geom*/
      32 && S !== (S = /*geom*/
      e[5].cx)) && a(P, "cx", S), (!E || O & /*geom*/
      32 && H2 !== (H2 = /*geom*/
      e[5].cy)) && a(P, "cy", H2), (!E || O & /*geom*/
      32 && w !== (w = /*geom*/
      e[5].rx)) && a(P, "rx", w), (!E || O & /*geom*/
      32 && I !== (I = /*geom*/
      e[5].ry)) && a(P, "ry", I);
      const q = {};
      O & /*geom*/
      32 && (q.x = /*geom*/
      e[5].cx), O & /*geom*/
      32 && (q.y = /*geom*/
      e[5].cy - /*geom*/
      e[5].ry), O & /*viewportScale*/
      8 && (q.scale = /*viewportScale*/
      e[3]), j.$set(q);
      const Y2 = {};
      O & /*geom*/
      32 && (Y2.x = /*geom*/
      e[5].cx + /*geom*/
      e[5].rx), O & /*geom*/
      32 && (Y2.y = /*geom*/
      e[5].cy), O & /*viewportScale*/
      8 && (Y2.scale = /*viewportScale*/
      e[3]), L.$set(Y2);
      const Q2 = {};
      O & /*geom*/
      32 && (Q2.x = /*geom*/
      e[5].cx), O & /*geom*/
      32 && (Q2.y = /*geom*/
      e[5].cy + /*geom*/
      e[5].ry), O & /*viewportScale*/
      8 && (Q2.scale = /*viewportScale*/
      e[3]), b.$set(Q2);
      const $2 = {};
      O & /*geom*/
      32 && ($2.x = /*geom*/
      e[5].cx - /*geom*/
      e[5].rx), O & /*geom*/
      32 && ($2.y = /*geom*/
      e[5].cy), O & /*viewportScale*/
      8 && ($2.scale = /*viewportScale*/
      e[3]), A.$set($2);
    },
    i(T) {
      E || (W(j.$$.fragment, T), W(L.$$.fragment, T), W(b.$$.fragment, T), W(A.$$.fragment, T), E = true);
    },
    o(T) {
      J(j.$$.fragment, T), J(L.$$.fragment, T), J(b.$$.fragment, T), J(A.$$.fragment, T), E = false;
    },
    d(T) {
      T && (B(n), B(g), B(y), B(_), B(P), B(M), B(U), B(X), B(v)), de2(j, T), de2(L, T), de2(b, T), de2(A, T), C2 = false, Te2(G2);
    }
  };
}
function pi2(e) {
  let n, t;
  return n = new Pn2({
    props: {
      shape: (
        /*shape*/
        e[0]
      ),
      transform: (
        /*transform*/
        e[2]
      ),
      editor: (
        /*editor*/
        e[7]
      ),
      svgEl: (
        /*svgEl*/
        e[4]
      ),
      $$slots: {
        default: [
          ui2,
          ({ grab: o }) => ({ 12: o }),
          ({ grab: o }) => o ? 4096 : 0
        ]
      },
      $$scope: { ctx: e }
    }
  }), n.$on(
    "grab",
    /*grab_handler*/
    e[9]
  ), n.$on(
    "change",
    /*change_handler*/
    e[10]
  ), n.$on(
    "release",
    /*release_handler*/
    e[11]
  ), {
    c() {
      _e2(n.$$.fragment);
    },
    m(o, s) {
      he(n, o, s), t = true;
    },
    p(o, [s]) {
      const l = {};
      s & /*shape*/
      1 && (l.shape = /*shape*/
      o[0]), s & /*transform*/
      4 && (l.transform = /*transform*/
      o[2]), s & /*svgEl*/
      16 && (l.svgEl = /*svgEl*/
      o[4]), s & /*$$scope, geom, viewportScale, grab, computedStyle, mask*/
      12394 && (l.$$scope = { dirty: s, ctx: o }), n.$set(l);
    },
    i(o) {
      t || (W(n.$$.fragment, o), t = true);
    },
    o(o) {
      J(n.$$.fragment, o), t = false;
    },
    d(o) {
      de2(n, o);
    }
  };
}
function hi2(e, n, t) {
  let o, s, { shape: l } = n, { computedStyle: r } = n, { transform: i } = n, { viewportScale: c2 = 1 } = n, { svgEl: f } = n;
  const p = (h, k, D) => {
    const N = h.geometry.bounds;
    let [_, P] = [N.minX, N.minY], [S, H2] = [N.maxX, N.maxY];
    const [w, I] = D;
    if (k === "SHAPE")
      _ += w, S += w, P += I, H2 += I;
    else
      switch (k) {
        case "TOP": {
          P += I;
          break;
        }
        case "BOTTOM": {
          H2 += I;
          break;
        }
        case "LEFT": {
          _ += w;
          break;
        }
        case "RIGHT": {
          S += w;
          break;
        }
      }
    const M = Math.min(_, S), j = Math.min(P, H2), U = Math.abs(S - _), L = Math.abs(H2 - P), X = (_ + S) / 2, b = (P + H2) / 2, v = U / 2, A = L / 2;
    return {
      ...h,
      geometry: {
        ...h.geometry,
        cx: X,
        cy: b,
        rx: v,
        ry: A,
        bounds: {
          minX: M,
          minY: j,
          maxX: M + U,
          maxY: j + L
        }
      }
    };
  }, u = `ellipse-mask-${Math.random().toString(36).substring(2, 12)}`;
  function d(h) {
    ce2.call(this, e, h);
  }
  function g(h) {
    ce2.call(this, e, h);
  }
  function y(h) {
    ce2.call(this, e, h);
  }
  return e.$$set = (h) => {
    "shape" in h && t(0, l = h.shape), "computedStyle" in h && t(1, r = h.computedStyle), "transform" in h && t(2, i = h.transform), "viewportScale" in h && t(3, c2 = h.viewportScale), "svgEl" in h && t(4, f = h.svgEl);
  }, e.$$.update = () => {
    e.$$.dirty & /*shape*/
    1 && t(5, o = l.geometry), e.$$.dirty & /*geom, viewportScale*/
    40 && t(6, s = An2(o.bounds, 2 / c2));
  }, [
    l,
    r,
    i,
    c2,
    f,
    o,
    s,
    p,
    u,
    d,
    g,
    y
  ];
}
var di2 = class extends Re2 {
  constructor(n) {
    super(), Ce(this, n, hi2, pi2, Ne, {
      shape: 0,
      computedStyle: 1,
      transform: 2,
      viewportScale: 3,
      svgEl: 4
    });
  }
};
function To2(e) {
  let n, t, o, s, l, r, i, c2, f, p, u, d, g, y, h, k, D, N, _, P, S, H2;
  return {
    c() {
      n = V2("defs"), t = V2("mask"), o = V2("rect"), c2 = V2("ellipse"), g = V2("ellipse"), N = V2("ellipse"), a(o, "x", s = /*x*/
      e[2] - /*buffer*/
      e[6]), a(o, "y", l = /*y*/
      e[3] - /*buffer*/
      e[6]), a(o, "width", r = /*w*/
      e[4] + 2 * /*buffer*/
      e[6]), a(o, "height", i = /*h*/
      e[5] + 2 * /*buffer*/
      e[6]), a(o, "class", "svelte-1koqlb3"), a(c2, "cx", f = /*x*/
      e[2] + /*w*/
      e[4] / 2), a(c2, "cy", p = /*y*/
      e[3] + /*h*/
      e[5] / 2), a(c2, "rx", u = /*w*/
      e[4] / 2), a(c2, "ry", d = /*h*/
      e[5] / 2), a(c2, "class", "svelte-1koqlb3"), a(
        t,
        "id",
        /*maskId*/
        e[7]
      ), a(t, "class", "a9s-rubberband-ellipse-mask svelte-1koqlb3"), a(g, "class", "a9s-outer"), a(g, "mask", `url(#${/*maskId*/
      e[7]})`), a(g, "cx", y = /*x*/
      e[2] + /*w*/
      e[4] / 2), a(g, "cy", h = /*y*/
      e[3] + /*h*/
      e[5] / 2), a(g, "rx", k = /*w*/
      e[4] / 2), a(g, "ry", D = /*h*/
      e[5] / 2), a(N, "class", "a9s-inner"), a(N, "cx", _ = /*x*/
      e[2] + /*w*/
      e[4] / 2), a(N, "cy", P = /*y*/
      e[3] + /*h*/
      e[5] / 2), a(N, "rx", S = /*w*/
      e[4] / 2), a(N, "ry", H2 = /*h*/
      e[5] / 2);
    },
    m(w, I) {
      F(w, n, I), se2(n, t), se2(t, o), se2(t, c2), F(w, g, I), F(w, N, I);
    },
    p(w, I) {
      I & /*x, buffer*/
      68 && s !== (s = /*x*/
      w[2] - /*buffer*/
      w[6]) && a(o, "x", s), I & /*y, buffer*/
      72 && l !== (l = /*y*/
      w[3] - /*buffer*/
      w[6]) && a(o, "y", l), I & /*w, buffer*/
      80 && r !== (r = /*w*/
      w[4] + 2 * /*buffer*/
      w[6]) && a(o, "width", r), I & /*h, buffer*/
      96 && i !== (i = /*h*/
      w[5] + 2 * /*buffer*/
      w[6]) && a(o, "height", i), I & /*x, w*/
      20 && f !== (f = /*x*/
      w[2] + /*w*/
      w[4] / 2) && a(c2, "cx", f), I & /*y, h*/
      40 && p !== (p = /*y*/
      w[3] + /*h*/
      w[5] / 2) && a(c2, "cy", p), I & /*w*/
      16 && u !== (u = /*w*/
      w[4] / 2) && a(c2, "rx", u), I & /*h*/
      32 && d !== (d = /*h*/
      w[5] / 2) && a(c2, "ry", d), I & /*x, w*/
      20 && y !== (y = /*x*/
      w[2] + /*w*/
      w[4] / 2) && a(g, "cx", y), I & /*y, h*/
      40 && h !== (h = /*y*/
      w[3] + /*h*/
      w[5] / 2) && a(g, "cy", h), I & /*w*/
      16 && k !== (k = /*w*/
      w[4] / 2) && a(g, "rx", k), I & /*h*/
      32 && D !== (D = /*h*/
      w[5] / 2) && a(g, "ry", D), I & /*x, w*/
      20 && _ !== (_ = /*x*/
      w[2] + /*w*/
      w[4] / 2) && a(N, "cx", _), I & /*y, h*/
      40 && P !== (P = /*y*/
      w[3] + /*h*/
      w[5] / 2) && a(N, "cy", P), I & /*w*/
      16 && S !== (S = /*w*/
      w[4] / 2) && a(N, "rx", S), I & /*h*/
      32 && H2 !== (H2 = /*h*/
      w[5] / 2) && a(N, "ry", H2);
    },
    d(w) {
      w && (B(n), B(g), B(N));
    }
  };
}
function mi2(e) {
  let n, t = (
    /*origin*/
    e[1] && To2(e)
  );
  return {
    c() {
      n = V2("g"), t && t.c(), a(n, "class", "a9s-annotation a9s-rubberband");
    },
    m(o, s) {
      F(o, n, s), t && t.m(n, null), e[12](n);
    },
    p(o, [s]) {
      o[1] ? t ? t.p(o, s) : (t = To2(o), t.c(), t.m(n, null)) : t && (t.d(1), t = null);
    },
    i: Le2,
    o: Le2,
    d(o) {
      o && B(n), t && t.d(), e[12](null);
    }
  };
}
function gi2(e, n, t) {
  let o;
  const s = on();
  let { addEventListener: l } = n, { drawingMode: r } = n, { transform: i } = n, { viewportScale: c2 } = n, f, p, u, d, g, y, h, k = false, D = false, N, _;
  const P = (L) => {
    const X = L;
    N = performance.now(), r === "drag" && (t(1, p = i.elementToImage(X.offsetX, X.offsetY)), u = p, t(2, d = p[0]), t(3, g = p[1]), t(4, y = 1), t(5, h = 1));
  }, S = (L) => {
    const X = L || _;
    if (p)
      if (u = i.elementToImage(X.offsetX, X.offsetY), D) {
        const b = 2 * Math.abs(u[0] - p[0]), v = 2 * Math.abs(u[1] - p[1]);
        t(4, y = k ? Math.max(b, v) : b), t(5, h = k ? y : v), t(2, d = Math.min(u[0], p[0] - y / 2)), t(3, g = Math.min(u[1], p[1] - h / 2));
      } else {
        const b = Math.abs(u[0] - p[0]), v = Math.abs(u[1] - p[1]);
        t(4, y = k ? Math.max(b, v) : b), t(5, h = k ? y : v), t(2, d = Math.min(u[0], p[0])), t(3, g = Math.min(u[1], p[1]));
      }
    L && (_ = L);
  }, H2 = (L) => {
    const X = L, b = performance.now() - N;
    if (r === "click") {
      if (b > 300)
        return;
      p ? w() : (t(1, p = i.elementToImage(X.offsetX, X.offsetY)), u = p, t(2, d = p[0]), t(3, g = p[1]), t(4, y = 1), t(5, h = 1));
    } else
      p && (b > 300 || y * h > 100 ? (X.stopPropagation(), w()) : (t(1, p = void 0), u = void 0, _ = void 0));
  }, w = () => {
    if (y * h > 15) {
      const L = {
        type: ke2.ELLIPSE,
        geometry: {
          bounds: {
            minX: d,
            minY: g,
            maxX: d + y,
            maxY: g + h
          },
          cx: d + y / 2,
          cy: g + h / 2,
          rx: y / 2,
          ry: h / 2
        }
      };
      s("create", L);
    }
    t(1, p = void 0), u = void 0, _ = void 0;
  }, I = (L) => {
    L.key === "Shift" && (k = true, S()), L.key === "Control" && (D = true, S());
  }, M = (L) => {
    L.key === "Shift" && (k = false, S()), L.key === "Control" && (D = false, S());
  };
  bn2(() => (document.addEventListener("keyup", M), document.addEventListener("keydown", I), l("pointerdown", P), l("pointermove", S), l("pointerup", H2, true), () => {
    document.removeEventListener("keyup", M), document.removeEventListener("keydown", I);
  }));
  const j = `ellipse-mask-${Math.random().toString(36).substring(2, 12)}`;
  function U(L) {
    Bn2[L ? "unshift" : "push"](() => {
      f = L, t(0, f);
    });
  }
  return e.$$set = (L) => {
    "addEventListener" in L && t(8, l = L.addEventListener), "drawingMode" in L && t(9, r = L.drawingMode), "transform" in L && t(10, i = L.transform), "viewportScale" in L && t(11, c2 = L.viewportScale);
  }, e.$$.update = () => {
    e.$$.dirty & /*viewportScale*/
    2048 && t(6, o = 2 / c2);
  }, [
    f,
    p,
    d,
    g,
    y,
    h,
    o,
    j,
    l,
    r,
    i,
    c2,
    U
  ];
}
var _i2 = class extends Re2 {
  constructor(n) {
    super(), Ce(this, n, gi2, mi2, Ne, {
      addEventListener: 8,
      drawingMode: 9,
      transform: 10,
      viewportScale: 11
    });
  }
};
function yi2(e) {
  let n, t, o, s, l, r, i, c2, f, p, u, d, g, y, h, k, D, N;
  return g = new Ie({
    props: {
      class: "a9s-line-point-1",
      x: (
        /*geom*/
        e[5].points[0][0]
      ),
      y: (
        /*geom*/
        e[5].points[0][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), g.$on("pointerdown", function() {
    le2(
      /*grab*/
      e[10]("POINT1")
    ) && e[10]("POINT1").apply(this, arguments);
  }), h = new Ie({
    props: {
      class: "a9s-line-point-2",
      x: (
        /*geom*/
        e[5].points[1][0]
      ),
      y: (
        /*geom*/
        e[5].points[1][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), h.$on("pointerdown", function() {
    le2(
      /*grab*/
      e[10]("POINT2")
    ) && e[10]("POINT2").apply(this, arguments);
  }), {
    c() {
      n = V2("line"), r = ae2(), i = V2("line"), d = ae2(), _e2(g.$$.fragment), y = ae2(), _e2(h.$$.fragment), a(n, "class", "a9s-outer"), a(n, "x1", t = /*geom*/
      e[5].points[0][0]), a(n, "y1", o = /*geom*/
      e[5].points[0][1]), a(n, "x2", s = /*geom*/
      e[5].points[1][0]), a(n, "y2", l = /*geom*/
      e[5].points[1][1]), a(i, "class", "a9s-inner a9s-shape-handle"), a(
        i,
        "style",
        /*computedStyle*/
        e[1]
      ), a(i, "x1", c2 = /*geom*/
      e[5].points[0][0]), a(i, "y1", f = /*geom*/
      e[5].points[0][1]), a(i, "x2", p = /*geom*/
      e[5].points[1][0]), a(i, "y2", u = /*geom*/
      e[5].points[1][1]);
    },
    m(_, P) {
      F(_, n, P), F(_, r, P), F(_, i, P), F(_, d, P), he(g, _, P), F(_, y, P), he(h, _, P), k = true, D || (N = [
        ne(n, "pointerdown", function() {
          le2(
            /*grab*/
            e[10]("LINE")
          ) && e[10]("LINE").apply(this, arguments);
        }),
        ne(i, "pointerdown", function() {
          le2(
            /*grab*/
            e[10]("LINE")
          ) && e[10]("LINE").apply(this, arguments);
        })
      ], D = true);
    },
    p(_, P) {
      e = _, (!k || P & /*geom*/
      32 && t !== (t = /*geom*/
      e[5].points[0][0])) && a(n, "x1", t), (!k || P & /*geom*/
      32 && o !== (o = /*geom*/
      e[5].points[0][1])) && a(n, "y1", o), (!k || P & /*geom*/
      32 && s !== (s = /*geom*/
      e[5].points[1][0])) && a(n, "x2", s), (!k || P & /*geom*/
      32 && l !== (l = /*geom*/
      e[5].points[1][1])) && a(n, "y2", l), (!k || P & /*computedStyle*/
      2) && a(
        i,
        "style",
        /*computedStyle*/
        e[1]
      ), (!k || P & /*geom*/
      32 && c2 !== (c2 = /*geom*/
      e[5].points[0][0])) && a(i, "x1", c2), (!k || P & /*geom*/
      32 && f !== (f = /*geom*/
      e[5].points[0][1])) && a(i, "y1", f), (!k || P & /*geom*/
      32 && p !== (p = /*geom*/
      e[5].points[1][0])) && a(i, "x2", p), (!k || P & /*geom*/
      32 && u !== (u = /*geom*/
      e[5].points[1][1])) && a(i, "y2", u);
      const S = {};
      P & /*geom*/
      32 && (S.x = /*geom*/
      e[5].points[0][0]), P & /*geom*/
      32 && (S.y = /*geom*/
      e[5].points[0][1]), P & /*viewportScale*/
      8 && (S.scale = /*viewportScale*/
      e[3]), g.$set(S);
      const H2 = {};
      P & /*geom*/
      32 && (H2.x = /*geom*/
      e[5].points[1][0]), P & /*geom*/
      32 && (H2.y = /*geom*/
      e[5].points[1][1]), P & /*viewportScale*/
      8 && (H2.scale = /*viewportScale*/
      e[3]), h.$set(H2);
    },
    i(_) {
      k || (W(g.$$.fragment, _), W(h.$$.fragment, _), k = true);
    },
    o(_) {
      J(g.$$.fragment, _), J(h.$$.fragment, _), k = false;
    },
    d(_) {
      _ && (B(n), B(r), B(i), B(d), B(y)), de2(g, _), de2(h, _), D = false, Te2(N);
    }
  };
}
function wi2(e) {
  let n, t;
  return n = new Pn2({
    props: {
      shape: (
        /*shape*/
        e[0]
      ),
      transform: (
        /*transform*/
        e[2]
      ),
      editor: (
        /*editor*/
        e[6]
      ),
      svgEl: (
        /*svgEl*/
        e[4]
      ),
      $$slots: {
        default: [
          yi2,
          ({ grab: o }) => ({ 10: o }),
          ({ grab: o }) => o ? 1024 : 0
        ]
      },
      $$scope: { ctx: e }
    }
  }), n.$on(
    "grab",
    /*grab_handler*/
    e[7]
  ), n.$on(
    "change",
    /*change_handler*/
    e[8]
  ), n.$on(
    "release",
    /*release_handler*/
    e[9]
  ), {
    c() {
      _e2(n.$$.fragment);
    },
    m(o, s) {
      he(n, o, s), t = true;
    },
    p(o, [s]) {
      const l = {};
      s & /*shape*/
      1 && (l.shape = /*shape*/
      o[0]), s & /*transform*/
      4 && (l.transform = /*transform*/
      o[2]), s & /*svgEl*/
      16 && (l.svgEl = /*svgEl*/
      o[4]), s & /*$$scope, geom, viewportScale, grab, computedStyle*/
      3114 && (l.$$scope = { dirty: s, ctx: o }), n.$set(l);
    },
    i(o) {
      t || (W(n.$$.fragment, o), t = true);
    },
    o(o) {
      J(n.$$.fragment, o), t = false;
    },
    d(o) {
      de2(n, o);
    }
  };
}
function vi2(e, n, t) {
  let o, { shape: s } = n, { computedStyle: l } = n, { transform: r } = n, { viewportScale: i = 1 } = n, { svgEl: c2 } = n;
  const f = (g, y, h) => {
    const k = g.geometry;
    let [[D, N], [_, P]] = k.points;
    const [S, H2] = h;
    if (y === "LINE")
      D += S, _ += S, N += H2, P += H2;
    else
      switch (y) {
        case "POINT1": {
          D += S, N += H2;
          break;
        }
        case "POINT2": {
          _ += S, P += H2;
          break;
        }
      }
    return {
      ...g,
      geometry: {
        ...g.geometry,
        points: [[D, N], [_, P]],
        bounds: Se2([[D, N], [_, P]])
      }
    };
  };
  function p(g) {
    ce2.call(this, e, g);
  }
  function u(g) {
    ce2.call(this, e, g);
  }
  function d(g) {
    ce2.call(this, e, g);
  }
  return e.$$set = (g) => {
    "shape" in g && t(0, s = g.shape), "computedStyle" in g && t(1, l = g.computedStyle), "transform" in g && t(2, r = g.transform), "viewportScale" in g && t(3, i = g.viewportScale), "svgEl" in g && t(4, c2 = g.svgEl);
  }, e.$$.update = () => {
    e.$$.dirty & /*shape*/
    1 && t(5, o = s.geometry);
  }, [
    s,
    l,
    r,
    i,
    c2,
    o,
    f,
    p,
    u,
    d
  ];
}
var Ei2 = class extends Re2 {
  constructor(n) {
    super(), Ce(this, n, vi2, wi2, Ne, {
      shape: 0,
      computedStyle: 1,
      transform: 2,
      viewportScale: 3,
      svgEl: 4
    });
  }
};
function Mo2(e) {
  let n, t;
  return {
    c() {
      n = V2("line"), t = V2("line"), a(n, "class", "a9s-outer"), a(
        n,
        "x1",
        /*x1*/
        e[3]
      ), a(
        n,
        "y1",
        /*y1*/
        e[4]
      ), a(
        n,
        "x2",
        /*x2*/
        e[5]
      ), a(
        n,
        "y2",
        /*y2*/
        e[6]
      ), a(t, "class", "a9s-inner"), a(
        t,
        "x1",
        /*x1*/
        e[3]
      ), a(
        t,
        "y1",
        /*y1*/
        e[4]
      ), a(
        t,
        "x2",
        /*x2*/
        e[5]
      ), a(
        t,
        "y2",
        /*y2*/
        e[6]
      );
    },
    m(o, s) {
      F(o, n, s), F(o, t, s);
    },
    p(o, s) {
      s & /*x1*/
      8 && a(
        n,
        "x1",
        /*x1*/
        o[3]
      ), s & /*y1*/
      16 && a(
        n,
        "y1",
        /*y1*/
        o[4]
      ), s & /*x2*/
      32 && a(
        n,
        "x2",
        /*x2*/
        o[5]
      ), s & /*y2*/
      64 && a(
        n,
        "y2",
        /*y2*/
        o[6]
      ), s & /*x1*/
      8 && a(
        t,
        "x1",
        /*x1*/
        o[3]
      ), s & /*y1*/
      16 && a(
        t,
        "y1",
        /*y1*/
        o[4]
      ), s & /*x2*/
      32 && a(
        t,
        "x2",
        /*x2*/
        o[5]
      ), s & /*y2*/
      64 && a(
        t,
        "y2",
        /*y2*/
        o[6]
      );
    },
    d(o) {
      o && (B(n), B(t));
    }
  };
}
function bi2(e) {
  let n, t = (
    /*origin*/
    e[1] && /*cursor*/
    e[2] && Mo2(e)
  );
  return {
    c() {
      n = V2("g"), t && t.c(), a(n, "class", "a9s-annotation a9s-rubberband");
    },
    m(o, s) {
      F(o, n, s), t && t.m(n, null), e[11](n);
    },
    p(o, [s]) {
      o[1] && /*cursor*/
      o[2] ? t ? t.p(o, s) : (t = Mo2(o), t.c(), t.m(n, null)) : t && (t.d(1), t = null);
    },
    i: Le2,
    o: Le2,
    d(o) {
      o && B(n), t && t.d(), e[11](null);
    }
  };
}
function ki2(e, n, t) {
  const o = on();
  let { addEventListener: s } = n, { drawingMode: l } = n, { transform: r } = n, { viewportScale: i } = n, c2, f, p, u, d, g, y, h, k;
  const D = (H2) => {
    const w = H2, { timeStamp: I, offsetX: M, offsetY: j } = w;
    h = { timeStamp: I, offsetX: M, offsetY: j }, l === "drag" && (t(1, f = r.elementToImage(w.offsetX, w.offsetY)), t(2, p = f), t(3, u = f[0]), t(4, d = f[1]), t(5, g = p[0]), t(6, y = p[1]));
  }, N = (H2) => {
    const w = H2 || k;
    f && (t(2, p = r.elementToImage(w.offsetX, w.offsetY)), t(5, g = p[0]), t(6, y = p[1])), H2 && (k = H2);
  }, _ = (H2) => {
    const w = H2;
    if (l === "click") {
      const I = performance.now() - h.timeStamp, M = dn2([h.offsetX, h.offsetY], [w.offsetX, w.offsetY]);
      if (I > 300 || M > 15)
        return;
      f ? P() : (t(1, f = r.elementToImage(w.offsetX, w.offsetY)), t(2, p = f), t(3, u = f[0]), t(4, d = f[1]), t(5, g = p[0]), t(6, y = p[1]));
    } else
      f && (w.stopPropagation(), P());
  }, P = () => {
    if (f && p && dn2(f, p) > 4) {
      const H2 = {
        type: ke2.LINE,
        geometry: {
          bounds: Se2([f, p]),
          points: [f, p]
        }
      };
      o("create", H2);
    }
    t(1, f = void 0), t(2, p = void 0), k = void 0;
  };
  bn2(() => {
    s("pointerdown", D, true), s("pointermove", N), s("pointerup", _, true);
  });
  function S(H2) {
    Bn2[H2 ? "unshift" : "push"](() => {
      c2 = H2, t(0, c2);
    });
  }
  return e.$$set = (H2) => {
    "addEventListener" in H2 && t(7, s = H2.addEventListener), "drawingMode" in H2 && t(8, l = H2.drawingMode), "transform" in H2 && t(9, r = H2.transform), "viewportScale" in H2 && t(10, i = H2.viewportScale);
  }, [
    c2,
    f,
    p,
    u,
    d,
    g,
    y,
    s,
    l,
    r,
    i,
    S
  ];
}
var Pi2 = class extends Re2 {
  constructor(n) {
    super(), Ce(this, n, ki2, bi2, Ne, {
      addEventListener: 7,
      drawingMode: 8,
      transform: 9,
      viewportScale: 10
    });
  }
};
function Lo2(e) {
  let n, t, o, s, l, r = (
    /*isClosable*/
    e[1] && Ho2(e)
  );
  return {
    c() {
      n = V2("path"), o = V2("path"), r && r.c(), l = We2(), a(n, "class", t = Ge2(`a9s-outer ${/*isClosable*/
      e[1] ? "closed" : "open"}`) + " svelte-1fo8cp4"), a(
        n,
        "d",
        /*pathString*/
        e[2]
      ), a(o, "class", s = Ge2(`a9s-inner ${/*isClosable*/
      e[1] ? "closed" : "open"}`) + " svelte-1fo8cp4"), a(
        o,
        "d",
        /*pathString*/
        e[2]
      );
    },
    m(i, c2) {
      F(i, n, c2), F(i, o, c2), r && r.m(i, c2), F(i, l, c2);
    },
    p(i, c2) {
      c2 & /*isClosable*/
      2 && t !== (t = Ge2(`a9s-outer ${/*isClosable*/
      i[1] ? "closed" : "open"}`) + " svelte-1fo8cp4") && a(n, "class", t), c2 & /*pathString*/
      4 && a(
        n,
        "d",
        /*pathString*/
        i[2]
      ), c2 & /*isClosable*/
      2 && s !== (s = Ge2(`a9s-inner ${/*isClosable*/
      i[1] ? "closed" : "open"}`) + " svelte-1fo8cp4") && a(o, "class", s), c2 & /*pathString*/
      4 && a(
        o,
        "d",
        /*pathString*/
        i[2]
      ), /*isClosable*/
      i[1] ? r ? r.p(i, c2) : (r = Ho2(i), r.c(), r.m(l.parentNode, l)) : r && (r.d(1), r = null);
    },
    d(i) {
      i && (B(n), B(o), B(l)), r && r.d(i);
    }
  };
}
function Ho2(e) {
  let n, t, o;
  return {
    c() {
      n = V2("circle"), a(n, "class", "a9s-handle svelte-1fo8cp4"), a(n, "cx", t = /*points*/
      e[0][0][0]), a(n, "cy", o = /*points*/
      e[0][0][1]), a(
        n,
        "r",
        /*handleRadius*/
        e[3]
      );
    },
    m(s, l) {
      F(s, n, l);
    },
    p(s, l) {
      l & /*points*/
      1 && t !== (t = /*points*/
      s[0][0][0]) && a(n, "cx", t), l & /*points*/
      1 && o !== (o = /*points*/
      s[0][0][1]) && a(n, "cy", o), l & /*handleRadius*/
      8 && a(
        n,
        "r",
        /*handleRadius*/
        s[3]
      );
    },
    d(s) {
      s && B(n);
    }
  };
}
function Si2(e) {
  let n, t = (
    /*pathString*/
    e[2] && Lo2(e)
  );
  return {
    c() {
      n = V2("g"), t && t.c(), a(n, "class", "a9s-annotation a9s-rubberband");
    },
    m(o, s) {
      F(o, n, s), t && t.m(n, null);
    },
    p(o, [s]) {
      o[2] ? t ? t.p(o, s) : (t = Lo2(o), t.c(), t.m(n, null)) : t && (t.d(1), t = null);
    },
    i: Le2,
    o: Le2,
    d(o) {
      o && B(n), t && t.d();
    }
  };
}
var Ti2 = 20;
var Mi2 = 1500;
function Li2(e, n, t) {
  let o, s, l;
  const r = on();
  let { addEventListener: i } = n, { drawingMode: c2 } = n, { transform: f } = n, { viewportScale: p = 1 } = n, u, d = [], g, y = false, h;
  const k = (S) => {
    const H2 = S, { timeStamp: w, offsetX: I, offsetY: M } = H2;
    if (u = { timeStamp: w, offsetX: I, offsetY: M }, c2 === "drag" && d.length === 0) {
      const j = f.elementToImage(H2.offsetX, H2.offsetY);
      d.push(j), t(8, g = j);
    }
  }, D = (S) => {
    const H2 = S;
    if (h && clearTimeout(h), d.length > 0) {
      if (t(8, g = f.elementToImage(H2.offsetX, H2.offsetY)), d.length > 2) {
        const w = dn2(g, d[0]) * p;
        t(1, y = w < Ti2);
      }
      H2.pointerType === "touch" && (h = setTimeout(
        () => {
          _();
        },
        Mi2
      ));
    }
  }, N = (S) => {
    const H2 = S;
    if (h && clearTimeout(h), c2 === "click") {
      const w = H2.timeStamp - u.timeStamp, I = dn2([u.offsetX, u.offsetY], [H2.offsetX, H2.offsetY]);
      if (w > 300 || I > 15)
        return;
      if (y)
        P();
      else if (d.length === 0) {
        const M = f.elementToImage(H2.offsetX, H2.offsetY);
        d.push(M), t(8, g = M);
      } else
        d.push(g);
    } else {
      if (d.length === 1 && dn2(d[0], g) <= 4) {
        t(0, d = []), t(8, g = void 0);
        return;
      }
      H2.stopImmediatePropagation(), y ? P() : d.push(g);
    }
  }, _ = () => {
    if (!g)
      return;
    const S = d.slice(0, -1);
    if (S.length < 2)
      return;
    const H2 = {
      type: ke2.POLYLINE,
      geometry: {
        bounds: Se2(S),
        points: S.map((w) => ({ type: "CORNER", point: w }))
      }
    };
    t(0, d = []), t(8, g = void 0), r("create", H2);
  }, P = () => {
    const S = {
      type: ke2.POLYLINE,
      geometry: {
        bounds: Se2(d),
        points: d.map((H2) => ({ type: "CORNER", point: H2 })),
        closed: true
      }
    };
    t(0, d = []), t(8, g = void 0), r("create", S);
  };
  return bn2(() => {
    i("pointerdown", k, true), i("pointermove", D), i("pointerup", N, true), i("dblclick", _, true);
  }), e.$$set = (S) => {
    "addEventListener" in S && t(4, i = S.addEventListener), "drawingMode" in S && t(5, c2 = S.drawingMode), "transform" in S && t(6, f = S.transform), "viewportScale" in S && t(7, p = S.viewportScale);
  }, e.$$.update = () => {
    e.$$.dirty & /*viewportScale*/
    128 && t(3, o = 4 / p), e.$$.dirty & /*cursor, isClosable, points*/
    259 && t(9, s = g ? y ? d : [...d, g] : []), e.$$.dirty & /*coords, isClosable*/
    514 && t(2, l = s.length > 0 ? `M ${s[0][0]},${s[0][1]}` + s.slice(1).map(([S, H2]) => ` L ${S},${H2}`).join("") + (y ? " Z" : "") : "");
  }, [
    d,
    y,
    l,
    o,
    i,
    c2,
    f,
    p,
    g,
    s
  ];
}
var Hi2 = class extends Re2 {
  constructor(n) {
    super(), Ce(this, n, Li2, Si2, Ne, {
      addEventListener: 4,
      drawingMode: 5,
      transform: 6,
      viewportScale: 7
    });
  }
};
var Oi2 = (e, n, t) => {
  const { points: o, closed: s } = e.geometry, l = o[n], r = n === 0 ? s ? o.length - 1 : null : n - 1, i = n === o.length - 1 ? s ? 0 : null : n + 1;
  let c2 = 0, f = 0;
  if (r !== null && i !== null) {
    const u = o[r].point, d = o[i].point;
    c2 = d[0] - u[0], f = d[1] - u[1];
  } else if (r !== null) {
    const u = o[r].point;
    c2 = l.point[0] - u[0], f = l.point[1] - u[1];
  } else if (i !== null) {
    const u = o[i].point;
    c2 = u[0] - l.point[0], f = u[1] - l.point[1];
  }
  const p = Math.sqrt(c2 * c2 + f * f);
  if (p > 0) {
    const u = Math.min(0.3 * p, 100 / t);
    c2 = c2 / p * u, f = f / p * u;
  }
  return [c2, f];
};
var Ii2 = (e, n) => {
  if (e.type === "CORNER" && n.type === "CORNER")
    return [
      (e.point[0] + n.point[0]) / 2,
      (e.point[1] + n.point[1]) / 2
    ];
  const t = e.type === "CORNER" ? e.point : e.outHandle || e.point, o = n.type === "CORNER" ? n.point : n.inHandle || n.point, s = 0.5, l = 1 - s;
  return [
    l * l * l * e.point[0] + 3 * l * l * s * t[0] + 3 * l * s * s * o[0] + s * s * s * n.point[0],
    l * l * l * e.point[1] + 3 * l * l * s * t[1] + 3 * l * s * s * o[1] + s * s * s * n.point[1]
  ];
};
var Ni2 = (e, n, t) => {
  const o = e.geometry.points[n];
  if (o.type === "CORNER") {
    const [s, l] = Oi2(e, n, t), r = o.point, i = o.inHandle || [
      r[0] - s,
      r[1] - l
    ], c2 = o.outHandle || [
      r[0] + s,
      r[1] + l
    ];
    return {
      ...e,
      geometry: {
        ...e.geometry,
        points: e.geometry.points.map((f, p) => p === n ? {
          ...f,
          type: "CURVE",
          inHandle: i,
          outHandle: c2,
          locked: true
        } : f)
      }
    };
  } else
    return {
      ...e,
      geometry: {
        ...e.geometry,
        points: e.geometry.points.map((s, l) => l === n ? {
          ...s,
          type: "CORNER"
        } : s)
      }
    };
};
function Ci2(e) {
  let n, t, o, s, l;
  return {
    c() {
      n = V2("circle"), a(n, "class", "a9s-tangent-handle svelte-103tdqm"), a(n, "cx", t = /*controlPoint*/
      e[1][0]), a(n, "cy", o = /*controlPoint*/
      e[1][1]), a(
        n,
        "r",
        /*handleRadius*/
        e[3]
      );
    },
    m(r, i) {
      F(r, n, i), s || (l = ne(
        n,
        "pointerdown",
        /*pointerdown_handler_1*/
        e[8]
      ), s = true);
    },
    p(r, i) {
      i & /*controlPoint*/
      2 && t !== (t = /*controlPoint*/
      r[1][0]) && a(n, "cx", t), i & /*controlPoint*/
      2 && o !== (o = /*controlPoint*/
      r[1][1]) && a(n, "cy", o), i & /*handleRadius*/
      8 && a(
        n,
        "r",
        /*handleRadius*/
        r[3]
      );
    },
    d(r) {
      r && B(n), s = false, l();
    }
  };
}
function Ri2(e) {
  let n, t, o, s, l, r, i, c2, f;
  return {
    c() {
      n = V2("circle"), l = V2("circle"), a(n, "cx", t = /*controlPoint*/
      e[1][0]), a(n, "cy", o = /*controlPoint*/
      e[1][1]), a(n, "class", "a9s-tangent-handle-buffer svelte-103tdqm"), a(n, "r", s = 3 * /*handleRadius*/
      e[3]), Gn2(
        n,
        "touched",
        /*touched*/
        e[2]
      ), a(l, "class", "a9s-tangent-handle pointer-none svelte-103tdqm"), a(l, "cx", r = /*controlPoint*/
      e[1][0]), a(l, "cy", i = /*controlPoint*/
      e[1][1]), a(
        l,
        "r",
        /*handleRadius*/
        e[3]
      );
    },
    m(p, u) {
      F(p, n, u), F(p, l, u), c2 || (f = [
        ne(
          n,
          "pointerdown",
          /*pointerdown_handler*/
          e[7]
        ),
        ne(
          n,
          "pointerdown",
          /*onPointerDown*/
          e[4]
        ),
        ne(
          n,
          "pointerup",
          /*onPointerUp*/
          e[5]
        )
      ], c2 = true);
    },
    p(p, u) {
      u & /*controlPoint*/
      2 && t !== (t = /*controlPoint*/
      p[1][0]) && a(n, "cx", t), u & /*controlPoint*/
      2 && o !== (o = /*controlPoint*/
      p[1][1]) && a(n, "cy", o), u & /*handleRadius*/
      8 && s !== (s = 3 * /*handleRadius*/
      p[3]) && a(n, "r", s), u & /*touched*/
      4 && Gn2(
        n,
        "touched",
        /*touched*/
        p[2]
      ), u & /*controlPoint*/
      2 && r !== (r = /*controlPoint*/
      p[1][0]) && a(l, "cx", r), u & /*controlPoint*/
      2 && i !== (i = /*controlPoint*/
      p[1][1]) && a(l, "cy", i), u & /*handleRadius*/
      8 && a(
        l,
        "r",
        /*handleRadius*/
        p[3]
      );
    },
    d(p) {
      p && (B(n), B(l)), c2 = false, Te2(f);
    }
  };
}
function Ai2(e) {
  let n, t, o, s, l, r, i, c2, f, p, u;
  function d(h, k) {
    return Be2 ? Ri2 : Ci2;
  }
  let y = d()(e);
  return {
    c() {
      n = V2("g"), t = V2("line"), i = V2("line"), y.c(), a(t, "class", "a9s-tangent-line a9s-outer svelte-103tdqm"), a(t, "x1", o = /*corner*/
      e[0][0]), a(t, "y1", s = /*corner*/
      e[0][1]), a(t, "x2", l = /*controlPoint*/
      e[1][0]), a(t, "y2", r = /*controlPoint*/
      e[1][1]), a(i, "class", "a9s-tangent-line a9s-inner svelte-103tdqm"), a(i, "x1", c2 = /*corner*/
      e[0][0]), a(i, "y1", f = /*corner*/
      e[0][1]), a(i, "x2", p = /*controlPoint*/
      e[1][0]), a(i, "y2", u = /*controlPoint*/
      e[1][1]), a(n, "class", "a9s-bezier-handle svelte-103tdqm");
    },
    m(h, k) {
      F(h, n, k), se2(n, t), se2(n, i), y.m(n, null);
    },
    p(h, [k]) {
      k & /*corner*/
      1 && o !== (o = /*corner*/
      h[0][0]) && a(t, "x1", o), k & /*corner*/
      1 && s !== (s = /*corner*/
      h[0][1]) && a(t, "y1", s), k & /*controlPoint*/
      2 && l !== (l = /*controlPoint*/
      h[1][0]) && a(t, "x2", l), k & /*controlPoint*/
      2 && r !== (r = /*controlPoint*/
      h[1][1]) && a(t, "y2", r), k & /*corner*/
      1 && c2 !== (c2 = /*corner*/
      h[0][0]) && a(i, "x1", c2), k & /*corner*/
      1 && f !== (f = /*corner*/
      h[0][1]) && a(i, "y1", f), k & /*controlPoint*/
      2 && p !== (p = /*controlPoint*/
      h[1][0]) && a(i, "x2", p), k & /*controlPoint*/
      2 && u !== (u = /*controlPoint*/
      h[1][1]) && a(i, "y2", u), y.p(h, k);
    },
    i: Le2,
    o: Le2,
    d(h) {
      h && B(n), y.d();
    }
  };
}
function Di2(e, n, t) {
  let o, { corner: s } = n, { controlPoint: l } = n, { viewportScale: r = 1 } = n, i = false;
  const c2 = (d) => {
    d.pointerType === "touch" && t(2, i = true);
  }, f = () => t(2, i = false);
  function p(d) {
    ce2.call(this, e, d);
  }
  function u(d) {
    ce2.call(this, e, d);
  }
  return e.$$set = (d) => {
    "corner" in d && t(0, s = d.corner), "controlPoint" in d && t(1, l = d.controlPoint), "viewportScale" in d && t(6, r = d.viewportScale);
  }, e.$$.update = () => {
    e.$$.dirty & /*viewportScale*/
    64 && t(3, o = 4 / r);
  }, [
    s,
    l,
    i,
    o,
    c2,
    f,
    r,
    p,
    u
  ];
}
var os2 = class extends Re2 {
  constructor(n) {
    super(), Ce(this, n, Di2, Ai2, Ne, {
      corner: 0,
      controlPoint: 1,
      viewportScale: 6
    });
  }
};
function ct(e) {
  const n = e.slice(), t = (
    /*midpoints*/
    n[11][
      /*visibleMidpoint*/
      n[6]
    ]
  );
  return n[31] = t.point, n;
}
function Oo2(e, n, t) {
  const o = e.slice();
  return o[32] = n[t], o[34] = t, o;
}
function ft(e) {
  const n = e.slice(), t = (
    /*selectedCorners*/
    n[8][0]
  );
  n[35] = t;
  const o = (
    /*geom*/
    n[5].points[
      /*selectedCorner*/
      n[35]
    ]
  );
  return n[36] = o, n;
}
function ut(e) {
  const n = e.slice(), t = (
    /*midpoints*/
    n[11][
      /*visibleMidpoint*/
      n[6]
    ]
  );
  return n[31] = t.point, n;
}
function Io2(e) {
  let n, t, o, s, l, r, i, c2, f, p;
  return {
    c() {
      n = V2("mask"), t = V2("rect"), i = V2("circle"), a(t, "x", o = /*mask*/
      e[9].x), a(t, "y", s = /*mask*/
      e[9].y), a(t, "width", l = /*mask*/
      e[9].w), a(t, "height", r = /*mask*/
      e[9].h), a(t, "class", "svelte-sq8d4p"), a(i, "cx", c2 = /*point*/
      e[31][0]), a(i, "cy", f = /*point*/
      e[31][1]), a(i, "r", p = vt2 / /*viewportScale*/
      e[3]), a(i, "class", "svelte-sq8d4p"), a(
        n,
        "id",
        /*maskId*/
        e[20]
      ), a(n, "class", "a9s-polygon-editor-mask svelte-sq8d4p");
    },
    m(u, d) {
      F(u, n, d), se2(n, t), se2(n, i);
    },
    p(u, d) {
      d[0] & /*mask*/
      512 && o !== (o = /*mask*/
      u[9].x) && a(t, "x", o), d[0] & /*mask*/
      512 && s !== (s = /*mask*/
      u[9].y) && a(t, "y", s), d[0] & /*mask*/
      512 && l !== (l = /*mask*/
      u[9].w) && a(t, "width", l), d[0] & /*mask*/
      512 && r !== (r = /*mask*/
      u[9].h) && a(t, "height", r), d[0] & /*midpoints, visibleMidpoint*/
      2112 && c2 !== (c2 = /*point*/
      u[31][0]) && a(i, "cx", c2), d[0] & /*midpoints, visibleMidpoint*/
      2112 && f !== (f = /*point*/
      u[31][1]) && a(i, "cy", f), d[0] & /*viewportScale*/
      8 && p !== (p = vt2 / /*viewportScale*/
      u[3]) && a(i, "r", p);
    },
    d(u) {
      u && B(n);
    }
  };
}
function No2(e) {
  let n, t, o = (
    /*corner*/
    e[36].type === "CURVE" && Co2(e)
  );
  return {
    c() {
      o && o.c(), n = We2();
    },
    m(s, l) {
      o && o.m(s, l), F(s, n, l), t = true;
    },
    p(s, l) {
      s[36].type === "CURVE" ? o ? (o.p(s, l), l[0] & /*geom, selectedCorners*/
      288 && W(o, 1)) : (o = Co2(s), o.c(), W(o, 1), o.m(n.parentNode, n)) : o && (Ue(), J(o, 1, 1, () => {
        o = null;
      }), Xe2());
    },
    i(s) {
      t || (W(o), t = true);
    },
    o(s) {
      J(o), t = false;
    },
    d(s) {
      s && B(n), o && o.d(s);
    }
  };
}
function Co2(e) {
  let n, t, o, s = (
    /*corner*/
    e[36].inHandle && Ro2(e)
  ), l = (
    /*corner*/
    e[36].outHandle && Ao2(e)
  );
  return {
    c() {
      s && s.c(), n = ae2(), l && l.c(), t = We2();
    },
    m(r, i) {
      s && s.m(r, i), F(r, n, i), l && l.m(r, i), F(r, t, i), o = true;
    },
    p(r, i) {
      r[36].inHandle ? s ? (s.p(r, i), i[0] & /*geom, selectedCorners*/
      288 && W(s, 1)) : (s = Ro2(r), s.c(), W(s, 1), s.m(n.parentNode, n)) : s && (Ue(), J(s, 1, 1, () => {
        s = null;
      }), Xe2()), /*corner*/
      r[36].outHandle ? l ? (l.p(r, i), i[0] & /*geom, selectedCorners*/
      288 && W(l, 1)) : (l = Ao2(r), l.c(), W(l, 1), l.m(t.parentNode, t)) : l && (Ue(), J(l, 1, 1, () => {
        l = null;
      }), Xe2());
    },
    i(r) {
      o || (W(s), W(l), o = true);
    },
    o(r) {
      J(s), J(l), o = false;
    },
    d(r) {
      r && (B(n), B(t)), s && s.d(r), l && l.d(r);
    }
  };
}
function Ro2(e) {
  let n, t;
  return n = new os2({
    props: {
      corner: (
        /*corner*/
        e[36].point
      ),
      controlPoint: (
        /*corner*/
        e[36].inHandle
      ),
      viewportScale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), n.$on("pointerdown", function() {
    le2(
      /*grab*/
      e[30](`IN-${/*selectedCorner*/
      e[35]}`)
    ) && e[30](`IN-${/*selectedCorner*/
    e[35]}`).apply(this, arguments);
  }), {
    c() {
      _e2(n.$$.fragment);
    },
    m(o, s) {
      he(n, o, s), t = true;
    },
    p(o, s) {
      e = o;
      const l = {};
      s[0] & /*geom, selectedCorners*/
      288 && (l.corner = /*corner*/
      e[36].point), s[0] & /*geom, selectedCorners*/
      288 && (l.controlPoint = /*corner*/
      e[36].inHandle), s[0] & /*viewportScale*/
      8 && (l.viewportScale = /*viewportScale*/
      e[3]), n.$set(l);
    },
    i(o) {
      t || (W(n.$$.fragment, o), t = true);
    },
    o(o) {
      J(n.$$.fragment, o), t = false;
    },
    d(o) {
      de2(n, o);
    }
  };
}
function Ao2(e) {
  let n, t;
  return n = new os2({
    props: {
      corner: (
        /*corner*/
        e[36].point
      ),
      controlPoint: (
        /*corner*/
        e[36].outHandle
      ),
      viewportScale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), n.$on("pointerdown", function() {
    le2(
      /*grab*/
      e[30](`OUT-${/*selectedCorner*/
      e[35]}`)
    ) && e[30](`OUT-${/*selectedCorner*/
    e[35]}`).apply(this, arguments);
  }), {
    c() {
      _e2(n.$$.fragment);
    },
    m(o, s) {
      he(n, o, s), t = true;
    },
    p(o, s) {
      e = o;
      const l = {};
      s[0] & /*geom, selectedCorners*/
      288 && (l.corner = /*corner*/
      e[36].point), s[0] & /*geom, selectedCorners*/
      288 && (l.controlPoint = /*corner*/
      e[36].outHandle), s[0] & /*viewportScale*/
      8 && (l.viewportScale = /*viewportScale*/
      e[3]), n.$set(l);
    },
    i(o) {
      t || (W(n.$$.fragment, o), t = true);
    },
    o(o) {
      J(n.$$.fragment, o), t = false;
    },
    d(o) {
      de2(n, o);
    }
  };
}
function Do2(e) {
  let n, t;
  return n = new Ie({
    props: {
      class: "a9s-corner-handle",
      x: (
        /*pt*/
        e[32].point[0]
      ),
      y: (
        /*pt*/
        e[32].point[1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      ),
      selected: (
        /*selectedCorners*/
        e[8].includes(
          /*idx*/
          e[34]
        )
      )
    }
  }), n.$on(
    "dblclick",
    /*onDoubleClick*/
    e[17](
      /*idx*/
      e[34]
    )
  ), n.$on(
    "pointerenter",
    /*onEnterHandle*/
    e[12]
  ), n.$on(
    "pointerleave",
    /*onLeaveHandle*/
    e[13]
  ), n.$on(
    "pointerdown",
    /*onHandlePointerDown*/
    e[15]
  ), n.$on("pointerdown", function() {
    le2(
      /*grab*/
      e[30](`CORNER-${/*idx*/
      e[34]}`)
    ) && e[30](`CORNER-${/*idx*/
    e[34]}`).apply(this, arguments);
  }), n.$on(
    "pointerup",
    /*onHandlePointerUp*/
    e[16](
      /*idx*/
      e[34]
    )
  ), {
    c() {
      _e2(n.$$.fragment);
    },
    m(o, s) {
      he(n, o, s), t = true;
    },
    p(o, s) {
      e = o;
      const l = {};
      s[0] & /*geom*/
      32 && (l.x = /*pt*/
      e[32].point[0]), s[0] & /*geom*/
      32 && (l.y = /*pt*/
      e[32].point[1]), s[0] & /*viewportScale*/
      8 && (l.scale = /*viewportScale*/
      e[3]), s[0] & /*selectedCorners*/
      256 && (l.selected = /*selectedCorners*/
      e[8].includes(
        /*idx*/
        e[34]
      )), n.$set(l);
    },
    i(o) {
      t || (W(n.$$.fragment, o), t = true);
    },
    o(o) {
      J(n.$$.fragment, o), t = false;
    },
    d(o) {
      de2(n, o);
    }
  };
}
function Uo2(e) {
  let n, t;
  return n = new St2({
    props: {
      x: (
        /*point*/
        e[31][0]
      ),
      y: (
        /*point*/
        e[31][1]
      ),
      scale: (
        /*viewportScale*/
        e[3]
      )
    }
  }), n.$on("pointerdown", function() {
    le2(
      /*onAddPoint*/
      e[19](
        /*visibleMidpoint*/
        e[6]
      )
    ) && e[19](
      /*visibleMidpoint*/
      e[6]
    ).apply(this, arguments);
  }), {
    c() {
      _e2(n.$$.fragment);
    },
    m(o, s) {
      he(n, o, s), t = true;
    },
    p(o, s) {
      e = o;
      const l = {};
      s[0] & /*midpoints, visibleMidpoint*/
      2112 && (l.x = /*point*/
      e[31][0]), s[0] & /*midpoints, visibleMidpoint*/
      2112 && (l.y = /*point*/
      e[31][1]), s[0] & /*viewportScale*/
      8 && (l.scale = /*viewportScale*/
      e[3]), n.$set(l);
    },
    i(o) {
      t || (W(n.$$.fragment, o), t = true);
    },
    o(o) {
      J(n.$$.fragment, o), t = false;
    },
    d(o) {
      de2(n, o);
    }
  };
}
function Ui2(e) {
  let n, t, o, s, l, r, i, c2, f, p, u, d, g, y, h, k, D, N, _, P = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && Io2(ut(e))
  ), S = (
    /*selectedCorners*/
    e[8].length === 1 && No2(ft(e))
  ), H2 = Ve2(
    /*geom*/
    e[5].points
  ), w = [];
  for (let j = 0; j < H2.length; j += 1)
    w[j] = Do2(Oo2(e, H2, j));
  const I = (j) => J(w[j], 1, 1, () => {
    w[j] = null;
  });
  let M = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && Uo2(ct(e))
  );
  return {
    c() {
      n = V2("defs"), P && P.c(), t = ae2(), o = V2("g"), s = V2("rect"), f = V2("path"), u = V2("path"), g = ae2(), S && S.c(), y = ae2();
      for (let j = 0; j < w.length; j += 1)
        w[j].c();
      h = ae2(), M && M.c(), k = We2(), a(s, "x", l = /*mask*/
      e[9].x), a(s, "y", r = /*mask*/
      e[9].y), a(s, "width", i = /*mask*/
      e[9].w), a(s, "height", c2 = /*mask*/
      e[9].h), a(s, "class", "mask-buffer svelte-sq8d4p"), a(f, "class", p = Ge2(`a9s-outer polyline ${/*shape*/
      e[0].geometry.closed ? "closed" : "open"}`) + " svelte-sq8d4p"), a(
        f,
        "d",
        /*d*/
        e[10]
      ), a(u, "class", d = Ge2(`a9s-inner polyline a9s-shape-handle ${/*shape*/
      e[0].geometry.closed ? "closed" : "open"}`) + " svelte-sq8d4p"), a(
        u,
        "style",
        /*computedStyle*/
        e[1]
      ), a(
        u,
        "d",
        /*d*/
        e[10]
      ), a(o, "mask", `url(#${/*maskId*/
      e[20]})`);
    },
    m(j, U) {
      F(j, n, U), P && P.m(n, null), F(j, t, U), F(j, o, U), se2(o, s), se2(o, f), se2(o, u), F(j, g, U), S && S.m(j, U), F(j, y, U);
      for (let L = 0; L < w.length; L += 1)
        w[L] && w[L].m(j, U);
      F(j, h, U), M && M.m(j, U), F(j, k, U), D = true, N || (_ = [
        ne(
          f,
          "pointerup",
          /*onShapePointerUp*/
          e[14]
        ),
        ne(f, "pointerdown", function() {
          le2(
            /*grab*/
            e[30]("SHAPE")
          ) && e[30]("SHAPE").apply(this, arguments);
        }),
        ne(
          u,
          "pointerup",
          /*onShapePointerUp*/
          e[14]
        ),
        ne(u, "pointerdown", function() {
          le2(
            /*grab*/
            e[30]("SHAPE")
          ) && e[30]("SHAPE").apply(this, arguments);
        })
      ], N = true);
    },
    p(j, U) {
      if (e = j, /*visibleMidpoint*/
      e[6] !== void 0 && !/*isHandleHovered*/
      e[7] ? P ? P.p(ut(e), U) : (P = Io2(ut(e)), P.c(), P.m(n, null)) : P && (P.d(1), P = null), (!D || U[0] & /*mask*/
      512 && l !== (l = /*mask*/
      e[9].x)) && a(s, "x", l), (!D || U[0] & /*mask*/
      512 && r !== (r = /*mask*/
      e[9].y)) && a(s, "y", r), (!D || U[0] & /*mask*/
      512 && i !== (i = /*mask*/
      e[9].w)) && a(s, "width", i), (!D || U[0] & /*mask*/
      512 && c2 !== (c2 = /*mask*/
      e[9].h)) && a(s, "height", c2), (!D || U[0] & /*shape*/
      1 && p !== (p = Ge2(`a9s-outer polyline ${/*shape*/
      e[0].geometry.closed ? "closed" : "open"}`) + " svelte-sq8d4p")) && a(f, "class", p), (!D || U[0] & /*d*/
      1024) && a(
        f,
        "d",
        /*d*/
        e[10]
      ), (!D || U[0] & /*shape*/
      1 && d !== (d = Ge2(`a9s-inner polyline a9s-shape-handle ${/*shape*/
      e[0].geometry.closed ? "closed" : "open"}`) + " svelte-sq8d4p")) && a(u, "class", d), (!D || U[0] & /*computedStyle*/
      2) && a(
        u,
        "style",
        /*computedStyle*/
        e[1]
      ), (!D || U[0] & /*d*/
      1024) && a(
        u,
        "d",
        /*d*/
        e[10]
      ), /*selectedCorners*/
      e[8].length === 1 ? S ? (S.p(ft(e), U), U[0] & /*selectedCorners*/
      256 && W(S, 1)) : (S = No2(ft(e)), S.c(), W(S, 1), S.m(y.parentNode, y)) : S && (Ue(), J(S, 1, 1, () => {
        S = null;
      }), Xe2()), U[0] & /*geom, viewportScale, selectedCorners, onDoubleClick, onEnterHandle, onLeaveHandle, onHandlePointerDown, grab, onHandlePointerUp*/
      1073983784) {
        H2 = Ve2(
          /*geom*/
          e[5].points
        );
        let L;
        for (L = 0; L < H2.length; L += 1) {
          const X = Oo2(e, H2, L);
          w[L] ? (w[L].p(X, U), W(w[L], 1)) : (w[L] = Do2(X), w[L].c(), W(w[L], 1), w[L].m(h.parentNode, h));
        }
        for (Ue(), L = H2.length; L < w.length; L += 1)
          I(L);
        Xe2();
      }
      e[6] !== void 0 && !/*isHandleHovered*/
      e[7] ? M ? (M.p(ct(e), U), U[0] & /*visibleMidpoint, isHandleHovered*/
      192 && W(M, 1)) : (M = Uo2(ct(e)), M.c(), W(M, 1), M.m(k.parentNode, k)) : M && (Ue(), J(M, 1, 1, () => {
        M = null;
      }), Xe2());
    },
    i(j) {
      if (!D) {
        W(S);
        for (let U = 0; U < H2.length; U += 1)
          W(w[U]);
        W(M), D = true;
      }
    },
    o(j) {
      J(S), w = w.filter(Boolean);
      for (let U = 0; U < w.length; U += 1)
        J(w[U]);
      J(M), D = false;
    },
    d(j) {
      j && (B(n), B(t), B(o), B(g), B(y), B(h), B(k)), P && P.d(), S && S.d(j), Dn2(w, j), M && M.d(j), N = false, Te2(_);
    }
  };
}
function Xi2(e) {
  let n, t;
  return n = new Pn2({
    props: {
      shape: (
        /*shape*/
        e[0]
      ),
      transform: (
        /*transform*/
        e[2]
      ),
      editor: (
        /*editor*/
        e[18]
      ),
      svgEl: (
        /*svgEl*/
        e[4]
      ),
      $$slots: {
        default: [
          Ui2,
          ({ grab: o }) => ({ 30: o }),
          ({ grab: o }) => [o ? 1073741824 : 0]
        ]
      },
      $$scope: { ctx: e }
    }
  }), n.$on(
    "change",
    /*change_handler*/
    e[21]
  ), n.$on(
    "grab",
    /*grab_handler*/
    e[22]
  ), n.$on(
    "release",
    /*release_handler*/
    e[23]
  ), {
    c() {
      _e2(n.$$.fragment);
    },
    m(o, s) {
      he(n, o, s), t = true;
    },
    p(o, s) {
      const l = {};
      s[0] & /*shape*/
      1 && (l.shape = /*shape*/
      o[0]), s[0] & /*transform*/
      4 && (l.transform = /*transform*/
      o[2]), s[0] & /*svgEl*/
      16 && (l.svgEl = /*svgEl*/
      o[4]), s[0] & /*midpoints, visibleMidpoint, viewportScale, isHandleHovered, geom, selectedCorners, grab, shape, computedStyle, d, mask*/
      1073745899 | s[1] & /*$$scope*/
      64 && (l.$$scope = { dirty: s, ctx: o }), n.$set(l);
    },
    i(o) {
      t || (W(n.$$.fragment, o), t = true);
    },
    o(o) {
      J(n.$$.fragment, o), t = false;
    },
    d(o) {
      de2(n, o);
    }
  };
}
var ji2 = 250;
var qi2 = 1e3;
var Gi2 = 12;
var vt2 = 4.5;
function Bi2(e, n, t) {
  let o, s, l, r;
  const i = on();
  let { shape: c2 } = n, { computedStyle: f } = n, { transform: p } = n, { viewportScale: u = 1 } = n, { svgEl: d } = n, g, y = false, h = null, k = [], D = false;
  const N = () => t(7, y = true), _ = () => t(7, y = false), P = (E) => {
    if (k.length > 0 || !s.some(($2) => $2.visible)) {
      t(6, g = void 0);
      return;
    }
    const [C2, G2] = p.elementToImage(E.offsetX, E.offsetY), T = ($2) => Math.pow($2[0] - C2, 2) + Math.pow($2[1] - G2, 2), O = o.points.reduce(($2, ee) => T(ee.point) < T($2.point) ? ee : $2), q = s.filter(($2) => $2.visible).reduce(($2, ee) => T(ee.point) < T($2.point) ? ee : $2), Y2 = Math.pow(qi2 / u, 2);
    T(O.point) < Y2 || T(q.point) < Y2 ? t(6, g = s.indexOf(q)) : t(6, g = void 0);
  }, S = () => {
    document.activeElement !== d && d.focus();
  }, H2 = () => {
    t(8, k = []), S();
  }, w = (E) => {
    t(7, y = true), E.preventDefault(), E.stopPropagation(), h = performance.now();
  }, I = (E) => (C2) => {
    if (!h || performance.now() - h > ji2)
      return;
    const G2 = k.includes(E);
    if (k.length > 0 && (C2.metaKey || C2.ctrlKey || C2.shiftKey))
      G2 && k.length > 1 ? t(8, k = k.filter((T) => T !== E)) : G2 ? t(8, k = []) : t(8, k = [...k, E]);
    else {
      const T = Ni2(c2, E, u);
      i("change", T), (!G2 || k.length > 1) && t(8, k = [E]);
    }
  }, M = (E) => () => {
    const C2 = o.points[E];
    if (C2.type === "CORNER" || !C2.inHandle && !C2.outHandle)
      return;
    const G2 = o.points.map((q, Y2) => {
      if (Y2 !== E)
        return q;
      const Q2 = { ...q, locked: true }, $2 = C2.inHandle || C2.outHandle;
      if (!$2)
        return q;
      const ee = $2[0] - C2.point[0], ye2 = $2[1] - C2.point[1];
      if (Math.sqrt(ee ** 2 + ye2 ** 2) === 0)
        return q;
      const Pe2 = [C2.point[0] - ee, C2.point[1] - ye2];
      return C2.inHandle ? (Q2.inHandle = C2.inHandle, Q2.outHandle = Pe2) : C2.outHandle && (Q2.outHandle = C2.outHandle, Q2.inHandle = Pe2), Q2;
    }), T = Se2(un2(G2, o.closed)), O = {
      ...c2,
      geometry: { bounds: T, points: G2, closed: o.closed }
    };
    i("change", O);
  }, j = (E, C2, G2) => {
    const T = E.geometry, [O, q] = G2;
    let Y2;
    if (C2 === "SHAPE")
      Y2 = T.points.map(($2) => ({
        ...$2,
        point: [$2.point[0] + O, $2.point[1] + q],
        inHandle: $2.inHandle ? [$2.inHandle[0] + O, $2.inHandle[1] + q] : void 0,
        outHandle: $2.outHandle ? [$2.outHandle[0] + O, $2.outHandle[1] + q] : void 0
      }));
    else if (C2.startsWith("CORNER-")) {
      const $2 = parseInt(C2.split("-")[1]);
      Y2 = T.points.map((ee, ye2) => ye2 === $2 ? {
        ...ee,
        point: [ee.point[0] + O, ee.point[1] + q],
        inHandle: ee.inHandle ? [ee.inHandle[0] + O, ee.inHandle[1] + q] : void 0,
        outHandle: ee.outHandle ? [ee.outHandle[0] + O, ee.outHandle[1] + q] : void 0
      } : ee);
    } else if (C2.startsWith("IN-") || C2.startsWith("OUT-")) {
      const [$2, ee] = C2.split("-"), ye2 = parseInt(ee);
      Y2 = T.points.map((z, Pe2) => {
        if (Pe2 === ye2 && z.type === "CURVE") {
          const fe = D ? false : z.locked, we2 = { ...z, locked: fe };
          if ($2 === "IN" && z.inHandle) {
            if (we2.inHandle = [z.inHandle[0] + O, z.inHandle[1] + q], z.outHandle && fe) {
              const ue2 = we2.inHandle[0] - z.point[0], He2 = we2.inHandle[1] - z.point[1], pe2 = Math.sqrt(ue2 ** 2 + He2 ** 2), Oe = Math.sqrt((z.outHandle[0] - z.point[0]) ** 2 + (z.outHandle[1] - z.point[1]) ** 2);
              pe2 > 0 && (we2.outHandle = [
                z.point[0] - ue2 / pe2 * Oe,
                z.point[1] - He2 / pe2 * Oe
              ]);
            }
          } else if ($2 === "OUT" && z.outHandle && (we2.outHandle = [z.outHandle[0] + O, z.outHandle[1] + q], z.inHandle && fe)) {
            const ue2 = we2.outHandle[0] - z.point[0], He2 = we2.outHandle[1] - z.point[1], pe2 = Math.sqrt(ue2 ** 2 + He2 ** 2), Oe = Math.sqrt((z.inHandle[0] - z.point[0]) ** 2 + (z.inHandle[1] - z.point[1]) ** 2);
            pe2 > 0 && (we2.inHandle = [
              z.point[0] - ue2 / pe2 * Oe,
              z.point[1] - He2 / pe2 * Oe
            ]);
          }
          return we2;
        }
        return z;
      });
    } else
      Y2 = T.points;
    const Q2 = Se2(un2(Y2, T.closed));
    return {
      ...E,
      geometry: { bounds: Q2, points: Y2, closed: T.closed }
    };
  }, U = (E) => async (C2) => {
    C2.stopPropagation();
    const G2 = [
      ...o.points.slice(0, E + 1),
      {
        type: "CORNER",
        point: s[E].point
      },
      ...o.points.slice(E + 1)
    ], T = Se2(un2(G2, o.closed));
    i("change", {
      ...c2,
      geometry: { points: G2, bounds: T, closed: o.closed }
    }), await kt2();
    const O = [...document.querySelectorAll(".a9s-handle")][E + 1];
    if (O != null && O.firstChild) {
      const q = new PointerEvent(
        "pointerdown",
        {
          bubbles: true,
          cancelable: true,
          clientX: C2.clientX,
          clientY: C2.clientY,
          pointerId: C2.pointerId,
          pointerType: C2.pointerType,
          isPrimary: C2.isPrimary,
          buttons: C2.buttons
        }
      );
      O.firstChild.dispatchEvent(q);
    }
  }, L = () => {
    const E = o.closed ? 3 : 2;
    if (o.points.length - k.length < E)
      return;
    const C2 = o.points.filter((T, O) => !k.includes(O)), G2 = Se2(un2(C2, o.closed));
    i("change", {
      ...c2,
      geometry: {
        closed: c2.geometry.closed,
        bounds: G2,
        points: C2
      }
    }), t(8, k = []);
  };
  bn2(() => {
    const E = (G2) => {
      G2.altKey && !D && (D = true), (G2.key === "Delete" || G2.key === "Backspace") && L();
    }, C2 = (G2) => {
      !G2.altKey && D && (D = false);
    };
    return d.addEventListener("pointermove", P), window.addEventListener("keydown", E), window.addEventListener("keyup", C2), () => {
      d.removeEventListener("pointermove", P), window.removeEventListener("keydown", E), window.removeEventListener("keyup", C2);
    };
  });
  const X = `polygon-mask-${Math.random().toString(36).substring(2, 12)}`;
  function b(E) {
    ce2.call(this, e, E);
  }
  function v(E) {
    ce2.call(this, e, E);
  }
  function A(E) {
    ce2.call(this, e, E);
  }
  return e.$$set = (E) => {
    "shape" in E && t(0, c2 = E.shape), "computedStyle" in E && t(1, f = E.computedStyle), "transform" in E && t(2, p = E.transform), "viewportScale" in E && t(3, u = E.viewportScale), "svgEl" in E && t(4, d = E.svgEl);
  }, e.$$.update = () => {
    e.$$.dirty[0] & /*shape*/
    1 && t(5, o = c2.geometry), e.$$.dirty[0] & /*geom, viewportScale*/
    40 && t(11, s = Be2 ? [] : o.points.reduce(
      (E, C2, G2) => {
        const T = G2 === o.points.length - 1 ? (
          // Last point
          o.closed ? o.points[0] : void 0
        ) : o.points[G2 + 1];
        if (!T)
          return E;
        const [O, q] = Ii2(C2, T), Q2 = Math.sqrt(Math.pow(T.point[0] - O, 2) + Math.pow(T.point[1] - q, 2)) > Gi2 / u;
        return [...E, { point: [O, q], visible: Q2 }];
      },
      []
    )), e.$$.dirty[0] & /*geom*/
    32 && t(10, l = Ls2(o)), e.$$.dirty[0] & /*geom, viewportScale*/
    40 && t(9, r = An2(o.bounds, vt2 / u));
  }, [
    c2,
    f,
    p,
    u,
    d,
    o,
    g,
    y,
    k,
    r,
    l,
    s,
    N,
    _,
    H2,
    w,
    I,
    M,
    j,
    U,
    X,
    b,
    v,
    A
  ];
}
var Fi2 = class extends Re2 {
  constructor(n) {
    super(), Ce(
      this,
      n,
      Bi2,
      Xi2,
      Ne,
      {
        shape: 0,
        computedStyle: 1,
        transform: 2,
        viewportScale: 3,
        svgEl: 4
      },
      null,
      [-1, -1]
    );
  }
};
var $i2 = (e) => {
  e.registerDrawingTool("ellipse", _i2), e.registerShapeEditor(ke2.ELLIPSE, di2), e.registerDrawingTool("line", Pi2), e.registerShapeEditor(ke2.LINE, Ei2), e.registerDrawingTool("path", Hi2), e.registerShapeEditor(ke2.POLYLINE, Fi2);
};
export {
  tn as Editor,
  Hi as EditorMount,
  Xe as Handle,
  nl as IdentityTransform,
  bo as MidpointHandle,
  Ei as PolygonEditor,
  Li as RectangleEditor,
  xo as RectangleUtil,
  qi as RubberbandRectangle,
  Or as SVGAnnotationLayer,
  x as ShapeType,
  ji as ToolMount,
  po as UserSelectAction,
  tl as W3CImageFormat,
  Er as addEventListeners,
  Qt as approximateAsPolygon,
  bt as boundsFromMultiPolygonElements,
  ue as boundsFromPoints,
  $r as chainStyles,
  Jt as computeArea,
  At as computePolygonArea,
  fo as computeSVGPath,
  xr as computeStyle,
  Qr as createBody,
  ol as createImageAnnotator,
  Gr as createImageAnnotatorState,
  wr as createSVGTransform,
  jr as createSvelteImageAnnotatorState,
  el as defaultColorProvider,
  zr as detectTheme,
  wt as distance,
  oi as enableResponsive,
  qr as fillDefaults,
  Wo as getAllCorners,
  Vi as getEditor,
  kt as getMaskDimensions,
  Mo as getSVGPoint,
  So as getTool,
  Kr as initKeyboardCommands,
  jo as intersects,
  $o as isFragmentSelector,
  Mt as isImageAnnotation,
  Et as isImageAnnotationTarget,
  Qn as isMac,
  St as isPointInPolygon,
  Le as isTouch,
  Ao as listDrawingTools,
  $i2 as mountAnnotoriousTools,
  Pe as multipolygonElementToPath,
  es as parseFragmentSelector,
  ds as parseSVGSelector,
  $s as parseW3CImageAnnotation,
  Fo as pointsToPath,
  Ui as registerEditor,
  Qe as registerShapeUtil,
  xi as registerTool,
  Fr as sampleBrightness,
  ts as serializeFragmentSelector,
  gs as serializeSVGSelector,
  ei as serializeW3CImageAnnotation,
  Jn as setTheme,
  Zr as simplifyMultiPolygon,
  ao as simplifyPoints,
  Jr as simplifyPolygon,
  Hr as toSvelteStore
};
