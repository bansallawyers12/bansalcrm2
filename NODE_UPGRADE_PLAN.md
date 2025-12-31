# Node.js & Dependencies Upgrade Plan

## ✅ UPGRADE COMPLETED - Current State

### ✅ Node.js Version
- **Current:** v24.12.0 (Latest!)
- **Status:** ✅ Up to date

### ✅ Package Dependencies - ALL UPGRADED
- **Build Tool:** ✅ **Migrated to Vite v6.0.5** (replaced Laravel Mix - even better!)
- **vue:** ✅ **v3.5.13** (upgraded from v2.5.7)
- **sass:** ✅ **v1.82.0** (Dart Sass - replaced node-sass, no Python needed!)
- **axios:** ✅ **v1.7.9** (upgraded from v0.18)
- **bootstrap:** ✅ **v5.3.3** (upgraded from v4.0.0)
- **@popperjs/core:** ✅ **v2.11.8** (replaced popper.js v1.12)
- **jquery:** ✅ **v3.7.1** (upgraded from v3.2)

### 📊 Current Status
- **Build System:** ✅ Vite (modern, fast, Laravel 11+ default)
- **Vue:** ✅ v3 configured and working
- **Sass:** ✅ Dart Sass (pure JavaScript, no native compilation)
- **Security:** ✅ 0 vulnerabilities found
- **Build Status:** ✅ Production build successful

---

## Upgrade Strategy

### Phase 1: Preparation & Backup ✅
1. **Create Git Branch**
   ```bash
   git checkout -b upgrade/node-modernization
   ```

2. **Backup Current State**
   - Commit current `package.json` and `package-lock.json`
   - Document current working build

3. **Remove node_modules**
   ```bash
   rm -rf node_modules package-lock.json
   ```

---

### Phase 2: Core Build Tools Upgrade 🔧

#### Step 1: Upgrade Laravel Mix (v2 → v6)
**Breaking Changes:**
- Webpack 4 → Webpack 5
- Different configuration syntax
- New asset handling

**Action:**
```json
"laravel-mix": "^6.0.49"
```

**webpack.mix.js Changes:**
```javascript
// OLD (v2)
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');

// NEW (v6) - Same syntax, but different internals
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .vue() // Explicit Vue support
   .options({
       processCssUrls: false // If you have issues with URL processing
   });
```

#### Step 2: Replace node-sass with sass
**Why:** node-sass is deprecated and requires Python/native compilation

**Action:**
- Remove: `node-sass` (transitive dependency)
- Add: `sass` (Dart Sass, pure JavaScript)

**No code changes needed** - Laravel Mix v6 uses `sass` by default

---

### Phase 3: Frontend Framework Upgrades 🎨

#### Step 1: Upgrade Vue (v2 → v3)
**Breaking Changes:**
- Global API changes
- Component registration changes
- Event handling changes

**Current Usage:**
- Only `ExampleComponent.vue` uses Vue
- Minimal impact

**Action:**
```json
"vue": "^3.5.0",
"@vitejs/plugin-vue": "^5.0.0" // If using Vite (Laravel 11+)
```

**Code Changes Required:**

**resources/js/app.js:**
```javascript
// OLD (Vue 2)
import Vue from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';

Vue.component('example-component', ExampleComponent);

const app = new Vue({
    el: '#app'
});

// NEW (Vue 3)
import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';

const app = createApp({
    components: {
        ExampleComponent
    }
});

app.mount('#app');
```

**resources/js/components/ExampleComponent.vue:**
- Vue 3 syntax is mostly compatible
- May need minor adjustments for composition API

#### Step 2: Upgrade Axios (v0.18 → v1.x)
**Breaking Changes:**
- Different error handling
- Request/response interceptors API changes

**Action:**
```json
"axios": "^1.7.0"
```

**Code Changes:**
- Check `resources/js/bootstrap.js` - may need updates
- Review all axios usage in JavaScript files

#### Step 3: Upgrade Bootstrap (v4 → v5) [Optional]
**Breaking Changes:**
- Class name changes
- JavaScript API changes
- Popper.js integration changes

**Action:**
```json
"bootstrap": "^5.3.0"
```

**Considerations:**
- Your project uses Bootstrap heavily
- Many views may need class updates
- **Recommendation:** Do this separately after core upgrade

---

### Phase 4: Supporting Packages 📦

