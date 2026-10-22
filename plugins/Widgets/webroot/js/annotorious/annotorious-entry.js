// Core library — re-export everything (createImageAnnotator, etc.)
export * from "@annotorious/annotorious";

// Plugins — explicit named exports to avoid collisions with core
export { mountPlugin as mountAnnotoriousTools } from "@annotorious/plugin-tools";

// Styles — esbuild collects these into a sibling .css file
import "@annotorious/annotorious/annotorious.css";
import "@annotorious/plugin-tools/annotorious-plugin-tools.css";
