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
  const a = nt;
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
    context: new Map(t.context || (a ? a.$$.context : [])),
    // everything else
    callbacks: sn(),
    dirty: r,
    skip_bound: false,
    root: t.target || a.$$.root
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
  tt(a);
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
    function t(r, a) {
      var f = r.x - a.x, d = r.y - a.y;
      return f * f + d * d;
    }
    function n(r, a, f) {
      var d = a.x, u = a.y, h = f.x - d, g = f.y - u;
      if (h !== 0 || g !== 0) {
        var p = ((r.x - d) * h + (r.y - u) * g) / (h * h + g * g);
        p > 1 ? (d = f.x, u = f.y) : p > 0 && (d += h * p, u += g * p);
      }
      return h = r.x - d, g = r.y - u, h * h + g * g;
    }
    function o(r, a) {
      for (var f = r[0], d = [f], u, h = 1, g = r.length; h < g; h++)
        u = r[h], t(u, f) > a && (d.push(u), f = u);
      return f !== u && d.push(u), d;
    }
    function s(r, a, f, d, u) {
      for (var h = d, g, p = a + 1; p < f; p++) {
        var y = n(r[p], r[a], r[f]);
        y > h && (g = p, h = y);
      }
      h > d && (g - a > 1 && s(r, a, g, d, u), u.push(r[g]), f - g > 1 && s(r, g, f, d, u));
    }
    function i(r, a) {
      var f = r.length - 1, d = [r[0]];
      return s(r, 0, f, a, d), d.push(r[f]), d;
    }
    function l(r, a, f) {
      if (r.length <= 2)
        return r;
      var d = a !== void 0 ? a * a : 1;
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
    const l = e[s][0], r = e[s][1], a = e[i][0], f = e[i][1];
    r > n != f > n && t < (a - l) * (n - r) / (f - r) + l && (o = !o);
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
    const { cx: o, cy: s, rx: i, ry: l } = e.geometry, r = 0, a = Math.cos(r), f = Math.sin(r), d = t - o, u = n - s, h = a * d + f * u, g = f * d - a * u;
    return h * h / (i * i) + g * g / (l * l) <= 1;
  }
};
Qe(x.ELLIPSE, zo);
var qo = {
  area: (e) => 0,
  intersects: (e, t, n, o = 2) => {
    const [[s, i], [l, r]] = e.geometry.points, a = Math.abs((r - i) * t - (l - s) * n + l * i - r * s), f = wt([s, i], [l, r]);
    return a / f <= o;
  }
};
Qe(x.LINE, qo);
var Ko = {
  area: (e) => {
    const { polygons: t } = e.geometry;
    return t.reduce((n, o) => {
      const [s, ...i] = o.rings, l = At(s.points), r = i.reduce((a, f) => a + At(f.points), 0);
      return n + l - r;
    }, 0);
  },
  intersects: (e, t, n) => {
    const { polygons: o } = e.geometry;
    for (const s of o) {
      const [i, ...l] = s.rings;
      if (St(i.points, t, n)) {
        let r = false;
        for (const a of l)
          if (St(a.points, t, n)) {
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
      const a = ao(r.points, t);
      return {
        ...r,
        points: a
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
    const r = l / s, a = Math.pow(1 - r, 3) * e[0] + 3 * Math.pow(1 - r, 2) * r * t[0] + 3 * (1 - r) * Math.pow(r, 2) * n[0] + Math.pow(r, 3) * o[0], f = Math.pow(1 - r, 3) * e[1] + 3 * Math.pow(1 - r, 2) * r * t[1] + 3 * (1 - r) * Math.pow(r, 2) * n[1] + Math.pow(r, 3) * o[1];
    i.push([a, f]);
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
      for (let a = 0; a < r.length - 1; a++)
        if (an(t, r[a], r[a + 1]) <= n)
          return true;
    } else if (an(t, s.point, i.point) <= n)
      return true;
  }
  return false;
};
var an = (e, t, n) => {
  const [o, s] = e, [i, l] = t, [r, a] = n, f = r - i, d = a - l, u = Math.sqrt(f * f + d * d);
  if (u === 0)
    return Math.sqrt((o - i) * (o - i) + (s - l) * (s - l));
  const h = ((o - i) * f + (s - l) * d) / (u * u);
  return h <= 0 ? Math.sqrt((o - i) * (o - i) + (s - l) * (s - l)) : h >= 1 ? Math.sqrt((o - r) * (o - r) + (s - a) * (s - a)) : Math.abs((a - l) * o - (r - i) * s + r * l - a * i) / u;
};
var fo = (e) => {
  if (!e.points || e.points.length === 0)
    return "";
  const t = [], n = e.points[0];
  t.push(`M ${n.point[0]} ${n.point[1]}`);
  for (let o = 1; o < e.points.length; o++) {
    const s = e.points[o], i = e.points[o - 1];
    if (s.type === "CURVE" || i.type === "CURVE") {
      const l = i.type === "CURVE" && i.outHandle || i.point, r = s.type === "CURVE" && s.inHandle || s.point, a = s.point;
      t.push(`C ${l[0]} ${l[1]} ${r[0]} ${r[1]} ${a[0]} ${a[1]}`);
    } else
      t.push(`L ${s.point[0]} ${s.point[1]}`);
  }
  if (e.closed) {
    const o = e.points[e.points.length - 1], s = e.points[0];
    if (o.type === "CURVE" || s.type === "CURVE") {
      const l = o.outHandle || o.point, r = s.inHandle || s.point, a = s.point;
      t.push(`C ${l[0]} ${l[1]} ${r[0]} ${r[1]} ${a[0]} ${a[1]}`);
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
      const s = o.x + o.w / 2, i = o.y + o.h / 2, l = t - s, r = n - i, a = Math.cos(o.rot), f = Math.sin(o.rot), d = l * a + r * f, u = -l * f + r * a;
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
  const [i, l, r, a, f, d, u] = s;
  if (l !== "xywh")
    throw new Error("Unsupported MediaFragment: " + n);
  if (r && r !== "pixel")
    throw new Error(`Unsupported MediaFragment unit: ${r}`);
  const [h, g, p, y] = [a, f, d, u].map(parseFloat);
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
  const s = n[0] - t[0], i = n[1] - t[1], l = o[0] - t[0], r = o[1] - t[1], a = s * r - i * l;
  return Math.abs(a) < 0.01;
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
        const a = [r.args[0], r.args[1]], f = [r.args[2], r.args[3]], d = [r.args[4], r.args[5]];
        if (n.length > 0) {
          const h = n[n.length - 1];
          (a[0] !== h.point[0] || a[1] !== h.point[1]) && (h.type = "CURVE", h.outHandle = a);
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
    const [, i, l] = s, r = l.trim() === "" ? [] : l.trim().split(/\s+/).map(Number).filter((a) => !isNaN(a));
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
  let a = 0;
  if (r && r.startsWith("rotate(")) {
    const p = r.match(/rotate\(([^)]+)\)/);
    p && (a = p[1].split(/\s+/).map(parseFloat)[0] * Math.PI / 180);
  }
  const f = o + i / 2, d = s + l / 2, h = [
    [o, s],
    [o + i, s],
    [o + i, s + l],
    [o, s + l]
  ].map(([p, y]) => {
    const S = p - f, w = y - d, A = Math.cos(a), _ = Math.sin(a);
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
      rot: a,
      bounds: g
    }
  };
};
var us = (e) => {
  const t = it(e), s = (t.nodeName === "path" ? [t] : Array.from(t.querySelectorAll("path"))).map((a) => a.getAttribute("d") || "").map((a) => os(a)).filter(Boolean), i = s.reduce((a, f) => [...a, ...f.rings[0].points], []), l = ue(i);
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
        const a = o + i / 2, f = s + l / 2, d = (r ?? 0) * 180 / Math.PI;
        t = `<svg><rect x="${o}" y="${s}" width="${i}" height="${l}" transform="rotate(${d} ${a} ${f})" /></svg>`;
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
  }, a = (g, p) => {
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
    userSelect: a
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
    const { ignore: l } = e.options, r = (a) => a && a.length > 0;
    if (!(r(s.created) || r(s.deleted))) {
      const a = (n = s.updated) == null ? void 0 : n.some((d) => r(d.bodiesCreated) || r(d.bodiesDeleted) || r(d.bodiesUpdated)), f = (o = s.updated) == null ? void 0 : o.some((d) => d.targetUpdated);
      if (l === "BODY_ONLY" && a && !f || l === "TARGET_ONLY" && f && !a)
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
  const n = new Set((e.created || []).map((u) => u.id)), o = new Set((e.updated || []).map(({ newValue: u }) => u.id)), s = new Set((t.created || []).map((u) => u.id)), i = new Set((t.deleted || []).map((u) => u.id)), l = new Set((t.updated || []).map(({ oldValue: u }) => u.id)), r = new Set((t.updated || []).filter(({ oldValue: u }) => n.has(u.id) || o.has(u.id)).map(({ oldValue: u }) => u.id)), a = [
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
  return { created: a, deleted: f, updated: d };
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
  }, a = (v, E = oe.LOCAL, m = oe.LOCAL) => {
    const b = Ds(E) ? m : E, L = r(v, E);
    L && i(b, { updated: [L] });
  }, f = (v, E = oe.LOCAL) => {
    e.get(v.id) ? a(v, E) : l(v, E);
  }, d = (v, E = oe.LOCAL) => {
    const m = v.reduce((b, L) => {
      const k = r(L);
      return k ? [...b, k] : b;
    }, []);
    m.length > 0 && i(E, { updated: m });
  }, u = (v, E = oe.LOCAL) => {
    const m = v.map(mt), { toAdd: b, toUpdate: L } = m.reduce((O, F) => e.get(F.id) ? { ...O, toUpdate: [...O.toUpdate, F] } : { ...O, toAdd: [...O.toAdd, F] }, { toAdd: [], toUpdate: [] }), k = L.map((O) => r(O, E)).filter(Boolean);
    b.forEach((O) => {
      e.set(O.id, O), O.bodies.forEach((F) => t.set(F.id, O.id));
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
        const F = O.id && e.get(O.id);
        return F ? [...k, F] : k;
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
  }, B = (v, E) => {
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
    const b = B(v, E);
    b && i(m, { updated: [b] });
  }, Z = (v, E = oe.LOCAL) => {
    const m = v.map((b) => B({ id: b.id, annotation: b.annotation }, b)).filter(Boolean);
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
    updateAnnotation: a,
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
  const a = (p) => p && p.length > 0 && e.bulkDeleteAnnotations(p), f = (p) => p && p.length > 0 && e.bulkAddAnnotations(p, false), d = (p) => p && p.length > 0 && e.bulkUpdateAnnotations(p.map(({ oldValue: y }) => y)), u = (p) => p && p.length > 0 && e.bulkUpdateAnnotations(p.map(({ newValue: y }) => y)), h = (p) => p && p.length > 0 && e.bulkAddAnnotations(p, false), g = (p) => p && p.length > 0 && e.bulkDeleteAnnotations(p);
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
        a(p), d(y), h(S), n.emit("undo", o[s]), s -= 1;
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
  const { hover: s, selection: i, store: l, viewport: r } = e, a = /* @__PURE__ */ new Map();
  let f = [], d, u;
  const h = (w, A) => {
    a.has(w) ? a.get(w).push(A) : a.set(w, [A]);
  }, g = (w, A) => {
    const _ = a.get(w);
    if (_) {
      const M = _.indexOf(A);
      M !== -1 && _.splice(M, 1);
    }
  }, p = (w, A, _) => {
    a.has(w) && setTimeout(() => {
      a.get(w).forEach((M) => {
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
  }, l = () => s.clear(), r = () => o.clear(), a = (w) => {
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
    getAnnotationById: a,
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
        let a = n(l), f = l;
        for (; f--; )
          if (r += e[a[f] & i], r.length >= l)
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
      let r = n(s), a = s;
      for (; a--; )
        if (r[a] < o && (l += e[r[a] % e.length], l.length >= i))
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
  const { id: o, type: s, purpose: i, value: l, created: r, modified: a, creator: f, ...d } = n;
  return {
    id: o || `temp-${Zs(n)}`,
    annotation: t,
    type: s,
    purpose: i,
    value: l,
    creator: wo(f),
    created: r ? new Date(r) : void 0,
    updated: a ? new Date(a) : void 0,
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
  } = e, a = Js(l || [], n), f = Array.isArray(e.target) ? e.target[0] : e.target, d = typeof f == "string" ? f : Array.isArray(f.selector) ? f.selector[0] : f.selector, u = $o(d) ? es(d, t.invertY) : (d == null ? void 0 : d.type) === "SvgSelector" ? ds(d) : void 0, h = Array.isArray(r.target) ? r.target[0] : r.targret;
  return u || !t.strict ? {
    parsed: {
      ...r,
      id: n,
      bodies: a,
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
    ...a
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
      ...a,
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
      const a = !ze.length;
      for (const f of o)
        f[1](), ze.push(f, e);
      if (a) {
        for (let f = 0; f < ze.length; f += 2)
          ze[f][0](ze[f + 1]);
        ze.length = 0;
      }
    }
  }
  function i(r) {
    s(r(e));
  }
  function l(r, a = $) {
    const f = [r, a];
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
    const l = t.getBoundingClientRect(), { width: r, height: a } = t.viewBox.baseVal, f = Math.max(
      l.width / r,
      l.height / a
    );
    o(f);
  }), s.observe(t.parentElement)), { destroy: () => {
    s && s.disconnect();
  }, subscribe: n };
};
var je = (e, t, n) => {
  const o = typeof t == "function" ? t(e, n) : t;
  if (o) {
    const { fill: s, fillOpacity: i, stroke: l, strokeWidth: r, strokeOpacity: a } = o;
    let f = "";
    return s && (f += `fill:${s};`), i || i === 0 ? f += `fill-opacity:${i};` : s && (f += "fill-opacity:0.25;"), l && (f += `stroke:${l};`, f += `stroke-width:${r || "1"};`, f += `stroke-opacity:${a || "1"};`), f;
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
    m(r, a) {
      Y(r, t, a), l && l.m(t, null), n = true, o || (s = [
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
    p(r, [a]) {
      l && l.p && (!n || a & /*$$scope*/
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
          a,
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
  let { shape: l } = t, { editor: r } = t, { transform: a } = t, { svgEl: f } = t, d, u, h;
  const g = (S) => (w) => {
    if (d = S, f) {
      const { left: _, top: M } = f.getBoundingClientRect(), P = w.clientX - _, I = w.clientY - M;
      u = a.elementToImage(P, I);
    } else {
      const { offsetX: _, offsetY: M } = w;
      u = a.elementToImage(_, M);
    }
    h = l, w.target.setPointerCapture(w.pointerId), i("grab", w);
  }, p = (S) => {
    if (d) {
      const [w, A] = a.elementToImage(S.offsetX, S.offsetY), _ = [w - u[0], A - u[1]];
      n(3, l = r(h, d, _)), i("change", l);
    }
  }, y = (S) => {
    S.target.releasePointerCapture(S.pointerId), d = void 0, h = l, i("release", S);
  };
  return e.$$set = (S) => {
    "shape" in S && n(3, l = S.shape), "editor" in S && n(4, r = S.editor), "transform" in S && n(5, a = S.transform), "svgEl" in S && n(6, f = S.svgEl), "$$scope" in S && n(7, s = S.$$scope);
  }, [
    g,
    p,
    y,
    l,
    r,
    a,
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
  let t, n, o, s, i, l, r, a, f = (
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
      Y(d, t, u), G(t, n), f && f.m(t, null), G(t, s), r || (a = [
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
      d && R(t), f && f.d(), r = false, Se(a);
    }
  };
}
function ai(e) {
  let t, n, o, s, i, l, r, a, f;
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
      Y(d, t, u), G(t, n), G(t, s), G(t, l), a || (f = [
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
      ], a = true);
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
      d && R(t), a = false, Se(f);
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
  let o, { x: s } = t, { y: i } = t, { scale: l } = t, { selected: r = void 0 } = t, a = false;
  const f = (_) => {
    _.pointerType === "touch" && n(4, a = true);
  }, d = () => n(4, a = false);
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
    a,
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
    m(a, f) {
      Y(a, t, f), G(t, n), G(t, s), G(t, i), l || (r = [
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
    p(a, f) {
      f & /*x*/
      1 && c(
        n,
        "cx",
        /*x*/
        a[0]
      ), f & /*y*/
      2 && c(
        n,
        "cy",
        /*y*/
        a[1]
      ), f & /*handleRadius*/
      4 && o !== (o = 1.75 * /*handleRadius*/
      a[2]) && c(n, "r", o), f & /*x*/
      1 && c(
        s,
        "cx",
        /*x*/
        a[0]
      ), f & /*y*/
      2 && c(
        s,
        "cy",
        /*y*/
        a[1]
      ), f & /*handleRadius*/
      4 && c(
        s,
        "r",
        /*handleRadius*/
        a[2]
      ), f & /*x*/
      1 && c(
        i,
        "cx",
        /*x*/
        a[0]
      ), f & /*y*/
      2 && c(
        i,
        "cy",
        /*y*/
        a[1]
      ), f & /*handleRadius*/
      4 && c(
        i,
        "r",
        /*handleRadius*/
        a[2]
      );
    },
    d(a) {
      a && R(t), l = false, Se(r);
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
  function a(f) {
    de.call(this, e, f);
  }
  return e.$$set = (f) => {
    "x" in f && n(0, s = f.x), "y" in f && n(1, i = f.y), "scale" in f && n(4, l = f.scale);
  }, e.$$.update = () => {
    e.$$.dirty & /*scale*/
    16 && n(2, o = 4 / l);
  }, [s, i, o, r, l, a];
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
  let t, n, o, s, i, l, r, a, f, d;
  return {
    c() {
      t = C("mask"), n = C("rect"), r = C("circle"), c(n, "x", o = /*mask*/
      e[9].x), c(n, "y", s = /*mask*/
      e[9].y), c(n, "width", i = /*mask*/
      e[9].w), c(n, "height", l = /*mask*/
      e[9].h), c(n, "class", "svelte-1h2slbm"), c(r, "cx", a = /*point*/
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
      1088 && a !== (a = /*point*/
      u[28][0]) && c(r, "cx", a), h[0] & /*midpoints, visibleMidpoint*/
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
  let t, n, o, s, i, l, r, a, f, d, u, h, g, p, y, S, w, A, _, M, P, I = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && gn(Ut(e))
  ), T = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && mn(Vt(e))
  ), B = ve(
    /*geom*/
    e[5].points
  ), N = [];
  for (let v = 0; v < B.length; v += 1)
    N[v] = pn(hn(e, B, v));
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
      t = C("defs"), n = C("mask"), o = C("rect"), a = C("polygon"), I && I.c(), T && T.c(), d = we(), u = C("polygon"), g = we(), p = C("polygon"), S = we();
      for (let v = 0; v < N.length; v += 1)
        N[v].c();
      w = we(), j && j.c(), A = Me(), c(o, "x", s = /*mask*/
      e[9].x), c(o, "y", i = /*mask*/
      e[9].y), c(o, "width", l = /*mask*/
      e[9].w), c(o, "height", r = /*mask*/
      e[9].h), c(o, "class", "svelte-1h2slbm"), c(a, "points", f = /*geom*/
      e[5].points.map(yn).join(" ")), c(a, "class", "svelte-1h2slbm"), c(n, "id", `${/*maskId*/
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
      Y(v, t, E), G(t, n), G(n, o), G(n, a), I && I.m(n, null), T && T.m(t, null), Y(v, d, E), Y(v, u, E), Y(v, g, E), Y(v, p, E), Y(v, S, E);
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
      e[5].points.map(yn).join(" "))) && c(a, "points", f), /*visibleMidpoint*/
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
        B = ve(
          /*geom*/
          e[5].points
        );
        let m;
        for (m = 0; m < B.length; m += 1) {
          const b = hn(e, B, m);
          N[m] ? (N[m].p(b, E), V(N[m], 1)) : (N[m] = pn(b), N[m].c(), V(N[m], 1), N[m].m(w.parentNode, w));
        }
        for (be(), m = B.length; m < N.length; m += 1)
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
        for (let E = 0; E < B.length; E += 1)
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
  let { shape: r } = t, { computedStyle: a } = t, { transform: f } = t, { viewportScale: d = 1 } = t, { svgEl: u } = t, h, g = false, p, y = [];
  const S = () => n(7, g = true), w = () => n(7, g = false), A = (m) => {
    if (y.length > 0 || !s.some((q) => q.visible)) {
      n(6, h = void 0);
      return;
    }
    const [b, L] = f.elementToImage(m.offsetX, m.offsetY), k = (q) => Math.pow(q[0] - b, 2) + Math.pow(q[1] - L, 2), O = o.points.reduce((q, K) => k(K) < k(q) ? K : q), F = s.filter((q) => q.visible).reduce((q, K) => k(K.point) < k(q.point) ? K : q), U = Math.pow(yi / d, 2);
    k(O) < U || k(F.point) < U ? n(6, h = s.indexOf(F)) : n(6, h = void 0);
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
    y.length > 1 ? k = O.points.map(([U, J], q) => y.includes(q) ? [U + L[0], J + L[1]] : [U, J]) : b === "SHAPE" ? k = O.points.map(([U, J]) => [U + L[0], J + L[1]]) : k = O.points.map(([U, J], q) => b === `HANDLE-${q}` ? [U + L[0], J + L[1]] : [U, J]);
    const F = ue(k);
    return { ...m, geometry: { points: k, bounds: F } };
  }, B = (m) => async (b) => {
    b.stopPropagation();
    const L = [
      ...o.points.slice(0, m + 1),
      s[m].point,
      ...o.points.slice(m + 1)
    ], k = ue(L);
    l("change", { ...r, geometry: { points: L, bounds: k } }), await io();
    const O = [...document.querySelectorAll(".a9s-handle")][m + 1];
    if (O != null && O.firstChild) {
      const F = new PointerEvent(
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
      O.firstChild.dispatchEvent(F);
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
    "shape" in m && n(0, r = m.shape), "computedStyle" in m && n(1, a = m.computedStyle), "transform" in m && n(2, f = m.transform), "viewportScale" in m && n(3, d = m.viewportScale), "svgEl" in m && n(4, u = m.svgEl);
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
    a,
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
    B,
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
  const [o, s] = e, [i, l] = t, r = Math.cos(n), a = Math.sin(n), f = o - i, d = s - l;
  return [
    i + f * r - d * a,
    l + f * a + d * r
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
  let a = [n + s / 2, o - t];
  return Eo(a, r, l);
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
  let t, n, o, s, i, l, r, a, f, d, u, h, g, p, y, S, w, A, _, M, P, I, T, B, N, Z, j, v, E, m, b, L, k, O, F, U, J, q, K, ee, he, ne, Ae, X, fe, te, ge, ie, Ue, rt, lt, at, Tt, Ce, Pt, Ie, Lt, Oe, Ct, Ne, W, It, on;
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
  }), Ce = new Xe({
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
  }), Ce.$on("pointerdown", function() {
    se(
      /*grab*/
      e[16]("TOP_LEFT")
    ) && e[16]("TOP_LEFT").apply(this, arguments);
  }), Ie = new Xe({
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
  }), Ie.$on("pointerdown", function() {
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
  }), Ne = new Xe({
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
  }), Ne.$on("pointerdown", function() {
    se(
      /*grab*/
      e[16]("BOTTOM_LEFT")
    ) && e[16]("BOTTOM_LEFT").apply(this, arguments);
  }), {
    c() {
      t = C("defs"), n = C("mask"), o = C("rect"), a = C("polygon"), d = we(), u = C("g"), h = C("line"), w = C("line"), ce(I.$$.fragment), T = we(), B = C("g"), N = C("polygon"), j = C("polygon"), E = we(), m = C("line"), F = we(), U = C("line"), he = we(), ne = C("line"), ge = we(), ie = C("line"), Tt = we(), ce(Ce.$$.fragment), Pt = we(), ce(Ie.$$.fragment), Lt = we(), ce(Oe.$$.fragment), Ct = we(), ce(Ne.$$.fragment), c(o, "class", "rect-mask-bg svelte-1bwhzbc"), c(o, "x", s = /*mask*/
      e[5].x), c(o, "y", i = /*mask*/
      e[5].y), c(o, "width", l = /*mask*/
      e[5].w), c(o, "height", r = /*mask*/
      e[5].h), c(a, "class", "rect-mask-fg svelte-1bwhzbc"), c(a, "points", f = /*rotatedCorners*/
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
      e[6][1][1]), c(U, "class", "a9s-edge-handle a9s-edge-handle-right"), c(U, "x1", J = /*rotatedCorners*/
      e[6][1][0]), c(U, "y1", q = /*rotatedCorners*/
      e[6][1][1]), c(U, "x2", K = /*rotatedCorners*/
      e[6][2][0]), c(U, "y2", ee = /*rotatedCorners*/
      e[6][2][1]), c(ne, "class", "a9s-edge-handle a9s-edge-handle-bottom"), c(ne, "x1", Ae = /*rotatedCorners*/
      e[6][2][0]), c(ne, "y1", X = /*rotatedCorners*/
      e[6][2][1]), c(ne, "x2", fe = /*rotatedCorners*/
      e[6][3][0]), c(ne, "y2", te = /*rotatedCorners*/
      e[6][3][1]), c(ie, "class", "a9s-edge-handle a9s-edge-handle-left"), c(ie, "x1", Ue = /*rotatedCorners*/
      e[6][3][0]), c(ie, "y1", rt = /*rotatedCorners*/
      e[6][3][1]), c(ie, "x2", lt = /*rotatedCorners*/
      e[6][0][0]), c(ie, "y2", at = /*rotatedCorners*/
      e[6][0][1]);
    },
    m(z, D) {
      Y(z, t, D), G(t, n), G(n, o), G(n, a), Y(z, d, D), Y(z, u, D), G(u, h), G(u, w), re(I, u, null), Y(z, T, D), Y(z, B, D), G(B, N), G(B, j), Y(z, E, D), Y(z, m, D), Y(z, F, D), Y(z, U, D), Y(z, he, D), Y(z, ne, D), Y(z, ge, D), Y(z, ie, D), Y(z, Tt, D), re(Ce, z, D), Y(z, Pt, D), re(Ie, z, D), Y(z, Lt, D), re(Oe, z, D), Y(z, Ct, D), re(Ne, z, D), W = true, It || (on = [
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
        Q(ne, "pointerdown", function() {
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
      e = z, (!W || D & /*mask*/
      32 && s !== (s = /*mask*/
      e[5].x)) && c(o, "x", s), (!W || D & /*mask*/
      32 && i !== (i = /*mask*/
      e[5].y)) && c(o, "y", i), (!W || D & /*mask*/
      32 && l !== (l = /*mask*/
      e[5].w)) && c(o, "width", l), (!W || D & /*mask*/
      32 && r !== (r = /*mask*/
      e[5].h)) && c(o, "height", r), (!W || D & /*rotatedCorners*/
      64 && f !== (f = /*rotatedCorners*/
      e[6].map(vn).join(" "))) && c(a, "points", f), (!W || D & /*rotatedCorners*/
      64 && g !== (g = /*rotatedCorners*/
      e[6][0][0] + /*rotatedCorners*/
      (e[6][1][0] - /*rotatedCorners*/
      e[6][0][0]) / 2)) && c(h, "x1", g), (!W || D & /*rotatedCorners*/
      64 && p !== (p = /*rotatedCorners*/
      e[6][0][1] + /*rotatedCorners*/
      (e[6][1][1] - /*rotatedCorners*/
      e[6][0][1]) / 2)) && c(h, "y1", p), (!W || D & /*rotationHandlePos*/
      128 && y !== (y = /*rotationHandlePos*/
      e[7][0])) && c(h, "x2", y), (!W || D & /*rotationHandlePos*/
      128 && S !== (S = /*rotationHandlePos*/
      e[7][1])) && c(h, "y2", S), (!W || D & /*rotatedCorners*/
      64 && A !== (A = /*rotatedCorners*/
      e[6][0][0] + /*rotatedCorners*/
      (e[6][1][0] - /*rotatedCorners*/
      e[6][0][0]) / 2)) && c(w, "x1", A), (!W || D & /*rotatedCorners*/
      64 && _ !== (_ = /*rotatedCorners*/
      e[6][0][1] + /*rotatedCorners*/
      (e[6][1][1] - /*rotatedCorners*/
      e[6][0][1]) / 2)) && c(w, "y1", _), (!W || D & /*rotationHandlePos*/
      128 && M !== (M = /*rotationHandlePos*/
      e[7][0])) && c(w, "x2", M), (!W || D & /*rotationHandlePos*/
      128 && P !== (P = /*rotationHandlePos*/
      e[7][1])) && c(w, "y2", P);
      const ct = {};
      D & /*rotationHandlePos*/
      128 && (ct.x = /*rotationHandlePos*/
      e[7][0]), D & /*rotationHandlePos*/
      128 && (ct.y = /*rotationHandlePos*/
      e[7][1]), D & /*viewportScale*/
      8 && (ct.scale = /*viewportScale*/
      e[3]), I.$set(ct), (!W || D & /*rotatedCorners*/
      64 && Z !== (Z = /*rotatedCorners*/
      e[6].map(An).join(" "))) && c(N, "points", Z), (!W || D & /*computedStyle*/
      2) && c(
        j,
        "style",
        /*computedStyle*/
        e[1]
      ), (!W || D & /*rotatedCorners*/
      64 && v !== (v = /*rotatedCorners*/
      e[6].map(Sn).join(" "))) && c(j, "points", v), (!W || D & /*rotatedCorners*/
      64 && b !== (b = /*rotatedCorners*/
      e[6][0][0])) && c(m, "x1", b), (!W || D & /*rotatedCorners*/
      64 && L !== (L = /*rotatedCorners*/
      e[6][0][1])) && c(m, "y1", L), (!W || D & /*rotatedCorners*/
      64 && k !== (k = /*rotatedCorners*/
      e[6][1][0])) && c(m, "x2", k), (!W || D & /*rotatedCorners*/
      64 && O !== (O = /*rotatedCorners*/
      e[6][1][1])) && c(m, "y2", O), (!W || D & /*rotatedCorners*/
      64 && J !== (J = /*rotatedCorners*/
      e[6][1][0])) && c(U, "x1", J), (!W || D & /*rotatedCorners*/
      64 && q !== (q = /*rotatedCorners*/
      e[6][1][1])) && c(U, "y1", q), (!W || D & /*rotatedCorners*/
      64 && K !== (K = /*rotatedCorners*/
      e[6][2][0])) && c(U, "x2", K), (!W || D & /*rotatedCorners*/
      64 && ee !== (ee = /*rotatedCorners*/
      e[6][2][1])) && c(U, "y2", ee), (!W || D & /*rotatedCorners*/
      64 && Ae !== (Ae = /*rotatedCorners*/
      e[6][2][0])) && c(ne, "x1", Ae), (!W || D & /*rotatedCorners*/
      64 && X !== (X = /*rotatedCorners*/
      e[6][2][1])) && c(ne, "y1", X), (!W || D & /*rotatedCorners*/
      64 && fe !== (fe = /*rotatedCorners*/
      e[6][3][0])) && c(ne, "x2", fe), (!W || D & /*rotatedCorners*/
      64 && te !== (te = /*rotatedCorners*/
      e[6][3][1])) && c(ne, "y2", te), (!W || D & /*rotatedCorners*/
      64 && Ue !== (Ue = /*rotatedCorners*/
      e[6][3][0])) && c(ie, "x1", Ue), (!W || D & /*rotatedCorners*/
      64 && rt !== (rt = /*rotatedCorners*/
      e[6][3][1])) && c(ie, "y1", rt), (!W || D & /*rotatedCorners*/
      64 && lt !== (lt = /*rotatedCorners*/
      e[6][0][0])) && c(ie, "x2", lt), (!W || D & /*rotatedCorners*/
      64 && at !== (at = /*rotatedCorners*/
      e[6][0][1])) && c(ie, "y2", at);
      const ft = {};
      D & /*rotatedCorners*/
      64 && (ft.x = /*rotatedCorners*/
      e[6][0][0]), D & /*rotatedCorners*/
      64 && (ft.y = /*rotatedCorners*/
      e[6][0][1]), D & /*viewportScale*/
      8 && (ft.scale = /*viewportScale*/
      e[3]), Ce.$set(ft);
      const ut = {};
      D & /*rotatedCorners*/
      64 && (ut.x = /*rotatedCorners*/
      e[6][1][0]), D & /*rotatedCorners*/
      64 && (ut.y = /*rotatedCorners*/
      e[6][1][1]), D & /*viewportScale*/
      8 && (ut.scale = /*viewportScale*/
      e[3]), Ie.$set(ut);
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
      e[3]), Ne.$set(ht);
    },
    i(z) {
      W || (V(I.$$.fragment, z), V(Ce.$$.fragment, z), V(Ie.$$.fragment, z), V(Oe.$$.fragment, z), V(Ne.$$.fragment, z), W = true);
    },
    o(z) {
      H(I.$$.fragment, z), H(Ce.$$.fragment, z), H(Ie.$$.fragment, z), H(Oe.$$.fragment, z), H(Ne.$$.fragment, z), W = false;
    },
    d(z) {
      z && (R(t), R(d), R(u), R(T), R(B), R(E), R(m), R(F), R(U), R(he), R(ne), R(ge), R(ie), R(Tt), R(Pt), R(Lt), R(Ct)), le(I), le(Ce, z), le(Ie, z), le(Oe, z), le(Ne, z), It = false, Se(on);
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
  let o, s, i, l, r, { shape: a } = t, { computedStyle: f } = t, { transform: d } = t, { viewportScale: u = 1 } = t, { svgEl: h } = t, g = false;
  const p = (_, M, P) => {
    let { x: I, y: T, w: B, h: N, rot: Z = 0 } = _.geometry;
    const [j, v] = P;
    if (M === "ROTATION") {
      const m = En(_.geometry, o), b = m[0] + j, L = m[1] + v, k = [I + B / 2, T + N / 2];
      Z += Si([m[0], m[1]], [b, L], k), g && (Z = Mi(Z));
      const O = 2 * Math.PI;
      Z = (Z % O + O) % O;
    } else if (M === "SHAPE")
      I += j, T += v;
    else {
      let m = 0, b = 0, L = B, k = N;
      const [O, F] = Z !== 0 ? Ai(j, v, Z) : [j, v];
      switch (M) {
        case "TOP":
        case "TOP_LEFT":
        case "TOP_RIGHT":
          b += F;
          break;
        case "BOTTOM":
        case "BOTTOM_LEFT":
        case "BOTTOM_RIGHT":
          k += F;
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
      const U = (m + L) / 2, J = (b + k) / 2;
      B = Math.abs(L - m), N = Math.abs(k - b);
      const q = [I + _.geometry.w / 2, T + _.geometry.h / 2], K = [
        U - _.geometry.w / 2,
        J - _.geometry.h / 2
      ], ee = Math.cos(Z), he = Math.sin(Z), ne = q[0] + K[0] * ee - K[1] * he, Ae = q[1] + K[0] * he + K[1] * ee;
      I = ne - B / 2, T = Ae - N / 2;
    }
    const E = ue(i);
    return {
      ..._,
      geometry: { x: I, y: T, w: B, h: N, rot: Z, bounds: E }
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
    "shape" in _ && n(0, a = _.shape), "computedStyle" in _ && n(1, f = _.computedStyle), "transform" in _ && n(2, d = _.transform), "viewportScale" in _ && n(3, u = _.viewportScale), "svgEl" in _ && n(4, h = _.svgEl);
  }, e.$$.update = () => {
    e.$$.dirty & /*viewportScale*/
    8 && n(11, o = 20 / u), e.$$.dirty & /*shape*/
    1 && n(10, s = a.geometry), e.$$.dirty & /*geom*/
    1024 && n(6, i = vi(s.x, s.y, s.w, s.h, s.rot)), e.$$.dirty & /*geom, ROTATION_HANDLE_OFFSET*/
    3072 && n(7, l = En(s, o)), e.$$.dirty & /*geom, viewportScale*/
    1032 && n(5, r = kt(s.bounds, 5 / u));
  }, [
    a,
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
  const i = o.rings.reduce((l, r, a) => {
    const f = r.points.map((d, u) => {
      const h = u === r.points.length - 1 ? r.points[0] : r.points[u + 1], g = (d[0] + h[0]) / 2, p = (d[1] + h[1]) / 2, S = Math.sqrt(
        Math.pow(h[0] - g, 2) + Math.pow(h[1] - p, 2)
      ) > Ci / t;
      return { point: [g, p], visible: S, elementIdx: s, ringIdx: a, pointIdx: u };
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
  let t, n, o, s, i, l, r, a, f, d;
  return {
    c() {
      t = C("mask"), n = C("rect"), r = C("circle"), c(n, "x", o = /*mask*/
      e[9].x), c(n, "y", s = /*mask*/
      e[9].y), c(n, "width", i = /*mask*/
      e[9].w), c(n, "height", l = /*mask*/
      e[9].h), c(n, "class", "svelte-1vxo6dc"), c(r, "cx", a = /*point*/
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
      1088 && a !== (a = /*point*/
      u[29][0]) && c(r, "cx", a), h[0] & /*midpoints, visibleMidpoint*/
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
      for (let a = 0; a < s.length; a += 1)
        s[a] && s[a].m(l, r);
      Y(l, t, r), n = true;
    },
    p(l, r) {
      if (r[0] & /*geom, viewportScale, selectedCorners, onEnterHandle, onLeaveHandle, onHandlePointerDown, grab, onHandlePointerUp*/
      268491048) {
        o = ve(
          /*ring*/
          l[33].points
        );
        let a;
        for (a = 0; a < o.length; a += 1) {
          const f = Pn(l, o, a);
          s[a] ? (s[a].p(f, r), V(s[a], 1)) : (s[a] = In(f), s[a].c(), V(s[a], 1), s[a].m(t.parentNode, t));
        }
        for (be(), a = o.length; a < s.length; a += 1)
          i(a);
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
  let t, n, o, s, i, l, r, a, f, d, u, h, g, p, y, S, w, A = (
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
      e[9].w), c(s, "height", a = /*mask*/
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
    m(T, B) {
      Y(T, t, B), G(t, n), G(n, o), G(o, s), G(o, f), A && A.m(o, null), _ && _.m(n, null), G(t, u), G(t, g);
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
    p(T, B) {
      if (e = T, (!y || B[0] & /*mask*/
      512 && i !== (i = /*mask*/
      e[9].x)) && c(s, "x", i), (!y || B[0] & /*mask*/
      512 && l !== (l = /*mask*/
      e[9].y)) && c(s, "y", l), (!y || B[0] & /*mask*/
      512 && r !== (r = /*mask*/
      e[9].w)) && c(s, "width", r), (!y || B[0] & /*mask*/
      512 && a !== (a = /*mask*/
      e[9].h)) && c(s, "height", a), (!y || B[0] & /*geom*/
      32 && d !== (d = Pe(
        /*element*/
        e[30]
      ))) && c(f, "d", d), /*visibleMidpoint*/
      e[6] !== void 0 && !/*isHandleHovered*/
      e[7] ? A ? A.p(Gt(e), B) : (A = Ln(Gt(e)), A.c(), A.m(o, null)) : A && (A.d(1), A = null), /*visibleMidpoint*/
      e[6] !== void 0 && !/*isHandleHovered*/
      e[7] ? _ ? _.p(Ht(e), B) : (_ = Cn(Ht(e)), _.c(), _.m(n, null)) : _ && (_.d(1), _ = null), (!y || B[0] & /*geom*/
      32 && h !== (h = Pe(
        /*element*/
        e[30]
      ))) && c(u, "d", h), (!y || B[0] & /*computedStyle*/
      2) && c(
        g,
        "style",
        /*computedStyle*/
        e[1]
      ), (!y || B[0] & /*geom*/
      32 && p !== (p = Pe(
        /*element*/
        e[30]
      ))) && c(g, "d", p), B[0] & /*geom, viewportScale, selectedCorners, onEnterHandle, onLeaveHandle, onHandlePointerDown, grab, onHandlePointerUp*/
      268491048) {
        M = ve(
          /*element*/
          e[30].rings
        );
        let N;
        for (N = 0; N < M.length; N += 1) {
          const Z = Tn(e, M, N);
          P[N] ? (P[N].p(Z, B), V(P[N], 1)) : (P[N] = On(Z), P[N].c(), V(P[N], 1), P[N].m(t, null));
        }
        for (be(), N = M.length; N < P.length; N += 1)
          I(N);
        Ee();
      }
    },
    i(T) {
      if (!y) {
        for (let B = 0; B < M.length; B += 1)
          V(P[B]);
        y = true;
      }
    },
    o(T) {
      P = P.filter(Boolean);
      for (let B = 0; B < P.length; B += 1)
        H(P[B]);
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
  for (let a = 0; a < s.length; a += 1)
    i[a] = Nn(kn(e, s, a));
  const l = (a) => H(i[a], 1, 1, () => {
    i[a] = null;
  });
  let r = (
    /*visibleMidpoint*/
    e[6] !== void 0 && !/*isHandleHovered*/
    e[7] && Dn(Xt(e))
  );
  return {
    c() {
      for (let a = 0; a < i.length; a += 1)
        i[a].c();
      t = we(), r && r.c(), n = Me();
    },
    m(a, f) {
      for (let d = 0; d < i.length; d += 1)
        i[d] && i[d].m(a, f);
      Y(a, t, f), r && r.m(a, f), Y(a, n, f), o = true;
    },
    p(a, f) {
      if (f[0] & /*geom, viewportScale, selectedCorners, onEnterHandle, onLeaveHandle, onHandlePointerDown, grab, onHandlePointerUp, maskId, computedStyle, onShapePointerUp, midpoints, visibleMidpoint, mask, isHandleHovered*/
      268763114) {
        s = ve(
          /*geom*/
          a[5].polygons
        );
        let d;
        for (d = 0; d < s.length; d += 1) {
          const u = kn(a, s, d);
          i[d] ? (i[d].p(u, f), V(i[d], 1)) : (i[d] = Nn(u), i[d].c(), V(i[d], 1), i[d].m(t.parentNode, t));
        }
        for (be(), d = s.length; d < i.length; d += 1)
          l(d);
        Ee();
      }
      a[6] !== void 0 && !/*isHandleHovered*/
      a[7] ? r ? (r.p(Xt(a), f), f[0] & /*visibleMidpoint, isHandleHovered*/
      192 && V(r, 1)) : (r = Dn(Xt(a)), r.c(), V(r, 1), r.m(n.parentNode, n)) : r && (be(), H(r, 1, 1, () => {
        r = null;
      }), Ee());
    },
    i(a) {
      if (!o) {
        for (let f = 0; f < s.length; f += 1)
          V(i[f]);
        V(r), o = true;
      }
    },
    o(a) {
      i = i.filter(Boolean);
      for (let f = 0; f < i.length; f += 1)
        H(i[f]);
      H(r), o = false;
    },
    d(a) {
      a && (R(t), R(n)), Ge(i, a), r && r.d(a);
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
  let { shape: r } = t, { computedStyle: a } = t, { transform: f } = t, { viewportScale: d = 1 } = t, { svgEl: u } = t, h, g = false, p, y = [];
  const S = () => n(7, g = true), w = () => n(7, g = false), A = (b) => {
    if (y.length > 0 || !s.some((K) => K.visible)) {
      n(6, h = void 0);
      return;
    }
    const [L, k] = f.elementToImage(b.offsetX, b.offsetY), O = (K) => Math.pow(K[0] - L, 2) + Math.pow(K[1] - k, 2), F = Wo(o).reduce((K, ee) => O(ee) < O(K) ? ee : K), U = s.filter((K) => K.visible).reduce((K, ee) => O(ee.point) < O(K.point) ? ee : K), J = Math.pow(Ri / d, 2);
    O(F) < J || O(U.point) < J ? n(6, h = s.indexOf(U)) : n(6, h = void 0);
  }, _ = () => {
    document.activeElement !== u && u.focus();
  }, M = () => {
    n(8, y = []), _();
  }, P = (b) => {
    n(7, g = true), b.preventDefault(), b.stopPropagation(), p = performance.now();
  }, I = (b, L, k) => (O) => {
    if (!p || Le || performance.now() - p > Di)
      return;
    const F = (J) => J.polygon === b && J.ring === L && J.point === k, U = y.some(F);
    O.metaKey || O.ctrlKey || O.shiftKey ? U ? n(8, y = y.filter((J) => !F(J))) : n(8, y = [...y, { polygon: b, ring: L, point: k }]) : U && y.length > 1 ? n(8, y = [{ polygon: b, ring: L, point: k }]) : U ? n(8, y = []) : n(8, y = [{ polygon: b, ring: L, point: k }]), _();
  }, T = (b, L, k) => {
    _();
    const O = b.geometry.polygons;
    let F;
    if (L === "SHAPE")
      F = O.map((U) => {
        const J = U.rings.map((K, ee) => ({ points: K.points.map((ne, Ae) => [ne[0] + k[0], ne[1] + k[1]]) })), q = ue(J[0].points);
        return { rings: J, bounds: q };
      });
    else {
      const [U, J, q, K] = L.split("-").map((ee) => parseInt(ee));
      F = O.map((ee, he) => {
        if (he === J) {
          const ne = ee.rings.map((X, fe) => fe === q ? { points: X.points.map((ge, ie) => ie === K ? [ge[0] + k[0], ge[1] + k[1]] : ge) } : X), Ae = ue(ne[0].points);
          return { rings: ne, bounds: Ae };
        } else
          return ee;
      });
    }
    return {
      ...b,
      geometry: {
        polygons: F,
        bounds: bt(F)
      }
    };
  }, B = (b) => async (L) => {
    L.stopPropagation();
    const k = s[b], O = o.polygons.map((U, J) => {
      if (J === k.elementIdx) {
        const q = U.rings.map((ee, he) => he === k.ringIdx ? { points: [
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
    const F = [...document.querySelectorAll(".a9s-handle")][b + 1];
    if (F != null && F.firstChild) {
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
      F.firstChild.dispatchEvent(U);
    }
  }, N = () => {
    const b = o.polygons.map((k, O) => {
      if (y.some((U) => U.polygon === O)) {
        const U = k.rings.map((q, K) => {
          const ee = y.filter((he) => he.polygon === O && he.ring === K);
          return ee.length && q.points.length - ee.length >= 3 ? { points: q.points.filter((ne, Ae) => !ee.some((X) => X.point === Ae)) } : q;
        }), J = ue(U[0].points);
        return { rings: U, bounds: J };
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
  const Z = `polygon-mask-${Math.random().toString(36).substring(2, 12)}`, j = (b, L, k, { polygon: O, ring: F, point: U }) => O === b && F === L && U === k;
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
    "shape" in b && n(0, r = b.shape), "computedStyle" in b && n(1, a = b.computedStyle), "transform" in b && n(2, f = b.transform), "viewportScale" in b && n(3, d = b.viewportScale), "svgEl" in b && n(4, u = b.svgEl);
  }, e.$$.update = () => {
    e.$$.dirty[0] & /*shape*/
    1 && n(5, o = r.geometry), e.$$.dirty[0] & /*geom, viewportScale*/
    40 && n(10, s = Le ? [] : Ii(o, d)), e.$$.dirty[0] & /*geom, viewportScale*/
    40 && n(9, i = kt(o.bounds, st / d));
  }, [
    r,
    a,
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
    B,
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
  let { annotation: i } = t, { editor: l } = t, { style: r } = t, { target: a } = t, { transform: f } = t, { viewportScale: d } = t, u;
  return Be(() => (n(6, u = new l({
    target: a,
    props: {
      shape: i.target.selector,
      computedStyle: o,
      transform: f,
      viewportScale: d,
      svgEl: a.closest("svg")
    }
  })), u.$on("change", (h) => {
    u.$$set({ shape: h.detail }), s("change", h.detail);
  }), u.$on("grab", (h) => s("grab", h.detail)), u.$on("release", (h) => s("release", h.detail)), () => {
    u.$destroy();
  })), e.$$set = (h) => {
    "annotation" in h && n(0, i = h.annotation), "editor" in h && n(1, l = h.editor), "style" in h && n(2, r = h.style), "target" in h && n(3, a = h.target), "transform" in h && n(4, f = h.transform), "viewportScale" in h && n(5, d = h.viewportScale);
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
    a,
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
  let { drawingMode: s } = t, { target: i } = t, { tool: l } = t, { transform: r } = t, { viewportScale: a } = t, f;
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
        viewportScale: a
      }
    })), f.$on("create", (g) => o("create", g.detail)), () => {
      u.forEach((g) => g()), f.$destroy();
    };
  }), e.$$set = (d) => {
    "drawingMode" in d && n(0, s = d.drawingMode), "target" in d && n(1, i = d.target), "tool" in d && n(2, l = d.tool), "transform" in d && n(3, r = d.transform), "viewportScale" in d && n(4, a = d.viewportScale);
  }, e.$$.update = () => {
    e.$$.dirty & /*toolComponent, transform*/
    40 && f && f.$set({ transform: r }), e.$$.dirty & /*toolComponent, viewportScale*/
    48 && f && f.$set({ viewportScale: a });
  }, [s, i, l, r, a, f];
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
  let t, n, o, s, i, l, r, a, f, d;
  return {
    c() {
      t = C("defs"), n = C("mask"), o = C("rect"), a = C("rect"), f = C("rect"), d = C("rect"), c(o, "class", "rect-mask-bg svelte-1a76qe7"), c(o, "x", s = /*x*/
      e[1] - /*buffer*/
      e[5]), c(o, "y", i = /*y*/
      e[2] - /*buffer*/
      e[5]), c(o, "width", l = /*w*/
      e[3] + 2 * /*buffer*/
      e[5]), c(o, "height", r = /*h*/
      e[4] + 2 * /*buffer*/
      e[5]), c(a, "class", "rect-mask-fg svelte-1a76qe7"), c(
        a,
        "x",
        /*x*/
        e[1]
      ), c(
        a,
        "y",
        /*y*/
        e[2]
      ), c(
        a,
        "width",
        /*w*/
        e[3]
      ), c(
        a,
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
      Y(u, t, h), G(t, n), G(n, o), G(n, a), Y(u, f, h), Y(u, d, h);
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
        a,
        "x",
        /*x*/
        u[1]
      ), h & /*y*/
      4 && c(
        a,
        "y",
        /*y*/
        u[2]
      ), h & /*w*/
      8 && c(
        a,
        "width",
        /*w*/
        u[3]
      ), h & /*h*/
      16 && c(
        a,
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
  let { addEventListener: i } = t, { drawingMode: l } = t, { transform: r } = t, { viewportScale: a = 1 } = t, f, d, u, h, g, p, y;
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
    "addEventListener" in P && n(7, i = P.addEventListener), "drawingMode" in P && n(8, l = P.drawingMode), "transform" in P && n(9, r = P.transform), "viewportScale" in P && n(10, a = P.viewportScale);
  }, e.$$.update = () => {
    e.$$.dirty & /*viewportScale*/
    1024 && n(5, o = 2 / a);
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
    a
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
  let t, n, o, s, i, l, r, a, f, d, u, h, g, p, y = (
    /*isClosable*/
    e[1] && Bn(e)
  );
  return {
    c() {
      t = C("defs"), n = C("mask"), o = C("rect"), a = C("polygon"), d = C("polygon"), h = C("polygon"), y && y.c(), p = Me(), c(o, "x", s = /*mask*/
      e[3].x), c(o, "y", i = /*mask*/
      e[3].y), c(o, "width", l = /*mask*/
      e[3].w), c(o, "height", r = /*mask*/
      e[3].h), c(o, "class", "svelte-18wrg3t"), c(a, "points", f = /*str*/
      e[19]), c(a, "class", "svelte-18wrg3t"), c(
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
      Y(S, t, w), G(t, n), G(n, o), G(n, a), Y(S, d, w), Y(S, h, w), y && y.m(S, w), Y(S, p, w);
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
      S[19]) && c(a, "points", f), w & /*coords*/
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
  let { addEventListener: r } = t, { drawingMode: a } = t, { transform: f } = t, { viewportScale: d = 1 } = t, u, h = [], g, p, y = false;
  const S = (I) => {
    const T = I, { timeStamp: B, offsetX: N, offsetY: Z } = T;
    if (u = { timeStamp: B, offsetX: N, offsetY: Z }, a === "drag" && h.length === 0) {
      const j = f.elementToImage(T.offsetX, T.offsetY);
      h.push(j), n(10, g = j);
    }
  }, w = (I) => {
    const T = I;
    if (p && clearTimeout(p), h.length > 0) {
      if (n(10, g = f.elementToImage(T.offsetX, T.offsetY)), h.length > 2) {
        const B = wt(g, h[0]) * d;
        n(1, y = B < Wi);
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
    if (p && clearTimeout(p), a === "click") {
      const B = T.timeStamp - u.timeStamp, N = wt([u.offsetX, u.offsetY], [T.offsetX, T.offsetY]);
      if (B > 300 || N > 15)
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
    "addEventListener" in I && n(6, r = I.addEventListener), "drawingMode" in I && n(7, a = I.drawingMode), "transform" in I && n(8, f = I.transform), "viewportScale" in I && n(9, d = I.viewportScale);
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
    a,
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
  const { cx: r, cy: a, rx: f, ry: d } = i;
  return e.$$set = (u) => {
    "annotation" in u && n(0, s = u.annotation), "geom" in u && n(6, i = u.geom), "style" in u && n(7, l = u.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    129 && n(1, o = je(s, l));
  }, [s, o, r, a, f, d, i, l];
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
  const { points: r } = i, [[a, f], [d, u]] = r;
  return e.$$set = (h) => {
    "annotation" in h && n(0, s = h.annotation), "geom" in h && n(6, i = h.geom), "style" in h && n(7, l = h.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    129 && n(1, o = je(s, l));
  }, [s, o, a, f, d, u, i, l];
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
          const a = Vn(i, o, r);
          s[r] ? s[r].p(a, l) : (s[r] = Un(a), s[r].c(), s[r].m(t, null));
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
  return e.$$set = (a) => {
    "annotation" in a && n(0, s = a.annotation), "geom" in a && n(3, i = a.geom), "style" in a && n(4, l = a.style);
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
  return e.$$set = (a) => {
    "annotation" in a && n(0, s = a.annotation), "geom" in a && n(3, i = a.geom), "style" in a && n(4, l = a.style);
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
    m(a, f) {
      Y(a, t, f), G(t, n), G(t, i);
    },
    p(a, [f]) {
      f & /*cssClass*/
      2 && o !== (o = Ze(`a9s-outer ${/*cssClass*/
      a[1]}`) + " svelte-1w0132l") && c(n, "class", o), f & /*computedStyle*/
      8 && s !== (s = /*computedStyle*/
      a[3] ? "display:none;" : void 0) && c(n, "style", s), f & /*d*/
      4 && c(
        n,
        "d",
        /*d*/
        a[2]
      ), f & /*cssClass*/
      2 && l !== (l = Ze(`a9s-inner ${/*cssClass*/
      a[1]}`) + " svelte-1w0132l") && c(i, "class", l), f & /*computedStyle*/
      8 && c(
        i,
        "style",
        /*computedStyle*/
        a[3]
      ), f & /*d*/
      4 && c(
        i,
        "d",
        /*d*/
        a[2]
      ), f & /*annotation*/
      1 && r !== (r = /*annotation*/
      a[0].id) && c(t, "data-id", r);
    },
    i: $,
    o: $,
    d(a) {
      a && R(t);
    }
  };
}
function gr(e, t, n) {
  let o, s, i, { annotation: l } = t, { geom: r } = t, { style: a } = t;
  return e.$$set = (f) => {
    "annotation" in f && n(0, l = f.annotation), "geom" in f && n(4, r = f.geom), "style" in f && n(5, a = f.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    33 && n(3, o = je(l, a)), e.$$.dirty & /*geom*/
    16 && n(2, s = fo(r)), e.$$.dirty & /*geom*/
    16 && n(1, i = r.closed ? "closed" : "open");
  }, [l, i, s, o, r, a];
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
    m(r, a) {
      Y(r, t, a), G(t, n), G(n, o), G(n, i);
    },
    p(r, [a]) {
      a & /*computedStyle*/
      64 && s !== (s = /*computedStyle*/
      r[6] ? "display:none;" : void 0) && c(o, "style", s), a & /*x*/
      16 && c(
        o,
        "x",
        /*x*/
        r[4]
      ), a & /*y*/
      4 && c(
        o,
        "y",
        /*y*/
        r[2]
      ), a & /*w*/
      8 && c(
        o,
        "width",
        /*w*/
        r[3]
      ), a & /*h*/
      2 && c(
        o,
        "height",
        /*h*/
        r[1]
      ), a & /*computedStyle*/
      64 && c(
        i,
        "style",
        /*computedStyle*/
        r[6]
      ), a & /*x*/
      16 && c(
        i,
        "x",
        /*x*/
        r[4]
      ), a & /*y*/
      4 && c(
        i,
        "y",
        /*y*/
        r[2]
      ), a & /*w*/
      8 && c(
        i,
        "width",
        /*w*/
        r[3]
      ), a & /*h*/
      2 && c(
        i,
        "height",
        /*h*/
        r[1]
      ), a & /*rectTransform*/
      32 && c(
        n,
        "transform",
        /*rectTransform*/
        r[5]
      ), a & /*annotation*/
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
  let o, s, i, l, r, a, f, { annotation: d } = t, { geom: u } = t, { style: h } = t;
  return e.$$set = (g) => {
    "annotation" in g && n(0, d = g.annotation), "geom" in g && n(7, u = g.geom), "style" in g && n(8, h = g.style);
  }, e.$$.update = () => {
    e.$$.dirty & /*annotation, style*/
    257 && n(6, o = je(d, h)), e.$$.dirty & /*geom*/
    128 && n(4, { x: s, y: i, w: l, h: r, rot: a } = u, s, (n(2, i), n(7, u)), (n(3, l), n(7, u)), (n(1, r), n(7, u)), (n(9, a), n(7, u))), e.$$.dirty & /*rot, x, w, y, h*/
    542 && n(5, f = (a ?? 0) !== 0 ? `translate(${s + l / 2}, ${i + r / 2}) rotate(${(a ?? 0) * 180 / Math.PI}) translate(${-(s + l / 2)}, ${-(i + r / 2)})` : void 0);
  }, [d, r, i, l, s, f, o, u, h, a];
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
      const { x: a, y: f } = Mo(l, e), d = Le ? 10 : 2, u = t.getAt(a, f, void 0, d);
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
  function r(a, f) {
    var d, u, h, g, p, y;
    return (
      /*selector*/
      ((d = a[45]) == null ? void 0 : d.type) === x.ELLIPSE ? 0 : (
        /*selector*/
        ((u = a[45]) == null ? void 0 : u.type) === x.RECTANGLE ? 1 : (
          /*selector*/
          ((h = a[45]) == null ? void 0 : h.type) === x.POLYGON ? 2 : (
            /*selector*/
            ((g = a[45]) == null ? void 0 : g.type) === x.MULTIPOLYGON ? 3 : (
              /*selector*/
              ((p = a[45]) == null ? void 0 : p.type) === x.POLYLINE ? 4 : (
                /*selector*/
                ((y = a[45]) == null ? void 0 : y.type) === x.LINE ? 5 : -1
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
    m(a, f) {
      ~t && l[t].m(a, f), Y(a, o, f), s = true;
    },
    p(a, f) {
      let d = t;
      t = r(a), t === d ? ~t && l[t].p(a, f) : (n && (be(), H(l[d], 1, 1, () => {
        l[d] = null;
      }), Ee()), ~t ? (n = l[t], n ? n.p(a, f) : (n = l[t] = i[t](a), n.c()), V(n, 1), n.m(o.parentNode, o)) : n = null);
    },
    i(a) {
      s || (V(n), s = true);
    },
    o(a) {
      H(n), s = false;
    },
    d(a) {
      a && R(o), ~t && l[t].d(a);
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
  function r(a, f) {
    return (
      /*editors*/
      a[6] ? 0 : (
        /*tool*/
        a[15] && /*drawingEnabled*/
        a[0] ? 1 : -1
      )
    );
  }
  return ~(t = r(e)) && (n = l[t] = i[t](e)), {
    c() {
      n && n.c(), o = Me();
    },
    m(a, f) {
      ~t && l[t].m(a, f), Y(a, o, f), s = true;
    },
    p(a, f) {
      let d = t;
      t = r(a), t === d ? ~t && l[t].p(a, f) : (n && (be(), H(l[d], 1, 1, () => {
        l[d] = null;
      }), Ee()), ~t ? (n = l[t], n ? n.p(a, f) : (n = l[t] = i[t](a), n.c()), V(n, 1), n.m(o.parentNode, o)) : n = null);
    },
    i(a) {
      s || (V(n), s = true);
    },
    o(a) {
      H(n), s = false;
    },
    d(a) {
      a && R(o), ~t && l[t].d(a);
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
      for (let a = 0; a < s.length; a += 1)
        s[a] && s[a].m(l, r);
      Y(l, t, r), n = true;
    },
    p(l, r) {
      if (r[0] & /*editors, drawingEl, style, transform, $scale, onChangeSelected*/
      8659266) {
        o = ve(
          /*editors*/
          l[6]
        );
        let a;
        for (a = 0; a < o.length; a += 1) {
          const f = Xn(l, o, a);
          s[a] ? (s[a].p(f, r), V(s[a], 1)) : (s[a] = Wn(f), s[a].c(), V(s[a], 1), s[a].m(t.parentNode, t));
        }
        for (be(), a = o.length; a < s.length; a += 1)
          i(a);
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
  ), a = [];
  for (let u = 0; u < r.length; u += 1)
    a[u] = Fn(Hn(e, r, u));
  const f = (u) => H(a[u], 1, 1, () => {
    a[u] = null;
  });
  let d = (
    /*drawingEl*/
    e[8] && zn(e)
  );
  return {
    c() {
      t = C("svg"), n = C("g");
      for (let u = 0; u < a.length; u += 1)
        a[u].c();
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
      for (let g = 0; g < a.length; g += 1)
        a[g] && a[g].m(n, null);
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
          a[g] ? (a[g].p(p, h), V(a[g], 1)) : (a[g] = Fn(p), a[g].c(), V(a[g], 1), a[g].m(n, null));
        }
        for (be(), g = r.length; g < a.length; g += 1)
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
          V(a[h]);
        V(d), s = true;
      }
    },
    o(u) {
      a = a.filter(Boolean);
      for (let h = 0; h < a.length; h += 1)
        H(a[h]);
      H(d), s = false;
    },
    d(u) {
      u && R(t), Ge(a, u), d && d.d(), e[35](null), e[36](null), i = false, Se(l);
    }
  };
}
function Ir(e, t, n) {
  let o, s, i, l, r, a, f, d, u, h, g, p, y = $, S = () => (y(), y = $n(b, (X) => n(18, p = X)), b);
  e.$$.on_destroy.push(() => y());
  let { drawingEnabled: w } = t, { image: A } = t, { preferredDrawingMode: _ } = t, { state: M } = t, { style: P = void 0 } = t, { toolName: I = Ao()[0] } = t, { user: T } = t, { visible: B = true } = t, N = 0;
  const Z = () => n(7, N += 1), j = () => I, v = () => w;
  let E, m, b;
  Be(() => S(n(9, b = oi(A, m))));
  const { hover: L, selection: k, store: O } = M;
  Nt(e, L, (X) => n(16, u = X)), Nt(e, k, (X) => n(33, h = X)), Nt(e, O, (X) => n(17, g = X));
  let F, U;
  const J = (X) => {
    F && O.unobserve(F);
    const fe = X.filter(({ editable: te }) => te).map(({ id: te }) => te);
    fe.length > 0 ? (n(5, U = fe.map((te) => O.getAnnotation(te)).filter((te) => te && Mt(te))), F = (te) => {
      const { updated: ge } = te.changes;
      n(5, U = ge == null ? void 0 : ge.map((ie) => ie.newValue));
    }, O.observe(F, { annotations: fe })) : n(5, U = void 0);
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
    var Ue;
    const { target: te } = X, ge = 10 * 60 * 1e3, ie = ((Ue = te.creator) == null ? void 0 : Ue.id) !== T.id || !te.created || (/* @__PURE__ */ new Date()).getTime() - te.created.getTime() > ge;
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
  }, he = (X) => Mt(X);
  function ne(X) {
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
    "drawingEnabled" in X && n(0, w = X.drawingEnabled), "image" in X && n(25, A = X.image), "preferredDrawingMode" in X && n(26, _ = X.preferredDrawingMode), "state" in X && n(27, M = X.state), "style" in X && n(1, P = X.style), "toolName" in X && n(2, I = X.toolName), "user" in X && n(28, T = X.user), "visible" in X && n(3, B = X.visible);
  }, e.$$.update = () => {
    e.$$.dirty[0] & /*toolName*/
    4 && n(15, { tool: o, opts: s } = So(I) || { tool: void 0, opts: void 0 }, o, (n(32, s), n(2, I))), e.$$.dirty[0] & /*preferredDrawingMode*/
    67108864 | e.$$.dirty[1] & /*opts*/
    2 && n(14, i = (s == null ? void 0 : s.drawingMode) || _), e.$$.dirty[0] & /*svgEl*/
    16 && n(13, l = wr(m)), e.$$.dirty[0] & /*svgEl*/
    16 && n(12, { onPointerDown: r, onPointerUp: a } = Er(m, O), r, (n(11, a), n(4, m))), e.$$.dirty[1] & /*$selection*/
    4 && J(h.selected), e.$$.dirty[0] & /*editableAnnotations*/
    32 && n(6, f = U ? U.map((X) => ({
      annotation: X,
      editor: Vi(X.target.selector)
    })).filter((X) => X.editor) : void 0), e.$$.dirty[0] & /*editors*/
    64 && n(10, d = (X) => f && f.some((fe) => fe.annotation.id === X.id));
  }, [
    w,
    P,
    I,
    B,
    m,
    U,
    f,
    N,
    E,
    b,
    d,
    a,
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
    he,
    ne,
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
      const a = o - n + 1, f = t - n + 1, d = Math.log(a), u = 0.5 * Math.exp(2 * d / 3), h = 0.5 * Math.sqrt(d * u * (a - u) / a) * (f - a / 2 < 0 ? -1 : 1), g = Math.max(n, Math.floor(t - f * u / a + h)), p = Math.min(o, Math.floor(t + (a - f) * u / a + h));
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
        const r = n.children[l], a = n.leaf ? s(r) : r;
        _t(t, a) && (n.leaf ? o.push(r) : qt(t, a) ? this._all(r, o) : i.push(r));
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
    let r, a, f;
    for (; o || i.length; ) {
      if (o || (o = i.pop(), a = i[i.length - 1], r = l.pop(), f = true), o.leaf) {
        const d = Rr(t, o.children, n);
        if (d !== -1)
          return o.children.splice(d, 1), i.push(o), this._condense(i), this;
      }
      !f && !o.leaf && qt(o, s) ? (i.push(o), l.push(r), r = 0, a = o, o = o.children[0]) : a ? (r++, o = a.children[r], f = false) : o = null;
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
    const a = Math.ceil(i / l), f = a * Math.ceil(Math.sqrt(l));
    Zn(t, n, o, f, this.compareMinX);
    for (let d = n; d <= o; d += f) {
      const u = Math.min(d + f - 1, o);
      Zn(t, d, u, a, this.compareMinY);
      for (let h = d; h <= u; h += a) {
        const g = Math.min(h + a - 1, u);
        r.children.push(this._build(t, h, g, s - 1));
      }
    }
    return qe(r, this.toBBox), r;
  }
  _chooseSubtree(t, n, o, s) {
    for (; s.push(n), !(n.leaf || s.length - 1 === o); ) {
      let i = 1 / 0, l = 1 / 0, r;
      for (let a = 0; a < n.children.length; a++) {
        const f = n.children[a], d = zt(f), u = Vr(t, f) - d;
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
      const a = $e(t, 0, r, this.toBBox), f = $e(t, r, o, this.toBBox), d = Ur(a, f), u = zt(a) + zt(f);
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
    let a = pt(l) + pt(r);
    for (let f = n; f < o - n; f++) {
      const d = t.children[f];
      et(l, t.leaf ? i(d) : d), a += pt(l);
    }
    for (let f = o - n - 1; f >= n; f--) {
      const d = t.children[f];
      et(r, t.leaf ? i(d) : d), a += pt(r);
    }
    return a;
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
  return t.observe(({ changes: a }) => {
    n.set((a.created || []).map((f) => f.target), false), (a.deleted || []).forEach((f) => n.remove(f.target)), (a.updated || []).forEach(({ oldValue: f, newValue: d }) => n.update(f.target, d.target));
  }), {
    store: {
      ...t,
      getAt: (a, f, d, u) => {
        const h = n.getAt(a, f, u);
        if (d)
          return h.map((p) => t.getAnnotation(p.annotation)).filter(Boolean).filter(d)[0];
        {
          const g = h[0];
          return g ? t.getAnnotation(g.annotation) : void 0;
        }
      },
      getIntersecting: (a, f, d, u) => n.getIntersecting(a, f, d, u).map((h) => t.getAnnotation(h.annotation)).filter(Boolean)
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
      const l = Math.round(i * t.width / 10), r = Math.round(s * t.height / 10), a = n.getImageData(l, r, 1, 1).data, f = (0.299 * a[0] + 0.587 * a[1] + 0.114 * a[2]) / 255;
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
  }), s = jr(o), { selection: i, store: l } = s, r = Vs(l, o.initialHistory), a = Xs(
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
  }, I = (E) => h.$set({ drawingEnabled: E }), T = (E) => h.$set({ preferredDrawingMode: E }), B = (E) => {
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
    on: a.on,
    off: a.off,
    registerDrawingTool: _,
    registerShapeEditor: M,
    setDrawingEnabled: I,
    setDrawingMode: T,
    setDrawingTool: P,
    setFilter: B,
    setStyle: N,
    setTheme: Z,
    setUser: j,
    setVisible: v,
    element: f,
    state: s
  };
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
