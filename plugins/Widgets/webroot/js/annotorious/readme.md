# Annotorious Bundle

See https://annotorious.dev/


## 1. Install the package
```
npm install @annotorious/annotorious
```

## 2. Create a tiny entry file
```
echo 'export * from "@annotorious/annotorious";' > annotorious-entry.js
```

## 3. Bundle it
```
npx esbuild --bundle --format=esm --outfile=annotorious.bundle.js annotorious-entry.js
```

## 4. Clean up
```
rm annotorious-entry.js
```
