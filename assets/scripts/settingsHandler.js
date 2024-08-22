import { config } from '../../appConfig.js';

/**
 * GateHandler
 */
const gateSelector = $('#gate-selector');

/** Get Gates Data From appConfig.js
 * Append It Inside The GateSelector
 */
const gatesData = config.gates_data;
for (let i = 0; i < gatesData.length; i++) {
  const gate = gatesData[i];
  gateSelector.append(`<option value="${gate.name}">${gate.name}</option>`);
}

/**
 * Set DefaultGate To LocalStorage
 */
gateSelector.change(() => {
  localStorage.defaultGate = gateSelector.val();
});

/**
 * Get `defaultGate` From LocalStorage
 * And Set That To GateSelector
 */
if (localStorage.defaultGate) {
  gateSelector.val(localStorage.defaultGate);
}

/**
 * ChatID Handler
 * Set ChatId In LocalStorage When Something Is Typed In Input
 * Otherwise Retrieve It From LocalStorage And Set It In Input
 */
const chatIdInput = $('#chatId-for-telegramStatus');

chatIdInput.on('keyup', (e) => {
  localStorage.setItem('chatId', e.target.value);
});

if (localStorage.getItem('chatId')) {
  chatIdInput.val(localStorage.getItem('chatId'));
}

/**
 * TelegramBotToken Handler
 * Set Telegram Bot Token In LocalStorage When Something Is Typed In Input
 * Otherwise Retrieve It From LocalStorage And Set It In Input
 */
const telegramBotTokenInput = $('#telegram_bot_token');

telegramBotTokenInput.on('keyup', (e) => {
  localStorage.setItem('telegramBotToken', e.target.value);
});

if (localStorage.getItem('telegramBotToken')) {
  telegramBotTokenInput.val(localStorage.getItem('telegramBotToken'));
}

/**
 * Proxy Toggle
 * Set Proxy Toggle In LocalStorage When It Is Toggled
 * Otherwise Retrieve It From LocalStorage And Set It In Toggle
 */

const proxyToggle = $('#proxy-toggle');

proxyToggle.change(() => {
  localStorage.setItem('proxy', proxyToggle.is(':checked'));
});

if (localStorage.getItem('proxy') === 'true') {
  $('#proxy-toggle').prop('checked', true);
}

/** ProxyHandler
 * Set Proxy In LocalStorage When Something Is Typed In Input
 * Otherwise Retrieve It From LocalStorage And Set It In Input
 */
const proxyTextarea = $('#proxy-list-textarea');

proxyTextarea.on('input', (e) => {
  localStorage.setItem('proxies', e.target.value);
});

if (localStorage.getItem('proxies')) {
  proxyTextarea.val(localStorage.getItem('proxies'));
}
