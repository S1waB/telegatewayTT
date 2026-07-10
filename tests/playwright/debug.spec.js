const { test, expect } = require('@playwright/test');

test('Debug Login', async ({ page }) => {
  page.on('console', msg => console.log('BROWSER CONSOLE:', msg.text()));
  page.on('pageerror', err => console.log('BROWSER ERROR:', err.message));
  page.on('response', response => {
    console.log(`HTTP ${response.status()} ${response.request().method()} ${response.url()}`);
  });

  console.log('Navigating to login...');
  await page.goto('http://127.0.0.1:8000/login');
  
  console.log('Filling form...');
  await page.fill('input[name="email"]', 'admin@telegateway.io');
  await page.fill('input[name="password"]', 'password');
  
  console.log('Submitting...');
  const navigationPromise = page.waitForNavigation({ waitUntil: 'networkidle' }).catch(e => console.log('Navigation error/timeout:', e.message));
  await page.click('form[action*="login"] button[type="submit"]');
  await navigationPromise;

  console.log('Final URL:', page.url());
  const bodyText = await page.textContent('body');
  console.log('Body snippet:', bodyText.substring(0, 500));
});
