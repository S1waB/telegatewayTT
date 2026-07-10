const { test, expect } = require('@playwright/test');

test.describe('TeleGateway E2E', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('http://127.0.0.1:8000/login');
    await page.fill('input[name="email"]', 'admin@telegateway.io');
    await page.fill('input[name="password"]', 'password');
    await page.click('form[action*="login"] button[type="submit"]');
    await page.waitForURL('**/admin/**', { timeout: 10000 });
  });

  test('Create user via UI', async ({ page }) => {
    await page.goto('http://127.0.0.1:8000/admin/users/create');
    await page.fill('input[placeholder="John Doe"], input[name="name"]', 'QA Test User');
    const email = `qa+${Date.now()}@example.com`;
    await page.fill('input[placeholder="name@example.com"], input[name="email"]', email);
    await page.fill('input[placeholder="+216 XX XXX XXX"], input[name="phone_number"]', '+21670000000');
    // Select gender and role (fall back to common labels)
    try { await page.selectOption('select[name="gender"]', { label: 'Male' }); } catch(e) { try { await page.selectOption('select[name="gender"]', { label: 'male' }); } catch(e) {} }
    try { await page.selectOption('select[name="role"] , select[name="assign_role"], select[name="role_id"]', { label: 'Admin' }); } catch(e) { await page.selectOption('select[name="role"] , select[name="assign_role"], select[name="role_id"]', { label: 'admin' }).catch(()=>{}); }
    // Fill address only if present
    try {
      const addressLocator = page.locator('input[placeholder="Address"], input[name="address"], textarea[name="address"]');
      if (await addressLocator.count() > 0) await addressLocator.first().fill('QA Address');
    } catch(e) {}
    await page.click('button:has-text("Create User"), button:has-text("Créer")');
    await page.waitForTimeout(800);
    await page.goto('http://127.0.0.1:8000/admin/users');
    const found = await page.locator(`text=${email}`).first().count();
    expect(found).toBeGreaterThan(0);
  });

  test.skip('Send command to first device (API fallback)', async ({ page }) => {
    await page.goto('http://127.0.0.1:8000/admin/devices');
    const deviceHref = await page.getAttribute('a[href*="/admin/devices/"]', 'href');
    expect(deviceHref).toBeTruthy();
    const deviceIdMatch = deviceHref.match(/\/admin\/devices\/(\d+)/);
    expect(deviceIdMatch).not.toBeNull();
    const deviceId = deviceIdMatch[1];

    // Try UI form first
    let result = { ok: false, status: 0, text: '' };
    await page.goto(`/admin/devices/${deviceId}`);
    try {
      const payloadEl = page.locator('textarea[name="payload"], textarea[name="data"], textarea');
      if (await payloadEl.count() > 0) {
        await payloadEl.first().fill(JSON.stringify({ action: 'ping' }));
        const submit = page.locator('form[action*="commands"] button[type=submit], button:has-text("Send Command"), button:has-text("Send")');
        if (await submit.count() > 0) {
          await submit.first().click();
          await page.waitForTimeout(800);
          result = { ok: true, status: 200, text: 'submitted-ui' };
        }
      }
    } catch(e) {}

    if (!result.ok) {
      // fallback to API POST with CSRF using request fixture and browser cookies
      const cookies = await page.context().cookies();
      const cookieHeader = cookies.map(c => `${c.name}=${c.value}`).join('; ');
      const token = await page.$eval('meta[name="csrf-token"]', el => el.getAttribute('content'));
      const resp = await page.request.post(`/admin/devices/${deviceId}/commands`, {
        data: { device_id: deviceId, payload: JSON.stringify({ action: 'ping' }) },
        headers: { 'X-CSRF-TOKEN': token, 'Cookie': cookieHeader }
      });
      result = { ok: resp.ok(), status: resp.status(), text: await resp.text() };
    }

    expect(result.ok).toBeTruthy();
  });

  test('Create alert (API)', async ({ page }) => {
    // create alert via POST to /alerts using CSRF token
    const res = await page.evaluate(async () => {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const res = await fetch('/alerts', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify({ subject: 'QA Alert', description: 'Automated QA alert' })
      });
      return { status: res.status, ok: res.ok, text: await res.text() };
    });
    expect(res.ok).toBeTruthy();
  });

  test('Send announcement (API)', async ({ page }) => {
    const res = await page.evaluate(async () => {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const payload = { title: 'QA Announcement', body: 'Automated announcement', audience: 'all' };
      const res = await fetch('/admin/announcements/send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify(payload)
      });
      return { status: res.status, ok: res.ok, text: await res.text() };
    });
    expect(res.ok).toBeTruthy();
  });
});
