# Edin Jašarspahić — Portfolio

Modern, responsive portfolio built with vanilla HTML, CSS & JS.
Glassmorphism design, bento-grid layout, dark/light theme toggle.

## Deploy to Cloudflare

```bash
# Install Wrangler (Cloudflare CLI)
npm install -g wrangler

# Login to Cloudflare
npx wrangler login

# Deploy
npx wrangler deploy
```

Your site will be live at `https://edin-portfolio.<your-subdomain>.workers.dev`

## Custom Domain

1. Register a domain (e.g. `edinjasarspahic.dev`) at [Cloudflare Registrar](https://dash.cloudflare.com/domains/register)
2. Add a Custom Domain in your Worker settings
3. Cloudflare handles DNS + SSL automatically

## Local Development

Just open `index.html` in a browser, or use any static server:

```bash
npx serve .
```

## Files

- `index.html` — Main page
- `style.css` — All styling (glassmorphism, animations, responsive)
- `script.js` — Theme toggle, scroll animations, skill bars, counters, form
- `wrangler.toml` — Cloudflare Workers config
