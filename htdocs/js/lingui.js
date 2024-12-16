var e,r,t,a,n,o,s={498:(e,r)=>{var t;Object.defineProperty(r,"__esModule",{value:!0}),r.errorMessages=r.ErrorType=void 0,function(e){e.MalformedUnicode="MALFORMED_UNICODE",e.MalformedHexadecimal="MALFORMED_HEXADECIMAL",e.CodePointLimit="CODE_POINT_LIMIT",e.OctalDeprecation="OCTAL_DEPRECATION",e.EndOfString="END_OF_STRING"}(t=r.ErrorType||(r.ErrorType={})),r.errorMessages=new Map([[t.MalformedUnicode,"malformed Unicode character escape sequence"],[t.MalformedHexadecimal,"malformed hexadecimal character escape sequence"],[t.CodePointLimit,"Unicode codepoint must not be greater than 0x10FFFF in escape sequence"],[t.OctalDeprecation,'"0"-prefixed octal literals and octal escape sequences are deprecated; for octal literals use the "0o" prefix instead'],[t.EndOfString,"malformed escape sequence at end of string"]])},12:(e,r,t)=>{Object.defineProperty(r,"__esModule",{value:!0}),r.unraw=r.errorMessages=r.ErrorType=void 0;const a=t(498);function n(e,r,t){const n=function(e){return e.match(/[^a-f0-9]/i)?NaN:parseInt(e,16)}(e);if(Number.isNaN(n)||void 0!==t&&t!==e.length)throw new SyntaxError(a.errorMessages.get(r));return n}function o(e,r){const t=n(e,a.ErrorType.MalformedUnicode,4);if(void 0!==r){const e=n(r,a.ErrorType.MalformedUnicode,4);return String.fromCharCode(t,e)}return String.fromCharCode(t)}Object.defineProperty(r,"ErrorType",{enumerable:!0,get:function(){return a.ErrorType}}),Object.defineProperty(r,"errorMessages",{enumerable:!0,get:function(){return a.errorMessages}});const s=new Map([["b","\b"],["f","\f"],["n","\n"],["r","\r"],["t","\t"],["v","\v"],["0","\0"]]);const i=/\\(?:(\\)|x([\s\S]{0,2})|u(\{[^}]*\}?)|u([\s\S]{4})\\u([^{][\s\S]{0,3})|u([\s\S]{0,4})|([0-3]?[0-7]{1,2})|([\s\S])|$)/g;function l(e,r=!1){return e.replace(i,(function(e,t,i,l,c,u,d,f,h){if(void 0!==t)return"\\";if(void 0!==i)return function(e){const r=n(e,a.ErrorType.MalformedHexadecimal,2);return String.fromCharCode(r)}(i);if(void 0!==l)return function(e){if("{"!==(r=e).charAt(0)||"}"!==r.charAt(r.length-1))throw new SyntaxError(a.errorMessages.get(a.ErrorType.MalformedUnicode));var r;const t=n(e.slice(1,-1),a.ErrorType.MalformedUnicode);try{return String.fromCodePoint(t)}catch(e){throw e instanceof RangeError?new SyntaxError(a.errorMessages.get(a.ErrorType.CodePointLimit)):e}}(l);if(void 0!==c)return o(c,u);if(void 0!==d)return o(d);if("0"===f)return"\0";if(void 0!==f)return function(e,r=!1){if(r)throw new SyntaxError(a.errorMessages.get(a.ErrorType.OctalDeprecation));const t=parseInt(e,8);return String.fromCharCode(t)}(f,!r);if(void 0!==h)return m=h,s.get(m)||m;var m;throw new SyntaxError(a.errorMessages.get(a.ErrorType.EndOfString))}))}r.unraw=l,r.default=l},494:(e,r,t)=>{t.a(e,(async(e,a)=>{try{t.d(r,{a:()=>n.ag});var n=t(555);
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */const{messages:e}=await t.e(190).then(t.bind(t,455));n.ag.load("de_DE",e),n.ag.activate("de_DE"),a()}catch(e){a(e)}}),1)},555:(e,r,t)=>{t.d(r,{ag:()=>b});var a=t(12);const n=e=>"string"==typeof e,o=new Map;function s(e){return[...Array.isArray(e)?e:[e],"en"]}function i(e,r,t){const a=s(e);return u((()=>d("date",a,t)),(()=>new Intl.DateTimeFormat(a,t))).format(n(r)?new Date(r):r)}function l(e,r,t){const a=s(e);return u((()=>d("number",a,t)),(()=>new Intl.NumberFormat(a,t))).format(r)}function c(e,r,t,{offset:a=0,...n}){const o=s(e),i=r?u((()=>d("plural-ordinal",o)),(()=>new Intl.PluralRules(o,{type:"ordinal"}))):u((()=>d("plural-cardinal",o)),(()=>new Intl.PluralRules(o,{type:"cardinal"})));return n[t]??n[i.select(t-a)]??n.other}function u(e,r){const t=e();let a=o.get(t);return a||(a=r(),o.set(t,a)),a}function d(e,r,t){return`${e}-${r.join("-")}-${JSON.stringify(t)}`}const f=/\\u[a-fA-F0-9]{4}|\\x[a-fA-F0-9]{2}/g;function h(e,r,t){return(o,s={})=>{const u=((e,r,t={})=>{r=r||e;const a=e=>n(e)?t[e]||{style:e}:e,o=(e,n)=>{const o=Object.keys(t).length?a("number"):{},s=l(r,e,o);return n.replace("#",s)};return{plural:(e,t)=>{const{offset:a=0}=t,n=c(r,!1,e,t);return o(e-a,n)},selectordinal:(e,t)=>{const{offset:a=0}=t,n=c(r,!0,e,t);return o(e-a,n)},select:(e,r)=>r[e]??r.other,number:(e,t)=>l(r,e,a(t)),date:(e,t)=>i(r,e,a(t)),undefined:e=>e}})(r,t,s),d=e=>Array.isArray(e)?e.reduce(((e,r)=>{if(n(r))return e+r;const[t,a,s]=r;let i={};null==s||n(s)?i=s:Object.keys(s).forEach((e=>{i[e]=d(s[e])}));const l=u[a](o[t],i);return null==l?e:e+l}),""):e,h=d(e);return n(h)&&f.test(h)?a(h.trim()):n(h)?h.trim():h}}var m=Object.defineProperty,p=(e,r,t)=>(((e,r,t)=>{r in e?m(e,r,{enumerable:!0,configurable:!0,writable:!0,value:t}):e[r]=t})(e,"symbol"!=typeof r?r+"":r,t),t);class g{constructor(){p(this,"_events",{})}on(e,r){return this._hasEvent(e)||(this._events[e]=[]),this._events[e].push(r),()=>this.removeListener(e,r)}removeListener(e,r){if(!this._hasEvent(e))return;const t=this._events[e].indexOf(r);~t&&this._events[e].splice(t,1)}emit(e,...r){this._hasEvent(e)&&this._events[e].map((e=>e.apply(this,r)))}_hasEvent(e){return Array.isArray(this._events[e])}}var _=Object.defineProperty,v=(e,r,t)=>(((e,r,t)=>{r in e?_(e,r,{enumerable:!0,configurable:!0,writable:!0,value:t}):e[r]=t})(e,"symbol"!=typeof r?r+"":r,t),t);class y extends g{constructor(e){super(),v(this,"_locale"),v(this,"_locales"),v(this,"_localeData"),v(this,"_messages"),v(this,"_missing"),v(this,"t",this._.bind(this)),this._messages={},this._localeData={},null!=e.missing&&(this._missing=e.missing),null!=e.messages&&this.load(e.messages),null!=e.localeData&&this.loadLocaleData(e.localeData),null==e.locale&&null==e.locales||this.activate(e.locale,e.locales)}get locale(){return this._locale}get locales(){return this._locales}get messages(){return this._messages[this._locale]??{}}get localeData(){return this._localeData[this._locale]??{}}_loadLocaleData(e,r){null==this._localeData[e]?this._localeData[e]=r:Object.assign(this._localeData[e],r)}loadLocaleData(e,r){null!=r?this._loadLocaleData(e,r):Object.keys(e).forEach((r=>this._loadLocaleData(r,e[r]))),this.emit("change")}_load(e,r){null==this._messages[e]?this._messages[e]=r:Object.assign(this._messages[e],r)}load(e,r){null!=r?this._load(e,r):Object.keys(e).forEach((r=>this._load(r,e[r]))),this.emit("change")}loadAndActivate({locale:e,locales:r,messages:t}){this._locale=e,this._locales=r||void 0,this._messages[this._locale]=t,this.emit("change")}activate(e,r){this._locale=e,this._locales=r,this.emit("change")}_(e,r={},{message:t,formats:a}={}){n(e)||(r=e.values||r,t=e.message,e=e.id);const o=!this.messages[e],s=this._missing;if(s&&o)return"function"==typeof s?s(this._locale,e):s;o&&this.emit("missing",{id:e,locale:this._locale});let i=this.messages[e]||t||e;return n(i)&&f.test(i)?JSON.parse(`"${i}"`):n(i)?i:h(i,this._locale,this._locales)(r,a)}date(e,r){return i(this._locales||this._locale,e,r)}number(e,r){return l(this._locales||this._locale,e,r)}}const b=function(e={}){return new y(e)}()}},i={};function l(e){var r=i[e];if(void 0!==r)return r.exports;var t=i[e]={exports:{}};return s[e](t,t.exports,l),t.exports}l.m=s,e="function"==typeof Symbol?Symbol("webpack queues"):"__webpack_queues__",r="function"==typeof Symbol?Symbol("webpack exports"):"__webpack_exports__",t="function"==typeof Symbol?Symbol("webpack error"):"__webpack_error__",a=e=>{e&&e.d<1&&(e.d=1,e.forEach((e=>e.r--)),e.forEach((e=>e.r--?e.r++:e())))},l.a=(n,o,s)=>{var i;s&&((i=[]).d=-1);var l,c,u,d=new Set,f=n.exports,h=new Promise(((e,r)=>{u=r,c=e}));h[r]=f,h[e]=e=>(i&&e(i),d.forEach(e),h.catch((e=>{}))),n.exports=h,o((n=>{var o;l=(n=>n.map((n=>{if(null!==n&&"object"==typeof n){if(n[e])return n;if(n.then){var o=[];o.d=0,n.then((e=>{s[r]=e,a(o)}),(e=>{s[t]=e,a(o)}));var s={};return s[e]=e=>e(o),s}}var i={};return i[e]=e=>{},i[r]=n,i})))(n);var s=()=>l.map((e=>{if(e[t])throw e[t];return e[r]})),c=new Promise((r=>{(o=()=>r(s)).r=0;var t=e=>e!==i&&!d.has(e)&&(d.add(e),e&&!e.d&&(o.r++,e.push(o)));l.map((r=>r[e](t)))}));return o.r?c:s()}),(e=>(e?u(h[t]=e):c(f),a(i)))),i&&i.d<0&&(i.d=0)},l.d=(e,r)=>{for(var t in r)l.o(r,t)&&!l.o(e,t)&&Object.defineProperty(e,t,{enumerable:!0,get:r[t]})},l.f={},l.e=e=>Promise.all(Object.keys(l.f).reduce(((r,t)=>(l.f[t](e,r),r)),[])),l.u=e=>"../msg/de_DE.js",l.o=(e,r)=>Object.prototype.hasOwnProperty.call(e,r),n={},o="Epigraf Widgets:",l.l=(e,r,t,a)=>{if(n[e])n[e].push(r);else{var s,i;if(void 0!==t)for(var c=document.getElementsByTagName("script"),u=0;u<c.length;u++){var d=c[u];if(d.getAttribute("src")==e||d.getAttribute("data-webpack")==o+t){s=d;break}}s||(i=!0,(s=document.createElement("script")).type="module",s.charset="utf-8",s.timeout=120,l.nc&&s.setAttribute("nonce",l.nc),s.setAttribute("data-webpack",o+t),s.src=e),n[e]=[r];var f=(r,t)=>{s.onerror=s.onload=null,clearTimeout(h);var a=n[e];if(delete n[e],s.parentNode&&s.parentNode.removeChild(s),a&&a.forEach((e=>e(t))),r)return r(t)},h=setTimeout(f.bind(null,void 0,{type:"timeout",target:s}),12e4);s.onerror=f.bind(null,s.onerror),s.onload=f.bind(null,s.onload),i&&document.head.appendChild(s)}},l.p="/",(()=>{var e={434:0};l.f.j=(r,t)=>{var a=l.o(e,r)?e[r]:void 0;if(0!==a)if(a)t.push(a[2]);else{var n=new Promise(((t,n)=>a=e[r]=[t,n]));t.push(a[2]=n);var o=l.p+l.u(r),s=new Error;l.l(o,(t=>{if(l.o(e,r)&&(0!==(a=e[r])&&(e[r]=void 0),a)){var n=t&&("load"===t.type?"missing":t.type),o=t&&t.target&&t.target.src;s.message="Loading chunk "+r+" failed.\n("+n+": "+o+")",s.name="ChunkLoadError",s.type=n,s.request=o,a[1](s)}}),"chunk-"+r,r)}};var r=(r,t)=>{var a,n,[o,s,i]=t,c=0;if(o.some((r=>0!==e[r]))){for(a in s)l.o(s,a)&&(l.m[a]=s[a]);if(i)i(l)}for(r&&r(t);c<o.length;c++)n=o[c],l.o(e,n)&&e[n]&&e[n][0](),e[n]=0},t=self.webpackChunkEpigraf_Widgets=self.webpackChunkEpigraf_Widgets||[];t.forEach(r.bind(null,0)),t.push=r.bind(null,t.push.bind(t))})();var c=l(494),u=(c=await c).a;export{u as i18n};