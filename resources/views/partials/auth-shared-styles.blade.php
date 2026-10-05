<style>
    .auth-page {
        max-width: 440px;
        margin: 0 auto;
        padding: 28px 16px 56px;
    }
    .auth-head {
        text-align: center;
        margin-bottom: 28px;
    }
    .auth-head .auth-logo {
        height: 44px;
        width: auto;
        display: inline-block;
        margin-bottom: 20px;
    }
    .auth-head h1 {
        margin: 0 0 10px;
        font-size: clamp(1.45rem, 1.2rem + 1vw, 1.85rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--eduit-brand);
    }
    .auth-head .auth-sub {
        margin: 0;
        font-size: 1rem;
        color: var(--eduit-muted);
        font-weight: 500;
    }

    .auth-card {
        background: #fff;
        border: 1px solid var(--eduit-line);
        border-radius: 16px;
        padding: 28px 24px 26px;
        box-shadow: var(--eduit-shadow);
    }

    .auth-field { margin-bottom: 20px; }
    .auth-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 700;
        color: var(--eduit-text);
    }
    .auth-req { color: #dc2626; font-weight: 800; margin-left: 2px; }

    .auth-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .auth-input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
        display: flex;
        z-index: 1;
    }

    .auth-input {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid var(--eduit-line);
        border-radius: 12px;
        padding: 12px 14px 12px 44px;
        font-size: 15px;
        font-family: inherit;
        color: var(--eduit-text);
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }
    .auth-input-wrap--password .auth-input { padding-right: 88px; }
    .auth-input-wrap--select .auth-select {
        padding-left: 44px;
        padding-right: 40px;
        min-height: 48px;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }
    .auth-input::placeholder { color: #9ca3af; }
    .auth-input:focus {
        outline: none;
        border-color: var(--eduit-brand);
        box-shadow: 0 0 0 3px rgba(26, 99, 152, .18);
    }
    .auth-input.is-readonly,
    .auth-input:read-only {
        background: #f1f5f9;
        color: var(--eduit-muted);
        border-color: #e2e8f0;
        cursor: not-allowed;
    }
    .auth-input.is-readonly:focus,
    .auth-input:read-only:focus {
        border-color: #e2e8f0;
        box-shadow: none;
    }
    .auth-input-wrap--readonly .auth-input-icon {
        color: #cbd5e1;
    }

    .auth-toggle-pw {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        padding: 8px;
        cursor: pointer;
        border-radius: 8px;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .auth-toggle-pw:hover { color: var(--eduit-brand); background: rgba(26, 99, 152, .08); }

    .auth-row-link {
        display: flex;
        justify-content: flex-end;
        margin-top: -8px;
        margin-bottom: 22px;
    }
    .auth-link {
        font-size: 14px;
        font-weight: 600;
        color: var(--eduit-brand);
        text-decoration: none;
    }
    .auth-link:hover { text-decoration: underline; }

    .auth-btn-submit {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 20px;
        border: none;
        border-radius: 14px;
        font-size: 16px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        background: var(--eduit-brand);
        color: #fff;
        box-shadow: 0 8px 20px -6px rgba(26, 99, 152, .45);
        transition: background .15s, transform .12s, box-shadow .15s;
    }
    .auth-btn-submit:hover {
        background: #2e87c7;
        transform: translateY(-1px);
    }
    .auth-btn-submit svg { flex-shrink: 0; }

    .auth-divider {
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 24px 0 20px;
        color: var(--eduit-muted);
        font-size: 13px;
        font-weight: 600;
    }
    .auth-divider::before,
    .auth-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--eduit-line);
    }

    .auth-btn-google {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 12px 18px;
        border-radius: 14px;
        border: 1px solid var(--eduit-line);
        background: #fff;
        font-size: 15px;
        font-weight: 600;
        font-family: inherit;
        color: var(--eduit-text);
        cursor: pointer;
        text-decoration: none;
        transition: background .15s, border-color .15s, box-shadow .15s;
    }
    .auth-btn-google:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
    }

    .auth-google-icon {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .auth-footer-card {
        text-align: center;
        margin-top: 22px;
        font-size: 15px;
        color: var(--eduit-muted);
    }
    .auth-footer-card a {
        font-weight: 700;
        color: var(--eduit-brand);
        text-decoration: none;
    }
    .auth-footer-card a:hover { text-decoration: underline; }

    .auth-legal {
        text-align: center;
        margin-top: 32px;
        font-size: 13px;
        line-height: 1.55;
        color: var(--eduit-muted);
        max-width: 420px;
        margin-left: auto;
        margin-right: auto;
    }
    .auth-legal a {
        color: var(--eduit-text);
        text-decoration: underline;
        font-weight: 500;
    }
    .auth-legal a:hover { color: var(--eduit-brand); }

    .auth-status {
        margin-bottom: 16px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
        font-weight: 600;
        font-size: 14px;
    }
    .auth-error {
        margin-bottom: 16px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #fecaca;
        background: #fff1f2;
        color: #b91c1c;
        font-weight: 600;
        font-size: 14px;
    }
</style>
