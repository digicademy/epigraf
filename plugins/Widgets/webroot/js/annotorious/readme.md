# Annotorious Bundle

See https://annotorious.dev/


## 1. Install the package
```
npm install @annotorious/annotorious
npm install @annotorious/plugin-tools
```

## 2. Create a tiny entry file

`annotorious-entry.js`:

```js
// Core library — re-export everything (createImageAnnotator, etc.)
export * from "@annotorious/annotorious";

// Plugins — explicit named exports to avoid collisions with core
export { mountPlugin as mountToolsPlugin } from "@annotorious/plugin-tools";

// Styles — esbuild collects these into a sibling .css file
import "@annotorious/annotorious/annotorious.css";
import "@annotorious/plugin-tools/annotorious-plugin-tools.css";
```

## 3. Bundle it
```
npx esbuild --bundle --format=esm --outfile=annotorious.bundle.js annotorious-entry.js
```

Move the resulting js and css files to the respective webroot folders.
