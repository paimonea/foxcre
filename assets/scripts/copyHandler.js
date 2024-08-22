// Copy Approved Section Cards When Pressing On Button
$('.copy-button-approved').click(() => {
  const approvedCards = [];

  $('.approved-card').each((_, item) => {
    const card = $(item)
      .find('.checked-card')
      .text()
      .replace(/\s*\n\s*/g, '');
    const response = $(item)
      .find('.response')
      .text()
      .replace(/\s*\n\s*/g, '');
    const threeDResult = $(item)
      .find('._3dresult')
      .text()
      .replace(/\s*\n\s*/g, '');
    const bin = $(item)
      .find('.bin')
      .text()
      .replace(/\s*\n\s*/g, '');
    const gate = $(item)
      .find('.gate-name')
      .text()
      .replace(/\s*\n\s*/g, '');
    approvedCards.push(`Approved - ${card}\nResponse - ${response}\n3D Result - ${threeDResult ?? '-'}\nBin- ${bin}\nGate - ${gate}`);
  });

  navigator.clipboard?.writeText(approvedCards.join('\n\n'));
});

// Copy Approved-CCN Section Cards When Pressing On Button
$('.copy-button-approved-ccn').click(() => {
  const approvedCCNCards = [];

  $('.approved-ccn-card').each((_, item) => {
    const card = $(item)
      .find('.checked-card')
      .text()
      .replace(/\s*\n\s*/g, '');
    const response = $(item)
      .find('.response')
      .text()
      .replace(/\s*\n\s*/g, '');
    const threeDResult = $(item)
      .find('._3dresult')
      .text()
      .replace(/\s*\n\s*/g, '');
    const bin = $(item)
      .find('.bin')
      .text()
      .replace(/\s*\n\s*/g, '');
    const gate = $(item)
      .find('.gate-name')
      .text()
      .replace(/\s*\n\s*/g, '');
    approvedCCNCards.push(
      `Approved-CCN - ${card}\nResponse - ${response}\n3D Result - ${threeDResult ?? '-'}\nBin- ${bin}\nGate - ${gate}`
    );
  });

  navigator.clipboard?.writeText(approvedCCNCards.join('\n\n'));
});

// Copy Declined Section Cards When Pressing On Button
$('.copy-button-declined').click(() => {
  const declinedCards = [];

  $('.declined-card').each((_, item) => {
    const card = $(item)
      .find('.checked-card')
      .text()
      .replace(/\s*\n\s*/g, '');
    const response = $(item)
      .find('.response')
      .text()
      .replace(/\s*\n\s*/g, '');
    const bin = $(item)
      .find('.bin')
      .text()
      .replace(/\s*\n\s*/g, '');
    const gate = $(item)
      .find('.gate-name')
      .text()
      .replace(/\s*\n\s*/g, '');
    declinedCards.push(`Declined - ${card}\nResponse - ${response}\nBin- ${bin}\nGate - ${gate}`);
  });
  navigator.clipboard?.writeText(declinedCards.join('\n\n'));
});
