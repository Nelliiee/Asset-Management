<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A clearer way to track, assign, and care for every company asset.">
    <title>QCU | SchoLab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #101a3a;
            --muted: #53617c;
            --white: #ffffff;
            --blue: #101c78;
            --blue-bright: #121de3;
            --red: #d91622;
            --gold: #b27a05;
            --yellow: #ffe500;
            --line: #d8deeb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--white);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
        }

        a { color: inherit; }

        .shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 28px;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 96px;
            border-bottom: 4px solid var(--gold);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--blue);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.18rem;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: .02em;
        }

        .brand-mark {
            width: 46px;
            height: 46px;
            border: 2px solid var(--gold);
            border-radius: 50%;
            object-fit: cover;
        }

        .nav-links, .nav-actions { display: flex; align-items: center; gap: 26px; }
        .nav-links a { color: var(--blue); font-size: .9rem; font-weight: 700; text-decoration: none; }
        .nav-links a:hover { color: var(--red); }
        .login { color: var(--blue); font-size: .9rem; font-weight: 700; text-decoration: none; }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 46px;
            padding: 0 19px;
            border: 1px solid transparent;
            border-radius: 3px;
            font-size: .9rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .button:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(16, 28, 120, .2); }
        .button-primary { background: var(--red); color: var(--white); }
        .button-light { border-color: var(--gold); background: var(--white); color: var(--blue); }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(360px, .9fr);
            gap: clamp(40px, 8vw, 100px);
            align-items: center;
            position: relative;
            padding: 100px 0 112px;
        }

        .hero::before { position: absolute; top: 0; right: calc((1180px - 100vw) / 2); bottom: 0; left: calc((1180px - 100vw) / 2); z-index: -1; background: var(--blue); content: ''; }
        .eyebrow { color: var(--yellow); font-size: .75rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        h1, h2 { margin: 0; font-family: 'Space Grotesk', sans-serif; letter-spacing: -.055em; }
        h1 { max-width: 640px; margin-top: 17px; color: var(--white); font-size: clamp(3.2rem, 6vw, 5.8rem); line-height: .98; }
        .hero-copy { max-width: 500px; margin: 26px 0 32px; color: #dce3ff; font-size: 1.08rem; line-height: 1.7; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; }

        .preview-wrap { position: relative; }
        .preview-wrap::before { position: absolute; inset: -20px -24px 22px 24px; z-index: 0; border: 2px solid var(--gold); border-radius: 3px; content: ''; }
        .preview { position: relative; z-index: 1; overflow: hidden; border: 3px solid var(--gold); border-radius: 3px; background: var(--white); box-shadow: 12px 12px 0 var(--red); }
        .preview-top { display: flex; justify-content: space-between; align-items: center; padding: 18px 20px; border-bottom: 3px solid var(--gold); background: #f5f7ff; }
        .preview-title { font-family: 'Space Grotesk', sans-serif; font-size: .9rem; font-weight: 700; }
        .dots { display: flex; gap: 5px; }
        .dots i { width: 6px; height: 6px; border-radius: 50%; background: var(--gold); }
        .preview-body { padding: 20px; }
        .metric-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 9px; margin-bottom: 20px; }
        .metric { padding: 13px 12px; border-radius: 3px; background: #e8ebff; }
        .metric small { display: block; color: var(--muted); font-size: .67rem; }
        .metric strong { display: block; margin-top: 5px; font-family: 'Space Grotesk', sans-serif; font-size: 1.35rem; }
        .metric:nth-child(2) { background: #ffe5e7; }
        .metric:nth-child(3) { background: #fff7b8; }
        .asset-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; border-top: 1px solid var(--line); font-size: .78rem; }
        .asset-name { display: flex; align-items: center; gap: 10px; font-weight: 700; }
        .asset-icon { display: grid; width: 28px; height: 28px; place-items: center; border-radius: 50%; background: var(--blue); color: var(--yellow); font-size: .7rem; font-weight: 700; }
        .status { padding: 5px 8px; border-radius: 3px; background: #e8ebff; color: var(--blue); font-size: .66rem; font-weight: 700; }
        .status.warn { background: #ffe5e7; color: var(--red); }

        .about { display: grid; grid-template-columns: .7fr 1fr; gap: 80px; padding: 84px 0 100px; border-top: 4px solid var(--gold); }
        .about h2 { max-width: 360px; font-size: clamp(2rem, 4vw, 3.5rem); line-height: 1.04; }
        .about-copy { max-width: 570px; color: var(--muted); line-height: 1.75; }
        .about-copy p { margin: 0 0 20px; }
        .principles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 32px; }
        .principle { padding-top: 16px; border-top: 3px solid var(--red); }
        .principle strong { display: block; margin-bottom: 8px; font-family: 'Space Grotesk', sans-serif; }
        .principle span { color: var(--muted); font-size: .84rem; line-height: 1.5; }

        footer { padding: 22px 0 28px; border-top: 4px solid var(--blue); color: var(--muted); font-size: .8rem; }

        @media (max-width: 760px) {
            .shell { padding: 0 20px; }
            .nav { min-height: 72px; }
            .nav-links { display: none; }
            .nav-actions { gap: 14px; }
            .nav-actions .button { min-height: 40px; padding: 0 13px; }
            .hero { grid-template-columns: 1fr; padding: 58px 0 72px; }
            h1 { font-size: clamp(3rem, 15vw, 4.6rem); }
            .preview-wrap { margin: 12px 12px 0 0; }
            .about { grid-template-columns: 1fr; gap: 26px; padding: 64px 0 72px; }
        }

        @media (max-width: 430px) {
            .metric-row { gap: 6px; }
            .metric { padding: 11px 8px; }
            .principles { grid-template-columns: 1fr; gap: 22px; }
        }
    </style>
</head>
<body>
    <header class="shell nav">
        <a class="brand" href="{{ url('/') }}"><img class="brand-mark" src="{{ asset('images/cropped-logo.webp') }}" alt=""> SchoLab</a>
        <nav class="nav-links" aria-label="Main navigation">
            <a href="#about">About</a>
        </nav>
        <div class="nav-actions">
            <a class="login" href="/admin/login">Log in</a>
            <a class="button button-primary" href="/admin/register">Sign up <span aria-hidden="true">&#8594;</span></a>
        </div>
    </header>

    <main class="shell">
        <section class="hero">
            <div>
                <div class="eyebrow">Asset operations, made clear</div>
                <h1>Know what you have. Know where it is.</h1>
                <p class="hero-copy">SchoLab gives your team one calm, reliable place to track equipment, understand its status, and make better decisions with less chasing.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="/admin/register">Start managing <span aria-hidden="true">&#8594;</span></a>
                    <a class="button button-light" href="#about">Why SchoLab</a>
                </div>
            </div>

            <div class="preview-wrap" aria-label="Asset dashboard preview">
                <div class="preview">
                    <div class="preview-top"><span class="preview-title">Operations overview</span><span class="dots"><i></i><i></i><i></i></span></div>
                    <div class="preview-body">
                        <div class="metric-row">
                            <div class="metric"><small>Total assets</small><strong>248</strong></div>
                            <div class="metric"><small>Assigned</small><strong>186</strong></div>
                            <div class="metric"><small>Available</small><strong>42</strong></div>
                        </div>
                        <div class="asset-row"><span class="asset-name"><span class="asset-icon">LT</span> Lenovo ThinkPad</span><span class="status">Assigned</span></div>
                        <div class="asset-row"><span class="asset-name"><span class="asset-icon">MN</span> Meeting display</span><span class="status warn">Maintenance</span></div>
                        <div class="asset-row"><span class="asset-name"><span class="asset-icon">KB</span> Keycard batch</span><span class="status">Available</span></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about" id="about">
            <h2>Less guesswork. More ownership.</h2>
            <div class="about-copy">
                <p>From the first purchase to its next assignment, every asset has a story. SchoLab helps your team keep that story visible, current, and useful.</p>
                <p>Whether you are running a growing office, a busy classroom, or a distributed operation, your inventory should feel like a source of confidence, not another spreadsheet to maintain.</p>
                <div class="principles">
                    <div class="principle"><strong>See the whole picture</strong><span>A live overview of what is assigned, available, and needing attention.</span></div>
                    <div class="principle"><strong>Move with context</strong><span>Find the details behind every item before making the next handoff.</span></div>
                    <div class="principle"><strong>Stay accountable</strong><span>Keep ownership and status clear as your operation changes.</span></div>
                </div>
            </div>
        </section>
    </main>

    <footer class="shell">SchoLab · A clearer way to care for what keeps work moving.</footer>
</body>
</html>