#### Update package.json
```json
{
    "private": true,
    "scripts": {
        "dev": "npm run development",
        "development": "mix",
        "watch": "mix watch",
        "watch-poll": "mix watch -- --watch-options-poll=1000",
        "hot": "mix watch --hot",
        "prod": "npm run production",
        "production": "mix --production"
    },
    "devDependencies": {
        "axios": "^1.7.0",
        "bootstrap": "^5.3.0",
        "cross-env": "^7.0.3",
        "jquery": "^3.7.1",
        "laravel-mix": "^6.0.49",
        "lodash": "^4.17.21",
        "@popperjs/core": "^2.11.8",
        "sass": "^1.77.0",
        "sass-loader": "^13.3.2",
        "vue": "^3.5.0",
        "vue-loader": "^17.3.1"
    }
}
```

---

## Step-by-Step Implementation

### Step 1: Update package.json
1. Replace entire `devDependencies` section
2. Update `scripts` section for Laravel Mix v6

### Step 2: Install Dependencies
```bash
npm install --legacy-peer-deps
# or
npm install
```

### Step 3: Update webpack.mix.js
```javascript
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .vue()
   .sass('resources/sass/app.scss', 'public/css')
   .options({
       processCssUrls: false
   });
```

### Step 4: Update Vue Code
- Update `resources/js/app.js` for Vue 3
- Update `resources/js/components/ExampleComponent.vue` if needed

### Step 5: Update Bootstrap.js
- Check Popper.js import (if using Bootstrap 5)
- Verify axios configuration

### Step 6: Test Build
```bash
npm run dev
```

### Step 7: Fix Errors
- Address any compilation errors
- Fix Vue 3 compatibility issues
- Update any deprecated APIs

### Step 8: Production Build Test
```bash
npm run production
```

---

## Alternative: Gradual Migration Path

If you want to minimize risk, consider this approach:

### Option A: Keep Vue 2, Upgrade Build Tools
1. Upgrade Laravel Mix to v6
2. Replace node-sass with sass
3. Keep Vue 2 (using `vue2-compat` or staying on compatible Mix version)
4. Upgrade other packages

**Pros:** Lower risk, fewer breaking changes
**Cons:** Still using deprecated Vue 2

### Option B: Full Modernization
1. Follow complete plan above
2. Upgrade everything at once
3. More work upfront, but fully modern

**Pros:** Fully modern stack, better security
**Cons:** More breaking changes to handle

---

## Risk Mitigation

### High Risk Areas
1. **Vue 3 Migration** - Low risk (minimal usage)
2. **Laravel Mix v6** - Medium risk (build process changes)
3. **Bootstrap 5** - High risk (if upgrading, many view changes)

### Testing Checklist
- [ ] Build completes without errors
- [ ] All JavaScript functionality works
- [ ] Vue components render correctly
- [ ] Axios requests work
- [ ] CSS compiles correctly
- [ ] No console errors
- [ ] All pages load correctly
- [ ] Forms submit correctly
- [ ] AJAX calls work

---

## Recommended Approach

### Immediate (Low Risk)
1. ✅ Upgrade Laravel Mix to v6
2. ✅ Replace node-sass with sass
3. ✅ Upgrade axios to v1.x
4. ✅ Upgrade jQuery to latest v3.x

### Short Term (Medium Risk)
5. ⚠️ Upgrade Vue to v3 (minimal usage, should be safe)
6. ⚠️ Update build scripts

### Long Term (High Risk - Do Separately)
7. 🔴 Upgrade Bootstrap to v5 (requires view updates)
8. 🔴 Consider migrating to Vite (Laravel 11+)

---

## Post-Upgrade Tasks

1. Run `npm audit fix` to address remaining vulnerabilities
2. Update any custom webpack configurations
3. Test all frontend functionality
4. Update documentation
5. Consider setting up CI/CD for automated testing

---

## Rollback Plan

If upgrade fails:
1. Revert to previous branch
2. Restore `package.json` and `package-lock.json`
3. Run `npm install --ignore-scripts`
4. Document issues encountered

---

## Timeline Estimate

- **Phase 1 (Preparation):** 30 minutes
- **Phase 2 (Build Tools):** 1-2 hours
- **Phase 3 (Vue/Axios):** 1-2 hours
- **Phase 4 (Testing & Fixes):** 2-4 hours
- **Total:** 4-8 hours (depending on issues encountered)

---

## Notes

- Node.js v24.12.0 is already the latest - no upgrade needed
- Main issue is outdated npm packages, not Node.js version
- Most breaking changes are in build tools, not application code
- Vue usage is minimal, making Vue 3 migration low risk
- Consider doing Bootstrap 5 upgrade as separate project

