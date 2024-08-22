class CodeHandler {
	constructor(value) {
		this.value = value;
	}
	getBetween(value, start, end) {
		/** Get Between From A String */
		let str = value.split(start);
		str = str[1].split(end);
		return str[0];
	}
	convertMultipartToUrlEncoded(multipartData) {
		const boundary = multipartData.split("\\r\\n")[0];
		const parts = multipartData.split(boundary).slice(1, -1);
		const data = {};

		for (const part of parts) {
			const [header, ...valueParts] = part.split("\\r\\n").slice(1, -1);
			const name = header.split("; ")[1].split("=")[1].replace(/"/g, "");
			const value = valueParts.join("\\r\\n").trim();
			data[name] = value;
		}

		return Object.keys(data)
			.map((key) => `${key}=${data[key]}`)
			.join("&")
			.replace(/%5Cr%5Cn/g, "")
			.replace(/\\r|\\n/g, "");
	}
	convert() {
		const rawText = this.value;

		/** Convert Curl Bash To PHP Curl */
		const url = this.getBetween(rawText, "curl '", "'");
		let headers =
			rawText
				.match(/-H '([^']*)'/g)
				?.map((header) => header.replace(/-H '/, "").replace(/'$/, "")) ?? [];
		let dataRaw = rawText.match(/--data-raw '([^']*)'/)?.[1];

		/** Convert Multipart Into url-encoded */
		if (
			headers?.filter((header) => header.indexOf("multipart/form-data") !== -1)
				.length !== 0
		) {
			dataRaw = rawText.match(/--data-raw \$\'([^']*)'/)?.[1];
			dataRaw = this.convertMultipartToUrlEncoded(dataRaw);
			headers[
				headers?.findIndex(
					(header) => header.indexOf("multipart/form-data") !== -1,
				)
			] = "content-Type: application/x-www-form-urlencoded";
		}

		/** Get Method */
		let method = "GET";
		if (
			rawText.includes("-d ") ||
			rawText.includes("--data ") ||
			rawText.includes("--data-raw ")
		)
			method = "POST";

		const customMethodMatch = rawText.match(/-X (\w+)/);
		if (customMethodMatch) {
			method = customMethodMatch[1];
		}

		/** Remove `sec-ch` Headers */
		const headersToRemove = [
			"sec-ch",
			"user-agent",
			"authority",
			"content-length",
			"cookie",
		];
		headers = headers.filter((header) =>
			headersToRemove.every(
				(headerToRemove) =>
					!header.toLowerCase().includes(headerToRemove.toLowerCase()),
			),
		);

		/** Add Extra Quote Around Header And \n Also */
		headers = `    \n        '${headers.join("',\n        '")}'\n    `;

		return {
			url,
			method,
			headers,
			dataRaw,
		};
	}
	generateCode() {
		$("#codegen-textarea").val(this.value);

		/** Create New Instance Of CodeHandler And Convert The Raw Data */
		const { url, method, headers, dataRaw } = new CodeHandler(
			this.value,
		).convert();

		/** Make Result Based On Given Method (GET,POST,PATCH,DELETE)
		 * And Append To Result Textarea
		 */

		if (method === "GET") {
			$("#codegen_result-textarea").val(
				`\n$resp = $curl->get('${url}',\n    [${headers}]\n);\n`,
			);
		}

		if (method === "POST") {
			$("#codegen_result-textarea").val(
				`\n$resp = $curl->post('${url}',\n    '${dataRaw}',\n    [${headers}]\n);\n`,
			);
		}

		if (method === "PATCH") {
			$("#codegen_result-textarea").val(
				`\n$resp = $curl->patch('${url}',\n    '${dataRaw}',\n    [${headers}]\n);\n`,
			);
		}

		if (method === "DELETE") {
			$("#codegen_result-textarea").val(
				`\n$resp = $curl->delete('${url}',\n    [${headers}]\n);\n`,
			);
		}
	}
}

/** Clear Button Handler */
$("#CodeGeneratorClear").click(() => {
	$("#codegen-textarea").val("");
	$("#codegen_result-textarea").val("");
});

/** Button Handler */
$("#CodeGenerator").click(() => {
	/** For Mozilla */
	if (!navigator.clipboard || !navigator.clipboard.readText) {
		/** Get Text From Textarea */
		const text = $("#codegen-textarea").val();

		/** Generate Code */
		const result = new CodeHandler(text).generateCode();

		/** Copy Result To Clipboard */
		$("#codegen_result-textarea").text(result);
	} else {
		/** Get Text From Clipboard And Paste Into Textarea */
		navigator.clipboard.readText().then((text) => {
			/** Generate Code */
			new CodeHandler(text).generateCode();

			/** Copy Result To Clipboard */
			navigator.clipboard.writeText($("#codegen_result-textarea").val());
		});
	}
});
