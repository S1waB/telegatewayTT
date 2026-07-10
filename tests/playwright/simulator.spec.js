const { test, expect } = require('@playwright/test');

test.describe('IoT Gateway Simulator UI', () => {
  
  test('verify simulator UI elements load', async ({ page }) => {
    await page.goto('/iot-simulator.html');
    
    // Check title
    await expect(page).toHaveTitle('IoT Gateway Simulator');
    
    // Check header
    await expect(page.locator('.header h1')).toHaveText('IoT Gateway Simulator');
    
    // Check default Gateway URL
    await expect(page.locator('input#gatewayUrl')).toHaveValue('/api/gateway/receive');
  });

  test('Lighting Relay interactivity and telemetry sending', async ({ page }) => {
    await page.goto('/iot-simulator.html');

    // Default state should be OFF
    await expect(page.locator('#val-relay')).toHaveText('OFF');
    await expect(page.locator('#badge-relay')).toHaveText('OFF');
    await expect(page.locator('#badge-relay')).toHaveClass(/badge-off/);

    // Turn ON
    await page.click('button[onclick="setRelay(1)"]');
    await expect(page.locator('#val-relay')).toHaveText('ON');
    await expect(page.locator('#badge-relay')).toHaveText('ON');
    await expect(page.locator('#badge-relay')).toHaveClass(/badge-on/);

    // Turn OFF
    await page.click('button[onclick="setRelay(0)"]');
    await expect(page.locator('#val-relay')).toHaveText('OFF');
    await expect(page.locator('#badge-relay')).toHaveText('OFF');
    await expect(page.locator('#badge-relay')).toHaveClass(/badge-off/);

    // Send to Gateway
    await page.click('button[onclick="sendDevice(\'relay\')"]');
    
    // Check log entry
    const logEntry = page.locator('#logBody .log-entry', { hasText: '[relay-001]' }).first();
    await expect(logEntry).toBeVisible({ timeout: 5000 });
    await expect(logEntry).toContainText('HTTP 202');
  });

  test('Router interactivity and telemetry sending', async ({ page }) => {
    await page.goto('/iot-simulator.html');

    // Default state should be CONNECTED and bandwidth 72 Mb/s
    await expect(page.locator('#val-router')).toHaveText('72 Mb/s');
    await expect(page.locator('#badge-router')).toHaveText('CONNECTED');

    // Set bandwidth slider
    const slider = page.locator('input#routerBw');
    await slider.fill('450');
    await expect(page.locator('#val-router')).toHaveText('450 Mb/s');

    // Send to Gateway while in a valid state (before setting error)
    await page.click('button[onclick="sendDevice(\'router\')"]');

    // Check log entry shows HTTP 202 while router is healthy
    const logEntry = page.locator('#logBody .log-entry', { hasText: '[router-001]' }).first();
    await expect(logEntry).toBeVisible({ timeout: 5000 });
    await expect(logEntry).toContainText('HTTP 202');

    // Now verify badge state changes (UI-only, no further send needed)
    await page.click('button[onclick="setRouterStatus(\'degraded\')"]');
    await expect(page.locator('#badge-router')).toHaveText('DEGRADED');
    await expect(page.locator('#badge-router')).toHaveClass(/badge-warn/);

    await page.click('button[onclick="setRouterStatus(\'error\')"]');
    await expect(page.locator('#badge-router')).toHaveText('ERROR');
    await expect(page.locator('#badge-router')).toHaveClass(/badge-err/);

    // Reset back to connected
    await page.click('button[onclick="setRouterStatus(\'connected\')"]');
    await expect(page.locator('#badge-router')).toHaveText('CONNECTED');
    await expect(page.locator('#badge-router')).toHaveClass(/badge-on/);
  });

  test('Lobby Temp Sensor interactivity and telemetry sending', async ({ page }) => {
    await page.goto('/iot-simulator.html');

    // Default status should be NORMAL
    await expect(page.locator('#badge-temp')).toHaveText('NORMAL');

    // Randomize temperature (keeps it in normal range)
    await page.click('button[onclick="randomTemp()"]');
    const tempText = await page.locator('#val-temp').textContent();
    const tempVal = parseFloat(tempText);
    expect(tempVal).toBeGreaterThanOrEqual(15);
    expect(tempVal).toBeLessThanOrEqual(30);

    // Send to Gateway while NORMAL — should succeed with HTTP 202
    await page.click('button[onclick="sendDevice(\'temp\')"]');
    const logEntry = page.locator('#logBody .log-entry', { hasText: '[temp-001]' }).first();
    await expect(logEntry).toBeVisible({ timeout: 5000 });
    await expect(logEntry).toContainText('HTTP 202');

    // Now verify UI badge changes (spike & freeze are UI-only assertions)
    await page.click('button[onclick="spikeTemp()"]');
    const spikedText = await page.locator('#val-temp').textContent();
    const spikedVal = parseFloat(spikedText);
    expect(spikedVal).toBeGreaterThan(35);
    await expect(page.locator('#badge-temp')).toHaveText('HIGH TEMP');
    await expect(page.locator('#badge-temp')).toHaveClass(/badge-err/);

    await page.click('button[onclick="updateTemp(-5)"]');
    await expect(page.locator('#badge-temp')).toHaveText('FREEZE');
    await expect(page.locator('#badge-temp')).toHaveClass(/badge-warn/);
  });

  test('Global actions (Send ALL and Clear log)', async ({ page }) => {
    await page.goto('/iot-simulator.html');

    // Send ALL
    await page.click('button[onclick="sendAll()"]');

    // Verify log entries exist
    await page.waitForSelector('#logBody .log-entry:nth-child(3)');
    const countText = await page.locator('#logCount').textContent();
    expect(countText).toContain('entries');

    // Clear log
    await page.click('button[onclick="clearLog()"]');
    await expect(page.locator('#logBody .log-entry')).toHaveCount(0);
    await expect(page.locator('#logCount')).toHaveText('0 entries');
  });
});

