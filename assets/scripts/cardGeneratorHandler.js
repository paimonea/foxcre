/** xxxReplacer
 * @param {string} bin
 * @returns {string}
 */
const xxxReplacer = (bin) => {
	const binFirst2Digits = parseInt(bin.substring(0, 2));
	const binLength = binFirst2Digits >= 34 && binFirst2Digits <= 37 ? 15 : 16;

	/** Add `x` After The Bin Times BinLength   */
	return bin + _.repeat("x", binLength - bin.length);
};

/** Luhn Validation
 * @param {string} cardNumber
 * @returns {boolean}
 */

const luhnValidation = (cardNumber) => {
	let length = cardNumber.length;
	let multiple = 0;
	const producedValue = [
		[0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
		[0, 2, 4, 6, 8, 1, 3, 5, 7, 9],
	];

	let sum = 0;
	while (length--) {
		sum += producedValue[multiple][parseInt(cardNumber.charAt(length), 10)];
		multiple ^= 1;
	}

	return sum % 10 === 0 && sum > 0;
};

/** xxxReplacerForcvv
 * @param {string} bin
 * @param {string} cvv
 * @returns {string}
 */

const xxxReplacerForcvv = (bin, cvv) => {
	/** If Bin Is Of Visa And Mastercard */
	if (bin.startsWith("4") || bin.startsWith("5")) {
		return cvv + _.repeat("x", 3 - cvv.length);
	}

	return cvv + _.repeat("x", 4 - cvv.length);
};

$("#randomCardGenerator").click(() => {
	const cardList = [];
	let flag = false;

	/** Grab Input Values */
	const bin = $("#binInGenerator").val();
	const expiryMonth = $("#monthInGenerator").val();
	const expiryYear = $("#yearInGenerator").val();
	const cvv = $("#cvvInGenerator").val();
	const quantity = $("#quantityInGenerator").val();

	/** Show Error State When Bin Value Is Empty */
	if (_.isEmpty(bin)) {
		$("#binInGenerator-error")?.removeClass("hidden");
		return;
		// biome-ignore lint/style/noUselessElse: <explanation>
	} else {
		$("#binInGenerator-error")?.addClass("hidden");
	}

	/** Find How Much Cards To Generate
	 * @default 10
	 */
	const quantityToGenerate = _.isEmpty(quantity) ? 10 : parseInt(quantity);

	/** Loop Until You Get 10 LuhnValidated Cards Or UserProvided Quantity Cards */
	while (!flag) {
		const card = xxxReplacer(bin).replace(/x/g, () => {
			return Math.floor(Math.random() * 10).toString();
		});

		const randomCvv = xxxReplacerForcvv(bin, cvv || "").replace(/x/g, () => {
			return Math.floor(Math.random() * 10).toString();
		});

		if (card) {
			if (luhnValidation(card)) {
				/** On expiryMonth Also Add 0 Before 1 to 9 */
				let randomExpMonth =
					expiryMonth === "Random"
						? (Math.floor(Math.random() * 12) + 1).toString()
						: expiryMonth;
				randomExpMonth =
					parseInt(randomExpMonth) < 10 ? `0${randomExpMonth}` : randomExpMonth;

				/** Get A Random Year Between 2023 To 2035 */
				const randomYear =
					expiryYear === "Random"
						? Math.floor(Math.random() * 12) + 2023
						: expiryYear;

				/** Push Card To Array */
				cardList.push(`${card}|${randomExpMonth}|${randomYear}|${randomCvv}`);
			}

			/** Break The Loop When We Have Quantity And Return Array */
			if (cardList.length === quantityToGenerate) {
				flag = true;

				// Add Card List To Textarea And Close Modal
				$("#card-list-textarea").val(cardList.join("\n"));
				$("#card-generator-modal").addClass("hidden");
				$("#card-generator-modal-overlay").addClass("hidden");
			}
		}
	}
});