test.describe('IoT Simulator Command Integration', () => {

  test('E2E Command dispatching, polling, execution, and confirmation', async ({ context, page }) => {
    // 1. Log in to the Admin Dashboard (page)
    await page.goto('/login');
    await page.fill('input[name="email"]', 'admin@telegateway.io');
    await page.fill('input[name="password"]', 'password');
    await page.click('form[action*="login"] button[type="submit"]');
    await page.waitForURL('**/admin/**', { timeout: 10000 });

    // 2. Open the simulator in a separate window/tab in the same browser context
    const simPage = await context.newPage();
    await simPage.goto('/iot-simulator.html');

    // Ensure the simulator makes its first connection to create or update 'relay-001' and keep it online
    await simPage.click('button[onclick="sendDevice(\'relay\')"]');
    await simPage.waitForSelector('#logBody .log-entry');

    // 3. Admin Panel: Find 'relay-001' in device list and navigate to details
    await page.goto('/admin/devices');
    const relayRow = page.locator('tr', { hasText: 'SN: RL-30001' });
    await expect(relayRow).toBeVisible();
    await relayRow.locator('a.btn-outline-info, a[href*="/devices/"]').first().click();
    await page.waitForURL('**/devices/*', { timeout: 10000 });

    // Ensure simulator relay state is OFF initially
    await simPage.click('button[onclick="setRelay(0)"]');
    await expect(simPage.locator('#val-relay')).toHaveText('OFF');

    // 4. Admin Panel: Click "Send Command" button to open the modal
    await page.click('button[data-bs-target="#sendCommandModal"]');
    await page.waitForSelector('#sendCommandModal.show');

    // Enter toggle action payload
    const commandPayload = { action: 'toggle' };
    await page.fill('textarea[name="payload"]', JSON.stringify(commandPayload));
    
    // Submit command
    await page.click('button[type="submit"][form="commandForm"]');
    
    // Wait for the modal to dismiss or command to appear in the table
    await page.waitForSelector('#sendCommandModal', { state: 'hidden' });

    // 5. Simulator Page: Verify command is received and processed
    // Polling takes place every 3 seconds. The simulator will poll, find the command,
    // and since autoExecute is checked, it will execute it (turning Relay ON) and respond.
    await expect(simPage.locator('#val-relay')).toHaveText('ON', { timeout: 12000 });
    
    // Verify success log entry in simulator
    const simLogEntry = simPage.locator('#logBody .log-entry', { hasText: 'REPLIED: Relay toggled to ON' }).first();
    await expect(simLogEntry).toBeVisible({ timeout: 5000 });

    // 6. Admin Panel: Verify the command status updates to "Success"
    // The admin details page polls for command status updates every 3 seconds.
    // Status badge uses capitalised "Success" — match case-insensitively.
    const commandRow = page.locator('#commandTableBody tr').first();
    await expect(commandRow.locator('.status-cell')).toContainText(/success/i, { timeout: 12000 });
    // Response cell should have a result button (locale-agnostic: in-flight badge gone, button present)
    await expect(commandRow.locator('.response-cell .in-flight-badge')).toHaveCount(0, { timeout: 5000 });
    await expect(commandRow.locator('.response-cell button').first()).toBeVisible();
  });
});
